<div class="auth-shell">
    <section class="auth-brand">
        <div class="auth-brand-top">
            <a href="/" class="auth-logo" style="color:white;margin:0">
                <span class="auth-logo-mark" style="background:rgba(255,255,255,.2);box-shadow:none">D</span>
                <span style="color:white">DjamonoPay</span>
            </a>
        </div>
        <div class="auth-brand-copy">
            <span class="auth-pill"><span class="auth-pill-dot"></span>Paiements simples, vie sereine</span>
            <h2>Votre argent avance avec vous.</h2>
            <p>Une expérience de paiement fluide et sécurisée, pensée pour vous accompagner au quotidien.</p>
        </div>
        <div class="auth-brand-foot">© {{ now()->year }} DjamonoPay · Une finance plus proche de vous</div>
    </section>
    <section class="auth-panel">
        <div class="auth-content">
            <a href="/" class="auth-logo">
                <span class="auth-logo-mark">D</span>
                <span>DjamonoPay</span>
            </a>
            {{ $slot }}
        </div>
    </section>
</div>
