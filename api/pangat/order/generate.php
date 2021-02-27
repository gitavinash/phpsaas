<?php

    function generate($connection, $data) {

        extract($data);

        /** Validate booking details */

        if($checkBooking=mysqli_query($connection, "SELECT booking_id, booking_user_id FROM ".TABLE_PREFIX."booking WHERE booking_id='{$id}'")) {

            if(mysqli_num_rows($checkBooking)!=0) {

                extract(mysqli_fetch_assoc($checkBooking));

                mysqli_query($connection, "START TRANSACTION");

                $txnAmount=str_replace(',', '', number_format($txnAmount, 2));

                /** Add booking into cart */

                $addIntoCart=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."cart(cart_session_id, cart_site_id, cart_relation_id, cart_relation_type, cart_qty, cart_price, cart_status, cart_generated) VALUES('".SESSION_ID."', ".APP_SITE_ID.", '{$id}', 'booking', '1', '{$txnAmount}', 0, NOW())");
                $cartID=mysqli_insert_id($connection);

                /** Create a new order */

                $createNewOrder=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."order(order_site_id, order_user_id, order_cart_session_id, order_product_price, order_currency, order_total_price, order_status) VALUES(".APP_SITE_ID.", '{$booking_user_id}', '".SESSION_ID."', '{$txnAmount}', '{$currency}', '{$txnAmount}', 0)");
                $orderID=mysqli_insert_id($connection);

                /** Initiate the new transactiuon */

                $initiateNewTransaction=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."transaction(transaction_site_id, transaction_user_id, transaction_cart_id, transaction_order_id, transaction_method, transaction_currency, transaction_amount, transaction_type, transaction_status) VALUES(".SITE_ID.", '{$booking_user_id}', '{$cartID}', '{$orderID}', 'online', '{$currency}', '{$txnAmount}', 'booking', 0)");
                $transaction_id=base64_encode(mysqli_insert_id($connection));

                if($addIntoCart && $createNewOrder && $initiateNewTransaction) {

                    $status=200;
                    $msg='Booking successfully add into cart';
                    $url='/payment/generate/?id='.$transaction_id.'&type=order&orderType=booking';


                } else {

                    $msg='Something is wrong while trying to connect with payment gateway';

                }


            } else {

                $msg='No booking information found';

            }

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
            'url' => ((isset($url)) ? $url : '#' )
        );

        if(isset($bookingRequest)) {

            return $result;

        } else {

            echo json_encode($result, TRUE);

        }

    }