<?php

    if($country=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."country ORDER BY country_name ASC")) {

        if(mysqli_num_rows($country)!=0) {

            while($countryInfo=mysqli_fetch_assoc($country)) {

                extract($countryInfo);
                
                $countryData[]=array(
                    'id' => $country_id,
                    'name' => $country_name
                );

                $status=200;
                $msg='Country data fetch successfully';

            }

        } else {

            $msg='Not Found';

        }

    }
    
    $result = array(
        'status' => $status,
        'msg' => ((isset($msg)) ? $msg : ''),
        'data' => ((isset($countryData)) ? $countryData : ''),
    );

    echo json_encode($result, TRUE);
    exit();