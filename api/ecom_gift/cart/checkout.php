<?php

    function checkout($connection, $data) {
        
        extract($data);

        if($first_name!='') {

            if($house_no!='') {

                if($pincode!='') {

                    if($email!='') {

                        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        
                            if($mobile!='') {
                        
                                if(preg_match("/^(\+91[\-\s]?)?[0]?(91)?[6789]\d{9}$/", $mobile)) {

                                    $mobile=ltrim((str_replace('+91', '', $mobile)), 0);
                        
                                    if($state!='') {
                        
                                        if($city!='') {

                                            /** Check user is register or not */

                                            $mobile=ltrim(str_replace('+91', '', (str_replace('91', '', $mobile))), 0);
                        
                                            if($checkUser=mysqli_query($connection, "SELECT user_id, user_mobile FROM ".TABLE_PREFIX."user WHERE user_email='{$email}' AND user_mobile='{$mobile}'")) {
                            
                                                if(mysqli_num_rows($checkUser)!=0) {
                                    
                                                    extract(mysqli_fetch_assoc($checkUser));
                                                    $addUser=TRUE;
                                                    $addUserProfile=TRUE;
                                                    $addUserSiteAccess=TRUE;
                                    
                                                } else {

                                                    if($checkUserEmail=mysqli_query($connection, "SELECT user_id, user_mobile FROM ".TABLE_PREFIX."user WHERE user_email='{$email}'")) {

                                                        if(mysqli_num_rows($checkUserEmail)!=0) {

                                                            echo 'Email found';

                                                        } else {

                                                            if($checkUserMobile=mysqli_query($connection, "SELECT user_id, user_mobile FROM ".TABLE_PREFIX."user WHERE user_mobile='{$mobile}'")) {

                                                                echo 'Mobile found';

                                                            }

                                                        }

                                                    }

                                                }

                                            }

                                            exit();

                                        } else {

                                            $msg='Please enter your city name';

                                        }

                                    } else {
                        
                                        $msg='Please select your state';
                        
                                    }
                        
                                } else {
                        
                                    $msg='Enter valid mobile number';
                        
                                }
                        
                        
                            } else {
                        
                                $msg='Please enter your mobile';
                        
                            }
                        
                        } else {
                        
                            $msg='Enter a valid email';
                        
                        }
                        
                        } else {
                        
                        $msg='Please enter your email';
                        
                        }

                } else {

                    $msg='Please enter your pin code';

                }

            } else {

                $msg='Please enter your house or flat number';

            }

        } else {

            $msg='Please enter your name';

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