<?php

    function checkout($connection, $data) {
        
        extract($data);

        if($email!='') {

            if($mobile!='') {

                /** Generate Salt Key for user account */
                        
                $saltKey=hash('sha256', uniqid(mt_rand(), true).$email.substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?'), 0, 12).time());
                $password=substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
                $enc_password=hash('sha256', $saltKey.$password);

                /** Profile Display Name */

                $display_name=$first_name.' '.$last_name;

                /** Checking Username is avilable or not */

                $username=strtolower(str_replace(' ', '', $first_name.$last_name));

                if($checkUsername=mysqli_query($connection, "SELECT user_username FROM ".TABLE_PREFIX."user WHERE user_username='{$username}'")) {
                    if(mysqli_num_rows($checkUsername)!=0) {
                        $username=strtolower($first_name).time();
                    }
                }

                if($checkMobileNumber=mysqli_query($connection, "SELECT user_id FROM ".TABLE_PREFIX."user WHERE user_mobile='{$mobile}'")) {

                    if(mysqli_num_rows($checkMobileNumber)!=0) {

                        $mobileNumber=time();                                

                    } else {

                        $mobileNumber=$mobile;

                    }

                    $mobileNumber=ltrim($mobileNumber, 0);

                }

                if($checkUser=mysqli_query($connection, "SELECT user_id, user_mobile FROM ".TABLE_PREFIX."user WHERE user_email='{$email}'")) {

                    if(mysqli_num_rows($checkUser)!=0) {
        
                        extract(mysqli_fetch_assoc($checkUser));
                        $addUser=TRUE;
                        $addUserProfile=TRUE;
                        $addUserSiteAccess=TRUE;
        
                    } else {

                        /** Save user into database */

                        mysqli_query($connection, "START TRANSACTION");

                        $addUser=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."user(user_username, user_email, user_mobile, user_password, user_salt_key, user_status, user_reg_time, user_reg_ip) VALUES('{$username}', '{$email}', '{$mobileNumber}', '{$enc_password}', '{$saltKey}', 0, NOW(), '".IP."')");
                        $user_id=mysqli_insert_id($connection);

                        $addUserProfile=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."profile(profile_owner_id, profile_first_name, profile_last_name, profile_display_name, profile_mobile, profile_email) VALUES('{$user_id}', '{$first_name}', '{$last_name}', '{$display_name}', '{$mobileNumber}', '{$email}')");

                        $addUserSiteAccess=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."site_access(site_access_site_id, site_access_user_id, site_access_type, site_access_status) VALUES(".SITE_ID.", '{$user_id}', 'member', 0)");
        
                    }
        
                }

                /** Fetch city id */

                if($checkCity=mysqli_query($connection, "SELECT city_id FROM ".TABLE_PREFIX."city WHERE city_name='{$city}'")) {

                    if(mysqli_num_rows($checkCity)!=0) {

                        extract(mysqli_fetch_assoc($checkCity));

                    } else {

                        $addNewCity=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."city(city_name, city_state_id, city_status) VALUES('{$city}', '{$state}', 0)");
                        $city_id=mysqli_insert_id($connection);

                    }

                }

                $addNewAddress=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."address(address_site_id, address_user_id, address_name, address_house_no, address_area, address_landmark, address_city_id, address_state_id, address_country_id, address_pin_code, address_mobile, address_type, address_status) VALUES(".SITE_ID.", '{$user_id}', '{$display_name}', '{$house_no}', '{$area}', '{$landmark}', '{$city_id}', '{$state}', '{$country}', '{$pincode}', '{$mobileNumber}', 'home', 1)");
                $addressID=mysqli_insert_id($connection);

                $updateCart=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."cart SET cart_user_id='{$user_id}', cart_status=2 WHERE cart_session_id='".SESSION_ID."' AND cart_status=0 AND cart_site_id='".SITE_ID."'");

                $addNewOrder=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."order(order_site_id, order_user_id, order_cart_session_id, order_address_id, order_status, order_generate) VALUES(".SITE_ID.", '{$user_id}', '".SESSION_ID."', '{$addressID}', 0, NOW())");
                $order_id=mysqli_insert_id($connection);

                if($addUser && $addUserProfile && $addUserSiteAccess && $addNewAddress && $updateCart && $addNewOrder) {

                    $status=200;
                    $msg='Cart update successfully';

                    mysqli_query($connection, "COMMIT");

                } else {

                    $msg='Something is wrong while updating cart';
                    $msg=$addUser.' && '.$addUserProfile.' && '.$addUserSiteAccess.' && '.$addNewAddress.' && '.$updateCart.' && '.$addNewOrder;
                    mysqli_query($connection, "ROLLBACK");

                }


            } else {

                $msg='Please enter your mobile';

            }

        } else {

            $msg='Please enter your email';

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
            'name' => ((isset($display_name)) ? $display_name : '' ),
            'address' => $house_no.', '.$area.', '.$landmark.', '.$city.', '.$pincode,
            'order' => base64_encode(((isset($order_id)) ? $order_id : '' )),
        );
    
        echo json_encode($result, TRUE);

    }