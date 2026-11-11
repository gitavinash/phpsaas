<?php

/**
 * V3 Authentication API - Login Endpoint
 * Path: /api/v3/auth/login.php
 */

header('Content-Type: application/json; charset=utf-8');

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (empty($_SERVER['DOCUMENT_ROOT'])) {
        $_SERVER['DOCUMENT_ROOT'] = $docRoot;
    }
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
}

$ssoFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__, 3) . '/lib/modules/sso.php';
if (file_exists($ssoFile)) {
    require_once $ssoFile;
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
    $siteQuery = mysqli_query($connection, "SELECT site_id FROM {$tblPrefix}site WHERE site_domain = '{$escapedHost}' OR site_url = '{$escapedHost}' OR site_url LIKE '%{$escapedHost}%' ORDER BY (site_domain = '{$escapedHost}') DESC, (site_url = '{$escapedHost}') DESC LIMIT 1");
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

// Parse request input (supports JSON payload, $_POST, or form-data)
$rawInput = file_get_contents('php://input');
$jsonData = json_decode($rawInput, true) ?? [];
$inputData = !empty($_POST) ? $_POST : $jsonData;

$identifier = trim($inputData['identifier'] ?? ($inputData['email'] ?? ($inputData['username'] ?? ($inputData['mobile'] ?? ''))));
$password   = (string)($inputData['password'] ?? ($inputData['pass'] ?? ''));
$redirectUrl = trim($inputData['redirect_url'] ?? ($inputData['redirect'] ?? '/dashboard/'));

if (empty($identifier)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Please enter your email, mobile number, or username.'
    ]);
    exit;
}

if (empty($password)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Please enter your account password.'
    ]);
    exit;
}

$escIdentifier = mysqli_real_escape_string($connection, $identifier);

// Query user by email, mobile, or username joined with site_access for the current site
$userSql = "
    SELECT 
        u.user_id, 
        u.user_username, 
        u.user_email, 
        u.user_mobile, 
        u.user_password, 
        u.user_salt_key, 
        u.user_status,
        p.profile_display_name, 
        p.profile_first_name, 
        p.profile_last_name, 
        p.profile_image,
        sa.site_access_id,
        sa.site_access_type, 
        sa.site_access_status, 
        sa.site_access_meta,
        s.site_app_id
    FROM {$tblPrefix}user u
    LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id
    LEFT JOIN {$tblPrefix}site_access sa ON sa.site_access_user_id = u.user_id AND sa.site_access_site_id = {$siteId}
    LEFT JOIN {$tblPrefix}site s ON s.site_id = sa.site_access_site_id
    WHERE (u.user_email = '{$escIdentifier}' OR u.user_mobile = '{$escIdentifier}' OR u.user_username = '{$escIdentifier}')
    ORDER BY (sa.site_access_status = 1) DESC, (sa.site_access_id IS NOT NULL) DESC, u.user_id ASC
    LIMIT 1
";

$userRes = mysqli_query($connection, $userSql);

if (!$userRes || mysqli_num_rows($userRes) === 0) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'code'   => 'user_not_found',
        'msg'    => 'No account found with the provided credentials.'
    ]);
    exit;
}

$user = mysqli_fetch_assoc($userRes);
$userId = (int)$user['user_id'];
$userSalt = (string)($user['user_salt_key'] ?? '');
$dbPassword = (string)($user['user_password'] ?? '');

// Verify Password Hash
$isPasswordValid = false;
if (!empty($userSalt) && hash('sha256', $userSalt . $password) === $dbPassword) {
    $isPasswordValid = true;
} elseif (password_verify($password, $dbPassword)) {
    $isPasswordValid = true;
} elseif (hash('sha256', $password) === $dbPassword) {
    $isPasswordValid = true;
}

if (!$isPasswordValid) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'code'   => 'invalid_password',
        'msg'    => 'Incorrect password. Please verify and try again.'
    ]);
    exit;
}

// Check Site Access Permission first (strict per-site + legacy 49/234 fallback)
$resolved = function_exists('iswResolveSiteAccessForUser')
    ? iswResolveSiteAccessForUser($connection, $userId, $siteId)
    : null;

if (!$resolved || empty($resolved['has_access'])) {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'code'   => 'access_denied',
        'msg'    => (($resolved['reason'] ?? '') === 'disabled')
            ? 'Access denied. Your workspace access has been deactivated.'
            : 'Access denied. You do not have active access permissions for this workspace.'
    ]);
    exit;
}

// Check Global User Status
if ((int)$user['user_status'] !== 1) {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'code'   => 'account_inactive',
        'msg'    => 'Your account is inactive or pending email verification.'
    ]);
    exit;
}

// Establish Authenticated Session & Cookies
$accessData  = $resolved['access'] ?? [];
$userDataRow = $resolved['user'] ?? $user;
$userRole    = strtolower(trim((string)($accessData['site_access_type'] ?? 'administrator')));
$displayName = trim((string)($userDataRow['logginUserName'] ?? ($user['profile_display_name'] ?? '')));
if (empty($displayName)) {
    $displayName = trim((string)(($userDataRow['profile_first_name'] ?? '') . ' ' . ($userDataRow['profile_last_name'] ?? '')));
}
if (empty($displayName)) {
    $displayName = $userDataRow['user_username'] ?? 'Team Member';
}

$userEmail  = trim((string)($userDataRow['user_email'] ?? ''));
$userMobile = trim((string)($userDataRow['user_mobile'] ?? ''));
$userAvatar = trim((string)($userDataRow['profile_image'] ?? ''));

$deviceId = !empty($_COOKIE['device_id']) ? trim((string)$_COOKIE['device_id']) : bin2hex(random_bytes(16));
$_COOKIE['device_id'] = $deviceId;
$_COOKIE['user_data'] = base64_encode($userSalt);

// Store in PHP Session if active
if (session_status() === PHP_SESSION_ACTIVE) {
    $_SESSION['user_id']      = $userId;
    $_SESSION['site_id']      = $siteId;
    $_SESSION['user_role']    = $userRole;
    $_SESSION['display_name'] = $displayName;
}

// Analytics Session Tracking
$analyticsId = 0;
$analyticsModule = (defined('API_MODULES') ? API_MODULES : dirname(__DIR__, 2) . '/') . 'analytics/session-record.php';
if (file_exists($analyticsModule)) {
    try {
        @include_once $analyticsModule;
    } catch (\Throwable $t) {
        // Analytics failure should never block login
    }
}

$activeDeviceId = trim((string)($_COOKIE['device_id'] ?? $deviceId));
if (function_exists('iswSyncGroupUserSessions')) {
    iswSyncGroupUserSessions($connection, $userId, $activeDeviceId, $siteId, (int)$analyticsId);
}
if (function_exists('iswSetSiteAuthCookies')) {
    iswSetSiteAuthCookies($resolved, $activeDeviceId, 30);
}

// Determine destination URL
$finalRedirect = function_exists('iswNormalizeRedirectPath')
    ? iswNormalizeRedirectPath($redirectUrl, '/dashboard/')
    : (!empty($redirectUrl) && $redirectUrl !== '#' ? $redirectUrl : '/dashboard/');

echo json_encode([
    'status'       => 'success',
    'msg'          => 'Welcome back, ' . $displayName . '! Redirecting...',
    'url'          => $finalRedirect,
    'redirect_url' => $finalRedirect,
    'user'         => [
        'id'           => $userId,
        'encoded_id'   => base64_encode((string)$userId),
        'name'         => $displayName,
        'email'        => $userEmail,
        'mobile'       => $userMobile,
        'role'         => $userRole,
        'avatar'       => $userAvatar
    ]
]);
exit;
