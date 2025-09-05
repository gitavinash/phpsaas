<?php

/** Check request is valid or not */

if (isset($id)) {

    $id = base64_decode($id);

    $orderType = ((isset($orderType)) ? $orderType : 'product');

    switch ($orderType) {

        case 'link':
            $query = "SELECT order_user_id as userId, order_id, order_total_price, cart_currency, profile_mobile, profile_email, profile_display_name FROM " . TABLE_PREFIX . "cart INNER JOIN " . TABLE_PREFIX . "transaction INNER JOIN " . TABLE_PREFIX . "order ON transaction_order_id=order_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=transaction_user_id WHERE transaction_id='{$id}' AND order_cart_session_id=cart_session_id AND cart_status=0 ORDER BY cart_updated DESC LIMIT 0,1";
            $cartProduct = 'Payment link';
            break;

        case 'enquiry':
            $query = "SELECT order_user_id as userId, order_id, order_total_price, cart_currency, profile_mobile, profile_email, profile_display_name FROM " . TABLE_PREFIX . "cart INNER JOIN " . TABLE_PREFIX . "transaction INNER JOIN " . TABLE_PREFIX . "order ON transaction_order_id=order_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=transaction_user_id WHERE transaction_id='{$id}' AND order_cart_session_id=cart_session_id AND cart_status=0 ORDER BY cart_updated DESC LIMIT 0,1";
            $cartProduct = '';
            break;

        case 'booking':
            $query = "SELECT order_user_id as userId, order_id, order_total_price, cart_currency, profile_mobile, profile_email, profile_display_name FROM " . TABLE_PREFIX . "cart INNER JOIN " . TABLE_PREFIX . "transaction INNER JOIN " . TABLE_PREFIX . "order ON transaction_order_id=order_id INNER JOIN " . TABLE_PREFIX . "{$orderType} ON cart_relation_id={$orderType}_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=transaction_user_id WHERE transaction_id='{$id}' AND transaction_site_id=" . SITE_ID . " AND order_cart_session_id=cart_session_id ORDER BY cart_updated DESC LIMIT 0,1";
            $cartProduct = 'Online booking';
            break;

        case 'visa_application':
            $query = "SELECT
                        transaction_method,
                        transaction_amount as order_total_price,
                        transaction_currency,
                        transaction_user_id as userId,
                        order_id,
                        order_cart_session_id,
                        visa_application_id,
                        cart_relation_id,
                        cart_price,
                        cart_currency,
                        profile_mobile,
                        profile_email,
                        profile_display_name,
                        GROUP_CONCAT(CONCAT(visa_country_title, ' - ', visa_package_title) SEPARATOR ', ') AS cartProduct
                    FROM
                        isw_transaction
                    INNER JOIN isw_order ON order_id = transaction_order_id AND transaction_type = order_product_type AND order_site_id = " . SITE_ID . "
                    INNER JOIN isw_cart ON cart_session_id=order_cart_session_id AND cart_site_id=" . SITE_ID . "
                    INNER JOIN isw_profile ON profile_user_id=transaction_user_id
                    INNER JOIN isw_visa_application ON cart_relation_id=visa_application_id AND visa_application_site_id=" . SITE_ID . "
                    INNER JOIN isw_visa_package ON visa_package_id=visa_application_package_id AND visa_package_site_id=" . SITE_ID . "
                    INNER JOIN isw_visa_country ON visa_country_id=visa_package_country_id AND visa_country_site_id=" . SITE_ID . "
                    WHERE
                        transaction_id = {$id} AND transaction_site_id = " . SITE_ID;
            break;

        default:
            $query = "SELECT order_user_id as userId, order_id, order_total_price, cart_currency, profile_mobile, profile_email, profile_display_name, GROUP_CONCAT({$orderType}_title, ',') as cartProduct FROM " . TABLE_PREFIX . "cart INNER JOIN " . TABLE_PREFIX . "transaction INNER JOIN " . TABLE_PREFIX . "order ON transaction_order_id=order_id INNER JOIN " . TABLE_PREFIX . "{$orderType} ON cart_relation_id={$orderType}_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=transaction_user_id WHERE transaction_id='{$id}' AND transaction_site_id=" . SITE_ID . " AND order_cart_session_id=cart_session_id ORDER BY cart_updated DESC LIMIT 0,1";
            break;
    }

    echo $query;
    exit();

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
            } else {
                $msg = "Something went wrong while updating the order status";
            }

            $paymentAmount = (float)$order_total_price + ((isset($deliveryFees)) ? (float)$deliveryFees : 0);

            $paymentAmount = str_replace(",", "", number_format($paymentAmount, 2));

            $cartProduct = rtrim($cartProduct, ",");

            $pgFile = PAYMENT_GATEWAY . PG_NAME . '/' . ((isset($urlStructure[1])) ? $urlStructure[1] : 'generate') . '.php';

            if (file_exists($pgFile)) {

                require_once $pgFile;
                exit();
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

echo $msg;
exit();