<?php

    /** Setting for how many months are visible in calender */

    function startDate($connection, $data) {

        extract($data);

        $calenderStartDate = date('Y-m-d', strtotime($other));

        // Check calender start is defined or not

        if($checkCalenderStartDate=mysqli_query($connection, "SELECT site_configuration_key FROM ".TABLE_PREFIX."site_configuration WHERE site_configuration_key='calender_start' AND site_configuration_site_id=".SITE_ID)) {

            if(mysqli_num_rows($checkCalenderStartDate)!=0) {

                // Update calender start variable 

                if($updateCalenderStartDate=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."site_configuration SET site_configuration_value='{$calenderStartDate}' WHERE site_configuration_key='calender_start' AND site_configuration_site_id=".SITE_ID)) {

                    $status=200;
                    $msg='Calender start date update successfully';

                }

            } else {

                // Insert new calender start variable

                if($insertalenderStartDate=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."site_configuration(site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status, site_configuration_global) VALUES(".SITE_ID.", 'calender_start', '{$calenderStartDate}', 1, 1)")) {

                    $status=200;
                    $msg='Calender start date add successfully';
                    
                }

            }

        }
            
        $result = array(
            'status' => (isset($status)) ? $status : 'error',
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' )
        );

        echo json_encode($result, TRUE);

    }