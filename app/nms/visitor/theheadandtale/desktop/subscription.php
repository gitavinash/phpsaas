<?php

    $productInfo=mysqli_query($connection, "SELECT inventory_price FROM ".TABLE_PREFIX."inventory WHERE inventory_relation_id='{$id}' AND inventory_status=1 AND inventory_type='product' AND  inventory_site_id=".SITE_ID);

    extract(mysqli_fetch_assoc($productInfo));

    if(!isset($inventory_price)) {
        echo "Invalid product request received";
        exit();
    }
    
    $userSaltKey = base64_decode($_COOKIE['user_data']);

    mysqli_query($connection, "START TRANSATION");

    if($checkUser=mysqli_query($connection, "SELECT user_id FROM ".TABLE_PREFIX."user WHERE user_salt_key='{$userSaltKey}' AND user_status=1")) {
        if(mysqli_num_rows($checkUser)!=0) {
            extract(mysqli_fetch_assoc($checkUser));
        } else {
            echo "invalid activity found by unknown users";
        }
    }

    $taxAmount = number_format((float)(18 / 100) * $inventory_price, 2, '.', '');
    $totalPrice=$inventory_price+$taxAmount;

    $addToCart=mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "cart(cart_session_id, cart_site_id, cart_user_id, cart_relation_id, cart_relation_type, cart_qty, cart_price, cart_status, cart_generated) VALUES('" . SESSION_ID . "', " . SITE_ID . ", '{$user_id}', '{$id}', 'product', '1', '{$totalPrice}', 2, NOW())");


    $addNewOrder=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."order(order_site_id, order_user_id, order_cart_session_id, order_product_price, order_status, order_generate) VALUES(".SITE_ID.", '{$user_id}', '".SESSION_ID."', '{$totalPrice}', 0, NOW())");
    $order_id=base64_encode(mysqli_insert_id($connection));


    if($addToCart && $addNewOrder) {
        mysqli_query($connection, "COMMIT");
        header("Location: /shipping/?id={$order_id}", TRUE, 301);
    } else {
        mysqli_query($connection, "ROLLBACK");
        echo "Something went wrong while process to subscription";
    }

    exit();