<?php

    function request($connection, $data) {
        
        extract($data);

        if(isset($self)) {

            $details=$details.'. I am also avilable in pangat';

        }

        $bookingDate=date('Y-m-d', strtotime($bookingDate));

        if(mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."booking(booking_site_id, booking_date, booking_time, booking_service, booking_details, booking_type, booking_status) VALUES(".SITE_ID.", '{$bookingDate}', '{$bookingTime}', 'pangat', '{$details}', 'pangat', 0)")) {

            $status=200;
            $msg='Pangat request received successfully';

        } else {

            $msg='Somthing is wrong while trying to book pangat';

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
            'availablity' => ((isset($response)) ? $response : '' )
        );
    
        echo json_encode($result, TRUE);

    }