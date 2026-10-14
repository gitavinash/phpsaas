<?php

// Link Redirection Handler
if (!function_exists('shortToLongUrl')) {
    if (defined('UTIL_FUNCTIONS') && file_exists(UTIL_FUNCTIONS)) {
        require_once UTIL_FUNCTIONS;
    }
}

$shortCode = isset($urlStructure[0]) ? $urlStructure[0] : '';
$redirectTo = shortToLongUrl($connection, $shortCode);

if (!empty($redirectTo)) {
    $status = (int)($redirectTo['redirection_http_status'] ?? 301);
    $location = $redirectTo['redirection_to'];
    $escapedLocation = htmlspecialchars($location, ENT_QUOTES, 'UTF-8');
    $delayMs = 1000;
    $delaySeconds = $delayMs / 1000;

    header('Content-Type: text/html; charset=utf-8');
    http_response_code($status);
    header('Refresh: ' . $delaySeconds . '; url=' . $location);
?>
    <!doctype html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Redirecting…</title>
        <meta http-equiv="refresh" content="<?php echo $delaySeconds; ?>;url=<?php echo $escapedLocation; ?>">
        <meta name="redirect-data" data-redirect-url="<?php echo $escapedLocation; ?>" data-delay="<?php echo $delayMs; ?>">
        <link rel="stylesheet" href="/app/link_shortener/visitor/default/desktop/assets/css/redirect.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <noscript>
            <meta http-equiv="refresh" content="0;url=<?php echo $escapedLocation; ?>">
        </noscript>
    </head>

    <body>
        <div class="redirect-container" role="status" aria-live="polite">
            <div class="spinner-wrap">
                <div class="spinner" aria-hidden="true"></div>
                <i class="fa-solid fa-link spinner-icon" aria-hidden="true"></i>
            </div>
            <h1 class="redirect-title">Redirecting you now…</h1>
            <p class="redirect-subtitle">Please hold on while we safely transport you to the requested destination.</p>
            <div class="destination-chip">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                <span><?php echo $escapedLocation; ?></span>
            </div>
            <div class="fallback-box">
                If you are not redirected automatically, <a class="fallback-link" href="<?php echo $escapedLocation; ?>">click here</a>.
            </div>
            <div class="redirect-branding">
                <span class="branding-text">Powered by</span>
                <a href="https://blumox.com" target="_blank" rel="noopener noreferrer" class="branding-link">
                    <i class="fa-solid fa-bolt branding-icon"></i>
                    <span class="branding-name">Blumox Technologies</span>
                </a>
            </div>
        </div>
        <script src="/app/link_shortener/visitor/default/desktop/assets/js/redirect.js"></script>
    </body>

    </html>
<?php
} else {
    http_response_code(404);
?>
    <!doctype html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Link Not Found</title>
        <link rel="stylesheet" href="/app/link_shortener/visitor/default/desktop/assets/css/redirect.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    </head>

    <body>
        <div class="redirect-container">
            <div class="error-badge">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h1 class="redirect-title">Link Not Found</h1>
            <p class="redirect-subtitle">The short link you are trying to visit does not exist, has expired, or is invalid.</p>
            <div class="fallback-box">
                <a class="fallback-link" href="/">Go to Home</a>
            </div>
            <div class="redirect-branding">
                <span class="branding-text">Powered by</span>
                <a href="https://aleya.in" target="_blank" rel="noopener noreferrer" class="branding-link">
                    <i class="fa-solid fa-bolt branding-icon"></i>
                    <span class="branding-name">Aleya</span>
                </a>
                <span class="branding-sep">•</span>
                <span class="branding-sub">Blumox</span>
            </div>
        </div>
    </body>

    </html>
<?php
}
exit();
