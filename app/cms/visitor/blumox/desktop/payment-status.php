<?php

$curl = curl_init();

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
$paymentResponse = json_decode($response, TRUE);
if ($paymentResponse['order_status'] == 'PAID') {
    $paymentResponse['customer_details']['customer_name'];
    $paymentResponse['order_amount'];
    $redirectionUrl = "/thank-you/?name=" . $paymentResponse['customer_details']['customer_name'];
    header("Location: {$redirectionUrl}", TRUE, 301);
} else {
    $paymentResponse['customer_details']['customer_name'];
    echo "fdf";
    $redirectionUrl = "/payment-failed/?name=" + $paymentResponse['customer_details']['customer_name'];
    header("Location: {$redirectionUrl}", TRUE, 301);
}
exit();
