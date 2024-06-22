<?php

    require_once LOGIN_CHECK;
    $status='warning';
    $leadId='';

    if(isset($first_name)) {
        if(isset($mobile)) {
            if(isset($package)) {
                if(isset($date_of_travel)) {
                    if(isset($number_of_days)) {
                        if(isset($number_of_adults)) {
                            if(isset($current_location)) {
                                if(isset($pickup_location)) {

                                    if (substr($mobile, 0, 1) === "0") {
                                        // If it is, remove the first character
                                        $mobile = substr($mobile, 1);
                                    }
                                    if (substr($mobile, 0, 3) === "+91") {
                                        // If it is, remove the first character
                                        $mobile = substr($mobile, 3);
                                    }

                                    $drop_location = (($drop_location=='') ? $pickup_location : '');
                                        
                                    mysqli_query($connection, "START TRANSACTION");

                                    // Check user is exiting or not

                                    $findUser = mysqli_query($connection, "SELECT user_id FROM ".TABLE_PREFIX."user WHERE user_email='{$email}' OR user_mobile='{$mobile}' LIMIT 1");

                                    $userId=0;

                                    if(mysqli_num_rows($findUser)!=0)  {
                                        $userInfo = mysqli_fetch_assoc($findUser);
                                        $userId = $userInfo['user_id'];
                                    }

                                    $addNewEnquiry = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "enquiry(enquiry_site_id, enquiry_user_id, enquiry_agent, enquiry_first_name, enquiry_middle_name, enquiry_last_name, enquiry_email, enquiry_mobile, enquiry_subject, enquiry_type, enquiry_source, enquiry_status, enquiry_time) VALUES(" . SITE_ID . ", '{$userId}', '".USER_ID."', '{$first_name}', '{$middle_name}', '{$last_name}', '{$email}', '{$mobile}', '{$package}', 'enquiry', 'direct', 2, NOW())");
                                    $leadId = mysqli_insert_id($connection);

                                    $enquiryHistory = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "enquiry_notes(enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value) VALUES('{$leadId}', '".USER_ID."', 'Generate', '{$logginUserName}')");
                                    $travelDate=date('Y-m-d', strtotime($date_of_travel));

                                    $createEnquiryProfile=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."enquiry_profile(enquiry_profile_ref_id, enquiry_profile_agent, date_of_travel, number_of_days, number_of_adults, number_of_childrens, number_of_infants, current_location, pickup_location, drop_location, requirements, status) VALUES('{$leadId}', '".USER_ID."', '{$travelDate}', '{$number_of_days}', '{$number_of_adults}', '{$number_of_childrens}', '{$number_of_infants}', '{$current_location}', '{$pickup_location}', '{$drop_location}', '{$requirements}', '1')");

                                    if ($addNewEnquiry && $enquiryHistory && $createEnquiryProfile) {
                                        $status='success';
                                        $msg='New enquiry save successfully';
                                        mysqli_query($connection, "COMMIT");
                                    } else {
                                        $status='error';
                                        $msg='Something went wrong while storing new enquiry';
                                        mysqli_query($connection, "ROLLBACK");
                                    }


                                } else {
                                    $msg="Please select client's pickup location";
                                }
                            } else {
                                $msg="Please enter client's current location";
                            }
                        } else {
                            $msg='Please enter number of adults';
                        }
                    } else {
                        $msg='Please enter package duration';
                    }
                } else {
                    $msg='Please select travel date';
                }
            } else {
                $msg='Please select tour package';
            }
        } else {
            $msg='Please enter valid mobile number';
        }
    } else {
        $msg='Please enter first name';
    }

    $result = array(
        'status'=>$status,
        'msg'=>$msg,
        'leadId' => $leadId
    );

    echo json_encode($result, TRUE);
    exit();