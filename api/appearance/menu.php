<?php

require_once USER_FUNCTIONS;
require_once LOGIN_CHECK;

$httpStatus = 500;
$status = 'warning';
$msg = 'Something went wrong';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $raw = file_get_contents('php://input');
    if ($raw) {
        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $status = 'success';
            $msg = 'JSON received';
            // OPTIONAL: debug dekhna ho to is array ko $result me include karna:
            // $result['received'] = $decoded;
        } else {
            $status = 'error';
            $msg = 'Invalid JSON: ' . json_last_error_msg();
        }
    }

    $menuName = 'site_nav_' . strtolower(str_replace(' ', '_', $decoded['position']));
    $menuPositions = json_encode($decoded['items'], TRUE);

    if ($checkConfiguration = mysqli_query($connection, "SELECT site_configuration_id FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_key='{$menuName}' AND site_configuration_status AND site_configuration_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkConfiguration) != 0) {
            extract(mysqli_fetch_assoc($checkConfiguration));
            $query = "UPDATE " . TABLE_PREFIX . "site_configuration SET site_configuration_value='{$menuPositions}' WHERE site_configuration_id='{$site_configuration_id}' AND site_configuration_site_id=" . SITE_ID;
        } else {
            $query = "INSERT INTO " . TABLE_PREFIX . "site_configuration
(
    site_configuration_site_id,
    site_configuration_key,
    site_configuration_value,
    site_configuration_status
)
VALUES (
    '" . SITE_ID . "',
    '" . mysqli_real_escape_string($connection, $menuName) . "',
    '" . mysqli_real_escape_string($connection, $menuPositions) . "',
    1)";
        }
    }

    if (mysqli_query($connection, $query)) {
        $httpStatus = 201;
        $status = 'success';
        $msg = ucwords($decoded['position']) . " Menu is update successfully";
    } else {
        $msg = "Something went wrong while saving the menu";
    }
} else {
    $httpStatus = 400;
    $msg = 'Bad request';
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Something went wrong')
);


http_response_code($httpStatus);
echo json_encode($result, TRUE);