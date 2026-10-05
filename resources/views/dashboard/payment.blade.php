@extends('layouts.simple.master')

@section('title', 'Effectuer un paiement')

@section('breadcrumb-title')
    <h3>Paiement sécurisé</h3>
@endsection


@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route(auth()->user()->role === \App\Enums\UserRole::CLIENT ? 'dashboard-client' : 'dashboard-distributeur') }}">Mon espace</a></li>
    <li class="breadcrumb-item active">Paiement</li>
@endsection

@section('content')
    <main class="dp-dashboard mx-auto max-w-6xl px-4 pb-10 pt-4 sm:px-6 lg:px-8">
        <a href="{{ route(auth()->user()->role === \App\Enums\UserRole::CLIENT ? 'dashboard-client' : 'dashboard-distributeur') }}" class="mb-5 inline-flex items-center gap-2 text-sm font-semibold text-blue-700 hover:text-blue-900">
            <i class="fa fa-arrow-left" aria-hidden="true"></i> Retour au tableau de bord
        </a>

        @if (session('payment_reference'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-emerald-900" role="status">
                <div class="flex items-start gap-3">
                    <span class="mt-0.5 text-xl"><i class="fa fa-check-circle" aria-hidden="true"></i></span>
                    <div>
                        <p class="font-bold">{{ session('withdrawal_pending') ? 'Demande de retrait envoyée' : 'Paiement effectué avec succès' }}</p>
                        <p class="mt-1 text-sm">
                            @if (session('withdrawal_pending'))
                                {{ number_format((int) session('payment_amount'), 0, ',', ' ') }} XOF sont réservés. Le distributeur confirmera la remise des espèces.
                            @else
                                Vous avez envoyé {{ number_format((int) session('payment_amount'), 0, ',', ' ') }} XOF. Le bénéficiaire reçoit {{ number_format((int) session('payment_received'), 0, ',', ' ') }} XOF après {{ number_format((int) session('payment_fee'), 0, ',', ' ') }} XOF de frais.
                            @endif
                        </p>
                        <p class="mt-1 text-xs text-emerald-700">Référence : {{ session('payment_reference') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <section class="grid overflow-hidden rounded-[2rem] border border-emerald-100 bg-white shadow-xl shadow-emerald-950/5 lg:grid-cols-[0.85fr_1.15fr]">
            <div class="dp-payment-hero relative overflow-hidden p-7 text-white sm:p-10">
                <div class="pointer-events-none absolute -bottom-20 -left-10 h-56 w-56 rounded-full border-[30px] border-white/10"></div>
                <div class="relative">
                    <span class="grid h-14 w-14 place-items-center rounded-2xl border border-white/20 bg-white/10 text-2xl"><i class="fa fa-shield" aria-hidden="true"></i></span>
                    <p class="mt-8 text-xs font-bold uppercase tracking-[0.18em] text-emerald-50">Transfert de portefeuille</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight">Un paiement simple et sûr.</h1>
                    <p class="mt-4 text-sm leading-6 text-emerald-50">
                        @if ($operation === 'retrait')
                            Choisissez un distributeur et le montant. Les fonds sont réservés jusqu’à la confirmation de remise des espèces. Frais : 2 %.
                        @else
                            Vérifiez le compte du bénéficiaire, choisissez le montant et confirmez. Les frais de 2 % sont déduits du montant reçu.
                        @endif
                    </p>
                    <x-dashboard-illustration class="mx-auto mt-5 h-44 w-full max-w-xs sm:h-52" />
                    <div class="mt-4 rounded-2xl border border-white/15 bg-white/10 p-5 backdrop-blur-sm">
                        <p class="text-sm text-emerald-50">Votre solde disponible</p>
                        <p class="mt-1 text-3xl font-bold">{{ number_format($availableBalance, 0, ',', ' ') }} <span class="text-base text-emerald-50">XOF</span></p>
                        <p class="mt-3 border-t border-white/15 pt-3 text-xs text-emerald-50">Compte {{ $account->numero_compte }}</p>
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-10">
                <div class="mb-7">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-700">Nouveau paiement</p>
                    <h2 class="mt-1 text-2xl font-bold text-slate-900">Détails du transfert</h2>
                    <p class="mt-2 text-sm text-slate-500">Les montants sont en francs CFA (XOF).</p>
                </div>

                <form method="POST" action="{{ route('payments.store') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="operation" value="{{ $operation }}">
                    <input type="hidden" name="payment_token" value="{{ old('payment_token', $paymentToken) }}">
                    <div>
                        <label for="recipient_account" class="mb-2 block text-sm font-semibold text-slate-700">{{ $operation === 'retrait' ? 'Distributeur qui remettra les espèces' : 'Téléphone du bénéficiaire' }}</label>
                        @if ($operation === 'retrait')
                            <select id="recipient_account" name="recipient_account" required class="block w-full rounded-xl border border-blue-200 bg-white px-4 py-3 text-sm font-medium text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100">
                                <option value="">Choisir un distributeur</option>
                                @foreach ($distributors as $distributorAccount)
                                    <option value="{{ $distributorAccount->numero_compte }}" @selected(old('recipient_account') === $distributorAccount->numero_compte)>
                                        {{ $distributorAccount->user->name }} · {{ $distributorAccount->numero_compte }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input
                                id="recipient_account"
                                name="recipient_account"
                                value="{{ old('recipient_account', $recipientNumber) }}"
                                @if ($recipientNumber) readonly @endif
                                required
                                autocomplete="off"
                                placeholder="Ex. 77 123 45 67 ou DP0000000123"
                                data-recipient-lookup
                                data-lookup-url="{{ $recipientLookupUrl }}"
                                data-operation="{{ $operation }}"
                                class="block w-full rounded-xl border border-blue-200 bg-blue-50/50 px-4 py-3 text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100 read-only:bg-blue-50"
                            >
                            <p class="mt-2 text-xs text-slate-500" data-recipient-lookup-status role="status" aria-live="polite">
                                Saisissez le numéro de téléphone associé au compte. Le nom s’affichera automatiquement.
                            </p>
                        @endif
                        @error('recipient_account')
                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                        @if ($operation !== 'retrait')
                            <div class="mt-3 flex items-center gap-3 rounded-xl border border-blue-100 bg-blue-50/70 p-3" data-recipient-card @if (! $recipient) hidden @endif>
                                <span class="grid h-10 w-10 place-items-center rounded-full bg-white font-bold text-blue-700" data-recipient-initials>@if ($recipient) {{ mb_substr($recipient->user->prenom, 0, 1) }}{{ mb_substr($recipient->user->nom, 0, 1) }} @endif</span>
                                <div>
                                    <p class="font-semibold text-slate-900" data-recipient-name>@if ($recipient) {{ $recipient->user->name }} @endif</p>
                                    <p class="text-xs text-slate-500" data-recipient-role>@if ($recipient) {{ ucfirst($recipient->user->role) }} · Bénéficiaire vérifié @endif</p>
                                </div>
                                <i class="fa fa-check-circle ml-auto text-lg text-emerald-600" aria-label="Compte vérifié" data-recipient-verified></i>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label for="amount" class="mb-2 block text-sm font-semibold text-slate-700">Montant à envoyer</label>
                        <div class="relative">
                            <input id="amount" name="amount" type="number" min="1" max="1000000000" step="1" value="{{ old('amount') }}" required inputmode="numeric" placeholder="0" class="block w-full rounded-xl border border-blue-200 bg-white px-4 py-4 pr-16 text-2xl font-bold text-slate-900 placeholder:text-slate-300 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 font-semibold text-slate-400">XOF</span>
                        </div>
                        @error('amount')
                            <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-slate-500">{{ $operation === 'retrait' ? 'Les fonds sont réservés. Le distributeur vous remettra le montant net après confirmation.' : 'Vérifiez le montant avant confirmation. Les frais sont de 2 % (arrondis au franc le plus proche).' }}</p>
                    </div>

                    <div class="space-y-2 rounded-xl bg-slate-50 px-4 py-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600">Montant envoyé</span>
                            <span class="font-semibold text-slate-800"><span data-payment-amount>0</span> XOF</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600">Frais (2 %)</span>
                            <span class="font-semibold text-slate-700"><span data-payment-fee>0</span> XOF</span>
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-200 pt-2">
                            <span class="font-semibold text-slate-700">{{ $operation === 'retrait' ? 'Espèces remises après confirmation' : 'Le bénéficiaire reçoit' }}</span>
                            <span class="font-bold text-emerald-700"><span data-payment-received>0</span> XOF</span>
                        </div>
                    </div>

                    <button type="submit" class="dp-payment-action inline-flex w-full items-center justify-center gap-2 rounded-xl px-5 py-3.5 font-semibold text-white shadow-lg shadow-blue-900/20 transition hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-blue-200">
                        <i class="fa fa-lock" aria-hidden="true"></i>
                        {{ $operation === 'retrait' ? 'Demander le retrait' : 'Confirmer le paiement' }}
                    </button>
                </form>
            </div>
        </section>
    </main>
@endsection
