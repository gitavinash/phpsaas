<?php

require_once MODULES . 'encryptor.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $AesCipher = new Encryptor();
    $response = $AesCipher->decrypt(PG_AUTH_KEY, PG_AUTH_IV, $_POST['encResponse']);

    // while ($response !== false) {
    //     $i = $i + 1;
    //     $token1 = strchr($token, "=");
    //     $token = strtok("&");
    //     $tokenValue = ltrim($token1, "=");
    //     if ($i == 4)
    //         $order_id = $tokenValue;
    //     if ($i == 12)
    //         $status = $tokenValue;
    //     if ($i == 15)
    //         $paymentTransactionID = $tokenValue;
    //     if ($i == 20)
    //         $bankTxnId = $tokenValue;
    // }

    print_r($response);

    // switch (strtolower($status)) {

    //     case 'success':
    //         $transactionStatus = 1;
    //         break;

    //     default:
    //         $transactionStatus = 3;
    //         break;
    // }

    // $order_id = str_replace('OD', '', $order_id);
    // $id = base64_encode($order_id);

    // /** Check order id is valid or not */

    // if ($checkTransaction = mysqli_query($connection, "SELECT transaction_id, transaction_cart_id, transaction_type FROM " . TABLE_PREFIX . "transaction WHERE transaction_order_id='{$order_id}'")) {

    //     if (mysqli_num_rows($checkTransaction) != 0) {

    //         extract(mysqli_fetch_assoc($checkTransaction));

    //         /** Update transaction details into transaction table */

    //         if ($updateTransaction = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "transaction SET transaction_reference_id='{$paymentTransactionID}', transaction_status=1 WHERE transaction_order_id='{$order_id}'")) {

    //             $transaction_id = base64_encode($transaction_id);

    //             if ($transactionStatus != 1) {
    //                 header("Location: /payment-failed/?id={$id}", TRUE, 301);
    //                 exit();
    //             }

    //             require_once PAYMENT_GATEWAY . 'success.php';
    //         } else {

    //             echo mysqli_error($connection);
    //         }
    //     } else {

    //         echo 'Invalid transaction id found';
    //     }
    // } else {

    //     echo mysqli_error($connection);
    //     exit();
    // }
} else {
    echo "Invalid request method";
}