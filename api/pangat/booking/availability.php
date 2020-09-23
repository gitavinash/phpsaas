<?php

    /**
     * 
     * Availability status
     * 
     * 0 -> Request received but booking not confirmed
     * 1 -> Slot not available
     * 2 -> Slot available
    */

    function availability($connection, $data) {
        
        extract($data);

        $availablityDate=($date!='' ? date('01-M-Y', strtotime($date)) : date('01-M-Y'));

        for($i=0; $i<date('t', strtotime($availablityDate)); $i++) {

            $calenderDate=date('Y-m-d', strtotime("+{$i} days", strtotime($availablityDate)));

            if(date('Y-m-d')<=$calenderDate) {

                if($checkDateAvailablity=mysqli_query($connection, "SELECT booking_date, booking_time, booking_status FROM ".TABLE_PREFIX."booking WHERE booking_date='{$calenderDate}'")) {

                    if(mysqli_num_rows($checkDateAvailablity)!=0) {
    
                        while($pangatStatus=mysqli_fetch_assoc($checkDateAvailablity)) {

                            extract($pangatStatus);

                            if($booking_time=='breakfast') {

                                $breakfast=intval($booking_status);

                            }

                            if($booking_time=='lunch') {

                                $lunch=intval($booking_status);

                            }

                            if($booking_time=='dinner') {

                                $dinner=intval($booking_status);

                            }

                            if($breakfast==1 && $lunch==1 && $dinner==1) {
                                
                                $bookingAllowed=0;

                            } else {
                                
                                $bookingAllowed=2;

                            }

                        }

                    } else {
    
                        $bookingAllowed=2;
                        $breakfast=2;
                        $lunch=2;
                        $dinner=2;
    
                    }

                    $availablityData[]=array(
                        'full_date' => date('d-m-Y', strtotime($calenderDate)),
                        'short_date' => date('D, d', strtotime($calenderDate)),
                        'bookingAllowed' => $bookingAllowed,
                        'breakfast' => $breakfast,
                        'lunch' => $lunch,
                        'dinner' => $dinner
                    );
    
                }

            } else {

                $availablityData[]=array(
                    'full_date' => date('d-m-Y', strtotime($calenderDate)),
                    'short_date' => date('D, d', strtotime($calenderDate)),
                    'bookingAllowed' => 1,
                    'breakfast' => 1,
                    'lunch' => 1,
                    'dinner' => 1
                );

            }

            $status=200;
            $msg='Data fetch successfully';

        }
    
        $result = array(
            'status' => intval((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
            'current_month' => date('F Y', strtotime($calenderDate)),
            'previous_month' => ((date('Y-m-01', strtotime("-1 month", strtotime($availablityDate))) < date('Y-m-01')) ? '' : date('Y-m-01', strtotime("-1 month", strtotime($availablityDate))) ),
            'next_month' => date('Y-m-01', strtotime("+1 month", strtotime($availablityDate))),
            'availablity' => ((isset($availablityData)) ? $availablityData : '' )
        );

        echo json_encode($result, TRUE);

    }