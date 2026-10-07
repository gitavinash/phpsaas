<script>
    (function() {
        if (!('serviceWorker' in navigator)) {
            console.warn('⚠️ Service workers are not supported in this browser.');
            return;
        }

        window.addEventListener('load', async () => {
            try {
                const reg = await navigator.serviceWorker.register('/dashboard/sw.js', {
                    scope: '/dashboard/'
                });
                console.log('✅ SW registered:', reg);

                // Faster first paints for navigations
                if ('navigationPreload' in reg) {
                    try {
                        await reg.navigationPreload.enable();
                    } catch {}
                }

                // When new SW takes control, reload once
                navigator.serviceWorker.addEventListener('controllerchange', () => location.reload());

                // Warmup: after ready (guarantees active worker)
                const ready = await navigator.serviceWorker.ready;
                (navigator.serviceWorker.controller || ready.active)?.postMessage?.({
                    type: 'WARMUP',
                    payload: {
                        urls: [
                            '/dashboard/?view=overview',
                        ]
                    }
                });
            } catch (err) {
                console.error('❌ SW registration failed:', err);
            }
        });
    })();

    (function() {
        const isiOSStandalone = window.navigator.standalone === true;
        if (!isiOSStandalone) return;

        document.addEventListener('click', function(e) {
            const a = e.target.closest('a[href]');
            if (!a) return;

            const url = new URL(a.getAttribute('href'), location.href);
            const sameOrigin = url.origin === location.origin;

            // Avoid target=_blank and external opens when installed on iOS
            if (sameOrigin) {
                e.preventDefault();
                location.assign(url.href);
            }
        }, {
            capture: true
        });
    })();
</script>