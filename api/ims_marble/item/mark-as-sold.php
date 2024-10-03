<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

if ($blockId != '') {
    $blockId = base64_decode($blockId);
} else {
    $msg = 'Block id is missing';
}

if (isset($validRequest)) {
    if ($checkBlock = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}block_item WHERE block__itemid='{$blockId}' AND block_item_status=1 AND block_item_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkBlock) != 0) {
            extract(mysqli_fetch_assoc($checkBlock));
            if (mysqli_query($connection, "UPDATE {$tableNamePrefix}block SET block_item_status=3 WHERE block_item_id='{$blockId}' AND block_item_site_id=" . SITE_ID)) {
                $httpStatus = 201;
                $status = 'success';
                $msg = 'Slab is now Mark as Sold successfully';
            } else {
                $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something went wrong while executing updated query');
            }
        } else {
            $msg = 'Block information not found';
        }
    } else {
        $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something went wrong while executing query');
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
    'id' => ((isset($productId)) ? $productId : 0),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
