<?php

    if(isset($state_id)) {

        if($city=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."city WHERE city_state_id='{$state_id}' ORDER BY city_name ASC")) {

            if(mysqli_num_rows($city)!=0) {
    
                while($cityInfo=mysqli_fetch_assoc($city)) {
    
                    extract($cityInfo);
                    
                    $cityData[]=array(
                        'id' => $city_id,
                        'name' => $city_name
                    );
    
                    $status=200;
                    $msg='City data fetch successfully';
    
                }
    
            } else {
    
                $msg='Not Found';
    
            }
    
        }

    } else {

        $msg='Invalid state id found';

    }
    
    $result = array(
        'status' => $status,
        'msg' => ((isset($msg)) ? $msg : ''),
        'cityData' => ((isset($cityData)) ? $cityData : ''),
    );

    echo json_encode($result, TRUE);
    exit();