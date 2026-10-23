<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once LOGIN_CHECK;

    $httpStatus = 500;
    $status = 'warning';
    $msg = '';
    $country = ((isset($country)) ? $country : '101');
    $vendorType = ((isset($vendorType)) ? (($vendorType != 'select') ? $vendorType : '') : '');
    $address = "";
    $city = "";
    $state = "";
    $pinCode = "";
    $vendorCode = ((isset($vendorCode)) ? $vendorCode : 'VEND-' . time());

    if ($name == '') {
        $msg = 'Kindly enter vendor name';
    }

    if ($email == '') {
        $msg = 'Kindly enter vendor email';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = 'Kindly enter valid email address';
    }

    if ($mobile == '') {
        $msg = 'Kindly enter vendor mobile';
    } else if (!preg_match("/^(\+91[\-\s]?)?[0]?(91)?[6789]\d{9}$/", $mobile)) {
        $msg = "Kindly enter a valid mobile number";
    }

    if ($msg == "") {

        require_once MODULES . 'imageUploader.php';

        if (!isset($image['featureImage'])) {
            $featureImage = '';
        }

        mysqli_query($connection, "START TRANSACTION");


        $addUser = true;
        $isNewUser = false;
        $newUserId = defined('USER_ID') ? USER_ID : 0;
        if ((int)SITE_APP_ID === 32) {
            $checkUser = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user WHERE user_email='{$email}' OR user_mobile='{$mobile}' LIMIT 1");
            if (mysqli_num_rows($checkUser) > 0) {
                $userData = mysqli_fetch_assoc($checkUser);
                $newUserId = $userData['user_id'];

                $checkAccess = mysqli_query($connection, "SELECT site_access_id FROM " . TABLE_PREFIX . "site_access WHERE site_access_user_id='{$newUserId}' AND site_access_site_id=" . SITE_ID . " AND site_access_type='vendor' LIMIT 1");
                if (mysqli_num_rows($checkAccess) == 0) {
                    $addUser = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_access(site_access_site_id, site_access_user_id, site_access_type, site_access_status, site_access_meta) VALUES(" . SITE_ID . ", '{$newUserId}', 'vendor', 1, '')");
                } else {
                    $addUser = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "site_access SET site_access_status=1 WHERE site_access_user_id='{$newUserId}' AND site_access_site_id=" . SITE_ID . " AND site_access_type='vendor'");
                }
            } else {
                $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?'), 0, 12);
                $saltKey = hash('sha256', uniqid(mt_rand(), true) . $email . substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?'), 0, 12) . time());
                $enc_password = hash('sha256', $saltKey . $password);
                $username = strtolower(str_replace(' ', '', $name));
                $checkUsername = mysqli_query($connection, "SELECT user_username FROM " . TABLE_PREFIX . "user WHERE user_username='{$username}'");
                if (mysqli_num_rows($checkUsername) != 0) {
                    $username = strtolower(str_replace(' ', '', $name)) . time();
                }

                $insertUser = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "user(user_username, user_email, user_mobile, user_password, user_salt_key, user_status, user_reg_time, user_reg_ip) VALUES('{$username}', '{$email}', '{$mobile}', '{$enc_password}', '{$saltKey}', 1, NOW(), '" . IP . "')");
                if ($insertUser) {
                    $newUserId = mysqli_insert_id($connection);
                    $insertProfile = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "profile(profile_user_id, profile_first_name, profile_last_name, profile_mobile, profile_display_name, profile_email) VALUES('{$newUserId}', '{$name}', '', '{$mobile}', '{$name}', '{$email}')");
                    $insertAccess = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_access(site_access_site_id, site_access_user_id, site_access_type, site_access_status, site_access_meta) VALUES(" . SITE_ID . ", '{$newUserId}', 'vendor', 1, '')");

                    $addUser = $insertProfile && $insertAccess;
                    if ($addUser) {
                        $isNewUser = true;
                    }
                } else {
                    $addUser = false;
                }
            }
        }

        if ($address != "") {
            $addAddress = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "address(address_site_id, address_user_id, address_name, address_area, address_city_id, address_state_id, address_country_id, address_pin_code, address_type, address_status) VALUES(" . SITE_ID . ", '{$newUserId}', '{$name}', '{$address}', '{$city}', '{$state}', '{$country}', '{$pinCode}', 'work', 1)");
            $addressId = mysqli_insert_id($connection);
        } else {
            $addAddress = true;
            $addressId = 0;
        }

        $addVendor = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "vendor(vendor_code, vendor_site_id, vendor_user_id, vendor_address_id, vendor_logo, vendor_title, vendor_description, vendor_gst, vendor_pan, vendor_cin, vendor_email, vendor_mobile, vendor_type, vendor_status) VALUES('{$vendorCode}', " . SITE_ID . ", '{$newUserId}', '{$addressId}', '{$featureImage}', '{$name}', '{$description}', '{$gst}', '{$pan}', '{$cin}', '{$email}', '{$mobile}', '{$vendorType}', 1)");
        $vendorId = mysqli_insert_id($connection);

        if ($addAddress && $addVendor && $addUser) {
            $status = 'success';
            $httpStatus = 201;
            $msg = 'New vendor is created successfully';
            mysqli_query($connection, "COMMIT");

            if ((int)SITE_APP_ID === 32 && $isNewUser === true) {
                $siteName = SITE_NAME;
                $siteDomain = SITE_DOMAIN;
                $siteQuery = mysqli_query($connection, "SELECT site_name, site_domain FROM " . TABLE_PREFIX . "site INNER JOIN " . TABLE_PREFIX . "site_setting ON site_setting_site_id=site_id WHERE site_id=" . SITE_ID . " LIMIT 1");
                if ($siteQuery && mysqli_num_rows($siteQuery) > 0) {
                    $siteData = mysqli_fetch_assoc($siteQuery);
                    $siteName = $siteData['site_name'];
                    $siteDomain = $siteData['site_domain'];
                }
                $profile_display_name = $name;

                require_once EMAIL_TEMPLATE . 'welcome-vendor.php';
            }
        } else {
            $status = 'error';
            $msg = 'Something went wrong while trying to create new vendor';
            $vendorId = 0;
            mysqli_query($connection, "ROLLBACK");
        }
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
        'vendorId' => ((isset($vendorId)) ? $vendorId : ''),
    );

    http_response_code($httpStatus);
    echo json_encode($result, TRUE);
}
