<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'DjamonoPay') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <header class="border-b border-gray-200 bg-white">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                    <a href="{{ route('dashboard') }}" class="font-semibold text-gray-900">{{ config('app.name', 'DjamonoPay') }}</a>
                    @auth
                        <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                    @endauth
                </div>
            </header>
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">{{ $header }}</div>
                </header>
            @endif
            <main>{{ $slot }}</main>
        </div>
        @stack('modals')
        @livewireScripts
    </body>
</html>
