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

    if (isset($otp_ref_id)) {
        $otp_ref_id = base64_decode($otp_ref_id);
        if ($findOTP = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "otp WHERE otp_id = {$otp_ref_id} LIMIT 1")) {
            if (mysqli_num_rows($findOTP) > 0) {
                $otpData = mysqli_fetch_assoc($findOTP);
                $otpCode = $otpData['otp_code'];

                // Send OTP via MSG91
                $sendStatus = sendOTPviaMSG91($otpData['otp_destination'], $otpCode);

                if ($sendStatus === true) {
                    $response['status'] = 'success';
                    $response['msg'] = 'OTP re-sent successfully';
                    $httpStatus = 200;
                } else {
                    $response['status'] = 'error';
                    $response['msg'] = 'Failed to resend OTP: ' . $sendStatus;
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