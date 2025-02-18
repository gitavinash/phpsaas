<?php

require_once LOGIN_CHECK;

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

if (empty($blockId)) {
    $msg = 'Block item id is missing or cannot be blank';
} elseif (!isset($_REQUEST['slabs']) || empty($_REQUEST['slabs'])) {
    $msg = 'Slabs is required for transfer';
} elseif (count($_REQUEST['slabs']) != 0) {
    if (empty($newWarehouseId)) {
        $msg = 'Warehouse id is missing or cannot be blank';
    } else {
        $newWarehouseId = explode('_', $newWarehouseId);
        $newWarehouseName = $newWarehouseId[1];
        $newWarehouseId = $newWarehouseId[0];
        $newWarehouseLocation = $newWarehouseId . '_' . $newWarehouseName;
        $validRequest = true;
        $blockItemId = base64_decode($blockId);
    }
} else {
    $msg = 'Slabs cannot be empty';
}

if (isset($validRequest)) {

    mysqli_query($connection, "START TRANSACTION");

    // Check block is valid

    if ($checkBlock = mysqli_query($connection, "SELECT *, (SELECT COUNT(*) FROM {$tableNamePrefix}block_item WHERE block_item_parent_id = block_id AND block_item_status=1 AND block_site_id='" . SITE_ID . "') as currentSlabsInBlock FROM {$tableNamePrefix}block INNER JOIN " . TABLE_PREFIX . "warehouse ON block_warehouse_id=warehouse_id WHERE block_id='{$blockId}' AND block_status=1 AND block_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkBlock) != 0) {
            extract(mysqli_fetch_assoc($checkBlock));

            $totalSlabsToTransfer = count($_REQUEST['slabs']);

            // Create new block code based on BlockCode_BlockWarehouseCode

            $newWarehouseBlockCode = $block_code . '_' . $warehouse_id;

            if ($totalSlabsToTransfer == $currentSlabsInBlock) {

                $requestData = json_encode(array(
                    'title' => 'Warehouse Transfer request',
                    'data' => array(
                        'block_title' => $block_title,
                        'block_code' => $block_code,
                        'current_location' => $warehouse_id . '_' . $warehouse_title,
                        'new_location' => $newWarehouseLocation,
                        'reason' => 'Block transfer',
                        'description' => 'Transfer whole block slab to ' . $newWarehouseName,
                        'old_status' =>  $block_status
                    )
                ), TRUE);

                $updateBlockSlabsStatus = true;
            } else {

                $blockItemSlabsId = implode(',', $_REQUEST['slabs']);
                $blockItemSlabsSeqId = implode(',', $_REQUEST['selectedSlabsSeqId']);

                $requestData = json_encode(array(
                    'title' => 'Warehouse Transfer request',
                    'data' => array(
                        'block_title' => $block_title,
                        'block_code' => $block_code,
                        'current_location' => $warehouse_id . '_' . $warehouse_title,
                        'new_location' => $newWarehouseLocation,
                        'reason' => 'Partial block transfer',
                        'description' => 'Transfer partial block slab to ' . $newWarehouseName,
                        'slabs_id_array' => $blockItemSlabsSeqId,
                        'hide_slabs_id_array' => $blockItemSlabsId,
                        'old_status' =>  $block_status
                    )
                ), TRUE);
            }

            // Add new request

            $addNewRequest = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "request(request_site_id, request_user_id, request_type, request_relation_id, request_title, request_data, request_status, request_time) VALUES(" . SITE_ID . ", " . USER_ID . ", 'block', '{$blockId}', 'Warehouse Transfer request', '{$requestData}', 0, current_timestamp())");

            $updateBlockStatus = mysqli_query($connection, "UPDATE {$tableNamePrefix}block SET block_status=5  WHERE block_id='{$blockId}' AND block_status=1 AND block_site_id=" . SITE_ID);

            $updateBlockSlabsStatus = mysqli_query($connection, "UPDATE {$tableNamePrefix}block_item SET block_item_status=5 WHERE block_item_id IN ($blockItemSlabsId) AND block_item_parent_id='{$blockId}' AND block_item_status=1 AND block_item_site_id=" . SITE_ID);

            if ($requestData && $addNewRequest && $updateBlockStatus && $updateBlockSlabsStatus) {
                $msg = 'Block transfer request sent successfully';
                $httpStatus = 200;
                $status = 'success';
                mysqli_query($connection, "COMMIT");
            } else {
                $msg = ((DEVELOPMENT_MODE == TRUE) ? "Full block commit: " . mysqli_error($connection) : 'Something went wrong while commiting query');
                $status = 'error';
            }
        } else {
            $msg = 'Invalid block information received';
        }
    } else {
        $msg = ((DEVELOPMENT_MODE == TRUE) ? "Fetching block info: " . mysqli_error($connection) : 'Something went wrong while executing query');
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response')
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
