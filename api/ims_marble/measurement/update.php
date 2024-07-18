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

    $id = base64_decode($configurationId);

    if ($checkConfiguration = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_id='{$id}' AND site_configuration_status=1 AND site_configuration_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkConfiguration) != 0) {
            if (mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "site_configuration SET site_configuration_key='{$configurationName}', site_configuration_value='{$configurationValue}' WHERE site_configuration_id='{$id}' AND site_configuration_status=1 AND site_configuration_site_id=" . SITE_ID)) {
                $status = 'success';
                $responseCode = 201;
                $msg = 'Measurement updated successfully';
            } else {
                $status = 'error';
                if (DEVELOPMENT_MODE == TRUE) {
                    $msg = mysqli_error($connection);
                } else {
                    $msg = "Something went wrong while updating measurement";
                }
            }
        } else {
            $responseCode = 404;
            $msg = 'Information not found';
            echo "SELECT * FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_id='{$id}' AND site_configuration_status=1 AND site_configuration_site_id=" . SITE_ID;
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
    'status' => ((isset($status)) ? $status : 'error'),
    'configurationId' => ((isset($configurationId)) ? $configurationId : 0),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response')
];

// Output JSON result
echo json_encode($result);
exit();
