<?php

    /** Setting for how many months are visible in calender */

    function months($connection, $data) {

        extract($data);

        if($month!='' && $month>0) {

            /** Check month range setting is already defined or not */

            if($checkSetting=mysqli_query($connection, "SELECT site_configuration_id FROm ".TABLE_PREFIX."site_configuration WHERE site_configuration_site_id=".APP_SITE_ID." AND site_configuration_key='calender_month_visible'")) {

                if(mysqli_num_rows($checkSetting)!=0) {

                    extract(mysqli_fetch_assoc($checkSetting));

                    /** Update query of month setting */

                    $query="UPDATE ".TABLE_PREFIX."site_configuration SET site_configuration_value='{$month}', site_configuration_global=1 WHERE site_configuration_id='{$site_configuration_id}'";

                } else {

                    /** Insert query of month setting */

                    $query="INSERT INTO ".TABLE_PREFIX."site_configuration(site_configuration_site_id, site_configuration_name, site_configuration_value, site_configuration_global, site_configuration_status) VALUES(".APP_SITE_ID.", 'calender_month_visible', '{$month}', 1, 1)";

                }

            }

            /** RUn mysql query to update the calender month setting */

            if(mysqli_query($connection, $query)) {

                $status=200;
                $msg='Calender setting update successfully';

            } else {

                $msg='Something is wrong while updating the calender setting';

            }

        } else {

            $msg='Please enter a valid month in number';

        }
            
        $result = array(
            'status' => (isset($status)) ? $status : 'error',
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' )
        );

        echo json_encode($result, TRUE);

    }