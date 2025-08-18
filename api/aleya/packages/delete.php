<?php

require_once LOGIN_CHECK;

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid Request';

if (isset($id)) {
    $deleteData = true;
    $tourPackageId = base64_decode($id);
} else {
    $msg = 'Package id is required';
}

if ($checkData = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "tour_package WHERE tour_package_id='{$tourPackageId}'AND tour_package_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkData) == 1) {
        extract(mysqli_fetch_assoc($checkData));
        if (isset($deleteData)) {
            if (mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "tour_package WHERE tour_package_id='{$tourPackageId}' AND tour_package_site_id=" . SITE_ID)) {
                $httpStatus = 201;
                $status = 'success';
                $msg = 'Package is deleted successfully';
            } else {
                $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : "Something went wrong while deliting price");
            }
        }
    } else {
        $httpStatus = 204;
        $msg = 'Package id is not found';
    }
} else {
    $msg = 'Invalid package id';
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);