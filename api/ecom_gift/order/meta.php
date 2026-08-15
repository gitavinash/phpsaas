<?php

function meta($connection, $data)
{

    extract($data);

    $order_id = base64_decode($order_id);
    mysqli_query($connection, "START TRANSACTION");

    // Always update cart status to 2 for this session
    $updateCart = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "cart SET cart_status=2 WHERE cart_session_id='" . SESSION_ID . "' AND (cart_status=0 OR cart_status=2) AND cart_site_id='" . SITE_ID . "'");

    $allMetasOk = true;

    foreach ($data as $order_meta_key => $order_meta_value) {
        $meta_filter = array('order_id', 'type', 'action', 'deliverySlot');

        if (!in_array($order_meta_key, $meta_filter) && !empty($order_meta_value)) {
            $escapedValue = mysqli_real_escape_string($connection, $order_meta_value);
            $orderMetaQuery = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "order_meta(order_relation_id, order_site_id, order_meta_key, order_meta_value, order_meta_status) VALUES('{$order_id}', " . SITE_ID . ", '{$order_meta_key}', '{$escapedValue}', 1)");
            if (!$orderMetaQuery) {
                $allMetasOk = false;
            }
        }

        if ($order_meta_key == 'deliverySlot' && !empty($order_meta_value)) {
            $deliverySlotArr = explode('_', $order_meta_value);
            $deliveryFees = isset($deliverySlotArr[1]) ? (float)$deliverySlotArr[1] : 0;
            $deliverySlot = $deliverySlotArr[0];

            $deliverySlotOrderQuery = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_delivery_charges='{$deliveryFees}' WHERE order_id='{$order_id}' AND order_status=0");
            $deliverySlotOrderMetaQuery = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "order_meta(order_relation_id, order_site_id, order_meta_key, order_meta_value, order_meta_status) VALUES('{$order_id}', " . SITE_ID . ",'deliverySlot', '$deliverySlot', 1)");

            if (!$deliverySlotOrderQuery || !$deliverySlotOrderMetaQuery) {
                $allMetasOk = false;
            }
        }
    }

    if ($updateCart && $allMetasOk) {
        mysqli_query($connection, "COMMIT");
        $status = 200;
        $msg = 'Order meta data saved successfully';
    } else {
        mysqli_query($connection, "ROLLBACK");
        $msg = 'Error while saving order meta data';
        $status = 500;
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
        'redirect' => '/shipping/?id=' . base64_encode($order_id)
    );

    echo json_encode($result, TRUE);
}