<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once LOGIN_CHECK;

    $httpStatus = 500;
    $status = 'warning';
    $msg = '';
    $country = ((isset($country)) ? $country : '101');
    $warehouseType = ((isset($warehouseType)) ? (($warehouseType != 'select') ? $warehouseType : '') : '');
    $formName = ((SITE_APP == 'spa') ? 'Branch' : 'Warehouse');
    if ($name == '') {
        $msg = "Kindly enter {$formName} name";
    }

    if ($address == '') {
        $msg = "Kindly enter {$formName} address";
    }

    if ($city == "") {
        $msg = "Kindly enter {$formName} city";
    }

    if ($state == "") {
        $msg = "Kindly select {$formName} state";
    }

    if ($pinCode == "") {
        $msg = "Kindly enter {$formName} pin code";
    }

    if ($email == "") {
        $msg = "Kindly enter {$formName} email";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "Kindly enter valid email address";
    }

    if ($mobile == "") {
        $msg = "Kindly enter {$formName} mobile";
    } else if (!preg_match("/^(\+91[\-\s]?)?[0]?(91)?[6789]\d{9}$/", $mobile)) {
        $msg = "Kindly enter a valid mobile number";
    }

    if ($msg == "") {

        mysqli_query($connection, "START TRANSACTION");

        if ($checkWarehouse = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "warehouse WHERE warehouse_id='{$warehouseId}'")) {
            if (mysqli_num_rows($checkWarehouse) > 0) {
                $addAddress = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "address(address_site_id, address_user_id, address_name, address_area, address_city_id, address_state_id, address_country_id, address_pin_code, address_type, address_status) VALUES(" . SITE_ID . ", " . USER_ID . ", '{$name}', '{$address}', '{$city}', '{$state}', '{$country}', '{$pinCode}', 'warehouse', 1)");
                $addressId = mysqli_insert_id($connection);

                $updateWarehouse = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "warehouse SET warehouse_code='{$warehouseCode}', warehouse_address_id='{$addressId}', warehouse_company_id='{$companyId}', warehouse_title='{$name}', warehouse_description='{$description}', warehouse_gst='{$gst}', warehouse_email='{$email}', warehouse_mobile='{$mobile}' WHERE warehouse_id='{$warehouseId}'");

                if ($addAddress && $updateWarehouse) {
                    $status = 'success';
                    $httpStatus = 204;
                    $msg = $formName . ' details updated successfully';
                    mysqli_query($connection, "COMMIT");
                } else {
                    $status = 'error';
                    $msg = 'Something went wrong while trying to updating ' . $formName;
                    mysqli_query($connection, "ROLLBACK");
                }
            } else {
                $msg = "Invalid {$formName} request found to update";
            }
        } else {
            $msg = "Something went wrong while executing the query";
        }
    }


    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
    );
    echo json_encode($result, TRUE);
} else {
    $httpStatus = 400;
    $msg = "Invalid http request";
}