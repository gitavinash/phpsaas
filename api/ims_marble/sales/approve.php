<?php

header('Content-Type: application/json');
$input = file_get_contents('php://input');
require_once LOGIN_CHECK;

$httpStatus = 405;
$validRequest = false;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($id != '') {
        $requestId = base64_decode($id);
        if ($agent != '') {
            $agentId = base64_decode($agent);
            $validRequest = true;
        } else {
            $msg = 'Agent parameter is missing';
        }
    } else {
        $msg = 'requestId parameter is missing';
    }
}

if ($validRequest) {

    mysqli_query($connection, "START TRANSACTION");

    if ($checkSales = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}sales WHERE sales_id='{$requestId}' AND sales_agent_id='{$agentId}' AND sales_status=0 AND sales_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkSales) != 0) {
            extract(mysqli_fetch_assoc($checkSales));
            $salesData = json_decode($sales_data, TRUE);
            $reqBlockInfo = array();

            // make new array object of block and slab id

            foreach ($salesData as $blockInfo => $blockSlabInfo) {
                $reqBlockId = explode('_', $blockInfo);
                $blockId = $reqBlockId[1];
                if (!isset($reqBlockInfo[$blockId])) {
                    $reqBlockInfo[$blockId] = array();
                }

                $reqBlockSlabInfo = explode(',', $blockSlabInfo);
                foreach ($reqBlockSlabInfo as $reqBlockSlabInfoExplode) {
                    $blockSlabInfoId = explode('_', $reqBlockSlabInfoExplode);
                    $reqBlockInfo[$blockId][] = $blockSlabInfoId[0];
                }
                $reqBlockInfo[$blockId] = implode(',', $reqBlockInfo[$blockId]);
            }
            foreach ($reqBlockInfo as $blockIds => $blockSlabIds) {
                $blockInfoQuery =
                    "SELECT 
                        CASE 
                            -- Centimeter conversions
                            WHEN block_item_dimension_type = 'cm' AND block_size_dimension = 'sq. mm.' THEN ROUND(SUM(block_item_width * block_item_height) * 100, 2)
                            WHEN block_item_dimension_type = 'cm' AND block_size_dimension = 'sq. in.' THEN ROUND(SUM(block_item_width * block_item_height) * 0.15500031, 2)
                            WHEN block_item_dimension_type = 'cm' AND block_size_dimension = 'sq. ft.' THEN ROUND(SUM(block_item_width * block_item_height) * 0.001076391, 2)
                            WHEN block_item_dimension_type = 'cm' AND block_size_dimension = 'sq. mt.' THEN ROUND(SUM(block_item_width * block_item_height) * 0.0001, 2)
                            
                            -- Millimeter conversions
                            WHEN block_item_dimension_type = 'mm' AND block_size_dimension = 'sq. cm.' THEN ROUND(SUM(block_item_width * block_item_height) * 0.01, 2)
                            WHEN block_item_dimension_type = 'mm' AND block_size_dimension = 'sq. in.' THEN ROUND(SUM(block_item_width * block_item_height) * 0.0015500031, 2)
                            WHEN block_item_dimension_type = 'mm' AND block_size_dimension = 'sq. ft.' THEN ROUND(SUM(block_item_width * block_item_height) * 0.00001076391, 2)
                            WHEN block_item_dimension_type = 'mm' AND block_size_dimension = 'sq. mt.' THEN ROUND(SUM(block_item_width * block_item_height) * 0.000001, 2)
                            
                            -- Inch conversions
                            WHEN block_item_dimension_type = 'in' AND block_size_dimension = 'sq. cm.' THEN ROUND(SUM(block_item_width * block_item_height) * 6.4516, 2)
                            WHEN block_item_dimension_type = 'in' AND block_size_dimension = 'sq. mm.' THEN ROUND(SUM(block_item_width * block_item_height) * 645.16, 2)
                            WHEN block_item_dimension_type = 'in' AND block_size_dimension = 'sq. ft.' THEN ROUND(SUM(block_item_width * block_item_height) * 0.00694444, 2)
                            WHEN block_item_dimension_type = 'in' AND block_size_dimension = 'sq. mt.' THEN ROUND(SUM(block_item_width * block_item_height) * 0.00064516, 2)

                            -- Foot conversions
                            WHEN block_item_dimension_type = 'ft' AND block_size_dimension = 'sq. cm.' THEN ROUND(SUM(block_item_width * block_item_height) * 929.0304, 2)
                            WHEN block_item_dimension_type = 'ft' AND block_size_dimension = 'sq. mm.' THEN ROUND(SUM(block_item_width * block_item_height) * 92903.04, 2)
                            WHEN block_item_dimension_type = 'ft' AND block_size_dimension = 'sq. in.' THEN ROUND(SUM(block_item_width * block_item_height) * 144, 2)
                            WHEN block_item_dimension_type = 'ft' AND block_size_dimension = 'sq. mt.' THEN ROUND(SUM(block_item_width * block_item_height) * 0.09290304, 2)

                            -- Meter conversions
                            WHEN block_item_dimension_type = 'mt' AND block_size_dimension = 'sq. cm.' THEN ROUND(SUM(block_item_width * block_item_height) * 10000, 2)
                            WHEN block_item_dimension_type = 'mt' AND block_size_dimension = 'sq. mm.' THEN ROUND(SUM(block_item_width * block_item_height) * 1000000, 2)
                            WHEN block_item_dimension_type = 'mt' AND block_size_dimension = 'sq. in.' THEN ROUND(SUM(block_item_width * block_item_height) * 1550.0031, 2)
                            WHEN block_item_dimension_type = 'mt' AND block_size_dimension = 'sq. ft.' THEN ROUND(SUM(block_item_width * block_item_height) * 10.7639, 2)

                            -- Default case if no conversion is needed
                            ELSE ROUND(SUM(block_item_width * block_item_height), 2)
                        END AS block_slab_total_size_converted,
                        ROUND(SUM(block_item_damage_size), 2) AS block_item_damage_size,
                        block_id,
                        block_size_dimension,
                        block_item_dimension_type,
                        COUNT(*) AS total_block_slabs
                    FROM 
                        isw_ims_marble_block_item 
                    INNER JOIN 
                        isw_ims_marble_block ON block_id = block_item_parent_id 
                    WHERE 
                        block_item_id IN ({$blockSlabIds}) 
                        AND block_item_status = 1
                        AND block_site_id = " . SITE_ID . "
                        AND block_item_site_id = " . SITE_ID;
                $blockInfo = mysqli_query($connection, $blockInfoQuery);
                if (mysqli_num_rows($blockInfo) != 0) {
                    while ($blockInfoData = mysqli_fetch_assoc($blockInfo)) {

                        // Update block 

                        $updateBlockSize = mysqli_query($connection, "UPDATE {$tableNamePrefix}block SET block_available=block_available-{$blockInfoData['total_block_slabs']}, block_gross_size=block_gross_size-{$blockInfoData['block_slab_total_size_converted']}, block_net_size=block_net_size-{$blockInfoData['block_slab_total_size_converted']}, block_damage_size=block_damage_size-{$blockInfoData['block_item_damage_size']} WHERE block_id={$blockInfoData['block_id']} AND  block_site_id=" . SITE_ID);

                        // Update block slab

                        $updateBlockSlab = mysqli_query($connection, "UPDATE {$tableNamePrefix}block_item SET block_item_status=3 WHERE block_item_parent_id={$blockInfoData['block_id']} AND block_item_id IN ({$blockSlabIds}) AND block_item_site_id=" . SITE_ID);
                    }
                }
            }
            $aerialAdjustment = (($aerialAdjustment == 'increase') ? '+' : '-');
            $salesData['aerials']['operation'] = $aerialAdjustment;
            $salesData['aerials']['value'] = $aerialValue;
            $salesData['aerials']['comment'] = $aerialComment;
            $salesData = json_encode($salesData);


            $updateSalesRequest = mysqli_query($connection, "UPDATE {$tableNamePrefix}sales SET sales_status=1, sales_aerial_op='{$aerialAdjustment}', sales_aerial_value='{$aerialValue}', sales_data='{$salesData}' WHERE sales_id='{$requestId}' AND sales_agent_id='{$agentId}' AND sales_status=0 AND sales_site_id=" . SITE_ID);

            if ($updateBlockSize && $updateBlockSlab && $updateSalesRequest) {
                $status = 'success';
                $httpStatus = 201;
                $msg = 'Sales request is approved successfully';
                mysqli_query($connection, "COMMIT");
            } else {
                $httpStatus = 500;
                $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something went wrong');
                mysqli_query($connection, "ROLLBACK");
            }
        } else {
            $status = 'warning';
            $httpStatus = 204;
            $msg = 'No record found';
        }
    } else {
        $httpStatus = 500;
        $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something went wrong while searching');
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid request method : ' . $_SERVER['REQUEST_METHOD']),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
