<?php

require_once LOGIN_CHECK;

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';
$validRequest = true;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    extract($_GET);

    $order_id = str_replace('BYV00', '', $referenceNumber);

    $checkApplicationQuery = "SELECT * FROM " . TABLE_PREFIX . "order INNER JOIN " . TABLE_PREFIX . "transaction ON transaction_order_id=order_id AND transaction_type='visa_application' AND order_site_id=" . SITE_ID . " {$tableNamePrefix}application ON  INNER JOIN " . TABLE_PREFIX . "user ON visa_application_user_id=user_id AND user_mobile='{$contactInfo}' INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=user_id WHERE transaction_order_id={$order_id} visa_application_id='{$referenceNumber}' AND transaction_site_id=" . SITE_ID . " AND visa_application_site_id=" . SITE_ID;

    echo $checkApplicationQuery;
    exit();

    if ($checkApplication = mysqli_query($connection, $checkApplicationQuery)) {
        if (mysqli_num_rows($checkApplication) != 0) {
            $httpStatus = 200;
            $status = 'success';
            $msg = mysqli_fetch_assoc($checkApplication);
        } else {
            $httpStatus = 400;
            $status = 'warning';
            $msg = 'Invalid application status found';
        }
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
    'data' => ((isset($responseData)) ? $responseData : [])
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);