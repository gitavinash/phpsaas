<?php

header('Content-Type: application/json');
$httpStatus = 405;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $msg = [];

    $allowedMimeTypes = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/gif'
    ];

    $textInputs = ['setupId', 'companyName', 'year', 'companyEmail', 'companyEmail', 'gstNumber', 'companyAddress', 'registration_type', 'companyType', 'companyCity', 'companyState', 'companyPinCode'];
    foreach ($textInputs as $inputName) {
        if (empty(trim($_POST[$inputName]))) {
            $msg[$inputName] = "{$inputName} is required.";
        }
    }

    $fileInputs = ['aadhar_card', 'pan_card', 'gst_certificate'];
    foreach ($fileInputs as $inputName) {
        if (empty($_FILES[$inputName]['name'])) {
            $msg[$inputName] = "{$inputName} file is required.";
        } else {
            $fileType = $_FILES[$inputName]['type'];
            if (!in_array($fileType, $allowedMimeTypes)) {
                $errors[$inputName] = "{$inputName} invalid file type. Only PDF and image files are allowed.";
            }
        }
    }

    if (empty($msg)) {
        $email = base64_decode($setupId);

        mysqli_query($connection, "START TRANSACTION");

        if ($checkEmail = mysqli_query($connection, "SELECT user_id as userId FROM " . TABLE_PREFIX . "user WHERE user_email='{$email}' AND user_status=0")) {
            if (mysqli_num_rows($checkEmail) != 0) {
                extract(mysqli_fetch_assoc($checkEmail));

                if (isset($uploadDocument)) {

                    require_once MODULES . 'fileUploader.php';

                    if (!empty($files)) {

                        $filesData = json_decode($files, true);
                        foreach ($filesData as $item) {
                            ${$item['name']} = $item['path'];
                        }

                        // Address

                        $address = $companyAddress . ',  ' . $companyCity . ', ' . $companyState;

                        $addAddress = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "address(address_site_id, address_user_id, address_name, address_house_no, address_pin_code, address_country_id, address_type, address_status) VALUES(" . SITE_ID . ", '{$userId}', '{$companyName}', '{$address}', '{$companyPinCode}', 101,'office', 1)");
                        $addressId = mysqli_insert_id($connection);

                        // Company

                        $addCompany = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "company(company_site_id, company_user_id, company_address_id, company_title, company_gst, company_pan, company_email, company_mobile, company_type, company_status) VALUES(" . SITE_ID . ", '{$userId}', '{$addressId}', '{$companyName}', '{$gstNumber}', '{$panNumber}', '{$companyEmail}', '{$companyMobile}', '{$companyType}', 1)");
                        $companyId = mysqli_insert_id($connection);

                        // KYC

                        $addKYC = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "kyc(kyc_ref_id, kyc_ref_type,kyc_request_by, kyc_gst_number, kyc_gst_file, kyc_gst_password, kyc_pan_no, kyc_pan_file, kyc_pan_password, kyc_aadhar, kyc_aadhar_file, kyc_aadhar_password, kyc_status, kyc_request_time) VALUES('{$companyId}', 'company', '{$userId}', '{$gstNumber}', '{$gst_certificate}', '{$gstComment}', '{$panNumber}', '{$pan_card}', '{$panComment}', '{$aadharNumber}', '{$aadhar_card}', '{$aadharComment}', 0, NOW())");

                        if ($addAddress && $addCompany && $addKYC) {
                            $httpStatus = 201;
                            $msg = 'KYC request sent successfully';
                            mysqli_query($connection, "COMMIT");
                        } else {
                            $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something went wrong while sending KYC request');
                            mysqli_query($connection, "ROLLBACK");
                        }
                    } else {
                        $msg = 'Upload valid documents';
                    }
                }
            } else {
                $msg = 'Invalid setup id found';
            }
        }
    } else {
        $httpStatus = 400;
    }
}


$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid request method : ' . $_SERVER['REQUEST_METHOD']),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);