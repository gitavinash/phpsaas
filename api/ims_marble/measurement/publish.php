<?php

require_once LOGIN_CHECK;
$responseCode = 500;
$submitForm = false;
$status = 'warning';
$requiredFields = [
    'productType' => 'Product type is required',
    'measurementType' => 'Measurement type is required',
    'calculationType' => 'Calculation type is required',
];

// Check required fields
foreach ($requiredFields as $field => $errorMsg) {
    if (empty($$field)) {
        $msg = $errorMsg;
        break;
    }
    $msg = '';
}

// If all required fields are set, proceed to submit form
if (empty($msg)) {
    $submitForm = true;
}

// Handle form submission
if ($submitForm) {
    $configurationValue = array(
        'description' => $description,
        'measurementType' => $measurementType,
        'calculationType' => $calculationType
    );

    $configurationValue = json_encode($configurationValue, TRUE);

    $configurationName = strtolower('measurement_' . $productType);

    if ($checkConfiguration = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_key='{$configurationName}' AND site_configuration_status=1 AND site_configuration_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkConfiguration) == 0) {
            if (mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_configuration(site_configuration_site_id,site_configuration_key, site_configuration_value, site_configuration_status) VALUES('" . SITE_ID . "', '{$configurationName}', '{$configurationValue}',1)")) {
                $configurationId = mysqli_insert_id($connection);
                $status = 'success';
                $responseCode = 201;
                $msg = 'New measurement added successfully';
            } else {
                $status = 'error';
                if (DEVELOPMENT_MODE == TRUE) {
                    $msg = mysqli_error($connection);
                } else {
                    $msg = "Something went wrong while creating new measurement";
                }
            }
        } else {
            $msg = 'Product type is already exist';
        }
    } else {
        if (DEVELOPMENT_MODE == TRUE) {
            $msg = mysqli_error($connection);
        }
    }
}

// Set HTTP response code
http_response_code($responseCode);

// Prepare result array
$result = [
    'status' => $status ? $status : 'error',
    'configurationId' => ((isset($configurationId)) ? $configurationId : 0),
    'msg' => $msg ?? 'Invalid response',
];

// Output JSON result
echo json_encode($result);
exit();