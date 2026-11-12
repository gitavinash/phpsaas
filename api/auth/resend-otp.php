<?php

require_once COMMUNICATION_FUNCTIONS;

header('Content-Type: application/json');

// ==== Initialize Response ====
$response = [
    'status' => 'error',
    'msg' => 'Invalid request',
    'id' => 0
];
$httpStatus = 400;

// ==== Validate GET Request ====
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (isset($otp_ref_id)) {
        $otpRefIdInt = (int) base64_decode((string) $otp_ref_id);
        $siteIdInt = (int) SITE_ID;
        if ($otpRefIdInt <= 0) {
            $response['status'] = 'error';
            $response['msg'] = 'Invalid OTP reference ID';
            $httpStatus = 400;
        } elseif ($findOTP = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "otp WHERE otp_id = {$otpRefIdInt} AND (otp_site_id = {$siteIdInt} OR otp_site_id = 0) AND otp_status = 0 LIMIT 1")) {
            if (mysqli_num_rows($findOTP) > 0) {
                $otpData = mysqli_fetch_assoc($findOTP);
                $modeReq = isset($otp_mode) && trim((string) $otp_mode) !== ''
                    ? strtolower(trim((string) $otp_mode))
                    : strtolower(trim((string) ($otpData['otp_mode'] ?? 'email')));

                if (mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "otp SET otp_expire=DATE_ADD(NOW(), INTERVAL 10 MINUTE) WHERE otp_id = {$otpRefIdInt} AND (otp_site_id = {$siteIdInt} OR otp_site_id = 0)")) {
                    $otpCode = $otpData['otp_code'];
                    $otpComment = $otpData['otp_comment'];
                    $siteDisplayName = isset($site_name) && $site_name !== '' ? (string) $site_name : (defined('SITE_NAME') ? (string) SITE_NAME : 'Blumox');
                    $siteDomainLabel = isset($site_domain) && $site_domain !== '' ? (string) $site_domain : (defined('SITE_DOMAIN') ? (string) SITE_DOMAIN : $siteDisplayName);

                    switch ($modeReq) {
                        case 'sms':
                            $sendStatus = sendOTPviaNimbus($otpData['otp_destination'], $otpCode, $otpComment, $siteDisplayName);
                            break;
                        default:
                            $sendStatus = sendOTPviaEmail($otpData['otp_destination'], $otpCode, $otpComment, $siteDomainLabel);
                            break;
                    }

                    if ($sendStatus === true) {
                        $response['status'] = 'success';
                        $response['msg'] = 'OTP re-sent successfully';
                        $httpStatus = 200;
                    } else {
                        $response['status'] = 'error';
                        $response['msg'] = 'Failed to resend OTP: ' . ((DEVELOPMENT_MODE == true) ? $sendStatus : '');
                        $httpStatus = 500;
                    }
                } else {
                    $response['msg'] = ((DEVELOPMENT_MODE == true) ? mysqli_error($connection) : 'Unable to regerate OTP');
                }
            } else {
                $response['status'] = 'error';
                $response['msg'] = 'Invalid OTP reference ID';
                $httpStatus = 400;
            }
        } else {
            $response['status'] = 'error';
            $response['msg'] = 'Database query failed';
            $httpStatus = 500;
        }
    } else {
        $response['status'] = 'warning';
        $response['msg'] = 'No otp_ref_id provided';
        $httpStatus = 400;
    }
} else {
    $response['msg'] = 'Invalid request method';
    $httpStatus = 405;
}

// ==== Output Response ====
if (isset($showOutput)) {
    if ($showOutput == true) {
        http_response_code($httpStatus);
        echo json_encode($response);
    }
} else {
    http_response_code($httpStatus);
    echo json_encode($response);
}