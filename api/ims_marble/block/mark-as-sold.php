<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    mysqli_query($connection, "START TRANSACTION");

    if ($blockId != '') {
        $blockId = base64_decode($blockId);
        $validRequest = true;
    } else {
        $msg = 'Block id is missing';
    }

    if (isset($validRequest)) {
        if ($checkBlock = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}block WHERE block_id='{$blockId}' AND block_status=1 AND block_site_id=" . SITE_ID)) {
            if (mysqli_num_rows($checkBlock) != 0) {
                extract(mysqli_fetch_assoc($checkBlock));
                $markBlockAsSold = mysqli_query($connection, "UPDATE {$tableNamePrefix}block SET block_status=3, block_available=0, block_gross_size=0, block_net_size=0, block_damage_size=0 WHERE block_id='{$blockId}' AND block_site_id=" . SITE_ID);
                $markBlockSlabAsSold = mysqli_query($connection, "UPDATE {$tableNamePrefix}block_item SET block_item_status=3 WHERE block_item_parent_id='{$blockId}' AND block_item_site_id=" . SITE_ID);

                if ($markBlockAsSold && $markBlockSlabAsSold) {
                    mysqli_query($connection, "COMMIT");
                    $httpStatus = 201;
                    $status = 'success';
                    $msg = 'Block is now Mark as Sold successfully';
                } else {
                    mysqli_query($connection, "ROLLBACK");
                    $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something went wrong while executing updated query');
                }
            } else {
                $msg = 'Block information not found';
            }
        } else {
            $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something went wrong while executing query');
        }
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);