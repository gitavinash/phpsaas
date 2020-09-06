<?php

    function availability($connection, $data) {
        
        extract($data);

        $availablityDate=((isset($date)) ? $date : date('Y-m-d'));

        for($i=1; $i<=14; $i++) {

            $calenderDate=date('Y-m-d', strtotime("+{$i} days", strtotime($availablityDate)));

            if($checkDateAvailablity=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."booking")) {

                if(mysqli_num_rows($checkDateAvailablity)!=0) {


                } else {

                    $bookingAllowed=1;
                    $breakfast=1;
                    $lunch=1;
                    $dinner=1;

                }

            }

            $status=200;
            $msg='Data fetch successfully';
            $response[]=array(
                'date' => date('D, d M', strtotime($calenderDate)),
                'bookingAllowed' => $bookingAllowed,
                'breakfast' => $breakfast,
                'lunch' => $lunch,
                'dinner' => $dinner,
            );

        }
    
        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
            'availablity' => ((isset($response)) ? $response : '' )
        );
    
        echo json_encode($result, TRUE);

    }