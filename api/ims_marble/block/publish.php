<?php

$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

if ($name != '') {
    if ($warehouseId != '') {
        if ($itemCode != '') {
            if ($totalItem != '') {
                if ($thickness != '') {
                    if ($thicknessSize != '') {
                        if ($productType != '') {
                            $validRequest = true;
                        } else {
                            $msg = 'Kindly select product type';
                        }
                    } else {
                        $msg = 'Kindly selext block item thickness size';
                    }
                } else {
                    $msg = 'Kindly enter block item thickenss';
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
    require_once MODULES . 'imageUploader.php';

    if (!isset($image['featureImage'])) {
        $featureImage = '';
    }
    if (isset($_FILES['gallery'])) {
        require_once MODULES . 'galleryUploader.php';
    } else {
        $gallery = '';
    }

    // Check block code

    if ($itemCode != '') {
        $checkItemCode = mysqli_query($connection, "SELECT block_code FROM {$tableNamePrefix}block WHERE block_code='{$itemCode}' AND block_warehouse_id='{$warehouseId}' AND block_status=1 AND block_site_id=" . SITE_ID);
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
        mysqli_query($connection, "START TRANSACTION");

        $dimensions = constant(strtoupper('measurement_' . $productType));
        extract(json_decode($dimensions, TRUE));

        $insertBlock = mysqli_query($connection, "INSERT INTO {$tableNamePrefix}block(block_site_id, block_user_id, block_warehouse_id, block_supplier_id, block_title, block_description, block_feature_image, block_gallery, block_code, block_thickness_size, block_thickness_dimension, block_product_type, block_size_dimension, block_total_item, block_available, block_status, block_created) VALUES(" . SITE_ID . ", " . USER_ID . ",  {$warehouseId}, '{$supplier}', '{$name}', '{$description}', '{$featureImage}', '{$gallery}', '{$itemCode}', '{$thickness}', '{$thicknessSize}', '{$productType}', '{$calculationType}', '{$totalItem}', '{$totalItem}', 1, now())");
        $blockId = mysqli_insert_id($connection);

        // Generate QR Code

        require_once QR_CODE . 'qrlib.php';
        $uploadPath = ROOT . '/upload/' . SITE_DOMAIN . '/qrcode/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $qrCodeURL = SITE_URL . '/view_block/?id=' . base64_encode($blockId);

        $qrCodeFile = $uploadPath . 'block_no_' . $blockId . '.png';
        QRcode::png($qrCodeURL, $qrCodeFile, 'H', 16, 1);
        // if (!file_exists($qrCodeFile)) {
        // }
        $publicQRCodeURL = str_replace(ROOT, '', $qrCodeFile);

        if ($itemCode == '') {
            $updateBlock = mysqli_query($connection, "UPDATE {$tableNamePrefix}block SET block_qr='{$publicQRCodeURL}', block_code='{$blockId}' WHERE block_id='{$blockId}' AND block_site_id=" . SITE_ID);
        } else {
            $updateBlock = mysqli_query($connection, "UPDATE {$tableNamePrefix}block SET block_qr='{$publicQRCodeURL}' WHERE block_id='{$blockId}' AND block_site_id=" . SITE_ID);
        }

        // $insertItemQuery = "INSERT INTO {$tableNamePrefix}block_item(block_item_parent_id, block_item_site_id, block_item_user_id, block_item_dimension_type, block_item_status, block_item_created) VALUES";


        // for ($i = 0; $i < $totalItem; $i++) {
        //     $insertItemQuery .= "({$blockId}, " . SITE_ID . ", " . USER_ID . ", '{$measurementType}', 0, now()),";
        // }

        // $insertItemQuery = rtrim($insertItemQuery, ",");

        // $insertBlockItem = mysqli_query($connection, $insertItemQuery);

        // if ($insertBlock && $updateBlock && $insertBlockItem) {
        if ($insertBlock && $updateBlock) {
            $httpStatus = 201;
            $status = 'success';
            $msg = 'New block created successfully';
            $productId = $blockId;
            mysqli_query($connection, 'COMMIT');
        } else {
            $status = 'error';
            $msg = 'Something went wrong while trying to create new block';
            mysqli_query($connection, 'ROLLBACK');
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