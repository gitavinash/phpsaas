<?php


    // Generate tempararoy username

    $username=time();
    $profileDisplayName=$firstName.' '.$lastName;

    // Check if this software is fresh install or not

    $site_id=(isset($installedSiteID) ? $installedSiteID : $site_id );
    $user_type=(isset($installedSiteID) ? 'administrator' : $user_type );
    $user_refer_id=(isset($affilate_user_id) ? $affilate_user_id : 0 );

	/* Encrypt password with salt key */

	$saltKey=hash('sha256', uniqid(mt_rand(), true).$email.substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?'), 0, 12).time());
	$enc_password=hash('sha256', $saltKey.$password);

    // Create new user

    $createNewUser=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."user(user_username, user_email, user_mobile, user_password, user_salt_key, user_status, user_reg_time, user_reg_ip, user_refer_id) VALUES('{$username}', '{$email}', '{$mobile}', '{$enc_password}', '{$saltKey}', 1, '".date('Y-m-d H:i:s')."', '".IP."', $user_refer_id)");
    $user_id=mysqli_insert_id($connection);

    // Create user profile

    $createNewUserProfile=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."profile(profile_user_id, profile_first_name, profile_last_name, profile_display_name, profile_mobile, profile_email) VALUES('{$user_id}', '{$firstName}', '{$lastName}', '{$profileDisplayName}', '{$mobile}', '{$email}')");


    // Allow site access to user

    $createNewSiteAccess=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."site_access(site_access_site_id, site_access_user_id, site_access_type, site_access_status) VALUES('{$site_id}', '{$user_id}', '{$user_type}', 1)");