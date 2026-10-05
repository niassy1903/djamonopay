<?php

namespace App\Actions;

use App\Enums\UserRole;
use App\Models\Compte;
use App\Models\SystemLogger;
use App\Models\Transaction;
use App\Models\TransactionCancellationRequest;
use App\Models\Users2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CancelWalletTransaction
{
    public function execute(Transaction $transaction, Users2 $actor, Request $request): Transaction
    {
        $this->authorize($transaction, $actor);

        return $this->cancel($transaction, $actor, $request, false);
    }

    public function executeFromSupport(
        TransactionCancellationRequest $cancellationRequest,
        Users2 $actor,
        Request $request
    ): Transaction {
        abort_unless($actor->role === UserRole::AGENT, 403);

        return $this->cancel($cancellationRequest->transaction, $actor, $request, true, $cancellationRequest);
    }

    private function cancel(
        Transaction $transaction,
        Users2 $actor,
        Request $request,
        bool $supportOverride,
        ?TransactionCancellationRequest $cancellationRequest = null
    ): Transaction {
        return DB::transaction(function () use ($transaction, $actor, $request, $supportOverride, $cancellationRequest): Transaction {
            $lockedTransaction = Transaction::query()->lockForUpdate()->findOrFail($transaction->id);

            if ($lockedTransaction->statut === 'annulee') {
                return $lockedTransaction;
            }

            if (! in_array($lockedTransaction->statut, ['en_attente', 'terminee'], true)) {
                throw ValidationException::withMessages([
                    'transaction' => 'Seules les transactions en attente ou terminées peuvent être annulées.',
                ]);
            }

            if (! $supportOverride && (
                ! $lockedTransaction->created_at
                || now()->greaterThan($lockedTransaction->created_at->copy()->addMinutes(15))
            )) {
                throw ValidationException::withMessages([
                    'transaction' => 'Le délai de 15 minutes est dépassé. Contactez l’assistance Djamanopay pour demander une annulation.',
                ]);
            }

            if ($supportOverride && (
                ! $cancellationRequest
                || (int) $cancellationRequest->transaction_id !== (int) $lockedTransaction->id
                || $cancellationRequest->status !== 'pending'
            )) {
                throw ValidationException::withMessages([
                    'transaction' => 'La demande d’annulation n’est plus en attente de vérification.',
                ]);
            }

            if ($lockedTransaction->statut === 'en_attente') {
                $lockedTransaction->update([
                    'statut' => 'annulee',
                    'cancelled_by_user_id' => $actor->id,
                ]);
                $this->log($lockedTransaction, $actor, $request, 'wallet.transaction.cancelled', 'Demande en attente annulée sans mouvement de solde.');

                return $lockedTransaction->fresh();
            }

            $bonusTransactions = Transaction::query()
                ->where('parent_transaction_id', $lockedTransaction->id)
                ->where('type', 'bonus_distributeur')
                ->where('statut', 'terminee')
                ->get();

            $accountIds = collect([
                $lockedTransaction->compte_source_id,
                $lockedTransaction->compte_destination_id,
            ])->merge($bonusTransactions->pluck('compte_destination_id'))
                ->filter()
                ->unique()
                ->sort()
                ->values();

            $accounts = Compte::query()
                ->whereIn('id', $accountIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $source = $accounts->get($lockedTransaction->compte_source_id);
            $destination = $accounts->get($lockedTransaction->compte_destination_id);

            if ($lockedTransaction->compte_source_id && ! $source) {
                throw ValidationException::withMessages(['transaction' => 'Le compte débité est introuvable.']);
            }
            if ($lockedTransaction->compte_destination_id && ! $destination) {
                throw ValidationException::withMessages(['transaction' => 'Le compte crédité est introuvable.']);
            }

            $amount = (float) $lockedTransaction->montant;
            $netAmount = max(0, $amount - (float) $lockedTransaction->frais);

            if ($source) {
                $source->solde = round((float) $source->solde + $amount, 2);
                $source->save();
            }

            $destinationRecovered = 0.0;
            if ($destination) {
                $destinationAvailable = max(0, (float) $destination->solde);
                $destinationRecovered = min($destinationAvailable, $netAmount);
                $destination->solde = round($destinationAvailable - $destinationRecovered, 2);
                $destination->save();
            }

            $lockedTransaction->update([
                'statut' => 'annulee',
                'cancelled_by_user_id' => $actor->id,
            ]);

            $reversal = Transaction::create([
                'reference' => 'REV-'.strtoupper((string) Str::ulid()),
                'actor_id' => $actor->id,
                'parent_transaction_id' => $lockedTransaction->id,
                'compte_source_id' => $destination?->id,
                'compte_destination_id' => $source?->id,
                'type' => 'remboursement_annulation',
                'montant' => $amount,
                'frais' => (float) $lockedTransaction->frais,
                'devise' => $lockedTransaction->devise,
                'statut' => 'terminee',
                'description' => 'Annulation de '.$lockedTransaction->reference.'; crédit restitué à la source, reprise sur le compte crédité limitée aux fonds disponibles ('
                    .number_format($destinationRecovered, 2, '.', '').' '.$lockedTransaction->devise.').',
                'traitee_at' => now(),
            ]);

            foreach ($bonusTransactions as $bonus) {
                $bonusAccount = $accounts->get($bonus->compte_destination_id);
                if (! $bonusAccount) {
                    throw ValidationException::withMessages([
                        'transaction' => 'Le compte de bonus est introuvable; l’annulation a été interrompue.',
                    ]);
                }

                $bonusAmount = (float) $bonus->montant;
                $bonusAvailable = max(0, (float) $bonusAccount->solde);
                $bonusRecovered = min($bonusAvailable, $bonusAmount);
                $bonusAccount->solde = round($bonusAvailable - $bonusRecovered, 2);
                $bonusAccount->save();
                $bonus->update(['statut' => 'annulee', 'cancelled_by_user_id' => $actor->id]);

                Transaction::create([
                    'reference' => 'REV-'.strtoupper((string) Str::ulid()),
                    'actor_id' => $actor->id,
                    'parent_transaction_id' => $bonus->id,
                    'compte_source_id' => $bonusAccount->id,
                    'type' => 'annulation_bonus',
                    'montant' => $bonusRecovered,
                    'frais' => 0,
                    'devise' => $bonus->devise,
                    'statut' => 'terminee',
                    'description' => 'Bonus lié à '.$lockedTransaction->reference.' annulé; montant repris : '.number_format($bonusRecovered, 2, '.', '').' '.$bonus->devise.'.',
                    'traitee_at' => now(),
                ]);
            }

            $this->log(
                $lockedTransaction,
                $actor,
                $request,
                'wallet.transaction.reversed',
                'Transaction '.$lockedTransaction->reference.' annulée; la reprise est limitée au solde disponible afin de ne pas créer de solde négatif.'
            );

            return $reversal;
        }, 3);
    }

    private function authorize(Transaction $transaction, Users2 $actor): void
    {
        abort_if(in_array($transaction->type, ['bonus_distributeur', 'annulation_bonus', 'remboursement_annulation'], true), 403);

        if ($actor->role === UserRole::CLIENT) {
            $ownsTransaction = (int) $transaction->compteSource?->user_id === (int) $actor->id
                || (int) $transaction->compteDestination?->user_id === (int) $actor->id;
            abort_unless($ownsTransaction, 403);

            return;
        }

        if ($actor->role === UserRole::AGENT) {
            $involvesClient = $transaction->compteSource?->user?->role === UserRole::CLIENT
                || $transaction->compteDestination?->user?->role === UserRole::CLIENT;
            abort_unless($involvesClient, 403);

            return;
        }

        if ($actor->role === UserRole::DISTRIBUTEUR) {
            $distributorAccountId = $actor->comptes()->value('id');
            $ownsTransaction = (int) $transaction->actor_id === (int) $actor->id
                || (int) $transaction->processed_by_user_id === (int) $actor->id;
            $assignedPendingWithdrawal = $transaction->type === 'retrait'
                && $transaction->statut === 'en_attente'
                && (int) $transaction->compte_destination_id === (int) $distributorAccountId;

            abort_unless($ownsTransaction || $assignedPendingWithdrawal, 403);

            return;
        }

        abort(403);
    }

    private function log(Transaction $transaction, Users2 $actor, Request $request, string $action, string $description): void
    {
        SystemLogger::create([
            'user_id' => $actor->id,
            'action' => $action,
            'description' => $description,
            'adresse_ip' => $request->ip(),
            'subject_type' => Transaction::class,
            'subject_id' => $transaction->id,
        ]);
    }
}
