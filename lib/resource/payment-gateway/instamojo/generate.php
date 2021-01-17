<?php

    /** Check request is valid or not */

    if(isset($id)) {

        $id=base64_decode($id);

        $orderType=((isset($orderType)) ? $orderType : 'product' );

        if($checkStatus=mysqli_query($connection, "SELECT order_id, order_total_price, profile_mobile, profile_email, profile_display_name, GROUP_CONCAT({$orderType}_title, ',') as cartProduct FROM ".TABLE_PREFIX."cart INNER JOIN ".TABLE_PREFIX."transaction INNER JOIN ".TABLE_PREFIX."order ON transaction_order_id=order_id INNER JOIN ".TABLE_PREFIX."{$orderType} ON cart_relation_id={$orderType}_id INNER JOIN ".TABLE_PREFIX."profile ON profile_user_id=transaction_user_id WHERE transaction_id='{$id}' AND transaction_site_id=".SITE_ID." AND order_cart_session_id=cart_session_id")) {

            if(mysqli_num_rows($checkStatus)!=0) {

                extract(mysqli_fetch_assoc($checkStatus));
                
                if($checkOrderMeta=mysqli_query($connection, "SELECT order_meta_key, order_meta_value FROM ".TABLE_PREFIX."order_meta WHERE order_relation_id='{$order_id}' AND order_meta_status=1")) {

                    if(mysqli_num_rows($checkOrderMeta)!=0) {

                        $orderMetas=array();

                        while($orderMeta=mysqli_fetch_assoc($checkOrderMeta)) {

                            extract($orderMeta);

                            $orderMetas[$order_meta_key] = $order_meta_value;

                        }

                    }

                }

                extract($orderMetas);

                $paymentAmount=$order_total_price + ((isset($deliveryFees)) ? $deliveryFees : 0 );

                /** Start payment gateway code */

                $ch = curl_init();

                if(DEVELOPMENT_MODE==TRUE) {

                    curl_setopt($ch, CURLOPT_URL, 'https://test.instamojo.com/api/1.1/payment-requests/');

                } else {

                    curl_setopt($ch, CURLOPT_URL, 'https://www.instamojo.com/api/1.1/payment-requests/');

                }
                
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    "X-Api-Key:{$pg_api_key}",
                    "X-Auth-Token:{$pg_auth_token}")
                );
                $payload = Array(
                    'purpose' => $cartProduct,
                    'amount' => $paymentAmount,
                    'phone' => "{$profile_mobile}",
                    'buyer_name' => $profile_display_name,
                    'redirect_url' => SITE_URL.'/payment/status/',
                    'send_email' => false,
                    'send_sms' => false,
                    'email' => $profile_email,
                    'allow_repeated_payments' => false
                );
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
                $response = curl_exec($ch);
                curl_close($ch); 

                $response=json_decode($response, TRUE);

                if($response['success']==1) {

                    if($updateTransaction=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."transaction SET transaction_gateway='{$pg_name}', transaction_reference_id='{$response['payment_request']['id']}', transaction_status=2 WHERE transaction_id='{$id}' AND transaction_site_id=".SITE_ID)) {

                        header("Location: {$response['payment_request']['longurl']}", TRUE, 301);
                        exit();
    
                    } else {
    
                        echo 'Something is wrong while generation transaction status';
    
                    }

                } else {

                    print_r($response['message']).' of '.SITE_DOMAIN;

                }

            } else {

                echo 'Invalid Transactional ID';

            }

        } else {

            echo mysqli_error($connection);

        }

    }