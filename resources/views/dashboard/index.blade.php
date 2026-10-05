@extends('layouts.simple.master')

@section('title', 'Pilotage agent')

@section('breadcrumb-title')
    <h3>Pilotage financier</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item active">Espace agent</li>
@endsection

@section('content')
    <main class="dp-dashboard mx-auto max-w-7xl space-y-6 px-4 pb-10 pt-4 sm:px-6 lg:px-8">
        @if (session('operation_message'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800" role="status">
                {{ session('operation_message') }}
            </div>
        @endif

        <section class="dp-hero relative overflow-hidden rounded-[2rem] px-6 py-8 text-white shadow-2xl shadow-blue-950/20 sm:px-10 sm:py-10">
            <div class="pointer-events-none absolute -right-12 -top-24 h-72 w-72 rounded-full border-[36px] border-white/10"></div>
            <div class="relative grid gap-6 lg:grid-cols-[1.15fr_0.75fr_0.8fr] lg:items-end">
                <div>
                    <p class="mb-3 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-50">
                        <span class="h-2 w-2 rounded-full bg-emerald-300"></span> Supervision sécurisée
                    </p>
                    <h1 class="max-w-2xl text-3xl font-bold tracking-tight sm:text-5xl">Bonjour {{ auth()->user()->prenom }}, gardez le contrôle.</h1>
                    <p class="mt-4 max-w-xl text-sm leading-6 text-emerald-50 sm:text-base">Suivez les comptes, alimentez les distributeurs et traitez les opérations clients depuis un espace unique.</p>
                    <a href="{{ route('dashboard-utilisateurs') }}" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-amber-300 px-5 py-3 font-semibold text-emerald-950 shadow-lg transition hover:-translate-y-0.5 hover:bg-amber-200">
                        <i class="fa fa-users" aria-hidden="true"></i> Gérer les utilisateurs
                    </a>
                </div>
                <div class="dp-hero-art h-40 sm:h-48 lg:h-56">
                    <x-dashboard-illustration class="h-full w-full max-w-xs" />
                    <p class="dp-hero-art-caption">Pilotez chaque opération<br>en toute confiance</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    @foreach ([
                        ['Clients', (int) $userCounts->get(\App\Enums\UserRole::CLIENT, 0)],
                        ['Distributeurs', (int) $userCounts->get(\App\Enums\UserRole::DISTRIBUTEUR, 0)],
                        ['Agents', (int) $userCounts->get(\App\Enums\UserRole::AGENT, 0)],
                        ['Comptes actifs', (int) $activeAccounts],
                    ] as [$label, $value])
                        <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm transition duration-300 hover:-translate-y-1 hover:bg-white/15">
                            <p class="text-xs font-medium text-emerald-50">{{ $label }}</p>
                            <p class="mt-1 text-2xl font-bold">{{ number_format($value, 0, ',', ' ') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Indicateurs financiers">
            @foreach ([
                ['Transactions enregistrées', number_format($transactionCount, 0, ',', ' '), 'fa-exchange', 'blue'],
                ['À traiter', number_format($pendingCount, 0, ',', ' '), 'fa-clock-o', 'amber'],
                ['Soldes des comptes', number_format((float) $totalBalance, 0, ',', ' ').' XOF', 'fa-credit-card', 'cyan'],
                ['Volume terminé', number_format((float) $successfulAmount, 0, ',', ' ').' XOF', 'fa-line-chart', 'indigo'],
            ] as [$label, $value, $icon, $color])
                <article class="dp-metric-card rounded-2xl border border-blue-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg hover:shadow-blue-900/5">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
                            <p class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{{ $value }}</p>
                        </div>
                        <span class="dp-stat-icon grid h-12 w-12 shrink-0 place-items-center rounded-2xl text-lg text-white shadow-lg">
                            <i class="fa {{ $icon }}" aria-hidden="true"></i>
                        </span>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.1fr_1.6fr]">
            <article class="dp-operation-card rounded-2xl border border-blue-100 bg-white p-5 shadow-sm sm:p-7">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-blue-600">Trésorerie contrôlée</p>
                <h2 class="mt-1 text-xl font-bold text-slate-900">Créditer un distributeur</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">Chaque alimentation est datée, attribuée à votre compte agent et visible dans l’historique.</p>
                <form method="POST" action="{{ route('agent.distributors.credit') }}" class="mt-5 space-y-4">
                    @csrf
                    <input type="hidden" name="credit_token" value="{{ $creditToken }}">
                    <div>
                        <label for="distributor_account" class="mb-2 block text-sm font-semibold text-slate-700">Distributeur</label>
                        <select id="distributor_account" name="distributor_account" required class="block w-full rounded-xl border border-blue-200 bg-blue-50/40 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100">
                            <option value="">Choisir un compte</option>
                            @foreach ($distributors as $distributor)
                                @php($distributorAccount = $distributor->comptes->first())
                                @if ($distributorAccount)
                                    <option value="{{ $distributorAccount->id }}" @selected(old('distributor_account') == $distributorAccount->id)>
                                        {{ $distributor->name }} · {{ $distributorAccount->numero_compte }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('distributor_account') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="credit_amount" class="mb-2 block text-sm font-semibold text-slate-700">Montant à créditer</label>
                        <div class="relative">
                            <input id="credit_amount" name="amount" type="number" min="1" max="1000000000" step="1" required inputmode="numeric" value="{{ old('amount') }}" class="block w-full rounded-xl border border-blue-200 bg-white px-4 py-3 pr-16 text-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-400">XOF</span>
                        </div>
                        @error('amount') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="dp-payment-action inline-flex w-full items-center justify-center gap-2 rounded-xl px-4 py-3 font-semibold text-white shadow-lg">
                        <i class="fa fa-plus-circle" aria-hidden="true"></i> Confirmer le crédit
                    </button>
                </form>
            </article>

            <article class="dp-chart-card rounded-2xl border border-blue-100 bg-white p-5 shadow-sm sm:p-7">
                <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-blue-600">Tendance réelle</p>
                        <h2 class="mt-1 text-xl font-bold text-slate-900">Volume des transactions</h2>
                        <p class="mt-1 text-sm text-slate-500">Six derniers mois · données de la base</p>
                    </div>
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">XOF</span>
                </div>
                @php($hasChartActivity = collect($monthlyChart['series'])->contains(fn ($series) => collect($series['data'])->contains(fn ($value) => (float) $value > 0)))
                @if ($hasChartActivity)
                    <div class="min-h-72" data-dashboard-chart data-chart-library-src="{{ asset('assets/js/chart/apex-chart/apex-chart.js') }}" data-chart='@json($monthlyChart)'></div>
                @else
                    <div class="grid min-h-72 content-center justify-items-center rounded-2xl border border-dashed border-blue-200 bg-blue-50/50 px-6 text-center" role="status">
                        <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white text-xl text-blue-600 shadow-sm"><i class="fa fa-line-chart" aria-hidden="true"></i></span>
                        <p class="mt-4 font-semibold text-slate-800">Aucune transaction sur les six derniers mois</p>
                        <p class="mt-2 max-w-sm text-sm leading-6 text-slate-500">Le graphique s’affichera dès qu’une opération financière aura été enregistrée.</p>
                    </div>
                @endif
            </article>
        </section>

        <section class="dp-table-card overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-blue-50 px-5 py-5 sm:px-7">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-blue-600">Opérations sensibles</p>
                    <h2 class="mt-1 text-xl font-bold text-slate-900">Transactions client à traiter</h2>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('agent.cancellation-requests.index') }}" class="text-sm font-semibold text-amber-700 hover:text-amber-900">Demandes d’annulation</a>
                    <a href="{{ route('dashboard-transactions') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-900">Historique complet →</a>
                </div>
            </div>
            <div class="divide-y divide-blue-50">
                @forelse ($clientTransactions as $transaction)
                    <div class="flex flex-wrap items-center justify-between gap-4 px-5 py-4 sm:px-7">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-blue-50 text-blue-700"><i class="fa fa-exchange" aria-hidden="true"></i></span>
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-slate-900">{{ $transaction->compteSource?->user?->name ?? 'Trésorerie' }} → {{ $transaction->compteDestination?->user?->name ?? 'Trésorerie' }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ ucfirst(str_replace('_', ' ', $transaction->type)) }} · {{ $transaction->reference }} · {{ ucfirst(str_replace('_', ' ', $transaction->statut)) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <p class="whitespace-nowrap font-bold text-slate-900">{{ number_format((float) $transaction->montant, 0, ',', ' ') }} {{ $transaction->devise }}</p>
                            @if ($transaction->created_at && now()->lessThanOrEqualTo($transaction->created_at->copy()->addMinutes(15)))
                                <form method="POST" action="{{ route('agent.transactions.cancel', $transaction) }}" onsubmit="return confirm('Annuler cette opération ? La reprise ne créera pas de solde négatif.')">
                                    @csrf
                                    <button type="submit" class="rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-50">Annuler</button>
                                </form>
                            @else
                                <a href="{{ route('agent.cancellation-requests.index') }}" class="text-xs font-semibold text-amber-700">Assistance requise</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-sm text-slate-500 sm:px-7">Aucune transaction client à traiter.</div>
                @endforelse
            </div>
        </section>

        <section class="dp-table-card overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-blue-50 px-5 py-5 sm:px-7">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-blue-600">Activité en direct</p>
                    <h2 class="mt-1 text-xl font-bold text-slate-900">Dernières transactions</h2>
                </div>
                <a href="{{ route('dashboard-transactions') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-900">Tout voir →</a>
            </div>
            <div class="divide-y divide-blue-50">
                @forelse ($recentTransactions as $transaction)
                    <div class="flex flex-wrap items-center justify-between gap-4 px-5 py-4 sm:px-7">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $transaction->compteSource?->user?->name ?? 'Trésorerie' }} → {{ $transaction->compteDestination?->user?->name ?? 'Trésorerie' }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $transaction->reference ?: '—' }} · {{ ucfirst(str_replace('_', ' ', $transaction->type)) }} · {{ ucfirst(str_replace('_', ' ', $transaction->statut)) }}</p>
                        </div>
                        <p class="font-bold text-blue-800">{{ number_format((float) $transaction->montant, 0, ',', ' ') }} {{ $transaction->devise }}</p>
                    </div>
                @empty
                    <p class="px-5 py-8 text-center text-sm text-slate-500">Aucune transaction enregistrée.</p>
                @endforelse
            </div>
        </section>

        <section class="dp-table-card overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-blue-50 px-5 py-5 sm:px-7">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-blue-600">Traçabilité</p>
                    <h2 class="mt-1 text-xl font-bold text-slate-900">Dernières activités</h2>
                </div>
                <a href="{{ route('dashboard-activitées') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-900">Journal complet →</a>
            </div>
            <div class="divide-y divide-blue-50">
                @forelse ($recentActivities as $activity)
                    <div class="flex flex-wrap items-start justify-between gap-3 px-5 py-4 sm:px-7">
                        <div class="flex min-w-0 items-start gap-3">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-sky-50 text-blue-700"><i class="fa fa-shield" aria-hidden="true"></i></span>
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-900">{{ $activity->description ?: $activity->action }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $activity->user?->name ?? 'Système' }} · {{ $activity->action ?: 'Activité' }}</p>
                            </div>
                        </div>
                        <time class="shrink-0 text-xs text-slate-500" datetime="{{ $activity->created_at?->toAtomString() }}">{{ $activity->created_at?->format('d/m/Y H:i') }}</time>
                    </div>
                @empty
                    <p class="px-5 py-8 text-center text-sm text-slate-500">Aucune activité enregistrée.</p>
                @endforelse
            </div>
        </section>
    </main>
@endsection
