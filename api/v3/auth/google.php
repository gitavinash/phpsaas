<?php

/**
 * V3 Authentication API - Google OAuth / One-Tap Login
 * Path: /api/v3/auth/google.php
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
        p.profile_image
    FROM {$tblPrefix}user u
    LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id
    WHERE u.user_email = '{$escEmail}'
    LIMIT 1
";

$userRes = mysqli_query($connection, $userSql);

if ($userRes && mysqli_num_rows($userRes) > 0) {
    $user = mysqli_fetch_assoc($userRes);
    $userId = (int)$user['user_id'];

    $resolved = iswResolveSiteAccessForUser($connection, $userId, $siteId);
    if (!$resolved || empty($resolved['has_access'])) {
        http_response_code(403);
        echo json_encode([
            'status' => 'error',
            'code'   => 'access_denied',
            'msg'    => (($resolved['reason'] ?? '') === 'disabled')
                ? 'Access denied. Your workspace access has been deactivated.'
                : 'Access denied. You do not have access to this workspace.'
        ]);
        exit;
    }

    if ((int)$user['user_status'] !== 1) {
        http_response_code(403);
        echo json_encode([
            'status' => 'error',
            'code'   => 'account_inactive',
            'msg'    => 'Your account is inactive or pending email verification.'
        ]);
        exit;
    }

    // Update avatar if currently empty
    if (empty($user['profile_image']) && !empty($googlePicture)) {
        $escPic = mysqli_real_escape_string($connection, $googlePicture);
        mysqli_query($connection, "UPDATE {$tblPrefix}profile SET profile_image = '{$escPic}' WHERE profile_user_id = {$userId}");
    }

    $userDataRow = $resolved['user'] ?? $user;
    $accessData  = $resolved['access'] ?? [];
    $userSalt    = (string)($userDataRow['user_salt_key'] ?? '');
    $userRole    = strtolower(trim((string)($accessData['site_access_type'] ?? 'administrator')));
    $displayName = trim((string)($userDataRow['logginUserName'] ?? ''));
    if (empty($displayName)) {
        $displayName = !empty($googleName) ? $googleName : ($userDataRow['user_username'] ?? 'Team Member');
        $resolved['user']['logginUserName'] = $displayName;
    }
    $userEmail  = (string)($userDataRow['user_email'] ?? $googleEmail);
    $userMobile = (string)($userDataRow['user_mobile'] ?? '');
    $userAvatar = !empty($userDataRow['profile_image']) ? (string)$userDataRow['profile_image'] : $googlePicture;
} else {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'code'   => 'access_denied',
        'msg'    => 'Access denied. You do not have access to this workspace.'
    ]);
    exit;
}

// Establish Device ID, Group Sessions & Cookies
$deviceId = !empty($_COOKIE['device_id']) ? trim((string)$_COOKIE['device_id']) : bin2hex(random_bytes(16));
$_COOKIE['device_id'] = $deviceId;
$_COOKIE['user_data'] = base64_encode($userSalt);

iswSyncGroupUserSessions($connection, $userId, $deviceId, $siteId, 0);
iswSetSiteAuthCookies($resolved, $deviceId, 30);

if (session_status() === PHP_SESSION_ACTIVE) {
    $_SESSION['user_id'] = $userId;
    $_SESSION['site_id'] = $siteId;
    $_SESSION['user_role'] = $userRole;
    $_SESSION['display_name'] = $displayName;
}

$finalRedirect = iswNormalizeRedirectPath($redirectUrl, '/dashboard/');

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
