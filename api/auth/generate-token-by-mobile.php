<?php

// Assign site access to user

$checkSiteAccess = "
    SELECT site_access_id 
    FROM " . TABLE_PREFIX . "site_access 
    WHERE site_access_user_id = '{$user_id}' 
      AND site_access_site_id = '" . SITE_ID . "' 
      AND site_access_type = 'customer' 
    LIMIT 1";

$checkResult = mysqli_query($connection, $checkSiteAccess);

if (mysqli_num_rows($checkResult) === 0) {
    // No existing record, proceed to insert
    $addSiteAccess = "
        INSERT INTO " . TABLE_PREFIX . "site_access 
        (site_access_user_id, site_access_site_id, site_access_type, site_access_status) 
        VALUES ('{$user_id}', '" . SITE_ID . "', 'customer', 1)";

    if (mysqli_query($connection, $addSiteAccess)) {
        $siteAccessId = mysqli_insert_id($connection);
    } else {
        $status = 'error';
        $msg = 'Failed to assign site access: ' . mysqli_error($connection);
        $httpStatus = 500;
    }
} else {
    // Record already exists
    $msg = 'Site access already exists for this user.';
}


if ($checkUser = mysqli_query($connection, "SELECT user_id, user_salt_key, user_status, user_type_code as user_type, site_app_id, profile_display_name, profile_image, site_access_site_id, site_access_status, site_access_meta FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "profile ON user_id=profile_user_id INNER JOIN " . TABLE_PREFIX . "site_access ON site_access_user_id=user_id INNER JOIN " . TABLE_PREFIX . "site ON site_id=site_access_site_id INNER JOIN " . TABLE_PREFIX . "user_type ON user_type_code=site_access_type AND site_access_site_id=" . SITE_ID . " WHERE user_id='{$user_id}' LIMIT 1")) {

    if (mysqli_num_rows($checkUser) != 0) {

        extract(mysqli_fetch_assoc($checkUser));

        /** User has access of this site */

        if ($site_access_status == 1 && $user_status == 1) {

            ob_start();

            setcookie('user_name', base64_encode($profile_display_name), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
            setcookie('user_mobile', base64_encode($mobile), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
            setcookie('user_image', base64_encode($profile_image), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
            setcookie('user_data', base64_encode($user_salt_key), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
            setcookie('user_meta', base64_encode($site_access_meta), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
            setcookie('user_type', base64_encode($user_type), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);
            setcookie('site_app', base64_encode(SITE_APP), time() + 30 * 24 * 60 * 60, '/', MAIN_DOMAIN);

            ob_end_flush();
            $tokenStatus = 'success';
            $tokenMsg = 'Login Successfully';
        } else {

            if ($user_status == 0) {

                /** User verification url using the user salt key */

                $verifyURL = SITE_DOMAIN . '/verify/email/?user_id=' . $user_salt_key . '&email=' . base64_encode($email);

                require_once EMAIL_TEMPLATE . 'email-verification.php';

                $tokenMsg = 'Please verify your account';
            } else {

                $tokenMsg = 'Something is wrong with user profile';
            }
        }
    } else {
        $tokenStatus = 'error';
        $tokenMsg = 'No user found';
    }
} else {
    $tokenStatus = 'error';
    $tokenMsg = 'Something went wrong';
}