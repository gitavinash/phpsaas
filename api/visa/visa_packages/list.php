<?php

header('Content-Type: application/json');

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

// Default response values
$httpStatus = 400;
$status = 'warning';
$msg = 'Invalid request';
$packageList = [];

// Input validation
if (!isset($country) || trim($country) === '') {
    $msg = 'Country is required.';
} else {
    // Escape country for safety
    $country = str_replace('_', ' ', trim($country));
    $countryEscaped = mysqli_real_escape_string($connection, $country);

    // Prepare visa purpose filter if provided
    $visaPurposeFilter = '';
    if (isset($purpose) && trim($purpose) !== '') {
        $purposeEscaped = mysqli_real_escape_string($connection, trim($purpose));
        $visaPurposeFilter = "AND visa_package_visa_type = '{$purposeEscaped}'";
    }

    // SQL Query
    $query = "
        SELECT 
            visa_package_id,
            visa_package_title,
            visa_package_mrp,
            visa_package_price,
            visa_package_stay_duration,
            visa_package_validity_duration
        FROM {$tableNamePrefix}package
        INNER JOIN {$tableNamePrefix}country 
            ON visa_country_id = visa_package_country_id
        WHERE visa_country_title = '{$countryEscaped}'
            {$visaPurposeFilter}
            AND visa_package_status = 1
            AND visa_package_site_id = " . SITE_ID;

    $checkPackage = mysqli_query($connection, $query);

    if ($checkPackage) {
        if (mysqli_num_rows($checkPackage) > 0) {
            while ($row = mysqli_fetch_assoc($checkPackage)) {
                $packageList[] = $row;
            }
            $status = 'success';
            $httpStatus = 200;
            $msg = 'Packages found.';
        } else {
            $msg = "No packages found for {$country}.";
        }
    } else {
        $msg = 'Database query failed: ' . mysqli_error($connection);
    }
}

// Final response
http_response_code($httpStatus);
echo json_encode([
    'status' => $status,
    'msg' => $msg,
    'packageList' => $packageList,
]);