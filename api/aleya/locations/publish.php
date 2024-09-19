<?php

require_once LOGIN_CHECK;
$responseCode = 500;

if ($name != '') {

    if ($state != '') {

        $state = strtolower(str_replace(' ', '_', $state));

        require_once MODULES . 'imageUploader.php';

        if (!isset($icon)) {
            /** if feature image is not upload then set featureImage as blank */
            $icon = '';
        }

        if ($checkLocations = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "locations WHERE location_title='{$name}' AND location_state='{$state}' AND location_country='{$country}'")) {
            if (mysqli_num_rows($checkLocations) == 0) {
                if (mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "locations(location_thumbnail, location_title, location_description, location_state, location_country) VALUES('{$icon}', '{$name}', '{$description}', '{$state}', '{$country}')")) {
                    $responseCode = 201;
                    $status = 'success';
                    $msg = 'Location added successfully';
                } else {
                    $msg = 'Something went wrong while adding new location';
                }
            } else {
                $responseCode = 409;
                $msg = "Locations is already exits in " . ucwords(str_replace('_', ' ', $state));
            }
        } else {
            $msg = ((DEVELOPMENT_MODE == true) ? mysqli_error($connection) : "Something went wrong");
        }
    } else {
        $msg = 'Kindly select location state';
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
