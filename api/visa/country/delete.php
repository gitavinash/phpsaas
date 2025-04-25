<?php

require_once LOGIN_CHECK;

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';
$validRequest = true;

if (!isset($id)) {
    $msg = 'Country id is required';
    $validRequest = false;
} else {
    $countryId = base64_decode($id);
}

if ($validRequest == true) {
    if ($validateCountryId = mysqli_query($connection, "SELECT visa_country_id, visa_country_code, visa_country_title FROM {$tableNamePrefix}country WHERE visa_country_id='{$countryId}' AND visa_country_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($validateCountryId) != 0) {
            if (mysqli_query($connection, "DELETE FROM {$tableNamePrefix}country WHERE visa_country_id='{$countryId}' AND visa_country_site_id=" . SITE_ID)) {
                $httpStatus = 201;
                $status = 'success';
                $msg = 'Country delete successfully';
            }
        } else {
            $msg = 'No country information found';
        }
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
    'id' => ((isset($countryId)) ? $countryId : 0),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);