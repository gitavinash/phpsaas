<?php

function sendOTP($connection, $data)
{
    $ssoFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__, 2) . '/lib/modules/sso.php';
    if (file_exists($ssoFile)) {
        require_once $ssoFile;
    }

    extract($data);

    $rawMobile = isset($mobile) ? trim((string) $mobile) : '';
    $siteIdInt = (int) SITE_ID;
    $lookup = function_exists('iswFindUserAndSiteAccessByIdentifier')
        ? iswFindUserAndSiteAccessByIdentifier($connection, $rawMobile, $siteIdInt, 'mobile')
        : ['reason' => 'no_access', 'resolved' => null];

    if ($lookup['reason'] === 'disabled') {
        $status = 'error';
        $msg = 'Your workspace access has been deactivated.';
    } elseif ($lookup['reason'] === 'inactive_user') {
        $status = 'error';
        $msg = 'Your account is inactive or pending verification.';
    } elseif ($lookup['reason'] !== 'ok' || empty($lookup['resolved']) || empty($lookup['resolved']['has_access'])) {
        $status = 'error';
        $msg = 'You do not have access to this workspace.';
    } else {
        $user_id = (int) ($lookup['resolved']['user']['user_id'] ?? 0);
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

            require_once SMS_TEMPLATE . 'otp.php';
        } else {

            $msg = 'Technical errro while sending otp';
        }
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : ''),
        'url' => ((isset($url)) ? $url : '')
    );

    echo json_encode($result, true);
}