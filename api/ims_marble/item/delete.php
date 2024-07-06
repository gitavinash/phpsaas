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

mysqli_query($connection, "START TRANSACTION");

if ($checkData = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}block_item WHERE block_item_id={$blockId} AND block_item_status!=3 AND block_item_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkData) == 1) {

        extract(mysqli_fetch_assoc($checkData));

        if (isset($deleteData)) {
            if (isset($deteteType)) {

                // Updated JSON Object

                $damageAreaJSON = json_decode($block_item_damage_area, TRUE);

                if (isset($damageAreaJSON[$damageIndex])) {
                    array_splice($damageAreaJSON, $damageIndex, 1);
                }

                $updatedDamageAreaJSON = json_encode($damageAreaJSON, TRUE);

                $deleteDataFromDatabase = mysqli_query($connection, "UPDATE {$tableNamePrefix}block_item SET block_item_damage_area='{$updatedDamageAreaJSON}' WHERE block_item_id={$blockId} AND block_item_site_id=" . SITE_ID);

                if ($deleteDataFromDatabase) {
                    $msg = "Damage area removed successfully";
                    $httpStatus = 202;
                    $status = 'success';
                    mysqli_query($connection, "COMMIT");
                } else {
                    $status = 'error';
                    $msg = "Something went wrong while removing damage area";
                    mysqli_query($connection, "ROLLBACK");
                }
            } else {
                // Default data will detele

                $deleteDataFromDatabase = mysqli_query($connection, "UPDATE {$tableNamePrefix}block_item SET block_item_status=3 WHERE block_item_id={$blockId} AND block_item_site_id=" . SITE_ID);

                $deleteBlockItemDataFromDatabase = mysqli_query($connection, "UPDATE {$tableNamePrefix}block SET block_total_item=block_total_item-1 WHERE block_id={$block_item_parent_id} AND block_site_id=" . SITE_ID);

                if ($deleteDataFromDatabase && $deleteBlockItemDataFromDatabase) {
                    $msg = "Block item deleted successfully";
                    $httpStatus = 202;
                    $status = 'success';
                    mysqli_query($connection, "COMMIT");
                } else {
                    $status = 'error';
                    $msg = "Something went wrong while deleting block";
                    mysqli_query($connection, "ROLLBACK");
                }
            }
        }
    }
} else {
    $msg = 'Invalid block id';
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);