@extends('layouts.simple.master')

@section('title', 'Espace '.$title)

@section('breadcrumb-title')
    <h3>Mon espace {{ strtolower($title) }}</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item active">Portefeuille</li>
@endsection

@section('content')
    <main class="dp-dashboard mx-auto max-w-7xl space-y-6 px-4 pb-10 pt-4 sm:px-6 lg:px-8">
        @if (session('operation_message'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800" role="status">
                {{ session('operation_message') }}
            </div>
        @endif

        <section class="dp-hero relative overflow-hidden rounded-[2rem] px-6 py-8 text-white shadow-2xl shadow-emerald-950/20 sm:px-10 sm:py-10">
            <div class="pointer-events-none absolute -right-12 -top-24 h-72 w-72 rounded-full border-[36px] border-white/10"></div>
            <div class="pointer-events-none absolute -bottom-32 right-1/4 h-72 w-72 rounded-full bg-sky-300/20 blur-3xl"></div>
            <div class="relative grid gap-8 lg:grid-cols-[1.05fr_0.7fr_0.9fr] lg:items-end">
                <div>
                    <p class="mb-3 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-50">
                        <span class="h-2 w-2 rounded-full bg-emerald-300"></span>
                        Espace sécurisé · {{ $title }}
                    </p>
                    <h1 class="max-w-2xl text-3xl font-bold tracking-tight sm:text-5xl">Bonjour {{ auth()->user()->prenom }}, votre argent en mouvement.</h1>
                    <p class="mt-4 max-w-xl text-sm leading-6 text-emerald-50 sm:text-base">Suivez votre portefeuille, partagez votre QR de paiement et retrouvez vos mouvements au même endroit.</p>
                    <div class="dp-hero-art mt-2 h-40 sm:h-48 lg:h-56">
                        <x-dashboard-illustration class="h-full w-full max-w-xs" />
                        <p class="dp-hero-art-caption">Votre portefeuille, simplement en mouvement</p>
                    </div>
                    <div class="mt-7 flex flex-wrap gap-3">
                        <a href="{{ route('payments.new') }}" class="inline-flex items-center gap-2 rounded-xl bg-amber-300 px-5 py-3 font-semibold text-emerald-950 shadow-lg transition hover:-translate-y-0.5 hover:bg-amber-200">
                            <i class="fa fa-paper-plane" aria-hidden="true"></i>
                            Envoyer de l’argent
                        </a>
                        @if ($role === \App\Enums\UserRole::CLIENT)
                            <a href="{{ route('payments.new', ['operation' => 'retrait']) }}" class="inline-flex items-center gap-2 rounded-xl border border-white/30 bg-white/10 px-5 py-3 font-semibold text-white backdrop-blur transition hover:bg-white/20">
                                <i class="fa fa-money" aria-hidden="true"></i>
                                Retirer chez un distributeur
                            </a>
                        @endif
                    </div>
                </div>
                <div class="dp-balance-panel rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur-md sm:p-6">
                    <p class="text-sm font-medium text-blue-100">Solde disponible</p>
                    <div class="mt-2 flex flex-wrap items-baseline gap-2">
                        <span class="text-4xl font-bold tracking-tight sm:text-5xl" data-balance-value data-visible="{{ number_format($availableBalance, 0, ',', ' ') }}">{{ number_format($availableBalance, 0, ',', ' ') }}</span>
                        <span class="text-lg font-semibold text-blue-100">XOF</span>
                    </div>
                    <button type="button" class="mt-2 text-xs font-semibold text-blue-100 underline decoration-white/40 underline-offset-4 hover:text-white" data-toggle-balance aria-pressed="false">
                        Masquer le solde disponible
                    </button>
                    @if ($role === \App\Enums\UserRole::CLIENT && $availableBalance !== (float) $account->solde)
                        <p class="mt-1 text-xs text-blue-100">Retraits en attente : {{ number_format((float) $account->solde - $availableBalance, 0, ',', ' ') }} XOF réservés.</p>
                    @endif
                    <div class="mt-5 flex items-center justify-between border-t border-white/15 pt-4 text-sm">
                        <span class="text-blue-100">Compte</span>
                        <button type="button" class="inline-flex items-center gap-2 font-semibold text-white hover:text-sky-100" data-copy-account="{{ $account->numero_compte }}">
                            {{ $account->numero_compte }}
                            <i class="fa fa-copy" aria-hidden="true"></i>
                        </button>
                    </div>
                    <p class="mt-2 text-right text-xs text-blue-100" data-copy-feedback aria-live="polite"></p>
                </div>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Résumé du portefeuille">
            @foreach ([
                ['Transactions envoyées', $sentCount, 'fa-arrow-up'],
                ['Transactions reçues', $receivedCount, 'fa-arrow-down'],
                ['Total envoyé', number_format((float) $sentAmount, 0, ',', ' ').' XOF', 'fa-paper-plane'],
                ['Total reçu', number_format((float) $receivedAmount, 0, ',', ' ').' XOF', 'fa-wallet'],
            ] as [$label, $value, $icon])
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

        <section class="grid gap-6 xl:grid-cols-[1.55fr_0.8fr]">
            <article class="dp-chart-card rounded-2xl border border-blue-100 bg-white p-5 shadow-sm sm:p-7">
                <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-blue-600">Activité financière</p>
                        <h2 class="mt-1 text-xl font-bold text-slate-900">Vos mouvements</h2>
                        <p class="mt-1 text-sm text-slate-500">Paiements envoyés et reçus · 6 derniers mois</p>
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
                        <p class="mt-2 max-w-sm text-sm leading-6 text-slate-500">Le graphique se remplira automatiquement après vos premiers paiements, dépôts ou retraits confirmés.</p>
                    </div>
                @endif
            </article>

            <article class="dp-qr-card flex flex-col items-center rounded-2xl border border-blue-100 bg-gradient-to-b from-white to-blue-50/80 p-5 text-center shadow-sm sm:p-7">
                <div class="mb-2 grid h-12 w-12 place-items-center rounded-2xl bg-blue-100 text-xl text-blue-700">
                    <i class="fa fa-qrcode" aria-hidden="true"></i>
                </div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-blue-600">Recevoir un paiement</p>
                <h2 class="mt-1 text-xl font-bold text-slate-900">Votre QR code</h2>
                <p class="mt-2 max-w-xs text-sm leading-6 text-slate-500">Le scan ouvre un paiement prérempli avec votre compte. Le payeur choisit le montant.</p>
                <div class="mt-5 grid h-56 w-56 place-items-center rounded-2xl border border-blue-100 bg-white p-3 shadow-inner transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                    <canvas class="max-h-full max-w-full" data-payment-qr="{{ $paymentUrl }}" aria-label="QR code de paiement du compte {{ $account->numero_compte }}"></canvas>
                </div>
                <p class="mt-3 min-h-5 text-xs text-rose-600" data-qr-error aria-live="polite"></p>
                <button type="button" class="mt-2 inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-white px-4 py-2.5 text-sm font-semibold text-blue-800 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-blue-300 hover:bg-blue-50 hover:shadow-md" data-download-qr>
                    <i class="fa fa-download" aria-hidden="true"></i>
                    Télécharger le QR
                </button>
                <p class="mt-3 text-xs text-slate-400">Paiements en XOF · Frais client de 2 % déduits du montant reçu</p>
            </article>
        </section>

        @if ($role === \App\Enums\UserRole::DISTRIBUTEUR)
            <section class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
                <article class="dp-operation-card rounded-2xl border border-blue-100 bg-white p-5 shadow-sm sm:p-7">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-blue-600">Service de proximité</p>
                    <h2 class="mt-1 text-xl font-bold text-slate-900">Enregistrer un dépôt client</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">Remettez d’abord les espèces et débitez votre portefeuille distributeur. Le client reçoit le montant intégral; votre bonus de 1 % est crédité automatiquement.</p>
                    <form method="POST" action="{{ route('distributor.deposits.store') }}" class="mt-5 space-y-4">
                        @csrf
                        <input type="hidden" name="deposit_token" value="{{ $depositToken }}">
                        <div>
                            <label for="client_account" class="mb-2 block text-sm font-semibold text-slate-700">Numéro de compte client</label>
                            <input id="client_account" name="client_account" required autocomplete="off" placeholder="Ex. DP0000000123" value="{{ old('client_account') }}" class="block w-full rounded-xl border border-blue-200 bg-blue-50/40 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100">
                            @error('client_account') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="deposit_amount" class="mb-2 block text-sm font-semibold text-slate-700">Montant remis en espèces (XOF)</label>
                            <input id="deposit_amount" name="amount" type="number" min="1" max="1000000000" step="1" required inputmode="numeric" value="{{ old('amount') }}" class="block w-full rounded-xl border border-blue-200 bg-blue-50/40 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100">
                            @error('amount') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <button type="submit" class="dp-payment-action inline-flex w-full items-center justify-center gap-2 rounded-xl px-4 py-3 font-semibold text-white shadow-lg">
                            <i class="fa fa-plus-circle" aria-hidden="true"></i> Créditer le compte client
                        </button>
                    </form>
                </article>

                <article class="dp-table-card overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-sm">
                    <div class="border-b border-blue-50 px-5 py-5 sm:px-7">
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-blue-600">Remise d’espèces</p>
                        <h2 class="mt-1 text-xl font-bold text-slate-900">Retraits à confirmer <span class="ml-1 rounded-full bg-blue-100 px-2 py-1 text-sm text-blue-800">{{ $pendingWithdrawals->count() }}</span></h2>
                    </div>
                    <div class="divide-y divide-blue-50">
                        @forelse ($pendingWithdrawals as $withdrawal)
                            <div class="flex flex-wrap items-center justify-between gap-4 px-5 py-4 sm:px-7">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $withdrawal->compteSource?->user?->name ?? 'Client' }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $withdrawal->reference }} · Retirer {{ number_format((float) $withdrawal->montant - (float) $withdrawal->frais, 0, ',', ' ') }} XOF en espèces</p>
                                    <p class="mt-1 text-xs text-slate-500">Frais : {{ number_format((float) $withdrawal->frais, 0, ',', ' ') }} XOF</p>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <form method="POST" action="{{ route('distributor.withdrawals.complete', $withdrawal) }}" onsubmit="return confirm('Confirmez-vous avoir remis les espèces au client ?')">
                                        @csrf
                                        <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-bold text-white hover:bg-emerald-700">Espèces remises</button>
                                    </form>
                                    @if ($withdrawal->created_at && now()->lessThanOrEqualTo($withdrawal->created_at->copy()->addMinutes(15)))
                                        <form method="POST" action="{{ route('distributor.transactions.cancel', $withdrawal) }}" onsubmit="return confirm('Refuser cette demande de retrait ?')">
                                            @csrf
                                            <button type="submit" class="rounded-lg border border-rose-200 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-50">Refuser</button>
                                        </form>
                                    @else
                                        <span class="text-xs font-semibold text-amber-700">Annulation via assistance</span>
                                    @endif
                                </div>
                                @if ($withdrawal->created_at && now()->greaterThan($withdrawal->created_at->copy()->addMinutes(15)))
                                    @include('dashboard.partials.cancellation-request-form', [
                                        'transaction' => $withdrawal,
                                        'cancellationRequest' => $withdrawal->cancellationRequests->sortByDesc('created_at')->first(),
                                    ])
                                @endif
                            </div>
                        @empty
                            <p class="px-5 py-10 text-center text-sm text-slate-500 sm:px-7">Aucune demande de retrait à traiter.</p>
                        @endforelse
                    </div>
                </article>
            </section>

            <section class="dp-bonus-card relative overflow-hidden rounded-2xl border border-blue-100 bg-gradient-to-r from-blue-950 to-blue-700 p-5 text-white shadow-lg transition duration-300 hover:-translate-y-1 hover:shadow-xl sm:p-7">
                <p class="text-sm text-blue-100">Bonus distributeur cumulés</p>
                <p class="mt-1 text-3xl font-bold">{{ number_format((float) $bonusTotal, 0, ',', ' ') }} <span class="text-base text-blue-100">XOF</span></p>
                <p class="mt-2 text-xs text-blue-100">1 % des dépôts clients et retraits effectivement remis. Un bonus est repris si la transaction est annulée.</p>
            </section>
        @endif

        <section class="dp-table-card overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-blue-50 px-5 py-5 sm:px-7">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-blue-600">Historique</p>
                    <h2 class="mt-1 text-xl font-bold text-slate-900">Dernières transactions</h2>
                </div>
                <a href="{{ route('payments.new') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-900">Nouveau paiement <span aria-hidden="true">→</span></a>
            </div>
            <div class="divide-y divide-blue-50">
                @forelse ($transactions as $transaction)
                    @php($isOutgoing = $transaction->compte_source_id === $account->id)
                    @php($otherUser = $isOutgoing ? $transaction->compteDestination?->user : $transaction->compteSource?->user)
                    @php($label = match ($transaction->type) { 'depot' => 'Dépôt client', 'retrait' => 'Retrait', 'paiement_qr' => $isOutgoing ? 'Paiement envoyé' : 'Paiement reçu', 'remboursement_annulation' => 'Remboursement d’annulation', default => ucfirst(str_replace('_', ' ', $transaction->type)) })
                    <div class="flex flex-wrap items-center justify-between gap-4 px-5 py-4 sm:px-7">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl {{ $isOutgoing ? 'bg-blue-50 text-blue-700' : 'bg-emerald-50 text-emerald-700' }}">
                                <i class="fa {{ $isOutgoing ? 'fa-arrow-up' : 'fa-arrow-down' }}" aria-hidden="true"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-slate-900">{{ $label }}{{ $otherUser ? ' · '.$otherUser->name : '' }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $transaction->reference }} · {{ $transaction->created_at?->format('d/m/Y à H:i') }} · {{ ucfirst(str_replace('_', ' ', $transaction->statut)) }}</p>
                            </div>
                        </div>
                        @if ($transaction->created_at && now()->greaterThan($transaction->created_at->copy()->addMinutes(15)) && in_array($transaction->statut, ['terminee', 'en_attente'], true))
                            @include('dashboard.partials.cancellation-request-form', [
                                'transaction' => $transaction,
                                'cancellationRequest' => $transaction->cancellationRequests->sortByDesc('created_at')->first(),
                            ])
                        @endif
                        <div class="flex items-center gap-3">
                            @if (
                                in_array($transaction->statut, ['terminee', 'en_attente'], true)
                                && $transaction->created_at
                                && now()->lessThanOrEqualTo($transaction->created_at->copy()->addMinutes(15))
                                && (
                                    $role === \App\Enums\UserRole::CLIENT
                                    || ($role === \App\Enums\UserRole::DISTRIBUTEUR && (
                                        (int) $transaction->actor_id === (int) auth()->id()
                                        || (int) $transaction->processed_by_user_id === (int) auth()->id()
                                        || ($transaction->type === 'retrait' && $transaction->statut === 'en_attente' && (int) $transaction->compte_destination_id === (int) $account->id)
                                    ))
                                )
                            )
                                <form method="POST" action="{{ $role === \App\Enums\UserRole::CLIENT ? route('client.transactions.cancel', $transaction) : route('distributor.transactions.cancel', $transaction) }}" onsubmit="return confirm('Annuler cette transaction ? La reprise ne créera pas de solde négatif.')">
                                    @csrf
                                    <button type="submit" class="rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50">Annuler</button>
                                </form>
                            @endif
                            <p class="whitespace-nowrap font-bold {{ $isOutgoing ? 'text-slate-900' : 'text-emerald-700' }}">{{ $isOutgoing ? '−' : '+' }}{{ number_format($isOutgoing ? (float) $transaction->montant : (float) $transaction->montant - (float) $transaction->frais, 0, ',', ' ') }} XOF</p>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-12 text-center sm:px-7">
                        <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-blue-50 text-xl text-blue-500"><i class="fa fa-exchange" aria-hidden="true"></i></span>
                        <p class="mt-4 font-semibold text-slate-800">Aucun mouvement pour le moment</p>
                        <p class="mt-1 text-sm text-slate-500">Vos paiements apparaîtront ici dès votre première transaction.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </main>
@endsection
