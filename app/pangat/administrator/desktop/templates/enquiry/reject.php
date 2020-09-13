<?php

    if(isset($enquiry_id)) {

        mysqli_query($connection, "START TRANSACTION");

        $updateEnquiryStatus=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."enquiry SET enquiry_status=2 WHERE enquiry_id='{$enquiry_id}'");

        $updateBookingStatus=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."booking SET booking_status=2 WHERE booking_enquiry_id='{$enquiry_id}'");


        if($updateEnquiryStatus && $updateBookingStatus) {

            mysqli_query($connection, "COMMIT");

            header("Location: /dashboard/enquiry/?type=list&status=all");
            exit();

        } else {

            echo 'Something is wrong while trying to update enquiry status';
            mysqli_query($connection, "ROLLBACK");

        }

    }