<?php

if (!isset($id)) {
    echo 'Something went wrong';
    exit();
}

$orderId = base64_decode($id);

if ($checkDetails = mysqli_query($connection, "SELECT order_cart_session_id, order_total_price, order_currency, cart_relation_type FROM " . TABLE_PREFIX . "order INNER JOIN " . TABLE_PREFIX . "cart ON cart_session_id=order_cart_session_id WHERE order_id='{$orderId}' AND order_product_type='order' AND order_status=0 AND order_site_id=" . SITE_ID . " ORDER BY cart_updated DESC")) {
    if (mysqli_num_rows($checkDetails) != 0) {
        extract(mysqli_fetch_assoc($checkDetails));

        $cartQuery = "SELECT * FROM " . TABLE_PREFIX . "cart INNER JOIN " . TABLE_PREFIX . "order ON cart_session_id= order_cart_session_id WHERE cart_relation_type='order' AND order_product_type='order' AND cart_site_id=" . SITE_ID . " ORDER BY cart_updated DESC";

        if ($cartInfo = mysqli_query($connection, $cartQuery)) {
            if (mysqli_num_rows($cartInfo) == 0) {
                if (DEVELOPMENT_MODE == true) {
                    echo $cartQuery;
                } else {
                    echo "<h1>Cart is empty</h1>";
                }
            } else {
                extract(mysqli_fetch_assoc($cartInfo));
                $inventory_currency_symbol = '₹';
                $subTotal = $order_product_price;
                $inventory_tax_status = 0;
                $orderType = 'order';
                $coupon = '';
                $discountValue = 0;
                $paymentMode = 'online';

                if ($checkOrderStatus = mysqli_query($connection, "SELECT order_id, order_user_id, order_product_id, order_product_type, order_product_qty, order_delivery_charges, profile_first_name, profile_email, profile_mobile as mobile FROM " . TABLE_PREFIX . "order INNER JOIN " . TABLE_PREFIX . "profile ON order_user_id=profile_user_id WHERE order_id='{$orderId}' AND order_status=0 AND order_site_id=" . SITE_ID)) {

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

                        /** Fetch product details */

                        $product_title = 'Corporate Subscription';

                        $enableLimit = '';

                        if (SITE_ID == 49) {
                            $enableLimit == "LIMIT 0, 1";
                        }

                        if ($checkCart = mysqli_query($connection, "SELECT GROUP_CONCAT(cart_relation_id SEPARATOR ', ') as product, GROUP_CONCAT(cart_qty SEPARATOR ', ') as quantity, GROUP_CONCAT(cart_price SEPARATOR ', ') as price, SUM(cart_price) as totalPrice, cart_currency, cart_user_id as user_id FROM isw_cart WHERE cart_session_id='" . SESSION_ID . "' AND cart_status=2 AND cart_user_id!=0 AND cart_site_id=" . SITE_ID . " ORDER BY cart_updated DESC {$enableLimit}")) {

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

                                        if ($updateCart = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_product_id='{$product}', order_product_qty='{$quantity}', order_product_price='{$price}', order_delivery_charges='{$deliveryCharges}', order_total_price='{$totalPrice}', order_coupon='{$coupon}', order_payment_mode='{$paymentMode}' WHERE order_id='{$orderId}' AND order_cart_session_id='" . SESSION_ID . "' AND order_site_id=" . SITE_ID)) {

                                            /** Start new transaction */

                                            if ($startTransaction = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "transaction(transaction_site_id, transaction_user_id, transaction_order_id, transaction_method, transaction_gateway, transaction_currency, transaction_amount, transaction_coupon, transaction_coupon_amount, transaction_type, transaction_status) VALUES(" . SITE_ID . ", '{$order_user_id}', '{$orderId}', '{$paymentMode}', '" . PG_NAME . "', '{$cart_currency}', '{$totalPrice}', '{$coupon}', '{$discountValue}', '$orderType', 0)")) {

                                                $status = 200;
                                                $transaction_id = base64_encode(mysqli_insert_id($connection));

                                                if ($paymentMode == 'cash') {
                                                    $url = '/thank-you/?id=' . $transaction_id;
                                                } else {

                                                    $siteApp = ((isset($app)) ? $app : 'default');
                                                    $pgRequestType = ((isset($pgRequestType)) ? $pgRequestType : 'default');
                                                    $url = '/payment/generate/?id=' . $transaction_id . '&pg_type=order&app=' . str_replace(' ', '', $siteApp) . ' &pgRequestType=' . str_replace(' ', '', $pgRequestType) . '&customRedirection=' . rtrim(SITE_URL, '/') . '/corporate-subscription-status/?orderId=' . base64_encode($transaction_id);
                                                    header('Location: ' . $url, true, 301);
                                                }
                                            }
                                        }
                                    } else {

                                        echo 'Please select payment mode';
                                    }
                                } else {

                                    echo 'Coupon code is invalid';
                                }
                            } else {

                                echo 'Cart is empty or something is wrong';
                            }
                        }
                    } else {
                        echo 'Something is wrong while trying to create a new order';
                    }
                }
            }
        } else {
            echo "Something went wrong while trying to fetch cart data";
        }
    }
} else {
    echo "<h1>Something went wrong</h1>";
    exit();
}