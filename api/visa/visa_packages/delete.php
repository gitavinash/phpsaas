<?php

require_once LOGIN_CHECK;

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';
$validRequest = true;

if (!isset($id)) {
    $msg = 'Package id is required';
    $validRequest = false;
} else {
    $packageId = base64_decode($id);
}

if ($validRequest == true) {
    if ($validatePackageId = mysqli_query($connection, "SELECT visa_package_id FROM {$tableNamePrefix}package WHERE visa_package_id='{$packageId}' AND visa_package_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($validatePackageId) != 0) {
            if (mysqli_query($connection, "DELETE FROM {$tableNamePrefix}package WHERE visa_package_id='{$packageId}' AND visa_package_site_id=" . SITE_ID)) {
                $httpStatus = 201;
                $status = 'success';
                $msg = 'Package delete successfully';
            }
        } else {
            $msg = 'No package information found';
        }
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
    'id' => ((isset($packageId)) ? $packageId : 0),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);