<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once LOGIN_CHECK;

    $httpStatus = 500;
    $status = 'warning';
    $msg = '';
    $country = ((isset($country)) ? $country : '101');
    $companyType = ((isset($companyType)) ? (($companyType != 'select') ? $companyType : '') : '');
    $address = "";
    $city = "";
    $state = "";
    $pinCode = "";
    $companyCode = ((isset($companyCode)) ? $companyCode : 'COMP-' . time());

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

        mysqli_query($connection, "START TRANSACTION");

        if ($address != "") {
            $addAddress = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "address(address_site_id, address_user_id, address_name, address_area, address_city_id, address_state_id, address_country_id, address_pin_code, address_type, address_status) VALUES(" . SITE_ID . ", " . USER_ID . ", '{$name}', '{$address}', '{$city}', '{$state}', '{$country}', '{$pinCode}', 'work', 1)");
            $addressId = mysqli_insert_id($connection);
        } else {
            $addAddress = true;
            $addressId = 0;
        }

        $addCompany = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "company(company_code, company_site_id, company_user_id, company_address_id, company_logo, company_title, company_description, company_gst, company_pan, company_cin, company_email, company_mobile, company_type, company_status) VALUES('{$companyCode}', " . SITE_ID . ", " . USER_ID . ", '{$addressId}', '{$featureImage}', '{$name}', '{$description}', '{$gst}', '{$pan}', '{$cin}', '{$email}', '{$mobile}', '{$companyType}', 1)");
        $companyId = mysqli_insert_id($connection);

        if ($addAddress && $addCompany) {
            $status = 'success';
            $httpStatus = 201;
            $msg = 'New company is created successfully';
            mysqli_query($connection, "COMMIT");
        } else {
            $status = 'error';
            $msg = 'Something went wrong while trying to create new company';
            $companyId = 0;
            mysqli_query($connection, "ROLLBACK");
        }
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
        'companyId' => ((isset($companyId)) ? $companyId : ''),
    );

    http_response_code($httpStatus);
    echo json_encode($result, TRUE);
}
