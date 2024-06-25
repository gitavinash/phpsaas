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
                            if ($supplier != '') {
                                $validRequest = true;
                            } else {
                                $msg = 'Kindly select block supplier';
                            }
                        } else {
                            $msg = 'Kindly selext block item thickness size';
                        }
                    } else {
                        $msg = 'Kindly enter block item thickenss';
                    }
                } else {
                    $msg = 'Block total item cannot be less than ' . $oldTotalItem . ' < ' . $totalItem;
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
        $featureImage = $oldFeatureImage;
    }
    if (isset($_FILES['gallery'])) {
        require_once MODULES . 'galleryUploader.php';
    } else {
        $gallery = $oldGalleryImages;
    }

    // Check block code

    if ($itemCode != $oldItemCode) {
        $checkItemCode = mysqli_query($connection, "SELECT block_code FROM {$tableNamePrefix}block WHERE block_code='{$itemCode}' AND block_status=1 AND block_site_id=" . SITE_ID);
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

        $updateBlock = mysqli_query($connection, "UPDATE {$tableNamePrefix}block SET block_warehouse_id='{$warehouseId}', block_supplier_id='{$supplier}', block_title='{$name}', block_description='{$description}', block_feature_image='{$featureImage}', block_gallery='{$gallery}', block_code='{$itemCode}', block_thickness_size='{$thickness}', block_thickness_dimension='{$thicknessSize}', block_total_item='{$totalItem}' WHERE block_id='{$blockId}' AND block_site_id=" . SITE_ID);

        if ($totalItem != $oldTotalItem) {

            $insertItemQuery = "INSERT INTO {$tableNamePrefix}block_item(block_item_parent_id, block_item_site_id, block_item_user_id, block_item_dimenstion_type, block_item_status, block_item_created) VALUES";

            $insertNewRecordsNumber = $totalItem - $oldTotalItem;

            for ($i = 0; $i < $insertNewRecordsNumber; $i++) {
                $insertItemQuery .= "({$blockId}, " . SITE_ID . ", " . USER_ID . ", 'ft', 0, now()),";
            }

            $insertItemQuery = rtrim($insertItemQuery, ",");

            $updateBlockItem = mysqli_query($connection, $insertItemQuery);
        } else {
            $updateBlockItem = true;
        }

        if ($updateBlock && $updateBlockItem) {
            $httpStatus = 201;
            $status = 'success';
            $msg = 'Block update successfully';
            $productId = $blockId;
            mysqli_query($connection, 'COMMIT');
        } else {
            $status = 'error';
            $msg = 'Something went wrong while trying to update block';
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
