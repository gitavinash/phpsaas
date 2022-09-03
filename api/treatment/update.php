<?php

    function update($connection, $data) {

        extract($data);
        
        if($title!='') {

            if(isset($category)) {

                if($checkservice=mysqli_query($connection, "SELECT link_id, meta_relation_id as oldLinkID, service_section FROM ".TABLE_PREFIX."service INNER JOIN ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."meta ON meta_relation_id=link_id WHERE link_url='{$oldURL}' AND meta_type='service' AND service_id='{$id}'")) {

                    if(mysqli_num_rows($checkservice)!=0) {

                        extract(mysqli_fetch_assoc($checkservice));

                        $serviceSection = json_decode($service_section, true);

                        require_once MODULES.'imageUploader.php';

                        if(isset($image)) {
                            
                            // Extract the single upload image string to variables
                            
                            $featureImage=(((isset($featureImage)) ? $featureImage : $oldFeatureImage ));
                            $thumbnailImage=(((isset($thumbnailImage)) ? $featureImage : $featureImage ));
                            $facebookImage=(((isset($facebookImage)) ? $facebookImage : $oldFacebookImage ));
                            $twitterImage=(((isset($twitterImage)) ? $twitterImage : $oldTwitterImage ));

                        } else {

                            $featureImage=$oldFeatureImage;
                            $thumbnailImage=(((isset($thumbnailImage)) ? $featureImage : $oldFeatureImage ));
                            $facebookImage=(((isset($facebookImage)) ? $facebookImage : $oldFacebookImage ));
                            $twitterImage=(((isset($twitterImage)) ? $twitterImage : $oldTwitterImage ));

                        }

                        $featurePost=((isset($featurePost)) ? 1 : 0 );
                        $in_stock=((isset($in_stock)) ? 1 : 0 );
                        $backorder=((isset($backorder)) ? 1 : 0 );
                        $postType=((isset($postType)) ? $postType : $type );
                        $currentStock=((isset($currentStock)) ? $currentStock : 0 );

                        $updatedServiceSection = json_encode(array(
                            'feature_video' => array(
                                'url' => $featureVideo
                            ),
                            'quote' => array(
                                'quote' => $quote
                            ),
                            'treatment_option'=> array(
                                0 => array(
                                    'image' => (($treatmentOptionFeatureImage_1!='') ? $treatmentOptionFeatureImage_1 : $serviceSection['treatment_option'][0]['image']),
                                    'header' => $treatmentOptionHeader_1,
                                    'indications' => $treatmentOptionIndications_1,
                                    'process' => $treatmentOptionProcess_1,
                                    'duration' => $treatmentOptionDuration_1,
                                    'sitting' => $treatmentOptionSitting_1,
                                    'bookNow' => $treatmentOptionBookNow_1,
                                    'watchVideo' => $treatmentOptionWatchVideo_1,
                                ),
                                1 => array(
                                    'image' => (($treatmentOptionFeatureImage_2!='') ? $treatmentOptionFeatureImage_2 : $serviceSection['treatment_option'][1]['image']),
                                    'header' => $treatmentOptionHeader_2,
                                    'indications' => $treatmentOptionIndications_2,
                                    'process' => $treatmentOptionProcess_2,
                                    'duration' => $treatmentOptionDuration_2,
                                    'sitting' => $treatmentOptionSitting_2,
                                    'bookNow' => $treatmentOptionBookNow_2,
                                    'watchVideo' => $treatmentOptionWatchVideo_2,
                                ),
                                2 => array(
                                    'image' => (($treatmentOptionFeatureImage_3!='') ? $treatmentOptionFeatureImage_3 : $serviceSection['treatment_option'][2]['image']),
                                    'header' => $treatmentOptionHeader_3,
                                    'indications' => $treatmentOptionIndications_3,
                                    'process' => $treatmentOptionProcess_3,
                                    'duration' => $treatmentOptionDuration_3,
                                    'sitting' => $treatmentOptionSitting_3,
                                    'bookNow' => $treatmentOptionBookNow_3,
                                    'watchVideo' => $treatmentOptionWatchVideo_3,
                                ),
                                3 => array(
                                    'image' => (($treatmentOptionFeatureImage_4!='') ? $treatmentOptionFeatureImage_4 : $serviceSection['treatment_option'][3]['image']),
                                    'header' => $treatmentOptionHeader_4,
                                    'indications' => $treatmentOptionIndications_4,
                                    'process' => $treatmentOptionProcess_4,
                                    'duration' => $treatmentOptionDuration_4,
                                    'sitting' => $treatmentOptionSitting_4,
                                    'bookNow' => $treatmentOptionBookNow_4,
                                    'watchVideo' => $treatmentOptionWatchVideo_4,
                                ),
                                4 => array(
                                    'image' => (($treatmentOptionFeatureImage_5!='') ? $treatmentOptionFeatureImage_5 : $serviceSection['treatment_option'][4]['image']),
                                    'header' => $treatmentOptionHeader_5,
                                    'indications' => $treatmentOptionIndications_5,
                                    'process' => $treatmentOptionProcess_5,
                                    'duration' => $treatmentOptionDuration_5,
                                    'sitting' => $treatmentOptionSitting_5,
                                    'bookNow' => $treatmentOptionBookNow_5,
                                    'watchVideo' => $treatmentOptionWatchVideo_5,
                                ),
                                5 => array(
                                    'image' => (($treatmentOptionFeatureImage_6!='') ? $treatmentOptionFeatureImage_6 : $serviceSection['treatment_option'][5]['image']),
                                    'header' => $treatmentOptionHeader_6,
                                    'indications' => $treatmentOptionIndications_6,
                                    'process' => $treatmentOptionProcess_6,
                                    'duration' => $treatmentOptionDuration_6,
                                    'sitting' => $treatmentOptionSitting_6,
                                    'bookNow' => $treatmentOptionBookNow_6,
                                    'watchVideo' => $treatmentOptionWatchVideo_6,
                                ),
                                6 => array(
                                    'image' => (($treatmentOptionFeatureImage_7!='') ? $treatmentOptionFeatureImage_7 : $serviceSection['treatment_option'][6]['image']),
                                    'header' => $treatmentOptionHeader_7,
                                    'indications' => $treatmentOptionIndications_7,
                                    'process' => $treatmentOptionProcess_7,
                                    'duration' => $treatmentOptionDuration_7,
                                    'sitting' => $treatmentOptionSitting_7,
                                    'bookNow' => $treatmentOptionBookNow_7,
                                    'watchVideo' => $treatmentOptionWatchVideo_7,
                                ),
                                7 => array(
                                    'image' => (($treatmentOptionFeatureImage_8!='') ? $treatmentOptionFeatureImage_8 : $serviceSection['treatment_option'][7]['image']),
                                    'header' => $treatmentOptionHeader_8,
                                    'indications' => $treatmentOptionIndications_8,
                                    'process' => $treatmentOptionProcess_8,
                                    'duration' => $treatmentOptionDuration_8,
                                    'sitting' => $treatmentOptionSitting_8,
                                    'bookNow' => $treatmentOptionBookNow_8,
                                    'watchVideo' => $treatmentOptionWatchVideo_8,
                                ),
                                8 => array(
                                    'image' => (($treatmentOptionFeatureImage_9!='') ? $treatmentOptionFeatureImage_9 : $serviceSection['treatment_option'][8]['image']),
                                    'header' => $treatmentOptionHeader_9,
                                    'indications' => $treatmentOptionIndications_9,
                                    'process' => $treatmentOptionProcess_9,
                                    'duration' => $treatmentOptionDuration_9,
                                    'sitting' => $treatmentOptionSitting_9,
                                    'bookNow' => $treatmentOptionBookNow_9,
                                    'watchVideo' => $treatmentOptionWatchVideo_9,
                                ),
                                9 => array(
                                    'image' => (($treatmentOptionFeatureImage_10!='') ? $treatmentOptionFeatureImage_10 : $serviceSection['treatment_option'][9]['image']),
                                    'header' => $treatmentOptionHeader_10,
                                    'indications' => $treatmentOptionIndications_10,
                                    'process' => $treatmentOptionProcess_10,
                                    'duration' => $treatmentOptionDuration_10,
                                    'sitting' => $treatmentOptionSitting_10,
                                    'bookNow' => $treatmentOptionBookNow_10,
                                    'watchVideo' => $treatmentOptionWatchVideo_10,
                                ),
                            ),
                            'before_after' => array(
                                'header' => $berforeAfterHeader,
                                'subHeader' => $berforeAfterSubHeader,
                                'description' => $berforeAfterDescription,
                                'videoLink' => $berforeAfterVideLink,
                                'images' => array(
                                    'before' => (($before!='') ? $before : $serviceSection['before_after']['images']['before']),
                                    'after' => (($after!='') ? $after : $serviceSection['before_after']['images']['after'])
                                )
                            ),
                            'why_choose_us' => array(
                                'image' => (($whyChooseUs!='') ? $whyChooseUs : $serviceSection['why_choose_us']['image']),
                                'header' => $whyChooseTitle,
                                'description' => $whyChooseDescription,
                                'steps' => array(
                                    array(
                                        'icon' => (($stepIcon_1!='') ? $stepIcon_1 : $serviceSection['why_choose_us']['steps'][0]['icon']),
                                        'description'=> $stepHeader_1
                                    ),
                                    array(
                                        'icon' => (($stepIcon_2!='') ? $stepIcon_2 : $serviceSection['why_choose_us']['steps'][1]['icon']),
                                        'description'=> $stepHeader_2
                                    ),
                                    array(
                                        'icon' => (($stepIcon_3!='') ? $stepIcon_3 : $serviceSection['why_choose_us']['steps'][2]['icon']),
                                        'description'=> $stepHeader_3
                                    ),
                                )
                            ),
                        ), true);

                        if(isset($_FILES['gallery'])) {
                
                            require_once MODULES.'galleryUploader.php';
                    
                        } else {

                            $gallery=$oldGallery;

                        }
                            
                        /** Category */

                        $category=explode('_', $category);
                        $categoryURL=$category[1];
                        $category=$category[0];

                        /** treatment page url */

                        $url=strtolower('/treatment'.$categoryURL.preg_replace('~[^\p{M}\w-]+~u', '-', $url)).'/';
                                
                        /* define facebook and twitter meta desciption */

                        $fb_desc=(($fb_desc=='') ? $meta_desc : $fb_desc );
                        $tw_desc=(($tw_desc=='') ? $meta_desc : $tw_desc );

                        /** Start updating in treatments */

                        mysqli_query($connection, "START TRANSACTION");

                        if($oldURL!=$url) {
                        
                            if($checkURL=mysqli_query($connection, "SELECT link_url FROM ".TABLE_PREFIX."link WHERE link_url='{$url}' AND link_type='service' AND link_site_id='".SITE_ID."'")) {
                    
                                if(mysqli_num_rows($checkURL)==0) {
                    
                                    /** Add 301 redirect */
                    
                                    $addRedirect=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."link SET link_status=301 WHERE link_url='{$oldURL}' AND link_type='service' AND link_site_id='".SITE_ID."'");
                    
                                    /** Add new active url of the post */
                    
                                    $updateLink=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published, link_is_featured) VALUES('{$url}', '{$id}', 200, 'service', '".USER_ID."', '".SITE_ID."', NOW(), NOW(), '{$featurePost}')");
                                    $link_id=mysqli_insert_id($connection);
                    
                                } else {
                    
                                    $addRedirect=FALSE;
                                    $status='warning';
                                    $msg="Please change post url. Because its already in used";
                    
                                }
                    
                            }
                    
                        } else {
                    
                            $addRedirect=TRUE;
                    
                            if($postStatus==0) {
                    
                                $updateLink=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."link SET link_status=200, link_published=NOW(), link_modified=NOW(), link_is_featured='{$featurePost}' WHERE link_relation_id='{$id}' AND link_type='service' AND link_url='{$oldURL}' AND link_site_id='".SITE_ID."'");
                    
                            } else {
                    
                                $updateLink=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."link SET link_status=200, link_is_featured='{$featurePost}' WHERE link_relation_id='{$id}' AND link_type='service' AND link_url='{$oldURL}' AND link_site_id='".SITE_ID."'");
                    
                            }
                    
                        }

                        $updateservice=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."service SET service_image='{$featureImage}', service_thumbnail='{$thumbnailImage}', service_gallery='{$gallery}', service_title='{$title}', service_description='{$description}', service_highlight='{$highlight}', service_category='{$category}', service_owner_id='".USER_ID."', service_status=1, service_type='treatment', service_section='{$updatedServiceSection}' WHERE service_id='{$id}'");

                        $updateInventtory=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."inventory SET inventory_sku='{$sku}', inventory_currency='INR', inventory_price='{$salePrice}', inventory_mrp='{$mrp}', inventory_tax_status='{$taxStatus}', inventory_tax_rate='{$taxRate}', inventory_in_stock='{$in_stock}', inventory_current_stock='{$currentStock}', inventory_backorder='{$backorder}', inventory_stock_notice='{$stock_warning}', inventory_weight='{$weight}', inventory_lenght='{$lenght}', inventory_width='{$width}', inventory_height='{$height}', inventory_status=1 WHERE inventory_relation_id='{$id}'");

                        $updateMeta=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."meta SET meta_title='{$meta_title}', meta_description='{$meta_desc}', meta_canonical='{$canonical}', meta_keyword='{$meta_key}', meta_relation_id='{$link_id}', fb_img='{$facebookImage}', fb_title='{$fb_title}', fb_desc='{$fb_desc}', tw_img='{$twitterImage}', tw_title='{$tw_title}', tw_desc='{$tw_desc}' WHERE meta_relation_id='{$oldLinkID}'");
                            
                        $deleteTags=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."tag WHERE tag_relation_id='{$id}' AND tag_site_id=".SITE_ID." AND tag_type='treatment'");

                        /* Insert tags into tag table */

                        if($tags!='') {

                            $tags=explode(',', $tags);

                            for($i=0; $i<sizeof($tags); $i++) {

                                $tagsName=strtolower(preg_replace('~[^\p{M}\s\w,-]+~u', '', rtrim(ltrim($tags[$i], ' '), ' ')));
                                $updateTags=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagsName}', '{$id}', '".USER_ID."', '".SITE_ID."', 'treatment')");
                
                            }

                        } else {

                            $updateTags=TRUE;

                        }

                        if($updateservice && $addRedirect && $updateInventtory && $updateMeta && $updateTags) {

                            $status=200;
                            $msg='Treatment upload successfully';
                            $redirect="/dashboard/treatment/?type=edit&editor=true&id=".base64_encode($id);
                            mysqli_query($connection, "COMMIT");

                        } else {

                            echo $updateservice.' && '.$addRedirect.' && '.$updateInventtory.' && '.$updateMeta.' && '.$updateTags;
                            $msg='Something is wrong while updateing treatment';
                            mysqli_query($connection, "ROLLBACK");

                        }

                    } else {

                        $msg='Invalid value received';

                    }

                }

            } else {

                $msg='Please select treatment category';

            }

        } else {

            $msg='Please enter treatment name';

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
            'redirect' => ((isset($redirect)) ? $redirect : '' )
        );
    
        echo json_encode($result, TRUE);

    }