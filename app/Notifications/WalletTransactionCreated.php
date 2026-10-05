<?php

namespace App\Notifications;

use App\Enums\UserRole;
use App\Models\Transaction;
use App\Models\Users2;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WalletTransactionCreated extends Notification
{
    use Queueable;

    public function __construct(private readonly Transaction $transaction) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $labels = [
            'depot' => 'Dépôt',
            'retrait' => 'Retrait',
            'paiement_qr' => 'Paiement',
        ];

        $label = $labels[$this->transaction->type];
        $notifiableId = $notifiable instanceof Users2 ? (int) $notifiable->id : null;
        $isIncoming = $notifiableId !== null
            && (int) $this->transaction->compteDestination?->user_id === $notifiableId;
        $title = $this->transaction->type === 'paiement_qr'
            ? ($isIncoming ? 'Paiement reçu' : 'Paiement envoyé')
            : 'Nouveau '.$label;
        $dashboardRoute = $notifiable instanceof Users2
            ? UserRole::dashboardRoute($notifiable->role)
            : null;

        return [
            'title' => $title,
            'body' => number_format((float) $this->transaction->montant, 0, ',', ' ')
                .' '.$this->transaction->devise.' · Réf. '.$this->transaction->reference,
            'reference' => $this->transaction->reference,
            'type' => $this->transaction->type,
            'amount' => $this->transaction->montant,
            'currency' => $this->transaction->devise,
            'status' => $this->transaction->statut,
            'url' => route($notifiableId !== null && $notifiable->role === UserRole::AGENT
                ? 'dashboard-transactions'
                : ($dashboardRoute ?? 'dashboard')),
        ];
    }
}
