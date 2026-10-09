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
header("Location: " . $targetUrl, true, 301);
exit();
