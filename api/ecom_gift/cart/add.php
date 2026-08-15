<?php

function add($connection, $data)
{

    extract($data);

    $itemType = mysqli_real_escape_string($connection, ((isset($itemType)) ? $itemType : 'product'));

    // Check in product table first
    if ($checkProduct = mysqli_query($connection, "SELECT product_title as productName, product_thumbnail as productImage, inventory_mrp, inventory_price FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id WHERE product_id='{$id}' AND product_status=1 AND link_type='product' AND link_status=200 AND link_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkProduct) != 0) {
            extract(mysqli_fetch_assoc($checkProduct));
            $relationType = 'product';
        }
    }

    // If not found in product table, check in event table
    if (!isset($relationType)) {
        if ($checkEvent = mysqli_query($connection, "SELECT event_title as productName, COALESCE(NULLIF(event_thumbnail, ''), event_image) as productImage, inventory_mrp, inventory_price FROM " . TABLE_PREFIX . "event INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=event_id LEFT JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=event_id WHERE event_id='{$id}' AND event_status=1 AND link_type='event' AND link_status=200 AND link_site_id=" . SITE_ID)) {
            if (mysqli_num_rows($checkEvent) != 0) {
                extract(mysqli_fetch_assoc($checkEvent));
                $relationType = 'event';
            }
        }
    }

    if (isset($relationType)) {
        if (SITE_ID != 7) {
            $price = ((isset($inventory_price) && $inventory_price != 0) ? $inventory_price : (isset($inventory_mrp) ? $inventory_mrp : $price));
        }

        if ($addToCart = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "cart(cart_session_id, cart_site_id, cart_relation_id, cart_relation_type, cart_qty, cart_price, cart_status, cart_generated) VALUES('" . SESSION_ID . "', " . SITE_ID . ", '{$id}', '{$relationType}', '{$quantity}', '{$price}', 0, NOW())")) {

            $status = 200;
            $msg = ucfirst($relationType) . ' add to cart is successful';
        } else {

            $msg = mysqli_error($connection);
        }
    } else {

        $msg = 'Invalid response found';
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
        'productName' => ((isset($productName)) ? $productName : ''),
        'productImage' => ((isset($productImage)) ? $productImage : ''),
        'productQty' => ((isset($quantity)) ? $quantity : 0),
        'relationType' => ((isset($relationType)) ? $relationType : 'product')
    );

    echo json_encode($result, TRUE);
}