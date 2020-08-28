<?php

    require_once LOGIN_CHECK;

    function update($connection, $data) {

        extract($data);

        mysqli_query($connection, "START TRANSACTION");

        /** Validate country field value */

        if($country!='') {

            $country=explode('_', $country);
            $updateCountry=" address_country_id={$country[0]}";

            if($state!='') {

                /** Validate state field value */

                $state=explode('_', $state);
                $updateState=" address_state_id={$state[0]}";

                if($city!='') {

                    /** Validate city field value */

                    $city=explode('_', $city);
                    $updateCity=" address_city_id={$city[0]}";

                    if($pin_code!='') {

                        /** Validate pin code field value */

                        $pin_code=explode('_', $pin_code);
                        $updatePinCode=" address_pin_code={$pin_code[0]}";


                    } else {
            
                        $msg='Please enter pin code';
            
                    }

                } else {
        
                    $msg='Please select city';
        
                }

            } else {
    
                $msg='Please select state';
    
            }

        } else {

            $msg='Please select country';

        }
        
        $result=array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Something is wrong while adding new building' )
        );
    
        echo json_encode($result, TRUE);

    }