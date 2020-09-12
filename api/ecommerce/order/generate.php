<?php

    function generate($connection, $data) {

        extract($data);

        /** Get Product from cart */

        if($checkCart=mysqli_query($connection, "SELECT GROUP_CONCAT(cart_product_id SEPARATOR ', ') as product, GROUP_CONCAT(cart_product_qty SEPARATOR ', ') as quantity, GROUP_CONCAT(cart_price SEPARATOR ', ') as price, SUM(cart_price) as totalPrice, cart_currency FROM isw_cart WHERE cart_session_id='".SESSION_ID."' AND cart_status=1 AND cart_site_id=".SITE_ID)) {

            if(mysqli_num_rows($checkCart)!=0) {

                extract(mysqli_fetch_assoc($checkCart));
                
                if($addNewOrder=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."order(order_site_id, order_product_id, order_product_qty, order_product_price, order_total_price, order_coupon, order_status, order_payment_mode, order_generate) VALUES(".SITE_ID.", '{$product}', '{$quantity}', '{$price}', '{$totalPrice}', '{$coupon}', 0, '{$paymentMode}', NOW())")) {

                    $status=200;
                    $msg='Redirect to payment mode';
                    $orderID=base64_encode(mysqli_insert_id($connection));
                    if($paymentMode=='cod') {

                        $url='/thank-you/?order_id='.$orderID;

                    } else {

                        $url='/payment/generate/?id='.$orderID.'&type=order';

                    }

                } else {

                    // echo mysqli_error($connection);
                    echo "INSERT INTO ".TABLE_PREFIX."order(order_site_id, order_product_id, order_product_qty, order_product_price, order_total_price, order_coupon, order_status, order_payment_mode, order_generate) VALUES(".SITE_ID.", '{$product}', '{$quantity}', '{$price}', '{$totalPrice}', '{$coupon}', 0, '{$paymentMode}', NOW()";

                }

            } else {

                $msg='Cart is empty or something is wrong';

            }

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
            'url' => ((isset($url)) ? $url : '#' )
        );
    
        echo json_encode($result, TRUE);

    }