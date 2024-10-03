<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

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
                if (mysqli_query($connection, "UPDATE {$tableNamePrefix}block SET block_status=3 WHERE block_id='{$blockId}' AND block_site_id=" . SITE_ID)) {
                    $httpStatus = 201;
                    $status = 'success';
                    $msg = 'Block is now Mark as Sold successfully';
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
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
