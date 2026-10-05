<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="auth-eyebrow">Rejoignez-nous</div>
        <h1 class="auth-title">Créez votre compte</h1>
        <p class="auth-subtitle">Quelques informations et votre espace sécurisé est prêt.</p>

        <x-validation-errors class="auth-alert" />

        <form class="auth-form" method="POST" action="{{ route('register') }}">
            @csrf

            <div class="auth-grid">
                <div>
                    <x-label for="prenom" value="Prénom" />
                    <x-input id="prenom" type="text" name="prenom" :value="old('prenom')" required autofocus autocomplete="given-name" />
                </div>

                <div>
                    <x-label for="nom" value="Nom" />
                    <x-input id="nom" type="text" name="nom" :value="old('nom')" required autocomplete="family-name" />
                </div>

                <div>
                    <x-label for="email" value="Adresse e-mail" />
                    <x-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" />
                </div>

                <div>
                    <x-label for="telephone" value="Téléphone" />
                    <x-input id="telephone" type="tel" name="telephone" :value="old('telephone')" required autocomplete="tel" />
                </div>

                <div>
                    <x-label for="adresse" value="Adresse" />
                    <x-input id="adresse" type="text" name="adresse" :value="old('adresse')" required autocomplete="street-address" />
                </div>

                <div>
                    <x-label for="date_naissance" value="Date de naissance" />
                    <x-input id="date_naissance" type="date" name="date_naissance" :value="old('date_naissance')" required max="{{ now()->subDay()->toDateString() }}" />
                </div>

                <div>
                    <x-label for="numero_identite" value="Numéro d’identité" />
                    <x-input id="numero_identite" type="text" name="numero_identite" :value="old('numero_identite')" required autocomplete="off" />
                </div>
            </div>

            <div class="mt-4">
                <x-label for="password" value="Mot de passe" />
                <x-input id="password" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="Confirmer le mot de passe" />
                <x-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="mt-6">
                <x-button class="auth-submit w-full">
                    {{ __('Créer mon compte') }}
                </x-button>
            </div>
        </form>

        <div class="auth-switch">
            Vous avez déjà un compte ?
            <a class="auth-link" href="{{ route('login') }}">Se connecter</a>
        </div>
    </x-authentication-card>
</x-guest-layout>
