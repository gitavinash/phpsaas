<?php

$redirectUrl = SITE_URL . '/payment/status/?transactionId=' . base64_encode($order_id);
$callbackUrl = SITE_URL . '/payment/status/?transactionId=' . base64_encode($order_id);

$requestBody = json_encode(array(
    'merchantId' => PG_MERCHANT_ID,
    'merchantTransactionId' => $order_id,
    'merchantUserId' => $userId,
    'amount' => $order_total_price * 100,
    'redirectUrl' => $redirectUrl,
    'redirectMode' => 'POST',
    'callbackUrl' => $callbackUrl,
    'mobileNumber' => $profile_mobile,
    'paymentInstrument' => array(
        'type' => 'PAY_PAGE'
    ),
), TRUE);

$encodeRequestBody = base64_encode($requestBody);

$xVerify = hash('sha256', $encodeRequestBody . '/pg/v1/pay' . PG_SALT_KEY) . '###' . PG_SALT_INDEX;

// Sent request to PhonePe Server to get redirectURL 

$postRequestBody = json_encode(array(
    'request' => $encodeRequestBody
), TRUE);

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api-preprod.phonepe.com/apis/merchant-simulator/pg/v1/pay",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => $postRequestBody,
    CURLOPT_HTTPHEADER => array(
        "X-verify: $xVerify",
        "Content-Type: application/json"
    ),
));

$response = curl_exec($curl);
curl_close($curl);

$response = json_decode($response, TRUE);

print_r($response);

$clientRedirectUrl = $response['data']['instrumentResponse']['redirectInfo']['url'];

header("Location: " . $clientRedirectUrl, TRUE, 301);
exit();