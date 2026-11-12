<?php

$ssoFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__, 2) . '/lib/modules/sso.php';
if (file_exists($ssoFile)) {
    require_once $ssoFile;
}

$httpStatus = 400;
$status = 'error';
$msg = 'Invalid request';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $rawMobile = isset($mobile) ? trim((string) $mobile) : trim((string) ($_GET['mobile'] ?? ''));
    $cleanMobile = preg_replace('/\D/', '', $rawMobile);
    if (strlen($cleanMobile) === 12 && substr($cleanMobile, 0, 2) === '91') {
        $cleanMobile = substr($cleanMobile, 2);
    } elseif (strlen($cleanMobile) === 13 && substr($cleanMobile, 0, 3) === '091') {
        $cleanMobile = substr($cleanMobile, 3);
    }

    if (!preg_match('/^[6-9]\d{9}$/', $cleanMobile)) {
        $status = 'error';
        $msg = 'Invalid mobile number. Must be 10 digits and start with 6-9.';
        $httpStatus = 400;
    } else {
        $mobile = $cleanMobile;
        $user_id = 0;
        $saltKey = '';
        $profile_display_name = '';

        if (function_exists('iswFindUserAndSiteAccessByIdentifier')) {
            $lookup = iswFindUserAndSiteAccessByIdentifier($connection, $mobile, (int) SITE_ID, 'mobile');
            if (!empty($lookup['resolved']['user'])) {
                $resolvedUser = $lookup['resolved']['user'];
                $user_id = (int) ($resolvedUser['user_id'] ?? 0);
                $saltKey = (string) ($resolvedUser['user_salt_key'] ?? '');
                $profile_display_name = trim((string) ($resolvedUser['logginUserName'] ?? ($resolvedUser['profile_display_name'] ?? '')));
            }
        } else {
            $escMobile = mysqli_real_escape_string($connection, $mobile);
            if ($checkMobile = mysqli_query($connection, "SELECT u.user_id, u.user_salt_key AS saltKey, p.profile_display_name FROM " . TABLE_PREFIX . "user u LEFT JOIN " . TABLE_PREFIX . "profile p ON p.profile_user_id = u.user_id WHERE u.user_mobile = '{$escMobile}' LIMIT 1")) {
                if (mysqli_num_rows($checkMobile) > 0) {
                    $uRow = mysqli_fetch_assoc($checkMobile);
                    $user_id = (int) ($uRow['user_id'] ?? 0);
                    $saltKey = (string) ($uRow['saltKey'] ?? '');
                    $profile_display_name = trim((string) ($uRow['profile_display_name'] ?? ''));
                }
            }
        }

        $otpComment = ($profile_display_name !== '') ? $profile_display_name : 'User';

        // Send OTP
        $showOutput = false;
        $otpMode = 'sms';
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