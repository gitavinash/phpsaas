<?php

    function request($connection, $data) {
        
        // print_r($data);
        extract($data);

        if($first_name!='') {

            if($email!='') {

                if(filter_var($email, FILTER_VALIDATE_EMAIL)) {

                    if($mobile!='') {

                        if(preg_match("/^(\+91[\-\s]?)?[0]?(91)?[6789]\d{9}$/", $mobile)) {
        
                            
                            $noOfGuest=(($noOfGuest!='') ? $noOfGuest : 0 );
                
                            if($guest==1) {
                
                                if($noOfGuest!='') {

                                    if($noOfGuest<100) {
                                        
                                        $bookingValid=true;

                                    } else {

                                        $bookingValid=false;
                                        $msg='Maximum 99 guest are allowed';
                                        
                                    }
                                    
                                } else {
                                    
                                    $msg='Please enter no of guests';
                
                                }

                                $details='yes';
                
                            } else {
                
                                $bookingValid=true;
                                $details='no';
                
                            }

                            if($bookingValid==true) {


                
                                mysqli_query($connection, "START TRANSACTION");
                
                                $currency=((isset($currency)) ? $currency : 'INR' );

                                $bookingDate=date('Y-m-d', strtotime($bookingDate));
                
                                $mobile=ltrim((str_replace('+91', '', $mobile)), 0);

                                /** Found user details */

                                /** Generate Salt Key for user account */
                                                    
                                $saltKey=hash('sha256', uniqid(mt_rand(), true).$email.substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?'), 0, 12).time());
                                $password=substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
                                $enc_password=hash('sha256', $saltKey.$password);
                            
                                /** Checking Username is avilable or not */
                            
                                $username=strtolower(str_replace(' ', '', $first_name.$last_name));
                            
                                if($checkUsername=mysqli_query($connection, "SELECT user_username FROM ".TABLE_PREFIX."user WHERE user_username='{$username}'")) {
                                    if(mysqli_num_rows($checkUsername)!=0) {
                                        $username=strtolower($first_name).time();
                                    }
                                }

                                /** Profile Display Name */
                                                    
                                $display_name=$first_name.' '.$last_name;

                                if($checkUser=mysqli_query($connection, "SELECT user_id FROM ".TABLE_PREFIX."user WHERE user_email='{$email}' AND user_mobile='{$mobile}'")) {
                            
                                    if(mysqli_num_rows($checkUser)!=0) {
                        
                                        extract(mysqli_fetch_assoc($checkUser));
                        
                                    } else {

                                        if($checkUserEmail=mysqli_query($connection, "SELECT user_id FROM ".TABLE_PREFIX."user WHERE user_email='{$email}'")) {

                                            if(mysqli_num_rows($checkUserEmail)!=0) {

                                                extract(mysqli_fetch_assoc($checkUserEmail));

                                                /** Add mobile as a alternative number */

                                                if($checkAlternateMobile=mysqli_query($connection, "SELECT profile_meta_value FROM ".TABLE_PREFIX."profile_meta WHERE profile_meta_key='mobile' AND profile_meta_profile_id='{$user_id}' AND profile_meta_value='{$mobile}'")) {

                                                    if(mysqli_num_rows($checkAlternateMobile)!=0) {

                                                        extract(mysqli_fetch_assoc($checkAlternateMobile));
                                                        $mobile=$profile_meta_value;

                                                    } else {

                                                        if(!mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."profile_meta(profile_meta_profile_id, profile_meta_key, profile_meta_value) VALUES('{$user_id}', 'mobile', '{$mobile}')")) {

                                                            $error=TRUE;
                                                            $msg='Something is wrong while adding alternate mobile number';

                                                        } else {

                                                            $alternateID=mysqli_insert_id($connection);

                                                        }

                                                    }

                                                }

                                            } else {

                                                if($checkUserMobile=mysqli_query($connection, "SELECT user_id FROM ".TABLE_PREFIX."user WHERE user_mobile='{$mobile}'")) {

                                                    mysqli_query($connection, "START TRANSACTION");

                                                    if(mysqli_num_rows($checkUserMobile)!=0) {

                                                        extract(mysqli_fetch_assoc($checkUserMobile));
                                                        $addUserType='email';
                                                        
                                                    } else {
                                                        
                                                        $addUserType='mobile';

                                                    }

                                                    /** Add new user */

                                                    if(isset($addUserType)) {

                                                        if($addUserType=='email') {

                                                            /** Generate random mobile number */

                                                            $mobileNumber=time();

                                                            $addUser=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."user(user_username, user_email, user_mobile, user_password, user_salt_key, user_status, user_reg_time, user_reg_ip) VALUES('{$username}', '{$email}', '{$mobileNumber}', '{$enc_password}', '{$saltKey}', 0, NOW(), '".IP."')");
                                                            $user_id=mysqli_insert_id($connection);

                                                        } else {

                                                            $addUser=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."user(user_username, user_email, user_mobile, user_password, user_salt_key, user_status, user_reg_time, user_reg_ip) VALUES('{$username}', '{$email}', '{$mobile}', '{$enc_password}', '{$saltKey}', 0, NOW(), '".IP."')");
                                                            $user_id=mysqli_insert_id($connection);

                                                        }
                                
                                                        $addUserProfile=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."profile(profile_user_id, profile_first_name, profile_last_name, profile_display_name, profile_mobile, profile_email) VALUES('{$user_id}', '{$first_name}', '{$last_name}', '{$display_name}', '{$mobile}', '{$email}')");
                                                        $addUserSiteAccess=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."site_access(site_access_site_id, site_access_user_id, site_access_type, site_access_status) VALUES(".SITE_ID.", '{$user_id}', 'member', 0)");

                                                    }

                                                }

                                            }

                                        }

                                    }

                                }
                
                                $newEnquiry=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."enquiry(enquiry_site_id, enquiry_user_id, enquiry_first_name, enquiry_last_name, enquiry_email, enquiry_mobile, enquiry_query, enquiry_message, enquiry_type, enquiry_status) VALUES(".APP_SITE_ID.", '{$user_id}', '{$first_name}', '{$last_name}', '{$email}', '{$mobile}', '{$noOfGuest}', '{$details}', 'pangat', 0)");
                                $enquiryID=mysqli_insert_id($connection);

                                $newBooking=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."booking(booking_site_id, booking_user_id, booking_enquiry_id, booking_date, booking_time, booking_service, booking_details, booking_type, booking_status) VALUES(".APP_SITE_ID.", '{$user_id}', '{$enquiryID}', '{$bookingDate}', '{$bookingTime}', 'pangat', '{$details}', 'pangat', 0)");
                                $bookingID=mysqli_insert_id($connection);

                                if($newEnquiry && $newBooking) {

                                    if(isset($acceptPayment)) {

                                        if($acceptPayment==1) {

                                            /** Generate new order for pangat */

                                            require_once API_APP_MODULES.'order/generate.php';

                                            switch($bookingTime) {

                                                case 'breakfast':
                                                    $amount=1500;
                                                    break;

                                                default:
                                                    $amount=3100;
                                                    break;

                                            }

                                            $data=array(
                                                'id' => $bookingID,
                                                'price' => $amount,
                                                'donation' => $donation,
                                                'txnAmount' => $amount + $donation,
                                                'bookingRequest' => true,
                                                'currency' => $currency
                                            );

                                            $msg = generate($connection, $data);

                                            extract($msg);

                                        }

                                    } else {

                                        $status=200;
                                        $msg='Pangat request received successfully';

                                        /** Send sms */

                                        require_once APP_SMS_TEMPLATE.'booking-confirmation.php';

                                        /** Send email */

                                        require_once APP_EMAIL_TEMPLATE.'booking-confirmation.php';

                                    }

                                    mysqli_query($connection, "COMMIT");

                                } else {

                                    $msg='Somthing is wrong while trying to book pangat';
                                    mysqli_query($connection, "ROLLBACK");

                                }
                
                            }
                
                        } else {
                
                            $msg="Enter a valid mobile number";
                
                        }
        
                    } else {
        
                        $msg='Enter your mobile number';
        
                    }

                } else {

                    $msg='Enter a valid email address';

                }

            } else {

                $msg='Enter your email address';

            }

        } else {

            $msg='Please enter your name';

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Something went wrong. please try again' ),
            'mobile' => ((isset($mobile)) ? $mobile : '' ),
            'url' => ((isset($url)) ? $url : '#' ),
        );
    
        echo json_encode($result, TRUE);

    }