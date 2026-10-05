
 <!-- latest jquery-->
 <script defer src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
 <!-- Bootstrap js-->
<script defer src="{{asset('assets/js/bootstrap/bootstrap.bundle.min.js')}}"></script>
<!-- feather icon js-->
<script defer src="{{asset('assets/js/icons/feather-icon/feather.min.js')}}"></script>
<script defer src="{{asset('assets/js/icons/feather-icon/feather-icon.js')}}"></script>
<!-- scrollbar js-->
<script defer src="{{asset('assets/js/scrollbar/simplebar.js')}}"></script>
<script defer src="{{asset('assets/js/scrollbar/custom.js')}}"></script>
<!-- Sidebar jquery-->
<script defer src="{{asset('assets/js/config.js')}}"></script>
<!-- Plugins JS start-->
<script defer id="menu" src="{{asset('assets/js/sidebar-menu.js')}}"></script>
<script defer src="{{ asset('assets/js/slick/slick.min.js') }}"></script>
<script defer src="{{ asset('assets/js/slick/slick.js') }}"></script>
<script defer src="{{ asset('assets/js/header-slick.js') }}"></script>
@yield('script')

@if(Route::current()->getName() != 'popover') 
	<script defer src="{{asset('assets/js/tooltip-init.js')}}"></script>
@endif

<!-- Plugins JS Ends-->
<!-- Theme js-->
<script defer src="{{asset('assets/js/script.js')}}"></script>
<!-- <script src="{{asset('assets/js/theme-customizer/customizer.js')}}"></script> -->

@if (auth()->check())
<script>
    (() => {
        const root = document.querySelector('[data-agent-notifications]');
        if (!root) return;

        const enableButton = root.querySelector('[data-notification-enable]');
        const toggleButton = root.querySelector('[data-notification-toggle]');
        const panel = root.querySelector('[data-notification-panel]');
        const list = root.querySelector('[data-notification-list]');
        const empty = root.querySelector('[data-notification-empty]');
        const count = root.querySelector('[data-notification-count]');
        const status = root.querySelector('[data-notification-status]');
        const endpoint = root.dataset.endpoint;
        const enabledKey = `djamonopay-notifications-enabled-${root.dataset.userId}`;
        let audioContext = null;
        let soundReady = false;
        let pollingStarted = false;
        let unreadCount = 0;

        const setStatus = (message) => { status.textContent = message; };

        function playSound() {
            if (!soundReady || !audioContext) return;
            const oscillator = audioContext.createOscillator();
            const gain = audioContext.createGain();
            oscillator.type = 'sine';
            oscillator.frequency.value = 880;
            gain.gain.setValueAtTime(0.0001, audioContext.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.12, audioContext.currentTime + 0.02);
            gain.gain.exponentialRampToValueAtTime(0.0001, audioContext.currentTime + 0.35);
            oscillator.connect(gain);
            gain.connect(audioContext.destination);
            oscillator.start();
            oscillator.stop(audioContext.currentTime + 0.36);
        }

        function addNotification(item) {
            const data = item.data || {};
            const row = document.createElement('li');
            const title = document.createElement('strong');
            const body = document.createElement('small');
            title.textContent = data.title || 'Nouvelle notification';
            body.textContent = data.body || '';
            row.append(title, body);
            list.prepend(row);
            empty.hidden = true;
            unreadCount += 1;
            count.textContent = unreadCount > 99 ? '99+' : String(unreadCount);
            count.hidden = false;

            if ('Notification' in window && Notification.permission === 'granted') {
                try {
                    const browserNotification = new Notification(data.title || 'DjamonoPay', {
                        body: data.body || '',
                        tag: item.id,
                    });
                    browserNotification.onclick = () => {
                        window.focus();
                        if (data.url) window.location.href = data.url;
                    };
                } catch (error) {
                    console.error('Impossible d’afficher la notification navigateur.', error);
                    setStatus('La notification navigateur n’a pas pu être affichée.');
                }
            }

            playSound();
        }

        async function poll() {
            try {
                const response = await fetch(endpoint, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                const payload = await response.json();
                payload.notifications.forEach(addNotification);
                setStatus('Alertes actives. Les nouvelles opérations apparaîtront ici.');
            } catch (error) {
                console.error('Échec de récupération des notifications agent.', error);
                setStatus('Connexion aux notifications interrompue ; nouvelle tentative en cours.');
            } finally {
                window.setTimeout(poll, 15000);
            }
        }

        function startPolling() {
            if (pollingStarted) return;
            pollingStarted = true;
            poll();
        }

        toggleButton.addEventListener('click', () => {
            panel.hidden = !panel.hidden;
            toggleButton.setAttribute('aria-expanded', String(!panel.hidden));
        });

        enableButton.addEventListener('click', async () => {
            if (!window.isSecureContext || !('Notification' in window)) {
                setStatus('Les notifications navigateur nécessitent un navigateur compatible et une connexion HTTPS.');
                return;
            }

            try {
                const AudioContextClass = window.AudioContext || window.webkitAudioContext;
                let resumeAudio = Promise.resolve(false);
                if (AudioContextClass) {
                    audioContext = audioContext || new AudioContextClass();
                    resumeAudio = audioContext.resume()
                        .then(() => audioContext.state === 'running')
                        .catch((error) => {
                            console.error('Impossible d’activer le son des alertes.', error);
                            return false;
                        });
                }

                if (Notification.permission !== 'granted') {
                    const permission = await Notification.requestPermission();
                    if (permission !== 'granted') {
                        setStatus('Autorisation refusée. Autorisez les notifications dans les réglages du navigateur.');
                        return;
                    }
                }

                soundReady = await resumeAudio;
                localStorage.setItem(enabledKey, 'true');
                enableButton.textContent = soundReady ? 'Son activé' : 'Alertes activées';
                setStatus(soundReady
                    ? 'Alertes navigateur et son activés.'
                    : 'Alertes navigateur activées ; le son n’est pas disponible sur cet appareil.');
                startPolling();
            } catch (error) {
                console.error('Impossible d’activer les alertes navigateur.', error);
                setStatus('Impossible d’activer les alertes. Vérifiez les réglages de votre navigateur.');
            }
        });

        if (!window.isSecureContext || !('Notification' in window)) {
            enableButton.disabled = true;
            enableButton.textContent = 'Alertes navigateur indisponibles';
            setStatus('Les alertes navigateur nécessitent un navigateur compatible et une connexion HTTPS. Les notifications dans l’application restent actives.');
        } else if (Notification.permission === 'granted' && localStorage.getItem(enabledKey) === 'true') {
            enableButton.textContent = 'Réactiver le son';
            setStatus('Notifications navigateur actives. Cliquez pour réactiver le son.');
        } else if (Notification.permission === 'denied') {
            setStatus('Les notifications dans l’application restent actives. Autorisez les alertes dans les réglages du navigateur.');
        }
        startPolling();
    })();
</script>
@endif

{{-- @if(Route::current()->getName() == 'index')
	<script src="{{asset('assets/js/layout-change.js')}}"></script>
@endif --}}
