<?php

require_once LOGIN_CHECK;

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';
$validRequest = true;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST);
    if (isset($applicationId)) {
        if ($applicationId != '') {
            $applicationId = base64_decode($applicationId);
            if ($checkApplication = mysqli_query($connection, "SELECT visa_application_user_id, visa_application_data, user_email, user_mobile FROM {$tableNamePrefix}application INNER JOIN " . TABLE_PREFIX . "user ON user_id=visa_application_user_id WHERE visa_application_id='{$applicationId}' AND visa_application_status=0 AND visa_application_user_id='" . USER_ID . "' AND visa_application_site_id=" . SITE_ID)) {
                if (mysqli_num_rows($checkApplication) != 0) {
                    $responseData = (mysqli_fetch_assoc($checkApplication));
                    if ($updateApplicationStatus = mysqli_query($connection, "UPDATE {$tableNamePrefix}application SET visa_application_status=2 WHERE visa_application_id='{$applicationId}' AND visa_application_site_id=" . SITE_ID)) {
                        $httpStatus = 200;
                        $status = 'success';
                        $msg = 'Application submit successfully';

                        $visaData = json_decode($responseData['visa_application_data'], true);

                        // Send email alerts


                    } else {
                        $msg = 'Something went wrong while updating status';
                    }
                } else {
                    $httpStatus = 204;
                    $msg = 'Application details not found';
                }
            }
        } else {
            $httpStatus = 400;
            $msg = "Required paramater is empty";
        }
    } else {
        $httpStatus = 400;
        $msg = 'Required paramater is missing';
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
    'data' => ((isset($responseData)) ? $responseData : [])
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);