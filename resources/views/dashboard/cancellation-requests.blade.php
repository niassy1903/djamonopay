@extends('layouts.simple.master')

@section('title', 'Demandes d’annulation')

@section('breadcrumb-title')
    <h3>Demandes d’annulation</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('index') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Demandes d’annulation</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row align-items-center">
                <div class="col-sm-8">
                    <h3>Demandes d’annulation</h3>
                    <p class="text-muted mb-0">Vérifiez les informations réelles de chaque transaction avant toute décision.</p>
                </div>
                <div class="col-sm-4 text-sm-end">
                    <a href="{{ route('dashboard-transactions') }}" class="btn btn-outline-success">Historique des transactions</a>
                </div>
            </div>
        </div>

        @if (session('operation_message'))
            <div class="alert alert-success" role="status">{{ session('operation_message') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            @forelse ($requests as $cancellationRequest)
                @php($transaction = $cancellationRequest->transaction)
                <div class="col-12 col-xl-6">
                    <article class="card">
                        <div class="card-header">
                            <h5 class="mb-1">Transaction {{ $transaction->reference }}</h5>
                            <small class="text-muted">Demande du {{ $cancellationRequest->created_at?->format('d/m/Y à H:i') }}</small>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-3">
                                <dt class="col-sm-5">Demandeur</dt>
                                <dd class="col-sm-7">{{ $cancellationRequest->requester?->name ?? 'Compte indisponible' }} · {{ $cancellationRequest->requester?->telephone ?? '—' }}</dd>
                                <dt class="col-sm-5">Montant enregistré</dt>
                                <dd class="col-sm-7">{{ number_format((float) $transaction->montant, 2, ',', ' ') }} {{ $transaction->devise }}</dd>
                                <dt class="col-sm-5">Téléphones des comptes</dt>
                                <dd class="col-sm-7">
                                    {{ $transaction->compteSource?->user?->telephone ?? '—' }}
                                    →
                                    {{ $transaction->compteDestination?->user?->telephone ?? '—' }}
                                </dd>
                                <dt class="col-sm-5">Faits saisis</dt>
                                <dd class="col-sm-7">
                                    Réf. {{ $cancellationRequest->verified_reference }} ·
                                    {{ number_format((float) $cancellationRequest->verified_amount, 2, ',', ' ') }} {{ $transaction->devise }} ·
                                    {{ $cancellationRequest->verified_phone }}
                                </dd>
                                <dt class="col-sm-5">Motif</dt>
                                <dd class="col-sm-7">{{ $cancellationRequest->reason }}</dd>
                            </dl>
                            <p class="rounded bg-warning-subtle p-3 small">
                                Les champs de référence, montant et téléphone ont été comparés aux données enregistrées.
                                Vérifiez également le contexte de l’opération avant de décider. Une annulation ne débitera
                                pas un compte au-delà de son solde disponible.
                            </p>

                            <form method="POST" action="{{ route('agent.cancellation-requests.approve', $cancellationRequest) }}" class="mb-3">
                                @csrf
                                <label class="form-label" for="review-note-{{ $cancellationRequest->id }}">Compte-rendu de vérification</label>
                                <textarea id="review-note-{{ $cancellationRequest->id }}" name="review_note" required minlength="10" maxlength="1000" class="form-control mb-2" rows="2"></textarea>
                                <label class="form-check mb-3">
                                    <input type="checkbox" name="confirmed_facts" value="1" required class="form-check-input">
                                    <span class="form-check-label">J’ai vérifié les informations et confirme l’annulation.</span>
                                </label>
                                <button type="submit" class="btn btn-success">Vérifier et annuler</button>
                            </form>
                            <form method="POST" action="{{ route('agent.cancellation-requests.reject', $cancellationRequest) }}">
                                @csrf
                                <label class="form-label" for="reject-note-{{ $cancellationRequest->id }}">Motif du refus</label>
                                <textarea id="reject-note-{{ $cancellationRequest->id }}" name="review_note" required minlength="10" maxlength="1000" class="form-control mb-2" rows="2"></textarea>
                                <button type="submit" class="btn btn-outline-danger">Refuser la demande</button>
                            </form>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="card-body text-center text-muted">Aucune demande d’annulation à vérifier.</div></div>
                </div>
            @endforelse
        </div>
        {{ $requests->links() }}
    </div>
@endsection
