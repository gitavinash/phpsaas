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

        if ($address != "") {
            $addAddress = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "address(address_site_id, address_user_id, address_name, address_area, address_city_id, address_state_id, address_country_id, address_pin_code, address_type, address_status) VALUES(" . SITE_ID . ", " . USER_ID . ", '{$name}', '{$address}', '{$city}', '{$state}', '{$country}', '{$pinCode}', 'work', 1)");
            $addressId = mysqli_insert_id($connection);
        } else {
            $addAddress = true;
            $addressId = 0;
        }

        $addVendor = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "vendor(vendor_code, vendor_site_id, vendor_user_id, vendor_address_id, vendor_logo, vendor_title, vendor_description, vendor_gst, vendor_pan, vendor_cin, vendor_email, vendor_mobile, vendor_type, vendor_status) VALUES('{$vendorCode}', " . SITE_ID . ", " . USER_ID . ", '{$addressId}', '{$featureImage}', '{$name}', '{$description}', '{$gst}', '{$pan}', '{$cin}', '{$email}', '{$mobile}', '{$vendorType}', 1)");
        $vendorId = mysqli_insert_id($connection);

        if ($addAddress && $addVendor) {
            $status = 'success';
            $httpStatus = 201;
            $msg = 'New vendor is created successfully';
            mysqli_query($connection, "COMMIT");
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
