<?php

    function blockDate($connection, $data) {
        
        extract($data);

        $updateBlockSlots=(($slot=='all') ? '' : ", booking_time='{$slot}'");
        $insertBlockSlots=(($slot=='all') ? '' : $slot);
        
        $blockSlotsStatus=(($slot=='all') ? '4' : "5");

        // mysqli_query($connection, "START TRANSACTION");
        
        $bookingDate=date('Y-m-d', strtotime($date));

        if($checkDate=mysqli_query($connection, "SELECT booking_date FROM ".TABLE_PREFIX."booking WHERE booking_date='{$bookingDate}' AND (booking_status!={$blockSlotsStatus} AND booking_status!=2)")) {

            if(mysqli_num_rows($checkDate)!=0) {

                /** Show warning */

                $msg=date('jS M Y', strtotime($bookingDate))." is already in used";

            } else {

                if($checkDate=mysqli_query($connection, "SELECT booking_date FROM ".TABLE_PREFIX."booking WHERE booking_date='{$bookingDate}' AND booking_status={$blockSlotsStatus}")) {

                    if(mysqli_num_rows($checkDate)!=0) {
        
                        /** Update data */
        
                        if($blockDate=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."booking SET booking_status={$blockSlotsStatus} {$updateBlockSlots} WHERE booking_date='{$bookingDate}'")) {

                            $status=200;
                            $msg=date('jS M Y', strtotime($bookingDate))." has blocked successfully";

                        } else {

                            $msg='Something is wrong while blocking the date';

                        }
        
                    } else {
        
                        /** Block calender date */

                        if($blockDate=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."booking(booking_site_id, booking_date, booking_time, booking_status) VALUES(".APP_SITE_ID.", '{$bookingDate}', '{$insertBlockSlots}', {$blockSlotsStatus})")) {

                            $status=200;
                            $msg=date('jS M Y', strtotime($bookingDate))." has blocked successfully";

                        } else {

                            $msg='Something is wrong while blocking the date';

                        }
        
                    }
        
                }

            }

        } else {

            echo mysqli_error($connection);

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Something is wrong while trying to block date' ),
        );
    
        echo json_encode($result, TRUE);

    }