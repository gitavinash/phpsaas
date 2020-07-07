<?php

    function robots($connection, $data) {
        
        require_once LOGIN_CHECK;

        extract($data);

        if($updateFile=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."site_setting SET site_robots_txt='{$robotsData}' WHERE site_setting_id=".SITE_ID)) {

            $status='success';
            $msg='Robots.txt file update successfully';

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Unable to update Robots.txt' )
        );

        echo json_encode($result, TRUE);

    }