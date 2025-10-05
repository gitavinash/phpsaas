<?php

function registerUserByMobile($connection, $mobile, $name = 'user', $emailDomain = '@noemail.com')
{

    mysqli_query($connection, "START TRANSACTION");

    /* Encrypt password with salt key */

    $email = $mobile . $emailDomain;

    $saltKey = hash('sha256', uniqid(mt_rand(), true) . $email . substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?'), 0, 12) . time());
    $enc_password = hash('sha256', $saltKey . 'Qwerty!@345');

    $name = explode(' ', $name);
    $first_name = $name[0];
    $last_name = end($name);
    $profile_display_name = $first_name . ' ' . $last_name;

    /** Checking Username is avilable or not */

    $username = strtolower(str_replace(' ', '', $first_name . $last_name));

    if ($checkUsername = mysqli_query($connection, "SELECT user_username FROM " . TABLE_PREFIX . "user WHERE user_username='{$username}'")) {
        if (mysqli_num_rows($checkUsername) != 0) {
            $username = strtolower($first_name) . time();
        }
    }

    $siteURL = $username . '.' . SITE_DOMAIN;
    $userType = ((isset($reg_type)) ? $reg_type : 'subscriber');

    $insertUser = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "user(user_username, user_email, user_mobile, user_password, user_salt_key, user_reg_time, user_reg_ip) VALUES('{$username}', '{$email}', '{$mobile}', '{$enc_password}', '$saltKey', NOW(), '" . IP . "')");
    $userID = mysqli_insert_id($connection);

    $insertProfile = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "profile(profile_user_id, profile_first_name, profile_last_name, profile_mobile, profile_display_name, profile_email) VALUES('{$userID}', '{$first_name}', '{$last_name}', '{$mobile}', '{$profile_display_name}', '{$email}')");

    if ($insertUser && $insertProfile) {
        mysqli_query($connection, "COMMIT");
    } else {
        mysqli_query($connection, "ROLLBACK");
        $userID = '';
    }
    return $userID;
}