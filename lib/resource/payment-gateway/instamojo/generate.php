<?php

    /** Check request is valid or not */

    if(isset($id)) {

        $id=base64_decode($id);

        if($checkStatus=mysqli_query($connection, "SELECT order_total_price, profile_mobile, profile_email, profile_display_name, GROUP_CONCAT(cart_product_id, ',') as cartProduct FROM isw_cart, isw_transaction INNER JOIN isw_order ON transaction_order_id=order_id INNER JOIN isw_profile ON profile_owner_id=transaction_user_id WHERE transaction_id='{$id}' AND transaction_site_id=".SITE_ID." AND order_cart_session_id=cart_session_id")) {

            if(mysqli_num_rows($checkStatus)!=0) {

                extract(mysqli_fetch_assoc($checkStatus));

                /** Start payment gateway code */

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, 'https://test.instamojo.com/api/1.1/payment-requests/');
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    "X-Api-Key:{$pg_api_key}",
                    "X-Auth-Token:{$pg_auth_token}")
                );
                $payload = Array(
                    'purpose' => $cartProduct,
                    'amount' => $order_total_price,
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
                
                if($updateTransaction=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."transaction SET transaction_gateway='{$pg_name}', transaction_reference_id='{$response['payment_request']['id']}', transaction_status=2 WHERE transaction_id='{$id}' AND transaction_site_id=".SITE_ID)) {

                    header("Location: {$response['payment_request']['longurl']}", TRUE, 301);
                    exit();

                } else {

                    echo 'Something is wrong while generation transaction status';
                    // echo mysqli_error($connection);

                }

            } else {

                echo 'Invalid Transactional ID';

            }

        }

    }