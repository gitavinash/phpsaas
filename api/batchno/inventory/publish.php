<?php

$httpStatus = 400;
$status = 'warning';
$msg = 'Invalid request';

if ($name != '') {

    require_once MODULES . 'imageUploader.php';

    if (!isset($image['featureImage'])) {
        $featureImage = '';
        $thumbnailImage = '';
    }


    // Extract the single upload image string to variables

    $postType = 'product';
    $currentStock = ((isset($totalItem)) ? $totalItem : 0);

    $facebookImage = (((isset($facebookImage)) ? $facebookImage : $featureImage));
    $twitterImage = (((isset($twitterImage)) ? $twitterImage : $featureImage));

    if (isset($_FILES['gallery'])) {

        require_once MODULES . 'galleryUploader.php';
    } else {

        $gallery = '';
    }

    // Product Variation

    $productVariation = '';
    $mrp = (($purchaseMRP == 0) ? $salePrice : $purchaseMRP);
    $salePrice = (($sellingPrice == 0) ? $mrp : $sellingPrice);

    if (isset($_POST['similarProducts'])) {

        for ($i = 0; $i < sizeof($_POST['similarProducts']); $i++) {

            $splitString[] = explode('&&', $_POST['similarProducts'][$i]);
        }

        $productVariation = json_encode($splitString, TRUE);
    }

    $productVariation = base64_encode($productVariation);

    /** Category */

    if (isset($productType)) {

        /** product page url */

        $url = strtolower('/product-' . preg_replace('~[^\p{M}\w-]+~u', '-', $name)) . ' - ' . time() . '/';

        /* define facebook and twitter meta desciption */

        $sell_online = ((isset($sell_online)) ? $sell_online : 1);

        if ($checkURL = mysqli_query($connection, "SELECT link_relation_id FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_site_id='" . SITE_ID . "'")) {

            if (mysqli_num_rows($checkURL) == 0) {

                require_once EDITOR_CONTENT_FILTER;

                $brand = ((isset($brand)) ? $brand : 0);

                /** Start uploading the product */

                mysqli_query($connection, "START TRANSACTION");

                /* Insert data into link table */

                $addNewProduct = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "product(product_site_id, product_brand_id, product_image, product_thumbnail, product_gallery, product_title, product_description, product_highlight, product_category, product_owner_id, product_status, product_type) VALUES(" . SITE_ID . ", '{$brand}', '{$featureImage}', '{$thumbnailImage}', '{$gallery}', '{$name}', '{$description}', '', '{$productType}', " . USER_ID . ",'1', 'product')");
                $productID = mysqli_insert_id($connection);

                $addNewLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_is_featured, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$productID}', '200', 'product', '{$featurePost}', " . USER_ID . ", '" . SITE_ID . "', NOW() ,NOW())");
                $linkID = mysqli_insert_id($connection);

                $addNewInventory = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "inventory(inventory_relation_id, inventory_site_id, inventory_warehouse_id, inventory_supplier_id, inventory_sku, inventory_currency, inventory_price, inventory_purchase_price, inventory_mrp, inventory_tax_status, inventory_tax_rate, inventory_in_stock, inventory_stock, inventory_current_stock, inventory_backorder, inventory_stock_notice, inventory_weight, inventory_lenght, inventory_width, inventory_height, inventory_status, inventory_update, inventory_sell_online, inventory_variation) VALUES ('{$productID}', " . SITE_ID . ", '{$warehouseId}', '{$supplierId}', '{$itemCode}', 'INR', '{$salePrice}', '{$purchaseCost}', '{$mrp}', '', '', '', '{$currentStock}', '{$currentStock}', '', '{$minStock}', '', '', '', '', '1', current_timestamp(), '{$sell_online}', '{$productVariation}')");

                $addNewMeta = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "meta(meta_relation_id, meta_site_id,  meta_title, meta_type, meta_description, meta_keyword, meta_canonical, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$linkID}', " . SITE_ID . ", '{$name}', 'product', '', '', '', '', '', '', '', '', '', '')");

                /* Insert tags into tag table */

                if (isset($tags)) {

                    if ($tags != '') {

                        $tags = explode(',', $tags);

                        for ($i = 0; $i < sizeof($tags); $i++) {

                            $tagsName = strtolower(preg_replace('~[^\p{M}\s\w,-]+~u', '', rtrim(ltrim($tags[$i], ' '), ' ')));
                            $addNewTags = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagsName}', '{$productID}', '" . USER_ID . "', '" . SITE_ID . "', '{$postType}')");
                        }
                    } else {

                        $addNewTags = TRUE;
                    }
                } else {
                    $addNewTags = TRUE;
                }

                if (isset($_POST['productMeta'])) {

                    foreach ($_POST['productMeta'] as $product_meta_key => $product_meta_value) {

                        $addProductMeta = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "product_meta(product_meta_relation_id, product_meta_site_id, product_meta_key, product_meta_value, product_meta_status) VALUES('{$productID}', " . SITE_ID . ", '{$product_meta_key}', '{$product_meta_value}', 1)");
                    }
                } else {

                    $addProductMeta = true;
                }

                // Generate QR Code

                require_once QR_CODE . 'qrlib.php';
                $uploadPath = ROOT . '/upload/' . SITE_DOMAIN . '/qrcode/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $qrCodeURL = SITE_URL . '/inventory/?id=' . base64_encode($productID);

                $qrCodeFile = $uploadPath . 'product_no_' . $productID . '.png';
                QRcode::png($qrCodeURL, $qrCodeFile, 'H', 16, 1);
                // if (!file_exists($qrCodeFile)) {
                // }
                $publicQRCodeURL = str_replace(ROOT, '', $qrCodeFile);


                $updateInventoryQR = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "inventory SET inventory_qr='{$publicQRCodeURL}' WHERE inventory_relation_id='{$productID}' AND inventory_site_id=" . SITE_ID);

                if ($addNewProduct && $addNewLink && $addNewInventory && $addNewMeta && $addNewTags && $addProductMeta && $updateInventoryQR) {
                    $httpStatus = 200;
                    $status = 'success';
                    $msg = 'Product add successfully';
                    $redirect = '/dashboard/product/?type=edit&editor=true&id=' . base64_encode($productID);
                    mysqli_query($connection, "COMMIT");
                } else {
                    $httpStatus = 500;
                    // $msg = 'Something is wrong while adding new product';
                    $msg = $addNewProduct . " && " . $addNewLink . " && " . $addNewInventory . " && " . $addNewMeta . " && " . $addNewTags . " && " . $addProductMeta;
                    mysqli_query($connection, "ROLLBACK");
                }
            } else {

                $msg = 'Permalink value is not unique';
            }
        } else {

            $msg = 'Technical errro';
        }
    } else {

        $msg = 'Please select product category';
    }
} else {

    $msg = 'Please enter product name';
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
    'redirect' => ((isset($redirect)) ? $redirect : '')
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
