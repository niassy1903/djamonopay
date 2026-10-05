@extends('layouts.simple.master')

@section('title', 'Transactions')

@section('breadcrumb-title')
    <h3>Transactions</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('index') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Transactions</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row g-3 mb-4">
            @foreach ([
                ['Total', $transactionCount],
                ['Terminées', $successfulCount],
                ['En attente', $pendingCount],
                ['Échouées / annulées', $failedCount],
            ] as [$label, $count])
                <div class="col-xl-3 col-sm-6">
                    <div class="card"><div class="card-body">
                        <p class="text-muted mb-1">{{ $label }}</p>
                        <h4 class="mb-0">{{ number_format($count, 0, ',', ' ') }}</h4>
                    </div></div>
                </div>
            @endforeach
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <p class="text-muted mb-1">Montant total des transactions terminées</p>
                <h3 class="mb-0">{{ number_format((float) $totalAmount, 0, ',', ' ') }} XOF</h3>
            </div>
        </div>
        <div class="card">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h4 class="mb-0">Historique des transactions</h4>
                <a href="{{ route('agent.cancellation-requests.index') }}" class="btn btn-outline-warning">Demandes à vérifier par l’assistance</a>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('dashboard-transactions') }}" class="row g-2 mb-4">
                    <div class="col-lg-4">
                        <label class="visually-hidden" for="transaction_search">Rechercher une transaction</label>
                        <input id="transaction_search" type="search" class="form-control" name="search" value="{{ request('search') }}" placeholder="Référence, nom, e-mail, téléphone, compte">
                    </div>
                    <div class="col-sm-6 col-lg-2">
                        <label class="visually-hidden" for="transaction_type">Type</label>
                        <select id="transaction_type" class="form-select" name="type">
                            <option value="">Tous les types</option>
                            @foreach (['credit_agent' => 'Crédit agent', 'depot' => 'Dépôt', 'retrait' => 'Retrait', 'paiement_qr' => 'Paiement', 'bonus_distributeur' => 'Bonus', 'annulation_bonus' => 'Annulation bonus', 'remboursement_annulation' => 'Remboursement'] as $value => $label)
                                <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6 col-lg-2">
                        <label class="visually-hidden" for="transaction_status">Statut</label>
                        <select id="transaction_status" class="form-select" name="status">
                            <option value="">Tous les statuts</option>
                            @foreach (['terminee' => 'Terminée', 'en_attente' => 'En attente', 'echouee' => 'Échouée', 'annulee' => 'Annulée'] as $value => $label)
                                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6 col-lg-1">
                        <label class="visually-hidden" for="transaction_start_date">Depuis le</label>
                        <input id="transaction_start_date" type="date" class="form-control" name="start_date" value="{{ request('start_date') }}" aria-label="Depuis le">
                    </div>
                    <div class="col-sm-6 col-lg-1">
                        <label class="visually-hidden" for="transaction_end_date">Jusqu’au</label>
                        <input id="transaction_end_date" type="date" class="form-control" name="end_date" value="{{ request('end_date') }}" aria-label="Jusqu’au">
                    </div>
                    <div class="col-lg-2 d-flex gap-2">
                        <button class="btn btn-primary flex-grow-1" type="submit"><i class="fa fa-search me-1" aria-hidden="true"></i>Filtrer</button>
                        @if (request()->hasAny(['search', 'type', 'status', 'start_date', 'end_date']))
                            <a class="btn btn-light" href="{{ route('dashboard-transactions') }}" aria-label="Effacer les filtres">×</a>
                        @endif
                    </div>
                </form>
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
                @endif
                <p class="small text-muted">Résultats : {{ number_format($transactions->total(), 0, ',', ' ') }} transaction(s)</p>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Date</th><th>Référence</th><th>Type</th><th>Émetteur</th><th>Bénéficiaire</th><th>Montant</th><th>Frais</th><th>Statut</th><th>Action</th></tr></thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                                @php($involvesClient = $transaction->compteSource?->user?->role === \App\Enums\UserRole::CLIENT || $transaction->compteDestination?->user?->role === \App\Enums\UserRole::CLIENT)
                                <tr>
                                    <td>{{ $transaction->created_at?->format('d/m/Y H:i') }}</td>
                                    <td>{{ $transaction->reference ?: '—' }}</td>
                                    <td>{{ ucfirst($transaction->type) }}</td>
                                    <td>{{ $transaction->compteSource?->user?->name ?? '—' }}</td>
                                    <td>{{ $transaction->compteDestination?->user?->name ?? '—' }}</td>
                                    <td>{{ number_format((float) $transaction->montant, 0, ',', ' ') }} {{ $transaction->devise }}</td>
                                    <td>{{ number_format((float) $transaction->frais, 0, ',', ' ') }} {{ $transaction->devise }}</td>
                                    <td>
                                        @php($badge = match ($transaction->statut) { 'terminee' => 'success', 'en_attente' => 'warning', 'echouee', 'annulee' => 'danger', default => 'secondary' })
                                        <span class="badge bg-light-{{ $badge }}">{{ ucfirst(str_replace('_', ' ', $transaction->statut)) }}</span>
                                    </td>
                                    <td>
                                        @if ($involvesClient && in_array($transaction->statut, ['terminee', 'en_attente'], true) && ! in_array($transaction->type, ['bonus_distributeur', 'annulation_bonus', 'remboursement_annulation'], true) && $transaction->created_at && now()->lessThanOrEqualTo($transaction->created_at->copy()->addMinutes(15)))
                                            <form method="POST" action="{{ route('agent.transactions.cancel', $transaction) }}" onsubmit="return confirm('Annuler cette transaction ? La reprise ne créera pas de solde négatif.')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Annuler</button>
                                            </form>
                                        @elseif ($involvesClient && in_array($transaction->statut, ['terminee', 'en_attente'], true))
                                            <a class="btn btn-sm btn-outline-warning" href="{{ route('agent.cancellation-requests.index') }}">Assistance requise</a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="py-5 text-center text-muted">Aucune transaction enregistrée dans la base de données.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
@endsection
