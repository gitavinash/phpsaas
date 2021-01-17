<?php

    require_once 'header.php';

    if(isset($id)) {

        $transaction_id=base64_decode($id);
        
        if($checkOrder=mysqli_query($connection, "SELECT transaction_order_id as order_id, transaction_method, order_product_id, order_total_price, order_product_qty, order_product_price, order_delivery_charges, profile_mobile as mobile, profile_email, profile_first_name, profile_last_name, profile_display_name, city_name, state_name, country_name, address_name, address_house_no, address_area, address_landmark, address_pin_code FROM isw_transaction INNER JOIN isw_profile ON transaction_user_id=profile_user_id INNER JOIN isw_order ON transaction_order_id=order_id INNER JOIN isw_address ON address_id=order_address_id INNER JOIN isw_city ON city_id=address_city_id INNER JOIN isw_state ON state_id=address_state_id INNER JOIN isw_country ON country_id=address_country_id WHERE transaction_id='{$transaction_id}' AND transaction_site_id=".APP_SITE_ID)) {
            
            if(mysqli_num_rows($checkOrder)!=0) {

                extract(mysqli_fetch_assoc($checkOrder));

                mysqli_query($connection, "START TRANSACTION");

                $updateTransaction=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."transaction SET transaction_status=1 WHERE transaction_order_id='{$order_id}'");

                if($updateTransaction) {
                    
                    mysqli_query($connection, "COMMIT");
                    
                    $orderProductID=str_replace(' ', '', explode(',', $order_product_id));
                    $productID='';

                    for($i=0; $i<sizeof($orderProductID); $i++) {

                        if($i==0) {

                            $productID=$productID."product_id='{$orderProductID[$i]}'";

                        } else {

                            $productID=$productID." OR product_id='{$orderProductID[$i]}'";

                        }

                    }

                    if($productDetails=mysqli_query($connection, "SELECT GROUP_CONCAT(product_title) as productName, GROUP_CONCAT(product_image) as productImage FROM isw_product WHERE product_type='product' AND {$productID}")) {

                        $productName='';

                        if(mysqli_num_rows($productDetails)!=0) {

                            extract(mysqli_fetch_assoc($productDetails));

                        }

                    }

                    $ch = curl_init();
                    
                    if($transaction_method=='online') {

                        $updateOrder=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."order SET order_status=1 WHERE order_id='{$order_id}'");

                        /** Calling Numbus API */
                        // curl_setopt($ch, CURLOPT_URL, 'https://ship.nimbuspost.com/api/orders/create');
                        // curl_setopt($ch, CURLOPT_HEADER, FALSE);
                        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                        // curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        //     "NP-API-KEY: cc13f759563bd6a031e6d51c5e58950325aac6ea1558"
                        // ));

                        // $payload = Array(
                        //     'order_number' => 'OD'.$order_id,
                        //     'payment_method' => 'prepaid',
                        //     'amount' => $order_total_price,
                        //     'fname' => $profile_first_name,
                        //     'lname' => $profile_last_name,
                        //     'address' => $address_house_no.', '.$address_area,
                        //     'address_2' => $address_landmark,
                        //     'phone' => $mobile,
                        //     'city' => $city_name,
                        //     'state' => $state_name,
                        //     'country' => $country_name,
                        //     'pincode' => $address_pin_code,
                        //     'products' => array(
                        //         array(
                        //         'name' => $productName,
                        //         'qty' => $order_product_qty,
                        //         'price' => $order_total_price,
                        //         )
                        //     )
                        // );

                    }

                    /** Paramantra API */
                    curl_setopt($ch, CURLOPT_URL, 'https://cloud.paramantra.com/paramantra/api/data/new/format/json');
                    curl_setopt($ch, CURLOPT_HEADER, FALSE);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        "X-API-KEY: NDEpNDBNE1CuxpMMBAMECGHupG",
                        "ACTION-ON: J3tMI",
                    ));
                    curl_setopt($ch, CURLOPT_USERPWD,
                        "NDEpNDBNE1CuxpMMBAMECGHupG" . ':' . "NDEpNDBNE1CuxpMMBAMECGHupG"
                    );

                    $payload = Array(
                        'phonefax' => $mobile,
                        'rep_id' => 'prepaid',
                        'channel_id' => 'website',
                        'email' => $profile_email,
                        'f_name' => $profile_first_name,
                        'subject' => 'New order received from website',
                        'queryid' => 'OD'.$order_id,
                        'notes' => '',
                        'produ_24453' => $productName,
                        'quant_24509' => $order_product_qty,
                        'price_89724' => $order_product_price,
                        'order_11827' => 'OD'.$order_id,
                        'payme_12021' => $transaction_method,
                        'sku_24026' => '',
                        'coupo_24122' => '',
                        'shipp_24076' => $order_delivery_charges,
                        'deliv_26333' => $address_house_no.', '.$address_area.', '.$address_landmark.', '.$city_name.', '.$state_name.', '.$address_pin_code.', '.$country_name,
                        'total_24157' => $order_total_price,
                        'shipp_24216' => '',
                        'awbnu_24187' => '',
                    );

                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
                    $response = curl_exec($ch);
                    curl_close($ch);
                    
                    /** Update cart table */

                    if(!mysqli_query($connection, "UPDATE ".TABLE_PREFIX."cart SET cart_status=1 WHERE cart_session_id='".SESSION_ID."'")) { ?>

                        <section class='continue-shopping' >
                            <h1>Error<span>Something is wrong while updating cart.</span></h1>
                            <a href="/" class="btn btn-success" >Continue Shopping</a>
                        </section>

                    <?php


                    } else {
                        
                        ?>
                    
                        <section class='continue-shopping' >
                            <h1>Thank You<span>Your order number <strong>OD<?php echo $order_id; ?></strong> is successfully received. You may get a confirmation call in a while.</span></h1>
                            <a href="/" class="btn btn-success" >Continue Shopping</a>
                        </section>
                        <?php
                        
                    }

                } else {

                    mysqli_query($connection, "ROLLBACK");

                }

            } else { ?>

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