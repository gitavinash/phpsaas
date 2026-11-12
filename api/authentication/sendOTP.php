<?php

function sendOTP($connection, $data)
{
    if (!function_exists('sendOTPviaNimbus')) {
        $commFile = defined('COMMUNICATION_FUNCTIONS') ? COMMUNICATION_FUNCTIONS : dirname(__DIR__, 2) . '/lib/functions/communicatipn.php';
        if (file_exists($commFile)) {
            require_once $commFile;
        }
    }
    $ssoFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__, 2) . '/lib/modules/sso.php';
    if (file_exists($ssoFile)) {
        require_once $ssoFile;
    }

    extract($data);

    $rawMobile = isset($mobile) ? trim((string) $mobile) : '';
    $siteIdInt = (int) SITE_ID;
    $user_id = 0;

    if (function_exists('iswFindUserAndSiteAccessByIdentifier')) {
        $lookup = iswFindUserAndSiteAccessByIdentifier($connection, $rawMobile, $siteIdInt, 'mobile');
        if (!empty($lookup['resolved']['user']['user_id'])) {
            $user_id = (int) $lookup['resolved']['user']['user_id'];
        }
    } else {
        $escLookupMobile = mysqli_real_escape_string($connection, $rawMobile);
        if ($checkUser = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user WHERE user_mobile = '{$escLookupMobile}' LIMIT 1")) {
            if (mysqli_num_rows($checkUser) > 0) {
                $uRow = mysqli_fetch_assoc($checkUser);
                $user_id = (int) ($uRow['user_id'] ?? 0);
            }
        }
    }

    $mode = mysqli_real_escape_string($connection, (string) ((isset($method)) ? $method : 'sms'));
    $escMobile = mysqli_real_escape_string($connection, $rawMobile);

    $expireDuration = ((isset($setting_otp_expire)) ? (int) $setting_otp_expire : 10);
    $expireTime = date('Y-m-d H:i:s', strtotime("{$expireDuration} minutes"));

    $otpCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $saltKey = $otpCode;

    if ($sendOTP = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "otp(otp_user_id, otp_site_id, otp_mode, otp_destination, otp_code, otp_status, otp_expire) VALUES('{$user_id}', '{$siteIdInt}', '{$mode}', '{$escMobile}', '{$otpCode}', 0, '{$expireTime}')")) {

        $otp_id = mysqli_insert_id($connection);
        $status = 200;
        $msg = 'OTP send successfully';
        $url = "/verify-otp/?id=" . base64_encode((string) $otp_id) . "&mobile=" . urlencode($rawMobile);

        sendOTPviaNimbus($rawMobile, $otpCode);
    } else {

        $msg = 'Technical errro while sending otp';
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : ''),
        'url' => ((isset($url)) ? $url : '')
    );

    echo json_encode($result, true);
}