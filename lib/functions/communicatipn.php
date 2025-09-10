<?php

function sendOTPviaSMS($mobile, $otpCode, $name = 'User', $site_name = 'Aleya.in'): bool
{
    $smsUserName = defined('SMS_USERNAME') ? SMS_USERNAME : 'blumoxsms';
    $smsPassword = defined('SMS_PASSWORD') ? SMS_PASSWORD : '123456';
    $smsHeader   = defined('SMS_HEADER')   ? SMS_HEADER   : 'BLUMOX';

    $message = "Your OTP is {$otpCode}. Please use this code to verify your account. Do not share this code with anyone. - Blumox";

    $queryParams = http_build_query([
        'user'     => $smsUserName,
        'pass'     => $smsPassword,
        'sender'   => $smsHeader,
        'phone'    => $mobile,
        'text'     => $message,
        'priority' => 'ndnd',
        'stype'    => 'normal',
    ], '', '&', PHP_QUERY_RFC3986);

    $url = "https://bhash.notify360.in/api/sendmsg.php?{$queryParams}";

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST       => 'GET',            // force GET
        CURLOPT_NOBODY              => false,            // MUST be false (true => HEAD)
        CURLOPT_RETURNTRANSFER      => false,            // don't buffer
        CURLOPT_HEADER              => false,            // ignore headers
        CURLOPT_HTTPHEADER          => ['Connection: close'],
        CURLOPT_HTTP_VERSION        => CURL_HTTP_VERSION_1_0, // simple close semantics
        CURLOPT_IPRESOLVE           => CURL_IPRESOLVE_V4,     // avoid IPv6 delays
        CURLOPT_CONNECTTIMEOUT_MS   => 400,              // enough to actually connect
        CURLOPT_TIMEOUT_MS          => 1200,             // short, but lets request be sent
        // Discard any body ASAP (don’t block on reading)
        CURLOPT_WRITEFUNCTION       => static function ($ch, $data) {
            return strlen($data);
        },
    ]);
    @curl_exec($ch); // fire the GET; we don't care about the response
    @curl_close($ch);
    return true;
}



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

    $url = defined('SMTP_API_TEMPLATE_HOST')
        ? SMTP_API_TEMPLATE_HOST
        : 'https://api.zeptomail.in/v1.1/email/template';

    $templateKey = defined('SMTP_OTP_TEMPLATE')
        ? SMTP_OTP_TEMPLATE
        : '2518b.79db608369f04f8.k1.84d65520-8675-11f0-b801-62df313bf14d.199007f6b72';

    $apiKey = defined('SMTP_API_KEY') ? SMTP_API_KEY : 'Zoho-enczapikey PHtE6r0IRenjiGUs8hEGsaPtF86gZtssqO00LwgVtt1CW/YKHk0Eqt4olDK/rkgvUaQXFfKYzIs9ub+e5eyGdzrsMTkZWmqyqK3sx/VYSPOZsbq6x00YtV8cf0fcUIbrd99r1yHWvNjeNA==';

    $headers = [
        'accept: application/json',
        'content-type: application/json',
        'authorization: ' . $apiKey,
        'cache-control: no-cache',
    ];

    $payload = [
        "mail_template_key" => $templateKey,
        "from" => [
            "address" => defined('EMAIL_SMTP_EMAIL') ? EMAIL_SMTP_EMAIL : 'alerts@blumox.com',
            "name"    => defined('EMAIL_SMTP_NAME') ? EMAIL_SMTP_NAME : 'Blumox Technologies'
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