<?php

require_once LOGIN_CHECK;

$applicationId = base64_decode($id);
$orderType = 'visa_application';

if ($checkApplication = mysqli_query($connection, "SELECT visa_package_id, visa_country_title, visa_application_persons, visa_package_goverment_fee, visa_package_price, visa_package_mrp, visa_application_time, visa_application_update FROM " . TABLE_PREFIX . "visa_application INNER JOIN " . TABLE_PREFIX . "visa_package ON visa_package_id=visa_application_package_id INNER JOIN " . TABLE_PREFIX . "visa_country ON visa_country_id=visa_package_country_id WHERE visa_application_id={$applicationId} AND visa_application_user_id='" . USER_ID . "' AND visa_application_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkApplication) != 0) {

        extract(mysqli_fetch_assoc($checkApplication));

        $totalPrice = ($visa_package_price + $visa_package_goverment_fee) * $visa_application_persons;

        if ($checkCart = mysqli_query($connection, "SELECT cart_id FROM " . TABLE_PREFIX . "cart WHERE cart_session_id='" . SESSION_ID . "' AND cart_site_id='" . SITE_ID . "' AND cart_status!=1")) {
            if (mysqli_num_rows($checkCart) == 0) {
                $addToCart = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "cart(cart_session_id, cart_site_id, cart_user_id, cart_relation_id, cart_relation_type, cart_qty, cart_price, cart_status, cart_generated) VALUES('" . SESSION_ID . "', " . SITE_ID . ", " . USER_ID . ", '" . $applicationId . "', '{$orderType}', '1', '{$totalPrice}', 0, NOW())");
            } else {
                $addToCart = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "cart SET cart_relation_id='{$applicationId}', cart_price='{$totalPrice}' WHERE cart_session_id='" . SESSION_ID . "' AND cart_site_id='" . SITE_ID . "'");
            }
        }

        if ($checkOrder = mysqli_query($connection, "SELECT order_id FROM " . TABLE_PREFIX . "order WHERE order_cart_session_id='" . SESSION_ID . "' AND order_site_id='" . SITE_ID . "' AND order_status!=1")) {
            if (mysqli_num_rows($checkOrder) == 0) {
                $addNewOrder = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "order(order_site_id, order_user_id, order_cart_session_id, order_product_price, order_product_id, order_product_type, order_status, order_generate, order_payment_mode) VALUES(" . SITE_ID . ", " . USER_ID . ", '" . SESSION_ID . "', '{$totalPrice}', '{$visa_package_id}', '{$orderType}', 0, NOW(), 'online')");
                $order_id = mysqli_insert_id($connection);
            } else {
                extract(mysqli_fetch_assoc($checkOrder));
                $addNewOrder = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_product_id='{$visa_package_id}', order_product_price='{$totalPrice}' WHERE order_cart_session_id='" . SESSION_ID . "' AND order_site_id='" . SITE_ID . "'");
            }
        }

        /** Start new transaction */

        $coupon = '';
        $discountValue = '';

        $startTransaction = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "transaction(transaction_site_id, transaction_user_id, transaction_order_id, transaction_method, transaction_gateway, transaction_currency, transaction_amount, transaction_coupon, transaction_coupon_amount, transaction_type, transaction_status) VALUES(" . SITE_ID . ", '" . USER_ID . "', '{$order_id}', 'online', '" . PG_NAME . "', 'INR', '{$totalPrice}', '{$coupon}', '{$discountValue}', '{$orderType}', 0)");
        $transaction_id = base64_encode(mysqli_insert_id($connection));

        $updateApplicationOrderid = mysqli_query($connection, "UPDATE {$tableNamePrefix}application SET visa_application_order_id='{$order_id}' WHERE visa_application_id='{$applicationId}' AND visa_application_site_id=" . SITE_ID);

        if ($addToCart && $addNewOrder && $startTransaction && $updateApplicationOrderid) {
            mysqli_query($connection, "COMMIT");
            $siteApp = ((isset($app)) ? $app : 'default');
            $pgRequestType = ((isset($pgRequestType)) ? $pgRequestType : 'default');
            $url = '/payment/generate/?id=' . $transaction_id . '&pg_type=order&orderType=' . $orderType . '&app=' . str_replace(' ', '', $siteApp) . '&pgRequestType=' . str_replace(' ', '', $pgRequestType);
            header("Location: {$url}", TRUE, 301);
        } else {
            mysqli_query($connection, "ROLLBACK");
            echo "Something went wrong while process to subscription";
        }
    } else {
        echo "Invalid application id";
    }
} else {
    echo ((DEVELOPMENT_MODE == true) ? mysqli_error($connection) : 'Something went wrong');
}

exit();