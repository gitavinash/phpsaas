<?php

require_once LOGIN_CHECK;
$responseCode = 500;

if ($name != '') {

    require_once MODULES . 'imageUploader.php';

    if (!isset($icon)) {
        /** if feature image is not upload then set featureImage as blank */
        $icon = '';
    }

    if ($checkAmenities = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "amenities WHERE amenitie_id='{$id}'")) {
        if (mysqli_num_rows($checkAmenities) != 0) {
            if (mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "amenities SET amenitie_thumbnail='{$icon}', amenitie_title='{$name}', amenitie_description='{$description}' WHERE amenitie_id='{$id}'")) {
                $responseCode = 200;
                $status = 'success';
                $msg = 'Amenities update successfully';
            } else {
                $msg = 'Something went wrong while updating amenities';
            }
        } else {
            $responseCode = 409;
            $msg = 'Invalid amenities details found';
        }
    }
}

http_response_code($responseCode);

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

echo json_encode($result, TRUE);