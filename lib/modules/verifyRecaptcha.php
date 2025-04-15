<?php
function verifyRecaptchaToken($token, $secretKey)
{
    $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';

    $data = [
        'secret' => $secretKey,
        'response' => $token
    ];

    $options = [
        'http' => [
            'method' => 'POST',
            'content' => http_build_query($data),
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n"
        ]
    ];

    $context = stream_context_create($options);
    $verifyResponse = file_get_contents($verifyUrl, false, $context);
    $responseKeys = json_decode($verifyResponse, true);

    return $responseKeys;
}
?>