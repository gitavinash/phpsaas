<?php

    require_once 'header.php';

    if(isset($order_id)) {

        $transaction_id=base64_decode($order_id);
        
        if($checkOrder=mysqli_query($connection, "SELECT transaction_order_id, transaction_method FROM ".TABLE_PREFIX."transaction WHERE transaction_id='{$transaction_id}'")) {
            
            if(mysqli_num_rows($checkOrder)!=0) {

                extract(mysqli_fetch_assoc($checkOrder));

                mysqli_query($connection, "START TRANSACTION");

                $updateOrder=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."order SET order_status=1 WHERE order_id='{$transaction_order_id}'");
                $updateTransaction=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."transaction SET transaction_status=1 WHERE transaction_id='{$transaction_id}'");

                if($updateOrder && $updateTransaction) {

                    mysqli_query($connection, "COMMIT");

                    if($transaction_method='online') {

                        /** Calling Numbus API */

                        $ch = curl_init();

                        curl_setopt($ch, CURLOPT_URL, 'https://ship.nimbuspost.com/api/orders/create');
                        curl_setopt($ch, CURLOPT_HEADER, FALSE);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            "NP-API-KEY: 54d047d5c50066f0067b541684584fd73f0e00521558"
                        ),
                        $payload = Array(
                            'order_number' => $cartProduct,
                            'payment_method' => $order_total_price,
                            'amount' => "{$profile_mobile}",
                            'fname' => $profile_display_name,
                            'lname' => SITE_URL.'/payment/status/',
                            'address' => false,
                            'phone' => false,
                            'city' => $profile_email,
                            'state' => $profile_email,
                            'country' => $profile_email,
                            'pincode' => $profile_email,
                            'products' => array(
                                'name' => '',
                                'qty' => '',
                                'price' => ''
                            );
                        ),
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
                        $response = curl_exec($ch);
                        curl_close($ch); 
                        print_r($response);
                        exit();

                    } ?>
                    
                    <section class='continue-shopping' >
                        <h1>Thank You<span>Your order has been successfully received.</span></h1>
                        <a href="/" class="btn btn-success" >Continue Shopping</a>
                    </section>

                    <?php

                } else {

                    mysqli_query($connection, "ROLLBACK");

                }

            } else { echo mysqli_error($connection); ?>

                <section class='continue-shopping' >
                    <h1>Error<span>Something is wrong while generate a order request.</span></h1>
                    <a href="/" class="btn btn-success" >Continue Shopping</a>
                </section>

            <?php
            }
            
        }
        
    }

	require_once 'footer.php';

?>