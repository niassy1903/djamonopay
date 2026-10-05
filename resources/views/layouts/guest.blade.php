<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>DjamonoPay</title>
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <meta name="theme-color" content="#063f32">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
        <style>
            :root {
                --pay-blue: #00a878;
                --pay-ink: #101615;
                --pay-muted: #66766e;
            }

            * { box-sizing: border-box; }
            body {
                min-height: 100vh;
                margin: 0;
                color: var(--pay-ink);
                background: radial-gradient(ellipse at 8% 8%, rgba(0,168,120,.16), transparent 34%), radial-gradient(ellipse at 95% 92%, rgba(49,95,84,.15), transparent 32%), linear-gradient(135deg, #063f32 0%, #315f54 50%, #063f32 100%);
                font-family: Figtree, Inter, ui-sans-serif, system-ui, sans-serif;
            }
            .auth-shell {
                display: grid;
                grid-template-columns: minmax(300px, .9fr) minmax(460px, 1.1fr);
                width: min(1160px, calc(100% - 48px));
                min-height: min(760px, calc(100vh - 64px));
                margin: 32px auto;
                overflow: hidden;
                border: 1px solid rgba(248,250,248,.8);
                border-radius: 30px;
                background: rgba(248,250,248,.96);
                box-shadow: 0 32px 90px rgba(4,43,35,.28);
            }
            .auth-brand {
                position: relative;
                display: flex;
                min-height: 100%;
                flex-direction: column;
                justify-content: space-between;
                overflow: hidden;
                padding: 48px;
                color: white;
                background: linear-gradient(145deg, #063f32 0%, #315f54 55%, #00a878 100%);
            }
            .auth-brand::before, .auth-brand::after {
                position: absolute;
                width: 310px;
                height: 310px;
                border: 1px solid rgba(255,255,255,.2);
                border-radius: 50%;
                content: "";
            }
            .auth-brand::before { top: 18%; right: -125px; box-shadow: 0 0 0 32px rgba(255,255,255,.045), 0 0 0 65px rgba(255,255,255,.035); }
            .auth-brand::after { bottom: -180px; left: -215px; width: 390px; height: 390px; }
            .auth-brand-top, .auth-brand-copy, .auth-brand-foot { position: relative; z-index: 1; }
            .auth-brand-copy { max-width: 430px; margin: auto 0; padding: 56px 0; }
            .auth-brand-copy h2 { margin: 22px 0 16px; color: white; font-size: clamp(2.4rem, 4vw, 3.5rem); font-weight: 750; letter-spacing: -.055em; line-height: 1.08; }
            .auth-brand-copy p { max-width: 360px; color: rgba(255,255,255,.78); font-size: 1.05rem; line-height: 1.75; }
            .auth-brand-foot { color: rgba(255,255,255,.65); font-size: .82rem; }
            .auth-pill { display: inline-flex; align-items: center; gap: 9px; padding: 9px 14px; border: 1px solid rgba(255,255,255,.26); border-radius: 999px; background: rgba(255,255,255,.12); color: white; font-size: .78rem; font-weight: 650; letter-spacing: .06em; text-transform: uppercase; }
            .auth-pill-dot { width: 7px; height: 7px; border-radius: 50%; background: #a8f2d0; box-shadow: 0 0 12px #a8f2d0; }
            .auth-panel { display: flex; align-items: center; justify-content: center; padding: 44px clamp(28px, 6vw, 76px); }
            .auth-content { width: 100%; max-width: 470px; }
            .auth-logo { display: inline-flex; align-items: center; gap: 11px; margin-bottom: 36px; color: var(--pay-ink); font-size: 1.25rem; font-weight: 750; letter-spacing: -.04em; text-decoration: none; }
            .auth-logo-mark { display: grid; width: 42px; height: 42px; place-items: center; border-radius: 14px; background: linear-gradient(145deg, #063f32, #00a878); box-shadow: 0 9px 20px rgba(6,63,50,.25); color: #f8faf8; font-size: 1.2rem; }
            .auth-eyebrow { margin-bottom: 8px; color: var(--pay-blue); font-size: .77rem; font-weight: 750; letter-spacing: .12em; text-transform: uppercase; }
            .auth-title { margin: 0; color: var(--pay-ink); font-size: clamp(1.85rem, 3vw, 2.35rem); font-weight: 750; letter-spacing: -.055em; line-height: 1.18; }
            .auth-subtitle { margin: 10px 0 27px; color: var(--pay-muted); font-size: .96rem; line-height: 1.65; }
            .auth-form label { display: block; margin-bottom: 7px; color: #315f54; font-size: .84rem; font-weight: 650; }
            .auth-form input:not([type=checkbox]) { width: 100%; min-height: 48px; border: 1px solid #d9e2dd; border-radius: 12px; background: #f8faf8; padding: 11px 14px; color: var(--pay-ink); outline: none; box-shadow: none; transition: border-color .2s, box-shadow .2s, background .2s; }
            .auth-form input:not([type=checkbox]):focus { border-color: #00a878; background: #f8faf8; box-shadow: 0 0 0 4px rgba(0,168,120,.16); }
            .auth-form input[type=checkbox] { accent-color: var(--pay-blue); }
            .auth-form .mt-4 { margin-top: 17px; }
            .auth-form .auth-grid { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 15px 14px; }
            .auth-form .auth-grid .mt-4 { margin-top: 0; }
            .auth-form .auth-submit { display: inline-flex; min-height: 48px; align-items: center; justify-content: center; border: 0; border-radius: 12px; background: #ffc83d; padding: 0 22px; color: #063f32; font-size: .9rem; font-weight: 700; text-transform: none; box-shadow: 0 10px 22px rgba(6,63,50,.2); transition: transform .2s, box-shadow .2s; }
            .auth-form .auth-submit:hover { transform: translateY(-1px); background: #ffd65c; box-shadow: 0 14px 26px rgba(6,63,50,.26); }
            .auth-form .auth-link { color: var(--pay-blue); font-size: .84rem; font-weight: 650; text-decoration: none; }
            .auth-form .auth-link:hover { text-decoration: underline; }
            .auth-switch { margin-top: 24px; padding-top: 20px; border-top: 1px solid #d9e2dd; color: var(--pay-muted); font-size: .88rem; text-align: center; }
            .auth-alert { margin-bottom: 18px; border: 1px solid #fecaca; border-radius: 12px; background: #fff7f7; padding: 12px 14px; color: #b42318; font-size: .87rem; }
            @media (max-width: 820px) {
                .auth-shell { grid-template-columns: 1fr; width: min(560px, calc(100% - 28px)); min-height: 0; margin: 14px auto; }
                .auth-brand { display: none; }
                .auth-panel { padding: 34px 28px; }
                .auth-logo { margin-bottom: 27px; }
            }
            @media (max-width: 480px) {
                .auth-shell { border-radius: 22px; }
                .auth-panel { padding: 28px 20px; }
                .auth-form .auth-grid { grid-template-columns: 1fr; }
            }
        </style>
    </head>
    <body>
        <div class="font-sans text-gray-900 antialiased">
            {{ $slot }}
        </div>

        @livewireScripts
    </body>
</html>
