<?php

require_once USER_FUNCTIONS;
require_once LOGIN_CHECK;

$httpStatus = 500;
$status = 'warning';
$msg = 'Something went wrong';
$data = array();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $menuFilter = trim(((isset($name)) ? "{$name}" : ''));

    $query = "SELECT site_configuration_value as data FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_key LIKE '%site_nav_{$menuFilter}%' AND site_configuration_status=1 AND site_configuration_site_id=" . SITE_ID;

    if ($checkMenu = mysqli_query($connection, $query)) {
        if (mysqli_num_rows($checkMenu) != 0) {
            extract(mysqli_fetch_assoc($checkMenu));
            $data = json_decode($data, TRUE);
            $status = 'success';
            $httpStatus = 200;
            $msg = 'Menu found';
        } else {
            $httpStatus = 204;
            $msg = 'No menu found';
        }
    }
} else {
    $httpStatus = 400;
    $msg = 'Bad request';
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Something went wrong'),
    'data' => $data
);


http_response_code($httpStatus);
echo json_encode($result, TRUE);