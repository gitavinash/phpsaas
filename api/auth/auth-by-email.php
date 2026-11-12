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
        $user_id = 0;
        $saltKey = '';
        $profile_display_name = '';

        if (function_exists('iswFindUserAndSiteAccessByIdentifier')) {
            $lookup = iswFindUserAndSiteAccessByIdentifier($connection, $email, (int) SITE_ID, 'email');
            if (!empty($lookup['resolved']['user'])) {
                $resolvedUser = $lookup['resolved']['user'];
                $user_id = (int) ($resolvedUser['user_id'] ?? 0);
                $saltKey = (string) ($resolvedUser['user_salt_key'] ?? '');
                $profile_display_name = trim((string) ($resolvedUser['logginUserName'] ?? ($resolvedUser['profile_display_name'] ?? '')));
            }
        } else {
            $escEmail = mysqli_real_escape_string($connection, $email);
            if ($checkEmail = mysqli_query($connection, "SELECT u.user_id, u.user_salt_key AS saltKey, p.profile_display_name FROM " . TABLE_PREFIX . "user u LEFT JOIN " . TABLE_PREFIX . "profile p ON p.profile_user_id = u.user_id WHERE u.user_email = '{$escEmail}' LIMIT 1")) {
                if (mysqli_num_rows($checkEmail) > 0) {
                    $uRow = mysqli_fetch_assoc($checkEmail);
                    $user_id = (int) ($uRow['user_id'] ?? 0);
                    $saltKey = (string) ($uRow['saltKey'] ?? '');
                    $profile_display_name = trim((string) ($uRow['profile_display_name'] ?? ''));
                }
            }
        }

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
