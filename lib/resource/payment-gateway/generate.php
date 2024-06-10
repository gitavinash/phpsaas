<?php

/** Check request is valid or not */

if (isset($id)) {

    $id = base64_decode($id);

    $orderType = ((isset($orderType)) ? $orderType : 'product');

    switch ($orderType) {

        case 'link':
            $query = $query = "SELECT order_user_id as userId, order_id, order_total_price, cart_currency, profile_mobile, profile_email, profile_display_name FROM " . TABLE_PREFIX . "cart INNER JOIN " . TABLE_PREFIX . "transaction INNER JOIN " . TABLE_PREFIX . "order ON transaction_order_id=order_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=transaction_user_id WHERE transaction_id='{$id}' AND order_cart_session_id=cart_session_id AND cart_status=0";
            $cartProduct = 'Payment link';
            break;

        case 'booking':
            $query = "SELECT order_user_id as userId, order_id, order_total_price, cart_currency, profile_mobile, profile_email, profile_display_name FROM " . TABLE_PREFIX . "cart INNER JOIN " . TABLE_PREFIX . "transaction INNER JOIN " . TABLE_PREFIX . "order ON transaction_order_id=order_id INNER JOIN " . TABLE_PREFIX . "{$orderType} ON cart_relation_id={$orderType}_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=transaction_user_id WHERE transaction_id='{$id}' AND transaction_site_id=" . SITE_ID . " AND order_cart_session_id=cart_session_id";
            $cartProduct = 'Online booking';
            break;

        default:
            $query = "SELECT order_user_id as userId, order_id, order_total_price, cart_currency, profile_mobile, profile_email, profile_display_name, GROUP_CONCAT({$orderType}_title, ',') as cartProduct FROM " . TABLE_PREFIX . "cart INNER JOIN " . TABLE_PREFIX . "transaction INNER JOIN " . TABLE_PREFIX . "order ON transaction_order_id=order_id INNER JOIN " . TABLE_PREFIX . "{$orderType} ON cart_relation_id={$orderType}_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=transaction_user_id WHERE transaction_id='{$id}' AND transaction_site_id=" . SITE_ID . " AND order_cart_session_id=cart_session_id";
            break;
    }

    if ($checkStatus = mysqli_query($connection, $query)) {

        if (mysqli_num_rows($checkStatus) != 0) {

            extract(mysqli_fetch_assoc($checkStatus));
            if ($checkOrderMeta = mysqli_query($connection, "SELECT order_meta_key, order_meta_value FROM " . TABLE_PREFIX . "order_meta WHERE order_relation_id='{$order_id}' AND order_meta_status=1")) {

                if (mysqli_num_rows($checkOrderMeta) != 0) {

                    $orderMetas = array();

                    while ($orderMeta = mysqli_fetch_assoc($checkOrderMeta)) {

                        extract($orderMeta);

                        $orderMetas[$order_meta_key] = $order_meta_value;
                    }

                    extract($orderMetas);
                }
            }

            $paymentAmount = $order_total_price + ((isset($deliveryFees)) ? $deliveryFees : 0);

            $paymentAmount = str_replace(",", "", number_format($paymentAmount, 2));
            $cartProduct = rtrim($cartProduct, ",");

            $pgFile = PAYMENT_GATEWAY . PG_NAME . '/' . ((isset($urlStructure[1])) ? $urlStructure[1] : 'generate') . '.php';

            if (file_exists($pgFile)) {

                require_once $pgFile;
            } else {

                require_once SYSTEM_THEME . 'payment-gateway-file-missing.php';
            }
        } else {

            $msg = 'Invalid Transactional ID';
        }
    } else {

        $msg = 'Something is wrong while fetching order details';
    }
} else {

    $msg = 'No transaction id found';
}