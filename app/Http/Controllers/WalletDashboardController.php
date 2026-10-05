<?php

namespace App\Http\Controllers;

use App\Actions\CancelWalletTransaction;
use App\Enums\UserRole;
use App\Models\Compte;
use App\Models\SystemLogger;
use App\Models\Transaction;
use App\Models\Users2;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WalletDashboardController extends Controller
{
    public function client(): View
    {
        return $this->dashboard(UserRole::CLIENT, 'Client');
    }

    public function distributor(): View
    {
        return $this->dashboard(UserRole::DISTRIBUTEUR, 'Distributeur');
    }

    public function createPayment(Request $request): View
    {
        $user = $request->user();
        $this->assertCanPay($user);
        $operation = $request->query('operation', 'paiement');
        abort_unless(in_array($operation, ['paiement', 'retrait'], true), 404);
        abort_if($operation === 'retrait' && $user->role !== UserRole::CLIENT, 403);

        $account = $user->comptes()->where('devise', 'XOF')->firstOrFail();
        $recipientNumber = $request->query('recipient_account');
        $recipient = $recipientNumber
            ? $this->resolveRecipient($recipientNumber, $operation)
            : null;

        if ($recipientNumber && ! $this->isEligibleRecipient($recipient, $operation)) {
            throw ValidationException::withMessages([
                'recipient_account' => 'Ce compte ne peut pas recevoir cette opération.',
            ]);
        }

        $distributors = $operation === 'retrait'
            ? Compte::with('user')
                ->where('devise', 'XOF')
                ->where('statut', 'actif')
                ->whereHas('user', fn ($query) => $query->where('role', UserRole::DISTRIBUTEUR)->where('etat_compte', true))
                ->get()
            : collect();

        return view('dashboard.payment', [
            'account' => $account,
            'availableBalance' => (float) $account->solde - $this->pendingWithdrawals($account->id),
            'recipient' => $recipient,
            'recipientNumber' => $recipientNumber,
            'recipientLookupUrl' => route('recipients.lookup'),
            'distributors' => $distributors,
            'operation' => $operation,
            'paymentToken' => (string) Str::uuid(),
        ]);
    }

    public function lookupRecipient(Request $request): JsonResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:32'],
            'operation' => ['required', 'string', 'in:paiement,retrait'],
        ]);

        $recipient = $this->findRecipientByPhone($data['phone'], $data['operation']);

        if (! $recipient) {
            return response()->json([
                'message' => 'Aucun compte actif correspondant à ce numéro.',
            ], 404);
        }

        return response()->json([
            'name' => $recipient->user->name,
            'role' => UserRole::getDescription($recipient->user->role),
        ]);
    }

    public function storePayment(Request $request): RedirectResponse
    {
        $user = $request->user();
        $this->assertCanPay($user);

        $data = $request->validate([
            'operation' => ['required', 'string', 'in:paiement,retrait'],
            'recipient_account' => ['required', 'string', 'max:32'],
            'amount' => ['required', 'integer', 'min:1', 'max:1000000000'],
            'payment_token' => ['required', 'uuid'],
        ]);

        if ($data['operation'] === 'retrait') {
            abort_unless($user->role === UserRole::CLIENT, 403);
        }

        $sender = $user->comptes()->where('devise', 'XOF')->firstOrFail();
        $recipient = $this->resolveRecipient($data['recipient_account'], $data['operation']);

        if (! $this->isEligibleRecipient($recipient, $data['operation'])) {
            throw ValidationException::withMessages([
                'recipient_account' => 'Ce compte est introuvable ou ne peut pas recevoir cette opération.',
            ]);
        }

        if ($recipient->id === $sender->id) {
            throw ValidationException::withMessages([
                'recipient_account' => 'Vous ne pouvez pas effectuer cette opération vers votre propre compte.',
            ]);
        }

        $amount = (int) $data['amount'];
        $fee = $this->percentage($amount, 2);
        $transaction = DB::transaction(function () use ($request, $sender, $recipient, $amount, $fee, $data): Transaction {
            $accounts = $this->lockAccounts([$sender->id, $recipient->id]);
            $lockedSender = $accounts->get($sender->id);
            $lockedRecipient = $accounts->get($recipient->id);

            $existing = Transaction::where('idempotency_key', $data['payment_token'])->first();
            if ($existing) {
                if ((int) $existing->compte_source_id !== (int) $sender->id
                    || (int) $existing->compte_destination_id !== (int) $recipient->id
                    || (int) $existing->montant !== $amount
                    || $existing->type !== ($data['operation'] === 'retrait' ? 'retrait' : 'paiement_qr')) {
                    throw ValidationException::withMessages([
                        'payment_token' => 'Cette confirmation a déjà été utilisée pour une autre opération.',
                    ]);
                }

                return $existing;
            }

            $this->assertAccountAvailable($lockedSender, $lockedRecipient);
            $reservedAmount = $this->pendingWithdrawals($lockedSender->id);
            if ((float) $lockedSender->solde - $reservedAmount < $amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Votre solde disponible après les retraits en attente est insuffisant.',
                ]);
            }

            if ($data['operation'] === 'retrait') {
                $payment = Transaction::create([
                    'reference' => 'WDR-'.strtoupper((string) Str::ulid()),
                    'idempotency_key' => $data['payment_token'],
                    'actor_id' => $request->user()->id,
                    'compte_source_id' => $lockedSender->id,
                    'compte_destination_id' => $lockedRecipient->id,
                    'type' => 'retrait',
                    'montant' => $amount,
                    'frais' => $fee,
                    'devise' => 'XOF',
                    'statut' => 'en_attente',
                    'description' => 'Retrait demandé; en attente de remise des espèces par le distributeur.',
                ]);

                $this->log($request, $payment, 'wallet.withdrawal.requested', 'Retrait de '.$amount.' XOF demandé au distributeur '.$lockedRecipient->numero_compte.'.');

                return $payment;
            }

            $lockedSender->solde = round((float) $lockedSender->solde - $amount, 2);
            $lockedRecipient->solde = round((float) $lockedRecipient->solde + $amount - $fee, 2);
            $lockedSender->save();
            $lockedRecipient->save();

            $payment = Transaction::create([
                'reference' => 'PAY-'.strtoupper((string) Str::ulid()),
                'idempotency_key' => $data['payment_token'],
                'actor_id' => $request->user()->id,
                'compte_source_id' => $lockedSender->id,
                'compte_destination_id' => $lockedRecipient->id,
                'type' => 'paiement_qr',
                'montant' => $amount,
                'frais' => $fee,
                'devise' => 'XOF',
                'statut' => 'terminee',
                'description' => 'Transfert client par QR code ou numéro de compte; frais de 2 % déduits du montant reçu.',
                'traitee_at' => now(),
            ]);

            $this->log($request, $payment, 'wallet.payment.completed', 'Transfert de '.$amount.' XOF, frais de '.$fee.' XOF vers '.$lockedRecipient->numero_compte.'.');

            return $payment;
        }, 3);

        if ($data['operation'] === 'retrait') {
            return redirect()
                ->route('payments.new', ['operation' => 'retrait'])
                ->with('payment_reference', $transaction->reference)
                ->with('withdrawal_pending', true)
                ->with('payment_amount', $amount)
                ->with('payment_fee', $fee);
        }

        return redirect()
            ->route('payments.new', ['recipient_account' => $recipient->numero_compte])
            ->with('payment_reference', $transaction->reference)
            ->with('payment_amount', $amount)
            ->with('payment_received', $amount - $fee)
            ->with('payment_fee', $fee);
    }

    public function storeDeposit(Request $request): RedirectResponse
    {
        $distributor = $request->user();
        abort_unless($distributor->role === UserRole::DISTRIBUTEUR, 403);

        $data = $request->validate([
            'client_account' => ['required', 'string', 'max:32'],
            'amount' => ['required', 'integer', 'min:1', 'max:1000000000'],
            'deposit_token' => ['required', 'uuid'],
        ]);

        $source = $distributor->comptes()->where('devise', 'XOF')->firstOrFail();
        $clientAccount = Compte::with('user')->where('numero_compte', $data['client_account'])->first();

        if (! $clientAccount || $clientAccount->user?->role !== UserRole::CLIENT || ! $this->isEligibleRecipient($clientAccount, 'depot')) {
            throw ValidationException::withMessages(['client_account' => 'Le compte client est introuvable ou inactif.']);
        }

        $amount = (int) $data['amount'];
        $result = DB::transaction(function () use ($request, $source, $clientAccount, $amount, $data): Transaction {
            $accounts = $this->lockAccounts([$source->id, $clientAccount->id]);
            $lockedSource = $accounts->get($source->id);
            $lockedClient = $accounts->get($clientAccount->id);

            $existing = Transaction::where('idempotency_key', $data['deposit_token'])->first();
            if ($existing) {
                if ((int) $existing->compte_source_id !== (int) $source->id
                    || (int) $existing->compte_destination_id !== (int) $clientAccount->id
                    || (int) $existing->montant !== $amount
                    || $existing->type !== 'depot') {
                    throw ValidationException::withMessages(['deposit_token' => 'Cette confirmation a déjà été utilisée pour un autre dépôt.']);
                }

                return $existing;
            }

            $this->assertAccountAvailable($lockedSource, $lockedClient);
            if ((float) $lockedSource->solde - $this->pendingWithdrawals($lockedSource->id) < $amount) {
                throw ValidationException::withMessages(['amount' => 'Le solde disponible du distributeur est insuffisant.']);
            }

            $lockedSource->solde = round((float) $lockedSource->solde - $amount, 2);
            $lockedClient->solde = round((float) $lockedClient->solde + $amount, 2);
            $lockedSource->save();
            $lockedClient->save();

            $deposit = Transaction::create([
                'reference' => 'DEP-'.strtoupper((string) Str::ulid()),
                'idempotency_key' => $data['deposit_token'],
                'actor_id' => $request->user()->id,
                'compte_source_id' => $lockedSource->id,
                'compte_destination_id' => $lockedClient->id,
                'type' => 'depot',
                'montant' => $amount,
                'frais' => 0,
                'devise' => 'XOF',
                'statut' => 'terminee',
                'description' => 'Dépôt espèces effectué par le distributeur.',
                'traitee_at' => now(),
            ]);

            $bonus = $this->creditDistributorBonus($deposit, $lockedSource, $request->user());
            $this->log($request, $deposit, 'wallet.deposit.completed', 'Dépôt de '.$amount.' XOF; bonus distributeur de '.$bonus.' XOF.');

            return $deposit;
        }, 3);

        return redirect()
            ->route('dashboard-distributeur')
            ->with('operation_reference', $result->reference)
            ->with('operation_message', 'Dépôt client effectué avec succès.');
    }

    public function completeWithdrawal(Request $request, Transaction $transaction): RedirectResponse
    {
        $distributor = $request->user();
        abort_unless($distributor->role === UserRole::DISTRIBUTEUR, 403);
        $distributorAccount = $distributor->comptes()->where('devise', 'XOF')->firstOrFail();

        $completed = DB::transaction(function () use ($request, $transaction, $distributor, $distributorAccount): Transaction {
            $withdrawal = Transaction::query()->lockForUpdate()->findOrFail($transaction->id);
            abort_unless($withdrawal->type === 'retrait'
                && (int) $withdrawal->compte_destination_id === (int) $distributorAccount->id, 403);

            if ($withdrawal->statut === 'terminee') {
                return $withdrawal;
            }
            if ($withdrawal->statut !== 'en_attente') {
                throw ValidationException::withMessages(['transaction' => 'Cette demande de retrait n’est plus en attente.']);
            }

            $accounts = $this->lockAccounts([$withdrawal->compte_source_id, $withdrawal->compte_destination_id]);
            $client = $accounts->get($withdrawal->compte_source_id);
            $distributorWallet = $accounts->get($withdrawal->compte_destination_id);
            $this->assertAccountAvailable($client, $distributorWallet);

            $amount = (float) $withdrawal->montant;
            $otherReservations = $this->pendingWithdrawals($client->id, $withdrawal->id);
            if ((float) $client->solde - $otherReservations < $amount) {
                throw ValidationException::withMessages(['transaction' => 'Le solde disponible du client ne couvre plus ce retrait.']);
            }

            $fee = (float) $withdrawal->frais;
            $bonus = $this->percentage((int) $amount, 1);
            $client->solde = round((float) $client->solde - $amount, 2);
            $distributorWallet->solde = round((float) $distributorWallet->solde + $amount - $fee + $bonus, 2);
            $client->save();
            $distributorWallet->save();

            $withdrawal->update([
                'statut' => 'terminee',
                'processed_by_user_id' => $distributor->id,
                'traitee_at' => now(),
            ]);
            $this->createBonusRecord($withdrawal, $distributorWallet, $distributor, $bonus);
            $this->log($request, $withdrawal, 'wallet.withdrawal.completed', 'Retrait remis en espèces; bonus de '.$bonus.' XOF crédité au distributeur.');

            return $withdrawal->fresh();
        }, 3);

        return redirect()
            ->route('dashboard-distributeur')
            ->with('operation_reference', $completed->reference)
            ->with('operation_message', 'Retrait confirmé et remis en espèces.');
    }

    public function cancelTransaction(
        Request $request,
        Transaction $transaction,
        CancelWalletTransaction $cancelWalletTransaction
    ): RedirectResponse {
        abort_unless(in_array($request->user()->role, [UserRole::CLIENT, UserRole::DISTRIBUTEUR], true), 403);
        $cancelWalletTransaction->execute($transaction, $request->user(), $request);

        return back()->with('operation_message', 'La transaction a été annulée sans créer de solde négatif.');
    }

    private function dashboard(string $role, string $title): View
    {
        $user = request()->user();
        abort_unless($user->role === $role, 403);
        $account = $user->comptes()->where('devise', 'XOF')->firstOrFail();
        $activity = Transaction::query()
            ->where('devise', 'XOF')
            ->where(function ($query) use ($account): void {
                $query->where('compte_source_id', $account->id)
                    ->orWhere('compte_destination_id', $account->id);
            });
        $visibleActivity = (clone $activity)->whereNotIn('type', ['bonus_distributeur', 'annulation_bonus', 'remboursement_annulation']);

        $pendingWithdrawals = $role === UserRole::DISTRIBUTEUR
            ? (clone $activity)
                ->with(['compteSource.user', 'cancellationRequests'])
                ->where('type', 'retrait')
                ->where('statut', 'en_attente')
                ->latest()
                ->get()
            : collect();

        $distributors = $role === UserRole::CLIENT
            ? Compte::with('user')
                ->where('devise', 'XOF')
                ->where('statut', 'actif')
                ->whereHas('user', fn ($query) => $query->where('role', UserRole::DISTRIBUTEUR)->where('etat_compte', true))
                ->get()
            : collect();

        return view('dashboard.wallet', [
            'title' => $title,
            'role' => $role,
            'account' => $account,
            'availableBalance' => (float) $account->solde - $this->pendingWithdrawals($account->id),
            'sentCount' => (clone $visibleActivity)->where('compte_source_id', $account->id)->where('statut', 'terminee')->count(),
            'receivedCount' => (clone $visibleActivity)->where('compte_destination_id', $account->id)->where('statut', 'terminee')->count(),
            'sentAmount' => (clone $visibleActivity)->where('compte_source_id', $account->id)->where('statut', 'terminee')->sum('montant'),
            'receivedAmount' => (clone $visibleActivity)
                ->where('compte_destination_id', $account->id)
                ->where('statut', 'terminee')
                ->sum(DB::raw('montant - frais')),
            'bonusTotal' => (float) Transaction::where('type', 'bonus_distributeur')
                ->where('statut', 'terminee')
                ->where('compte_destination_id', $account->id)
                ->sum('montant')
                - (float) Transaction::where('type', 'annulation_bonus')
                    ->where('statut', 'terminee')
                    ->where('compte_source_id', $account->id)
                    ->sum('montant'),
            'transactions' => (clone $visibleActivity)
                ->with(['compteSource.user', 'compteDestination.user', 'cancellationRequests'])
                ->latest()
                ->limit(12)
                ->get(),
            'monthlyChart' => $this->monthlyActivity($account),
            'paymentUrl' => route('payments.new', ['recipient_account' => $account->numero_compte]),
            'distributors' => $distributors,
            'pendingWithdrawals' => $pendingWithdrawals,
            'depositToken' => (string) Str::uuid(),
        ]);
    }

    private function monthlyActivity(Compte $account): array
    {
        $start = now()->startOfMonth()->subMonths(5);
        $driver = DB::connection()->getDriverName();
        $monthExpression = match ($driver) {
            'sqlite' => "strftime('%Y-%m', created_at)",
            'pgsql' => "to_char(created_at, 'YYYY-MM')",
            default => "DATE_FORMAT(created_at, '%Y-%m')",
        };

        $results = Transaction::query()
            ->selectRaw($monthExpression.' as month')
            ->selectRaw('SUM(CASE WHEN compte_source_id = ? THEN montant ELSE 0 END) as sent', [$account->id])
            ->selectRaw('SUM(CASE WHEN compte_destination_id = ? THEN montant - frais ELSE 0 END) as received', [$account->id])
            ->where('statut', 'terminee')
            ->whereNotIn('type', ['bonus_distributeur', 'annulation_bonus', 'remboursement_annulation'])
            ->where(function ($query) use ($account): void {
                $query->where('compte_source_id', $account->id)
                    ->orWhere('compte_destination_id', $account->id);
            })
            ->where('created_at', '>=', $start)
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $labels = [];
        $sent = [];
        $received = [];

        for ($offset = 0; $offset < 6; $offset++) {
            $month = $start->copy()->addMonths($offset);
            $row = $results->get($month->format('Y-m'));
            $labels[] = $month->format('m/Y');
            $sent[] = (float) ($row?->sent ?? 0);
            $received[] = (float) ($row?->received ?? 0);
        }

        return [
            'labels' => $labels,
            'series' => [
                ['name' => 'Envoyé', 'data' => $sent],
                ['name' => 'Reçu', 'data' => $received],
            ],
        ];
    }

    private function lockAccounts(array $ids)
    {
        return Compte::query()
            ->whereIn('id', $ids)
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');
    }

    private function assertAccountAvailable(?Compte $source, ?Compte $destination): void
    {
        if (! $source || ! $destination
            || $source->statut !== 'actif'
            || $destination->statut !== 'actif'
            || ! $source->user?->etat_compte
            || ! $destination->user?->etat_compte) {
            throw ValidationException::withMessages([
                'recipient_account' => 'Un des comptes de cette opération est inactif ou indisponible.',
            ]);
        }
    }

    private function pendingWithdrawals(int $accountId, ?int $exceptTransactionId = null): float
    {
        return (float) Transaction::query()
            ->where('type', 'retrait')
            ->where('statut', 'en_attente')
            ->where('compte_source_id', $accountId)
            ->when($exceptTransactionId, fn ($query) => $query->where('id', '!=', $exceptTransactionId))
            ->sum('montant');
    }

    private function percentage(int $amount, int $percentage): int
    {
        return (int) round($amount * $percentage / 100, 0, PHP_ROUND_HALF_UP);
    }

    private function creditDistributorBonus(Transaction $parent, Compte $account, Users2 $distributor): int
    {
        $bonus = $this->percentage((int) $parent->montant, 1);
        if ($bonus > 0) {
            $account->solde = round((float) $account->solde + $bonus, 2);
            $account->save();
            $this->createBonusRecord($parent, $account, $distributor, $bonus);
        }

        return $bonus;
    }

    private function createBonusRecord(Transaction $parent, Compte $account, Users2 $distributor, int $bonus): void
    {
        if ($bonus < 1) {
            return;
        }

        Transaction::create([
            'reference' => 'BON-'.strtoupper((string) Str::ulid()),
            'actor_id' => $distributor->id,
            'processed_by_user_id' => $distributor->id,
            'parent_transaction_id' => $parent->id,
            'compte_destination_id' => $account->id,
            'type' => 'bonus_distributeur',
            'montant' => $bonus,
            'frais' => 0,
            'devise' => 'XOF',
            'statut' => 'terminee',
            'description' => 'Bonus de 1 % pour la transaction '.$parent->reference.'.',
            'traitee_at' => now(),
        ]);
    }

    private function isEligibleRecipient(?Compte $account, string $operation): bool
    {
        if (! $account || $account->statut !== 'actif' || $account->devise !== 'XOF' || ! $account->user?->etat_compte) {
            return false;
        }

        return $operation === 'retrait'
            ? $account->user->role === UserRole::DISTRIBUTEUR
            : in_array($account->user->role, [UserRole::CLIENT, UserRole::DISTRIBUTEUR], true);
    }

    private function resolveRecipient(string $identifier, string $operation): ?Compte
    {
        if (preg_match('/^\+?[\d\s().-]+$/', $identifier)) {
            return $this->findRecipientByPhone($identifier, $operation);
        }

        $account = Compte::with('user')
            ->where('numero_compte', $identifier)
            ->first();

        return $this->isEligibleRecipient($account, $operation) ? $account : null;
    }

    private function findRecipientByPhone(string $phone, string $operation): ?Compte
    {
        $digits = preg_replace('/\D+/', '', $phone);
        if (! $digits) {
            return null;
        }

        $phoneCandidates = [$digits];
        if (str_starts_with($digits, '221') && strlen($digits) > 9) {
            $phoneCandidates[] = substr($digits, 3);
        } elseif (strlen($digits) === 9) {
            $phoneCandidates[] = '221'.$digits;
        }

        $roles = $operation === 'retrait'
            ? [UserRole::DISTRIBUTEUR]
            : [UserRole::CLIENT, UserRole::DISTRIBUTEUR];

        $users = Users2::query()
            ->where('etat_compte', true)
            ->whereIn('role', $roles)
            ->whereRaw(
                "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(telephone, '+', ''), ' ', ''), '-', ''), '(', ''), ')', ''), '.', '') IN (".implode(',', array_fill(0, count($phoneCandidates), '?')).')',
                $phoneCandidates
            )
            ->with(['comptes' => fn ($query) => $query->where('devise', 'XOF')->where('statut', 'actif')])
            ->get();

        if ($users->count() !== 1) {
            return null;
        }

        $account = $users->first()->comptes->first();

        return $this->isEligibleRecipient($account, $operation) ? $account->load('user') : null;
    }

    private function assertCanPay(Users2 $user): void
    {
        abort_unless(in_array($user->role, [UserRole::CLIENT, UserRole::DISTRIBUTEUR], true), 403);
    }

    private function log(Request $request, Transaction $transaction, string $action, string $description): void
    {
        SystemLogger::create([
            'user_id' => $request->user()->id,
            'action' => $action,
            'description' => $description,
            'adresse_ip' => $request->ip(),
            'subject_type' => Transaction::class,
            'subject_id' => $transaction->id,
        ]);
    }
}
