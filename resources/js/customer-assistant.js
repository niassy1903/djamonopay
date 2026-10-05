const assistant = document.querySelector('[data-customer-assistant]');

if (assistant) {
    const panel = assistant.querySelector('[data-assistant-panel]');
    const toggle = assistant.querySelector('[data-assistant-toggle]');
    const close = assistant.querySelector('[data-assistant-close]');
    const form = assistant.querySelector('[data-assistant-form]');
    const input = form.querySelector('input');
    const messages = assistant.querySelector('[data-assistant-messages]');

    const answers = [
        {
            matches: ['frais', 'commission', 'cout'],
            answer: 'Les paiements et les demandes de retrait comportent des frais de 2 %. Le montant et les frais sont affichés avant la confirmation.',
        },
        {
            matches: ['envoyer', 'paiement', 'transfert', 'telephone', 'numero'],
            answer: 'Ouvrez « Envoyer de l’argent », saisissez le numéro de téléphone lié au compte du bénéficiaire et vérifiez son nom lorsqu’il s’affiche. Vous pouvez aussi utiliser son QR code.',
        },
        {
            matches: ['retrait', 'retirer', 'espece', 'distributeur'],
            answer: 'Choisissez « Retirer chez un distributeur », sélectionnez le distributeur et le montant. La somme est réservée ; le retrait est confirmé lorsque le distributeur remet les espèces.',
        },
        {
            matches: ['qr', 'code'],
            answer: 'Votre QR code de paiement est disponible dans votre espace portefeuille. Le bénéficiaire peut le scanner pour préparer un paiement.',
        },
        {
            matches: ['securite', 'mot de passe', 'compte'],
            answer: 'Ne partagez jamais votre mot de passe ni vos codes de vérification. Vérifiez toujours le nom du bénéficiaire et le montant avant de confirmer un paiement.',
        },
    ];

    function addMessage(text, sender) {
        const message = document.createElement('p');
        message.className = `dp-assistant-message dp-assistant-message--${sender}`;
        message.textContent = text;
        messages.append(message);
        messages.scrollTop = messages.scrollHeight;
    }

    function answerQuestion(question) {
        addMessage(question, 'user');
        const normalized = question
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLocaleLowerCase('fr-FR');
        const match = answers.find((entry) => entry.matches.some((word) => normalized.includes(word)));
        addMessage(
            match?.answer || 'Je ne trouve pas encore de réponse à cette question. Consultez votre historique d’opérations ou contactez votre agent depuis votre espace.',
            'bot',
        );
    }

    toggle.addEventListener('click', () => {
        panel.hidden = !panel.hidden;
        toggle.setAttribute('aria-expanded', String(!panel.hidden));
        if (!panel.hidden) input.focus();
    });

    close.addEventListener('click', () => {
        panel.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');
        toggle.focus();
    });

    assistant.querySelectorAll('[data-assistant-question]').forEach((button) => {
        button.addEventListener('click', () => answerQuestion(button.dataset.assistantQuestion));
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const question = input.value.trim();
        if (!question) return;
        answerQuestion(question);
        input.value = '';
    });
}
