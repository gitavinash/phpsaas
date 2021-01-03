<?php

    function generate($connection, $data) {

        extract($data);

        /** Get Product from cart */

        if($checkOrderStatus=mysqli_query($connection, "SELECT order_delivery_charges FROM ".TABLE_PREFIX."order WHERE order_id='{$order}' AND order_status=0")) {

            if(mysqli_num_rows($checkOrderStatus)!=0) {

                extract(mysqli_fetch_assoc($checkOrderStatus));

                if($checkCart=mysqli_query($connection, "SELECT GROUP_CONCAT(cart_relation_id SEPARATOR ', ') as product, GROUP_CONCAT(cart_qty SEPARATOR ', ') as quantity, GROUP_CONCAT(cart_price SEPARATOR ', ') as price, SUM(cart_price) as totalPrice, cart_currency, cart_user_id as user_id FROM isw_cart WHERE cart_session_id='".SESSION_ID."' AND cart_status=2 AND cart_user_id!=0 AND cart_site_id=".SITE_ID)) {

                    if(mysqli_num_rows($checkCart)!=0) {
        
                        extract(mysqli_fetch_assoc($checkCart));
        
                        if($paymentMode=='cash') {
        
                            $deliveryCharges=50;
                            
                        } else {
                            
                            $deliveryCharges=(($order_delivery_charges!='') ? $order_delivery_charges : 0 );
                            
                        }

                        $totalPrice=$totalPrice+$deliveryCharges;
                        
                        if($paymentMode!='') {
        
                            if($updateCart=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."order SET order_product_id='{$product}', order_product_qty='{$quantity}', order_product_price='{$price}', order_delivery_charges='{$deliveryCharges}', order_total_price='{$totalPrice}', order_coupon='{$coupon}', order_payment_mode='{$paymentMode}' WHERE order_id='{$order}' AND order_cart_session_id='".SESSION_ID."' AND order_site_id=".SITE_ID)) {
        
                                /** Start new transaction */
            
                                if($startTransaction=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."transaction(transaction_site_id, transaction_user_id, transaction_order_id, transaction_method, transaction_currency, transaction_amount, transaction_type, transaction_status) VALUES(".SITE_ID.", '{$user_id}', '{$order}', '{$paymentMode}', '{$cart_currency}', '{$totalPrice}', 'purchase', 0)")) {
            
                                    $status=200;
                                    $msg='Redirect to payment mode';
                                    $transaction_id=base64_encode(mysqli_insert_id($connection));
                                    if($paymentMode=='cash') {
            
                                        $url='/thank-you/?id='.$transaction_id;
            
                                    } else {
            
                                        $url='/payment/generate/?id='.$transaction_id.'&type=order';
            
                                    }
            
                                }
            
                            }
        
                        } else {
        
        
        
                        }
        
                    } else {
        
                        $msg='Cart is empty or something is wrong';
        
                    }
        
                }

            } else {

                $msg='Something is wrong while trying to create a new order';

            }

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
            'url' => ((isset($url)) ? $url : '#' )
        );
    
        echo json_encode($result, TRUE);

    }