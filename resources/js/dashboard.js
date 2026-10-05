import QRCode from 'qrcode';
import './recipient-lookup';
import './customer-assistant';

document.querySelectorAll('[data-payment-qr]').forEach((canvas) => {
    const error = canvas.closest('.dp-dashboard')?.querySelector('[data-qr-error]');

    QRCode.toCanvas(canvas, canvas.dataset.paymentQr, {
        width: 208,
        margin: 1,
        errorCorrectionLevel: 'H',
        color: {
            dark: '#063f32',
            light: '#f8faf8',
        },
    }).catch((cause) => {
        if (error) {
            error.textContent = 'Impossible de générer le QR code. Rechargez la page.';
        }
        console.error('Échec de génération du QR de paiement.', cause);
    });
});

document.querySelectorAll('[data-download-qr]').forEach((button) => {
    button.addEventListener('click', () => {
        const canvas = button.closest('.dp-dashboard')?.querySelector('[data-payment-qr]');
        if (!(canvas instanceof HTMLCanvasElement) || !canvas.width) {
            const error = button.closest('.dp-dashboard')?.querySelector('[data-qr-error]');
            if (error) {
                error.textContent = 'Le QR code n’est pas encore prêt.';
            }
            return;
        }

        const link = document.createElement('a');
        try {
            link.download = 'djamonopay-qr.png';
            link.href = canvas.toDataURL('image/png');
            document.body.append(link);
            link.click();
            window.setTimeout(() => link.remove(), 1000);
        } catch (cause) {
            link.remove();
            const error = button.closest('.dp-dashboard')?.querySelector('[data-qr-error]');
            if (error) {
                error.textContent = 'Le QR code n’a pas pu être téléchargé.';
            }
            console.error('Échec du téléchargement du QR de paiement.', cause);
        }
    });
});

document.querySelectorAll('[data-copy-account]').forEach((button) => {
    button.addEventListener('click', async () => {
        const feedback = button.closest('.dp-dashboard')?.querySelector('[data-copy-feedback]');
        try {
            await navigator.clipboard.writeText(button.dataset.copyAccount);
            if (feedback) {
                feedback.textContent = 'Numéro de compte copié.';
            }
        } catch (cause) {
            if (feedback) {
                feedback.textContent = 'Copie impossible. Sélectionnez le numéro manuellement.';
            }
            console.error('Échec de copie du numéro de compte.', cause);
        }
    });
});

const paymentAmount = document.querySelector('#amount');
if (paymentAmount) {
    const formatXof = (amount) => new Intl.NumberFormat('fr-FR').format(amount);
    const updatePaymentPreview = () => {
        const amount = Math.max(0, Number.parseInt(paymentAmount.value, 10) || 0);
        const fee = Math.round(amount * 0.02);
        document.querySelector('[data-payment-amount]').textContent = formatXof(amount);
        document.querySelector('[data-payment-fee]').textContent = formatXof(fee);
        document.querySelector('[data-payment-received]').textContent = formatXof(amount - fee);
    };

    paymentAmount.addEventListener('input', updatePaymentPreview);
    updatePaymentPreview();
}

document.querySelectorAll('[data-toggle-balance]').forEach((button) => {
    button.addEventListener('click', () => {
        const balance = button.closest('.dp-dashboard')?.querySelector('[data-balance-value]');
        if (!balance) {
            return;
        }

        const isHidden = button.getAttribute('aria-pressed') === 'true';
        balance.textContent = isHidden ? balance.dataset.visible : '••••••';
        button.textContent = isHidden ? 'Masquer le solde disponible' : 'Afficher le solde disponible';
        button.setAttribute('aria-pressed', String(!isHidden));
    });
});

const userModals = document.querySelectorAll('[data-user-modal]');
userModals.forEach((modal) => {
    const openModal = () => {
        if (modal instanceof HTMLDialogElement && !modal.open) {
            modal.showModal();
        }
    };

    modal.querySelectorAll('[data-close-user-modal]').forEach((button) => {
        button.addEventListener('click', () => modal.close());
    });

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.close();
        }
    });

    if (modal.dataset.openOnLoad === 'true') {
        if (modal.id === 'edit-user-modal') {
            const editTrigger = [...document.querySelectorAll('[data-edit-user]')]
                .find((button) => button.dataset.userId === modal.dataset.userId);
            const form = modal.querySelector('[data-edit-user-form]');
            if (editTrigger && form instanceof HTMLFormElement) {
                form.action = editTrigger.dataset.updateUrl;
            }
        }
        openModal();
    }
});

document.querySelectorAll('[data-open-user-modal]').forEach((button) => {
    button.addEventListener('click', () => {
        document.getElementById(button.dataset.openUserModal)?.showModal();
    });
});

document.querySelectorAll('[data-edit-user]').forEach((button) => {
    button.addEventListener('click', () => {
        const modal = document.getElementById('edit-user-modal');
        const form = modal?.querySelector('[data-edit-user-form]');
        if (!(form instanceof HTMLFormElement) || !modal) {
            return;
        }

        form.action = button.dataset.updateUrl;
        form.elements.namedItem('user_id').value = button.dataset.userId;
        for (const [field, value] of Object.entries({
            prenom: button.dataset.firstName,
            nom: button.dataset.lastName,
            email: button.dataset.email,
            role: button.dataset.role,
            telephone: button.dataset.phone,
            adresse: button.dataset.address,
            date_naissance: button.dataset.birthDate,
            numero_identite: button.dataset.identity,
            etat_compte: button.dataset.status,
        })) {
            form.elements.namedItem(field).value = value;
        }
        form.elements.namedItem('mot_de_passe').value = '';
        modal.showModal();
    });
});

document.querySelectorAll('[data-delete-user-form]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm('Supprimer cet utilisateur ? La suppression sera refusée si son compte possède un solde ou un historique financier.')) {
            event.preventDefault();
        }
    });
});

let apexChartsPromise;

function loadApexCharts(src) {
    if (window.ApexCharts) {
        return Promise.resolve();
    }

    if (!apexChartsPromise) {
        apexChartsPromise = new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = src;
            script.onload = () => {
                if (window.ApexCharts) {
                    resolve();
                    return;
                }

                reject(new Error('ApexCharts chargé sans exposer son constructeur.'));
            };
            script.onerror = () => reject(new Error(`Impossible de charger ApexCharts depuis ${src}.`));
            document.head.append(script);
        });
    }

    return apexChartsPromise;
}

function renderDashboardChart(element) {
    let data;
    try {
        data = JSON.parse(element.dataset.chart);
    } catch (cause) {
        element.textContent = 'Les données du graphique sont illisibles.';
        console.error('Données de graphique invalides.', cause);
        return;
    }

    if (!Array.isArray(data.series) || !Array.isArray(data.labels)) {
        element.textContent = 'Les données du graphique sont illisibles.';
        console.error('Format de données de graphique invalide.');
        return;
    }

    const hasMixedSeries = data.series.some((series) => series.type);
    const chart = new window.ApexCharts(element, {
        chart: {
            type: hasMixedSeries ? 'line' : 'area',
            height: 300,
            toolbar: { show: false },
            fontFamily: 'Rubik, sans-serif',
            foreColor: '#66766e',
        },
        series: data.series,
        colors: ['#00a878', '#ffc83d'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: hasMixedSeries ? [0, 3] : 3 },
        fill: {
            type: hasMixedSeries ? 'solid' : 'gradient',
            opacity: hasMixedSeries ? 0.9 : [0.22, 0.12],
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.28,
                opacityTo: 0.03,
                stops: [0, 90, 100],
            },
        },
        plotOptions: { bar: { borderRadius: 5, columnWidth: '42%' } },
        xaxis: { categories: data.labels, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis: hasMixedSeries
            ? [
                { title: { text: 'Transactions' }, min: 0 },
                { opposite: true, title: { text: 'Volume XOF' }, min: 0, labels: { formatter: (value) => new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(value) } },
            ]
            : { min: 0, labels: { formatter: (value) => new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(value) } },
        grid: { borderColor: '#d9e2dd', strokeDashArray: 4, padding: { left: 8, right: 8 } },
        legend: { position: 'top', horizontalAlign: 'right', markers: { radius: 8 } },
        tooltip: {
            theme: 'light',
            y: {
                formatter: (value, { seriesIndex }) => {
                    const formatted = new Intl.NumberFormat('fr-FR').format(value);
                    return hasMixedSeries && seriesIndex === 0 ? formatted : `${formatted} XOF`;
                },
            },
        },
        noData: { text: 'Aucune transaction sur cette période' },
    });

    chart.render().catch((cause) => {
        element.textContent = 'Le graphique n’a pas pu être affiché.';
        console.error('Échec de rendu du graphique.', cause);
    });
}

document.querySelectorAll('[data-dashboard-chart]').forEach((element) => {
    let started = false;
    const start = () => {
        if (started) return;
        started = true;
        loadApexCharts(element.dataset.chartLibrarySrc)
            .then(() => renderDashboardChart(element))
            .catch((cause) => {
                element.textContent = 'Le graphique est indisponible. Rechargez la page.';
                console.error('Échec de chargement du graphique.', cause);
            });
    };

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            if (entries.some((entry) => entry.isIntersecting)) {
                observer.disconnect();
                start();
            }
        }, { rootMargin: '250px' });
        observer.observe(element);
    } else {
        start();
    }
});
