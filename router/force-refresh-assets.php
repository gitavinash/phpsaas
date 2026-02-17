<?php
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

$redirectPath = '/';
if (isset($_GET['redirect'])) {
    $candidate = trim((string) $_GET['redirect']);
    if ($candidate !== '' && strpos($candidate, '/') === 0 && strpos($candidate, '//') !== 0) {
        $redirectPath = $candidate;
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Refreshing assets...</title>
</head>

<body>
    <p>Refreshing cached assets, please wait...</p>
    <script>
        (async function() {
            const redirectPath = <?php echo json_encode($redirectPath); ?>;

            try {
                if ('serviceWorker' in navigator) {
                    const regs = await navigator.serviceWorker.getRegistrations();
                    await Promise.all(regs.map(function(reg) {
                        return reg.unregister();
                    }));
                }
            } catch (e) {}

            try {
                if ('caches' in window) {
                    const keys = await caches.keys();
                    await Promise.all(keys.map(function(key) {
                        return caches.delete(key);
                    }));
                }
            } catch (e) {}

            try {
                localStorage.removeItem('asset_refresh_hits_v1');
                sessionStorage.removeItem('visitorSwCacheCleaned');
            } catch (e) {}

            const targetUrl = new URL(redirectPath, window.location.origin);
            targetUrl.searchParams.set('rf', String(Date.now()));
            window.location.replace(targetUrl.toString());
        })();
    </script>
</body>

</html>