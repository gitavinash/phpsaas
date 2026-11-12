<?php

function generate($connection, $data)
{

    extract($data);

    /** Get Product from cart */

    if ($checkOrderStatus = mysqli_query($connection, "SELECT order_delivery_charges FROM " . TABLE_PREFIX . "order WHERE order_id='{$order}' AND order_status=0")) {

        if (mysqli_num_rows($checkOrderStatus) != 0) {

            extract(mysqli_fetch_assoc($checkOrderStatus));

            $cartSummarySql = "SELECT
                    GROUP_CONCAT(c.cart_relation_id SEPARATOR ', ') as product,
                    GROUP_CONCAT(GREATEST(c.cart_qty, 1) SEPARATOR ', ') as quantity,
                    GROUP_CONCAT(COALESCE(NULLIF(CAST(c.cart_price AS DECIMAL(12,2)), 0), NULLIF(i.inventory_price, 0), NULLIF(i.inventory_mrp, 0), 0) SEPARATOR ', ') as price,
                    SUM(COALESCE(NULLIF(CAST(c.cart_price AS DECIMAL(12,2)), 0), NULLIF(i.inventory_price, 0), NULLIF(i.inventory_mrp, 0), 0) * GREATEST(c.cart_qty, 1)) as totalPrice,
                    COALESCE(MAX(NULLIF(c.cart_currency, '')), 'INR') as cart_currency,
                    MAX(c.cart_user_id) as user_id
                FROM " . TABLE_PREFIX . "cart c
                LEFT JOIN " . TABLE_PREFIX . "inventory i
                    ON i.inventory_relation_id = c.cart_relation_id
                   AND i.inventory_site_id = " . SITE_ID . "
                   AND ((c.cart_relation_type = 'product' AND i.inventory_type = 'product') OR (c.cart_relation_type = 'event' AND i.inventory_type = 'event'))
                WHERE c.cart_session_id = '" . SESSION_ID . "'
                  AND c.cart_status = 2
                  AND c.cart_user_id != 0
                  AND c.cart_site_id = " . SITE_ID;

            if ($checkCart = mysqli_query($connection, $cartSummarySql)) {

                $cartRow = mysqli_fetch_assoc($checkCart);
                if (!empty($cartRow) && !empty($cartRow['product'])) {

                    extract($cartRow);
                    $cart_currency = !empty($cart_currency) ? $cart_currency : 'INR';
                    $totalPrice = (float) ($totalPrice ?? 0);

                    if ($paymentMode == 'cash') {

                        $deliveryCharges = 50;
                    } else {

                        $deliveryCharges = (($order_delivery_charges != '') ? $order_delivery_charges : 0);
                    }

                    $totalPrice = $totalPrice + $deliveryCharges;

                    if ($paymentMode != '') {

                        if ($updateCart = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_product_id='{$product}', order_product_qty='{$quantity}', order_product_price='{$price}', order_delivery_charges='{$deliveryCharges}', order_total_price='{$totalPrice}', order_coupon='{$coupon}', order_payment_mode='{$paymentMode}' WHERE order_id='{$order}' AND order_cart_session_id='" . SESSION_ID . "' AND order_site_id=" . SITE_ID)) {

                            /** Start new transaction */

                            if ($startTransaction = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "transaction(transaction_site_id, transaction_user_id, transaction_order_id, transaction_method, transaction_gateway, transaction_currency, transaction_amount, transaction_type, transaction_status) VALUES(" . SITE_ID . ", '{$user_id}', '{$order}', '{$paymentMode}', '" . PG_NAME . "', '{$cart_currency}', '{$totalPrice}', 'product', 0)")) {

                                $status = 200;
                                $msg = 'Redirect to payment mode';
                                $transaction_id = base64_encode(mysqli_insert_id($connection));
                                if ($paymentMode == 'cash') {

                                    $url = '/thank-you/?id=' . $transaction_id;
                                } else {

                                    $url = '/payment/generate/?id=' . $transaction_id . '&pg_type=order';
                                }
                            }
                        }
                    } else {

                        $msg = 'Please select payment mode';
                    }
                } else {

                    $msg = 'Cart is empty or something is wrong';
                }
            }
        } else {

            $msg = 'Something is wrong while trying to create a new order';
        }
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
        'url' => ((isset($url)) ? $url : '#')
    );

    echo json_encode($result, TRUE);
}