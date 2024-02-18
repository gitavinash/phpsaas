<?php

    function icon($connection, $data) {

        require_once LOGIN_CHECK;

        require_once IMAGE_UPLOADER;

        

        if($checkSiteAccess=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."site_access WHERE site_access_site_id=".SITE_ID." AND site_access_user_id=".USER_ID."")) {

            if(mysqli_num_rows($checkSiteAccess)==1) {

                // $siteIcon=SITE_URL.$siteIcon;

                if($updateSiteIcon=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."site SET site_icon='{$siteIcon}' WHERE site_id=".SITE_ID)) {
                    $status='success';
                    $msg='Site Icon Updated Successfully';
                }

            } else {

                echo 'You do not have access permission';

            }

        } else {

            echo mysqli_error($connection);

        }

        $result=array(
            'status' => $status=((isset($status)) ? $status : 'error' ),
            'msg' => $msg=((isset($msg)) ? $msg : 'Something is wrong while updating site icon' ),
        );

        echo json_encode($result, TRUE);

    }