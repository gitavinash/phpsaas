<?php

class orderModule
{

    function generatePaymentLinkOrder($connection, $user_id, $currency, $amount)
    {

        $redirectUrl = '';

        /** Create new order */

        mysqli_query($connection, "START TRANSACTION");

        $userData = mysqli_query($connection, "SELECT profile_display_name, profile_email, profile_mobile FROM " . TABLE_PREFIX . "profile WHERE profile_user_id='{$user_id}'");

        if (mysqli_num_rows($userData) != 0) {
            extract(mysqli_fetch_assoc($userData));

            $addNewOrder = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "order(order_site_id, order_user_id, order_cart_session_id, order_total_price, order_status, order_generate) VALUES(" . SITE_ID . ", '{$user_id}', '" . SESSION_ID . "', '{$amount}', 0, NOW())");
            $orderId = mysqli_insert_id($connection);

            $createNewCart = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "cart(cart_session_id, cart_site_id, cart_relation_id, cart_relation_type, cart_qty, cart_price, cart_status, cart_generated) VALUES('" . SESSION_ID . "', " . SITE_ID . ", '{$orderId}', 'link', '1', '{$amount}', 0, NOW())");

            $initiateTransaction = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "transaction(transaction_site_id, transaction_user_id, transaction_order_id, transaction_method, transaction_gateway, transaction_currency, transaction_amount, transaction_type, transaction_status) VALUES(" . SITE_ID . ", '{$user_id}', '{$orderId}', 'online', '" . PG_NAME . "', '{$currency}', '{$amount}', 'link', 0)");
            $transaction_id = base64_encode(mysqli_insert_id($connection));

            if ($addNewOrder && $createNewCart && $initiateTransaction) {

                mysqli_query($connection, "COMMIT");
                $redirectUrl = SITE_URL . '/payment/generate/?id=' . $transaction_id . '&pg_type=link&orderType=link';
                session_destroy();
                session_start();
            } else {
                mysqli_query($connection, "ROLLBACK");
            }
        }

        return $redirectUrl;
    }
}