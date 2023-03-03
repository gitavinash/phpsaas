<?php

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $response = explode('|', $_POST['msg']);

    $order_id = $response[3];
    $worldlineTransactionID = $response[5];

    switch ($response[0]) {

        case '0300':
            $transactionStatus = 1;
            break;

        case '0399':
            $transactionStatus = 3;
            break;

        default:
            $transactionStatus = 2;
            break;
    }

    $order_id = str_replace('OD', '', $order_id);
    $id = base64_encode($order_id);

    /** Check order id is valid or not */

    if ($checkTransaction = mysqli_query($connection, "SELECT transaction_id, transaction_cart_id, transaction_type FROM " . TABLE_PREFIX . "transaction WHERE transaction_order_id='{$order_id}'")) {

        if (mysqli_num_rows($checkTransaction) != 0) {

            extract(mysqli_fetch_assoc($checkTransaction));

            /** Update transaction details into transaction table */

            if ($updateTransaction = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "transaction SET transaction_reference_id='{$worldlineTransactionID}', transaction_status=1 WHERE transaction_order_id='{$order_id}'")) {

                $transaction_id = base64_encode($transaction_id);

                if ($transactionStatus != 1) {
                    header("Location: /payment-failed/?id={$id}", TRUE, 301);
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
} else {
    echo "Invalid request method";
}