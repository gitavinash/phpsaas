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
    $package_id = base64_decode($packageId);
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

            // Decode personsData from the original JSON
            $personsDataObject = json_decode($oldData['personsData'], TRUE);

            if (is_string($personsDataObject)) {
                $personsDataObject = json_decode($personsDataObject, true);
            }

            foreach ($_FILES as $field => $fileData) {
                // Match input names like passportCopy_0, profilePhoto_1, supportingDocuments_0[1], etc.
                if (preg_match('/^(passportCopy|profilePhoto|supportingDocuments)_(\d+)(?:\[(\d+)\])?$/', $field, $matches)) {
                    $type = $matches[1];     // passportCopy, profilePhoto, supportingDocuments
                    $personIndex = (int)$matches[2]; // Person index in the array

                    if (!isset($personsDataObject[$personIndex])) {
                        continue; // Skip if person doesn't exist
                    }

                    $path = handleFileUpload($fileData); // Your upload function

                    if ($path) {
                        // Initialize 'media' key if not already set
                        if (!isset($personsDataObject[$personIndex]) || !is_array($personsDataObject[$personIndex])) {
                            continue; // Skip if the person data is missing or not an array
                        }

                        if (!isset($personsDataObject[$personIndex]['media']) || !is_array($personsDataObject[$personIndex]['media'])) {
                            $personsDataObject[$personIndex]['media'] = [
                                'passport' => null,
                                'profilePhoto' => null,
                                'documents' => []
                            ];
                        }

                        // Attach the uploaded file path to the appropriate field
                        switch ($type) {
                            case 'passportCopy':
                                $personsDataObject[$personIndex]['media']['passport'] = $path;
                                break;

                            case 'profilePhoto':
                                $personsDataObject[$personIndex]['media']['profilePhoto'] = $path;
                                break;

                            case 'supportingDocuments':
                                $personsDataObject[$personIndex]['media']['documents'][] = $path;
                                break;
                        }
                    }
                }
            }

            // Re-encode the modified personsData back into oldData
            $oldData['personsData'] = $personsDataObject;
        } else {
            $rawData = array(
                'arrivalDate' => date('Y-d-m', strtotime($arrivalDate)),
                'departureDate' => date('Y-d-m', strtotime($departureDate)),
                'purposeOfTravel' => trim($purposeOfTravel),
                'visaEntry' => trim($visaEntry),
                'accommodation' => trim($accommodation)
            );
        }

        // Decode new raw data (if it's JSON string)
        $newData = is_string($rawData) ? json_decode($rawData, true) : (array)$rawData;

        // Merge old data with new data — new data overwrites old on key conflict
        $combinedData = array_merge($oldData, $newData);
        // Encode back to JSON if needed

        $combinedJson = json_encode($combinedData);

        $query = "UPDATE {$tableNamePrefix}application SET visa_application_data='" . mysqli_real_escape_string($connection, $combinedJson) . "' WHERE visa_application_id='{$application_id}' AND visa_application_user_id=" . USER_ID . " AND visa_application_site_id=" . SITE_ID;
    } else {
        $query = "INSERT INTO {$tableNamePrefix}application (visa_application_site_id, visa_application_user_id, visa_application_package_id, visa_application_persons, visa_application_data, visa_application_status, visa_application_time) VALUES ('" . SITE_ID . "', '" . USER_ID . "', '{$package_id}', '{$totalPersons}', '" . mysqli_real_escape_string($connection, $rawData) . "', 0, NOW())";
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