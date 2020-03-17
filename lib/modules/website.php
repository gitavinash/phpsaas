<?php

    function add($connection, $data) {

        extract($data);
        global $table_prefix;

        if($first_name=='' || $last_name=='') {

            header("Location: /dashboard/user/?type=add&msg=Please enter a first or last name&error=error");
            exit();

        }

        if($email=='' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {

            header("Location: /dashboard/user/?type=add&msg=Please enter a valid email&error=error");
            exit();

        }

        /** Generate automatic password of 8 chatracter */
        $password=substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789!@#$%&()_+=-<>/?'), 0, 8);

        /** Generating salt key of the user */
        $saltKey=hash('sha256', uniqid(mt_rand(), true).$data['email'].substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?'), 0, 12).time());
        $dbPassword=hash('sha256', $saltKey.$password);
        
        
        $username=strtolower(str_replace(' ', '', $first_name.$last_name));

        if($checkUsername=mysqli_query($connection, "SELECT user_username FROM ".TABLE_PREFIX."user WHERE user_username='{$username}'")) {
            
            if(mysqli_num_rows($checkUsername)!=0) {
                
                $username=strtolower($first_name).time();

            }

        }

        if($checkEmail=mysqli_query($connection, "SELECT user_email FROM ".TABLE_PREFIX."user WHERE user_email='{$email}'")) {
            
            if(mysqli_num_rows($checkEmail)!=0) {
                
                header("Location: /dashboard/app/?type=list&msg=Email is already use&error=error");
                exit();

            }

        }
        
        $currentDate = date("Y-m-d");
        $expireDate = strtotime(date("Y-m-d", strtotime($currentDate)) . " +1 year");

        $site_url=$username.'.'.SITE_DOMAIN;

        /** Start mysql transaction */
        $verifyURL=$site_url.'/verify/email.php?user_id='.$saltKey.'&email='.base64_encode($email);

        $website_user_id=((isset($website_user_id)) ? $website_user_id :  USER_ID );

        mysqli_query($connection, "START TRANSACTION");
            
        $addUser=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."user(user_parent_id, user_username, user_email, user_password, user_salt_key, user_type, user_status, user_reg_time, user_reg_ip) VALUES('{$website_user_id}', '{$username}', '{$email}', '{$dbPassword}', '{$saltKey}', 'administrator', 0, NOW(), '".IP."')");
            
        $newUserId=mysqli_insert_id($connection);
            
        $createProfile=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."profile(profile_owner_id, profile_first_name, profile_last_name, profile_display_name, profile_mobile, profile_email) VALUES('{$newUserId}', '{$first_name}', '{$last_name}', '".$first_name.' '.$last_name."', '{$mobile}', '{$email}')");

        $createNewWebsite=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."site(site_owner_id, site_lang, site_domain, site_url, site_name, site_app, site_indexing, site_status, site_expire_date) VALUES('{$newUserId}', 'en',  '".SITE_DOMAIN."', '{$site_url}', '{$website_title}', '{$app}', 1, 1, '{$expireDate}')");

        $websiteID=mysqli_insert_id($connection);

        $userSiteAccess=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."site_access(site_access_site_id, site_access_user_id, site_access_status) VALUES('{$websiteID}', '{$newUserId}', 1)");

        $websiteSetting=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."site_setting(site_setting_id) VALUES('{$websiteID}')");

        if($addUser && $createProfile && $createNewWebsite && $userSiteAccess && $websiteSetting) {

            /** Add all data into database */

            require_once EMAIL_TEMPLATE.'add-new-user.php';

            mysqli_query($connection, 'COMMIT');
            header("Location: /dashboard/app/?type=list&msg=App is Install Successfully&error=success");

        } else {

            /** Rollback mysql query if query is fail */
            mysqli_query($connection, 'ROLLBACK');
            echo 'something is wrong while installing app';

        }

    }