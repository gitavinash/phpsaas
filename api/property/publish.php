<?php

require_once LOGIN_CHECK;
$responseCode = 500;

if ($name != '') {

    if ($category != '') {

        $category = strtolower(str_replace(' ', '_', $category));

        if ($checkProperty = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "property WHERE property_title='{$name}' AND property_pincode='{$pinCode}'")) {
            if (mysqli_num_rows($checkProperty) == 0) {
                if (mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "property(property_site_id, property_title, property_email, property_mobile, property_description, property_address, property_city, property_pincode, property_map_url, property_type, property_created_by) VALUES(" . SITE_ID . ", '{$name}', '{$email}', '{$mobile}', '{$description}', '{$address}', '{$city}', '{$pinCode}', '{$googleMapUrl}', '{$category}', " . USER_ID . ")")) {
                    $propertyId = mysqli_insert_id($connection);
                    $responseCode = 201;
                    $status = 'success';
                    $msg = 'New property added successfully';
                } else {
                    // $msg = 'Something went wrong while adding new property';
                    $msg = mysqli_error($connection);
                }
            } else {
                $msg = "Property is already exits in current address";
            }
        }
    } else {
        $msg = 'Kindly select property type';
    }
} else {
    $msg = 'Property name cannot be blank';
}

http_response_code($responseCode);

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

echo json_encode($result, TRUE);
