<?php

header('Content-Type: application/json');
$httpStatus = 405;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $warehouseFilter = ((isset($warehouseId)) ? "block_warehouse_id='{$warehouseId}' AND " : '');

    $blockItemQuery = "SELECT * FROM {$tableNamePrefix}block WHERE {$warehouseFilter} block_status=1 AND block_gross_size!=0 AND block_site_id=" . SITE_ID;

    if ($blocks = mysqli_query($connection, $blockItemQuery)) {
        if (mysqli_num_rows($blocks) != 0) {
            $blockList = array();
            while ($blocksData = mysqli_fetch_assoc($blocks)) {
                $blockList[] = $blocksData;
            }
            $httpStatus = 200;
            $status = 'success';
            $msg = "Data fetch successfully";
        } else {
            $httpStatus = 404;
            $msg = "No record found in this block id";
        }
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid request method -> ' . $_SERVER['REQUEST_METHOD']),
    'totalBlocks' => ((isset($blockList)) ? sizeof($blockList) : ''),
    'items' => ((isset($blockList)) ? $blockList : ''),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);