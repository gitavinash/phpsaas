<?php

require_once UTIL_FUNCTIONS;
$ssoFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__, 2) . '/lib/modules/sso.php';
if (file_exists($ssoFile)) {
    require_once $ssoFile;
}

$httpStatus = 400;
$status = 'error';
$msg = 'Invalid request';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $email = isset($email) ? trim((string) $email) : trim((string) ($_GET['email'] ?? ''));

    if (!isEmailValid($email)) {
        $status     = 'error';
        $msg        = 'Invalid email address';
        $httpStatus = 422;
    } else {
        $lookup = iswFindUserAndSiteAccessByIdentifier($connection, $email, (int) SITE_ID, 'email');

        if ($lookup['reason'] === 'disabled') {
            $status = 'error';
            $msg = 'Your workspace access has been deactivated.';
            $httpStatus = 403;
        } elseif ($lookup['reason'] === 'inactive_user') {
            $status = 'error';
            $msg = 'Your account is inactive or pending verification.';
            $httpStatus = 403;
        } elseif ($lookup['reason'] !== 'ok' || empty($lookup['resolved']) || empty($lookup['resolved']['has_access'])) {
            $status = 'error';
            $msg = 'You do not have access to this workspace.';
            $httpStatus = 403;
        } else {
            $resolvedUser = $lookup['resolved']['user'] ?? [];
            $user_id = (int) ($resolvedUser['user_id'] ?? 0);
            $saltKey = (string) ($resolvedUser['user_salt_key'] ?? '');
            $profile_display_name = trim((string) ($resolvedUser['logginUserName'] ?? ($resolvedUser['profile_display_name'] ?? '')));

            // Send OTP
            $showOutput = false;
            $otpMode = 'email';
            $otpComment = ($profile_display_name !== '') ? $profile_display_name : 'User';
            require_once 'send-otp.php';
            if (isset($response['status']) && ($response['status'] == 'success' || $response['status'] == 'warning')) {
                $status = 'success';
                $msg = 'OTP sent successfully';
                $httpStatus = 200;
            } else {
                $status = 'error';
                $msg = !empty($response['msg']) ? $response['msg'] : 'Failed to send OTP';
                $httpStatus = isset($httpStatus) && $httpStatus >= 400 ? (int) $httpStatus : 500;
            }
        }
    }
} else {
    $status = 'error';
    $msg = 'Invalid request method';
    $httpStatus = 405;
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
    'otpRef' => ((isset($response['otpRef'])) ? base64_encode((string) $response['otpRef']) : 0),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
