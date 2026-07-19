<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once LOGIN_CHECK;

    $httpStatus = 500;
    $status = 'warning';
    $msg = '';
    $country = ((isset($country)) ? $country : '101');
    $companyType = ((isset($companyType)) ? (($companyType != 'select') ? $companyType : '') : '');
    $address = ((isset($address)) ? $address : '');
    $city = ((isset($city)) ? $city : '');
    $state = ((isset($state)) ? $state : '');
    $pinCode = ((isset($pinCode)) ? $pinCode : '');
    $companyCode = ((isset($companyCode)) ? $companyCode : '');

    if ($name == '') {
        $msg = 'Kindly enter company name';
    }

    if ($email == '') {
        $msg = 'Kindly enter company email';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = 'Kindly enter valid email address';
    }

    if ($mobile == '') {
        $msg = 'Kindly enter company mobile';
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

        if ($checkCompany = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "company WHERE company_id='{$companyId}'")) {
            if (mysqli_num_rows($checkCompany) > 0) {
                $addAddress = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "address(address_site_id, address_user_id, address_name, address_area, address_city_id, address_state_id, address_country_id, address_pin_code, address_type, address_status) VALUES(" . SITE_ID . ", " . USER_ID . ", '{$name}', '{$address}', '{$city}', '{$state}', '{$country}', '{$pinCode}', 'work', 1)");
                $addressId = mysqli_insert_id($connection);

                $updateCompany = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "company SET company_code='{$companyCode}', company_address_id='{$addressId}'" . (($featureImage != '') ? ", company_logo = '{$featureImage}'" : '') . ", company_title='{$name}', company_description='{$description}', company_gst='{$gst}', company_pan='{$pan}', company_cin='{$cin}', company_email='{$email}', company_mobile='{$mobile}', company_type='{$companyType}' WHERE company_id='{$companyId}'");

                if ($addAddress && $updateCompany) {
                    $status = 'success';
                    $httpStatus = 204;
                    $msg = 'Company details updated successfully';
                    mysqli_query($connection, "COMMIT");
                } else {
                    $status = 'error';
                    $msg = 'Something went wrong while trying to updating company';
                    mysqli_query($connection, "ROLLBACK");
                }
            } else {
                $msg = "Invalid company request found to update";
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
