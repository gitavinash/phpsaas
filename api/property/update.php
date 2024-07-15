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

        if (isset($_FILES['gallery'])) {
            require_once MODULES . 'galleryUploader.php';
        } else {
            $gallery = '';
        }

        if ($checkAmenities = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "amenities WHERE amenitie_id='{$id}'")) {
            if (mysqli_num_rows($checkAmenities) != 0) {
                if (mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "amenities SET amenitie_thumbnail='{$icon}', amenitie_title='{$name}', amenitie_description='{$description}', amenitie_type='{$category}' WHERE amenitie_id='{$id}'")) {
                    $responseCode = 200;
                    $status = 'success';
                    $msg = 'Amenities update successfully';
                } else {
                    $msg = 'Something went wrong while updating amenities';
                }
            } else {
                $msg = 'Invalid amenities details found';
            }
        }
    } else {
        $msg = 'Kindly select amenities type';
    }
} else {
    $msg = 'Amenities name cannot be blank';
}

http_response_code($responseCode);

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

echo json_encode($result, TRUE);