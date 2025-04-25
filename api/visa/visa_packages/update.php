<?php

require_once LOGIN_CHECK;

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';
$isSuccessResponse = false;
$errors = [];

// Validation
$fieldsToValidate = [
    'packageId' => 'Package id is required',
    'countryId' => 'Please select a country.',
    'title' => 'Please enter the title of package.',
    'promoText' => 'Please enter the promo text.',
    'appointment' => 'Please specify if appointment is required.',
    'visaType' => 'Please select at least one visa type.',
    'stayUpto' => 'Please enter the stay duration.',
    'validity' => 'Please enter the visa validity period.',
    'entryType' => 'Please select entry type.',
    'priceMrp' => 'Please enter without discount price.',
    'price' => 'Please enter offer price.',
    'planDisclaimer' => 'Please add a disclaimer.',
    'visaProcessingTime' => 'Please enter visa processing time.',
    'visaApplicationCenters' => 'Please enter visa application center(s).'
];

foreach ($fieldsToValidate as $field => $errorMessage) {
    if (empty($$field)) {
        $errors[$field] = $errorMessage;
    }
}

// If valid
if (empty($errors)) {
    $validRequest = true;
}

// Handle insert
if (!empty($validRequest)) {
    if ($checkPackage = mysqli_query($connection, "SELECT visa_package_id FROM {$tableNamePrefix}package WHERE visa_package_id={$packageId} AND visa_package_status=1 AND visa_package_site_id=" . SITE_ID)) {

        if (mysqli_num_rows($checkPackage) != 0) {

            // require_once MODULES . 'imageUploader.php';

            // Default value for feature image if not provided
            $featuredImage = isset($image['featureImage']) ? $image['featureImage'] : '';

            // Check if country exists
            $checkCountryQuery = "SELECT visa_country_id 
                          FROM {$tableNamePrefix}country 
                          WHERE visa_country_id = '{$countryId}' 
                            AND visa_country_status = 1 
                            AND visa_country_site_id = " . SITE_ID;

            $checkItemCode = mysqli_query($connection, $checkCountryQuery);

            if (mysqli_num_rows($checkItemCode) > 0) {

                $updateQuery = "UPDATE {$tableNamePrefix}package SET
            visa_package_country_id='{$countryId}',
            visa_package_title='{$title}',
            visa_package_promo_text='{$promoText}',
            visa_package_appointment_time='{$appointment}',
            visa_package_visa_type='{$visaType}',
            visa_package_stay_duration='{$stayUpto}',
            visa_package_validity_duration='{$validity}',
            visa_package_entry_type='{$entryType}',
            visa_package_disclaimer='{$planDisclaimer}',
            visa_package_schedule='{$visaProcessingTime}',
            visa_package_center='{$visaApplicationCenters}',
            visa_package_mrp='{$priceMrp}',
            visa_package_price='{$price}',
            visa_package_is_featured='{$isFeatured}',
            visa_package_feature_image='{$featuredImage}'
            WHERE
            visa_package_id='{$packageId}'
            AND
            visa_package_site_id=" . SITE_ID;

                if (mysqli_query($connection, $updateQuery)) {
                    $isSuccessResponse = true;
                    $packageId = mysqli_insert_id($connection);
                } else {
                    $msg = 'Failed to update package record.';
                }
            } else {
                $msg = 'Invalid country package request received.';
            }

            if ($isSuccessResponse) {
                $httpStatus = 201;
                $status = 'success';
                $msg = 'Package details have been updated successfully.';
                mysqli_query($connection, 'COMMIT');
            } else {
                $status = 'error';
                $msg = $msg ?? 'Something went wrong while trying to updating package.';
                mysqli_query($connection, 'ROLLBACK');
            }
        } else {
            $msg = 'Invalid package request found';
        }
    }
}

// Final response
$result = [
    'status' => $status,
    'msg' => $msg ?? 'Unknown error occurred.',
    'id' => $packageId ?? 0,
    'errors' => $errors,
];

http_response_code($httpStatus);
echo json_encode($result);