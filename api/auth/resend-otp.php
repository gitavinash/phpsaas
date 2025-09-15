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
        $otp_ref_id = base64_decode($otp_ref_id);
        if ($findOTP = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "otp WHERE otp_id = {$otp_ref_id} LIMIT 1")) {
            if (mysqli_num_rows($findOTP) > 0) {
                $otpData = mysqli_fetch_assoc($findOTP);
                $otpCode = $otpData['otp_code'];
                $otpComment = $otpData['otp_comment'];

                switch ($otp_mode) {
                    case 'sms';
                        if (!defined('SMS_GATEWAY')) {
                            define('SMS_GATEWAY', '');
                        }

                        switch (SMS_GATEWAY) {
                            case 'msg91':
                                $sendStatus = sendOTPviaMSG91($otpData['otp_destination'], $otpCode, $otpComment, $site_name);
                                break;
                            default:
                                $sendStatus = sendOTPviaNimbus($otpData['otp_destination'], $otpCode, $otpComment, $site_name);
                                break;
                        }
                        break;
                    default:
                        $sendStatus = sendOTPviaEmail($otpData['otp_destination'], $otpCode, $otpComment, $site_domain);
                        break;
                }

                if ($sendStatus === true) {
                    $response['status'] = 'success';
                    $response['msg'] = 'OTP re-sent successfully';
                    $httpStatus = 200;
                } else {
                    $response['status'] = 'error';
                    $response['msg'] = 'Failed to resend OTP: '  . ((DEVELOPMENT_MODE == true) ? $sendStatus : '');;
                    $httpStatus = 500;
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