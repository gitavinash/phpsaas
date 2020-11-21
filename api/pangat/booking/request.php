<?php

    function request($connection, $data) {
        
        extract($data);

        if($first_name!='') {

            if($email!='') {

                if(filter_var($email, FILTER_VALIDATE_EMAIL)) {

                    if($mobile!='') {

                        if(preg_match("/^(\+91[\-\s]?)?[0]?(91)?[6789]\d{9}$/", $mobile)) {
        
                            $details=((isset($self)) ? 'yes' : 'no' );
                            $noOfGuest=((isset($noOfGuest)) ? $noOfGuest : 0 );
                
                            if($guest!=false) {
                
                                if($noOfGuest!=0) {
                
                                    $bookingValid=true;
                                    
                                } else {

                                    $bookingValid=false;
                                    $msg='Please enter no of guests';
                
                                }
                
                            } else {
                
                                $bookingValid=true;
                
                            }
                
                            if($bookingValid==true) {
                
                                mysqli_query($connection, "START TRANSACTION");
                
                                $bookingDate=date('Y-m-d', strtotime($bookingDate));
                
                                $mobile=ltrim(str_replace('+91', '', (str_replace('91', '', $mobile))), 0);
                
                                if($checkUser=mysqli_query($connection, "SELECT user_id FROM ".TABLE_PREFIX."user WHERE user_mobile='{$mobile}'")) {
                
                                    if(mysqli_num_rows($checkUser)!=0) {
                
                                        extract(mysqli_fetch_assoc($checkUser));
                
                                    } else {
                
                                        $user_id=0;
                
                                    }
                
                                    $newEnquiry=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."enquiry(enquiry_site_id, enquiry_user_id, enquiry_first_name, enquiry_last_name, enquiry_email, enquiry_mobile, enquiry_query, enquiry_message, enquiry_type, enquiry_status) VALUES(".APP_SITE_ID.", '{$user_id}', '{$first_name}', '{$last_name}', '{$email}', '{$mobile}', '{$noOfGuest}', '{$details}', 'pangat', 0)");
                                    $enquiryID=mysqli_insert_id($connection);
                
                                    $newBooking=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."booking(booking_site_id, booking_user_id, booking_enquiry_id, booking_date, booking_time, booking_service, booking_details, booking_type, booking_status) VALUES(".APP_SITE_ID.", '{$user_id}', '{$enquiryID}', '{$bookingDate}', '{$bookingTime}', 'pangat', '{$details}', 'pangat', 0)");
                
                                    if($newEnquiry && $newBooking) {
                
                                        $status=200;
                                        $msg='Pangat request received successfully';
                                        mysqli_query($connection, "COMMIT");
                
                                    } else {
                
                                        $msg='Somthing is wrong while trying to book pangat';
                                        mysqli_query($connection, "ROLLBACK");
                
                                    }
                
                
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
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
            'mobile' => ((isset($mobile)) ? $mobile : '' )
        );
    
        echo json_encode($result, TRUE);

    }