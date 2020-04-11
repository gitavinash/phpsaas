<?php

    if($checkDevice=mysqli_query($connection, "SELECT device_token FROM ".TABLE_PREFIX."device WHERE device_token='{$token}'")) {

        if(mysqli_num_rows($checkDevice)==0) {

            if($registerDevice=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."device(device_site_id, device_token, device_type, device_status) VALUES(".SITE_ID.", '{$token}', '{$device}', 1)")) {

                $status=200;
                $msg='Device is registered successfully';

            } else {
                
                $msg='Something is wrong while registering new device';

            }

        } else {

            $msg='Device is already registered';

        }

    }

    $result=array(
        'status' => ((isset($status)) ? $status : 206 ),
        'msg' => ((isset($msg)) ? $msg : SITE_VERSION ),
    );

    echo json_encode($result, TRUE);