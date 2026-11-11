<?php

require_once LOGIN_CHECK;

function publish($connection, $data)
{

    extract($data);

    $msg = '';
    $status = 'warning';

    if ($email == '') {

        $msg = 'Please enter a valid email';
    }

    if ($first_name == '') {

        $msg = 'Please enter a first name';
    }

    if ($mobile == '') {

        $msg = 'Please enter a mobile number';
    }

    if ($userRole == '') {

        $msg = 'Please select user role';
    }

    $site_access_meta_value = '';

    // For Intenary management

    if (isset($company)) {
        if (!isset($warehouse)) {
            $warehouse = 'all';
        } else {
            $warehouse = (($warehouse != '') ? $warehouse : 'all');
        }
        $site_access_meta_value = json_encode(array(
            'company' => $company,
            'warehouse' => $warehouse
        ), TRUE);
    }

    if ($userRole == 'delivery_agent') {
        $site_access_meta_value = json_encode(array(
            'vendor' => VENDOR_ID,
        ), TRUE);
    }

    // User registraion process

    if ($msg == '') {

        if ($checkEmail = mysqli_query($connection, "SELECT user_email, user_id FROM " . TABLE_PREFIX . "user WHERE user_email='{$email}'")) {

            if (mysqli_num_rows($checkEmail) == 0) {

                /** Check mobile number is available or not */

                if ($checkMobile = mysqli_query($connection, "SELECT profile_mobile FROM " . TABLE_PREFIX . "profile WHERE profile_mobile='{$mobile}'")) {

                    if (mysqli_num_rows($checkMobile) == 0) {

                        /** Start the user registration process */

                        $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?'), 0, 12);

                        /* Encrypt password with salt key */

                        $saltKey = hash('sha256', uniqid(mt_rand(), true) . $email . substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?'), 0, 12) . time());
                        $enc_password = hash('sha256', $saltKey . $password);

                        $profile_display_name = $first_name . ' ' . $last_name;

                        /** Checking Username is avilable or not */

                        $username = strtolower(str_replace(' ', '', $first_name . $last_name));

                        if ($checkUsername = mysqli_query($connection, "SELECT user_username FROM " . TABLE_PREFIX . "user WHERE user_username='{$username}'")) {
                            if (mysqli_num_rows($checkUsername) != 0) {
                                $username = strtolower($first_name) . time();
                            }
                        }

                        mysqli_query($connection, "START TRANSACTION");

                        /* Insert data into user table */

                        $insertUser = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "user(user_parent_id, user_username, user_email, user_mobile, user_password, user_salt_key, user_status, user_reg_time, user_reg_ip) VALUES(" . USER_ID . ",'{$username}', '{$email}', '{$mobile}', '{$enc_password}', '$saltKey', 1, NOW(), '" . IP . "')");
                        $userID = mysqli_insert_id($connection);

                        $insertSiteAccess = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_access(site_access_site_id, site_access_user_id, site_access_type, site_access_status, site_access_meta) VALUES(" . SITE_ID . ", '{$userID}', '{$userRole}', 1, '{$site_access_meta_value}')");

                        $insertProfile = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "profile(profile_user_id, profile_first_name, profile_last_name, profile_mobile, profile_display_name, profile_email) VALUES('{$userID}', '{$first_name}', '{$last_name}', '{$mobile}', '{$profile_display_name}', '{$email}')");

                        if ($insertUser && $insertSiteAccess && $insertProfile) {

                            $status = 'success';
                            $msg = 'You are successfully registered';
                            mysqli_query($connection, 'COMMIT');
                        } else {

                            $status = 'error';
                            $msg = 'Something is wrong while trying to register';
                            mysqli_query($connection, 'ROLLBACK');
                        }
                    } else {

                        $status = 'warning';
                        $msg = 'This mobile number is already in use';
                    }
                }
            } else {

                extract(mysqli_fetch_assoc($checkEmail));

                if ($checkAccess = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "site_access WHERE site_access_user_id='{$user_id}' AND site_access_type='{$userRole}' AND site_access_site_id=" . SITE_ID)) {
                    if (mysqli_num_rows($checkAccess) == 0) {
                        if (!mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_access(site_access_user_id, site_access_site_id,site_access_type, site_access_status, site_access_meta) VALUES('{$user_id}', " . SITE_ID . ", '{$userRole}', 1, '{$site_access_meta_value}')")) {
                            $status = 'error';
                            $msg = 'Something went wrong while trying to user access';
                        } else {
                            $userID = $user_id;
                            $status = 'success';
                            $msg = 'You are successfully registered';
                        }
                    } else {
                        extract(mysqli_fetch_assoc($checkAccess));

                        if (!mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "site_access SET site_access_status=1, site_access_meta='{$site_access_meta_value}' WHERE site_access_user_id='{$user_id}' AND site_access_type='{$userRole}' AND site_access_site_id=" . SITE_ID)) {
                            $status = 'error';
                            $msg = 'Something went wrong while trying to user access';
                        } else {
                            $userID = $user_id;
                            $status = 'success';
                            $msg = 'You are successfully registered';
                        }
                    }
                } else {
                    $status = 'error';
                    $msg = mysqli_error($connection);
                }
            }
        }
    }

    $msg = urlencode($msg);

    if (!headers_sent()) {
        header('Content-Type: text/html; charset=utf-8');
    }

    $encodedUserId = isset($userID) ? urlencode(base64_encode((string) $userID)) : '';

    if (SITE_DASHBOARD_VERSION == 1) {
        if ($status == 'success') {
            header("Location: /dashboard/user/?type=edit&editor=true&id={$encodedUserId}&responseMsg={$msg}", TRUE, 302);
        } else {
            header("Location: /dashboard/user/?type=add&responseMsg={$msg}&status={$status}", TRUE, 302);
        }
    } else {
        if ($status === 'success') {
            header("Location: /dashboard/user/edit/?id={$encodedUserId}&responseMsg={$msg}", TRUE, 302);
        } else {
            header("Location: /dashboard/user/add/?responseMsg={$msg}&status={$status}", TRUE, 302);
        }
    }
    exit();
}
