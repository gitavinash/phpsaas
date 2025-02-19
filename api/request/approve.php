<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid Request';

if (isset($id)) {
    $deleteData = true;
    $requestId = base64_decode($id);
} else {
    $msg = 'Request id is required';
}

if (isset($deleteData)) {
    mysqli_query($connection, "START TRANSACTION");
    if ($checkData = mysqli_query($connection, "SELECT request_data, request_type, request_relation_id FROM " . TABLE_PREFIX . "request WHERE request_id={$requestId} AND request_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkData) == 1) {

            extract(mysqli_fetch_assoc($checkData));

            $requestData = json_decode($request_data, TRUE);

            $oldBlockStatus = $requestData['data']['old_status'];
            $newWarehouse = explode('_', $requestData['data']['new_location']);
            $oldWarehouse = $requestData['data']['current_location'];

            // Delete request

            $updateRequest = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "request SET request_status=1 WHERE request_id='{$requestId}' AND request_site_id=" . SITE_ID);

            // Update block status to old status

            $updateBlockStatus = mysqli_query($connection, "UPDATE {$tableNamePrefix}block SET block_status={$oldBlockStatus}, block_warehouse_id={$newWarehouse[0]} WHERE block_id='{$request_relation_id}' AND block_site_id=" . SITE_ID);


            $updateOldBlock = TRUE;
            $createNewBlock = TRUE;
            $updateOldSlabsBlock = TRUE;

            if (isset($requestData['data']['hide_slabs_id_array'])) {

                if ($getBlockSlabsSize = mysqli_query(
                    $connection,
                    "SELECT 
    ROUND(SUM(
        CASE 
            WHEN block_size_dimension = 'mt (t)' THEN block_item_width 
            ELSE block_item_width * block_item_height 
        END
    ), 2) AS block_item_gross_size, 

    ROUND(SUM(
        CASE 
            WHEN block_size_dimension = 'mt (t)' THEN block_item_width 
            ELSE (block_item_width * block_item_height) - COALESCE(block_item_damage_size, 0) 
        END
    ), 2) AS block_item_net_size, 

    ROUND(SUM(block_item_damage_size), 2) AS block_item_damage_size,
    block_product_type, block_size_dimension
FROM 
    isw_ims_marble_block_item 
INNER JOIN isw_ims_marble_block 
    ON block_id = block_item_parent_id 
WHERE 
    block_item_id IN ({$requestData['data']['hide_slabs_id_array']}) 
    AND block_code = {$requestData['data']['block_code']} 
    AND block_item_site_id = " . SITE_ID
                )) {
                    extract(mysqli_fetch_assoc($getBlockSlabsSize));

                    $measurementData = constant(strtoupper('measurement_' . $block_product_type));
                    extract(json_decode($measurementData, TRUE));

                    switch ([$measurementType, $calculationType]) {

                            // Centimeter

                        case ['cm', 'sq. mm.']:
                            $block_item_gross_size = $block_item_gross_size * 100;
                            $block_item_net_size = $block_item_net_size * 100;
                            $block_item_damage_size = $block_item_damage_size * 100;
                            break;
                        case ['cm', 'sq. in.']:
                            $block_item_gross_size = $block_item_gross_size * 0.15500031;
                            $block_item_net_size = $block_item_net_size * 0.15500031;
                            $block_item_damage_size = $block_item_damage_size * 0.15500031;
                            break;
                        case ['cm', 'sq. ft.']:

                            $block_item_gross_size = $block_item_gross_size * 0.001076391;
                            $block_item_net_size = $block_item_net_size * 0.001076391;
                            $block_item_damage_size = $block_item_damage_size * 0.001076391;
                            break;
                        case ['cm', 'sq. mt.']:
                            $block_item_gross_size = $block_item_gross_size * 0.0001;
                            $block_item_net_size = $block_item_net_size * 0.0001;
                            $block_item_damage_size = $block_item_damage_size * 0.0001;
                            break;

                            // Millimeter

                        case ['mm', 'sq. cm.']:
                            $block_item_gross_size = $block_item_gross_size * 0.01;
                            $block_item_net_size = $block_item_net_size * 0.01;
                            $block_item_damage_size = $block_item_damage_size * 0.01;
                            break;
                        case ['mm', 'sq. in.']:
                            $block_item_gross_size = $block_item_gross_size * 0.0015500031;
                            $block_item_net_size = $block_item_net_size * 0.0015500031;
                            $block_item_damage_size = $block_item_damage_size * 0.0015500031;
                            break;
                        case ['mm', 'sq. ft.']:
                            $block_item_gross_size = $block_item_gross_size * 0.00001076391;
                            $block_item_net_size = $block_item_net_size * 0.00001076391;
                            $block_item_damage_size = $block_item_damage_size * 0.00001076391;
                            break;
                        case ['mm', 'sq. mt.']:
                            $block_item_gross_size = $block_item_gross_size * 0.000001;
                            $block_item_net_size = $block_item_net_size * 0.000001;
                            $block_item_damage_size = $block_item_damage_size * 0.000001;
                            break;

                            // Inches

                        case ['in', 'sq. cm.']:
                            $block_item_gross_size = $block_item_gross_size * 6.4516;
                            $block_item_net_size = $block_item_net_size * 6.4516;
                            $block_item_damage_size = $block_item_damage_size * 6.4516;
                            break;
                        case ['in', 'sq. mm.']:
                            $block_item_gross_size = $block_item_gross_size * 645.16;
                            $block_item_net_size = $block_item_net_size * 645.16;
                            $block_item_damage_size = $block_item_damage_size * 645.16;
                            break;
                        case ['in', 'sq. ft.']:
                            $block_item_gross_size = $block_item_gross_size * 0.00694444;
                            $block_item_net_size = $block_item_net_size * 0.00694444;
                            $block_item_damage_size = $block_item_damage_size * 0.00694444;
                            break;
                        case ['in', 'sq. mt.']:
                            $block_item_gross_size = $block_item_gross_size * 0.00064516;
                            $block_item_net_size = $block_item_net_size * 0.00064516;
                            $block_item_damage_size = $block_item_damage_size * 0.00064516;
                            break;

                            // Feet

                        case ['ft', 'sq. cm.']:
                            $block_item_gross_size = $block_item_gross_size * 929.0304;
                            $block_item_net_size = $block_item_net_size * 929.0304;
                            $block_item_damage_size = $block_item_damage_size * 929.0304;
                            break;
                        case ['ft', 'sq. mm.']:
                            $block_item_gross_size = $block_item_gross_size * 92903.04;
                            $block_item_net_size = $block_item_net_size * 92903.04;
                            $block_item_damage_size = $block_item_damage_size * 92903.04;
                            break;
                        case ['ft', 'sq. in.']:
                            $block_item_gross_size = $block_item_gross_size * 0.09290304;
                            $block_item_net_size = $block_item_net_size * 0.09290304;
                            $block_item_damage_size = $block_item_damage_size * 0.09290304;
                            break;
                        case ['ft', 'sq. mt.']:
                            $block_item_gross_size = $block_item_gross_size * 0.09290304;
                            $block_item_net_size = $block_item_net_size * 0.09290304;
                            $block_item_damage_size = $block_item_damage_size * 0.09290304;
                            break;

                            // Meter

                        case ['mt', 'sq. cm.']:
                            $block_item_gross_size = $block_item_gross_size * 10000;
                            $block_item_net_size = $block_item_net_size * 10000;
                            $block_item_damage_size = $block_item_damage_size * 10000;
                            break;
                        case ['mt', 'sq. mm.']:
                            $block_item_gross_size = $block_item_gross_size * 1000000;
                            $block_item_net_size = $block_item_net_size * 1000000;
                            $block_item_damage_size = $block_item_damage_size * 1000000;
                            break;
                        case ['mt', 'sq. in.']:
                            $block_item_gross_size = $block_item_gross_size * 1550.0031;
                            $block_item_net_size = $block_item_net_size * 1550.0031;
                            $block_item_damage_size = $block_item_damage_size * 1550.0031;
                            break;
                        case ['mt', 'sq. ft.']:
                            $block_item_gross_size = $block_item_gross_size * 10.7639;
                            $block_item_net_size = $block_item_net_size * 10.7639;
                            $block_item_damage_size = $block_item_damage_size * 10.7639;
                            break;
                    }

                    if ($block_size_dimension == 'mt (t)') {
                        $block_item_gross_size = array_sum($block_item_gross_size);
                        $block_item_net_size = array_sum($block_item_net_size);
                        $block_item_damage_size = array_sum($block_item_damage_size);
                    } else {
                        $block_item_gross_size = number_format((float)$block_item_gross_size, 2, '.', '');
                        $block_item_net_size = number_format((float)$block_item_net_size, 2, '.', '');
                        $block_item_damage_size = number_format((float)$block_item_damage_size, 2, '.', '');
                    }

                    // Create new block

                    $countSize = explode(',', $requestData['data']['hide_slabs_id_array']);
                    $totalSlabsToTransfer = count($countSize);


                    // Old warehouse info

                    $oldLocationMeta = explode('_', $requestData['data']['current_location']);
                    $oldWarehouseId = $oldLocationMeta[0];

                    $updateOldBlock = mysqli_query($connection, "UPDATE {$tableNamePrefix}block SET block_gross_size=block_gross_size-'{$block_item_gross_size}', block_net_size=block_net_size-'{$block_item_net_size}', block_damage_size=block_damage_size-{$block_item_damage_size}, block_total_item=block_total_item-{$totalSlabsToTransfer}, block_available=block_available-{$totalSlabsToTransfer}, block_warehouse_id='{$oldWarehouseId}' WHERE block_code = '{$requestData['data']['block_code']}' AND block_status=1 AND block_site_id=" . SITE_ID);

                    // Generate new block id 

                    $newLocationMeta = explode('_', $requestData['data']['new_location']);
                    $newWarehouseId = $newLocationMeta[0];
                    $newWarehouseName = $newLocationMeta[1];

                    $newBlockCode = $requestData['data']['block_code'] . '-' . $newWarehouseId;

                    if ($checkNewBlockIsExists = mysqli_query($connection, "SELECT block_code FROM {$tableNamePrefix}block WHERE block_code='{$newBlockCode}' AND block_status=1 AND block_site_id=" . SITE_ID)) {
                        if (mysqli_num_rows($checkNewBlockIsExists) == 0) {

                            // Fetch previos block information to clone

                            if ($checkOldBlockData = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}block WHERE block_id='{$request_relation_id}' AND block_code='{$requestData['data']['block_code']}' AND block_site_id=" . SITE_ID)) {
                                if (mysqli_num_rows($checkOldBlockData) != 0) {

                                    extract(mysqli_fetch_assoc($checkOldBlockData));

                                    $updateGrossSize = $block_gross_size - $block_item_gross_size;
                                    $updateDamageSize = $block_damage_size - $block_item_damage_size;
                                    $updateNetSize = $block_net_size - $block_item_net_size;

                                    $createNewBlock = mysqli_query($connection, "INSERT INTO {$tableNamePrefix}block(block_site_id, block_user_id, block_warehouse_id, block_supplier_id, block_title, block_description, block_feature_image, block_gallery, block_code, block_thickness_size, block_thickness_dimension, block_product_type, block_size_dimension, block_total_item, block_status, block_created, block_gross_size, block_damage_size, block_net_size, block_available) VALUES(" . SITE_ID . ", '{
                                    $block_user_id}',  {$newWarehouseId}, '{$block_supplier_id}', '{$block_title}', '{$block_description}', '{$block_feature_image}', '{$block_gallery}', '{$newBlockCode}', '{$block_thickness_size}', '{$block_thickness_dimension}', '{$block_product_type}', '{$block_size_dimension}', '{$totalSlabsToTransfer}', 1, now(), '{$updateGrossSize}', '{$updateDamageSize}', '{$updateNetSize}', '{$totalSlabsToTransfer}')");

                                    $newWarehouseBlockId = mysqli_insert_id($connection);

                                    // Swap old block id with new block id

                                    $updateOldSlabsBlock = mysqli_query($connection, "UPDATE {$tableNamePrefix}block_item SET block_item_parent_id='{$newWarehouseBlockId}', block_item_status=1 WHERE block_item_id IN ({$requestData['data']['hide_slabs_id_array']}) AND block_item_parent_id='{$request_relation_id}' AND block_item_site_id=" . SITE_ID);
                                }
                            }
                        } else {
                            $msg = "Block is already exists in {$newWarehouseName}";
                        }
                    } else {
                        $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something went wrong while verifying the block');
                    }
                } else {
                    $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something went wrong while verifying the block');
                }
            }

            if ($updateRequest && $updateBlockStatus && $updateOldBlock && $createNewBlock && $updateOldSlabsBlock) {
                $msg = "Request accepted successfully";
                $httpStatus = 202;
                $status = 'success';
                mysqli_query($connection, "COMMIT");
            } else {
                $status = 'error';
                $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : "Something went wrong while deleting request");
                mysqli_query($connection, "ROLLBACK");
            }
        } else {
            $msg = 'Invalid request id';
        }
    } else {
        $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : "Something went wrong while trying to execute query");
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
