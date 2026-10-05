<section class="dp-assistant" data-customer-assistant aria-label="Assistant DjamonoPay">
    <div class="dp-assistant-panel" id="dp-assistant-panel" data-assistant-panel hidden>
        <header class="dp-assistant-header">
            <div>
                <p class="dp-assistant-eyebrow">Aide DjamonoPay</p>
                <h2>Comment puis-je vous aider ?</h2>
            </div>
            <button type="button" class="dp-assistant-close" data-assistant-close aria-label="Fermer l’assistant">×</button>
        </header>
        <div class="dp-assistant-messages" data-assistant-messages aria-live="polite">
            <p class="dp-assistant-message dp-assistant-message--bot">Bonjour ! Posez-moi une question sur les paiements, les frais, les retraits ou la sécurité de votre compte.</p>
        </div>
        <div class="dp-assistant-suggestions" aria-label="Questions fréquentes">
            <button type="button" data-assistant-question="Quels sont les frais ?">Frais</button>
            <button type="button" data-assistant-question="Comment envoyer de l’argent ?">Envoyer</button>
            <button type="button" data-assistant-question="Comment faire un retrait ?">Retrait</button>
        </div>
        <form class="dp-assistant-form" data-assistant-form>
            <label class="sr-only" for="assistant-question">Votre question</label>
            <input id="assistant-question" name="question" maxlength="240" autocomplete="off" placeholder="Écrivez votre question…" required>
            <button type="submit" aria-label="Envoyer la question"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
        </form>
    </div>
    <button type="button" class="dp-assistant-launcher" data-assistant-toggle aria-expanded="false" aria-controls="dp-assistant-panel">
        <i class="fa fa-comments" aria-hidden="true"></i>
        <span>Besoin d’aide ?</span>
    </button>
</section>
