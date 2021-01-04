<?php

    function mainfest($connection, $data) {
        
        require_once LOGIN_CHECK;

        extract($data);

        $maifestFile=json_encode($mainfestData, TRUE);

        if($updateFile=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."site_setting SET site_mainfest_json='{$maifestFile}' WHERE site_setting_site_id=".SITE_ID)) {

            $status='success';
            $msg='Mainfest.json file update successfully';
    
        }
    
        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Unable to update Mainfest.json' )
        );
    
        echo json_encode($result, TRUE);

    }