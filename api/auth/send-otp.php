<?php

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

    // ✅ Mobile number validation
    if (!preg_match('/^[6-9]\d{9}$/', $mobile)) {
        $response['msg'] = 'Invalid mobile number. Must be 10 digits and start with 6-9.';
        $httpStatus = 422; // Unprocessable Entity
    } else {
        // ✅ Generate 6-digit OTP
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // ✅ Insert OTP into DB using mysqli
        $sql = "INSERT INTO " . TABLE_PREFIX . "otp(otp_user_id, otp_site_id, otp_mode, otp_destination, otp_code, otp_status, otp_time) VALUES ('{$user_id}', " . SITE_ID . ", 'sms', '{$mobile}', '{$otpCode}', 0, NOW())";
        $insertOTP = mysqli_query($connection, $sql);

        if ($insertOTP) {
            $response['status'] = 'success';
            $response['msg'] = 'OTP stored successfully';
            $response['id'] = mysqli_insert_id($connection);
            $httpStatus = 200;
            $response['otpRef'] = mysqli_insert_id($connection);

            // ✅ Send OTP via MSG91
            $sendResult = sendOTPviaMSG91($mobile, $otpCode);

            if ($sendResult !== true) {
                $response['status'] = 'warning';
                $response['msg'] = 'OTP stored but failed to send SMS';
                $response['error'] = $sendResult;
            }

            // ✅ For Testing: Show OTP (Remove in Production)
            $response['otp'] = $otpCode;
        } else {
            $response['msg'] = 'Failed to insert OTP: ' . mysqli_error($connection);
            $httpStatus = 500;
        }
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
}

// ========================================
// ✅ Function to Send OTP via MSG91 API
function sendOTPviaMSG91($mobile, $otpCode)
{
    $url = "https://control.msg91.com/api/v5/flow/";

    $payload = [
        "template_id" => MSG91_TEMPLATE_ID,
        "recipients" => [
            [
                "mobiles" => "91" . $mobile,
                "var1" => $otpCode
            ]
        ]
    ];

    $headers = [
        'accept: application/json',
        'content-type: application/json',
        'authkey: ' . MSG91_AUTH_KEY
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);

    curl_close($ch);

    if ($curlError) {
        return 'cURL Error: ' . $curlError;
    }

    if ($httpCode != 200) {
        return 'MSG91 API Error: ' . $result;
    }

    return true;
}