<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once LOGIN_CHECK;

    $httpStatus = 500;
    $status = 'warning';
    $msg = '';
    $country = ((isset($country)) ? $country : '101');
    $supplierType = ((isset($supplierType)) ? (($supplierType != 'select') ? $supplierType : '') : '');

    if ($name == '') {
        $msg = 'Kindly enter supplier name';
    }

    if ($address == '') {
        $msg = 'Kindly enter supplier address';
    }

    if ($city == '') {
        $msg = 'Kindly enter supplier city';
    }

    if ($state == '') {
        $msg = 'Kindly select supplier state';
    }

    if ($pinCode == '') {
        $msg = 'Kindly enter supplier pin code';
    }

    if ($email == '') {
        $msg = 'Kindly enter supplier email';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = 'Kindly enter valid email address';
    }

    if ($mobile == '') {
        $msg = 'Kindly enter supplier mobile';
    } else if (!preg_match("/^(\+91[\-\s]?)?[0]?(91)?[6789]\d{9}$/", $mobile)) {
        $msg = "Kindly enter a valid mobile number";
    }

    if ($msg == "") {

        mysqli_query($connection, "START TRANSACTION");

        $addAddress = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "address(address_site_id, address_user_id, address_name, address_area, address_city_id, address_state_id, address_country_id, address_pin_code, address_type, address_status) VALUES(" . SITE_ID . ", " . USER_ID . ", '{$name}', '{$address}', '{$city}', '{$state}', '{$country}', '{$pinCode}', 'work', 1)");
        $addressId = mysqli_insert_id($connection);

        $addSupplier = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "supplier(supplier_code, supplier_site_id, supplier_user_id, supplier_address_id, supplier_title, supplier_description, supplier_gst, supplier_pan, supplier_cin, supplier_email, supplier_mobile, supplier_type, supplier_status) VALUES('{$supplierCode}', " . SITE_ID . ", " . USER_ID . ", '{$addressId}', '{$name}', '{$description}', '{$gst}', '{$pan}', '{$cin}', '{$email}', '{$mobile}', '{$supplierType}', 1)");
        $supplierId = mysqli_insert_id($connection);

        if ($addAddress && $addSupplier) {
            $status = 'success';
            $httpStatus = 201;
            $msg = 'New supplier is created successfully';
            mysqli_query($connection, "COMMIT");
        } else {
            $status = 'error';
            $msg = 'Something went wrong while trying to create new supplier';
            $supplierId = 0;
            mysqli_query($connection, "ROLLBACK");
        }
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
        'supplierId' => ((isset($supplierId)) ? $supplierId : ''),
    );

    http_response_code($httpStatus);
    echo json_encode($result, TRUE);
}
