<?php

header('Content-Type: application/json');
$httpStatus = 405;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($blockId)) {
        if ($blocks = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}block_item WHERE block_item_parent_id={$blockId} AND block_item_status=1 AND block_item_site_id=" . SITE_ID)) {
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
    } else {
        $httpStatus = 422;
        $msg = "Block id paramater is missing";
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid request method -> ' . $_SERVER['REQUEST_METHOD']),
    'items' => ((isset($blockList)) ? $blockList : ''),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);