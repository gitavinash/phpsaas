<?php

header('Content-Type: application/json');
require_once LOGIN_CHECK;
$httpStatus = 405;
$validRequest = false;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($id != '') {
        $salesId = base64_decode($id);
        if ($mobile != '') {
            $mobile = ltrim($mobile, '0');
            if (is_numeric($mobile)) {
                $validRequest = true;
            } else {
                $msg = 'Enter a valid mobile number';
            }
        } else {
            $msg = 'Mobile number cannot be blank';
        }
    } else {
        $msg = 'Sales number is required';
    }
}

if ($validRequest) {
    if ($checkSales = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}sales WHERE sales_id={$salesId} AND sales_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkSales) != 0) {
            extract(mysqli_fetch_assoc($checkSales));
            $inventoryData = array();
            $salesData = json_decode($sales_data, TRUE);
            foreach ($salesData as $blockName => $slabsDetails) {

                $slabsDetails = explode(',', $slabsDetails);
                $inventoryData[str_replace('salesBlocks_', '', $blockName)] = $slabsDetails;
            }
            $httpStatus = 200;
            $msg = 'Inventory details fetch successfully';
            $status = 'success';
        } else {
            $status = 'warning';
            $msg = 'No record found';
            $httpStatus = 204;
        }
    } else {
        $httpStatus = 500;
        $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something went wrong');
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid request method : ' . $_SERVER['REQUEST_METHOD']),
    'blocks' => ((isset($inventoryData)) ? $inventoryData : []),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);