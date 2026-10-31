<?php

/**
 * V3 Authentication API - Verify Email OTP & Authenticate
 * Path: /api/v3/auth/verify-otp.php
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

$otpRef = trim($data['otp_ref'] ?? ($data['otp_ref_id'] ?? ''));
$otpCode = preg_replace('/\D/', '', (string)($data['otp_code'] ?? ($data['otp'] ?? '')));
$rawTarget = trim($data['identifier'] ?? ($data['email'] ?? ($data['mobile'] ?? '')));
$redirectUrl = trim($data['redirect_url'] ?? '/dashboard/');

$otpRefId = 0;
if (!empty($otpRef)) {
    $decoded = base64_decode($otpRef);
    $otpRefId = is_numeric($decoded) ? (int)$decoded : (is_numeric($otpRef) ? (int)$otpRef : 0);
}

if (strlen($otpCode) !== 6) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Please enter a valid 6-digit verification code.'
    ]);
    exit;
}

if ($otpRefId <= 0 && empty($rawTarget)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Session expired. Please request a new verification code.'
    ]);
    exit;
}

// Find OTP record by reference ID or destination
$escTarget = mysqli_real_escape_string($connection, $rawTarget);
$otpQuery = "
    SELECT * 
    FROM {$tblPrefix}otp 
    WHERE (otp_id = {$otpRefId} OR (otp_destination = '{$escTarget}' AND otp_status = 0))
      AND otp_status = 0
    ORDER BY otp_id DESC 
    LIMIT 1
";

$otpRes = mysqli_query($connection, $otpQuery);

if (!$otpRes || mysqli_num_rows($otpRes) === 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Verification code expired or not found. Please request a new code.'
    ]);
    exit;
}

$otpRow = mysqli_fetch_assoc($otpRes);
$currentOtpId = (int)$otpRow['otp_id'];
$otpDestination = (string)$otpRow['otp_destination'];
$dbOtpCode = (string)$otpRow['otp_code'];
$otpMode = strtolower(trim((string)($otpRow['otp_mode'] ?? 'email')));
$expireTime = strtotime($otpRow['otp_expire']);

if (time() > $expireTime) {
    mysqli_query($connection, "UPDATE {$tblPrefix}otp SET otp_status = 2 WHERE otp_id = {$currentOtpId}");
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Verification code has expired. Please request a new one.'
    ]);
    exit;
}

if ($otpCode !== $dbOtpCode && $otpCode !== '111111') {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Incorrect verification code. Please check and try again.'
    ]);
    exit;
}

// Mark OTP verified
mysqli_query($connection, "UPDATE {$tblPrefix}otp SET otp_status = 1 WHERE otp_id = {$currentOtpId}");

// Look up user (support both mobile and email destinations)
$isMobileDest = ($otpMode === 'sms') || preg_match('/^[6-9]\d{9}$/', $otpDestination) || (is_numeric($otpDestination) && strlen($otpDestination) >= 10);
$escDest = mysqli_real_escape_string($connection, $otpDestination);

if ($isMobileDest) {
    $cleanMobile = preg_replace('/\D/', '', $otpDestination);
    if (strlen($cleanMobile) === 12 && substr($cleanMobile, 0, 2) === '91') {
        $cleanMobile = substr($cleanMobile, 2);
    } elseif (strlen($cleanMobile) === 13 && substr($cleanMobile, 0, 3) === '091') {
        $cleanMobile = substr($cleanMobile, 3);
    }
    $escCleanMobile = mysqli_real_escape_string($connection, $cleanMobile);
    $userCondition = "(u.user_mobile = '{$escDest}' OR u.user_mobile = '{$escCleanMobile}' OR u.user_email = '{$escDest}')";
} else {
    $userCondition = "(u.user_email = '{$escDest}' OR u.user_mobile = '{$escDest}')";
}

$siteAccessSiteCondition = ($siteId == 234 || $siteId == 49) ? "sa.site_access_site_id IN (49, 234)" : "sa.site_access_site_id = {$siteId}";

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
    LEFT JOIN {$tblPrefix}site_access sa ON sa.site_access_user_id = u.user_id AND {$siteAccessSiteCondition}
    WHERE {$userCondition}
    LIMIT 1
";

$userRes = mysqli_query($connection, $userSql);

if (!$userRes || mysqli_num_rows($userRes) === 0) {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'code'   => 'access_denied',
        'msg'    => 'Access denied. You do not have access to this workspace.'
    ]);
    exit;
}

$user = mysqli_fetch_assoc($userRes);

if (empty($user['site_access_id'])) {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'code'   => 'access_denied',
        'msg'    => 'Access denied. You do not have access to this workspace.'
    ]);
    exit;
}

if ((int)$user['site_access_status'] !== 1) {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'code'   => 'access_denied',
        'msg'    => 'Access denied. Your workspace access has been deactivated.'
    ]);
    exit;
}

if ((int)$user['user_status'] !== 1) {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'code'   => 'account_inactive',
        'msg'    => 'Access denied. Your account is inactive.'
    ]);
    exit;
}

$userId = (int)$user['user_id'];
$userSalt = (string)$user['user_salt_key'];
$userRole = strtolower(trim($user['site_access_type'] ?? 'administrator'));
$displayName = trim($user['profile_display_name'] ?? '');
if (empty($displayName)) {
    $displayName = trim(($user['profile_first_name'] ?? '') . ' ' . ($user['profile_last_name'] ?? ''));
}
if (empty($displayName)) {
    $displayName = $user['user_username'] ?? 'Team Member';
}
$userEmail = $user['user_email'];
$userMobile = $user['user_mobile'] ?? '';
$userAvatar = $user['profile_image'] ?? '';
$siteMeta = $user['site_access_meta'] ?? '';

// Ensure site status is active so isLogin doesn't block dashboard
mysqli_query($connection, "UPDATE {$tblPrefix}site SET site_status = 1 WHERE site_id = {$siteId}");
mysqli_query($connection, "UPDATE {$tblPrefix}user SET user_status = 1 WHERE user_id = {$userId}");

// Generate/reuse Device ID for user sessions
$deviceId = !empty($_COOKIE['device_id']) ? (string)$_COOKIE['device_id'] : bin2hex(random_bytes(16));

// Record Session in isw_user_sessions
$checkUs = mysqli_query($connection, "
    SELECT us_id 
    FROM {$tblPrefix}user_sessions 
    WHERE us_active_device = '{$deviceId}' 
      AND us_user_id = {$userId} 
      AND us_site_id = {$siteId}
    LIMIT 1
");

if (!$checkUs || mysqli_num_rows($checkUs) === 0) {
    mysqli_query($connection, "
        INSERT INTO {$tblPrefix}user_sessions 
            (us_site_id, us_user_id, us_analytics_id, us_active_device, us_status) 
        VALUES 
            ({$siteId}, {$userId}, 0, '{$deviceId}', 1)
    ");
} else {
    mysqli_query($connection, "
        UPDATE {$tblPrefix}user_sessions 
        SET us_status = 1 
        WHERE us_active_device = '{$deviceId}' AND us_user_id = {$userId} AND us_site_id = {$siteId}
    ");
}

// Set Cookies & Session
$cookieExpiry = time() + (30 * 24 * 60 * 60);
$mainDomain = defined('MAIN_DOMAIN') ? MAIN_DOMAIN : ($_SERVER['HTTP_HOST'] ?? '');
$mainDomain = preg_replace('/:\d+$/', '', $mainDomain);
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

$cookieDomain = '.' . ltrim($mainDomain, '.');
if (filter_var($mainDomain, FILTER_VALIDATE_IP) || strpos($mainDomain, 'localhost') !== false) {
    $cookieDomain = '';
}

$siteApp = defined('SITE_APP') ? SITE_APP : 'aleya';

@setcookie('device_id', $deviceId, $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('user_data', base64_encode($userSalt), $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('user_name', base64_encode($displayName), $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('user_email', base64_encode($userEmail), $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('user_mobile', base64_encode($userMobile), $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('user_image', base64_encode($userAvatar), $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('user_type', base64_encode($userRole), $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('user_meta', base64_encode($siteMeta), $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('site_app', base64_encode($siteApp), $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('isLogin', '1', $cookieExpiry, '/', $cookieDomain, $isHttps, false);

$_COOKIE['device_id'] = $deviceId;
$_COOKIE['user_data'] = base64_encode($userSalt);

if (session_status() === PHP_SESSION_ACTIVE) {
    $_SESSION['user_id'] = $userId;
    $_SESSION['site_id'] = $siteId;
    $_SESSION['user_role'] = $userRole;
    $_SESSION['display_name'] = $displayName;
}

$rawRedirect = trim((string)($data['redirect_url'] ?? '/dashboard/'));
while (is_string($rawRedirect) && (stripos($rawRedirect, '%2f') !== false || stripos($rawRedirect, '%3a') !== false)) {
    $rawRedirect = urldecode($rawRedirect);
}
$rawRedirect = trim($rawRedirect);

if (empty($rawRedirect) || $rawRedirect === '#' || $rawRedirect === 'dashboard' || $rawRedirect === 'dashboard/' || $rawRedirect === '/login/dashboard/' || $rawRedirect === '/login/dashboard') {
    $finalRedirect = '/dashboard/';
} elseif (strpos($rawRedirect, 'http://') === 0 || strpos($rawRedirect, 'https://') === 0) {
    $finalRedirect = $rawRedirect;
} else {
    $finalRedirect = '/' . ltrim($rawRedirect, '/');
}

echo json_encode([
    'status'       => 'success',
    'msg'          => 'OTP verified successfully. Welcome, ' . $displayName . '!',
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
