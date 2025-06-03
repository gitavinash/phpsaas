<?php

require_once LOGIN_CHECK;

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';
$validRequest = true;

$rawData = file_get_contents("php://input");

if (!empty($rawData)) {
    $json = json_decode($rawData, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        if (isset($applicationId)) {
            $getOldJsonObject = mysqli_query($connection, "SELECT visa_application_data FROM {$tableNamePrefix}application WHERE visa_application_id='{$applicationId}' AND visa_application_site_id=" . SITE_ID);
            if (mysqli_num_rows($getOldJsonObject) > 0) {
                $oldJsonObject = mysqli_fetch_assoc($getOldJsonObject);
                $oldData = json_decode($oldJsonObject['visa_application_data'], true);
            }
        } else {
            $oldData = [];
        }

        // Decode new raw data (if it's JSON string)
        $newData = is_string($rawData) ? json_decode($rawData, true) : (array)$rawData;

        // Merge old data with new data — new data overwrites old on key conflict
        $combinedData = array_merge($oldData, $newData);

        unset($combinedData['packageId'], $combinedData['applicationId']);

        // Encode back to JSON if needed
        $combinedJson = json_encode($combinedData);

        if (isset($applicationId)) {
            $query = "UPDATE {$tableNamePrefix}application SET visa_application_data='" . mysqli_real_escape_string($connection, $combinedJson) . "' WHERE visa_application_id='{$applicationId}' AND visa_application_user_id=" . USER_ID . " AND visa_application_site_id=" . SITE_ID;
        } else {
            $query = "INSERT INTO {$tableNamePrefix}application (visa_application_site_id, visa_application_user_id, visa_application_package_id, visa_application_data, visa_application_status, visa_application_time) VALUES ('" . SITE_ID . "', '" . USER_ID . "', '{$packageId}', '" . mysqli_real_escape_string($connection, $combinedJson) . "', " . SITE_ID . ")";
        }

        echo $query;
    } else {
        $httpStatus = 400;
        $msg = "Invalid JSON: " . json_last_error_msg();
    }
} else {
    $httpStatus = 400;
    $msg = "No raw input received.";
}


$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
    'id' => ((isset($countryId)) ? $countryId : 0),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);