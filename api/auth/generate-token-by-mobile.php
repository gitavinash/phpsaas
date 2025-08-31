<?php

mysqli_query($connection, "START TRANSACTION");

if ($checkUser = mysqli_query($connection, "SELECT user_id, user_salt_key, user_status, user_type_code as user_type, site_app_id, profile_display_name, profile_image, site_access_site_id, site_access_status, site_access_meta FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "profile ON user_id=profile_user_id INNER JOIN " . TABLE_PREFIX . "site_access ON site_access_user_id=user_id INNER JOIN " . TABLE_PREFIX . "site ON site_id=site_access_site_id INNER JOIN " . TABLE_PREFIX . "user_type ON user_type_code=site_access_type AND site_access_site_id=" . SITE_ID . " WHERE user_id='{$user_id}' LIMIT 1")) {

    if (mysqli_num_rows($checkUser) != 0) {

        extract(mysqli_fetch_assoc($checkUser));
        $genarateToken = true;
        $existingUser = true;
        /** User has access of this site */
    } else {


        if (filter_var($mobile, FILTER_VALIDATE_EMAIL)) {
            $checkUserCondition = "user_email='{$mobile}'";
        } elseif (preg_match('/^[6-9]\d{9}$/', $mobile)) {
            $checkUserCondition = "user_mobile='{$mobile}'";
        }

        $userEmail = ((isset($email)) ? $email : $mobile . '@' . SITE_DOMAIN);
        $user_salt_key = hash('sha256', uniqid(mt_rand(), true) . $mobile . substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?'), 0, 12) . time());

        if ($checkUser = mysqli_query($connection, "SELECT user_id as newUserId FROM " . TABLE_PREFIX . "user WHERE {$checkUserCondition}")) {
            if (mysqli_num_rows($checkUser) == 0) {
                $addUserTableQuery = "INSERT INTO " . TABLE_PREFIX . "user (user_mobile, user_username, user_email, user_salt_key, user_status, user_reg_time, 
        user_reg_ip) VALUES ('{$mobile}', '{$mobile}', '{$userEmail}', '{$user_salt_key}', 1, NOW(), '" . IP . "')";

                $createNewUser = mysqli_query($connection, $addUserTableQuery);
                $newUserId = mysqli_insert_id($connection);

                $insertProfileQuery = "INSERT INTO " . TABLE_PREFIX . "profile (profile_user_id, profile_email) VALUES ('{$newUserId}', '{$userEmail}')";
                $createNewUserProfile = mysqli_query($connection, $insertProfileQuery);
            } else {
                $createNewUser = true;
                $createNewUserProfile = true;
                extract(mysqli_fetch_assoc($checkUser));
            }
        }
        $site_access_meta = '';
        $user_type = 'customer';
        $insertSiteAccessQuery = "INSERT INTO " . TABLE_PREFIX . "site_access(site_access_site_id, site_access_user_id, site_access_type, site_access_status, site_access_meta) VALUES('" . SITE_ID . "', '{$newUserId}', '{$user_type}', 1, '')";
        $createNewSiteAccess = mysqli_query($connection, $insertSiteAccessQuery);

        if ($createNewUser && $createNewSiteAccess && $createNewUserProfile) {
            mysqli_query($connection, "COMMIT");
            $tokenStatus = 'success';
            $existingUser = false;
            $genarateToken = true;
        } else {
            mysqli_query($connection, "ROLLBACK");
            $tokenStatus = 'error';
            $tokenMsg = 'Something went wrong';
        }
    }

    $profile_display_name = ((isset($profile_display_name)) ? $profile_display_name : '');
    $profile_image = ((isset($profile_image)) ? $profile_image : '');

    if (isset($genarateToken)) {
        if ($genarateToken == true) {
            ob_start();
            setcookie('user_name', base64_encode($profile_display_name), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
            setcookie('user_mobile', base64_encode($mobile), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
            setcookie('user_image', base64_encode($profile_image), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
            setcookie('user_data', base64_encode($user_salt_key), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
            setcookie('user_meta', base64_encode($site_access_meta), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
            setcookie('user_type', base64_encode($user_type), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
            setcookie('site_app', base64_encode(SITE_APP), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
            $_COOKIE['user_data'] = $user_salt_key;
            require_once API_MODULES . 'analytics/session-record.php';
            ob_end_flush();
        }
    }
} else {
    $tokenStatus = 'error';
    $tokenMsg = 'Something went wrong';
}