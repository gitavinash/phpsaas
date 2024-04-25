<?php

function generate($connection, $data)
{

    extract($data);

    $coupon = ((isset($coupon)) ? $coupon : '');
    $orderType = ((isset($orderType)) ? $orderType : 'product');
    $discountValue = 0;

    /** Get Product from cart */

    if ($checkOrderStatus = mysqli_query($connection, "SELECT order_id, order_user_id, order_product_id, order_product_type, order_product_qty, order_delivery_charges, profile_first_name, profile_email, profile_mobile as mobile FROM " . TABLE_PREFIX . "order INNER JOIN " . TABLE_PREFIX . "profile ON order_user_id=profile_user_id WHERE order_id='{$order}' AND order_status=0 AND order_site_id=".SITE_ID)) {

        if (mysqli_num_rows($checkOrderStatus) != 0) {

            extract(mysqli_fetch_assoc($checkOrderStatus));

            $orderProductID = str_replace(' ', '', explode(',', $order_product_id));
            $productID = '';

            for ($i = 0; $i < sizeof($orderProductID); $i++) {

                if ($i == 0) {

                    $productID = $productID . "{$order_product_type}_id='{$orderProductID[$i]}'";
                } else {

                    $productID = $productID . " OR {$order_product_type}_id='{$orderProductID[$i]}'";
                }
            }

            /** Fetch product details  */

            if ($productDetails = mysqli_query($connection, "SELECT GROUP_CONCAT({$order_product_type}_title) as product_title, GROUP_CONCAT({$order_product_type}_image) as productImage FROM ".TABLE_PREFIX."_$order_product_type WHERE {$order_product_type}_type'='{$orderType}' AND {$productID}")) {

                $product_title = '';

                if (mysqli_num_rows($productDetails) != 0) {

                    extract(mysqli_fetch_assoc($productDetails));
                }
            }

            $enableLimit = '';

            if(SITE_ID==49){
                $enableLimit == "LIMIT 0, 1";
            }

            if ($checkCart = mysqli_query($connection, "SELECT GROUP_CONCAT(cart_relation_id SEPARATOR ', ') as product, GROUP_CONCAT(cart_qty SEPARATOR ', ') as quantity, GROUP_CONCAT(cart_price SEPARATOR ', ') as price, SUM(cart_price) as totalPrice, cart_currency, cart_user_id as user_id FROM isw_cart WHERE cart_session_id='" . SESSION_ID . "' AND cart_status=2 AND cart_user_id!=0 AND cart_site_id=" . SITE_ID." ORDER BY cart_updated DESC {$enableLimit}")) {

                if (mysqli_num_rows($checkCart) != 0) {

                    extract(mysqli_fetch_assoc($checkCart));
                    $deliveryCharges = (($order_delivery_charges != '') ? $order_delivery_charges : 0);

                    /** Check coupon */

                    if ($coupon != '') {
                        require_once MODULES . 'validateCoupon.php';
                    } else {

                        $couponCodeIsValid = true;
                    }

                    /** Continue if coupon code is valid or not in used */

                    if ($couponCodeIsValid) {

                        $totalPrice = ($totalPrice + $deliveryCharges) - $discountValue;
                        $totalPrice = str_replace(',', '', number_format($totalPrice, 2));

                        if ($paymentMode != '') {

                            if ($updateCart = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_product_id='{$product}', order_product_qty='{$quantity}', order_product_price='{$price}', order_delivery_charges='{$deliveryCharges}', order_total_price='{$totalPrice}', order_coupon='{$coupon}', order_payment_mode='{$paymentMode}' WHERE order_id='{$order}' AND order_cart_session_id='" . SESSION_ID . "' AND order_site_id=" . SITE_ID)) {

                                /** Start new transaction */

                                if ($startTransaction = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "transaction(transaction_site_id, transaction_user_id, transaction_order_id, transaction_method, transaction_gateway, transaction_currency, transaction_amount, transaction_coupon, transaction_coupon_amount, transaction_type, transaction_status) VALUES(" . SITE_ID . ", '{$order_user_id}', '{$order}', '{$paymentMode}', '" . PG_NAME . "', '{$cart_currency}', '{$totalPrice}', '{$coupon}', '{$discountValue}', '$orderType', 0)")) {

                                    $status = 200;
                                    $msg = 'Redirect to payment mode';
                                    $transaction_id = base64_encode(mysqli_insert_id($connection));

                                    if ($paymentMode == 'cash') {

                                        // /** Send SMS */
                                        // require_once APP_SMS_TEMPLATE . 'order/cod.php';

                                        // /** Send Email */
                                        // require_once APP_EMAIL_TEMPLATE . 'order/cod.php';

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

                        $msg = 'Coupon code is invalid';
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