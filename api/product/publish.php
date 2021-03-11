<?php

    function publish($connection, $data) {

        extract($data);
        
        if($title!='') {

            require_once MODULES.'imageUploader.php';

            if(isset($image['featureImage'])) {

                // Extract the single upload image string to variables

                $featurePost=((isset($featurePost)) ? 1 : 0 );
                $postType=((isset($postType)) ? $postType : $type );
                $currentStock=((isset($currentStock)) ? $currentStock : 0 );
        
                $facebookImage=(((isset($facebookImage)) ? $facebookImage : $featureImage ));
                $twitterImage=(((isset($twitterImage)) ? $twitterImage : $featureImage ));

                if(isset($_FILES['gallery'])) {
        
                    require_once MODULES.'galleryUploader.php';

                    /** Category */

                    if(isset($category)) {

                        $category=explode('_', $category);
                        $categoryURL=$category[1];
                        $category=$category[0];

                        /** product page url */

                        $url=strtolower('/product'.$categoryURL.preg_replace('~[^\p{M}\w-]+~u', '-', $url)).'/';
                        
                        /* define facebook and twitter meta desciption */

                        $fb_desc=(($fb_desc=='') ? $meta_desc : $fb_desc );
                        $tw_desc=(($tw_desc=='') ? $meta_desc : $tw_desc );

                        if($checkURL=mysqli_query($connection, "SELECT link_relation_id FROM ".TABLE_PREFIX."link WHERE link_url='{$url}' AND link_site_id='".SITE_ID."'")) {

                            if(mysqli_num_rows($checkURL)==0) {

                                require_once EDITOR_CONTENT_FILTER;

                                /** Start uploading the product */

                                mysqli_query($connection, "START TRANSACTION");

                                /* Insert data into link table */

                                $addNewProduct=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."product(product_site_id, product_image, product_thumbnail, product_gallery, product_title, product_description, product_highlight, product_category, product_owner_id, product_status, product_type) VALUES(".SITE_ID.", '{$featureImage}', '{$thumbnailImage}', '{$gallery}', '{$title}', '{$description}', '{$highlight}', '{$category}', ".USER_ID.",'1', 'product')");
                                $productID=mysqli_insert_id($connection);

                                $addNewLink=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_is_featured, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$productID}', '200', 'product', '{$featurePost}', ".USER_ID.", '".SITE_ID."', NOW() ,NOW())");
                                $linkID=mysqli_insert_id($connection);

                                $addNewInventory=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."inventory(inventory_relation_id, inventory_site_id, inventory_sku, inventory_currency, inventory_price, inventory_mrp, inventory_tax_status, inventory_tax_rate, inventory_in_stock, inventory_current_stock, inventory_backorder, inventory_stock_notice, inventory_weight, inventory_lenght, inventory_width, inventory_height, inventory_status, inventory_update) VALUES ('{$productID}', ".SITE_ID.", '{$sku}', 'INR', '{$salePrice}', '{$mrp}', '{$taxStatus}', '{$taxRate}', '{$in_stock}', '{$currentStock}', '{$backorder}', '{$stock_warning}', '{$weight}', '{$lenght}', '{$width}', '{$height}', '1', current_timestamp())");

                                $addNewMeta=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."meta(meta_relation_id, meta_site_id,  meta_title, meta_type, meta_description, meta_keyword, meta_canonical, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$linkID}', ".SITE_ID.", '{$meta_title}', 'product', '{$meta_desc}', '{$meta_key}', '{$canonical}', '{$robots}', '{$facebookImage}', '{$fb_title}', '{$fb_desc}', '{$twitterImage}', '{$tw_title}', '{$tw_desc}')");

                                /* Insert tags into tag table */

                                if($tags!='') {

                                    $tags=explode(',', $tags);

                                    for($i=0; $i<sizeof($tags); $i++) {

                                        $tagsName=strtolower(preg_replace('~[^\p{M}\s\w,-]+~u', '', rtrim(ltrim($tags[$i], ' '), ' ')));
                                        $addNewTags=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagsName}', '{$productID}', '".USER_ID."', '".SITE_ID."', '{$postType}')");

                                    }

                                } else {

                                    $addNewTags=TRUE;

                                }

                                if(isset($_POST['productMeta'])) {

                                    foreach($_POST['productMeta'] as $product_meta_key => $product_meta_value ) {
                        
                                        $addProductMeta=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."product_meta(product_meta_relation_id, product_meta_site_id, product_meta_key, product_meta_value, product_meta_status) VALUES('{$productID}', ".SITE_ID.", '{$product_meta_key}', '{$product_meta_value}', 1)");
                        
                                    }
                        
                                } else {
                        
                                    $addProductMeta=true;
                        
                                }

                                if($addNewProduct && $addNewLink && $addNewInventory && $addNewMeta && $addNewTags && $addProductMeta) {

                                    $status=200;
                                    $msg='Product add successfully';
                                    $redirect='/dashboard/product/?type=edit&editor=true&id='.base64_encode($productID);
                                    mysqli_query($connection, "COMMIT");

                                } else {

                                    $msg='Something is wrong while adding new product';
                                    mysqli_query($connection, "ROLLBACK");

                                }

                            } else {

                                $msg='Permalink value is not unique';

                            }

                        } else {

                            $msg='Technical errro';

                        }

                    } else {

                        $msg='Please select product category';

                    }
            
                } else {

                    print_r($_FILES);

                }

            } else {

                $msg='Please select product fearure image.';

            }

        } else {

            $msg='Please enter product name';

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
            'redirect' => ((isset($redirect)) ? $redirect : '' )
        );
    
        echo json_encode($result, TRUE);

    }