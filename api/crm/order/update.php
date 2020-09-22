<?php

    require_once LOGIN_CHECK;

    function update($connection, $data) {

        extract($data);

        if($checkValidOrder=mysqli_query($connection, "SELECT order_user_id as user_id, order_address_id as address_id FROM ".TABLE_PREFIX."order WHERE order_id='{$id}'")) {

            if(mysqli_num_rows($checkValidOrder)!=0) {

                extract(mysqli_fetch_assoc($checkValidOrder));

                $displayName=$first_name.' '.$last_name;

                mysqli_query($connection, "START TRANSACTION");

                $updateProfile=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."profile SET profile_first_name='{$first_name}', profile_last_name='{$last_name}', profile_display_name='{$displayName}', profile_mobile='{$mobile}' WHERE profile_user_id='{$user_id}'");

                if($findCity=mysqli_query($connection, "SELECT city_id FROM ".TABLE_PREFIX."city WHERE city_name='{$city}' AND city_state_id='{$state}'")) {

                    if(mysqli_num_rows($findCity)!=0) {

                        extract(mysqli_fetch_assoc($findCity));

                    } else {

                        $insertCity=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."city(city_name, city_state_id, city_state) VALUES('{$city}', '{$state}', 1)");
                        $city_id=mysqli_insert_id($connection);

                    }

                }

                $updateAddress=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."address SET address_house_no='{$address_house_no}', address_area='{}', address_landmark='{$address_landmark}', address_city_id='{$city_id}', address_state_id='{$state}', address_pin_code='{$address_pin_code}', address_type='{$address_type}' WHERE address_id='{$address_id}'");

                if($updateProfile && $updateAddress) {

                    $status=200;
                    $msg='Data update successfully';
                    mysqli_query($connection, "COMMIT");

                } else {

                    $msg='Something is wrong while data updating';
                    mysqli_query($connection, "ROLLBACK");

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