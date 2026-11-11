<script>
    (function() {
        if (!('serviceWorker' in navigator)) {
            console.warn('⚠️ Service workers are not supported in this browser.');
            return;
        }

        window.addEventListener('load', async () => {
            try {
                const inDash = location.pathname === '/dashboard' || location.pathname.startsWith('/dashboard/');
                const hadController = !!navigator.serviceWorker.controller;
                let reloading = false;

                // When new SW takes control on an already-controlled dashboard view without an active POST form, reload once
                navigator.serviceWorker.addEventListener('controllerchange', () => {
                    if (!inDash || !hadController || reloading) return;
                    if (document.querySelector('form[method="POST"], form[method="post"]')) return;
                    reloading = true;
                    location.reload();
                });

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

                // Warmup: after ready (only within /dashboard/ scope so .ready does not hang on /login/)
                if (inDash) {
                    const ready = await navigator.serviceWorker.ready;
                    (navigator.serviceWorker.controller || ready.active)?.postMessage?.({
                        type: 'WARMUP',
                        payload: {
                            urls: [
                                '/dashboard/?view=overview',
                            ]
                        }
                    });
                }
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