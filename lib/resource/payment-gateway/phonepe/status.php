<?php

$orderId = base64_decode($_GET['transactionId']);
$id = base64_encode($orderId);
$transactionStatus = 3;

if (DEVELOPMENT_MODE == true) {
    $phonePeEndPoint = 'https://api-preprod.phonepe.com/apis/merchant-simulator/pg/v1/status/' . PG_MERCHANT_ID . "/" . $orderId;
} else {
    $phonePeEndPoint = 'https://api.phonepe.com/apis/hermes/pg/v1/status/' . PG_MERCHANT_ID . "/" . $orderId;
}

$xVerify = hash('sha256', "/pg/v1/status/" . PG_MERCHANT_ID . "/{$orderId}" . PG_SALT_KEY) . '###' . PG_SALT_INDEX;

// Sent request to PhonePe Server to get payment status 

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => $phonePeEndPoint,
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

$response = json_decode($response, TRUE);

if ($response['code'] == 'PAYMENT_SUCCESS') {
    $transactionStatus = 1;
}

$paymentMethod = $response['data']['paymentInstrument']['type'];
$bankTxnId = $response['data']['paymentInstrument']['pgServiceTransactionId'];
$paymentTransactionID = $response['data']['paymentInstrument']['pgTransactionId'];

/** Check order id is valid or not */

if ($checkTransaction = mysqli_query($connection, "SELECT transaction_id, transaction_cart_id, transaction_type FROM " . TABLE_PREFIX . "transaction WHERE transaction_order_id='{$orderId}'")) {

    if (mysqli_num_rows($checkTransaction) != 0) {

        extract(mysqli_fetch_assoc($checkTransaction));

        /** Update transaction details into transaction table */

        if ($updateTransaction = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "transaction SET transaction_payment_id='{$paymentTransactionID}', transaction_reference_id = '{$bankTxnId}', transaction_payment_mode='{$paymentMethod}', transaction_status=1 WHERE transaction_order_id='{$orderId}'")) {

            $transaction_id = base64_encode($transaction_id);

            if ($transactionStatus != 1) {
                header("Location: /payment-failed/?id=" . base64_encode($orderId), TRUE, 301);
                exit();
            }

            require_once PAYMENT_GATEWAY . 'success.php';
        } else {

            echo mysqli_error($connection);
        }
    } else {

        echo 'Invalid transaction id found';
    }
} else {

    echo mysqli_error($connection);
    exit();
}