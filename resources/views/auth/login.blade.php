<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="auth-eyebrow">Bienvenue à nouveau</div>
        <h1 class="auth-title">Connectez-vous</h1>
        <p class="auth-subtitle">Accédez à votre espace DjamonoPay en toute sécurité.</p>

        <x-validation-errors class="auth-alert" />

        @if (session('status'))
            <div class="auth-alert" style="border-color:#bbf7d0;background:#f0fdf4;color:#15803d">
                {{ session('status') }}
            </div>
        @endif

        <form class="auth-form" method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="Adresse e-mail" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="Mot de passe" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="flex items-center justify-between mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Se souvenir de moi') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="auth-link" href="{{ route('password.request') }}">
                        {{ __('Mot de passe oublié ?') }}
                    </a>
                @endif
            </div>

            <div class="mt-6">
                <x-button class="auth-submit w-full">
                    {{ __('Se connecter') }}
                </x-button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="auth-switch">
                Vous n’avez pas encore de compte ?
                <a class="auth-link" href="{{ route('register') }}">Créer un compte</a>
            </div>
        @endif
    </x-authentication-card>
</x-guest-layout>
