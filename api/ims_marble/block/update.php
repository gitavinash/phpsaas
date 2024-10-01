<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

if ($name != '') {
    if ($warehouseId != '') {
        if ($itemCode != '') {
            if ($totalItem != '') {
                if ($oldTotalItem <= $totalItem) {
                    if ($thickness != '') {
                        if ($thicknessSize != '') {
                            if ($productType != '') {
                                $validRequest = true;
                            } else {
                                $msg = "Kindly select product type";
                            }
                        } else {
                            $msg = 'Kindly selext block item thickness size';
                        }
                    } else {
                        $msg = 'Kindly enter block item thickenss';
                    }
                } else {
                    $msg = 'Block total item cannot be less than ' . $oldTotalItem;
                }
            } else {
                $msg = 'Kindly enter total items in block';
            }
        } else {
            $msg = 'Kindly enter block code';
        }
    } else {
        $msg = 'Kindly select warehouse';
    }
} else {
    $msg = 'Please enter block name';
}

if (isset($validRequest)) {
    if ($checkBlock = mysqli_query($connection, "SELECT block_size_dimension, block_product_type, block_gross_size, block_damage_size, block_net_size, COUNT(block_item_id) as totalExistingSlab FROM {$tableNamePrefix}block INNER JOIN {$tableNamePrefix}block_item ON block_item_parent_id=block_id WHERE block_id='{$blockId}' AND block_status=1 AND block_item_status=1 AND block_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkBlock) != 0) {
            extract(mysqli_fetch_assoc($checkBlock));


            require_once MODULES . 'imageUploader.php';

            if (!isset($image['featureImage'])) {
                $featureImage = $oldFeatureImage;
            }
            if (isset($_FILES['gallery'])) {
                require_once MODULES . 'galleryUploader.php';
            } else {
                $gallery = $oldGalleryImages;
            }

            // Check block code

            if ($itemCode != $oldItemCode) {
                $checkItemCode = mysqli_query($connection, "SELECT block_product_type block FROM {$tableNamePrefix}block WHERE block_code='{$itemCode}' AND block_warehouse_id='{$warehouseId}' AND block_status=1 AND block_site_id=" . SITE_ID);
                if (mysqli_num_rows($checkItemCode) == 0) {
                    $validBlockCode = true;
                } else {
                    $httpStatus = 409;
                    $msg = 'Block code is already exist in records';
                }
            } else {
                $validBlockCode = true;
            }

            if (isset($validBlockCode)) {

                if ($checkMeasurement = mysqli_query($connection, "SELECT site_configuration_value FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_key='" . strtoupper('measurement_' . $productType) . "' AND site_configuration_status=1 AND site_configuration_site_id=" . SITE_ID)) {
                    if (mysqli_num_rows($checkMeasurement) != 0) {
                        extract(mysqli_fetch_assoc($checkMeasurement));
                        $newProductType = json_decode($site_configuration_value, TRUE);

                        $block_gross_size = (($block_gross_size == '') ? 0 : $block_gross_size);
                        $block_net_size = (($block_net_size == '') ? 0 : $block_net_size);
                        $block_damage_size = (($block_damage_size == '') ? 0 : $block_damage_size);

                        switch ([$block_size_dimension, $newProductType['calculationType']]) {

                                // Centimeter

                            case ['sq. cm.', 'sq. mm.']:
                                $block_gross_size = $block_gross_size * 100;
                                $block_net_size = $block_net_size * 100;
                                $block_damage_size = $block_damage_size * 100;
                                break;
                            case ['sq. cm.', 'sq. in.']:
                                $block_gross_size = $block_gross_size * 0.15500031;
                                $block_net_size = $block_net_size * 0.15500031;
                                $block_damage_size = $block_damage_size * 0.15500031;
                                break;
                            case ['sq. cm.', 'sq. ft.']:
                                $block_gross_size = $block_gross_size * 0.001076391;
                                $block_net_size = $block_net_size * 0.001076391;
                                $block_damage_size = $block_damage_size * 0.001076391;
                                break;
                            case ['sq. cm.', 'sq. mt.']:
                                $block_gross_size = $block_gross_size * 0.0001;
                                $block_net_size = $block_net_size * 0.0001;
                                $block_damage_size = $block_damage_size * 0.0001;
                                break;

                                // Millimeter

                            case ['sq. mm.', 'sq. cm.']:
                                $block_gross_size = $block_gross_size * 0.01;
                                $block_net_size = $block_net_size * 0.01;
                                $block_damage_size = $block_damage_size * 0.01;
                                break;
                            case ['sq. mm.', 'sq. in.']:
                                $block_gross_size = $block_gross_size * 0.0015500031;
                                $block_net_size = $block_net_size * 0.0015500031;
                                $block_damage_size = $block_damage_size * 0.0015500031;
                                break;
                            case ['sq. mm.', 'sq. ft.']:
                                $block_gross_size = $block_gross_size * 0.00001076391;
                                $block_net_size = $block_net_size * 0.00001076391;
                                $block_damage_size = $block_damage_size * 0.00001076391;
                                break;
                            case ['sq. mm.', 'sq. mt.']:
                                $block_gross_size = $block_gross_size * 0.000001;
                                $block_net_size = $block_net_size * 0.000001;
                                $block_damage_size = $block_damage_size * 0.000001;
                                break;

                                // Inches

                            case ['sq. in.', 'sq. cm.']:
                                $block_gross_size = $block_gross_size * 6.4516;
                                $block_net_size = $block_net_size * 6.4516;
                                $block_damage_size = $block_damage_size * 6.4516;
                                break;
                            case ['sq. in.', 'sq. mm.']:
                                $block_gross_size = $block_gross_size * 645.16;
                                $block_net_size = $block_net_size * 645.16;
                                $block_damage_size = $block_damage_size * 645.16;
                                break;
                            case ['sq. in.', 'sq. ft.']:
                                $block_gross_size = $block_gross_size * 0.00694444;
                                $block_net_size = $block_net_size * 0.00694444;
                                $block_damage_size = $block_damage_size * 0.00694444;
                                break;
                            case ['sq. in.', 'sq. mt.']:
                                $block_gross_size = $block_gross_size * 0.00064516;
                                $block_net_size = $block_net_size * 0.00064516;
                                $block_damage_size = $block_damage_size * 0.00064516;
                                break;

                                // Feet

                            case ['sq. ft.', 'sq. cm.']:
                                $block_gross_size = $block_gross_size * 929.0304;
                                $block_net_size = $block_net_size * 929.0304;
                                $block_damage_size = $block_damage_size * 929.0304;
                                break;
                            case ['sq. ft.', 'sq. mm.']:
                                $block_gross_size = $block_gross_size * 92903.04;
                                $block_net_size = $block_net_size * 92903.04;
                                $block_damage_size = $block_damage_size * 92903.04;
                                break;
                            case ['sq. ft.', 'sq. in.']:
                                $block_gross_size = $block_gross_size * 144;
                                $block_net_size = $block_net_size * 144;
                                $block_damage_size = $block_damage_size * 144;
                                break;
                            case ['sq. ft.', 'sq. mt.']:
                                $block_gross_size = $block_gross_size * 0.09290304;
                                $block_net_size = $block_net_size * 0.09290304;
                                $block_damage_size = $block_damage_size * 0.09290304;
                                break;

                                // Meter

                            case ['sq. mt.', 'sq. cm.']:
                                $block_gross_size = $block_gross_size * 10000;
                                $block_net_size = $block_net_size * 10000;
                                $block_damage_size = $block_damage_size * 10000;
                                break;
                            case ['sq. mt.', 'sq. mm.']:
                                $block_gross_size = $block_gross_size * 1000000;
                                $block_net_size = $block_net_size * 1000000;
                                $block_damage_size = $block_damage_size * 1000000;
                                break;
                            case ['sq. mt.', 'sq. in.']:
                                $block_gross_size = $block_gross_size * 1550.0031;
                                $block_net_size = $block_net_size * 1550.0031;
                                $block_damage_size = $block_damage_size * 1550.0031;
                                break;
                            case ['sq. mt.', 'sq. ft.']:
                                $block_gross_size = $block_gross_size * 10.7639;
                                $block_net_size = $block_net_size * 10.7639;
                                $block_damage_size = $block_damage_size * 10.7639;
                                break;
                        }

                        $block_gross_size = number_format((float)$block_gross_size, 2, '.', '');
                        $block_net_size = number_format((float)$block_net_size, 2, '.', '');
                        $block_damage_size = number_format((float)$block_damage_size, 2, '.', '');

                        mysqli_query($connection, "START TRANSACTION");

                        $dimensions = constant(strtoupper('measurement_' . $productType));
                        extract(json_decode($dimensions, TRUE));

                        $updateBlock = mysqli_query($connection, "UPDATE {$tableNamePrefix}block SET block_warehouse_id='{$warehouseId}', block_supplier_id='{$supplier}', block_title='{$name}', block_description='{$description}', block_feature_image='{$featureImage}', block_gallery='{$gallery}', block_code='{$itemCode}', block_thickness_size='{$thickness}', block_thickness_dimension='{$thicknessSize}', block_product_type='{$productType}', block_size_dimension='{$calculationType}', block_total_item='{$totalItem}', block_damage_size='{$block_damage_size}', block_net_size='{$block_net_size}', block_gross_size='{$block_gross_size}'  WHERE block_id='{$blockId}' AND block_site_id=" . SITE_ID);

                        if ($totalItem != $oldTotalItem) {

                            $insertItemQuery = "INSERT INTO {$tableNamePrefix}block_item(block_item_parent_id, block_item_site_id, block_item_user_id, block_item_dimension_type, block_item_status, block_item_created) VALUES";

                            $insertNewRecordsNumber = $totalItem - $oldTotalItem;

                            for ($i = 0; $i < $insertNewRecordsNumber; $i++) {
                                $insertItemQuery .= "({$blockId}, " . SITE_ID . ", " . USER_ID . ", '{$measurementType}', 0, now()),";
                            }

                            $insertItemQuery = rtrim($insertItemQuery, ",");

                            $updateBlockItem = mysqli_query($connection, $insertItemQuery);
                        } else {
                            if ($block_product_type != $productType) {
                                $updateBlockItem = mysqli_query($connection, "UPDATE {$tableNamePrefix}block_item SET block_item_dimension_type='{$measurementType}' WHERE block_item_parent_id='{$blockId}' AND block_item_site_id=" . SITE_ID);
                            } else {
                                $updateBlockItem = true;
                            }
                        }

                        if ($updateBlock && $updateBlockItem) {
                            $httpStatus = 201;
                            $status = 'success';
                            $msg = 'Block update successfully';
                            $productId = $blockId;
                            mysqli_query($connection, 'COMMIT');
                        } else {
                            $status = 'error';
                            $msg = ((DEVELOPMENT_MODE == true) ? mysqli_error($connection) : 'Something went wrong while trying to update block');
                            mysqli_query($connection, 'ROLLBACK');
                        }
                    } else {
                        $msg = "No product type found";
                    }
                } else {
                    $msg = mysqli_error($connection);
                }
            }
        } else {
            $msg = 'Invalid block request received';
        }
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
    'id' => ((isset($productId)) ? $productId : 0),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
