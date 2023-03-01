<?php

class orderModule
{

    function generatePaymentLinkOrder($connection, $user_id, $currency, $amount)
    {

        $redirectUrl = '';

        /** Create new order */

        mysqli_query($connection, "START TRANSACTION");

        $addNewOrder = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "order(order_site_id, order_user_id, order_cart_session_id, order_total_price, order_status, order_generate) VALUES(" . SITE_ID . ", '{$user_id}', '" . SESSION_ID . "', '{$amount}', 0, NOW())");
        $orderId = mysqli_insert_id($connection);

        $initiateTransaction = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "transaction(transaction_site_id, transaction_user_id, transaction_order_id, transaction_method, transaction_currency, transaction_amount, transaction_type, transaction_status) VALUES(" . SITE_ID . ", '{$user_id}', '{$orderId}', 'online', '{$currency}', '{$amount}', 'link', 0)");
        $transaction_id = base64_encode(mysqli_insert_id($connection));

        if ($addNewOrder && $initiateTransaction) {

            mysqli_query($connection, "COMMIT");
            $redirectUrl = '/payment/generate/?id=' . $transaction_id . '&pg_type=link';
        } else {
            mysqli_query($connection, "ROLLBACK");
        }

        return $redirectUrl;
    }
}