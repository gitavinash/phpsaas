<?php

/**
 * V3 Authentication API - Google OAuth / One-Tap Login
 * Path: /api/v3/auth/google.php
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

$credential = trim($data['credential'] ?? ($data['id_token'] ?? ''));
$redirectUrl = trim($data['redirect_url'] ?? '/dashboard/');

$googleEmail = '';
$googleName = '';
$googlePicture = '';
$googleSub = '';

if (!empty($credential)) {
    // 1. Try decoding Google JWT token payload safely
    $tokenParts = explode('.', $credential);
    if (count($tokenParts) >= 2) {
        $payloadRaw = base64_decode(strtr($tokenParts[1], '-_', '+/'));
        $payload = json_decode($payloadRaw, true);
        if (is_array($payload) && !empty($payload['email'])) {
            $googleEmail   = strtolower(trim($payload['email']));
            $googleName    = trim($payload['name'] ?? '');
            $googlePicture = trim($payload['picture'] ?? '');
            $googleSub     = trim($payload['sub'] ?? '');
        }
    }

    // 2. If tokeninfo verification is desired
    if (empty($googleEmail)) {
        $verifyUrl = "https://oauth2.googleapis.com/tokeninfo?id_token=" . urlencode($credential);
        $ch = curl_init($verifyUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        $resp = curl_exec($ch);
        curl_close($ch);

        if ($resp) {
            $tokenInfo = json_decode($resp, true);
            if (is_array($tokenInfo) && !empty($tokenInfo['email'])) {
                $googleEmail   = strtolower(trim($tokenInfo['email']));
                $googleName    = trim($tokenInfo['name'] ?? '');
                $googlePicture = trim($tokenInfo['picture'] ?? '');
                $googleSub     = trim($tokenInfo['sub'] ?? '');
            }
        }
    }
}

if (empty($googleEmail)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Google authentication failed or expired. Please try again.'
    ]);
    exit;
}

$escEmail = mysqli_real_escape_string($connection, $googleEmail);

// Look up existing user
$userSql = "
    SELECT 
        u.user_id, 
        u.user_username, 
        u.user_email, 
        u.user_mobile, 
        u.user_salt_key, 
        u.user_status,
        p.profile_display_name, 
        p.profile_first_name, 
        p.profile_last_name, 
        p.profile_image,
        sa.site_access_id,
        sa.site_access_type, 
        sa.site_access_status, 
        sa.site_access_meta
    FROM {$tblPrefix}user u
    LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id
    LEFT JOIN {$tblPrefix}site_access sa ON sa.site_access_user_id = u.user_id AND sa.site_access_site_id = {$siteId}
    WHERE u.user_email = '{$escEmail}'
    LIMIT 1
";

$userRes = mysqli_query($connection, $userSql);

if ($userRes && mysqli_num_rows($userRes) > 0) {
    $user = mysqli_fetch_assoc($userRes);
    $userId = (int)$user['user_id'];
    $userSalt = (string)$user['user_salt_key'];
    $userRole = strtolower(trim($user['site_access_type'] ?? 'administrator'));
    $displayName = trim($user['profile_display_name'] ?? '');
    if (empty($displayName)) {
        $displayName = !empty($googleName) ? $googleName : ($user['user_username'] ?? 'Team Member');
    }
    $userEmail = $user['user_email'];
    $userMobile = $user['user_mobile'] ?? '';
    $userAvatar = !empty($user['profile_image']) ? $user['profile_image'] : $googlePicture;
    $siteMeta = $user['site_access_meta'] ?? '';

    // Update avatar if currently empty
    if (empty($user['profile_image']) && !empty($googlePicture)) {
        $escPic = mysqli_real_escape_string($connection, $googlePicture);
        mysqli_query($connection, "UPDATE {$tblPrefix}profile SET profile_image = '{$escPic}' WHERE profile_user_id = {$userId}");
    }

    if (empty($user['site_access_id']) || (int)$user['site_access_status'] !== 1) {
        http_response_code(403);
        echo json_encode([
            'status' => 'error',
            'code'   => 'access_denied',
            'msg'    => 'Access denied. You do not have access to this workspace.'
        ]);
        exit;
    }
} else {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'code'   => 'access_denied',
        'msg'    => 'Access denied. You do not have access to this workspace.'
    ]);
    exit;
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

$siteApp = defined('SITE_APP') ? SITE_APP : 'aleya';

@setcookie('user_data', base64_encode($userSalt), $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('user_name', base64_encode($displayName), $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('user_email', base64_encode($userEmail), $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('user_mobile', base64_encode($userMobile), $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('user_image', base64_encode($userAvatar), $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('user_type', base64_encode($userRole), $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('user_meta', base64_encode($siteMeta), $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('site_app', base64_encode($siteApp), $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('isLogin', '1', $cookieExpiry, '/', $cookieDomain, $isHttps, false);

$_COOKIE['user_data'] = $userSalt;

if (session_status() === PHP_SESSION_ACTIVE) {
    $_SESSION['user_id'] = $userId;
    $_SESSION['site_id'] = $siteId;
    $_SESSION['user_role'] = $userRole;
    $_SESSION['display_name'] = $displayName;
}

$finalRedirect = !empty($redirectUrl) && $redirectUrl !== '#' ? $redirectUrl : '/dashboard/';

echo json_encode([
    'status'       => 'success',
    'msg'          => 'Google login successful. Welcome, ' . $displayName . '!',
    'url'          => $finalRedirect,
    'redirect_url' => $finalRedirect,
    'user'         => [
        'id'     => $userId,
        'name'   => $displayName,
        'email'  => $userEmail,
        'role'   => $userRole,
        'avatar' => $userAvatar
    ]
]);
exit;
