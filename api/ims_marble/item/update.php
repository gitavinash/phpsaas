<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

if (isset($id)) {
    if ($id != '') {
        if (isset($damageArea)) {
            if ($damageArea != '') {
                if ($damageAreaSize != '') {
                    if ($damageAreaSizeType != '') {
                        if ($totatDamageCount != '') {
                            $validRequest = true;
                            $blockItemId = base64_decode($id);
                        } else {
                            $msg = 'Total Damage count parameter is missing';
                        }
                    } else {
                        $msg = 'Damage area size type parameter is missing';
                    }
                } else {
                    $msg = 'Damage area size parameter is missing';
                }
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

    mysqli_query($connection, "START TRANSACTION");

    // Check block code

    $checkBlockItem = mysqli_query($connection, "SELECT block_item_parent_id, block_gross_size, block_size_dimension FROM {$tableNamePrefix}block_item INNER JOIN {$tableNamePrefix}block ON block_item_parent_id=block_id WHERE block_item_id='{$blockItemId}' AND block_item_status!=3 AND block_item_site_id=" . SITE_ID);
    if (mysqli_num_rows($checkBlockItem) != 0) {

        extract(mysqli_fetch_assoc($checkBlockItem));

        if ($block_size_dimension != 'ft') {
            switch ($block_size_dimension) {
                case "mm":
                    $damageAreaSize = $damageAreaSize * 0.00328084;
                    break;
                case "cm":
                    $damageAreaSize = $damageAreaSize * 0.0328084;
                    break;
                case "in":
                    $damageAreaSize = $damageAreaSize * 0.0833333;
                    break;
            }
            $netSize = $block_gross_size - $damageAreaSize;
        } else {
            $netSize = $block_gross_size - $damageAreaSize;
        }

        $netSize = number_format((float)$netSize, 2, '.', '');
        $damageAreaSize = number_format((float)$damageAreaSize, 2, '.', '');

        $updateBlockItem = mysqli_query($connection, "UPDATE {$tableNamePrefix}block_item SET block_item_damage_area='{$damageArea}', block_item_status=1, block_item_damage_count={$totatDamageCount} WHERE block_item_id='{$blockItemId}' AND block_item_status!=3 AND block_item_site_id=" . SITE_ID);

        $updateBlock = mysqli_query($connection, "UPDATE {$tableNamePrefix}block SET block_net_size='{$netSize}', block_damage_size='{$damageAreaSize}' WHERE block_id='{$block_item_parent_id}' AND block_site_id=" . SITE_ID);

        if ($updateBlockItem && $updateBlock) {
            mysqli_query($connection, "COMMIT");
            $httpStatus = 204;
            $status = 'success';
            $msg = 'Damage area update successfully.';
        } else {
            $status = 'error';
            $msg = 'Something went wrong while updating damage area.';
            mysqli_query($connection, "ROLLBACK");
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