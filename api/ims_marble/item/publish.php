<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

if (isset($blockId)) {
    if ($blockId != '') {
        $validRequest = true;
        $blockItemId = base64_decode($blockId);
    } else {
        $msg = 'Block item id cannot be blank';
    }
} else {
    $msg = 'Block item id is missing';
}

if (isset($validRequest)) {

    if ($checkBlock = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}block WHERE block_id='{$blockId}' AND block_status=1 AND block_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkBlock) != 0) {
            $blockItemIndex = explode(",", $$blockItemIndex);
            $widthQuery = "";
            $heightQuery = "";
            for ($i = 0; $i < count($blockItemIndex); $i++) {
                // $widthQuery .= "WHEN block_item_id='{$blockItemIndex[$i]}' THEN ${width[$blockItemIndex[$i]]}";
            }
            echo $widthQuery;
            exit();
        } else {
            $msg = 'Invalid block request';
        }
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response')
);

echo json_encode($result, TRUE);
