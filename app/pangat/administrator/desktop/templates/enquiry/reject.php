<?php

    if(isset($enquiry_id)) {

        if($checkEnquiry=mysqli_query($connection, "SELECT booking_enquiry_id, booking_date, booking_time, enquiry_email as email, enquiry_mobile as mobile FROM isw_booking INNER JOIN isw_enquiry ON booking_enquiry_id=enquiry_id WHERE booking_enquiry_id={$enquiry_id} AND booking_site_id=".APP_SITE_ID)) {

        if(mysqli_num_rows($checkEnquiry)!=0) {

            mysqli_query($connection, "START TRANSACTION");

            $updateEnquiryStatus=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."enquiry SET enquiry_status=2 WHERE enquiry_id='{$enquiry_id}' AND enquiry_site_id=".APP_SITE_ID);

            $updateBookingStatus=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."booking SET booking_status=2 WHERE booking_enquiry_id='{$enquiry_id}' AND booking_site_id=".APP_SITE_ID);

            if($updateEnquiryStatus && $updateBookingStatus) {

                mysqli_query($connection, "COMMIT");

                header("Location: /dashboard/enquiry/?type=list&status=all");
                exit();

            } else {

                echo 'Something is wrong while trying to update enquiry status';
                mysqli_query($connection, "ROLLBACK");

            }

        } else {

            echo 'Invalid booking id';

        }

    }