<?php

require_once LOGIN_CHECK;

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

$checkCountryCode = '';
$checkCountryName = '';

if (isset($id)) {
    $countryId = base64_decode($id);
    if ($name != '') {
        if ($code != '') {
            if ($requirements != '') {
                if ($information != '') {
                    if ($faqs != '') {
                        if ($visaTypes != '') {
                            $validRequest = true;
                        } else {
                            $msg = 'At least one visa service is required';
                        }
                    } else {
                        $msg = 'FAQs is required';
                    }
                } else {
                    $msg = 'Important information is required';
                }
            } else {
                $msg = 'Document requirements is required';
            }
        } else {
            $msg = 'Country code is required';
        }
    } else {
        $msg = 'Country name is required';
    }
} else {
    $msg = 'Country id is required';
}


if ($validateCountryId = mysqli_query($connection, "SELECT visa_country_id, visa_country_code, visa_country_title FROM {$tableNamePrefix}country WHERE visa_country_id='{$countryId}' AND visa_country_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($validateCountryId) != 0) {
        extract(mysqli_fetch_assoc($validateCountryId));

        // Check country code
        $checkItemCode = mysqli_query($connection, "SELECT visa_country_title, visa_country_code FROM {$tableNamePrefix}country WHERE visa_country_code='{$visa_country_code}' AND visa_country_code='{$visa_country_title}' AND visa_country_status=1 AND visa_country_site_id=" . SITE_ID);
        if (mysqli_num_rows($checkItemCode) == 0) {
            $validRequest = true;
        } else {
            $httpStatus = 409;
            $msg = 'Country is already exist in records test';
        }

        mysqli_query($connection, "START TRANSACTION");
        if ($code != $visa_country_code) {
            $checkCountryCode = "visa_country_code='{$code}'" . (($checkCountryCode != '') ? " OR " : " AND ");
        }
        if ($name != $visa_country_title) {
            $checkCountryName = "visa_country_title='{$name}' AND ";
        }

        if ($checkCountryCode != '' || $checkCountryName != '') {
            if ($checkCountry = mysqli_query($connection, "SELECT visa_country_id FROM {$tableNamePrefix}country WHERE {$checkCountryCode} {$checkCountryName} visa_country_status=1 AND visa_country_site_id=" . SITE_ID)) {
                if (mysqli_num_rows($checkCountry) != 0) {
                    $validRequest = false;
                    $msg = 'Country name or code already exists';
                }
            } else {
                $validRequest = false;
            }
        }
    } else {
        $validRequest = false;
        $msg = "No country record found";
    }
} else {
    $validRequest = false;
}

if (isset($validRequest)) {
    require_once MODULES . 'imageUploader.php';

    if (!isset($image['featureImage'])) {
        $featureImage = '';
    }

    if ($featureImage == '') {
        $featureImage = $oldFeatureImage;
    }

    if ($updateCountry = mysqli_query($connection, "UPDATE {$tableNamePrefix}country SET visa_country_title='{$name}', visa_country_code='{$code}', visa_country_documents='{$requirements}', visa_country_information='{$information}', visa_country_image='{$featureImage}', visa_country_faq='{$faqs}', visa_country_services='{$visaTypes}', visa_country_is_featured='{$isFeatured}', visa_country_type='{$countryType}' WHERE visa_country_id='{$countryId}' AND visa_country_site_id=" . SITE_ID)) {
        $httpStatus = 201;
        $status = 'success';
        $msg = 'Country details are updated successfully';
        mysqli_query($connection, 'COMMIT');
    } else {
        $status = 'error';
        $msg = ((isset($msg)) ? $msg : 'Something went wrong while trying to create new country');
        mysqli_query($connection, 'ROLLBACK');
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
    'id' => ((isset($countryId)) ? $countryId : 0),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);