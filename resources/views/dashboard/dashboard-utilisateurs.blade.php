@extends('layouts.simple.master')

@section('title', 'Utilisateurs')

@section('breadcrumb-title')
    <h3>Utilisateurs</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('index') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Utilisateurs</li>
@endsection

@section('content')
    <div class="container-fluid">
        @if (session('operation_message'))
            <div class="alert alert-success" role="status">{{ session('operation_message') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <p class="mb-1 fw-semibold">Vérifiez les informations saisies :</p>
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        <div class="d-flex justify-content-end mb-3">
            <button type="button" class="btn btn-primary" data-open-user-modal="create-user-modal">
                <i class="fa fa-user-plus me-2" aria-hidden="true"></i>Ajouter un utilisateur
            </button>
        </div>
        <dialog class="dp-user-modal" id="create-user-modal" data-user-modal @if (old('modal') === 'create') data-open-on-load="true" @endif aria-labelledby="create-user-title">
            <div class="dp-user-modal__header">
                <div><p class="dp-user-modal__eyebrow">Djamanopay · Annuaire</p><h2 id="create-user-title">Créer un compte</h2></div>
                <button type="button" class="dp-user-modal__close" data-close-user-modal aria-label="Fermer">&times;</button>
            </div>
            <form method="POST" action="{{ route('agent.users.store') }}" class="row g-3 p-4">
                @csrf
                <input type="hidden" name="modal" value="create">
                <div class="col-md-6"><label class="form-label" for="create_first_name">Prénom</label><input id="create_first_name" name="prenom" value="{{ old('modal') === 'create' ? old('prenom') : '' }}" required maxlength="255" autocomplete="given-name" class="form-control"></div>
                <div class="col-md-6"><label class="form-label" for="create_last_name">Nom</label><input id="create_last_name" name="nom" value="{{ old('modal') === 'create' ? old('nom') : '' }}" required maxlength="255" autocomplete="family-name" class="form-control"></div>
                <div class="col-md-6"><label class="form-label" for="create_email">Adresse e-mail</label><input id="create_email" name="email" type="email" value="{{ old('modal') === 'create' ? old('email') : '' }}" required maxlength="255" autocomplete="email" class="form-control"></div>
                <div class="col-md-6"><label class="form-label" for="create_role">Rôle</label><select id="create_role" name="role" required class="form-select"><option value="">Choisir un rôle</option>@foreach (['client' => 'Client', 'distributeur' => 'Distributeur', 'agent' => 'Agent'] as $value => $label)<option value="{{ $value }}" @selected(old('modal') === 'create' && old('role') === $value)>{{ $label }}</option>@endforeach</select></div>
                <div class="col-md-6"><label class="form-label" for="create_phone">Téléphone</label><input id="create_phone" name="telephone" value="{{ old('modal') === 'create' ? old('telephone') : '' }}" required maxlength="255" autocomplete="tel" class="form-control"></div>
                <div class="col-md-6"><label class="form-label" for="create_address">Adresse</label><input id="create_address" name="adresse" value="{{ old('modal') === 'create' ? old('adresse') : '' }}" required maxlength="255" autocomplete="street-address" class="form-control"></div>
                <div class="col-md-6"><label class="form-label" for="create_birth_date">Date de naissance</label><input id="create_birth_date" name="date_naissance" type="date" value="{{ old('modal') === 'create' ? old('date_naissance') : '' }}" required max="{{ now()->subDay()->toDateString() }}" class="form-control"></div>
                <div class="col-md-6"><label class="form-label" for="create_identity">Numéro d’identité</label><input id="create_identity" name="numero_identite" value="{{ old('modal') === 'create' ? old('numero_identite') : '' }}" required maxlength="255" class="form-control"></div>
                <div class="col-12"><label class="form-label" for="create_password">Mot de passe provisoire</label><input id="create_password" name="mot_de_passe" type="password" required minlength="8" autocomplete="new-password" class="form-control"></div>
                <div class="col-12 d-flex justify-content-end gap-2"><button type="button" class="btn btn-light" data-close-user-modal>Annuler</button><button type="submit" class="btn btn-primary">Créer le compte</button></div>
            </form>
        </dialog>
        <dialog class="dp-user-modal" id="edit-user-modal" data-user-modal data-user-id="{{ old('modal') === 'edit' ? old('user_id') : '' }}" @if (old('modal') === 'edit') data-open-on-load="true" @endif aria-labelledby="edit-user-title">
            <div class="dp-user-modal__header">
                <div><p class="dp-user-modal__eyebrow">Djamanopay · Annuaire</p><h2 id="edit-user-title">Modifier le compte</h2></div>
                <button type="button" class="dp-user-modal__close" data-close-user-modal aria-label="Fermer">&times;</button>
            </div>
            <form method="POST" action="#" class="row g-3 p-4" data-edit-user-form>
                @csrf
                @method('PUT')
                <input type="hidden" name="modal" value="edit">
                <input type="hidden" name="user_id" value="{{ old('modal') === 'edit' ? old('user_id') : '' }}">
                <div class="col-md-6"><label class="form-label" for="edit_first_name">Prénom</label><input id="edit_first_name" name="prenom" required maxlength="255" autocomplete="given-name" class="form-control" value="{{ old('modal') === 'edit' ? old('prenom') : '' }}"></div>
                <div class="col-md-6"><label class="form-label" for="edit_last_name">Nom</label><input id="edit_last_name" name="nom" required maxlength="255" autocomplete="family-name" class="form-control" value="{{ old('modal') === 'edit' ? old('nom') : '' }}"></div>
                <div class="col-md-6"><label class="form-label" for="edit_email">Adresse e-mail</label><input id="edit_email" name="email" type="email" required maxlength="255" autocomplete="email" class="form-control" value="{{ old('modal') === 'edit' ? old('email') : '' }}"></div>
                <div class="col-md-6"><label class="form-label" for="edit_role">Rôle</label><select id="edit_role" name="role" required class="form-select">@foreach (['client' => 'Client', 'distributeur' => 'Distributeur', 'agent' => 'Agent'] as $value => $label)<option value="{{ $value }}" @selected(old('modal') === 'edit' && old('role') === $value)>{{ $label }}</option>@endforeach</select></div>
                <div class="col-md-6"><label class="form-label" for="edit_phone">Téléphone</label><input id="edit_phone" name="telephone" required maxlength="255" autocomplete="tel" class="form-control" value="{{ old('modal') === 'edit' ? old('telephone') : '' }}"></div>
                <div class="col-md-6"><label class="form-label" for="edit_address">Adresse</label><input id="edit_address" name="adresse" required maxlength="255" autocomplete="street-address" class="form-control" value="{{ old('modal') === 'edit' ? old('adresse') : '' }}"></div>
                <div class="col-md-6"><label class="form-label" for="edit_birth_date">Date de naissance</label><input id="edit_birth_date" name="date_naissance" type="date" required max="{{ now()->subDay()->toDateString() }}" class="form-control" value="{{ old('modal') === 'edit' ? old('date_naissance') : '' }}"></div>
                <div class="col-md-6"><label class="form-label" for="edit_identity">Numéro d’identité</label><input id="edit_identity" name="numero_identite" required maxlength="255" class="form-control" value="{{ old('modal') === 'edit' ? old('numero_identite') : '' }}"></div>
                <div class="col-md-6"><label class="form-label" for="edit_password">Nouveau mot de passe <span class="text-muted">(facultatif)</span></label><input id="edit_password" name="mot_de_passe" type="password" minlength="8" autocomplete="new-password" class="form-control"></div>
                <div class="col-md-6"><label class="form-label" for="edit_status">Statut</label><select id="edit_status" name="etat_compte" required class="form-select"><option value="1" @selected(old('modal') === 'edit' && old('etat_compte') == '1')>Actif</option><option value="0" @selected(old('modal') === 'edit' && old('etat_compte') == '0')>Désactivé</option></select></div>
                <div class="col-12 d-flex justify-content-end gap-2"><button type="button" class="btn btn-light" data-close-user-modal>Annuler</button><button type="submit" class="btn btn-primary">Enregistrer</button></div>
            </form>
        </dialog>
        <div class="row g-3 mb-4">
            @foreach ([
                ['Clients', $clientCount],
                ['Distributeurs', $distributorCount],
                ['Agents', $agentCount],
                ['Comptes utilisateurs actifs', $activeUserCount],
            ] as [$label, $count])
                <div class="col-xl-3 col-sm-6">
                    <div class="card"><div class="card-body">
                        <p class="text-muted mb-1">{{ $label }}</p>
                        <h4 class="mb-0">{{ number_format($count, 0, ',', ' ') }}</h4>
                    </div></div>
                </div>
            @endforeach
        </div>
        <div class="card">
            <div class="card-header">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <h4 class="mb-0">Comptes utilisateurs</h4>
                    <a class="btn btn-outline-primary btn-sm" href="#user-filters"><i class="fa fa-filter me-1" aria-hidden="true"></i>Recherche et filtres</a>
                </div>
            </div>
            <div class="card-body">
                <form id="user-filters" method="GET" action="{{ route('dashboard-utilisateurs') }}" class="row g-2 mb-4">
                    <div class="col-lg-5">
                        <label class="form-label" for="user_search">Rechercher un utilisateur</label>
                        <input id="user_search" type="search" class="form-control" name="search" value="{{ request('search') }}" placeholder="Nom, e-mail, téléphone ou numéro de compte">
                    </div>
                    <div class="col-sm-4 col-lg-2">
                        <label class="form-label" for="user_role_filter">Rôle</label>
                        <select id="user_role_filter" class="form-select" name="role">
                            <option value="">Tous les rôles</option>
                            @foreach (['client' => 'Client', 'distributeur' => 'Distributeur', 'agent' => 'Agent'] as $value => $label)
                                <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-4 col-lg-2">
                        <label class="form-label" for="user_status_filter">Statut du compte</label>
                        <select id="user_status_filter" class="form-select" name="status">
                            <option value="">Tous les statuts</option>
                            <option value="active" @selected(request('status') === 'active')>Actifs</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>Désactivés</option>
                        </select>
                    </div>
                    <div class="col-sm-4 col-lg-3 d-flex gap-2">
                        <button class="btn btn-primary flex-grow-1" type="submit"><i class="fa fa-search me-1" aria-hidden="true"></i>Rechercher</button>
                        @if (request()->hasAny(['search', 'role', 'status']))
                            <a class="btn btn-light" href="{{ route('dashboard-utilisateurs') }}">Effacer</a>
                        @endif
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Nom</th><th>E-mail</th><th>Téléphone</th><th>Adresse</th><th>Rôle</th><th>Solde comptes</th><th>Statut</th><th>Inscrit le</th><th>Actions</th></tr></thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->telephone }}</td>
                                    <td>{{ $user->adresse }}</td>
                                    <td><span class="dp-user-badge dp-user-badge--role">{{ ucfirst($user->role) }}</span></td>
                                    <td>{{ number_format((float) ($user->solde_total ?? 0), 0, ',', ' ') }} XOF</td>
                                    <td><span class="dp-user-badge {{ $user->etat_compte ? 'dp-user-badge--active' : 'dp-user-badge--inactive' }}">{{ $user->etat_compte ? 'Actif' : 'Désactivé' }}</span></td>
                                    <td>{{ $user->created_at?->format('d/m/Y') }}</td>
                                    <td>
                                        @if ((int) auth()->id() !== (int) $user->id)
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary" data-edit-user
                                                    data-user-id="{{ $user->id }}"
                                                    data-update-url="{{ route('agent.users.update', $user) }}"
                                                    data-first-name="{{ $user->prenom }}"
                                                    data-last-name="{{ $user->nom }}"
                                                    data-email="{{ $user->email }}"
                                                    data-role="{{ $user->role }}"
                                                    data-phone="{{ $user->telephone }}"
                                                    data-address="{{ $user->adresse }}"
                                                    data-birth-date="{{ $user->date_naissance?->format('Y-m-d') }}"
                                                    data-identity="{{ $user->numero_identite }}"
                                                    data-status="{{ $user->etat_compte ? '1' : '0' }}">
                                                    <i class="fa fa-pencil me-1" aria-hidden="true"></i>Modifier
                                                </button>
                                                <form method="POST" action="{{ route('agent.users.destroy', $user) }}" data-delete-user-form>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash me-1" aria-hidden="true"></i>Supprimer</button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-muted">Compte courant</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="py-5 text-center text-muted">Aucun utilisateur ne correspond à ces critères.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
