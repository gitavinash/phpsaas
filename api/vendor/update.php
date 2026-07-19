<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once LOGIN_CHECK;

    $httpStatus = 500;
    $status = 'warning';
    $msg = '';
    $country = ((isset($country)) ? $country : '101');
    $vendorType = ((isset($vendorType)) ? (($vendorType != 'select') ? $vendorType : '') : '');
    $address = ((isset($address)) ? $address : '');
    $city = ((isset($city)) ? $city : '');
    $state = ((isset($state)) ? $state : '');
    $pinCode = ((isset($pinCode)) ? $pinCode : '');
    $vendorCode = ((isset($vendorCode)) ? $vendorCode : '');

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

        if ($featureImage == '') {
            $featureImage = ((isset($oldFeatureImage)) ? $oldFeatureImage : '');
        }

        mysqli_query($connection, "START TRANSACTION");

        if ($checkVendor = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "vendor WHERE vendor_id='{$vendorId}'")) {
            if (mysqli_num_rows($checkVendor) > 0) {
                $addAddress = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "address(address_site_id, address_user_id, address_name, address_area, address_city_id, address_state_id, address_country_id, address_pin_code, address_type, address_status) VALUES(" . SITE_ID . ", " . USER_ID . ", '{$name}', '{$address}', '{$city}', '{$state}', '{$country}', '{$pinCode}', 'work', 1)");
                $addressId = mysqli_insert_id($connection);

                $updateVendor = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "vendor SET vendor_code='{$vendorCode}', vendor_address_id='{$addressId}'" . (($featureImage != '') ? ", vendor_logo = '{$featureImage}'" : '') . ", vendor_title='{$name}', vendor_description='{$description}', vendor_gst='{$gst}', vendor_pan='{$pan}', vendor_cin='{$cin}', vendor_email='{$email}', vendor_mobile='{$mobile}', vendor_type='{$vendorType}' WHERE vendor_id='{$vendorId}'");

                if ($addAddress && $updateVendor) {
                    $status = 'success';
                    $httpStatus = 204;
                    $msg = 'Vendor details updated successfully';
                    mysqli_query($connection, "COMMIT");
                } else {
                    $status = 'error';
                    $msg = 'Something went wrong while trying to updating vendor';
                    mysqli_query($connection, "ROLLBACK");
                }
            } else {
                $msg = "Invalid vendor request found to update";
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
