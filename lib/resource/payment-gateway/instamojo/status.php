<?php

    /** Check request is valid or not */

    if(isset($payment_request_id)) {


        if($checkTransaction=mysqli_query($connection, "SELECT transaction_id, transaction_order_id, transaction_type FROM ".TABLE_PREFIX."transaction WHERE transaction_reference_id='{$payment_request_id}' AND transaction_site_id=".SITE_ID)) {

            if(mysqli_num_rows($checkTransaction)!=0) {

                extract(mysqli_fetch_assoc($checkTransaction));

                /** Start payment gateway code */

                $ch = curl_init();

                if(DEVELOPMENT_MODE==TRUE) {

                    curl_setopt($ch, CURLOPT_URL, "https://test.instamojo.com/api/1.1/payments/{$payment_id}/");

                } else {

                    curl_setopt($ch, CURLOPT_URL, "https://www.instamojo.com/api/1.1/payments/{$payment_id}/");

                }

                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    "X-Api-Key:{PG_API_KEY}",
                    "X-Auth-Token:{PG_AUTH_TOKEN}")
                );
                $response = curl_exec($ch);
                curl_close($ch);

                $response=json_decode($response, TRUE);
                
                if($response['payment']['status']=='Credit') {

                    $status=", transaction_status=1";

                } else {

                    $status=", transaction_status=3";

                }

                if($updateTransaction=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."transaction SET transaction_payment_id='{$payment_id}', transaction_payment_mode='".strtolower($response['payment']['instrument_type'])."' {$status} WHERE transaction_reference_id='{$payment_request_id}'")) {

                    $transaction_id=base64_encode($transaction_id);

                    if($response['payment']['status']=='Credit') {

                        /** Update cart, order and transacytion status when payment is successfull received */

                        require_once PAYMENT_GATEWAY.'success.php';

                        $transaction_id=base64_encode($transaction_id);

                        header("Location: /thank-you/?id={$transaction_id}", TRUE, 301);

                    } else {

                        header("Location: /payment-failed/?id={$transaction_id}", TRUE, 301);

                    }

                } else {

                    echo "Error whle trying to update transaction status";

                }

            } else {

                echo "SELECT transaction_order_id as order_id, transaction_method, order_product_id, order_total_price, order_product_qty, order_product_price, order_delivery_charges, profile_mobile as mobile, profile_email, profile_first_name, profile_last_name, profile_display_name, city_name, state_name, country_name, address_name, address_house_no, address_area, address_landmark, address_pin_code FROM isw_transaction INNER JOIN isw_profile ON transaction_user_id=profile_user_id INNER JOIN isw_order ON transaction_order_id=order_id INNER JOIN isw_address ON address_id=order_address_id INNER JOIN isw_city ON city_id=address_city_id INNER JOIN isw_state ON state_id=address_state_id INNER JOIN isw_country ON country_id=address_country_id WHERE transaction_reference_id='{$payment_request_id}' AND transaction_site_id=".SITE_ID;

            }

        } else {

            echo mysqli_error($connection);

        }

    }

    exit();