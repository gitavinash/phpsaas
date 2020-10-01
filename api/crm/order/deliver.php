<?php

    function deliver($connection, $data) {

        extract($data);

        if(isset($id)) {

            if($checkOrder=mysqli_query($connection, "SELECT order_id FROM ".TABLE_PREFIX."order WHERE order_id='{$id}'")) {

                if(mysqli_num_rows($checkOrder)!=0) {

                    extract(mysqli_fetch_assoc($checkOrder));

                    if($checkOrder=mysqli_query($connection, "SELECT order_product_id, order_total_price, order_product_qty, order_payment_mode, profile_mobile, profile_email, profile_first_name, profile_last_name, city_name, state_name, country_name, address_name, address_house_no, address_area, address_landmark, address_pin_code FROM ".TABLE_PREFIX."transaction INNER JOIN ".TABLE_PREFIX."profile ON transaction_user_id=profile_user_id INNER JOIN ".TABLE_PREFIX."order ON transaction_order_id=order_id INNER JOIN ".TABLE_PREFIX."address ON address_id=order_address_id INNER JOIN ".TABLE_PREFIX."city ON city_id=address_city_id INNER JOIN ".TABLE_PREFIX."state ON state_id=address_state_id INNER JOIN isw_country ON country_id=address_country_id WHERE order_id='{$id}' AND order_site_id=".APP_SITE_ID)) {

                        if(mysqli_num_rows($checkOrder)!=0) {
    
                            extract(mysqli_fetch_assoc($checkOrder));
    
                            if(mysqli_query($connection, "UPDATE ".TABLE_PREFIX."order SET order_status=6 WHERE order_id='{$id}'")) {
    
                                $status=200;
                                $msg='Order update successfully to deliver list';
    
                            } else {
    
                                $msg='Something is wrong while updating the order status';
    
                            }
                            
                        } else {
    
                            $msg='No order information found';
    
                        }
    
                    } else {
    
                        echo mysqli_error($connection);
    
                    }

                } else {

                    $msg='Invalid order ID';

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