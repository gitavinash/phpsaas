<?php

$transactionID = base64_decode($txn_id);
$orderId = base64_decode($id);
$transactionStatus = 3;


/** Check order id is valid or not */

mysqli_query($connection, "START TRANSACTION");

if($checkTransaction=mysqli_query($connection, "SELECT transaction_id, transaction_order_id, transaction_cart_id, transaction_type FROM ".TABLE_PREFIX."transaction WHERE transaction_id={$transactionID} AND transaction_order_id={$orderId} AND transaction_site_id=".SITE_ID)) {

    if(mysqli_num_rows($checkTransaction)!=0) {
        extract(mysqli_fetch_assoc($checkTransaction));

        if($_POST['responseCode']==000) {
            $transactionStatus=1;
        }

        $paymentMode = strtolower((($_POST['paymentMode']) ? $_POST['paymentMode'] : 'net banking'));
        $paymentId = strtolower($_POST['paymentID']);
        $paymentReferenceId = strtolower($_POST['txnID']);
        $paymentDetails = (($_POST['paymentInstId']) ? strtolower($_POST['paymentInstId']) : '');

        switch(strtolower($_POST['paymentSubInstType'])) {
            case 'dc';
                $paymentMethod = 'debit card';
                break;
            case 'cc';
                $paymentMethod = 'debit card';
                break;
            default;
                $paymentMethod = strtolower($_POST['paymentSubInstType']);
                break;
        }

        $updateTransaction=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."transaction SET transaction_reference_id='{$paymentReferenceId}', transaction_payment_id='{$paymentID}', transaction_payment_mode='{$paymentMode}', transaction_status={$transactionStatus} WHERE transaction_id='{$transactionID}' AND transaction_order_id='{$orderId}' AND transaction_site_id=".SITE_ID);

        $updateOrderStatus = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_status=1 WHERE order_id='{$orderId}'");

        $updateCartStatus = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "cart SET cart_status=1 WHERE cart_session_id='".SESSION_ID."'");

        if($updateTransaction && $updateOrderStatus && $updateCartStatus) {
            mysqli_query($connection, "COMMIT");
        } else {
            mysqli_query($connection, "ROLLBACK");
        }

        if($transactionStatus==1) {
            header("Location: /thank-you/?txn_id={$txn_id}", TRUE, 301);
        } else {
            header("Location: /error/?txn_id={$txn_id}", TRUE, 301);
        }

    } else {
        echo "Invalid transaction found";
    }
}

exit();