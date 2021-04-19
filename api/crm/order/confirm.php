<?php

    function confirm($connection, $data) {

        extract($data);

        if(isset($id)) {

            if($checkOrder=mysqli_query($connection, "SELECT order_id FROM ".TABLE_PREFIX."order WHERE order_id='{$id}'")) {

                if(mysqli_num_rows($checkOrder)!=0) {

                    extract(mysqli_fetch_assoc($checkOrder));

                    if($checkOrder=mysqli_query($connection, "SELECT order_product_id, order_total_price, order_product_qty, order_payment_mode, profile_mobile, profile_email, profile_first_name, profile_last_name, city_name, state_name, country_name, address_name, address_house_no, address_area, address_landmark, address_pin_code FROM ".TABLE_PREFIX."transaction INNER JOIN ".TABLE_PREFIX."profile ON transaction_user_id=profile_user_id INNER JOIN ".TABLE_PREFIX."order ON transaction_order_id=order_id INNER JOIN ".TABLE_PREFIX."address ON address_id=order_address_id INNER JOIN ".TABLE_PREFIX."city ON city_id=address_city_id INNER JOIN ".TABLE_PREFIX."state ON state_id=address_state_id INNER JOIN ".TABLE_PREFIX."country ON country_id=address_country_id WHERE order_id='{$id}' AND order_site_id=".APP_SITE_ID)) {

                        if(mysqli_num_rows($checkOrder)!=0) {
    
                            extract(mysqli_fetch_assoc($checkOrder));

                            $product_id='';
                            $product_qty='';

                            for($i=0; $i<$totalProducts; $i++) {

                                if($i==0) {
                                    
                                    $product_id=$product_id." product_id=".${'product_id_'.$i};

                                } else {

                                    $product_id=$product_id." OR product_id=".${'product_id_'.$i};

                                }

                                $product_qty=$product_qty.${'qty_'.$i}.', ';

                            }

                            // $product_id=rtrim($product_id);
                            $product_qty=rtrim(str_replace(' ', '', $product_qty), ',');

                            if($checkProductName=mysqli_query($connection, "SELECT GROUP_CONCAT(product_title) as productName, GROUP_CONCAT(product_image) as productImage FROM ".TABLE_PREFIX."product WHERE {$product_id}")) {

                                if(mysqli_num_rows($checkProductName)!=0) {

                                    extract(mysqli_fetch_assoc($checkProductName)); 
         
                                    if(mysqli_query($connection, "UPDATE ".TABLE_PREFIX."order SET order_status=1 WHERE order_id='{$id}'")) {
            
                                        $status=200;
                                        $msg='Order update successfully';

                                        /** Send SMS */

                                        include_once APP_SMS_TEMPLATE.$type.'/'.$action.'.php';
                                        
                                        /** Send Email */

                                        include_once APP_EMAIL_TEMPLATE.$type.'/'.$action.'.php';
            
                                    } else {
            
                                        $msg='Something is wrong while updating the order status';
            
                                    }

                                } else {

                                    echo 'Something is wrong while fetching product details';

                                }

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