<?php

	/* register module */

	function register($connection, $data) {

        extract($data);
        
        if($checkEmail=mysqli_query($connection, "SELECT user_email FROM ".TABLE_PREFIX."user WHERE user_email='{$email}'")) {

            if(mysqli_num_rows($checkEmail)==0) {

                /** Check mobile number is available or not */

                if($checkMobile=mysqli_query($connection, "SELECT profile_mobile FROM ".TABLE_PREFIX."profile WHERE profile_mobile='{$mobile}'")) {

                    if(mysqli_num_rows($checkMobile)==0) {

                        /** Start the user registration process */

                        /* Encrypt password with salt key */

                        $saltKey=hash('sha256', uniqid(mt_rand(), true).$email.substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?'), 0, 12).time());
                        $enc_password=hash('sha256', $saltKey.$password);

                        $profile_display_name=$first_name.' '.$last_name;

                        /** Checking Username is avilable or not */

                        $username=strtolower(str_replace(' ', '', $first_name.$last_name));

                        if($checkUsername=mysqli_query($connection, "SELECT user_username FROM ".TABLE_PREFIX."user WHERE user_username='{$username}'")) {
                            if(mysqli_num_rows($checkUsername)!=0) {
                                $username=strtolower($first_name).time();
                            }
                        }


                        $siteURL=$username.'.'.SITE_DOMAIN;

                        mysqli_query($connection, "START TRANSACTION");

                        /* Insert data into user table */

                        $insertUser=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."user(user_username, user_email, user_password, user_salt_key, user_reg_time, user_reg_ip) VALUES('{$username}', '{$email}', '{$enc_password}', '$saltKey', NOW(), '{$_SERVER['REMOTE_ADDR']}')");
                        $userID=mysqli_insert_id($connection);

                        $insertSite=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."site(site_owner_id, site_url, site_status) VALUES('{$userID}', '{$siteURL}', 0)");
                        $siteID=mysqli_insert_id($connection);

                        $insertSiteApp=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."site_app(site_app_status) VALUES(1)");

                        $insertSiteAccess=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."site_access(site_access_site_id, site_access_user_id, site_access_type, site_access_status) VALUES('{$siteID}', '{$userID}', 'administrator', 1)");

                        $insertSiteSetting=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."site_setting(site_pagination_limit, site_sitemap_limit, site_feed_limit, site_image_quality, site_thumbnail_width, site_thumbnail_height, site_mainfest_json, site_robots_txt) VALUES(10, 10, 10, 60, 223, 96, '".json_encode(DEFAULT_MAINFEST_JSON, TRUE)."', '".DEFAULT_ROBOTS_TXT."')");

                        $insertProfile=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."profile(profile_user_id, profile_first_name, profile_last_name, profile_mobile, profile_display_name, profile_email) VALUES('{$userID}', '{$first_name}', '{$last_name}', '{$mobile}', '{$profile_display_name}', '{$email}')");

                        if($insertUser && $insertSite && $insertSiteApp && $insertSiteAccess && $insertSiteSetting && $insertProfile) {

                            mysqli_query($connection, 'COMMIT');

                            /** Send User's Email Verification Mail */

                            $verifyURL=SITE_DOMAIN.'/verify/email.php?user_id='.$saltKey.'&email='.base64_encode($email);

                            require_once EMAIL_TEMPLATE.'add-new-user.php';
                            
                            $status='success';
                            $msg='You are successfully registered';
                            $url='/website-setup.php?setup_id='.base64_encode($email);


                        } else {

                            echo "INSERT INTO ".TABLE_PREFIX."site(site_owner_id, site_status) VALUES('{$userID}', 0)";

                            //echo $insertUser.' && '.$insertSite.' && '.$insertSiteApp.' && '.$insertSiteAccess.' && '.$insertSiteSetting.' && '.$insertProfile;

                            $status='error';
                            $msg='Something is wrong while trying to register';
                            mysqli_query($connection, 'ROLLBACK');

                        }

                    } else {

                        $status='warning';
                        $msg='This mobile number is already in use';

                    }

                }

            } else {

                $status='warning';
                $msg='This email is already in use.';

            }

        }
		    
		$return=array(
			'status' => ((isset($status)) ? $status : 'error' ),
			'msg' => ((isset($msg)) ? $msg : 'Unkhown Error' ),
			'url' => ((isset($url)) ? $url : '#' )
        );

		echo json_encode($return, TRUE);
    
    }