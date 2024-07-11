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

if ($checkData = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}block_item INNER JOIN {$tableNamePrefix}block ON block_item_parent_id=block_id WHERE block_item_id={$blockId} AND block_item_status!=3 AND block_item_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkData) == 1) {

        extract(mysqli_fetch_assoc($checkData));

        if (isset($deleteData)) {
            if (isset($deteteType)) {

                // Updated JSON Object

                $damageAreaJSON = json_decode($block_item_damage_area, TRUE);

                switch ($damageAreaJSON[$damageIndex]['damageAreaWidthThicknessDimension']) {
                    case "mm":
                        $widthDamageAreaSize = $damageAreaJSON[$damageIndex]['damageAreaWidth'] * 0.00328084;
                        break;
                    case "cm":
                        $widthDamageAreaSize = $damageAreaJSON[$damageIndex]['damageAreaWidth'] * 0.0328084;
                        break;
                    case "in":
                        $widthDamageAreaSize = $damageAreaJSON[$damageIndex]['damageAreaWidth'] * 0.0833333;
                        break;
                    default:
                        $widthDamageAreaSize = $damageAreaJSON[$damageIndex]['damageAreaWidth'];
                        break;
                }
                switch ($damageAreaJSON[$damageIndex]['damageAreaHeightThicknessDimension']) {
                    case "mm":
                        $heightDamageAreaSize = $damageAreaJSON[$damageIndex]['damageAreaHeight'] * 0.00328084;
                        break;
                    case "cm":
                        $heightDamageAreaSize = $damageAreaJSON[$damageIndex]['damageAreaHeight'] * 0.0328084;
                        break;
                    case "in":
                        $heightDamageAreaSize = $damageAreaJSON[$damageIndex]['damageAreaHeight'] * 0.0833333;
                        break;
                    default:
                        $widthDamageAreaSize = $damageAreaJSON[$damageIndex]['damageAreaHeight'];
                        break;
                }

                $recoverDamageArea = $widthDamageAreaSize * $heightDamageAreaSize;

                if ($block_size_dimension != 'ft') {
                    switch ($block_size_dimension) {
                        case "mm":
                            $recoverDamageArea = $recoverDamageArea * 0.00328084;
                            break;
                        case "cm":
                            $recoverDamageArea = $recoverDamageArea * 0.0328084;
                            break;
                        case "in":
                            $recoverDamageArea = $recoverDamageArea * 0.0833333;
                            break;
                    }
                }

                $recoverDamageArea = number_format((float)$recoverDamageArea, 2, '.', '');

                if (isset($damageAreaJSON[$damageIndex])) {
                    array_splice($damageAreaJSON, $damageIndex, 1);
                }

                $updatedDamageAreaJSON = json_encode($damageAreaJSON, TRUE);

                $deleteDataFromDatabase = mysqli_query($connection, "UPDATE {$tableNamePrefix}block_item SET block_item_damage_area='{$updatedDamageAreaJSON}' WHERE block_item_id={$blockId} AND block_item_site_id=" . SITE_ID);

                $updateBlock = mysqli_query($connection, "UPDATE {$tableNamePrefix}block SET block_net_size=block_net_size+$recoverDamageArea, block_damage_size=block_damage_size-$recoverDamageArea WHERE block_id={$block_item_parent_id} AND block_site_id=" . SITE_ID);

                if ($deleteDataFromDatabase && $updateBlock) {
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

                $totalAreaOfItem = $block_item_width * $block_item_height;
                $totalAreaOfItem = number_format((float)$totalAreaOfItem, 2, '.', '');

                $deleteDataFromDatabase = mysqli_query($connection, "UPDATE {$tableNamePrefix}block_item SET block_item_status=3 WHERE block_item_id={$blockId} AND block_item_site_id=" . SITE_ID);

                $deleteBlockItemDataFromDatabase = mysqli_query($connection, "UPDATE {$tableNamePrefix}block SET block_total_item=block_total_item-1, block_net_size=block_net_size-$totalAreaOfItem, block_gross_size=block_gross_size-$totalAreaOfItem WHERE block_id={$block_item_parent_id} AND block_site_id=" . SITE_ID);

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