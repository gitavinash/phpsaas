<?php

require_once LOGIN_CHECK;

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';
$validRequest = true;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    extract($_POST);
    unset($_POST['packageId']);
    if (isset($applicationId)) {
        $application_id = base64_decode($_POST['applicationId']);
    }
    unset($_POST['applicationId']);
    $rawData = json_encode($_POST, true);

    if (isset($application_id)) {

        $getOldJsonObject = mysqli_query($connection, "SELECT visa_application_data FROM {$tableNamePrefix}application WHERE visa_application_id='{$application_id}' AND visa_application_site_id=" . SITE_ID);
        if (mysqli_num_rows($getOldJsonObject) != 0) {
            $oldJsonObject = mysqli_fetch_assoc($getOldJsonObject);
            $oldData = json_decode($oldJsonObject['visa_application_data'], true);
        } else {
            $oldData = [];
        }
        if (!empty($_FILES)) {
            require_once MODULES . 'upload.php';

            $media = [
                'passport' => [],
                'profilePhoto' => [],
                'documents' => [],
            ];

            foreach ($_FILES as $field => $fileData) {
                // Match field names like passportCopy_0, profilePhoto_0, supportingDocuments_0[0]
                if (preg_match('/^(passportCopy|profilePhoto|supportingDocuments)_(\d+)(?:\[(\d+)\])?$/', $field, $matches)) {
                    $type = $matches[1];          // passportCopy / profilePhoto / supportingDocuments
                    $personIndex = $matches[2];   // 0, 1, 2...
                    $fileIndex = $matches[3] ?? null;

                    $path = handleFileUpload($fileData);

                    if ($path) {
                        if ($type === 'passportCopy') {
                            $media['passport'][$personIndex] = $path;
                        } elseif ($type === 'profilePhoto') {
                            $media['profilePhoto'][$personIndex] = $path;
                        } elseif ($type === 'supportingDocuments') {
                            $media['documents'][$personIndex][] = $path;
                        }
                    }
                }
            }

            // Combine with existing data
            $rawData = json_encode($media, JSON_UNESCAPED_SLASHES);
        }

        // Decode new raw data (if it's JSON string)
        $newData = is_string($rawData) ? json_decode($rawData, true) : (array)$rawData;

        // Merge old data with new data — new data overwrites old on key conflict
        $combinedData = array_merge($oldData, $newData);

        $packageId = base64_decode($packageId);

        // Encode back to JSON if needed
        $combinedJson = json_encode($combinedData);

        $query = "UPDATE {$tableNamePrefix}application SET visa_application_data='" . mysqli_real_escape_string($connection, $combinedJson) . "' WHERE visa_application_id='{$application_id}' AND visa_application_user_id=" . USER_ID . " AND visa_application_site_id=" . SITE_ID;
    } else {
        $query = "INSERT INTO {$tableNamePrefix}application (visa_application_site_id, visa_application_user_id, visa_application_package_id, visa_application_data, visa_application_status, visa_application_time) VALUES ('" . SITE_ID . "', '" . USER_ID . "', '{$packageId}', '" . mysqli_real_escape_string($connection, $rawData) . "', 0, NOW())";
    }

    if (mysqli_query($connection, $query)) {
        $status = 'success';
        $msg = 'Draft saved successfully.';
        $httpStatus = 200;
        if (!isset($applicationId)) {
            $applicationId = base64_encode(mysqli_insert_id($connection));
        }
    } else {
        $httpStatus = 500;
        $msg = ((DEVELOPMENT_MODE == true) ? mysqli_error($connection) : 'Database error occurred while saving the draft.');
    }
} else {
    $msg = 'Invalid request method';
}


$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
    'applicationId' => ((isset($applicationId)) ? $applicationId : 0),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);