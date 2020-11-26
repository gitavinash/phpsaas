<?php

    require_once LOGIN_CHECK;

    function cancel($connection, $data) {

        extract($data);

        if(isset($id)) {

            if($id!='') {

                $id=base64_decode($id);
                
                if($checkEnquiry=mysqli_query($connection, "SELECT booking_enquiry_id, booking_date, booking_time, enquiry_email as email, enquiry_mobile as mobile FROM isw_booking INNER JOIN isw_enquiry ON booking_enquiry_id=enquiry_id WHERE booking_enquiry_id={$id} AND booking_site_id=".APP_SITE_ID)) {

                    if(mysqli_num_rows($checkEnquiry)!=0) {
                    
                        mysqli_query($connection, "START TRANSACTION");
                    
                        $updateEnquiryStatus=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."enquiry SET enquiry_status=2 WHERE enquiry_id='{$id}' AND enquiry_site_id=".APP_SITE_ID);
                    
                        $updateBookingStatus=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."booking SET booking_status=2 WHERE booking_enquiry_id='{$id}' AND booking_site_id=".APP_SITE_ID);
                    
                        if($updateEnquiryStatus && $updateBookingStatus) {
                    
                            $status=200;
                            $msg='Booking is canceled successfully';
                            mysqli_query($connection, "COMMIT");
                    
                        } else {
                    
                            $msg='Something is wrong while trying to cancel booking';
                            mysqli_query($connection, "ROLLBACK");
                    
                        }
                    
                    } else {
                    
                        $msg='Invalid booking id';
                    
                    }
                    
                }

            } else {

                $msg='Booking id is required';

            }

        } else {

            $msg='Booking id parameters is required';

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
            'mobile' => ((isset($mobile)) ? $mobile : '' )
        );
    
        echo json_encode($result, TRUE);

    }