<?php

if ($checkDevice = mysqli_query($connection, "SELECT device_id, device_token FROM " . TABLE_PREFIX . "device WHERE device_token='{$token}' AND device_site_id=" . SITE_ID)) {

    if (mysqli_num_rows($checkDevice) == 0) {

        mysqli_query($connection, "START TRANSACTION");

        $user_id = 0;
        if (defined('USER_ID') && (int)USER_ID > 0) {
            $user_id = (int)USER_ID;
        } elseif (isset($_COOKIE['user_data'])) {
            $user_salt_key = mysqli_real_escape_string($connection, base64_decode($_COOKIE['user_data']));
            if ($checkUserIsReal = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user WHERE user_salt_key='{$user_salt_key}'")) {
                if (mysqli_num_rows($checkUserIsReal) != 0) {
                    $uData = mysqli_fetch_assoc($checkUserIsReal);
                    $user_id = (int)$uData['user_id'];
                }
            }
        }

        $registerDevice = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "device(device_site_id, device_user_id, device_token, device_type, device_os, device_browser, device_status) VALUES('" . SITE_ID . "', '{$user_id}','{$token}', '{$device}', '" . DEVICE_OS . "', '" . DEVICE_BROWSER . "', 1)");
        $device_id = mysqli_insert_id($connection);

        require_once 'group.php';
    } else {
        $user_id = 0;
        if (defined('USER_ID') && (int)USER_ID > 0) {
            $user_id = (int)USER_ID;
        } elseif (isset($_COOKIE['user_data'])) {
            $user_salt_key = mysqli_real_escape_string($connection, base64_decode($_COOKIE['user_data']));
            if ($checkUserIsReal = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user WHERE user_salt_key='{$user_salt_key}'")) {
                if (mysqli_num_rows($checkUserIsReal) != 0) {
                    $uData = mysqli_fetch_assoc($checkUserIsReal);
                    $user_id = (int)$uData['user_id'];
                }
            }
        }

        if ($user_id > 0) {
            mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "device SET device_user_id = '{$user_id}', device_status = 1 WHERE device_token = '{$token}' AND device_site_id = " . SITE_ID);
        }

        $status = 200;
        $msg = 'Device is already registered and updated';
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 206),
    'msg' => ((isset($msg)) ? $msg : 'Something is wrong while registering device'),
);

echo json_encode($result, TRUE);
