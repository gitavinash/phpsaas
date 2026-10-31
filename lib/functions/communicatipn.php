<?php

function sendOTPviaNimbus($mobile, $otpCode, $name = 'User', $site_name = 'Aleya.in'): bool
{
    $smsUserName = defined('SMS_USERNAME') ? SMS_USERNAME : 'blumoxbiz';
    $smsPassword = defined('SMS_PASSWORD') ? SMS_PASSWORD : 'nvb!f1pY';
    $smsHeader   = defined('SMS_HEADER')   ? SMS_HEADER   : 'BLUMOX';

    $message = "Your OTP is {$otpCode}. Please use this code to verify your account. Do not share this code with anyone. - Blumox";

    $queryParams = http_build_query([
        'UserID'     => $smsUserName,
        'Password'     => $smsPassword,
        'SenderID'   => $smsHeader,
        'Phno'    => $mobile,
        'Msg'     => $message,
        'EntityID' => '1201159479739325882',
        'TemplateID'    => '1607100000000316341',
    ], '', '&', PHP_QUERY_RFC3986);

    $url = "http://nimbusit.biz/api/SmsApi/SendSingleApi?{$queryParams}";

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
        CURLOPT_CONNECTTIMEOUT_MS   => 2000,              // enough to actually connect
        CURLOPT_TIMEOUT_MS          => 5000,             // short, but lets request be sent
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
    $templateId = defined('MSG91_TEMPLATE_ID') ? MSG91_TEMPLATE_ID : '68214b65d6fc051e341d60c2';
    $authKey    = defined('MSG91_AUTH_KEY')    ? MSG91_AUTH_KEY    : '450705AN4fMGODY6821a622P1';

    $cleanMobile = preg_replace('/\D/', '', (string)$mobile);
    if (strlen($cleanMobile) === 12 && substr($cleanMobile, 0, 2) === '91') {
        $cleanMobile = substr($cleanMobile, 2);
    } elseif (strlen($cleanMobile) === 13 && substr($cleanMobile, 0, 3) === '091') {
        $cleanMobile = substr($cleanMobile, 3);
    } elseif (strlen($cleanMobile) === 11 && substr($cleanMobile, 0, 1) === '0') {
        $cleanMobile = substr($cleanMobile, 1);
    }

    $payload = [
        "template_id" => $templateId,
        "recipients" => [
            [
                "mobiles" => "91" . $cleanMobile,
                "var1" => (string)$otpCode
            ]
        ]
    ];

    $headers = [
        'accept: application/json',
        'content-type: application/json',
        'authkey: ' . $authKey,
        'Connection: close',
        'Expect:'
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL               => $url,
        CURLOPT_POST              => true,
        CURLOPT_POSTFIELDS        => json_encode($payload, JSON_UNESCAPED_SLASHES),
        CURLOPT_RETURNTRANSFER    => true,
        CURLOPT_HTTPHEADER        => $headers,
        CURLOPT_IPRESOLVE         => CURL_IPRESOLVE_V4,      // Avoid IPv6 DNS resolution delays
        CURLOPT_TCP_NODELAY       => 1,                      // Send TCP packets immediately without Nagle buffering
        CURLOPT_HTTP_VERSION      => CURL_HTTP_VERSION_1_1,  // Reliable fast HTTP/1.1
        CURLOPT_CONNECTTIMEOUT_MS => 1500,                   // 1.5s connection timeout
        CURLOPT_TIMEOUT_MS        => 3000,                   // 3.0s total execution timeout
        CURLOPT_DNS_CACHE_TIMEOUT => 300,                    // Cache DNS lookup for 5 minutes
        CURLOPT_NOSIGNAL          => 1                       // Millisecond timeout safety in PHP
    ]);

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

    $respData = json_decode((string)$result, true);
    if (isset($respData['type']) && strtolower($respData['type']) === 'error') {
        return 'MSG91 API Error: ' . ($respData['message'] ?? $result);
    }

    return true;
}
function sendOTPviaEmail($email, $otpCode, $name = 'User', $site_name = 'Blumox Technologies')
{
    $name = (($name != '') ? $name : 'User');
    $email = trim((string)$email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Invalid email address: ' . $email;
    }

    $apiKey = defined('SMTP_API_KEY') ? SMTP_API_KEY : 'Zoho-enczapikey PHtE6r1eEensjTJ+9xNV7aW4Fs/2NY14r+xvKQIU4YhCW6MAGk1Ro9kumje3/kgtB/kRQPKTy9k7sumUsuiNdmjvPD4dXWqyqK3sx/VYSPOZsbq6x00Zs1gSdkzaV4PrctNu3SDfv9+X';
    $fromAddress = defined('EMAIL_SMTP_EMAIL') ? EMAIL_SMTP_EMAIL : 'alerts@blumox.com';
    $fromName = defined('EMAIL_SMTP_NAME') ? EMAIL_SMTP_NAME : (defined('SITE_NAME') ? SITE_NAME : 'Blumox Technologies');

    // Build custom HTML email template based on current UI
    $htmlEmailFile = dirname(__DIR__) . '/resource/email/otp-verification.php';
    $htmlContent = '';
    if (file_exists($htmlEmailFile)) {
        ob_start();
        include $htmlEmailFile;
        ob_get_clean();
        $htmlContent = $email_body ?? '';
    }

    $subject = "Your Login Verification Code: {$otpCode} - {$site_name}";

    // Direct Custom HTML API dispatch via Zeptomail (without Zepto templates)
    $headers = [
        'accept: application/json',
        'content-type: application/json',
        'authorization: ' . $apiKey,
        'cache-control: no-cache',
    ];

    $htmlUrl = 'https://api.zeptomail.in/v1.1/email';
    $htmlPayload = [
        "from" => [
            "address" => $fromAddress,
            "name"    => $fromName
        ],
        "to" => [[
            "email_address" => [
                "address" => $email,
                "name"    => $name
            ]
        ]],
        "subject"  => $subject,
        "htmlbody" => $htmlContent
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $htmlUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($htmlPayload, JSON_UNESCAPED_SLASHES));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return 'cURL Error: ' . $curlError;
    }

    if ($httpCode != 200 && $httpCode != 201) {
        return 'Zeptomail API Error: ' . $result;
    }

    return true;
}

function sendNewsLetterVerficationLinkviaEmail($email)
{
    $email = trim((string)$email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Invalid email address: ' . $email;
    }

    // Generate a verification link

    $payload = [
        'email'   => strtolower(trim($email)),
        'purpose' => 'email_verify',
        'exp'     => time() + VERIFY_TTL // 10 minutes expiry
    ];

    $json    = json_encode($payload, JSON_UNESCAPED_SLASHES);
    $data    = base64_encode($json);

    $sig = hash_hmac('sha256', $data, EMAIL_VERIFY_SECRET);

    $verificationURL = SITE_URL . '/verify/newsletter/'
        . '?data=' . urlencode($data)
        . '&sig='  . urlencode($sig);

    $url = defined('SMTP_API_TEMPLATE_HOST')
        ? SMTP_API_TEMPLATE_HOST
        : 'https://api.zeptomail.in/v1.1/email/template';

    $templateKey = defined('SMTP_NEWSLETTER_VERIFICATION_TEMPLATE')
        ? SMTP_NEWSLETTER_VERIFICATION_TEMPLATE
        : '2518b.5ed70a1e2b45b697.k1.f3fe18c0-dae4-11f0-91a1-621740bce2a6.19b29da544c';

    $apiKey = defined('SMTP_API_KEY') ? SMTP_API_KEY : 'Zoho-enczapikey PHtE6r1eEensjTJ+9xNV7aW4Fs/2NY14r+xvKQIU4YhCW6MAGk1Ro9kumje3/kgtB/kRQPKTy9k7sumUsuiNdmjvPD4dXWqyqK3sx/VYSPOZsbq6x00Zs1gSdkzaV4PrctNu3SDfv9+X';

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
            ]
        ]],
        "merge_info" => [
            "verification_link" => $verificationURL,
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

    if ($httpCode != 200 && $httpCode != 201) {
        return 'SMTP API Error: ' . $result;
    }

    return true;
}
