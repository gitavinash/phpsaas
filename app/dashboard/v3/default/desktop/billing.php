<?php
/**
 * Billing Route Compatibility Handler
 * File: app/dashboard/v3/default/desktop/billing.php
 * Redirects legacy /dashboard/billing/ to V3 Accounts Ledger with verified status
 */

$params = [];
if (!empty($_SERVER['QUERY_STRING'])) {
    parse_str($_SERVER['QUERY_STRING'], $params);
}
if (!isset($params['status'])) {
    $params['status'] = 'verified';
}

$targetUrl = '/dashboard/accounts/?' . http_build_query($params);

if (!headers_sent()) {
    header("Location: " . $targetUrl, true, 301);
} else {
    echo '<script>window.location.replace(' . json_encode($targetUrl) . ');</script>';
    echo '<noscript><meta http-equiv="refresh" content="0;url=' . htmlspecialchars($targetUrl, ENT_QUOTES, 'UTF-8') . '"></noscript>';
}
exit();
