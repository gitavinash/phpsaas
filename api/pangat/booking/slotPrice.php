<?php

    function slotPrice($connection, $data) {
        
        extract($data);
        
        $price=explode('_',$other);
        $slotName=strtolower($price[0]);
        $price=$price[1];

        // Check slot is is defined or not

        if($checkCalenderStartDate=mysqli_query($connection, "SELECT site_configuration_key FROM ".TABLE_PREFIX."site_configuration WHERE site_configuration_key='{$slotName}_slot_price' AND site_configuration_site_id=".SITE_ID)) {

            if(mysqli_num_rows($checkCalenderStartDate)!=0) {

                // Update slot price

                if($updateCalenderStartDate=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."site_configuration SET site_configuration_value='{$price}' WHERE site_configuration_key='{$slotName}_slot_price' AND site_configuration_site_id=".SITE_ID)) {

                    $status=200;
                    $msg="{$slotName} price update successfully";

                }

            } else {

                // Insert new slot

                if($insertalenderStartDate=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."site_configuration(site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status, site_configuration_global) VALUES(".SITE_ID.", '{$slotName}_slot_price', '{$price}', 1, 1)")) {

                    $status=200;
                    $msg="{$slotName} price add successfully";
                    
                }

            }

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Something is wrong while trying to add or update slot' ),
        );
    
        echo json_encode($result, TRUE);

    }