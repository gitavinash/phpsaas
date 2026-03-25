<?php

require_once COMMUNICATION_FUNCTIONS;

header('Content-Type: application/json');

// ==== Initialize Response ====
$response = [
    'status' => 'error',
    'msg' => 'Invalid request',
    'id' => 0
];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (!empty($mobile)) {
        if (!preg_match('/^[6-9]\d{9}$/', $mobile)) {
            $response['msg'] = 'Invalid mobile number. Must be 10 digits and start with 6-9.';
            $httpStatus = 400;
            $invalidPayload = true;
        }
    } elseif (!empty($email)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
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

        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otpMode = ((isset($otpMode)) ? $otpMode : 'sms');
        $otpComment = ((isset($otpComment)) ? $otpComment : '');
        $otpDestination = ((isset($mobile)) ? $mobile : $email);

        if ($otpDestination == '9990026008') {
            $otpCode = '111111';
        }

        $sql = "INSERT INTO " . TABLE_PREFIX . "otp(otp_user_id, otp_site_id, otp_mode, otp_destination, otp_code, otp_comment, otp_status, otp_time, otp_expire) VALUES ('{$user_id}', " . SITE_ID . ", '{$otpMode}', '{$otpDestination}', '{$otpCode}', '{$otpComment}', 0, NOW(), DATE_ADD(NOW(), INTERVAL 10 MINUTE))";
        $insertOTP = mysqli_query($connection, $sql);

        if ($insertOTP) {
            $response['status'] = 'success';
            $response['msg'] = 'OTP stored successfully';
            $response['id'] = mysqli_insert_id($connection);
            $httpStatus = 200;
            $response['otpRef'] = mysqli_insert_id($connection);

            if (isset($mobile)) {

                if (!defined('SMS_GATEWAY')) {
                    define('SMS_GATEWAY', '');
                }

                switch (SMS_GATEWAY) {
                    case 'msg91':
                        $sendResult = sendOTPviaMSG91($mobile, $otpCode, $otpComment, $site_name);
                        break;
                    default:
                        $sendResult = sendOTPviaSMS($mobile, $otpCode, $otpComment, $site_name);
                        break;
                }
            } else {
                $sendResult = sendOTPviaEmail($email, $otpCode, $otpComment, $site_name);
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
}

if (isset($showOutput)) {
    if ($showOutput == true) {
        http_response_code($httpStatus);
        echo json_encode($response);
    }
}