<?php

require_once UTIL_FUNCTIONS;

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>bootstrap.min.css?<?php echo assetQuery('bootstrap.min.css', ROOT . CSS_LIBRARY); ?>">
    <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>all.min.css?<?php echo assetQuery('all.min.css', ROOT . CSS_LIBRARY); ?>">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.css?<?php echo assetQuery('css/style.css'); ?>">

    <?php require_once DEFAULT_META; ?>

    <script>
        (function() {
            try {
                const key = 'asset_refresh_hits_v1';
                const now = Date.now();
                const oneMinute = 60 * 1000;
                const url = new URL(window.location.href);
                const currentToken = Number(url.searchParams.get('rf') || 0);

                let hits = JSON.parse(localStorage.getItem(key) || '[]');
                if (!Array.isArray(hits)) {
                    hits = [];
                }

                hits = hits.filter(function(ts) {
                    return (now - Number(ts)) <= oneMinute;
                });
                hits.push(now);
                localStorage.setItem(key, JSON.stringify(hits.slice(-10)));

                if (hits.length >= 2 && (currentToken === 0 || (now - currentToken) > oneMinute)) {
                    url.searchParams.set('rf', String(now));
                    window.location.replace(url.toString());
                }
            } catch (e) {}
        })();
    </script>
    <script>
        (function() {
            if (!('serviceWorker' in navigator)) {
                return;
            }

            const swVersion = '<?php echo assetVersion('service-worker.php', ROOT . '/router'); ?>';

            window.addEventListener('load', async function() {
                let pageNeedsReload = false;

                try {
                    const regs = await navigator.serviceWorker.getRegistrations();
                    for (const reg of regs) {
                        const swUrl = (reg.active && reg.active.scriptURL) ||
                            (reg.waiting && reg.waiting.scriptURL) ||
                            (reg.installing && reg.installing.scriptURL) || '';

                        if (swUrl.includes('/sw.js') && !swUrl.includes('/dashboard/sw.js')) {
                            const removed = await reg.unregister();
                            if (removed) {
                                pageNeedsReload = true;
                            }
                        }
                    }
                } catch (e) {}

                try {
                    if ('caches' in window) {
                        const cacheKeys = await caches.keys();
                        const visitorCacheKeys = cacheKeys.filter(function(key) {
                            return key.indexOf('-cache-') !== -1;
                        });

                        if (visitorCacheKeys.length) {
                            await Promise.all(visitorCacheKeys.map(function(key) {
                                return caches.delete(key);
                            }));
                            pageNeedsReload = true;
                        }
                    }
                } catch (e) {}

                if (pageNeedsReload && !sessionStorage.getItem('visitorSwCacheCleaned')) {
                    sessionStorage.setItem('visitorSwCacheCleaned', '1');
                    window.location.reload();
                    return;
                }

                try {
                    await navigator.serviceWorker.register('/sw.js?v=' + swVersion);
                } catch (e) {}
            });
        })();
    </script>

</head>

<body>
    <header>

    </header>
    <!-- header end -->