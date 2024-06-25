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

        if ($checkSupplier = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "supplier WHERE supplier_id='{$supplierId}'")) {
            if (mysqli_num_rows($checkSupplier) > 0) {
                $addAddress = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "address(address_site_id, address_user_id, address_name, address_area, address_city_id, address_state_id, address_country_id, address_pin_code, address_type, address_status) VALUES(" . SITE_ID . ", " . USER_ID . ", '{$name}', '{$address}', '{$city}', '{$state}', '{$country}', '{$pinCode}', 'work', 1)");
                $addressId = mysqli_insert_id($connection);

                $updateSupplier = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "supplier SET supplier_code='{$supplierCode}', supplier_address_id='{$addressId}', supplier_title='{$name}', supplier_description='{$description}', supplier_gst='{$gst}', supplier_pan='{$pan}', supplier_cin='{$cin}', supplier_email='{$email}', supplier_mobile='{$mobile}', supplier_type='{$supplierType}' WHERE supplier_id='{$supplierId}'");

                if ($addAddress && $updateSupplier) {
                    $status = 'success';
                    $httpStatus = 204;
                    $msg = 'Supplier details updated successfully';
                    mysqli_query($connection, "COMMIT");
                } else {
                    $status = 'error';
                    $msg = 'Something went wrong while trying to updating supplier';
                    mysqli_query($connection, "ROLLBACK");
                }
            } else {
                $msg = "Invalid supplier request found to update";
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
