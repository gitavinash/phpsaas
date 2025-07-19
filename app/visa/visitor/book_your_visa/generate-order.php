<?php

$redirect_url = ((isset($redirect_url)) ? $redirect_url : '/manage-subscription/');

$productInfo = mysqli_query($connection, "SELECT inventory_price, inventory_tax_status, inventory_tax_rate FROM " . TABLE_PREFIX . "inventory WHERE inventory_relation_id='{$id}' AND inventory_status=1 AND inventory_type='product' AND  inventory_site_id=" . SITE_ID);

extract(mysqli_fetch_assoc($productInfo));

if (!isset($inventory_price)) {
    echo "Invalid product request received";
    exit();
}

if (isset($_COOKIE['user_data'])) {

    $userSaltKey = base64_decode($_COOKIE['user_data']);

    mysqli_query($connection, "START TRANSACTION");

    if ($checkUser = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user WHERE user_salt_key='{$userSaltKey}' AND user_status=1")) {
        if (mysqli_num_rows($checkUser) != 0) {
            extract(mysqli_fetch_assoc($checkUser));
        } else {
            echo "invalid activity found by unknown users";
        }
    }

    if ($inventory_tax_status == 1) {
        $taxAmount = number_format((float)($inventory_tax_rate / 100) * $inventory_price, 2, '.', '');
    } else {
        $taxAmount = 0;
    }

    $totalPrice = $inventory_price + $taxAmount;

    if ($checkCart = mysqli_query($connection, "SELECT cart_id FROM " . TABLE_PREFIX . "cart WHERE cart_session_id='" . SESSION_ID . "' AND cart_site_id='" . SITE_ID . "' AND cart_status!=1")) {
        if (mysqli_num_rows($checkCart) == 0) {
            $addToCart = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "cart(cart_session_id, cart_site_id, cart_user_id, cart_relation_id, cart_relation_type, cart_qty, cart_price, cart_status, cart_generated) VALUES('" . SESSION_ID . "', " . SITE_ID . ", '{$user_id}', '{$id}', 'product', '1', '{$totalPrice}', 2, NOW())");
        } else {
            $addToCart = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "cart SET cart_relation_id='{$id}', cart_price='{$totalPrice}' WHERE cart_session_id='" . SESSION_ID . "' AND cart_site_id='" . SITE_ID . "'");
        }
    }

    if ($checkOrder = mysqli_query($connection, "SELECT order_id FROM " . TABLE_PREFIX . "order WHERE order_cart_session_id='" . SESSION_ID . "' AND order_site_id='" . SITE_ID . "' AND order_status!=1")) {
        if (mysqli_num_rows($checkOrder) == 0) {
            $addNewOrder = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "order(order_site_id, order_user_id, order_cart_session_id, order_product_price, order_status, order_generate) VALUES(" . SITE_ID . ", '{$user_id}', '" . SESSION_ID . "', '{$totalPrice}', 0, NOW())");
            $order_id = base64_encode(mysqli_insert_id($connection));
        } else {
            extract(mysqli_fetch_assoc($checkOrder));
            $addNewOrder = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_product_price='{$totalPrice}' WHERE order_cart_session_id='" . SESSION_ID . "' AND order_site_id='" . SITE_ID . "'");
            $order_id = base64_encode($order_id);
        }
    }

    if ($addToCart && $addNewOrder) {
        mysqli_query($connection, "COMMIT");
        $curlRedirectUrl = "/shipping/?id={$order_id}&redirect_url={$redirect_url}";
        header("Location: {$curlRedirectUrl}", TRUE, 301);
    } else {
        mysqli_query($connection, "ROLLBACK");
        echo "Something went wrong while process to subscription";
    }
} else {
    header("Location: /login/?redirect_url=" . $redirect_url . '&method=post&id=' . $id . '&baseUrl=/subscription/', TRUE, 301);
}

exit();