<?php

    function unblockDate($connection, $data) {
        
        extract($data);

        $bookingDate=date('Y-m-d', strtotime($date));

        if($checkDate=mysqli_query($connection, "SELECT booking_date FROM ".TABLE_PREFIX."booking WHERE booking_date='{$bookingDate}' AND booking_status=3 OR booking_status=4")) {

            if(mysqli_num_rows($checkDate)!=0) {

                if(mysqli_query($connection, "UPDATE ".TABLE_PREFIX."booking SET booking_status=2 WHERE booking_date='{$bookingDate}'")) {

                   $status=200;
                   date('jS M Y', strtotime($bookingDate))." has unblock successfully";

                }

            }

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Something went wrong while blocking the date' ),
        );
    
        echo json_encode($result, TRUE);

    }