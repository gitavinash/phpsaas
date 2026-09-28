<?php

/**
 * V3 Authentication API - App-Tailored Passwordless Registration
 * Path: /api/v3/auth/register.php
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
$siteId = defined('SITE_ID') && (int)SITE_ID > 0 ? (int)SITE_ID : (isset($_SESSION['site_id']) ? (int)$_SESSION['site_id'] : 0);

if ($siteId <= 0) {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $host = preg_replace('/:\d+$/', '', $host);
    $escapedHost = mysqli_real_escape_string($connection, $host);
    $siteQuery = mysqli_query($connection, "SELECT site_id FROM {$tblPrefix}site WHERE site_domain = '{$escapedHost}' OR site_url LIKE '%{$escapedHost}%' LIMIT 1");
    if ($siteQuery && mysqli_num_rows($siteQuery) > 0) {
        $row = mysqli_fetch_assoc($siteQuery);
        $siteId = (int)$row['site_id'];
    }
}

if ($siteId <= 0) {
    $siteId = 1;
}

if (!defined('SITE_ID')) {
    define('SITE_ID', $siteId);
}

$rawInput = file_get_contents('php://input');
$jsonData = json_decode($rawInput, true) ?? [];
$data = !empty($_POST) ? $_POST : $jsonData;

$firstName    = trim($data['first_name'] ?? '');
$lastName     = trim($data['last_name'] ?? '');
$email        = strtolower(trim($data['email'] ?? ''));
$mobile       = preg_replace('/\D/', '', (string)($data['mobile'] ?? ''));
$appName      = trim($data['app_type'] ?? ($data['app'] ?? (defined('SITE_APP') ? SITE_APP : 'ecom')));
$businessName = trim($data['business_name'] ?? ($data['store_name'] ?? ($data['salon_name'] ?? '')));
$subdomain    = strtolower(trim($data['subdomain'] ?? ($data['domain'] ?? '')));
$userType     = strtolower(trim($data['user_type'] ?? ($data['userType'] ?? 'administrator')));
$redirectUrl  = trim($data['redirect_url'] ?? '');

// Map App Type to App ID
$appIdMap = [
    'ecom'       => 7,
    'ecommerce'  => 7,
    'aleya'      => 20,
    'ecom_gift'  => 9,
    'e_com_gift' => 9,
    'gift'       => 9
];
$resolvedAppId = $appIdMap[strtolower($appName)] ?? 7;

// Validations
if (empty($firstName)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Please enter your first name.']);
    exit;
}

if (empty($lastName)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Please enter your last name.']);
    exit;
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Please enter a valid email address.']);
    exit;
}

if (!empty($mobile) && (strlen($mobile) < 10 || strlen($mobile) > 15)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Please enter a valid 10-digit phone number.']);
    exit;
}

$escEmail = mysqli_real_escape_string($connection, $email);
$escMobile = mysqli_real_escape_string($connection, $mobile);

// Check if email already registered
$checkEmailQuery = mysqli_query($connection, "
    SELECT u.user_id, u.user_salt_key, sa.site_access_id 
    FROM {$tblPrefix}user u
    LEFT JOIN {$tblPrefix}site_access sa ON sa.site_access_user_id = u.user_id AND sa.site_access_site_id = {$siteId}
    WHERE u.user_email = '{$escEmail}'
    LIMIT 1
");

if ($checkEmailQuery && mysqli_num_rows($checkEmailQuery) > 0) {
    $existing = mysqli_fetch_assoc($checkEmailQuery);
    if (!empty($existing['site_access_id'])) {
        http_response_code(409);
        echo json_encode([
            'status' => 'warning',
            'msg'    => 'An account with this email address already exists. Please sign in.'
        ]);
        exit;
    } else {
        $userId = (int)$existing['user_id'];
        $userSalt = (string)$existing['user_salt_key'];
        mysqli_query($connection, "
            INSERT INTO {$tblPrefix}site_access 
                (site_access_site_id, site_access_user_id, site_access_type, site_access_status, site_access_meta) 
            VALUES 
                ({$siteId}, {$userId}, '{$userType}', 1, '')
        ");
        $displayName = trim($firstName . ' ' . $lastName);
    }
} else {
    // Check if mobile already registered
    if (!empty($mobile)) {
        $checkMobileQuery = mysqli_query($connection, "SELECT user_id FROM {$tblPrefix}user WHERE user_mobile = '{$escMobile}' LIMIT 1");
        if ($checkMobileQuery && mysqli_num_rows($checkMobileQuery) > 0) {
            http_response_code(409);
            echo json_encode([
                'status' => 'warning',
                'msg'    => 'An account with this mobile number already exists. Please sign in.'
            ]);
            exit;
        }
    }
    // Generate salt key for passwordless user
    $userSalt = hash('sha256', uniqid(mt_rand(), true) . $email . time());
    $autoPassword = hash('sha256', $userSalt . uniqid('pass_', true));

    $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $firstName . $lastName));
    if (empty($baseUsername)) {
        $baseUsername = 'agency';
    }

    $rawUsername = $baseUsername;
    $escUser = mysqli_real_escape_string($connection, $rawUsername);
    $attempts = 0;
    while ($attempts < 10) {
        $checkUser = mysqli_query($connection, "SELECT user_id FROM {$tblPrefix}user WHERE user_username = '{$escUser}' LIMIT 1");
        if (!$checkUser || mysqli_num_rows($checkUser) === 0) {
            break;
        }
        $attempts++;
        $rawUsername = $baseUsername . random_int(100, 9999);
        $escUser = mysqli_real_escape_string($connection, $rawUsername);
    }

    $displayName = trim($firstName . ' ' . $lastName);
    if (empty($displayName)) {
        $displayName = $businessName ?: 'Travel Administrator';
    }
    $escFirst = mysqli_real_escape_string($connection, $firstName);
    $escLast  = mysqli_real_escape_string($connection, $lastName);
    $escDisp  = mysqli_real_escape_string($connection, $displayName);
    $clientIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

    mysqli_query($connection, "START TRANSACTION");

    $insertUser = mysqli_query($connection, "
        INSERT INTO {$tblPrefix}user 
            (user_parent_id, user_username, user_email, user_mobile, user_password, user_salt_key, user_status, user_reg_time, user_reg_ip)
        VALUES 
            (0, '{$escUser}', '{$escEmail}', '{$escMobile}', '{$autoPassword}', '{$userSalt}', 1, NOW(), '{$clientIp}')
    ");
    $userId = mysqli_insert_id($connection);

    $insertProfile = mysqli_query($connection, "
        INSERT INTO {$tblPrefix}profile 
            (profile_user_id, profile_first_name, profile_last_name, profile_display_name, profile_email, profile_mobile)
        VALUES 
            ({$userId}, '{$escFirst}', '{$escLast}', '{$escDisp}', '{$escEmail}', '{$escMobile}')
    ");

    // Create Site Record for this new tenant
    $siteDomain = !empty($subdomain) ? $subdomain : $rawUsername;
    $siteDomain = preg_replace('/[^a-zA-Z0-9-]/', '', strtolower($siteDomain));
    $mainDomain = defined('MAIN_DOMAIN') ? MAIN_DOMAIN : 'isw.one';
    $siteFullDomain = $siteDomain . '.' . $mainDomain;
    $escSiteDomain = mysqli_real_escape_string($connection, $siteFullDomain);
    $escBizName = mysqli_real_escape_string($connection, !empty($businessName) ? $businessName : $displayName . "'s Workspace");

    $insertNewSite = mysqli_query($connection, "
        INSERT INTO {$tblPrefix}site 
            (site_owner_id, site_domain, site_url, site_name, site_app_id, site_dashboard_version, site_status)
        VALUES 
            ({$userId}, '{$escSiteDomain}', 'https://{$escSiteDomain}', '{$escBizName}', {$resolvedAppId}, 3, 1)
    ");
    $newSiteId = mysqli_insert_id($connection);

    if ($newSiteId > 0) {
        $expiryDate = date('Y-m-d', strtotime('+1 year'));
        mysqli_query($connection, "
            INSERT INTO {$tblPrefix}site_installation 
                (site_installation_site_id, site_installation_parent_site_id, site_installation_user_id, site_installation_package_id, site_installation_status, site_installation_expiry_date)
            VALUES 
                ({$newSiteId}, {$newSiteId}, {$userId}, {$resolvedAppId}, 1, '{$expiryDate}')
        ");

        mysqli_query($connection, "
            INSERT INTO {$tblPrefix}site_access 
                (site_access_site_id, site_access_user_id, site_access_type, site_access_status, site_access_meta)
            VALUES 
                ({$newSiteId}, {$userId}, 'administrator', 1, '')
        ");

        $tenantThemeColor = ($appType === 'aleya') ? '#00647a' : '#4f46e5';
        mysqli_query($connection, "
            INSERT INTO {$tblPrefix}site_setting 
                (site_setting_site_id, site_pagination_limit, site_sitemap_limit, site_feed_limit, site_image_quality, site_theme_color)
            VALUES 
                ({$newSiteId}, 10, 10, 10, 85, '{$tenantThemeColor}')
        ");
    }

    // Grant access to current host site as well
    $insertAccess = mysqli_query($connection, "
        INSERT INTO {$tblPrefix}site_access 
            (site_access_site_id, site_access_user_id, site_access_type, site_access_status, site_access_meta)
        VALUES 
            ({$siteId}, {$userId}, '{$userType}', 1, '')
    ");

    if ($insertUser && $insertProfile && $insertAccess && $insertNewSite) {
        mysqli_query($connection, "COMMIT");
    } else {
        mysqli_query($connection, "ROLLBACK");
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'msg'    => 'Registration failed due to a database error. Please try again.'
        ]);
        exit;
    }
}

// Establish Cookies & Session
$cookieExpiry = time() + (30 * 24 * 60 * 60);
$mainDomain = defined('MAIN_DOMAIN') ? MAIN_DOMAIN : ($_SERVER['HTTP_HOST'] ?? '');
$mainDomain = preg_replace('/:\d+$/', '', $mainDomain);
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

$cookieDomain = '.' . ltrim($mainDomain, '.');
if (filter_var($mainDomain, FILTER_VALIDATE_IP) || strpos($mainDomain, 'localhost') !== false) {
    $cookieDomain = '';
}

$siteApp = !empty($appName) ? $appName : (defined('SITE_APP') ? SITE_APP : 'aleya');

$deviceId = !empty($_COOKIE['device_id']) ? (string)$_COOKIE['device_id'] : bin2hex(random_bytes(16));
@setcookie('device_id', $deviceId, $cookieExpiry, '/', $cookieDomain, $isHttps, false);
$_COOKIE['device_id'] = $deviceId;

// Record Session in isw_user_sessions
mysqli_query($connection, "
    INSERT INTO {$tblPrefix}user_sessions 
        (us_site_id, us_user_id, us_analytics_id, us_active_device, us_status) 
    VALUES 
        ({$siteId}, {$userId}, 0, '{$deviceId}', 1)
");

@setcookie('user_data', base64_encode($userSalt), $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('user_name', base64_encode($displayName), $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('user_email', base64_encode($email), $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('user_mobile', base64_encode($mobile), $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('user_type', base64_encode('administrator'), $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('user_meta', '', $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('site_app', base64_encode($siteApp), $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('isLogin', '1', $cookieExpiry, '/', $cookieDomain, $isHttps, false);

$_COOKIE['user_data'] = base64_encode($userSalt);

if (session_status() === PHP_SESSION_ACTIVE) {
    $_SESSION['user_id'] = $userId;
    $_SESSION['site_id'] = isset($newSiteId) && $newSiteId > 0 ? $newSiteId : $siteId;
    $_SESSION['user_role'] = 'administrator';
    $_SESSION['display_name'] = $displayName;
}

$finalRedirect = !empty($redirectUrl) && $redirectUrl !== '#' ? $redirectUrl : '/dashboard/';

echo json_encode([
    'status'       => 'success',
    'msg'          => 'Account created successfully! Welcome to your dashboard.',
    'url'          => $finalRedirect,
    'redirect_url' => $finalRedirect,
    'user'         => [
        'id'    => $userId,
        'name'  => $displayName,
        'email' => $email,
        'app'   => $siteApp
    ]
]);
exit;
