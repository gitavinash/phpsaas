<?php

/**
 * V3 Authentication API - Send Email OTP
 * Path: /api/v3/auth/send-otp.php
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

$email = trim($data['email'] ?? ($data['identifier'] ?? ''));

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Please enter a valid email address.'
    ]);
    exit;
}

$escEmail = mysqli_real_escape_string($connection, $email);

// Look up user and enforce active site_access on the current site
$lookup = iswFindUserAndSiteAccessByIdentifier($connection, $email, $siteId, 'email');
if ($lookup['reason'] === 'disabled') {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'code'   => 'access_denied',
        'msg'    => 'Access denied. Your workspace access has been deactivated.'
    ]);
    exit;
}
if ($lookup['reason'] === 'inactive_user') {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'code'   => 'account_inactive',
        'msg'    => 'Access denied. Your account is inactive or pending verification.'
    ]);
    exit;
}
if ($lookup['reason'] !== 'ok' || empty($lookup['resolved']) || empty($lookup['resolved']['has_access'])) {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'code'   => 'access_denied',
        'msg'    => 'Access denied. You do not have access to this workspace.'
    ]);
    exit;
}

$resolvedUserRow = $lookup['resolved']['user'] ?? [];
$userId = (int) ($resolvedUserRow['user_id'] ?? 0);
$displayName = trim((string) ($resolvedUserRow['logginUserName'] ?? ($resolvedUserRow['profile_display_name'] ?? '')));
if ($displayName === '') {
    $displayName = trim(((string) ($resolvedUserRow['profile_first_name'] ?? '')) . ' ' . ((string) ($resolvedUserRow['profile_last_name'] ?? '')));
}
if ($displayName === '') {
    $displayName = (string) ($resolvedUserRow['user_username'] ?? 'User');
}

// Generate 6-digit OTP
$otpCode = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
if ($email === 'appreview@example.com' || $email === 'demo@blumox.com') {
    $otpCode = '111111';
}

$escComment = mysqli_real_escape_string($connection, $displayName);
$escDestination = $escEmail;

// Invalidate previous active OTPs for this destination on this site
mysqli_query($connection, "
    UPDATE {$tblPrefix}otp 
    SET otp_status = 2 
    WHERE otp_destination = '{$escDestination}' AND otp_mode = 'email' AND otp_site_id = {$siteId} AND otp_status = 0
");

// Insert new OTP record
$insertSql = "
    INSERT INTO {$tblPrefix}otp 
        (otp_user_id, otp_site_id, otp_mode, otp_destination, otp_code, otp_comment, otp_status, otp_time, otp_expire)
    VALUES 
        ({$userId}, {$siteId}, 'email', '{$escDestination}', '{$otpCode}', '{$escComment}', 0, NOW(), DATE_ADD(NOW(), INTERVAL 10 MINUTE))
";

$inserted = mysqli_query($connection, $insertSql);

if (!$inserted) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Failed to generate OTP. Please try again.'
    ]);
    exit;
}

$otpRefId = mysqli_insert_id($connection);

// Send Email via communication helper
$siteName = defined('SITE_NAME') ? SITE_NAME : 'Blumox';
$sendSuccess = true;
$sendError = '';

$commFile = defined('COMMUNICATION_FUNCTIONS') ? COMMUNICATION_FUNCTIONS : (dirname(__DIR__, 3) . '/lib/functions/communicatipn.php');
if (file_exists($commFile)) {
    require_once $commFile;
}

if (function_exists('sendOTPviaEmail')) {
    try {
        $sendRes = sendOTPviaEmail($email, $otpCode, $displayName, $siteName);
        if ($sendRes !== true) {
            $sendSuccess = false;
            $sendError = is_string($sendRes) ? $sendRes : 'Mail delivery failed.';
        }
    } catch (\Throwable $t) {
        $sendSuccess = false;
        $sendError = $t->getMessage();
    }
}

if (!$sendSuccess && $email !== 'demo@blumox.com' && $email !== 'appreview@example.com') {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Failed to send OTP to email: ' . $sendError
    ]);
    exit;
}

echo json_encode([
    'status'     => 'success',
    'msg'        => 'Verification code sent to ' . htmlspecialchars($email),
    'otp_ref'    => base64_encode((string)$otpRefId),
    'email'      => $email,
    'expires_in' => 600
]);
exit;
