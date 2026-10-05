@extends('layouts.simple.master')

@section('title', 'Activités')

@section('breadcrumb-title')
    <h3>Journal d’activité</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('index') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Activités</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-1">Journal des actions</h4>
                    <p class="mb-0 text-muted">{{ number_format($activityCount, 0, ',', ' ') }} activité(s) enregistrée(s)</p>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('dashboard-activitées') }}" class="row g-2 mb-4">
                    <div class="col-lg-6">
                        <label class="visually-hidden" for="activity_search">Rechercher dans le journal</label>
                        <input id="activity_search" type="search" class="form-control" name="search" value="{{ request('search') }}" placeholder="Action, détails, adresse IP, nom ou e-mail">
                    </div>
                    <div class="col-sm-6 col-lg-2">
                        <label class="visually-hidden" for="activity_start_date">Depuis le</label>
                        <input id="activity_start_date" type="date" class="form-control" name="start_date" value="{{ request('start_date') }}" aria-label="Depuis le">
                    </div>
                    <div class="col-sm-6 col-lg-2">
                        <label class="visually-hidden" for="activity_end_date">Jusqu’au</label>
                        <input id="activity_end_date" type="date" class="form-control" name="end_date" value="{{ request('end_date') }}" aria-label="Jusqu’au">
                    </div>
                    <div class="col-lg-2 d-flex gap-2">
                        <button class="btn btn-primary flex-grow-1" type="submit"><i class="fa fa-search me-1" aria-hidden="true"></i>Rechercher</button>
                        @if (request()->hasAny(['search', 'start_date', 'end_date']))
                            <a class="btn btn-light" href="{{ route('dashboard-activitées') }}" aria-label="Effacer les filtres">×</a>
                        @endif
                    </div>
                </form>
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
                @endif
                <p class="small text-muted">Résultats : {{ number_format($activities->total(), 0, ',', ' ') }} activité(s)</p>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Date</th><th>Agent</th><th>Action</th><th>Détails</th><th>Adresse IP</th></tr></thead>
                        <tbody>
                            @forelse ($activities as $activity)
                                <tr>
                                    <td>{{ $activity->created_at?->format('d/m/Y H:i:s') }}</td>
                                    <td>{{ $activity->user?->name ?? 'Système' }}</td>
                                    <td>{{ $activity->action ?: 'Activité' }}</td>
                                    <td>{{ $activity->description ?: '—' }}</td>
                                    <td>{{ $activity->adresse_ip ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-5 text-center text-muted">Aucune activité enregistrée. Les actions de gestion effectuées par un agent apparaîtront ici.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $activities->links() }}
            </div>
        </div>
    </div>
@endsection
