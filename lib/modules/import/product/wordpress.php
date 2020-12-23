<?php

    require_once LOGIN_CHECK;

    while(($dataLines=fgetcsv($openFile)) !== false) {

        if($i!=0) {

            if($dataLines[0]!='') {

                $productName=mysqli_real_escape_string($connection, $dataLines[0]);
                $productURL=mysqli_real_escape_string($connection, $dataLines[1]);
                $productHighlights=mysqli_real_escape_string($connection, $dataLines[3]);
                $productDescription=mysqli_real_escape_string($connection, $dataLines[4]);
                $productStatus=mysqli_real_escape_string($connection, (($dataLines[5]=='publish') ? 1 : 0 ));
                $productDate=mysqli_real_escape_string($connection, date('Y-m-d', strtotime($dataLines[7])));
                $productSKU=mysqli_real_escape_string($connection, $dataLines[10]);
                $productMRP=mysqli_real_escape_string($connection, $dataLines[14]);
                
                /** Product sale price */

                $productSalePrice=mysqli_real_escape_string($connection, $dataLines[15]);
                $productSalePrice=(($productSalePrice!=0) ? $productSalePrice : $productMRP );

                $productWeight=mysqli_real_escape_string($connection, $dataLines[16]);
                $productLenght=mysqli_real_escape_string($connection, $dataLines[17]);
                $productWidth=mysqli_real_escape_string($connection, $dataLines[18]);
                $productHeight=mysqli_real_escape_string($connection, $dataLines[19]);
                $productInStock=mysqli_real_escape_string($connection, (($dataLines[22]=='instock') ? 1 : 0 ));
                $productBackOrder=mysqli_real_escape_string($connection, (($dataLines[23]=='yes') ? 1 : 0 ));
                $productManageStock=mysqli_real_escape_string($connection, (($dataLines[24]=='yes') ? 1 : 0 ));
                $productTaxStatus=mysqli_real_escape_string($connection, (($dataLines[25]=='taxable') ? 1 : 0));
                $productTaxRate=mysqli_real_escape_string($connection, $dataLines[20]);
                $productIsFeatured=mysqli_real_escape_string($connection, $dataLines[28]);
                $productImages=mysqli_real_escape_string($connection, $dataLines[38]);
                $productTags=str_replace('|', ',', mysqli_real_escape_string($connection, $dataLines[43]));

                if($productImages!='') {

                    $galleryImages=explode(' | ', $productImages);

                    $localSavedGallery=array();
                    $imageGallerySize=sizeof($galleryImages);

                    for($j=0; $j<$imageGallerySize; $j++) {

                        if(isset($galleryImages[$j])) {

                            $galleryImageNameArray=explode('/', $galleryImages[$j]);
                            $galleryImagesName=end($galleryImageNameArray);
                            $galleryImagesName=explode('.', $galleryImagesName);
                            $filename=$galleryImagesName[0];
                            $fileExtension=end($galleryImagesName);
                            $filetype='image/'.$fileExtension;

                            $uploadPath=ROOT.'/upload/'.date('Y/m/');
                            $uploadFile=$uploadPath.time().'_'.strtolower(preg_replace('~[^\p{M}\w.-]+~u', '', $filename)).'.'.$fileExtension;
                            $uploadMediaPath=str_replace(ROOT, '', $uploadFile);
                            $uploadMediaLink='/attachment'.str_replace('.'.$fileExtension, '', $uploadMediaPath).'/';
                            $uploadFileName=ucwords(preg_replace('/[_]+/', ' ',str_replace($fileExtension, '', preg_replace('~[^\p{M}\s\w-]+~u', '', $filename))));

							/** Original Image variables */
								
							$originalUploadDir=$uploadPath.'original/';

							/** Thumbnail Image variables */
	
							$thumbnailUploadDir=$uploadPath.'thumbnail/';

                            if(!is_dir($uploadPath)) {
            
                                mkdir($uploadPath, 0755, true);

                                /* Creating original image directory inside default upload directory */

                                mkdir($originalUploadDir, 0755, true);

                                /* Creating thumbnail image directory inside default upload directory */

                                mkdir($thumbnailUploadDir, 0755, true);

                            }

                            $response=file_put_contents($uploadFile, file_get_contents($galleryImages[$j]));

                            if($response) {

                                if($j==0) {

                                    $productFeaturedImage=$uploadMediaPath;

                                }

                                $addNewMedia=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."media(media_url, media_title, media_type, media_ext, media_owner_id, media_status, media_site_id) VALUES('{$uploadMediaPath}', '{$uploadFileName}', '{$filetype}', '{$fileExtension}', '".USER_ID."', 1, '".SITE_ID."')");
                                $mediaID=mysqli_insert_id($connection);
            
                                $mediaLink=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published, link_modified) VALUES('{$uploadMediaLink}', '{$mediaID}', 200, 'media', '".USER_ID."', '".SITE_ID."', NOW(), NOW(), NOW())");

                            } else {

                                $msg='Unable to download image from server ';

                            }

                            $localSavedGallery[]=$uploadMediaPath;

                        } else {

                            $addNewMedia=true;
                            $mediaLink=true;

                        }

                    }

                    $gallery=implode(', ', $localSavedGallery);

                } else {

                    $gallery='';
                    $addNewMedia=true;
                    $mediaLink=true;

                }

                if($checkProduct=mysqli_query($connection, "SELECT inventory_relation_id FROM ".TABLE_PREFIX."inventory WHERE inventory_sku='{$productSKU}'")) {

                    if(mysqli_num_rows($checkProduct)==0) {

                        mysqli_query($connection, "START TRANSACTION");

                        $categoryID=explode('_', $categoryID);
                        $categoryURL=str_replace('category/', '', $categoryID[1]);
                        $categoryID=$categoryID[0];

                        $url='/product/'.strtolower(preg_replace('~[^\p{M}\w-]+~u', '-', $productName)).'/';

                        if($checkURL=mysqli_query($connection, "SELECT link_url FROM ".TABLE_PREFIX."link WHERE link_url='{$url}' AND link_site_id='".SITE_ID."'")) {
                            
                            if(mysqli_num_rows($checkURL)!=0) {

                                $url=ltrim($url, '/').'-'.$productSKU.'/';

                            }

                        }

                        $addNewProduct=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."product(product_image, product_thumbnail, product_gallery, product_title, product_description, product_highlight, product_category, product_owner_id, product_status, product_type) VALUES('{$productFeaturedImage}', '{$productFeaturedImage}', '{$gallery}', '{$productName}', '{$productDescription}', '{$productHighlights}', '{$categoryID}', ".USER_ID.",'1', 'product')");
                        $productID=mysqli_insert_id($connection);

                        $addNewLink=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_is_featured, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$productID}', '200', 'product', '{$productIsFeatured}', ".USER_ID.", '".SITE_ID."', NOW() ,NOW())");
                        $linkID=mysqli_insert_id($connection);

                        $addNewInventory=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."inventory(inventory_relation_id, inventory_sku, inventory_currency, inventory_price, inventory_mrp, inventory_tax_status, inventory_tax_rate, inventory_in_stock, inventory_current_stock, inventory_backorder, inventory_stock_notice, inventory_weight, inventory_lenght, inventory_width, inventory_height, inventory_update, inventory_status) VALUES ('{$productID}', '{$productSKU}', 'INR', '{$productSalePrice}', '{$productMRP}', '{$productTaxStatus}', '{$productTaxRate}', '{$productInStock}', '{$productCurrentStock}', '{$productBackorder}', '{$productLowStockWarning}', '{$productWeight}', '{$productLenght}', '{$productWidth}', '{$productHeight}', '1', current_timestamp())");

                        $addNewMeta=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."meta(meta_relation_id, meta_type, meta_title, meta_description, meta_keyword, meta_canonical, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$linkID}', 'product', '{$productTitle}', '{$productHighlights}', '{$productTags}', '', '', '', '{$productTitle}', '{$productHighlights}', '', '{$productTitle}', '{$productHighlights}')");

                        /* Insert tags into tag table */

                        if($productTags!='') {

                            $tags=explode(',', $productTags);

                                for($i=0; $i<sizeof($tags); $i++) {

                                    $tagsName=strtolower(preg_replace('~[^\p{M}\s\w,-]+~u', '', rtrim(ltrim($tags[$i], ' '), ' ')));
                                    $addNewTags=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagsName}', '{$productID}', '".USER_ID."', '".SITE_ID."', 'product')");
                                
                                }

                        } else {

                            $addNewTags=TRUE;

                        }

                        if($addNewMedia && $mediaLink && $addNewProduct && $addNewLink && $addNewInventory && $addNewMeta && $addNewTags) {

                            $success++;
                            $msg='Product import successfully';
                            mysqli_query($connection, "COMMIT");
                                            
                        } else {

                            $failed++;
                            $msg='Something is wrong while adding new product';
                            mysqli_query($connection, "ROLLBACK");

                        }

                    } else {

                        $status='warning';
                        $dublicate++;
                        $msg='Dublicate products found';

                    }

                }

            }

        }
        
        $i++;

    }