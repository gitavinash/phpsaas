<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid Request';

if (isset($id)) {
    $deleteData = true;
    $blockId = base64_decode($id);
} else {
    $msg = 'Block id is required';
}

if (isset($deleteData)) {
    mysqli_query($connection, "START TRANSACTION");
    if ($checkData = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}block WHERE block_id={$blockId} and block_status=1 AND block_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkData) == 1) {

            $deleteDataFromDatabase = mysqli_query($connection, "UPDATE {$tableNamePrefix}block SET block_status=2 WHERE block_id={$blockId} AND block_site_id=" . SITE_ID);

            $deleteBlockItemDataFromDatabase = mysqli_query($connection, "UPDATE {$tableNamePrefix}block_item SET block_item_status=2 WHERE block_item_parent_id={$blockId} AND block_item_site_id=" . SITE_ID);

            if ($deleteDataFromDatabase && $deleteBlockItemDataFromDatabase) {
                $msg = "Block deleted successfully";
                $httpStatus = 202;
                $status = 'success';
                mysqli_query($connection, "COMMIT");
            } else {
                $status = 'error';
                $msg = "Something went wrong while deleting block";
                mysqli_query($connection, "ROLLBACK");
            }
        } else {
            $msg = 'Invalid block id';
        }
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
