<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once LOGIN_CHECK;

    $httpStatus = 500;
    $status = 'warning';
    $msg = '';
    $country = ((isset($country)) ? $country : '101');
    $warehouseType = ((isset($warehouseType)) ? (($warehouseType != 'select') ? $warehouseType : '') : '');

    if ($name == '') {
        $msg = 'Kindly enter warehouse name';
    }

    if ($address == '') {
        $msg = 'Kindly enter warehouse address';
    }

    if ($city == '') {
        $msg = 'Kindly enter warehouse city';
    }

    if ($state == '') {
        $msg = 'Kindly select warehouse state';
    }

    if ($pinCode == '') {
        $msg = 'Kindly enter warehouse pin code';
    }

    if ($email == '') {
        $msg = 'Kindly enter warehouse email';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = 'Kindly enter valid email address';
    }

    if ($mobile == '') {
        $msg = 'Kindly enter warehouse mobile';
    } else if (!preg_match("/^(\+91[\-\s]?)?[0]?(91)?[6789]\d{9}$/", $mobile)) {
        $msg = "Kindly enter a valid mobile number";
    }

    if ($msg == "") {

        mysqli_query($connection, "START TRANSACTION");

        $addAddress = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "address(address_site_id, address_user_id, address_name, address_area, address_city_id, address_state_id, address_country_id, address_pin_code, address_type, address_status) VALUES(" . SITE_ID . ", " . USER_ID . ", '{$name}', '{$address}', '{$city}', '{$state}', '{$country}', '{$pinCode}', 'warehouse', 1)");
        $addressId = mysqli_insert_id($connection);

        $addWarehouse = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "warehouse(warehouse_code, warehouse_site_id, warehouse_user_id, warehouse_address_id, warehouse_company_id, warehouse_title, warehouse_description, warehouse_gst, warehouse_email, warehouse_mobile, warehouse_status, warehouse_created) VALUES('{$warehouseCode}', " . SITE_ID . ", " . USER_ID . ", '{$addressId}', '{$companyId}', '{$name}', '{$description}', '{$gst}', '{$email}', '{$mobile}', 1, NOW())");
        $warehouseId = mysqli_insert_id($connection);

        if ($addAddress && $addWarehouse) {
            $status = 'success';
            $httpStatus = 201;
            $msg = 'New warehouse is created successfully';
            mysqli_query($connection, "COMMIT");
        } else {
            $status = 'error';
            $msg = 'Something went wrong while trying to create new warehouse';
            $warehouseId = 0;
            mysqli_query($connection, "ROLLBACK");
        }
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
        'warehouseId' => ((isset($warehouseId)) ? $warehouseId : ''),
    );

    http_response_code($httpStatus);
    echo json_encode($result, TRUE);
}
