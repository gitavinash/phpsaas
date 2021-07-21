<?php

    require_once LOGIN_CHECK;

    function confirm($connection, $data) {

        extract($data);

        if(isset($id)) {

            if($id!='') {

                $id=base64_decode($id);

                if($checkEnquiry=mysqli_query($connection, "SELECT booking_enquiry_id, booking_date, booking_time, CONCAT(enquiry_first_name, enquiry_last_name) as enquiry_display_name, enquiry_first_name, enquiry_last_name, enquiry_email as email, enquiry_mobile as mobile, enquiry_query as guest FROM isw_booking INNER JOIN isw_enquiry ON booking_enquiry_id=enquiry_id WHERE booking_enquiry_id={$id} AND booking_site_id=".APP_SITE_ID)) {

                    if(mysqli_num_rows($checkEnquiry)!=0) {
                    
                        extract(mysqli_fetch_assoc($checkEnquiry));
                    
                        mysqli_query($connection, "START TRANSACTION");
                    
                        $updateEnquiryStatus=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."enquiry SET enquiry_status=1 WHERE enquiry_id='{$id}' AND enquiry_site_id=".APP_SITE_ID);
                    
                        $updateBookingStatus=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."booking SET booking_status=1 WHERE booking_enquiry_id='{$id}' AND booking_site_id=".APP_SITE_ID);
                    
                        if($checkOtherBooking=mysqli_query($connection, "SELECT booking_enquiry_id FROM ".TABLE_PREFIX."booking WHERE booking_date='{$date}' AND booking_time='{$time}' AND booking_enquiry_id!='{$booking_enquiry_id}' AND booking_site_id=".APP_SITE_ID)) {
                    
                            if(mysqli_num_rows($checkOtherBooking)!=0) {
                    
                                while($bookingData=mysqli_fetch_assoc($checkOtherBooking)) {
                    
                                    extract($bookingData);
                    
                                    $updateOtherEnquiryStatus=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."enquiry SET enquiry_status=2 WHERE enquiry_id='{$booking_enquiry_id}' AND enquiry_site_id=".APP_SITE_ID);
                    
                                    $updateOtherBookingStatus=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."booking SET booking_status=2 WHERE booking_id='{$booking_enquiry_id}' AND booking_site_id=".APP_SITE_ID);
                    
                                }
                    
                            } else {
                    
                                $updateOtherEnquiryStatus=true;
                                $updateOtherBookingStatus=true;
                    
                            }
                    
                        } else {
                    
                            $msg=mysql_error($connection);
                    
                        }
                    
                    
                        if($updateEnquiryStatus && $updateBookingStatus && $updateOtherBookingStatus && $updateOtherEnquiryStatus) {
                    
                            $status=200;
                            $msg='Booking is confirmed successfully';

                            mysqli_query($connection, "COMMIT");
                    
                            /** Send Email and SMS to User of confirmation */
                    
                            include APP_EMAIL_TEMPLATE.'order/booking-confirmation.php';
                            include APP_SMS_TEMPLATE.'order/booking-confirmation.php';

                            // Send copy email is admin email notification is enable 

                            if(defined('ADMIN_EMAIL_NOTIFICATION')) {
                                
                                if(ADMIN_EMAIL_NOTIFICATION==1) {

                                    // Send booking confirmation email notice to admin

                                    include ADMIN_EMAIL_TEMPLATE.'order/booking-confirmation.php';
                                    include ADMIN_SMS_TEMPLATE.'order/booking-confirmation.php';

                                }

                            }
                    
                        } else {
                    
                            $msg='Something is wrong while trying to update enquiry status';
                            mysqli_query($connection, "ROLLBACK");
                    
                        }
                    
                    } else {
                    
                        $msg="Invalid enquiry details found";
                    
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