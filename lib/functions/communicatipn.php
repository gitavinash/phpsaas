<?php

function sendOTPviaMSG91($mobile, $otpCode, $name = 'User', $site_name = 'Aleya.in')
{
    $url = "https://control.msg91.com/api/v5/flow/";

    $name = (($name != '') ? $name : 'User');

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

    $result    = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
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

function sendOTPviaEmail($email, $otpCode, $name = 'User', $site_name = 'Aleya.in')
{
    $name = (($name != '') ? $name : 'User');
    $email = trim((string)$email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Invalid email address: ' . $email;
    }

    $url = defined('DEFAULT_SMTP_HOST')
        ? DEFAULT_SMTP_HOST
        : 'https://api.zeptomail.in/v1.1/email/template';

    $templateKey = defined('ZEPTO_TEMPLATE_KEY')
        ? ZEPTO_TEMPLATE_KEY
        : '2518b.79db608369f04f8.k1.84d65520-8675-11f0-b801-62df313bf14d.199007f6b72';

    $apiKey = defined('DEFAULT_SMTP_API_KEY') ? DEFAULT_SMTP_API_KEY : 'Zoho-enczapikey PHtE6r0IRenjiGUs8hEGsaPtF86gZtssqO00LwgVtt1CW/YKHk0Eqt4olDK/rkgvUaQXFfKYzIs9ub+e5eyGdzrsMTkZWmqyqK3sx/VYSPOZsbq6x00YtV8cf0fcUIbrd99r1yHWvNjeNA==';

    $headers = [
        'accept: application/json',
        'content-type: application/json',
        'authorization: ' . $apiKey,
        'cache-control: no-cache',
    ];

    $payload = [
        "mail_template_key" => $templateKey,
        "from" => [
            "address" => defined('DEFAULT_SMTP_EMAIL') ? DEFAULT_SMTP_EMAIL : 'alerts@blumox.com',
            "name"    => defined('DEFAULT_SMTP_NAME') ? DEFAULT_SMTP_NAME : 'Blumox Technologies'
        ],
        "to" => [[
            "email_address" => [
                "address" => $email,
                "name"    => $name
            ]
        ]],
        "merge_info" => [
            "site_name" => ucwords($site_name),
            "name"      => $name,
            "OTP"       => (string)$otpCode
        ]
    ];

    $json = json_encode($payload, JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        return 'JSON Encode Error: ' . json_last_error_msg();
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

    $result    = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return 'cURL Error: ' . $curlError;
    }

    if ($httpCode != 201) {
        return 'SMTP API Error: ' . $result;
    }

    return true;
}