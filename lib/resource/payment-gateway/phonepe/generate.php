<?php

if (DEVELOPMENT_MODE == true) {
    $phonePeEndPoint = 'https://api-preprod.phonepe.com/apis/merchant-simulator/pg/v1/pay';
} else {
    $phonePeEndPoint = 'https://api.phonepe.com/apis/hermes/pg/v1/pay';
}

$redirectUrl = ((isset($redirectUrl)) ? $redirectUrl : SITE_URL . '/payment/status/?transactionId=' . base64_encode($order_id));
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
    CURLOPT_URL => $phonePeEndPoint,
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

if ($response['code'] == 'PAYMENT_INITIATED') {

    $clientRedirectUrl = $response['data']['instrumentResponse']['redirectInfo']['url'];
    header("Location: " . $clientRedirectUrl, TRUE, 301);
} else {
    echo "Something went wrong while redirection to payment gateway.";
    if (DEVELOPMENT_MODE == true) {
        echo "Encode request body : " . $encodeRequestBody . '<br />';
        echo "xVerify request body : " . $xVerify;
        print_r($response);
    }
}

exit();