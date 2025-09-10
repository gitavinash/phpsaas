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

            // Update flow (prompt and activate immediately)
            reg.addEventListener('updatefound', () => {
                const nw = reg.installing;
                if (!nw) return;
                nw.addEventListener('statechange', () => {
                    if (nw.state === 'installed' && navigator.serviceWorker
                        .controller) {
                        const proceed = window.Swal ?
                            Swal.fire({
                                title: 'Update available',
                                text: 'Reload to update?',
                                icon: 'info',
                                showCancelButton: true,
                                confirmButtonText: 'Reload'
                            })
                            .then(r => r.isConfirmed) :
                            Promise.resolve(confirm(
                                'A new dashboard update is ready. Reload now?'));

                        Promise.resolve(proceed).then(ok => {
                            if (ok) reg.waiting?.postMessage({
                                type: 'SKIP_WAITING'
                            });
                        });
                    }
                });
            });

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