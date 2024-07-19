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

    $checkBlockItem = mysqli_query($connection, "SELECT block_item_parent_id, block_gross_size, block_size_dimension,block_product_type, block_item_damage_area FROM {$tableNamePrefix}block_item INNER JOIN {$tableNamePrefix}block ON block_item_parent_id=block_id WHERE block_item_id='{$blockItemId}' AND block_item_status!=3 AND block_item_site_id=" . SITE_ID);
    if (mysqli_num_rows($checkBlockItem) != 0) {

        extract(mysqli_fetch_assoc($checkBlockItem));

        $measurementData = constant(strtoupper('measurement_' . $block_product_type));
        extract(json_decode($measurementData, TRUE));

        switch ([$measurementType, $calculationType]) {

                // Centimeter

            case ['cm', 'sq. mm.']:
                $damageAreaSize = $damageAreaSize * 100;
                break;
            case ['cm', 'sq. in.']:
                $damageAreaSize = $damageAreaSize * 0.15500031;
                break;
            case ['cm', 'sq. cm.']:
                $damageAreaSize = $damageAreaSize * 30.48;
                break;
            case ['cm', 'sq. mt.']:
                $damageAreaSize = $damageAreaSize * 0.0001;
                break;

                // Millimeter

            case ['mm', 'sq. cm.']:
                $damageAreaSize = $damageAreaSize * 0.01;
                break;
            case ['mm', 'sq. in.']:
                $damageAreaSize = $damageAreaSize * 0.0015500031;
                break;
            case ['mm', 'sq. mm.']:
                $damageAreaSize = $damageAreaSize * 304.8;
                break;
            case ['mm', 'sq. mt.']:
                $damageAreaSize = $damageAreaSize * 0.000001;
                break;

                // Inches

            case ['in', 'sq. cm.']:
                $damageAreaSize = $damageAreaSize * 6.4516;
                break;
            case ['in', 'sq. mm.']:
                $damageAreaSize = $damageAreaSize * 645.16;
                break;
            case ['in', 'sq. in.']:
                $damageAreaSize = $damageAreaSize * 12;
                break;
            case ['in', 'sq. mt.']:
                $damageAreaSize = $damageAreaSize * 0.00064516;
                break;

                // Feet

            case ['ft', 'sq. cm.']:
                $damageAreaSize = $damageAreaSize * 30.48;
                break;
            case ['ft', 'sq. mm.']:
                $damageAreaSize = $damageAreaSize * 304.8;
                break;
            case ['ft', 'sq. in.']:
                $damageAreaSize = $damageAreaSize * 12;
                break;
            case ['ft', 'sq. mt.']:
                $damageAreaSize = $damageAreaSize * 30.48;
                break;

                // Meter

            case ['mt', 'sq. cm.']:
                $damageAreaSize = $damageAreaSize * 10000;
                break;
            case ['mt', 'sq. mm.']:
                $damageAreaSize = $damageAreaSize * 1000000;
                break;
            case ['mt', 'sq. in.']:
                $damageAreaSize = $damageAreaSize * 1550.0031;
                break;
            case ['mt', 'sq. mt.']:
                $damageAreaSize = $damageAreaSize * 30.48;
                break;
        }

        $damageAreaSize = number_format((float)$damageAreaSize, 2, '.', '');

        $updateBlockItem = mysqli_query($connection, "UPDATE {$tableNamePrefix}block_item SET block_item_damage_area='{$damageArea}', block_item_status=1, block_item_damage_count={$totatDamageCount} WHERE block_item_id='{$blockItemId}' AND block_item_status!=3 AND block_item_site_id=" . SITE_ID);

        if ($sameJson == 'false') {
            $updateBlock = mysqli_query($connection, "UPDATE {$tableNamePrefix}block SET block_net_size=block_net_size-{$damageAreaSize}, block_damage_size=block_damage_size+{$damageAreaSize} WHERE block_id='{$block_item_parent_id}' AND block_site_id=" . SITE_ID);
        } else {
            $updateBlock = true;
        }

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