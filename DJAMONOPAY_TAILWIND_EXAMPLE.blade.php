{{-- Exemple d'utilisation du thème Tailwind CSS Vert/Blanc/Gold pour DjamonoPay --}}
{{-- Ce fichier montre comment utiliser les différents composants dans chaque type de dashboard --}}

{{-- ========================================== --}}
{{-- EXEMPLE 1: DASHBOARD AGENT --}}
{{-- ========================================== --}}

@extends('layouts.dashboard-agent')

@section('dashboard-title', 'Tableau de bord Agent')
@section('dashboard-subtitle', 'Gestion des transactions et utilisateurs pour le ' . date('F Y'))

@section('dashboard-header-actions')
    <button class="btn-agent-secondary flex items-center gap-2">
        <x-icon name="download" class="w-5 h-5" />
        Exporter
    </button>
    <button class="btn-agent-primary flex items-center gap-2">
        <x-icon name="plus" class="w-5 h-5" />
        Nouvelle Transaction
    </button>
@endsection

@section('dashboard-content')
    {{-- Section des métriques principales --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-metric-card-agent 
            value="1,234,567 FCFA" 
            label="Revenu Total" 
            icon="currency-dollar" 
            trend="12.5"
        />
        
        <x-metric-card-agent 
            value="892" 
            label="Transactions" 
            icon="credit-card" 
            trend="8.2"
        />
        
        <x-metric-card-agent 
            value="234" 
            label="Clients Actifs" 
            icon="users" 
            trend="5.1"
        />
        
        <x-metric-card-agent 
            value="98.5%" 
            label="Taux de succès" 
            icon="check-circle" 
            trend="0.5"
        />
    </div>

    {{-- Cartes principales --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Carte de solde --}}
        <div class="card-agent lg:col-span-2">
            <h3 class="text-lg font-semibold text-primary-800 mb-4">Solde Actuel</h3>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-3xl font-bold text-primary-700">2,847,392 FCFA</p>
                    <p class="text-sm text-primary-500 mt-1">Dernière mise à jour: {{ now()->format('H:i') }}</p>
                </div>
                <div class="flex gap-4">
                    <button class="btn-agent-secondary text-sm">Dépôt</button>
                    <button class="btn-agent-primary text-sm">Retrait</button>
                </div>
            </div>
            <div class="mt-6 h-32 bg-gradient-to-r from-primary-50 to-primary-100 rounded-xl flex items-center justify-center">
                <p class="text-primary-500">Graphique de solde</p>
            </div>
        </div>

        {{-- Carte rapide --}}
        <div class="card-agent">
            <h3 class="text-lg font-semibold text-primary-800 mb-4">Actions rapides</h3>
            <div class="space-y-3">
                <button class="w-full text-left p-3 rounded-lg hover:bg-primary-50 transition-colors duration-200 flex items-center gap-3">
                    <x-icon name="plus-circle" class="w-5 h-5 text-primary-500" />
                    <span>Nouveau Client</span>
                </button>
                <button class="w-full text-left p-3 rounded-lg hover:bg-primary-50 transition-colors duration-200 flex items-center gap-3">
                    <x-icon name="repeat" class="w-5 h-5 text-primary-500" />
                    <span>Transaction Rapide</span>
                </button>
                <button class="w-full text-left p-3 rounded-lg hover:bg-primary-50 transition-colors duration-200 flex items-center gap-3">
                    <x-icon name="printer" class="w-5 h-5 text-primary-500" />
                    <span>Imprimer Reçu</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Dernières transactions --}}
    <div class="card-agent">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-primary-800">Dernières Transactions</h3>
            <button class="btn-agent-secondary text-sm">Voir tout</button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="table-agent w-full">
                <thead>
                    <tr>
                        <th class="table-agent-header">Date</th>
                        <th class="table-agent-header">Client</th>
                        <th class="table-agent-header">Montant</th>
                        <th class="table-agent-header">Type</th>
                        <th class="table-agent-header">Statut</th>
                        <th class="table-agent-header">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentTransactions as $transaction)
                    <tr class="table-agent-row">
                        <td class="table-agent-cell">{{ $transaction->date->format('d/m/Y H:i') }}</td>
                        <td class="table-agent-cell font-medium">{{ $transaction->client->name }}</td>
                        <td class="table-agent-cell">{{ number_format($transaction->amount, 0, ',', ' ') }} FCFA</td>
                        <td class="table-agent-cell">
                            <span class="badge-agent">
                                {{ $transaction->type }}
                            </span>
                        </td>
                        <td class="table-agent-cell">
                            @if($transaction->status === 'completed')
                                <span class="badge-agent bg-green-100 text-green-800">Complété</span>
                            @elseif($transaction->status === 'pending')
                                <span class="badge-agent bg-yellow-100 text-yellow-800">En attente</span>
                            @else
                                <span class="badge-agent bg-red-100 text-red-800">Échoué</span>
                            @endif
                        </td>
                        <td class="table-agent-cell">
                            <button class="text-primary-500 hover:text-primary-700 transition-colors">
                                <x-icon name="eye" class="w-5 h-5" />
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Statistiques --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card-agent">
            <h3 class="text-lg font-semibold text-primary-800 mb-4">Transactions par Jour</h3>
            <div class="h-64 bg-gradient-to-br from-primary-50 to-white rounded-xl flex items-center justify-center">
                <p class="text-primary-400">Graphique en barres</p>
            </div>
        </div>
        
        <div class="card-agent">
            <h3 class="text-lg font-semibold text-primary-800 mb-4">Répartition par Type</h3>
            <div class="h-64 bg-gradient-to-br from-primary-50 to-white rounded-xl flex items-center justify-center">
                <p class="text-primary-400">Graphique en camembert</p>
            </div>
        </div>
    </div>
@endsection


{{-- ========================================== --}}
{{-- EXEMPLE 2: DASHBOARD DISTRIBUTEUR --}}
{{-- ========================================== --}}

@extends('layouts.dashboard-distributeur')

@section('dashboard-title', 'Tableau de bord Distributeur')
@section('dashboard-subtitle', 'Gestion des agents et commissions')

@section('dashboard-header-actions')
    <button class="btn-distributeur-secondary flex items-center gap-2">
        <x-icon name="users" class="w-5 h-5" />
        Gérer les Agents
    </button>
    <button class="btn-distributeur-primary flex items-center gap-2">
        <x-icon name="plus" class="w-5 h-5" />
        Ajouter un Agent
    </button>
@endsection

@section('dashboard-content')
    {{-- Statistiques principales --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-metric-card-distributeur 
            value="45" 
            label="Agents Actifs" 
            icon="users" 
            trend="2.5"
        />
        
        <x-metric-card-distributeur 
            value="12,345,678 FCFA" 
            label="Commissions Totales" 
            icon="award" 
            trend="15.3"
        />
        
        <x-metric-card-distributeur 
            value="23,456" 
            label="Transactions Totales" 
            icon="credit-card" 
            trend="8.7"
        />
    </div>

    {{-- Cartes d'information --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="card-distributeur">
            <h3 class="text-lg font-semibold text-secondary-800 mb-4">Commission du Mois</h3>
            <div class="flex items-center justify-between mb-6">
                <div>
                    <p class="text-3xl font-bold text-secondary-700">2,847,392 FCFA</p>
                    <p class="text-sm text-secondary-500 mt-1">Moyenne quotidienne: 94,913 FCFA</p>
                </div>
                <div class="w-20 h-20 bg-secondary-100 rounded-full flex items-center justify-center">
                    <x-icon name="trending-up" class="w-10 h-10 text-secondary-600" />
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-secondary-600">Cette semaine:</span>
                    <span class="font-semibold text-secondary-700">856,000 FCFA</span>
                </div>
                <div class="w-full bg-secondary-100 rounded-full h-2">
                    <div class="bg-secondary-500 h-2 rounded-full" style="width: 75%"></div>
                </div>
            </div>
        </div>

        <div class="card-distributeur">
            <h3 class="text-lg font-semibold text-secondary-800 mb-4">Performance des Agents</h3>
            <div class="space-y-4">
                @foreach($topAgents as $agent)
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-secondary-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-secondary-600 font-semibold">{{ substr($agent->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-secondary-800">{{ $agent->name }}</p>
                            <p class="text-xs text-secondary-500">{{ $agent->transactions_count }} transactions</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-secondary-700">{{ number_format($agent->volume, 0, ',', ' ') }} FCFA</p>
                        <p class="text-xs text-secondary-500">Volume</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Liste des agents --}}
    <div class="card-distributeur">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-secondary-800">Tous les Agents</h3>
            <button class="btn-distributeur-secondary text-sm">
                <x-icon name="filter" class="w-4 h-4 mr-1" />
                Filtrer
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="table-distributeur w-full">
                <thead>
                    <tr>
                        <th class="table-distributeur-header">Agent</th>
                        <th class="table-distributeur-header">Email</th>
                        <th class="table-distributeur-header">Téléphone</th>
                        <th class="table-distributeur-header">Statut</th>
                        <th class="table-distributeur-header">Volume</th>
                        <th class="table-distributeur-header">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($agents as $agent)
                    <tr class="table-distributeur-row">
                        <td class="table-distributeur-cell">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-secondary-100 rounded-full flex items-center justify-center mr-2">
                                    <span class="text-secondary-600 text-sm font-semibold">{{ substr($agent->name, 0, 1) }}</span>
                                </div>
                                <span class="font-medium">{{ $agent->name }}</span>
                            </div>
                        </td>
                        <td class="table-distributeur-cell">{{ $agent->email }}</td>
                        <td class="table-distributeur-cell">{{ $agent->phone }}</td>
                        <td class="table-distributeur-cell">
                            <span class="badge-distributeur">Actif</span>
                        </td>
                        <td class="table-distributeur-cell font-semibold">{{ number_format($agent->volume, 0, ',', ' ') }} FCFA</td>
                        <td class="table-distributeur-cell">
                            <div class="flex gap-2">
                                <button class="text-secondary-500 hover:text-secondary-700">
                                    <x-icon name="eye" class="w-5 h-5" />
                                </button>
                                <button class="text-secondary-500 hover:text-secondary-700">
                                    <x-icon name="pencil" class="w-5 h-5" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection


{{-- ========================================== --}}
{{-- EXEMPLE 3: DASHBOARD CLIENT --}}
{{-- ========================================== --}}

@extends('layouts.dashboard-client')

@section('dashboard-title', 'Mon Tableau de bord')
@section('dashboard-subtitle', 'Vos transactions et historique')

@section('dashboard-header-actions')
    <button class="btn-client-secondary flex items-center gap-2">
        <x-icon name="clock" class="w-5 h-5" />
        Historique
    </button>
    <button class="btn-client-primary flex items-center gap-2">
        <x-icon name="plus" class="w-5 h-5" />
        Nouveau Paiement
    </button>
@endsection

@section('dashboard-content')
    {{-- Solde et Actions Rapides --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Carte de Solde --}}
        <div class="card-client lg:col-span-2">
            <h3 class="text-lg font-semibold text-accent-emerald-800 mb-2">Solde Actuel</h3>
            <p class="text-4xl font-bold text-accent-emerald-700 mb-4">125,000 FCFA</p>
            <div class="flex gap-4">
                <button class="btn-client-primary flex-1">
                    <x-icon name="plus" class="w-5 h-5 mr-1" />
                    Dépôt
                </button>
                <button class="btn-client-secondary flex-1">
                    <x-icon name="minus" class="w-5 h-5 mr-1" />
                    Retrait
                </button>
            </div>
            <div class="mt-6 h-24 bg-gradient-to-r from-accent-emerald-50 to-accent-emerald-100 rounded-xl flex items-center justify-center">
                <p class="text-accent-emerald-500">Graphique de solde</p>
            </div>
        </div>

        {{-- Carte de profil --}}
        <div class="profile-card-client">
            <div class="profile-image bg-accent-emerald-200 text-accent-emerald-700 flex items-center justify-center text-2xl font-bold">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <h4 class="profile-name">{{ auth()->user()->name }}</h4>
            <p class="profile-role">{{ auth()->user()->role }}</p>
            <div class="mt-4 pt-4 border-t border-accent-emerald-300">
                <p class="text-sm text-accent-emerald-600 mb-1">Membre depuis:</p>
                <p class="text-sm font-medium text-accent-emerald-800">{{ auth()->user()->created_at->format('d M Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Métriques Principales --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
        <x-metric-card-client 
            value="25" 
            label="Dépôts" 
            icon="arrow-down-circle" 
            trend="5"
        />
        
        <x-metric-card-client 
            value="18" 
            label="Retraits" 
            icon="arrow-up-circle" 
            trend="-2"
        />
        
        <x-metric-card-client 
            value="5" 
            label="En Attente" 
            icon="clock" 
            trend="0"
        />
        
        <x-metric-card-client 
            value="98%" 
            label="Taux de succès" 
            icon="check-circle" 
            trend="0.5"
        />
    </div>

    {{-- Dernières Transactions --}}
    <div class="card-client">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-accent-emerald-800">Dernières Transactions</h3>
            <button class="btn-client-secondary text-sm">Voir tout</button>
        </div>
        
        <div class="space-y-4">
            @foreach($recentTransactions as $transaction)
            <div class="flex items-center justify-between p-4 bg-accent-emerald-50 rounded-xl">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-accent-emerald-100 rounded-xl flex items-center justify-center mr-4">
                        @if($transaction->type === 'deposit')
                            <x-icon name="arrow-down" class="w-6 h-6 text-accent-emerald-600" />
                        @elseif($transaction->type === 'withdrawal')
                            <x-icon name="arrow-up" class="w-6 h-6 text-accent-emerald-600" />
                        @else
                            <x-icon name="repeat" class="w-6 h-6 text-accent-emerald-600" />
                        @endif
                    </div>
                    <div>
                        <p class="font-medium text-accent-emerald-800">{{ $transaction->description }}</p>
                        <p class="text-sm text-accent-emerald-500">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-semibold 
                        @if($transaction->type === 'deposit') text-green-600 
                        @elseif($transaction->type === 'withdrawal') text-red-600 
                        @else text-accent-emerald-600 @endif">
                        {{ $transaction->type === 'withdrawal' ? '-' : '+' }}
                        {{ number_format($transaction->amount, 0, ',', ' ') }} FCFA
                    </p>
                    <p class="text-sm text-accent-emerald-500 mt-1">
                        @if($transaction->status === 'completed')
                            <span class="badge-client">Complété</span>
                        @elseif($transaction->status === 'pending')
                            <span class="badge-client bg-yellow-100 text-yellow-800">En attente</span>
                        @else
                            <span class="badge-client bg-red-100 text-red-800">Échoué</span>
                        @endif
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Statistiques Personnelles --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card-client">
            <h3 class="text-lg font-semibold text-accent-emerald-800 mb-4">Dépenses par Catégorie</h3>
            <div class="h-64 bg-gradient-to-br from-accent-emerald-50 to-white rounded-xl flex items-center justify-center">
                <p class="text-accent-emerald-400">Graphique en camembert</p>
            </div>
        </div>
        
        <div class="card-client">
            <h3 class="text-lg font-semibold text-accent-emerald-800 mb-4">Historique du Solde</h3>
            <div class="h-64 bg-gradient-to-br from-accent-emerald-50 to-white rounded-xl flex items-center justify-center">
                <p class="text-accent-emerald-400">Graphique en courbe</p>
            </div>
        </div>
    </div>

    {{-- notifications --}}
    @if(auth()->user()->unreadNotifications->count() > 0)
    <div class="card-client mt-6">
        <h3 class="text-lg font-semibold text-accent-emerald-800 mb-4">Notifications</h3>
        <div class="space-y-3">
            @foreach(auth()->user()->unreadNotifications->take(5) as $notification)
            <div class="notification-client">
                <p class="notification-title">{{ $notification->data['title'] ?? 'Nouvelle notification' }}</p>
                <p class="text-sm text-accent-emerald-600">{{ $notification->data['message'] ?? '' }}</p>
                <p class="notification-time">{{ $notification->created_at->diffForHumans() }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif
@endsection