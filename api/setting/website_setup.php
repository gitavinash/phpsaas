<?php

	/* Set up default system application */

	function website_setup($connection, $data) {

        extract($data);

        $checkUser=mysqli_query($connection, "SELECT user_id FROM ".TABLE_PREFIX."user WHERE user_username='{$username}'");
    
        if(mysqli_num_rows($checkUser)!=0) {

            extract(mysqli_fetch_assoc($checkUser));

            $checkDomain=mysqli_query($connection, "SELECT site_domain FROM ".TABLE_PREFIX."site WHERE site_domain='{$domain}'");

            if(mysqli_num_rows($checkDomain)==0) {

                if($updateSite=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."site SET site_domain='{$domain}' WHERE site_owner_id='{$user_id}'")) {

                    $status='success';
                    $msg='Website setup is successfully completed';
                    $url='/dashboard/';

                } else {

                    $status='error';
                    $msg='System is unable to complete the task';

                }

                

            } else {

                $status='error';
                $msg='This Domain is already in used';

            }

        } else {

            $msg='error';
            $status='Invalid setup id found';

        }

        $result=array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Something is wrong while setup your website' ),
            'url' => ((isset($url)) ? $url : '#' )
        );

        echo json_encode($result, TRUE);

    }