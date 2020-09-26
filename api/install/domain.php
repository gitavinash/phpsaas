<?php

	/* Set up default system application */

	function domain($connection, $data) {

        extract($data);

		$expireDate=date('Y-m-d', strtotime('+1 years'));
        $theme=((isset($theme)) ? $theme : 1 );
        $package=((isset($package)) ? $package : 1 );
        $checkUser=mysqli_query($connection, "SELECT user_id, site_id FROM ".TABLE_PREFIX."user INNER JOIN ".TABLE_PREFIX."site ON site_owner_id=user_id WHERE user_username='{$username}'");
    
        if(mysqli_num_rows($checkUser)!=0) {

            extract(mysqli_fetch_assoc($checkUser));

            $checkDomain=mysqli_query($connection, "SELECT site_domain FROM ".TABLE_PREFIX."site WHERE site_domain='{$domain}'");

            if(mysqli_num_rows($checkDomain)==0) {

                mysqli_query($connection, "START TRANSACTION");

                $updateSite=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."site SET site_domain='{$domain}', site_app_id='{$app}', site_theme_id=1, site_status=1, site_expire_date='{$expireDate}' WHERE site_id='{$site_id}'");

                $updateSiteSetting=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."site_setting SET site_custom_domain=1 WHERE site_setting_id='{$site_id}'");

                $updateSiteApp=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."site_installation SET site_installation_site_id='{$site_id}', site_installation_user_id='{$user_id}', site_installation_package_id={$package}, site_installation_expiry_date='{$expireDate}' WHERE site_installation_id='{$site_id}'");

                if($updateSite && $updateSiteSetting && $updateSiteApp) {

                    $status='success';
                    $msg='Website setup is successfully completed';
                    $url='/login/';

                    mysqli_query($connection, "COMMIT");

                } else {

                    echo $updateSite.' && '.$updateSiteSetting.' && '.$updateSiteApp;
                    exit();

                    $status='error';
                    $msg='System is unable to complete the task';

                    mysqli_query($connection, "ROLLBACK");

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