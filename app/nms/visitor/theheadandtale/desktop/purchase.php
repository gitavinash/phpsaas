<?php

$productInfo = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "{$order} WHERE {$order}_id='{$id}' AND {$order}_status=1 AND {$order}_type='{$order}' AND  {$order}_site_id=" . SITE_ID);

extract(mysqli_fetch_assoc($productInfo));

if (!isset($news_price)) {
    echo "Invalid product request received";
    exit();
}

$userSaltKey = base64_decode($_COOKIE['user_data']);

mysqli_query($connection, "START TRANSACTION");

if ($checkUser = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user WHERE user_salt_key='{$userSaltKey}' AND user_status=1")) {
    if (mysqli_num_rows($checkUser) != 0) {
        extract(mysqli_fetch_assoc($checkUser));
    } else {
        echo "invalid activity found by unknown users";
    }
}

$taxAmount = 0;

$totalPrice = ${$order . '_price'} + $taxAmount;

if ($checkCart = mysqli_query($connection, "SELECT cart_id FROM " . TABLE_PREFIX . "cart WHERE cart_session_id='" . SESSION_ID . "' AND cart_site_id='" . SITE_ID . "' AND cart_status!=1")) {
    if (mysqli_num_rows($checkCart) == 0) {
        $addToCart = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "cart(cart_session_id, cart_site_id, cart_user_id, cart_relation_id, cart_relation_type, cart_qty, cart_price, cart_status, cart_generated) VALUES('" . SESSION_ID . "', " . SITE_ID . ", '{$user_id}', '{$id}', '{$order}', '1', '{$totalPrice}', 2, NOW())");
    } else {
        $addToCart = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "cart SET cart_relation_id='{$id}', cart_price='{$totalPrice}' WHERE cart_session_id='" . SESSION_ID . "' AND cart_site_id='" . SITE_ID . "'");
    }
}

if ($checkOrder = mysqli_query($connection, "SELECT order_id FROM " . TABLE_PREFIX . "order WHERE order_cart_session_id='" . SESSION_ID . "' AND order_site_id='" . SITE_ID . "' AND order_status!=1")) {
    if (mysqli_num_rows($checkOrder) == 0) {
        $addNewOrder = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "order(order_site_id, order_user_id, order_cart_session_id, order_product_type, order_total_price, order_product_price, order_status, order_generate) VALUES(" . SITE_ID . ", '{$user_id}', '" . SESSION_ID . "', '{$orderType}', '{$totalPrice}', '{$totalPrice}', 0, NOW())");
        $order_id = base64_encode(mysqli_insert_id($connection));
    } else {
        extract(mysqli_fetch_assoc($checkOrder));
        $addNewOrder = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_product_price='{$totalPrice}' WHERE order_cart_session_id='" . SESSION_ID . "' AND order_site_id='" . SITE_ID . "'");
        $order_id = base64_encode($order_id);
    }
}


if ($addToCart && $addNewOrder) {
    mysqli_query($connection, "COMMIT");
    header("Location: /shipping/?id={$order_id}&order={$order}", TRUE, 301);
} else {
    mysqli_query($connection, "ROLLBACK");
    echo "Something went wrong while process to subscription";
}

exit();