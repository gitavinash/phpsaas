<?php

if (isset($_COOKIE['user_data'])) {
    $userSaltKey = base64_decode($_COOKIE['user_data']);

    mysqli_query($connection, "START TRANSACTION");

    if ($checkUser = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user WHERE user_salt_key='{$userSaltKey}'")) {
        if (mysqli_num_rows($checkUser) != 0) {
            extract(mysqli_fetch_assoc($checkUser));
        } else {
            echo "invalid activity found by unknown users";
            exit();
        }
    }

    function get_price_per_seat(int $seats): int
    {
        if ($seats >= 51) {
            return 1799;
        }
        if ($seats >= 26) {
            return 2299;
        }
        return 2699;
    }

    $minSeats = 10;
    $requestedSeats = max($minSeats, (int)($_GET['num_users'] ?? $minSeats));
    $pricePerSeat = get_price_per_seat($requestedSeats);
    $calculatedTotal = $requestedSeats * $pricePerSeat;

    mysqli_query($connection, "START TRANSACTION");

    if ($checkCart = mysqli_query($connection, "SELECT cart_id FROM " . TABLE_PREFIX . "cart WHERE cart_session_id='" . SESSION_ID . "' AND cart_site_id='" . SITE_ID . "' AND cart_status!=1")) {
        if (mysqli_num_rows($checkCart) == 0) {
            $addToCart = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "cart(cart_session_id, cart_site_id, cart_user_id, cart_relation_id, cart_relation_type, cart_qty, cart_price, cart_status, cart_generated) VALUES('" . SESSION_ID . "', " . SITE_ID . ", '{$user_id}', '0', 'order', {$requestedSeats}, '{$calculatedTotal}', 2, NOW())");
        } else {
            $addToCart = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "cart SET cart_relation_id='0', cart_price='{$calculatedTotal}', cart_qty='{$requestedSeats}' WHERE cart_relation_type='order' AND  cart_session_id='" . SESSION_ID . "' AND cart_site_id='" . SITE_ID . "'");
        }
    }

    if ($checkOrder = mysqli_query($connection, "SELECT order_id FROM " . TABLE_PREFIX . "order WHERE order_cart_session_id='" . SESSION_ID . "' AND order_site_id='" . SITE_ID . "' AND order_status!=1")) {
        if (mysqli_num_rows($checkOrder) == 0) {
            $addNewOrder = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "order(order_site_id, order_user_id, order_cart_session_id, order_product_price, order_product_type, order_status, order_generate, order_msg) VALUES(" . SITE_ID . ", '{$user_id}', '" . SESSION_ID . "', '{$calculatedTotal}', 'order', 0, NOW(), '{$requestedSeats}')");
            $order_id = base64_encode(mysqli_insert_id($connection));
        } else {
            extract(mysqli_fetch_assoc($checkOrder));
            $addNewOrder = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_product_price='{$calculatedTotal}', order_msg='{$requestedSeats}' WHERE order_product_type='order' AND order_cart_session_id='" . SESSION_ID . "' AND order_site_id='" . SITE_ID . "'");
            $order_id = base64_encode($order_id);
        }
    }

    if ($addToCart && $addNewOrder) {
        mysqli_query($connection, "COMMIT");
        $curlRedirectUrl = "/procced-to-pg/?id={$order_id}&redirect_url=/corporate-subscription-status/";
        header("Location: {$curlRedirectUrl}", TRUE, 301);
    } else {
        mysqli_query($connection, "ROLLBACK");
        echo "Something went wrong while process to subscription";
    }
} else {
    echo "<h1>Please login to continue.</h1>";
}

exit();