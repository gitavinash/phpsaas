<?php

    require_once LOGIN_CHECK;
    
    function ads($connection, $data) {

        extract($data);

        if($updateFile=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."site_setting SET site_ads_txt='{$adsData}' WHERE site_setting_id=".SITE_ID)) {

            $status='success';
            $msg='Ads.txt file update successfully';
    
        }
    
        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Unable to update Ads.txt' )
        );
    
        echo json_encode($result, TRUE);

    }