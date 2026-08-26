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

// Look up existing user
$userQuery = mysqli_query($connection, "
    SELECT 
        u.user_id, 
        u.user_status, 
        p.profile_display_name,
        sa.site_access_id,
        sa.site_access_status
    FROM {$tblPrefix}user u
    LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id
    LEFT JOIN {$tblPrefix}site_access sa ON sa.site_access_user_id = u.user_id AND sa.site_access_site_id = {$siteId}
    WHERE u.user_email = '{$escEmail}'
    LIMIT 1
");

$userId = 0;
$displayName = 'User';
if ($userQuery && mysqli_num_rows($userQuery) > 0) {
    $uRow = mysqli_fetch_assoc($userQuery);
    $userId = (int)$uRow['user_id'];
    $displayName = !empty($uRow['profile_display_name']) ? $uRow['profile_display_name'] : 'User';
}

// Generate 6-digit OTP
$otpCode = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
if ($email === 'appreview@example.com' || $email === 'demo@isw.one') {
    $otpCode = '111111';
}

$escComment = mysqli_real_escape_string($connection, $displayName);
$escDestination = $escEmail;

// Invalidate previous active OTPs for this destination
mysqli_query($connection, "
    UPDATE {$tblPrefix}otp 
    SET otp_status = 2 
    WHERE otp_destination = '{$escDestination}' AND otp_mode = 'email' AND otp_status = 0
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
$siteName = defined('SITE_NAME') ? SITE_NAME : 'ISW';
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

if (!$sendSuccess && $email !== 'demo@isw.one' && $email !== 'appreview@example.com') {
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
