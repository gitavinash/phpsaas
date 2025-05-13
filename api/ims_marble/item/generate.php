<?php
header('Content-Type: application/json');
$input = file_get_contents('php://input');

require_once LOGIN_CHECK;

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

$requestBody = json_decode($input, TRUE);
extract($requestBody);

if (isset($blockId)) {
    if ($blockId != '') {
        $validRequest = true;
        $blockId = base64_decode($blockId);
    } else {
        $msg = 'Block item id cannot be blank';
    }
} else {
    $msg = 'Block item id is missing';
}

if (isset($validRequest)) {

    mysqli_query($connection, "START TRANSACTION");

    $checkBlockQuery = "SELECT 
    block_product_type,
    block_total_item,
    COUNT(block_item_id) AS total_existing_slabs,
    block_size_dimension 
FROM 
    {$tableNamePrefix}block 
LEFT JOIN 
    {$tableNamePrefix}block_item 
    ON block_item_parent_id = block_id 
    AND block_item_status IN (1, 3) 
    AND block_item_parent_id = {$blockId}
WHERE 
    block_id = '{$blockId}'
    AND block_status = 1
    AND block_site_id = " . SITE_ID . "
GROUP BY 
    block_product_type,
    block_total_item,
    block_size_dimension;";

    if ($checkBlock = mysqli_query($connection, $checkBlockQuery)) {
        if (mysqli_num_rows($checkBlock) != 0) {
            extract(mysqli_fetch_assoc($checkBlock));

            $measurementData = constant(strtoupper('measurement_' . $block_product_type));
            extract(json_decode($measurementData, TRUE));

            $widthQuery = '';
            $heightQuery = '';
            $singleItemArea = array();
            $blockTotalSlabs = 0;
            $slabValues = "";

            for ($i = 0; $i < sizeof($slabsData); $i++) {

                for ($j = 0; $j < $slabsData[$i]['totalSlab']; $j++) {
                    $slabValues = $slabValues . "({$blockId}, " . SITE_ID . ", " . USER_ID . ", '{$measurementType}', '{$slabsData[$i]['slabLenght']}', '{$slabsData[$i]['slabHeight']}', 1, now()),";
                    $singleItemArea[] = $slabsData[$i]['slabLenght'] * $slabsData[$i]['slabHeight'];
                    $singleItemWeight[] = $slabsData[$i]['slabLenght'];
                }

                $blockTotalSlabs = $blockTotalSlabs + $slabsData[$i]['totalSlab'];
            }

            if ($total_existing_slabs < $block_total_item) {
                if ($blockTotalSlabs <= ($block_total_item - $total_existing_slabs)) {

                    $updateItemQuery = "INSERT INTO {$tableNamePrefix}block_item(block_item_parent_id, block_item_site_id, block_item_user_id, block_item_dimension_type, block_item_width, block_item_height, block_item_status, block_item_created) VALUES" . rtrim($slabValues, ', ');
                    $totalArea = array_sum($singleItemArea);

                    $updateAvailableSlabs = mysqli_query($connection, "UPDATE {$tableNamePrefix}block SET block_available=block_available+{$blockTotalSlabs} WHERE block_id='{$blockId}' AND block_site_id=" . SITE_ID);

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


                    if ($block_size_dimension == 'mt (t)') {
                        $totalArea = array_sum($singleItemWeight);
                    } else {
                        $totalArea = number_format((float)$totalArea, 2, '.', '');
                    }

                    $updateBlockQuery = "UPDATE {$tableNamePrefix}block SET block_gross_size=block_gross_size+'{$totalArea}', block_net_size=block_net_size+'{$totalArea}' WHERE block_id='{$blockId}' AND block_site_id=" . SITE_ID;
                    $updateBlock = mysqli_query($connection, $updateBlockQuery);
                    $createSlabs = mysqli_query($connection, $updateItemQuery);

                    if ($updateBlock && $createSlabs && $updateAvailableSlabs) {
                        $httpStatus = 204;
                        $status = 'success';
                        $msg = 'New slabs generate successfully';
                        mysqli_query($connection, "COMMIT");
                    } else {
                        $status = 'error';
                        $msg = 'Something went wrong while trying to update block item';
                        mysqli_query($connection, "ROLLBACK");
                    }
                } else {
                    $msg = "The block is already full. You can't add more than {$totalSlabs} slabs.";
                }
            } else {
                $msg = "You cannot add more than {$totalSlabs} slabs to the existing block.";
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