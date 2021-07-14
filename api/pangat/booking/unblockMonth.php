<?php

    function unblockMonth($connection, $data) {
        
        extract($data);

        $noOfDaysInMonth=date('t', strtotime($other));

        $startDate=date("Y-m-01", strtotime($other));
        $endDate=date("Y-m-{$noOfDaysInMonth}", strtotime($other));
        
        // Start MySQL transaction to execute the complete date range blocking

        mysqli_query($connection, "START TRANSACTION");

        for($startDate; $startDate<=$endDate; $startDate++) {

            if($checkDate=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."booking SET booking_status=2, booking_details='' WHERE booking_date='{$startDate}'")) {

                // unblock month dates
                
                $msg=date('jS M Y', strtotime($startDate))." has blocked successfully";

            } else {

                $msg='Something is wrong while trying to update date';
                $errorFlag=TRUE;

            }

        }

        if(!isset($errorFlag)) {

            $status=200;
            $msg=date('F Y', strtotime($other)).' unblock successfully';
            mysqli_query($connection, "COMMIT");

        } else {

            mysqli_query($connection, "ROLLBACK");

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Something is wrong while trying to unblock month' ),
        );
    
        echo json_encode($result, TRUE);

    }