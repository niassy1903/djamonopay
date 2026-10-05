<?php

namespace App\Http\Controllers;

use App\Actions\CancelWalletTransaction;
use App\Enums\UserRole;
use App\Models\SystemLogger;
use App\Models\Transaction;
use App\Models\TransactionCancellationRequest;
use App\Models\Users2;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionCancellationRequestController extends Controller
{
    public function store(Request $request, Transaction $transaction): RedirectResponse
    {
        $data = $request->validate([
            'reference' => ['required', 'string', 'max:120'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999999999.99'],
            'phone' => ['required', 'string', 'min:6', 'max:30'],
            'reason' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $user = $request->user();
        DB::transaction(function () use ($transaction, $user, $data): void {
            $lockedTransaction = Transaction::query()->lockForUpdate()->findOrFail($transaction->id);
            $this->ensureRequestIsEligible($lockedTransaction, $user, $data);

            $hasPendingRequest = TransactionCancellationRequest::query()
                ->where('transaction_id', $lockedTransaction->id)
                ->where('status', 'pending')
                ->exists();

            if ($hasPendingRequest) {
                throw ValidationException::withMessages([
                    'transaction' => 'Une demande est déjà en cours de vérification pour cette transaction.',
                ]);
            }

            TransactionCancellationRequest::create([
                'transaction_id' => $lockedTransaction->id,
                'requester_id' => $user->id,
                'verified_reference' => $data['reference'],
                'verified_amount' => $data['amount'],
                'verified_phone' => $data['phone'],
                'reason' => $data['reason'],
            ]);
        }, 3);

        return back()->with('operation_message', 'Votre demande a été transmise à l’assistance Djamanopay pour vérification.');
    }

    public function index(): View
    {
        $requests = TransactionCancellationRequest::query()
            ->with([
                'transaction.compteSource.user',
                'transaction.compteDestination.user',
                'requester',
            ])
            ->where('status', 'pending')
            ->latest()
            ->paginate(15);

        return view('dashboard.cancellation-requests', compact('requests'));
    }

    public function approve(
        Request $request,
        TransactionCancellationRequest $cancellationRequest,
        CancelWalletTransaction $cancelWalletTransaction
    ): RedirectResponse {
        abort_unless($request->user()->role === UserRole::AGENT, 403);
        $data = $request->validate([
            'confirmed_facts' => ['required', 'accepted'],
            'review_note' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        DB::transaction(function () use ($request, $cancellationRequest, $cancelWalletTransaction, $data): void {
            $lockedRequest = TransactionCancellationRequest::query()
                ->lockForUpdate()
                ->findOrFail($cancellationRequest->id);

            if ($lockedRequest->status !== 'pending') {
                throw ValidationException::withMessages([
                    'request' => 'Cette demande a déjà été traitée.',
                ]);
            }

            $transaction = Transaction::query()->lockForUpdate()->findOrFail($lockedRequest->transaction_id);
            if (! in_array($transaction->statut, ['terminee', 'en_attente'], true)) {
                throw ValidationException::withMessages([
                    'transaction' => 'Cette transaction ne peut plus être annulée.',
                ]);
            }

            $this->verifyStoredFacts($lockedRequest, $transaction);
            $cancelWalletTransaction->executeFromSupport($lockedRequest, $request->user(), $request);

            $lockedRequest->update([
                'status' => 'approved',
                'reviewed_by_user_id' => $request->user()->id,
                'review_note' => $data['review_note'],
                'reviewed_at' => now(),
            ]);

            SystemLogger::create([
                'user_id' => $request->user()->id,
                'action' => 'wallet.transaction.cancellation_request.approved',
                'description' => 'Demande vérifiée et transaction '.$transaction->reference.' annulée. '.$data['review_note'],
                'adresse_ip' => $request->ip(),
                'subject_type' => Transaction::class,
                'subject_id' => $transaction->id,
            ]);
        }, 3);

        return back()->with('operation_message', 'La demande a été vérifiée et la transaction annulée.');
    }

    public function reject(Request $request, TransactionCancellationRequest $cancellationRequest): RedirectResponse
    {
        abort_unless($request->user()->role === UserRole::AGENT, 403);
        $data = $request->validate([
            'review_note' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        DB::transaction(function () use ($request, $cancellationRequest, $data): void {
            $lockedRequest = TransactionCancellationRequest::query()
                ->lockForUpdate()
                ->findOrFail($cancellationRequest->id);

            if ($lockedRequest->status !== 'pending') {
                throw ValidationException::withMessages([
                    'request' => 'Cette demande a déjà été traitée.',
                ]);
            }

            $lockedRequest->update([
                'status' => 'rejected',
                'reviewed_by_user_id' => $request->user()->id,
                'review_note' => $data['review_note'],
                'reviewed_at' => now(),
            ]);

            SystemLogger::create([
                'user_id' => $request->user()->id,
                'action' => 'wallet.transaction.cancellation_request.rejected',
                'description' => 'Demande d’annulation de '.$lockedRequest->transaction->reference.' refusée. '.$data['review_note'],
                'adresse_ip' => $request->ip(),
                'subject_type' => Transaction::class,
                'subject_id' => $lockedRequest->transaction_id,
            ]);
        }, 3);

        return back()->with('operation_message', 'La demande a été refusée et le motif enregistré.');
    }

    private function ensureRequestIsEligible(Transaction $transaction, Users2 $user, array $data): void
    {
        if (! $transaction->created_at || ! now()->greaterThan($transaction->created_at->copy()->addMinutes(15))) {
            throw ValidationException::withMessages([
                'transaction' => 'Une demande à l’assistance est possible uniquement après le délai de 15 minutes.',
            ]);
        }

        if (! in_array($transaction->statut, ['terminee', 'en_attente'], true)) {
            throw ValidationException::withMessages([
                'transaction' => 'Cette transaction ne peut pas faire l’objet d’une demande d’annulation.',
            ]);
        }

        $ownsSource = (int) $transaction->compteSource?->user_id === (int) $user->id;
        $ownsDestination = (int) $transaction->compteDestination?->user_id === (int) $user->id;
        if (! $ownsSource && ! $ownsDestination) {
            abort(403);
        }

        $registeredPhone = $this->digits($user->telephone);
        $submittedPhone = $this->digits($data['phone']);
        if ($registeredPhone === '' || ! hash_equals($registeredPhone, $submittedPhone)) {
            throw ValidationException::withMessages([
                'phone' => 'Le numéro saisi doit correspondre au téléphone enregistré sur votre compte.',
            ]);
        }

        if (! hash_equals((string) $transaction->reference, trim($data['reference']))) {
            throw ValidationException::withMessages([
                'reference' => 'La référence ne correspond pas à la transaction sélectionnée.',
            ]);
        }

        if (number_format((float) $transaction->montant, 2, '.', '') !== number_format((float) $data['amount'], 2, '.', '')) {
            throw ValidationException::withMessages([
                'amount' => 'Le montant ne correspond pas à la transaction sélectionnée.',
            ]);
        }
    }

    private function verifyStoredFacts(TransactionCancellationRequest $cancellationRequest, Transaction $transaction): void
    {
        $requester = Users2::query()->find($cancellationRequest->requester_id);
        $isParty = (int) $transaction->compteSource?->user_id === (int) $requester?->id
            || (int) $transaction->compteDestination?->user_id === (int) $requester?->id;
        $factsMatch = $requester
            && $isParty
            && hash_equals((string) $transaction->reference, $cancellationRequest->verified_reference)
            && number_format((float) $transaction->montant, 2, '.', '') === number_format((float) $cancellationRequest->verified_amount, 2, '.', '')
            && hash_equals($this->digits($requester->telephone), $this->digits($cancellationRequest->verified_phone));

        if (! $factsMatch) {
            throw ValidationException::withMessages([
                'request' => 'Les informations vérifiées ne correspondent plus aux données du compte et de la transaction.',
            ]);
        }
    }

    private function digits(?string $value): string
    {
        return preg_replace('/\D+/', '', $value ?? '') ?? '';
    }
}
