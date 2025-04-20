<?php

require_once LOGIN_CHECK;

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

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

if (isset($validRequest)) {
    // require_once MODULES . 'imageUploader.php';

    if (!isset($image['featureImage'])) {
        $featureImage = '';
    }

    // Check country code

    if ($code != '' || $name != '') {
        $checkItemCode = mysqli_query($connection, "SELECT visa_country_title, visa_country_code FROM {$tableNamePrefix}country WHERE visa_country_code='{$code}' AND visa_country_code='{$name}' AND visa_country_status=1 AND visa_country_site_id=" . SITE_ID);
        if (mysqli_num_rows($checkItemCode) == 0) {
            $validRequest = true;
        } else {
            $httpStatus = 409;
            $msg = 'Country is already exist in records';
        }
    } else {
        $validRequest = true;
    }

    if (isset($validRequest)) {
        mysqli_query($connection, "START TRANSACTION");

        if ($checkCountry = mysqli_query($connection, "SELECT visa_country_id FROM {$tableNamePrefix}country WHERE visa_country_code='{$code}' OR visa_country_title='{$name}' AND visa_country_site_id=" . SITE_ID)) {
            if (mysqli_num_rows($checkCountry) == 0) {
                $insertBlock = mysqli_query($connection, "INSERT INTO {$tableNamePrefix}country(visa_country_site_id, visa_country_published_by, visa_country_title, visa_country_code, visa_country_documents, visa_country_information, visa_country_image, visa_country_faq, visa_country_services, visa_country_is_featured, visa_country_status, visa_country_publish) VALUES(" . SITE_ID . ", " . USER_ID . ", '{$name}', '{$code}', '{$requirements}', '{$information}', '{$featureImage}', '{$faqs}', '{$visaTypes}', '{$isFeatured}', 1, now())");
                $countryId = mysqli_insert_id($connection);
            } else {
                $insertBlock = false;
                $msg = 'Country name or code already exists';
            }
        }

        if ($insertBlock) {
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
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
    'id' => ((isset($countryId)) ? $countryId : 0),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);