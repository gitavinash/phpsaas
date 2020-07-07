<?php

    function profile($connection, $data) {

        require_once LOGIN_CHECK;

        extract($data);

        if($checkMobile=mysqli_query($connection, "SELECT profile_id FROM ".TABLE_PREFIX."profile WHERE profile_mobile='{$mobile}'")) {

            mysqli_query($connection, "START TRANSACTION");

            $updateProfile=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."profile SET profile_first_name='{$first_name}', profile_last_name='{$last_name}', profile_display_name='{$display_name}', profile_alt_email='{$alt_email}', profile_mobile='{$mobile}', profile_alt_mobile='{$alt_mobile}' WHERE profile_owner_id=".USER_ID);

            if($updateProfile) {

                $status='success';
                $msg='Your profile information has been saved';
                mysqli_query($connection, "COMMIT");

            } else {
                
                $status='error';
                $msg='Technical error while updating information';
                mysqli_error($connection, "ROLLBACK");

            }

        } else {

            $status='warning';
            $msg='This mobile number is already in use.';

        }

        $result=array(
            'status' => ((isset($status)) ? $status : 'error'),
            'msg' => ((isset($msg)) ? $msg : 'Something is wrong while trying to update')
        );

        echo json_encode($result, TRUE);

    }