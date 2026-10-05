<?php

namespace App\Observers;

use App\Enums\UserRole;
use App\Models\Transaction;
use App\Models\Users2;
use App\Notifications\WalletTransactionCreated;

class TransactionObserver
{
    public function created(Transaction $transaction): void
    {
        if (! in_array($transaction->type, ['depot', 'retrait', 'paiement_qr'], true)) {
            return;
        }

        $transaction->loadMissing(['compteSource.user', 'compteDestination.user']);

        $agents = Users2::query()
            ->where('role', UserRole::AGENT)
            ->where('etat_compte', true)
            ->get();

        $participants = collect([
            $transaction->compteSource?->user,
            $transaction->compteDestination?->user,
        ])->filter(fn (?Users2 $user): bool => $user !== null && $user->etat_compte)
            ->concat($agents)
            ->unique('id');

        foreach ($participants as $participant) {
            $participant->notify(new WalletTransactionCreated($transaction));
        }
    }
}
