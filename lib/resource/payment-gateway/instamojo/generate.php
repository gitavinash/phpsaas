<?php

/** Start payment gateway code */

$ch = curl_init();

if (DEVELOPMENT_MODE == TRUE) {

    curl_setopt($ch, CURLOPT_URL, 'https://test.instamojo.com/api/1.1/payment-requests/');
} else {

    curl_setopt($ch, CURLOPT_URL, 'https://www.instamojo.com/api/1.1/payment-requests/');
}

curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt(
    $ch,
    CURLOPT_HTTPHEADER,
    array(
        "X-Api-Key: " . PG_API_KEY,
        "X-Auth-Token: " . PG_AUTH_TOKEN
    )
);
$payload = array(
    'purpose' => $cartProduct,
    'amount' => $paymentAmount,
    'phone' => "{$profile_mobile}",
    'buyer_name' => $profile_display_name,
    'redirect_url' => SITE_URL . '/payment/status/',
    'send_email' => false,
    'send_sms' => false,
    'email' => $profile_email,
    'allow_repeated_payments' => false
);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
$response = curl_exec($ch);
curl_close($ch);

$response = json_decode($response, TRUE);

if ($response['success'] == 1) {

    if ($updateTransaction = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "transaction SET transaction_reference_id='{$response['payment_request']['id']}', transaction_status=2 WHERE transaction_id='{$id}' AND transaction_site_id=" . SITE_ID)) {

        header("Location: {$response['payment_request']['longurl']}", TRUE, 301);
        exit();
    } else {

        $msg = 'Something is wrong while generation transaction status';
    }
} else {

    print_r($response['message']);
}