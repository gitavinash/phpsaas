<?php

    function blockMonth($connection, $data) {
        
        extract($data);

        $noOfDaysInMonth=date('t', strtotime($other));

        $startDate=date("Y-m-01", strtotime($other));
        $endDate=date("Y-m-{$noOfDaysInMonth}", strtotime($other));

        // Start MySQL transaction to execute the complete date range blocking

        mysqli_query($connection, "START TRANSACTION");

        for($startDate; $startDate<=$endDate; $startDate++) {

            if($checkDate=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."booking SET booking_status=4 WHERE booking_date='{$startDate}'")) {

                // Update date on block months

                if($blockDate=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."booking(booking_site_id, booking_date, booking_details, booking_status) VALUES(".APP_SITE_ID.", '{$startDate}', 'block by month', 4)")) {

                    $msg=date('jS M Y', strtotime($startDate))." has blocked successfully";

                } else {

                    $msg='Something is wrong while blocking the date';
                    $errorFlag=TRUE;

                }

            } else {

                $msg='Something is wrong while trying to update date';
                $errorFlag=TRUE;

            }

        }

        if(!isset($errorFlag)) {

            $status=200;
            $msg=date('F Y', strtotime($other)).' block successfully';
            mysqli_query($connection, "COMMIT");

        } else {

            mysqli_query($connection, "ROLLBACK");

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Something is wrong while trying to block date' ),
        );
    
        echo json_encode($result, TRUE);

    }