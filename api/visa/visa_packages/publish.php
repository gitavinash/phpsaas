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

        $insertQuery = "INSERT INTO {$tableNamePrefix}package (
            visa_package_country_id,
            visa_package_site_id,
            visa_package_user_id,
            visa_package_title,
            visa_package_promo_text,
            visa_package_appointment_time,
            visa_package_visa_type,
            visa_package_stay_duration,
            visa_package_validity_duration,
            visa_package_entry_type,
            visa_package_disclaimer,
            visa_package_schedule,
            visa_package_center,
            visa_package_mrp,
            visa_package_price,
            visa_package_is_featured,
            visa_package_feature_image,
            visa_package_status,
            visa_package_created,
            visa_package_published
        ) VALUES (
            '{$countryId}',
            '" . SITE_ID . "',
            " . USER_ID . ",
            '{$title}',
            '{$promoText}',
            '{$appointment}',
            '{$visaType}',
            '{$stayUpto}',
            '{$validity}',
            '{$entryType}',
            '{$planDisclaimer}',
            '{$visaProcessingTime}',
            '{$visaApplicationCenters}',
            '{$priceMrp}',
            '{$price}',
            '{$isFeatured}',
            '{$featuredImage}',
            '1',
            NOW(),
            NOW()
        )";

        if (mysqli_query($connection, $insertQuery)) {
            $isSuccessResponse = true;
            $packageId = mysqli_insert_id($connection);
        } else {
            $msg = 'Failed to insert package record.';
        }
    } else {
        $msg = 'Invalid country package request received.';
    }

    if ($isSuccessResponse) {
        $httpStatus = 201;
        $status = 'success';
        $msg = 'Package details have been successfully added.';
        mysqli_query($connection, 'COMMIT');
    } else {
        $status = 'error';
        $msg = $msg ?? 'Something went wrong while trying to create new package.';
        mysqli_query($connection, 'ROLLBACK');
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