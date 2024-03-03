<?php

    require_once LOGIN_CHECK;
    $status='warning';

    if(isset($first_name)) {
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
                                
                                $travelDate=date('Y-m-d', strtotime($date_of_travel));

                                $enquiryHistory = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "enquiry_notes(enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value) VALUES('{$leadId}', '".USER_ID."', 'update', '{$logginUserName}')");

                                $updateEnquiry=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."enquiry SET enquiry_first_name='{$first_name}',enquiry_middle_name='{$middle_name}', enquiry_last_name='{$last_name}', enquiry_email='{$email}', enquiry_subject='{$package}' WHERE enquiry_id='{$leadId}'");

                                if($checkEnquiryProfile=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."enquiry_profile WHERE enquiry_profile_ref_id='{$leadId}' AND enquiry_profile_agent='".USER_ID."'")) {
                                    if(mysqli_num_rows($checkEnquiryProfile)== 0) {

                                        $createEnquiryProfile=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."enquiry_profile(enquiry_profile_ref_id, enquiry_profile_agent, date_of_travel, number_of_days, number_of_adults, number_of_childrens, number_of_infants, current_location, pickup_location, drop_location, requirements, status) VALUES('{$leadId}', '".USER_ID."', '{$travelDate}', '{$number_of_days}', '{$number_of_adults}', '{$number_of_childrens}', '{$number_of_infants}', '{$current_location}', '{$pickup_location}', '{$drop_location}', '{$requirements}', '1')");

                                    } else {

                                        $createEnquiryProfile=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."enquiry_profile SET status='0' WHERE enquiry_profile_ref_id='{$leadId}'");

                                        $createEnquiryProfile=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."enquiry_profile(enquiry_profile_ref_id, enquiry_profile_agent, date_of_travel, number_of_days, number_of_adults, number_of_childrens, number_of_infants, current_location, pickup_location, drop_location, requirements, status) VALUES('{$leadId}', '".USER_ID."', '{$travelDate}', '{$number_of_days}', '{$number_of_adults}', '{$number_of_childrens}', '{$number_of_infants}', '{$current_location}', '{$pickup_location}', '{$drop_location}', '{$requirements}', '1')");

                                    }
                                }

                                if ($updateEnquiry && $enquiryHistory && $createEnquiryProfile) {
                                    $status='success';
                                    $msg='Enquiry update successfully';
                                    mysqli_query($connection, "COMMIT");
                                } else {
                                    $status='error';
                                    $msg='Something went wrong while updating enquiry';
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
        $msg='Please enter first name';
    }

    $result = array(
        'status'=>$status,
        'msg'=>$msg,
        'leadId' => $leadId
    );

    echo json_encode($result, TRUE);
    exit();