<?php
header('Content-Type: application/json');
$httpStatus = 405;

$exportFor = ((isset($format)) ? $format : 'default');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $dataLimit = ((isset($limit)) ? $limit : 250);
    $startFrom = ((isset($start)) ? $start : 0);

    $customerDataQuery = "SELECT user_mobile FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "site_access ON site_access_user_id=user_id WHERE site_access_site_id=" . SITE_ID . " LIMIT {$startFrom}, {$dataLimit}";

    if ($customerData = mysqli_query($connection, $customerDataQuery));
    $customerList = [];

    if (mysqli_num_rows($customerData) > 0) {

        while ($customerInfo = mysqli_fetch_assoc($customerData)) {

            $mobile = preg_replace('/\D/', '', $customerInfo['user_mobile']);

            // Remove country code if present
            if (strlen($mobile) === 12 && substr($mobile, 0, 2) === '91') {
                $mobile = substr($mobile, 2);
            }

            // Validate Indian mobile number
            if (preg_match('/^[6-9]\d{9}$/', $mobile)) {
                $customerList[] = $mobile;
            }
        }

        $customerList = array_values(array_unique($customerList));

        $httpStatus = 200;
        $status = 'success';
        $msg = count($customerList)
            ? 'Data fetched successfully'
            : 'No valid mobile numbers found';
    } else {

        $msg = DEVELOPMENT_MODE
            ? $customerDataQuery
            : 'No customer data found';
    }
} else {
    $msg = ((DEVELOPMENT_MODE == true) ? mysqli_error($connection) : 'Something went wrong while fetching customers data');
}

if ($exportFor == 'emvour') {
    $result = array_map(function ($mobile) {
        return [
            'receiver' => $mobile
        ];
    }, $customerList);
} else {

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Invalid request method -> ' . $_SERVER['REQUEST_METHOD']),
        'customerList' => ((isset($customerList)) ? $customerList : ''),
    );
}

http_response_code($httpStatus);
echo json_encode($result, TRUE);
