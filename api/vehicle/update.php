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

        if ($checkVehicle = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "vehicles WHERE vehicle_id='{$id}'")) {
            if (mysqli_num_rows($checkVehicle) != 0) {
                if (mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "vehicles SET vehicle_thumbnail='{$icon}', vehicle_title='{$name}', vehicle_description='{$description}', vehicle_seating='{$vehicle}', vehicle_type='{$category}' WHERE vehicle_id='{$id}'")) {
                    $responseCode = 200;
                    $status = 'success';
                    $msg = 'Vehicle update successfully';
                } else {
                    $msg = 'Something went wrong while updating vehicles';
                }
            } else {
                $msg = 'Invalid vehicles details found';
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
