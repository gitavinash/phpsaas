<?php

    require_once LOGIN_CHECK;

    function update($connection, $data) {

        extract($data);

        if($checkValidOrder=mysqli_query($connection, "SELECT order_user_id as user_id, order_product_id, order_product_qty, order_payment_mode, order_address_id as address_id FROM ".TABLE_PREFIX."order WHERE order_id='{$id}'")) {

            if(mysqli_num_rows($checkValidOrder)!=0) {

                extract(mysqli_fetch_assoc($checkValidOrder));

                $displayName=$first_name.' '.$last_name;

                $product_id='';
                $product_qty='';

                for($i=0; $i<$totalProducts; $i++) {

                    $product_id=$product_id.${'product_id_'.$i}.', ';
                    $product_qty=$product_qty.${'qty_'.$i}.', ';

                }

                $product_id=rtrim(str_replace(' ', '', $product_id), ',');
                $product_qty=rtrim(str_replace(' ', '', $product_qty), ',');
                
                $updateDisable=false;

                if($order_payment_mode=='online') {

                    
                    
                    if(strcmp(str_replace(' ', '', $order_product_id), $product_id)) {

                        $updateDisable=true;

                    }
                    
                    if(strcmp(str_replace(' ', '', $order_product_qty), $product_qty)) {

                        $updateDisable=true;

                    }

                }

                if($updateDisable==false) {

                    mysqli_query($connection, "START TRANSACTION");

                    $updateProduct=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."order SET order_product_id='{$product_id}', order_product_qty='{$product_qty}' WHERE order_id='{$id}'");

                    $updateProfile=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."profile SET profile_first_name='{$first_name}', profile_last_name='{$last_name}', profile_display_name='{$displayName}', profile_mobile='{$mobile}' WHERE profile_user_id='{$user_id}'");

                    if($findCity=mysqli_query($connection, "SELECT city_id FROM ".TABLE_PREFIX."city WHERE city_name='{$city}' AND city_state_id='{$state}'")) {

                        if(mysqli_num_rows($findCity)!=0) {

                            extract(mysqli_fetch_assoc($findCity));

                        } else {

                            $insertCity=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."city(city_name, city_state_id, city_state) VALUES('{$city}', '{$state}', 1)");
                            $city_id=mysqli_insert_id($connection);

                        }

                    }

                    $updateAddress=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."address SET address_house_no='{$address_house_no}', address_area='{$address_area}', address_landmark='{$address_landmark}', address_city_id='{$city_id}', address_state_id='{$state}', address_pin_code='{$address_pin_code}', address_type='{$address_type}' WHERE address_id='{$address_id}'");

                    if($updateProduct && $updateProfile && $updateAddress) {

                        $status=200;
                        $msg='Data update successfully';
                        mysqli_query($connection, "COMMIT");

                    } else {

                        $msg='Something is wrong while data updating';
                        mysqli_query($connection, "ROLLBACK");

                    }

                } else {

                    $msg="Sorry this order is prepaid. we cannot change the product details";

                }

            } else {

                $msg='Invalid order details found';

            }

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' )
        );
    
        echo json_encode($result, TRUE);

    }