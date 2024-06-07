<?php

require_once LOGIN_CHECK;
$responseCode = 500;

if ($name != '') {

    require_once MODULES . 'imageUploader.php';

    if (!isset($icon)) {
        /** if feature image is not upload then set featureImage as blank */
        $icon = '';
    }

    if ($checkAmenities = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "amenities WHERE amenitie_title='{$name}'")) {
        if (mysqli_num_rows($checkAmenities) == 0) {
            if (mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "amenities(amenitie_thumbnail, amenitie_title, amenitie_description) VALUES('{$icon}', '{$name}', '{$description}')")) {
                $responseCode = 201;
                $status = 'success';
                $msg = 'Amenities added successfully';
            } else {
                $msg = 'Something went wrong while adding new amenities';
            }
        } else {
            $responseCode = 409;
            $msg = 'Amenities is already exits';
        }
    }
}

http_response_code($responseCode);

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

echo json_encode($result, TRUE);