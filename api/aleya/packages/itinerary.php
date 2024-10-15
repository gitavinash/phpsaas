<?php

require_once LOGIN_CHECK;

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if ($checkItinerary = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "tour_package WHERE tour_package_city='%%' tour_package_site_id=" . SITE_ID)) {
    }
}

$result = array(
    'status' => $status,
    'msg' => $msg,
    'id' => base64_encode((isset($itineraryId) ? $itineraryId : 0)),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
