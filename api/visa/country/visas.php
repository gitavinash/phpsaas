<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

if ($country != '') {
    $countryName = str_replace('_', ' ', $country);
    if ($checkCountry = mysqli_query($connection, "SELECT visa_country_services FROM {$tableNamePrefix}country WHERE visa_country_title='{$countryName}' AND visa_country_status=1 AND visa_country_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkCountry) != 0) {
            extract(mysqli_fetch_assoc($checkCountry));
            $msg = 'Country data found successfully';
            $httpStatus = 200;
            $countryData = json_decode($visa_country_services, true);
        } else {
            $msg = 'Invalid country request found';
        }
    }
} else {
    $msg = 'Country is missing';
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
    'countryList' => ((isset($countryData)) ? $countryData : 0),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);