<?php

header('Content-Type: application/json');
$input = file_get_contents('php://input');
require_once LOGIN_CHECK;

$httpStatus = 405;
$validRequest = false;

exit();

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
            $salesData = json_decode($sales_data, TRUE);
            $slabsId = array();
            foreach ($salesData as $blockInfo) {
                $blockInfo = explode(',', $blockInfo);
                foreach ($blockInfo as $slabsInfo) {
                    $slabsInfo = explode('_', $slabsInfo);
                    $slabsId[] = $slabsInfo[0];
                }
            }
            $slabsList = implode(',', $slabsId);

            mysqli_query($connection, "START TRANSACTION");
            $createTempTableQuery = "CREATE TEMPORARY TABLE sales_qty_{$requestId} AS
                SELECT block_item_parent_id, COUNT(block_item_id) AS total_qty
                FROM {$tableNamePrefix}block_item
                WHERE block_item_id IN ({$slabsList}) AND block_item_status=1 AND block_item_site_id=" . SITE_ID . "
                GROUP BY block_item_parent_id";
            $createTempTable = mysqli_query($connection, $createTempTableQuery);

            $updateBlockSlabQuery = "UPDATE {$tableNamePrefix}block_item SET block_item_status = '2' WHERE block_item_id IN ({$slabsList}) AND block_item_site_id=" . SITE_ID;
            $updateBlockSlab = mysqli_query($connection, $updateBlockSlabQuery);

            $updateBlockQuery = "UPDATE {$tableNamePrefix}block AS b JOIN sales_qty_{$requestId} AS tq ON b.block_id = tq.block_item_parent_id SET b.block_available = b.block_available - tq.total_qty WHERE block_status=1 AND block_site_id=" . SITE_ID;
            $updateBlock = mysqli_query($connection, $updateBlockQuery);

            $updateBlockStatusQuery = "UPDATE {$tableNamePrefix}block
                SET block_status = '2' WHERE block_available = 0 AND  block_status=1 AND block_site_id=" . SITE_ID;
            $updateBlockStatus = mysqli_query($connection, $updateBlockStatusQuery);

            $dropTempTableQuery = "DROP TEMPORARY TABLE sales_qty_{$requestId}";
            $dropTempTable = mysqli_query($connection, $dropTempTableQuery);

            $updateSalesQuery = "UPDATE {$tableNamePrefix}sales SET sales_status=1 WHERE sales_id='{$requestId}' AND sales_status=0 AND sales_site_id=" . SITE_ID;
            $updateSales = mysqli_query($connection, $updateSalesQuery);

            if ($createTempTable && $updateBlockSlab && $updateBlock && $updateBlockStatus && $dropTempTable && $updateSales) {
                $status = 'success';
                $httpStatus = 201;
                $msg = 'Sales request is approved';
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