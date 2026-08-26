<?php

/**
 * V3 Authentication API - App-Specific Onboarding & Workspace Provisioning
 * Path: /api/v3/auth/onboarding.php
 */

header('Content-Type: application/json; charset=utf-8');

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Method not allowed. Use POST.'
    ]);
    exit;
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$rawInput = file_get_contents('php://input');
$jsonData = json_decode($rawInput, true) ?? [];
$data = !empty($_POST) ? $_POST : $jsonData;

$setupEmail   = strtolower(trim($data['setup_email'] ?? ($data['email'] ?? '')));
$businessName = trim($data['business_name'] ?? ($data['site_name'] ?? ($data['store_name'] ?? '')));
$subdomain    = strtolower(trim($data['subdomain'] ?? ($data['domain'] ?? '')));
$appType      = strtolower(trim($data['app_type'] ?? ($data['app'] ?? 'ecom')));
$themeColor   = trim($data['theme_color'] ?? '#4f46e5');
$category     = trim($data['category'] ?? '');
$currency     = trim($data['currency'] ?? 'INR');
$loadSamples  = !empty($data['load_samples']) && ($data['load_samples'] === '1' || $data['load_samples'] === 'true' || $data['load_samples'] === true);

if (empty($businessName)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Please enter your business or brand name.']);
    exit;
}

// Clean subdomain
$subdomain = preg_replace('/[^a-zA-Z0-9-]/', '', $subdomain);
if (empty($subdomain)) {
    $subdomain = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $businessName));
}

$mainDomain = defined('MAIN_DOMAIN') ? MAIN_DOMAIN : 'isw.one';
$fullDomain = $subdomain . '.' . $mainDomain;

$appIdMap = [
    'ecom'       => 7,
    'ecommerce'  => 7,
    'aleya'      => 20,
    'ecom_gift'  => 9,
    'e_com_gift' => 9,
    'gift'       => 9
];
$resolvedAppId = $appIdMap[$appType] ?? 7;

// Find Site Record by user email or session site_id
$siteId = 0;
$userId = 0;

if (!empty($setupEmail)) {
    $escSetupEmail = mysqli_real_escape_string($connection, $setupEmail);
    $userSiteQuery = mysqli_query($connection, "
        SELECT u.user_id, s.site_id 
        FROM {$tblPrefix}user u
        INNER JOIN {$tblPrefix}site s ON s.site_owner_id = u.user_id
        WHERE u.user_email = '{$escSetupEmail}'
        ORDER BY s.site_id DESC
        LIMIT 1
    ");
    if ($userSiteQuery && mysqli_num_rows($userSiteQuery) > 0) {
        $row = mysqli_fetch_assoc($userSiteQuery);
        $siteId = (int)$row['site_id'];
        $userId = (int)$row['user_id'];
    }
}

if ($siteId <= 0 && isset($_SESSION['site_id']) && (int)$_SESSION['site_id'] > 0) {
    $siteId = (int)$_SESSION['site_id'];
}

$escBizName = mysqli_real_escape_string($connection, $businessName);
$escFullDomain = mysqli_real_escape_string($connection, $fullDomain);
$escThemeColor = mysqli_real_escape_string($connection, $themeColor);

if ($siteId > 0) {
    // Update existing site
    mysqli_query($connection, "
        UPDATE {$tblPrefix}site 
        SET 
            site_name = '{$escBizName}',
            site_domain = '{$escFullDomain}',
            site_url = 'https://{$escFullDomain}',
            site_app_id = {$resolvedAppId},
            site_dashboard_version = 3,
            site_status = 1
        WHERE site_id = {$siteId}
    ");

    // Update site setting
    $checkSetting = mysqli_query($connection, "SELECT site_setting_id FROM {$tblPrefix}site_setting WHERE site_setting_site_id = {$siteId} LIMIT 1");
    if ($checkSetting && mysqli_num_rows($checkSetting) > 0) {
        mysqli_query($connection, "
            UPDATE {$tblPrefix}site_setting 
            SET site_theme_color = '{$escThemeColor}' 
            WHERE site_setting_site_id = {$siteId}
        ");
    } else {
        mysqli_query($connection, "
            INSERT INTO {$tblPrefix}site_setting 
                (site_setting_site_id, site_pagination_limit, site_sitemap_limit, site_feed_limit, site_image_quality, site_theme_color)
            VALUES 
                ({$siteId}, 10, 10, 10, 85, '{$escThemeColor}')
        ");
    }
}

// Update cookies
$cookieExpiry = time() + (30 * 24 * 60 * 60);
$cookieDomain = '.' . ltrim($mainDomain, '.');
if (filter_var($mainDomain, FILTER_VALIDATE_IP) || strpos($mainDomain, 'localhost') !== false) {
    $cookieDomain = '';
}
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');

@setcookie('site_app', base64_encode($appType), $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('isLogin', '1', $cookieExpiry, '/', $cookieDomain, $isHttps, false);

if (session_status() === PHP_SESSION_ACTIVE) {
    $_SESSION['site_id'] = $siteId;
    $_SESSION['site_app'] = $appType;
    $_SESSION['site_name'] = $businessName;
}

echo json_encode([
    'status'       => 'success',
    'msg'          => 'Workspace initialized successfully! Welcome to your dashboard.',
    'url'          => '/dashboard/',
    'redirect_url' => '/dashboard/',
    'workspace'    => [
        'name'   => $businessName,
        'domain' => $fullDomain,
        'app'    => $appType,
        'color'  => $themeColor
    ]
]);
exit;
