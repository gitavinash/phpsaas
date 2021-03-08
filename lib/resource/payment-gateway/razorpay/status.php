<?php

    require_once 'razorpay.php';
    use Razorpay\Api\Api;

    $api = new Api($pg_key_id, $pg_key_secret);

    if(isset($pay_id)) {

        $transactionID=base64_decode($txn_id);
        $orderID=base64_decode($id);
        $paymentID=base64_decode($pay_id);

        /** Check order id is valid or not */

        if($checkTransaction=mysqli_query($connection, "SELECT transaction_id, transaction_order_id, transaction_type FROM ".TABLE_PREFIX."transaction WHERE transaction_reference_id='{$transactionID}' AND transaction_site_id=".SITE_ID)) {

            if(mysqli_num_rows($checkTransaction)!=0) {

                extract(mysqli_fetch_assoc($checkTransaction));

                $transactionStatus=$api->payment->fetch($paymentID);

                /** Update transaction details into transaction table */

                if($updateTransaction=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."transaction SET transaction_payment_id='{$paymentID}', transaction_payment_mode='".strtolower($transactionStatus['method'])."', transaction_status=1 WHERE transaction_reference_id='{$transactionID}'")) {

                    $transaction_id=base64_encode($transaction_id);
                    require_once PAYMENT_GATEWAY.'success.php';

                    header("Location: /thank-you/?id={$id}", TRUE, 301);

                } else {

                    echo mysqli_error($connection);

                }

            } else {

                // echo 'Invalid transaction id found';
                echo "SELECT transaction_id, transaction_order_id, transaction_type FROM ".TABLE_PREFIX."transaction WHERE transaction_reference_id='{$orderID}' AND transaction_site_id=".SITE_ID;

            }

        }


    } else {

        header("Location: /payment-failed/?id={$id}", TRUE, 301);
        exit();

    }

?>