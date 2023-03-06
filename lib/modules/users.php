<?php

require_once MODULES . 'encryptor.php';

class Users
{
    function createNewUser($connection, $userInfo)
    {

        extract($userInfo);

        if (!isset($first_name)) {
            $nameArray = explode(' ', $name);
            $first_name = $nameArray[0];
            $last_name = str_replace($first_name . ' ', '', $name);
        }

        $username = strtolower(str_replace(' ', '', $first_name . $last_name));

        if ($userData = mysqli_query($connection, "SELECT user_id as userId FROM " . TABLE_PREFIX . "user WHERE user_mobile='{$mobile}' AND user_email='{$email}'")) {

            if (mysqli_num_rows($userData) != 0) {

                extract(mysqli_fetch_assoc(($userData)));
            } else {

                /** Generate automatic password of 8 chatracter */
                $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789!@#$%&()_+=-<>/?'), 0, 8);

                /** Generating salt key of the user */
                $saltKey = hash('sha256', uniqid(mt_rand(), true) . $email . substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?'), 0, 12) . time());
                $dbPassword = hash('sha256', $saltKey . $password);

                if ($checkUsername = mysqli_query($connection, "SELECT user_username FROM " . TABLE_PREFIX . "user WHERE user_username='{$username}'")) {
                    if (mysqli_num_rows($checkUsername) != 0) {
                        $username = strtolower($first_name) . time();
                    }
                }

                /** Start mysql transaction */
                $verifyURL = SITE_DOMAIN . '/verify/email/?user_id=' . $saltKey . '&email=' . base64_encode($email);

                mysqli_query($connection, "START TRANSACTION");

                $addUser = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "user(user_parent_id, user_username, user_email, user_mobile, user_password, user_salt_key, user_status, user_reg_time, user_reg_ip) VALUES(0, '{$username}', '{$email}', '{$mobile}', '{$dbPassword}', '{$saltKey}', 0, NOW(), '" . IP . "')");

                $userId = mysqli_insert_id($connection);

                $createProfile = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "profile(profile_user_id, profile_first_name, profile_last_name, profile_display_name, profile_mobile, profile_email) VALUES('{$userId}', '{$first_name}', '{$last_name}', '" . $first_name . ' ' . $last_name . "', '{$mobile}', '{$email}')");

                $userSiteAccess = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_access(site_access_site_id, site_access_user_id, site_access_status) VALUES(" . SITE_ID . ", '{$userId}', 1)");

                if ($addUser && $createProfile && $userSiteAccess) {

                    /** Add all data into database */

                    require_once EMAIL_TEMPLATE . 'add-new-user.php';

                    mysqli_query($connection, 'COMMIT');

                    $Encryptor = new Encryptor();
                    $userToken = $Encryptor->encrypt(ENCRYPTION_AUTH_KEY, ENCRYPTION_AUTH_IV, $userId);
                } else {

                    /** Rollback mysql query if query is fail */
                    mysqli_query($connection, 'ROLLBACK');
                }
            }
        }

        return $userInfo = array(
            'userId' => ((isset($userId)) ? $userId : ''),
            'userToken' => ((isset($userToken)) ? $userToken : '')
        );
    }
}
