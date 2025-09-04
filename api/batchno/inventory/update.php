<?php

$httpStatus = 400;
$status = 'warning';
$msg = 'Invalid request';

if ($name != '') {

    if (isset($productType)) {

        if ($checkProduct = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "inventory ON product_id=inventory_relation_id AND inventory_site_id=" . SITE_ID . " AND inventory_status=1 INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id AND link_site_id=" . SITE_ID . " AND link_status=200 AND link_type='product' WHERE product_id='{$productId}' AND product_status=1 AND product_site_id=" . SITE_ID)) {

            if (mysqli_num_rows($checkProduct) != 0) {

                extract(mysqli_fetch_assoc($checkProduct));

                require_once MODULES . 'imageUploader.php';

                if (!isset($image['featureImage'])) {
                    $featureImage = $oldFeatureImage;
                    $thumbnailImage = $oldFeatureImage;
                }

                $featurePost = ((isset($featurePost)) ? 1 : 0);
                $in_stock = ((isset($in_stock)) ? 1 : 0);
                $backorder = ((isset($backorder)) ? 1 : 0);
                $postType = 'product';
                $sell_online = ((isset($sell_online)) ? $sell_online : 1);

                if (isset($_FILES['gallery'])) {

                    require_once MODULES . 'galleryUploader.php';

                    if ($gallery != '') {

                        $isGalleryUpdate = TRUE;
                    }
                }

                $oldGallery = json_decode($product_gallery, TRUE);

                // Match old gallery db from from value
                $newProductGallery = array();

                if (isset($_POST['oldGallery'])) {

                    for ($i = 0; $i < sizeof($_POST['oldGallery']); $i++) {

                        $j = $_POST['oldGallery'][$i] - 1;

                        $newProductGallery[] = $oldGallery[$j];
                    }
                }

                /** Update old gallery */

                if (isset($isGalleryUpdate)) {

                    $newProductGallery = array_merge($newProductGallery, $images);
                }

                $newProductGallery = json_encode($newProductGallery, TRUE);

                // Product Variation

                $productVariation = '';

                if (isset($_POST['similarProducts'])) {

                    for ($i = 0; $i < sizeof($_POST['similarProducts']); $i++) {

                        $splitString[] = explode('&&', $_POST['similarProducts'][$i]);
                    }

                    $productVariation = json_encode($splitString, TRUE);
                }

                $productVariation = base64_encode($productVariation);

                /* define facebook and twitter meta desciption */

                $brand = ((isset($brand)) ? $brand : 0);

                /** Start updating in products */

                mysqli_query($connection, "START TRANSACTION");

                $updateProduct = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "product SET product_brand_id = '{$brand}', product_image='{$featureImage}', product_thumbnail='{$thumbnailImage}', product_gallery='{$newProductGallery}', product_title='{$name}', product_description='{$description}', product_highlight='', product_category='{$productType}', product_owner_id='" . USER_ID . "', product_status=1, product_type='{$postType}' WHERE product_id='{$productId}'");

                $updateInventtory = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "inventory SET inventory_warehouse_id='{$warehouseId}', inventory_supplier_id='{$supplierId}', inventory_sku='{$itemCode}', inventory_currency='INR', inventory_purchase_price='{$purchaseCost}', inventory_price='{$sellingPrice}', inventory_mrp='{$purchaseMRP}', inventory_tax_status='', inventory_tax_rate='', inventory_in_stock='', inventory_stock='{$totalItem}', inventory_backorder='{$backorder}', inventory_stock_notice='{$minStock}', inventory_weight='', inventory_lenght='', inventory_width='', inventory_height='', inventory_status=1, inventory_update=current_timestamp(), inventory_sell_online='{$sell_online}', inventory_variation='{$productVariation}' WHERE inventory_relation_id='{$productId}'");

                $updateMeta = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "meta SET meta_title='{$name}' WHERE meta_relation_id='{$link_id}'");

                // Update product meta

                if (isset($_POST['productMeta'])) {

                    foreach ($_POST['productMeta'] as $product_meta_key => $product_meta_value) {

                        if ($checkMeta = mysqli_query($connection, "SELECT product_meta_id FROM " . TABLE_PREFIX . "product_meta WHERE product_meta_key='{$product_meta_key}' AND product_meta_relation_id='{$productId}' AND product_meta_site_id=" . SITE_ID)) {

                            if (mysqli_num_rows($checkMeta) != 0) {

                                extract(mysqli_fetch_assoc($checkMeta));

                                $updateProductMeta = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "product_meta SET product_meta_value='{$product_meta_value}' WHERE product_meta_key='{$product_meta_key}' AND product_meta_relation_id='{$productId}' AND product_meta_site_id=" . SITE_ID);
                            } else {

                                $updateProductMeta = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "product_meta(product_meta_relation_id, product_meta_site_id, product_meta_key, product_meta_value, product_meta_status) VALUES('{$productId}', " . SITE_ID . ", '{$product_meta_key}', '{$product_meta_value}', 1)");
                            }
                        }
                    }
                } else {

                    $updateProductMeta = true;
                }

                if ($updateProduct && $updateInventtory && $updateMeta && $updateProductMeta) {

                    $httpStatus = 200;
                    $status = 'success';
                    $msg = 'Product update successfully';
                    $redirect = "/dashboard/product/?type=edit&editor=true&id=" . base64_encode($productId);
                    mysqli_query($connection, "COMMIT");
                } else {

                    echo $updateProduct . ' && ' . $addRedirect . ' && ' . $updateInventtory . ' && ' . $updateMeta . ' && ' . $updateTags . ' && ' . $updateProductMeta;
                    $msg = 'Something is wrong while updateing product';
                    mysqli_query($connection, "ROLLBACK");
                }
            } else {

                $msg = 'Invalid value received';
            }
        }
    } else {

        $msg = 'Please select category';
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