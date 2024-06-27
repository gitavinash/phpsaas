<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

if (isset($id)) {
    if ($id != '') {
        if (isset($damageArea)) {
            if ($damageArea != '') {
                $validRequest = true;
                $blockItemId = base64_decode($id);
            } else {
                $msg = 'Damage area value cannot be blank';
            }
        } else {
            $msg = 'Damage area is missing';
        }
    } else {
        $msg = 'Block item id cannot be blank';
    }
} else {
    $msg = 'Block item id is missing';
}

if (isset($validRequest)) {

    // Check block code

    $checkBlockItem = mysqli_query($connection, "SELECT block_item_parent_id FROM {$tableNamePrefix}block_item WHERE block_item_id='{$blockItemId}' AND block_item_status!=3 AND block_item_site_id=" . SITE_ID);
    if (mysqli_num_rows($checkBlockItem) != 0) {

        if (mysqli_query($connection, "UPDATE {$tableNamePrefix}block_item SET block_item_damage_area='{$damageArea}', block_item_status=1 WHERE block_item_id='{$blockItemId}' AND block_item_status!=3 AND block_item_site_id=" . SITE_ID)) {
            $httpStatus = 204;
            $status = 'success';
            $msg = 'Damage area update successfully.';
        } else {
            $status = 'error';
            $msg = 'Something went wrong while updating damage area.';
        }
    } else {
        $httpStatus = 404;
        $msg = 'Block item record is not exist';
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response')
);

echo json_encode($result, TRUE);
