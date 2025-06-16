<?php

require_once LOGIN_CHECK;
$responseCode = 500;

if ($name != '') {

    // For domestic

    if ($state != '') {

        $state = strtolower(str_replace(' ', '_', $state));

        require_once MODULES . 'imageUploader.php';

        if (!isset($icon)) {
            /** if feature image is not upload then set featureImage as blank */
            $icon = '';
        }

        if ($checkLocations = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "locations WHERE location_id='{$id}'")) {
            if (mysqli_num_rows($checkLocations) != 0) {
                if (mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "locations SET location_thumbnail='{$icon}', location_title='{$name}', location_description='{$description}', location_state='{$state}', location_country='{$country}', location_best_from='{$fromMonth}', location_best_to='{$toMonth}' WHERE location_id='{$id}'")) {
                    $responseCode = 200;
                    $status = 'success';
                    $msg = 'Locations update successfully';
                } else {
                    $msg = 'Something went wrong while updating locations';
                }
            } else {
                $msg = 'Invalid locations details found';
            }
        }
    } else {
        $msg = 'Kindly select locations state';
    }
} else {
    $msg = 'Locations name cannot be blank';
}

http_response_code($responseCode);

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

echo json_encode($result, true);