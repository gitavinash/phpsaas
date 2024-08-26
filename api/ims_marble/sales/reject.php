<?php

header('Content-Type: application/json');
require_once LOGIN_CHECK;

$httpStatus = 405;
$validRequest = false;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($id != '') {
        $requestId = base64_decode($id);
        if ($agent != '') {
            $agentId = base64_decode($agent);
            $validRequest = true;
        } else {
            $msg = 'Agent parameter is missing';
        }
    } else {
        $msg = 'requestId parameter is missing';
    }
}

if ($validRequest) {

    mysqli_query($connection, "START TRANSACTION");

    if ($checkSales = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}sales WHERE sales_id='{$requestId}' AND sales_agent_id='{$agentId}' AND sales_status=0 AND sales_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkSales) != 0) {
            extract(mysqli_fetch_assoc($checkSales));

            mysqli_query($connection, "START TRANSACTION");

            $updateSalesQuery = "UPDATE {$tableNamePrefix}sales SET sales_status=4 WHERE sales_id='{$requestId}' AND sales_status=0 AND sales_site_id=" . SITE_ID;
            $updateSales = mysqli_query($connection, $updateSalesQuery);

            if ($createTempTable && $updateBlockSlab && $updateBlock && $updateBlockStatus && $dropTempTable && $updateSales) {
                $status = 'success';
                $httpStatus = 201;
                $msg = 'Sales request is rejected';
                mysqli_query($connection, "COMMIT");
            } else {
                $httpStatus = 500;
                $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something went wrong');
                mysqli_query($connection, "ROLLBACK");
            }
        } else {
            $status = 'warning';
            $httpStatus = 204;
            $msg = 'No record found';
        }
    } else {
        $httpStatus = 500;
        $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something went wrong');
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid request method : ' . $_SERVER['REQUEST_METHOD']),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);