<?php

require_once LOGIN_CHECK;

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';
$isSuccessResponse = false;
$errors = [];

// Sanitize POST data
$packageId = $_POST['packageId'] ?? null;
$countryId = $_POST['countryId'] ?? null;
$title = $_POST['title'] ?? null;
$promoText = $_POST['promoText'] ?? null;
$appointment = $_POST['appointment'] ?? null;
$visaType = $_POST['visaType'] ?? null;
$stayUpto = $_POST['stayUpto'] ?? null;
$validity = $_POST['validity'] ?? null;
$entryType = $_POST['entryType'] ?? null;
$govermentFees = $_POST['govermentFees'] ?? null;
$priceMrp = $_POST['priceMrp'] ?? null;
$price = $_POST['price'] ?? null;
$planDisclaimer = $_POST['planDisclaimer'] ?? null;
$visaProcessingTime = $_POST['visaProcessingTime'] ?? null;
$visaApplicationCenters = $_POST['visaApplicationCenters'] ?? null;
$isFeatured = $_POST['isFeatured'] ?? 0;

$image = $_POST['image'] ?? [];
$featuredImage = $image['featureImage'] ?? '';

// Validate fields
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
    'govermentFees' => 'Please enter Government VISA fees.',
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

if (empty($errors)) {

    // Escape all inputs
    $packageId = (int) $packageId;
    $countryId = (int) $countryId;
    $title = mysqli_real_escape_string($connection, $title);
    $promoText = mysqli_real_escape_string($connection, $promoText);
    $appointment = mysqli_real_escape_string($connection, $appointment);
    $visaType = mysqli_real_escape_string($connection, $visaType);
    $stayUpto = mysqli_real_escape_string($connection, $stayUpto);
    $validity = mysqli_real_escape_string($connection, $validity);
    $entryType = mysqli_real_escape_string($connection, $entryType);
    $govermentFees = mysqli_real_escape_string($connection, $govermentFees);
    $priceMrp = mysqli_real_escape_string($connection, $priceMrp);
    $price = mysqli_real_escape_string($connection, $price);
    $planDisclaimer = mysqli_real_escape_string($connection, $planDisclaimer);
    $visaProcessingTime = mysqli_real_escape_string($connection, $visaProcessingTime);
    $visaApplicationCenters = mysqli_real_escape_string($connection, $visaApplicationCenters);
    $isFeatured = (int) $isFeatured;
    $featuredImage = mysqli_real_escape_string($connection, $featuredImage);

    $checkPackage = mysqli_query($connection, "SELECT visa_package_id FROM {$tableNamePrefix}package WHERE visa_package_id=$packageId AND visa_package_status=1 AND visa_package_site_id=" . SITE_ID);

    if ($checkPackage && mysqli_num_rows($checkPackage) > 0) {

        $checkCountry = mysqli_query($connection, "SELECT visa_country_id FROM {$tableNamePrefix}country WHERE visa_country_id = '$countryId' AND visa_country_status = 1 AND visa_country_site_id = " . SITE_ID);

        if ($checkCountry && mysqli_num_rows($checkCountry) > 0) {

            $updateQuery = "UPDATE {$tableNamePrefix}package SET
                visa_package_country_id='$countryId',
                visa_package_title='$title',
                visa_package_promo_text='$promoText',
                visa_package_appointment_time='$appointment',
                visa_package_visa_type='$visaType',
                visa_package_stay_duration='$stayUpto',
                visa_package_validity_duration='$validity',
                visa_package_entry_type='$entryType',
                visa_package_disclaimer='$planDisclaimer',
                visa_package_schedule='$visaProcessingTime',
                visa_package_center='$visaApplicationCenters',
                visa_package_goverment_fee='$govermentFees',
                visa_package_mrp='$priceMrp',
                visa_package_price='$price',
                visa_package_is_featured='$isFeatured',
                visa_package_feature_image='$featuredImage'
                WHERE visa_package_id='$packageId' AND visa_package_site_id=" . SITE_ID;

            if (mysqli_query($connection, $updateQuery)) {
                mysqli_query($connection, 'COMMIT');
                $httpStatus = 200;
                $status = 'success';
                $msg = 'Package details updated successfully.';
                $isSuccessResponse = true;
            } else {
                mysqli_query($connection, 'ROLLBACK');
                $msg = 'Failed to update package record.';
            }
        } else {
            $msg = 'Invalid country selected.';
        }
    } else {
        $msg = 'Invalid or inactive package.';
    }
}

// Final response
$result = [
    'status' => $status,
    'msg' => $msg,
    'id' => $packageId,
    'errors' => $errors
];

http_response_code($httpStatus);
header('Content-Type: application/json');
echo json_encode($result);