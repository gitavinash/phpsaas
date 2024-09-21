<?php

require_once LOGIN_CHECK;
$responseCode = 500;

if ($name != '') {

    if ($category != '') {

        $category = strtolower(str_replace(' ', '_', $category));

        require_once MODULES . 'imageUploader.php';

        if (!isset($icon)) {
            /** if feature image is not upload then set featureImage as blank */
            $icon = '';
        }

        if ($checkVehicle = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "vehicles WHERE vehicle_title='{$name}' AND vehicle_type='{$category}'")) {
            if (mysqli_num_rows($checkVehicle) == 0) {
                if (mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "vehicles(vehicle_thumbnail, vehicle_title, vehicle_description, vehicle_seating, vehicle_type) VALUES('{$icon}', '{$name}', '{$description}', '{$seating}', '{$category}')")) {
                    $responseCode = 201;
                    $status = 'success';
                    $msg = 'Vehicle added successfully';
                } else {
                    $msg = 'Something went wrong while adding new vehicle';
                }
            } else {
                $responseCode = 409;
                $msg = "Vehicle is already exits in " . ucwords(str_replace('_', ' ', $category));
            }
        }
    } else {
        $msg = 'Kindly select vehicles type';
    }
} else {
    $msg = 'Vehicle name cannot be blank';
}

http_response_code($responseCode);

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

echo json_encode($result, TRUE);
