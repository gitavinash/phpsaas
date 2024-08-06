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

    mysqli_query($connection, "START TRANSACTION");

    if ($checkBlock = mysqli_query($connection, "SELECT block_product_type, block_gross_size, block_damage_size FROM {$tableNamePrefix}block WHERE block_id='{$blockId}' AND block_status=1 AND block_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkBlock) != 0) {
            extract(mysqli_fetch_assoc($checkBlock));
            $blockItemIndex = explode(",", $blockItemIndex);

            $widthQuery = '';
            $heightQuery = '';
            $singleItemArea = array();

            foreach ($blockItemIndex as $index) {
                if (isset($_POST["width"][$index]) && isset($_POST["height"][$index])) {
                    $width[$index] = $_POST["width"][$index];
                    $height[$index] = $_POST["height"][$index];
                    $singleItemArea[] = $_POST["width"][$index] * $_POST["height"][$index];
                } else {
                    echo "Missing width or height for index $index";
                }
            }

            for ($i = 0; $i < count($blockItemIndex); $i++) {
                $index = $blockItemIndex[$i];
                $widthQuery .= "WHEN block_item_id='{$blockItemIndex[$i]}' THEN " . $width[$index] . ' ';
                $heightQuery .= "WHEN block_item_id='{$blockItemIndex[$i]}' THEN " . $height[$index] . ' ';
            }

            $totalArea = array_sum($singleItemArea);

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

            $totalArea = number_format((float)$totalArea, 2, '.', '');
            $block_net_size = $totalArea - $block_damage_size;

            $updateBlockQuery = "UPDATE {$tableNamePrefix}block SET block_gross_size='{$totalArea}' WHERE block_id='{$blockId}'AND block_site_id=" . SITE_ID;

            $updateBlockNetSizeQuery = "UPDATE {$tableNamePrefix}block SET block_net_size='{$block_net_size}' WHERE block_id='{$blockId}'AND block_site_id=" . SITE_ID;

            $updateItemQuery = "
                UPDATE {$tableNamePrefix}block_item
                SET
                    block_item_status=1,
                    block_item_width = CASE
                        {$widthQuery} 
                        ELSE block_item_width
                    END,
                    block_item_height = CASE
                        {$heightQuery} 
                        ELSE block_item_width
                    END
                WHERE block_item_parent_id={$blockId} AND block_item_status!=3 AND block_item_site_id=" . SITE_ID;

            $updateBlock = mysqli_query($connection, $updateBlockQuery);
            $updateBlockNetSize = mysqli_query($connection, $updateBlockNetSizeQuery);
            $updateItem = mysqli_query($connection, $updateItemQuery);

            if ($updateBlock && $updateBlockNetSize && $updateItem) {
                $httpStatus = 204;
                $status = 'success';
                $msg = 'Block item update successfully.';
                mysqli_query($connection, "COMMIT");
            } else {
                $status = 'error';
                $msg = 'Something went wrong while trying to update block item';
                mysqli_query($connection, "ROLLBACK");
            }
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