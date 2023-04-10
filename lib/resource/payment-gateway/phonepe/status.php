<?php

$orderId = base64_decode($_GET['transactionId']);

$xVerify = hash('sha256', "/pg/v1/status/" . PG_MERCHANT_ID . "/{$orderId}" . PG_SALT_KEY) . '###' . PG_SALT_INDEX;


// Sent request to PhonePe Server to get payment status 


$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api-preprod.phonepe.com/apis/merchant-simulator/pg/v1/status/" . PG_MERCHANT_ID . "/" . $orderId,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        "X-verify: $xVerify",
        "X-merchant-id: " . PG_MERCHANT_ID
    ),
));

$response = curl_exec($curl);
curl_close($curl);

print_r($response);

// header("Location: " . $clientRedirectUrl, TRUE, 301);
// exit();