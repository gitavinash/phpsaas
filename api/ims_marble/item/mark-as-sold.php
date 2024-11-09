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

        mysqli_query($connection, "START TRANSACTION");

        if ($checkBlock = mysqli_query($connection, "SELECT block_item_width, block_item_height, block_item_parent_id, block_item_damage_size, block_product_type FROM {$tableNamePrefix}block_item INNER JOIN {$tableNamePrefix}block ON block_id=block_item_parent_id WHERE block_item_id='{$blockId}' AND block_status=1 AND block_item_status=1 AND block_item_site_id=" . SITE_ID)) {
            if (mysqli_num_rows($checkBlock) != 0) {
                extract(mysqli_fetch_assoc($checkBlock));
                $updateBlockItem = mysqli_query($connection, "UPDATE {$tableNamePrefix}block_item SET block_item_status=3 WHERE block_item_id='{$blockId}' AND block_item_site_id=" . SITE_ID);

                // Calculate block slab size

                $totalArea = $block_item_width * $block_item_height;

                $measurementData = constant(strtoupper('measurement_' . $block_product_type));
                extract(json_decode($measurementData, TRUE));

                switch ([$measurementType, $calculationType]) {

                        // Centimeter

                    case ['cm', 'sq. mm.']:
                        $totalArea = $totalArea * 100;
                        break;
                    case ['cm', 'sq. in.']:
                        $totalArea = $totalArea * 0.15500031;
                        break;
                    case ['cm', 'sq. ft.']:
                        $totalArea = $totalArea * 0.001076391;
                        break;
                    case ['cm', 'sq. mt.']:
                        $totalArea = $totalArea * 0.0001;
                        break;

                        // Millimeter

                    case ['mm', 'sq. cm.']:
                        $totalArea = $totalArea * 0.01;
                        break;
                    case ['mm', 'sq. in.']:
                        $totalArea = $totalArea * 0.0015500031;
                        break;
                    case ['mm', 'sq. ft.']:
                        $totalArea = $totalArea * 0.00001076391;
                        break;
                    case ['mm', 'sq. mt.']:
                        $totalArea = $totalArea * 0.000001;
                        break;

                        // Inches

                    case ['in', 'sq. cm.']:
                        $totalArea = $totalArea * 6.4516;
                        break;
                    case ['in', 'sq. mm.']:
                        $totalArea = $totalArea * 645.16;
                        break;
                    case ['in', 'sq. ft.']:
                        $totalArea = $totalArea * 0.00694444;
                        break;
                    case ['in', 'sq. mt.']:
                        $totalArea = $totalArea * 0.00064516;
                        break;

                        // Feet

                    case ['ft', 'sq. cm.']:
                        $totalArea = $totalArea * 929.0304;
                        break;
                    case ['ft', 'sq. mm.']:
                        $totalArea = $totalArea * 92903.04;
                        break;
                    case ['ft', 'sq. in.']:
                        $totalArea = $totalArea * 144;
                        break;
                    case ['ft', 'sq. mt.']:
                        $totalArea = $totalArea * 0.09290304;
                        break;

                        // Meter

                    case ['mt', 'sq. cm.']:
                        $totalArea = $totalArea * 10000;
                        break;
                    case ['mt', 'sq. mm.']:
                        $totalArea = $totalArea * 1000000;
                        break;
                    case ['mt', 'sq. in.']:
                        $totalArea = $totalArea * 1550.0031;
                        break;
                    case ['mt', 'sq. ft.']:
                        $totalArea = $totalArea * 10.7639;
                        break;
                }

                $totalArea = number_format((float)$totalArea, 5, '.', '');

                $updateBlock = mysqli_query($connection, "UPDATE {$tableNamePrefix}block SET 
                block_available=block_available-1, block_damage_size=block_damage_size-{$block_item_damage_size},  block_gross_size=block_gross_size-{$totalArea}, block_net_size=block_net_size-{$totalArea} WHERE block_id='{$block_item_parent_id}' AND block_status=1 AND block_site_id=" . SITE_ID);

                if ($checkTotalRow = mysqli_query($connection, "SELECT count(block_item_id) as totalSlabsInBlock FROM {$tableNamePrefix}block_item WHERE block_item_parent_id={$block_item_parent_id} AND block_item_status=1 AND block_item_site_id=" . SITE_ID)) {
                    if (mysqli_num_rows($checkTotalRow) != 0) {
                        extract(mysqli_fetch_assoc($checkTotalRow));
                        if ($totalSlabsInBlock == 0) {
                            $updateBlockStatusToSoldOut = mysqli_query($connection, "UPDATE {$tableNamePrefix}block SET block_status=3 WHERE block_id='{$block_item_parent_id}' AND block_status=1 AND block_site_id=" . SITE_ID);
                        } else {
                            $updateBlockStatusToSoldOut = true;
                        }
                    }
                }

                if ($checkBlock && $updateBlockItem && $updateBlock && $updateBlockStatusToSoldOut) {
                    $httpStatus = 201;
                    $status = 'success';
                    $msg = 'Slab is now Mark as Sold successfully';
                    mysqli_query($connection, "COMMIT");
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
