<?php

    require_once 'header.php';

    if(isset($id)) {

        $order_id=base64_decode($id);
        
        if($checkOrder=mysqli_query($connection, "SELECT transaction_order_id, transaction_method, order_product_id, order_total_price, order_product_qty, profile_mobile, profile_email, profile_first_name, profile_last_name, city_name, state_name, country_name, address_name, address_house_no, address_area, address_landmark, address_pin_code FROM isw_transaction INNER JOIN isw_profile ON transaction_user_id=profile_owner_id INNER JOIN isw_order ON transaction_order_id=order_id INNER JOIN isw_address ON address_id=order_address_id INNER JOIN isw_city ON city_id=address_city_id INNER JOIN isw_state ON state_id=address_state_id INNER JOIN isw_country ON country_id=address_country_id WHERE transaction_order_id='{$order_id}' AND transaction_site_id=".SITE_ID)) {
            
            if(mysqli_num_rows($checkOrder)!=0) {

                extract(mysqli_fetch_assoc($checkOrder));

                mysqli_query($connection, "START TRANSACTION");

                $updateOrder=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."order SET order_status=1 WHERE order_id='{$transaction_order_id}'");
                $updateTransaction=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."transaction SET transaction_status=1 WHERE transaction_order_id='{$order_id}'");

                if($updateOrder && $updateTransaction) {

                    mysqli_query($connection, "COMMIT");

                    if($transaction_method=='online') {

                        switch($order_product_id) {

                            case 1 :
                                $productName='Immunity Vitals';
                                break;

                            case 2 :
                                $productName='Tar Free';
                                break;

                            case 3 :
                                $productName='Gyno Vitals';
                                break;

                            case 4 :
                                $productName='V Liver Vitals';
                                break;

                            default :
                                $productName='Combile Product';
                                break;

                        }

                        /** Calling Numbus API */

                        $ch = curl_init();

                        curl_setopt($ch, CURLOPT_URL, 'https://ship.nimbuspost.com/api/orders/create');
                        curl_setopt($ch, CURLOPT_HEADER, FALSE);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            "NP-API-KEY: cc13f759563bd6a031e6d51c5e58950325aac6ea1558"
                        ));

                        $payload = Array(
                            'order_number' => 'OD'.date('Ymd-').$transaction_order_id,
                            'payment_method' => 'prepaid',
                            'amount' => $order_total_price,
                            'fname' => $profile_first_name,
                            'lname' => $profile_last_name,
                            'address' => $address_house_no.', '.$address_area,
                            'address_2' => $address_landmark,
                            'phone' => $profile_mobile,
                            'city' => $city_name,
                            'state' => $state_name,
                            'country' => $country_name,
                            'pincode' => $address_pin_code,
                            'products' => array(
                                array(
                                'name' => $productName,
                                'qty' => $order_product_qty,
                                'price' => $order_total_price,
                                )
                            )
                        );

                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
                        $response = curl_exec($ch);
                        curl_close($ch);

                    }

                    /** Update cart table */

                    if(!mysqli_query($connection, "UPDATE ".TABLE_PREFIX."cart SET cart_status=1 WHERE cart_session_id='".SESSION_ID."'")) {

                        echo 'Something is wrong while updating cart';

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
            
        } else {

            mysqli_errno($connection);
            exit();

        }
        
    }

	require_once 'footer.php';

?>