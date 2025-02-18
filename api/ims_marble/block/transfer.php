<?php

require_once LOGIN_CHECK;

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

if ($reason != '') {
    if ($newWareHouseId != '') {
        if ($currentWarehouse != '') {
            if ($blockId != '') {
                $validRequest = true;
            } else {
                $msg = 'Block id is missing';
            }
        } else {
            $msg = 'Current warehouse is missing';
        }
    } else {
        $msg = 'Select new warehouse location';
    }
} else {
    $msg = 'Please provide reason for transfer';
}

if (isset($validRequest)) {
    if ($checkBlock = mysqli_query($connection, "SELECT block_title, block_code, block_size_dimension, block_product_type, block_gross_size, block_damage_size, block_net_size, COUNT(block_item_id) as totalExistingSlab, block_status FROM {$tableNamePrefix}block INNER JOIN {$tableNamePrefix}block_item ON block_item_parent_id=block_id WHERE block_id='{$blockId}' AND block_status=1 AND block_item_status=1 AND block_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkBlock) != 0) {
            extract(mysqli_fetch_assoc($checkBlock));

            mysqli_query($connection, "START TRANSACTION");

            $updateBlock = mysqli_query($connection, "UPDATE {$tableNamePrefix}block SET block_status=5  WHERE block_id='{$blockId}' AND block_status=1 AND block_site_id=" . SITE_ID);

            $requestData = json_encode(array(
                'title' => 'Warehouse Transfer request',
                'data' => array(
                    'block_title' => $block_title,
                    'block_code' => $block_code,
                    'current_location' => $currentWarehouse,
                    'new_location' => $newWareHouseId,
                    'reason' => $reason,
                    'description' => $description,
                    'old_status' =>  $block_status
                )
            ), TRUE);

            // Add new request

            $addNewRequest = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "request(request_site_id, request_user_id, request_type, request_relation_id, request_title, request_data, request_status, request_time) VALUES(" . SITE_ID . ", " . USER_ID . ", 'block', '{$blockId}', 'Warehouse Transfer request', '{$requestData}', 0, current_timestamp())");

            if ($updateBlock && $addNewRequest) {
                $httpStatus = 201;
                $status = 'success';
                $msg = 'Block transfer request sent successfully';
                $productId = $blockId;
                mysqli_query($connection, 'COMMIT');
            } else {
                $status = 'error';
                $msg = ((DEVELOPMENT_MODE == true) ? mysqli_error($connection) : 'Something went wrong while trying to update block');
                mysqli_query($connection, 'ROLLBACK');
            }
        } else {
            $msg = 'Invalid block request received';
        }
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
    'id' => ((isset($productId)) ? $productId : 0),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
