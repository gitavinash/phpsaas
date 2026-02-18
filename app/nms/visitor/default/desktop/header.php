<?php

require_once UTIL_FUNCTIONS;

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>all.min.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.css">

    <?php require_once DEFAULT_META; ?>

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