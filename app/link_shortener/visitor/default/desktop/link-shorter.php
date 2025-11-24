<?php

// Check link info db

require_once UTIL_FUNCTIONS;

$redirectTo = shortToLongUrl($connection, $urlStructure[0]);

if (!empty($redirectTo)) {
    $status = (int)$redirectTo['redirection_http_status'];
    $location = $redirectTo['redirection_to'];
    $escapedLocation = htmlspecialchars($location, ENT_QUOTES, 'UTF-8');
    $jsLocation = json_encode($location);

    // Return an HTML page with a loading animation and a JS + meta-refresh redirect.
    // We avoid sending an immediate Location header so users can see the animation,
    // but we still set the HTTP response status so browsers/tools know this is a redirect.
    header('Content-Type: text/html; charset=utf-8');
    http_response_code($status);
    // Also provide a server-side Refresh header as another fallback some clients honor.
    $delayMs = 1200; // how long to show the loader before triggering redirect (ms)
    $delaySeconds = $delayMs / 1000;
    header('Refresh: ' . $delaySeconds . '; url=' . $location);

    echo <<<HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Redirecting…</title>
    <meta http-equiv="refresh" content="{$delaySeconds};url={$escapedLocation}">
    <style>
        body{margin:0;font-family: system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial;color:#222;background:#fff;display:flex;align-items:center;justify-content:center;height:100vh}
        .container{display:flex;flex-direction:column;align-items:center;gap:1rem}
        .spinner{width:64px;height:64px;border:8px solid rgba(0,0,0,0.08);border-top-color:#0078d4;border-radius:50%;animation:spin 1s linear infinite}
        @keyframes spin{to{transform:rotate(360deg)}}
        .message{font-size:1rem}
        .link{color:#0078d4;text-decoration:none}
        .small{font-size:.85rem;color:#666}
    </style>
    <noscript>
        <meta http-equiv="refresh" content="0;url={$escapedLocation}">
    </noscript>
</head>
<body>
    <div class="container" role="status" aria-live="polite">
        <div class="spinner" aria-hidden="true"></div>
        <div class="message">Redirecting you—please wait…</div>
        <div class="small">If nothing happens, <a class="link" href="{$escapedLocation}">click here</a>.</div>
    </div>
    <script>
    (function(){
        var delay = {$delayMs};
        var target = {$jsLocation};
        setTimeout(function(){
            try { window.location.replace(target); }
            catch(e) { window.location.href = target; }
        }, delay);
    })();
    </script>
</body>
</html>
HTML;
} else {
    echo "Invalid Short URL";
}
exit();