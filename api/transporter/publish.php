<?php

require_once LOGIN_CHECK;
$responseCode = 500;

if ($name != '') {

    if ($serviceArea != '') {

        if ($checkTransporter = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "transporter WHERE transporter_title='{$name}' AND transporter_pincode='{$pinCode}'")) {
            if (mysqli_num_rows($checkTransporter) == 0) {
                if (mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "transporter(transporter_site_id, transporter_title, transporter_email, transporter_mobile, transporter_description, transporter_address, transporter_city, transporter_pincode, transporter_service_state, transporter_created_by) VALUES(" . SITE_ID . ", '{$name}', '{$email}', '{$mobile}', '{$description}', '{$address}', '{$city}', '{$pinCode}', '{$serviceArea}', " . USER_ID . ")")) {
                    $transporterId = mysqli_insert_id($connection);
                    $responseCode = 201;
                    $status = 'success';
                    $msg = 'New transporter added successfully';
                } else {
                    // $msg = 'Something went wrong while adding new transporter';
                    $msg = mysqli_error($connection);
                }
            } else {
                $msg = "Transporter is already exits in current address";
            }
        }
    } else {
        $msg = "Kindly enter transporter's service area";
    }
} else {
    $msg = 'Transporter name cannot be blank';
}

http_response_code($responseCode);

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

echo json_encode($result, TRUE);
