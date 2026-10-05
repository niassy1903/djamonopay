document.querySelectorAll('[data-recipient-lookup]').forEach((input) => {
    const form = input.closest('form');
    const card = form?.querySelector('[data-recipient-card]');
    const status = form?.querySelector('[data-recipient-lookup-status]');
    const name = card?.querySelector('[data-recipient-name]');
    const role = card?.querySelector('[data-recipient-role]');
    const initials = card?.querySelector('[data-recipient-initials]');

    if (!form || !card || !status || !name || !role || !initials || input.readOnly) {
        return;
    }

    let timer;
    let request;

    input.addEventListener('input', () => {
        window.clearTimeout(timer);
        request?.abort();
        card.hidden = true;

        const value = input.value.trim();
        const digits = value.replace(/\D/g, '');
        if (digits.length < 8 || !/^[+\d\s().-]+$/.test(value)) {
            status.textContent = 'Saisissez le numéro de téléphone associé au compte.';
            return;
        }

        status.textContent = 'Recherche du compte…';
        timer = window.setTimeout(async () => {
            request = new AbortController();
            const url = new URL(input.dataset.lookupUrl, window.location.origin);
            url.searchParams.set('phone', value);
            url.searchParams.set('operation', input.dataset.operation);

            try {
                const response = await fetch(url, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                    signal: request.signal,
                });
                const payload = await response.json();

                if (!response.ok) {
                    status.textContent = payload.message || 'Aucun compte actif correspondant à ce numéro.';
                    return;
                }

                name.textContent = payload.name;
                role.textContent = `${payload.role} · Bénéficiaire vérifié`;
                initials.textContent = payload.name
                    .split(/\s+/)
                    .slice(0, 2)
                    .map((part) => part.charAt(0).toLocaleUpperCase('fr-FR'))
                    .join('');
                card.hidden = false;
                status.textContent = 'Compte vérifié. Vérifiez le nom avant de confirmer.';
            } catch (error) {
                if (error.name === 'AbortError') return;
                console.error('Échec de recherche du bénéficiaire par téléphone.', error);
                status.textContent = error.message || 'Recherche impossible. Réessayez.';
            }
        }, 350);
    });
});
