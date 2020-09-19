<?php

    function confirm($connection, $data) {

        extract($data);

        if(isset($order_id)) {

            if($checkOrder=mysqli_query($connection, "SELECT order_id FROM ".TABLE_PREFIX."order WHERE order_id='{$order_id}'")) {

                echo "SELECT order_product_id, order_total_price, order_product_qty, profile_mobile, profile_email, profile_first_name, profile_last_name, city_name, state_name, country_name, address_name, address_house_no, address_area, address_landmark, address_pin_code FROM isw_profile ON transaction_user_id=profile_owner_id INNER JOIN isw_order ON transaction_order_id=order_id INNER JOIN isw_address ON address_id=order_address_id INNER JOIN isw_city ON city_id=address_city_id INNER JOIN isw_state ON state_id=address_state_id INNER JOIN isw_country ON country_id=address_country_id WHERE order_id='{$order_id}' AND order_site_id=".SITE_ID;
                exit();

                if($checkOrder=mysqli_query($connection, "SELECT order_product_id, order_total_price, order_product_qty, profile_mobile, profile_email, profile_first_name, profile_last_name, city_name, state_name, country_name, address_name, address_house_no, address_area, address_landmark, address_pin_code FROM isw_profile ON transaction_user_id=profile_owner_id INNER JOIN isw_order ON transaction_order_id=order_id INNER JOIN isw_address ON address_id=order_address_id INNER JOIN isw_city ON city_id=address_city_id INNER JOIN isw_state ON state_id=address_state_id INNER JOIN isw_country ON country_id=address_country_id WHERE order_id='{$order_id}' AND order_site_id=".SITE_ID)) {

                    if(mysqli_num_rows($checkOrder)!=0) {

                        extract(mysqli_fetch_assoc($checkOrder));

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

                        if(mysqli_query($connection, "UPDATE ".TABLE_PREFIX."order SET order_status=1 WHERE order_id='{$order_id}'")) {

                            $status=200;
                            $msg='Order update successfully';

                        } else {

                            $msg='Something is wrong while updating the order status';

                        }
                        
                    } else {

                        $msg='No order information found';

                    }

                }

            }

        } else {

            $msg='Invalid order id found';

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' )
        );
    
        echo json_encode($result, TRUE);

    }