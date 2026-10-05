<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    @php($profile = auth()->user())
    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <section class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-blue-950 via-blue-700 to-sky-500 p-8 text-white shadow-xl">
                <div class="flex flex-wrap items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        <div class="grid h-20 w-20 place-items-center rounded-2xl border border-white/30 bg-white/15 text-2xl font-bold">
                            {{ mb_strtoupper(mb_substr($profile->prenom, 0, 1).mb_substr($profile->nom, 0, 1)) }}
                        </div>
                        <div>
                            <p class="mb-1 text-sm font-semibold uppercase tracking-widest text-blue-100">Mon espace personnel</p>
                            <h1 class="text-2xl font-bold">{{ $profile->name }}</h1>
                            <p class="mt-1 text-blue-100">{{ ucfirst($profile->role) }} · {{ $profile->email }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="#edit-profile" class="rounded-xl bg-white px-5 py-3 text-sm font-semibold text-blue-700 shadow transition hover:-translate-y-0.5 hover:shadow-lg">
                            Modifier mon profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-xl border border-white/40 bg-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/20">
                                Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            </section>

            <section class="mb-8 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">Informations du profil</h2>
                        <p class="mt-1 text-sm text-slate-500">Vos informations personnelles enregistrées.</p>
                    </div>
                    <a href="#edit-profile" class="text-sm font-semibold text-blue-600 hover:text-blue-800">Modifier</a>
                </div>
                <dl class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ([
                        'Téléphone' => $profile->telephone,
                        'Adresse' => $profile->adresse,
                        'Date de naissance' => $profile->date_naissance?->format('d/m/Y'),
                        'Numéro d’identité' => $profile->numero_identite,
                        'Statut du compte' => $profile->etat_compte ? 'Actif' : 'Désactivé',
                    ] as $label => $value)
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">{{ $label }}</dt>
                            <dd class="mt-1 font-medium text-slate-700">{{ $value ?: 'Non renseigné' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>

            <div id="edit-profile" class="scroll-mt-8">
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                @livewire('profile.update-profile-information-form')

                <x-section-border />
            @endif

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.update-password-form')
                </div>

                <x-section-border />
            @endif

            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.two-factor-authentication-form')
                </div>

                <x-section-border />
            @endif

            <div class="mt-10 sm:mt-0">
                @livewire('profile.logout-other-browser-sessions-form')
            </div>

            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <x-section-border />

                <div class="mt-10 sm:mt-0">
                    @livewire('profile.delete-user-form')
                </div>
            @endif
            </div>
        </div>
    </div>
</x-app-layout>
