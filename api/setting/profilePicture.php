<?php

    function profilePicture($connection, $data) {

        require_once LOGIN_CHECK;

        require_once IMAGE_UPLOADER;

        extract($image);

        if($checkSiteAccess=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."site_access WHERE site_access_site_id=".SITE_ID." AND site_access_user_id=".USER_ID."")) {

            if(mysqli_num_rows($checkSiteAccess)==1) {

                $profile=SITE_URL.$profile;

                if($updateSiteIcon=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."profile SET profile_image='{$profile}' WHERE profile_owner_id=".USER_ID)) {
                    
                    $status='success';
                    $msg='Profile picture updated successfully';
                    
                    setcookie('user_image', base64_encode($profile), time()+30*24*60*60, '/');

                }

            } else {

                echo 'You do not have access permission';

            }

        } else {

            echo mysqli_error($connection);

        }

        $result=array(
            'status' => $status=((isset($status)) ? $status : 'error' ),
            'msg' => $msg=((isset($msg)) ? $msg : 'Something is wrong while updating site icon' )
        );

        echo json_encode($result, TRUE);

    }