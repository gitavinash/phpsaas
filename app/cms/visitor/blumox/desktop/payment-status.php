<?php

// require_once './lib/resource/payment-gateway/cashfree/generate.php';

$curl = curl_init();

echo $orderId;
exit();

if (DEVELOPMENT_MODE == TRUE) {

    $requestUrl = "https://sandbox.cashfree.com/pg/orders/{$orderId}";
} else {

    $requestUrl = "https://api.cashfree.com/pg/orders/{$orderId}";
}

curl_setopt_array($curl, [
    CURLOPT_URL => $requestUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_HTTPHEADER => [
        "accept: application/json",
        "content-type: application/json",
        "x-api-version: " . PG_API_VERSION,
        "x-client-id: " . PG_APP_ID,
        "x-client-secret: " . PG_SECRET_KEY
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

print_r($response);

exit();
