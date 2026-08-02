<?php

header('Content-Type: application/json');

// ==== Initialize Response ====
$response = [
    'status' => 'error',
    'msg' => 'Invalid request',
];
$httpStatus = 400;

// ==== Validate GET Request ====
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // ✅ Sanitize inputs
    $otpRefId = isset($_GET['otp_ref_id']) ? (int)base64_decode($_GET['otp_ref_id']) : 0;
    $otpCode = isset($_GET['otp_code']) ? preg_replace('/\D/', '', $_GET['otp_code']) : '';
    $otpMode = isset($_GET['otp_mode']) ? trim($_GET['otp_mode']) : '';

    // ✅ Basic validations
    if ($otpRefId <= 0) {
        $response['msg'] = 'Invalid or missing OTP reference ID';
    } elseif (strlen($otpCode) !== 6) {
        $response['msg'] = 'Invalid OTP code';
    } elseif (!in_array($otpMode, ['sms', 'email'])) {
        $response['msg'] = 'Invalid or missing OTP mode';
    } else {
        // ✅ Check OTP in database
        $sql = "SELECT * FROM " . TABLE_PREFIX . "otp 
                WHERE otp_id = {$otpRefId} 
                  AND otp_mode = '" . mysqli_real_escape_string($connection, $otpMode) . "' 
                  AND otp_status = 0
                LIMIT 1";

        $result = mysqli_query($connection, $sql);

        if ($result && mysqli_num_rows($result) === 1) {
            $otpData = mysqli_fetch_assoc($result);

            // ✅ Validate OTP status & expiry
            $otpStatus = $otpData['otp_status'];
            $otpTime = strtotime($otpData['otp_time']);
            $otpExpire = strtotime($otpData['otp_expire']);
            $currentTime = time();

            if ($otpStatus != 0) {
                $response['msg'] = 'OTP has already been used or is invalid.';
            } elseif ($otpExpire < $currentTime) {
                $response['msg'] = 'OTP has expired. Please request a new one.';
            } elseif ($otpData['otp_code'] !== $otpCode) {
                $response['msg'] = 'Incorrect OTP.';
            } else {
                // ✅ OTP verified successfully
                $update = "DELETE FROM " . TABLE_PREFIX . "otp WHERE otp_id = {$otpRefId}";
                mysqli_query($connection, $update);

                $response['status'] = 'success';
                $response['msg'] = 'OTP verified successfully';
                $response['userInfo'] = $otpData['otp_destination'];
                $httpStatus = 200;

                $user_id = $otpData['otp_user_id'];
                $mobile = $otpData['otp_destination'];

                require_once 'generate-token-by-mobile.php';

                $response['existingUser'] = ((isset($existingUser) && $existingUser) ? $existingUser : false);
            }
        } else {
            $response['msg'] = 'OTP record not found';
        }
    }
} else {
    $response['msg'] = 'Invalid request method';
}

// ==== Output Response ====
http_response_code($httpStatus);
echo json_encode($response);
