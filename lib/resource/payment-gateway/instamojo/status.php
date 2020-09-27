<?php

    /** Check request is valid or not */

    if(isset($payment_request_id)) {

        if($checkTransaction=mysqli_query($connection, "SELECT transaction_id FROM ".TABLE_PREFIX."transaction WHERE transaction_reference_id='{$payment_request_id}' AND transaction_site_id=".SITE_ID)) {

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
                    "X-Api-Key:{$pg_api_key}",
                    "X-Auth-Token:{$pg_auth_token}")
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

                        header("Location: /thank-you/?id={$transaction_id}", TRUE, 301);

                    } else {

                        header("Location: /payment-failed/?id={$transaction_id}", TRUE, 301);

                    }

                } else {

                    echo "Error whle trying to update transaction status";

                }

            }

        }

    }

    exit();