<?php

require_once COMMUNICATION_FUNCTIONS;
$ssoFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__, 2) . '/lib/modules/sso.php';
if (file_exists($ssoFile)) {
    require_once $ssoFile;
}

header('Content-Type: application/json');

// ==== Initialize Response ====
$response = [
    'status' => 'error',
    'msg' => 'Invalid request',
    'id' => 0
];
$httpStatus = 400;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $rawMobile = !empty($mobile) ? trim((string) $mobile) : '';
    $rawEmail  = !empty($email) ? trim((string) $email) : '';

    if ($rawMobile !== '') {
        if (!preg_match('/^[6-9]\d{9}$/', $rawMobile)) {
            $response['msg'] = 'Invalid mobile number. Must be 10 digits and start with 6-9.';
            $httpStatus = 400;
            $invalidPayload = true;
        }
    } elseif ($rawEmail !== '') {
        if (!filter_var($rawEmail, FILTER_VALIDATE_EMAIL)) {
            $response['msg'] = 'Invalid email address format.';
            $httpStatus = 400;
            $invalidPayload = true;
        }
    } else {
        $response['msg'] = 'Mobile or Email is required.';
        $httpStatus = 400;
        $invalidPayload = true;
    }

    if (!isset($invalidPayload)) {

        $otpDestination = ($rawMobile !== '') ? $rawMobile : $rawEmail;
        $lookupMode = ($rawMobile !== '') ? 'mobile' : 'email';

        // Optionally resolve matching user_id/display name without blocking OTP delivery
        $verifiedUid = (!empty($user_id) && (int) $user_id > 0) ? (int) $user_id : 0;
        $resolvedDisplayName = '';

        if ($verifiedUid <= 0 && function_exists('iswFindUserAndSiteAccessByIdentifier')) {
            $lookup = iswFindUserAndSiteAccessByIdentifier($connection, $otpDestination, (int) SITE_ID, $lookupMode);
            if (!empty($lookup['resolved']['user']['user_id'])) {
                $verifiedUid = (int) $lookup['resolved']['user']['user_id'];
                $resolvedDisplayName = trim((string) ($lookup['resolved']['user']['logginUserName'] ?? ''));
            }
        } elseif ($verifiedUid <= 0) {
            $escLookupDest = mysqli_real_escape_string($connection, $otpDestination);
            $col = ($lookupMode === 'mobile') ? 'u.user_mobile' : 'u.user_email';
            if ($uRes = mysqli_query($connection, "SELECT u.user_id, p.profile_display_name FROM " . TABLE_PREFIX . "user u LEFT JOIN " . TABLE_PREFIX . "profile p ON p.profile_user_id = u.user_id WHERE {$col} = '{$escLookupDest}' LIMIT 1")) {
                if (mysqli_num_rows($uRes) > 0) {
                    $uRow = mysqli_fetch_assoc($uRes);
                    $verifiedUid = (int) ($uRow['user_id'] ?? 0);
                    $resolvedDisplayName = trim((string) ($uRow['profile_display_name'] ?? ''));
                }
            }
        }

        $otpCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otpMode = ((isset($otpMode) && in_array($otpMode, ['sms', 'email'], true)) ? $otpMode : (($rawMobile !== '') ? 'sms' : 'email'));
        $otpComment = (isset($otpComment) && trim((string) $otpComment) !== '')
            ? (string) $otpComment
            : ($resolvedDisplayName !== '' ? $resolvedDisplayName : 'User');

        if ($otpDestination == '9990026008') {
            $otpCode = '111111';
        }
        if ($otpDestination == 'appreview@example.com') {
            $otpCode = '111111';
        }

        $escComment = mysqli_real_escape_string($connection, $otpComment);
        $escDest = mysqli_real_escape_string($connection, $otpDestination);
        $escMode = mysqli_real_escape_string($connection, $otpMode);
        $siteIdInt = (int) SITE_ID;
        $siteDisplayName = isset($site_name) && $site_name !== '' ? (string) $site_name : (defined('SITE_NAME') ? (string) SITE_NAME : 'Blumox');

        // Invalidate previous active OTPs for this destination and site
        mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "otp SET otp_status = 2 WHERE otp_destination = '{$escDest}' AND otp_mode = '{$escMode}' AND otp_site_id = {$siteIdInt} AND otp_status = 0");

        $sql = "INSERT INTO " . TABLE_PREFIX . "otp(otp_user_id, otp_site_id, otp_mode, otp_destination, otp_code, otp_comment, otp_status, otp_time, otp_expire) VALUES ({$verifiedUid}, {$siteIdInt}, '{$escMode}', '{$escDest}', '{$otpCode}', '{$escComment}', 0, NOW(), DATE_ADD(NOW(), INTERVAL 10 MINUTE))";
        $insertOTP = mysqli_query($connection, $sql);

        if ($insertOTP) {
            $newOtpId = mysqli_insert_id($connection);
            $response['status'] = 'success';
            $response['msg'] = 'OTP stored successfully';
            $response['id'] = $newOtpId;
            $httpStatus = 200;
            $response['otpRef'] = $newOtpId;

            if ($rawMobile !== '') {
                $sendResult = sendOTPviaNimbus($rawMobile, $otpCode, $otpComment, $siteDisplayName);
            } else {
                $sendResult = sendOTPviaEmail($rawEmail, $otpCode, $otpComment, $siteDisplayName);
            }

            if ($sendResult !== true) {
                $response['status'] = 'warning';
                $response['msg'] = 'OTP stored but failed to send' . ((DEVELOPMENT_MODE == true) ? $sendResult : '');
                $response['error'] = $sendResult;
            }

            $response['otp'] = $otpCode;
        } else {
            $response['msg'] = 'Failed to insert OTP: ' . mysqli_error($connection);
            $httpStatus = 500;
        }
    }
} else {
    $response['msg'] = 'Invalid request method';
    $httpStatus = 405;
}

if (!isset($showOutput) || $showOutput == true) {
    http_response_code($httpStatus);
    echo json_encode($response);
}
