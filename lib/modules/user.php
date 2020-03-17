<?php

    require_once MODULES.'isLogin.php';

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

        /** Start mysql transaction */
        $verifyURL=SITE_DOMAIN.'/verify/email.php?user_id='.$saltKey.'&email='.base64_encode($email);

        mysqli_query($connection, "START TRANSACTION");
        
        $addUser=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."user(user_parent_id, user_username, user_email, user_password, user_salt_key, user_type, user_status, user_reg_time, user_reg_ip) VALUES(".USER_ID.", '{$username}', '{$email}', '{$dbPassword}', '{$saltKey}', '{$user_type}', 1, NOW(), '".IP."')");
        
        $newUserId=mysqli_insert_id($connection);
        
        $createProfile=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."profile(profile_owner_id, profile_first_name, profile_last_name, profile_display_name, profile_mobile, profile_email) VALUES('{$newUserId}', '{$first_name}', '{$last_name}', '".$first_name.' '.$last_name."', '{$mobile}', '{$email}')");

        $userSiteAccess=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."site_access(site_access_site_id, site_access_user_id, site_access_status) VALUES(".SITE_ID.", '{$newUserId}', 1)");

        if($addUser && $createProfile && $userSiteAccess) {

            /** Add all data into database */

            require_once EMAIL_TEMPLATE.'add-new-user.php';

            mysqli_query($connection, 'COMMIT');
            header("Location: /dashboard/user/?type=list&msg=New use add successfully&error=success");

        } else {

            /** Rollback mysql query if query is fail */
            mysqli_query($connection, 'ROLLBACK');
            echo 'something is wrong while creating new user';

        }

        exit();

    }