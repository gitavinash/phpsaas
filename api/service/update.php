<?php

function update($connection, $data)
{

    extract($data);

    if ($title != '') {

        if (isset($category)) {

            if ($checkservice = mysqli_query($connection, "SELECT link_id, meta_relation_id as oldLinkID, service_section, service_status, service_gallery FROM " . TABLE_PREFIX . "service INNER JOIN " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "meta ON meta_relation_id=link_id WHERE link_url='{$oldURL}' AND meta_type='service' AND service_id='{$id}'")) {

                if (mysqli_num_rows($checkservice) != 0) {

                    $sku = ((isset($sku)) ? $sku : '');
                    $salePrice = ((isset($salePrice)) ? $salePrice : '');
                    $mrp = ((isset($mrp)) ? $mrp : '');
                    $taxStatus = ((isset($taxStatus)) ? $taxStatus : '');
                    $taxRate = ((isset($taxRate)) ? $taxRate : '');
                    $stock_warning = ((isset($stock_warning)) ? $stock_warning : '');
                    $weight = ((isset($weight)) ? $weight : '');
                    $height = ((isset($height)) ? $height : '');
                    $width = ((isset($width)) ? $width : '');
                    $lenght = ((isset($lenght)) ? $lenght : '');
                    $highlight = ((isset($highlight)) ? $highlight : '');

                    extract(mysqli_fetch_assoc($checkservice));

                    $serviceSection = json_decode(base64_decode(str_replace('\r\n', '<br />', $service_section)), true);

                    if ($serviceSection == '') {
                        $serviceSection = json_decode(str_replace('\r\n', '<br />', $service_section), TRUE);
                    }

                    require_once MODULES . 'imageUploader.php';

                    if (isset($image)) {

                        // Extract the single upload image string to variables

                        $featureImage = (((isset($featureImage)) ? $featureImage : $oldFeatureImage));
                        $thumbnailImage = (((isset($thumbnailImage)) ? $featureImage : $featureImage));
                        $facebookImage = (((isset($facebookImage)) ? $facebookImage : $oldFacebookImage));
                        $twitterImage = (((isset($twitterImage)) ? $twitterImage : $oldTwitterImage));
                    } else {

                        $featureImage = $oldFeatureImage;
                        $thumbnailImage = (((isset($thumbnailImage)) ? $featureImage : $oldFeatureImage));
                        $facebookImage = (((isset($facebookImage)) ? $facebookImage : $oldFacebookImage));
                        $twitterImage = (((isset($twitterImage)) ? $twitterImage : $oldTwitterImage));
                    }

                    $featurePost = ((isset($featurePost)) ? 1 : 0);
                    $in_stock = ((isset($in_stock)) ? 1 : 0);
                    $backorder = ((isset($backorder)) ? 1 : 0);
                    $postType = ((isset($postType)) ? $postType : $type);
                    $currentStock = ((isset($currentStock)) ? $currentStock : 0);

                    $updatedServiceSection = json_encode(array(
                        'quote' => array(
                            'quote' => ((isset($quote)) ? $quote : '')
                        ),
                        'treatment_steps' => array(
                            'header' => ((isset($treatment_header)) ? $treatment_header : ''),
                            'description' => ((isset($treatment_description)) ? $treatment_description : ''),
                            'steps' => array(
                                array(
                                    'image' => ((isset($treatmentImage_1)) ? $treatmentImage_1 : ((isset($serviceSection['treatment_steps']['steps'][0]['image'])) ? $serviceSection['treatment_steps']['steps'][0]['image'] : '')),
                                    'header' => ((isset($treatmentImageHeader_1)) ? $treatmentImageHeader_1 : ''),
                                    'description' => ((isset($treatmentImageDescription_1)) ? $treatmentImageDescription_1 : '')
                                ),
                                array(
                                    'image' => ((isset($treatmentImage_2)) ? $treatmentImage_2 : ((isset($serviceSection['treatment_steps']['steps'][1]['image'])) ? $serviceSection['treatment_steps']['steps'][1]['image'] : '')),
                                    'header' => ((isset($treatmentImageHeader_2)) ? $treatmentImageHeader_2 : ''),
                                    'description' => ((isset($treatmentImageDescription_2)) ? $treatmentImageDescription_2 : '')
                                ),
                                array(
                                    'image' => ((isset($treatmentImage_3)) ? $treatmentImage_3 : ((isset($serviceSection['treatment_steps']['steps'][2]['image'])) ? $serviceSection['treatment_steps']['steps'][2]['image'] : '')),
                                    'header' => ((isset($treatmentImageHeader_3)) ? $treatmentImageHeader_3 : ''),
                                    'description' => ((isset($treatmentImageDescription_3)) ? $treatmentImageDescription_3 : '')
                                )
                            )
                        ),
                        'why_choose_us' => array(
                            'image' => ((isset($whyChooseUs)) ? $whyChooseUs : ((isset($serviceSection['why_choose_us']['image'])) ? $serviceSection['why_choose_us']['image'] : '')),
                            'header' => ((isset($whyChooseTitle)) ? $whyChooseTitle : ''),
                            'description' => ((isset($whyChooseDescription)) ? $whyChooseDescription : ''),
                            'steps' => array(
                                array(
                                    'icon' => ((isset($stepIcon_1)) ? $stepIcon_1 : ((isset($serviceSection['why_choose_us']['steps'][0]['icon'])) ? $serviceSection['why_choose_us']['steps'][0]['icon'] : '')),
                                    'description' => ((isset($stepHeader_1)) ? $stepHeader_1 : '')
                                ),
                                array(
                                    'icon' => ((isset($stepIcon_2)) ? $stepIcon_2 : ((isset($serviceSection['why_choose_us']['steps'][1]['icon'])) ? $serviceSection['why_choose_us']['steps'][1]['icon'] : '')),
                                    'description' => ((isset($stepHeader_2)) ? $stepHeader_2 : '')
                                ),
                                array(
                                    'icon' => ((isset($stepIcon_3)) ? $stepIcon_3 : ((isset($serviceSection['why_choose_us']['steps'][2]['icon'])) ? $serviceSection['why_choose_us']['steps'][2]['icon'] : '')),
                                    'description' => ((isset($stepHeader_3)) ? $stepHeader_3 : '')
                                ),
                            )
                        ),
                        'before_after' => array(
                            'header' => ((isset($berforeAfterHeader)) ? $berforeAfterHeader : ''),
                            'subHeader' => ((isset($berforeAfterSubHeader)) ? $berforeAfterSubHeader : ''),
                            'description' => ((isset($berforeAfterDescription)) ? $berforeAfterDescription : ''),
                            'videoLink' => ((isset($berforeAfterVideLink)) ? $berforeAfterVideLink : ''),
                            'images' => array(
                                'before' => ((isset($before)) ? $before : ((isset($serviceSection['before_after']['images']['before'])) ? $serviceSection['before_after']['images']['before'] : '')),
                                'after' => ((isset($after)) ? $after : ((isset($serviceSection['before_after']['images']['after'])) ? $serviceSection['before_after']['images']['after'] : ''))
                            )
                        ),
                        'watch_video' => array(
                            'image' => ((isset($watchVideoImage)) ? $watchVideoImage : ((isset($serviceSection['watch_video']['image'])) ? $serviceSection['watch_video']['image'] : '')),
                            'header' => ((isset($watchVideoHeader)) ? $watchVideoHeader : ''),
                            'description' => ((isset($watchVideoDescription)) ? $watchVideoDescription : ''),
                            'videoLink' => ((isset($watchVideoLink)) ? $watchVideoLink : '')
                        ),
                        'our_commitment' => array(
                            'image' => ((isset($commitmentImage)) ? $commitmentImage : ((isset($serviceSection['our_commitment']['image'])) ? $serviceSection['our_commitment']['image'] : '')),
                            'header' => ((isset($commitmentHeader)) ? $commitmentHeader : ''),
                            'subHeader' => ((isset($commitmentSubHeader)) ? $commitmentSubHeader : ''),
                            'descripton' => ((isset($commitmentDescription)) ? $commitmentDescription : '')
                        ),
                        'faq' => array(
                            0 => array(
                                'question' => ((isset($faqQuestion1)) ? $faqQuestion1 : ''),
                                'answer' => ((isset($faqAnswer1)) ? $faqAnswer1 : '')
                            ),
                            1 => array(
                                'question' => ((isset($faqQuestion2)) ? $faqQuestion2 : ''),
                                'answer' => ((isset($faqAnswer2)) ? $faqAnswer2 : '')
                            ),
                            2 => array(
                                'question' => ((isset($faqQuestion3)) ? $faqQuestion3 : ''),
                                'answer' => ((isset($faqAnswer3)) ? $faqAnswer3 : '')
                            ),
                            3 => array(
                                'question' => ((isset($faqQuestion4)) ? $faqQuestion4 : ''),
                                'answer' => ((isset($faqAnswer4)) ? $faqAnswer4 : '')
                            ),
                            4 => array(
                                'question' => ((isset($faqQuestion5)) ? $faqQuestion5 : ''),
                                'answer' => ((isset($faqAnswer5)) ? $faqAnswer5 : '')
                            )
                        )
                    ), true);

                    $updatedServiceSection = base64_encode(str_replace('\r\n', '<br />', $updatedServiceSection));

                    if (isset($_FILES['gallery'])) {

                        require_once MODULES . 'galleryUploader.php';

                        if ($gallery != '') {

                            $isGalleryUpdate = TRUE;
                        }
                    }

                    $oldGallery = json_decode($service_gallery, TRUE);

                    // Match old gallery db from from value
                    $newServiceGallery = array();

                    if (isset($_POST['oldGallery'])) {

                        for ($i = 0; $i < sizeof($_POST['oldGallery']); $i++) {

                            $j = $_POST['oldGallery'][$i] - 1;

                            $newServiceGallery[] = $oldGallery[$j];
                        }
                    }

                    /** Update old gallery */

                    if (isset($isGalleryUpdate)) {

                        $newServiceGallery = array_merge($newServiceGallery, $images);
                    }

                    $newServiceGallery = json_encode($newServiceGallery, TRUE);


                    /** Category */

                    $category = explode('_', $category);
                    $categoryURL = $category[1];
                    $category = $category[0];

                    /** service page url */

                    $url = strtolower('/service' . $categoryURL . preg_replace('~[^\p{M}\w-]+~u', '-', $url)) . '/';

                    /* define facebook and twitter meta desciption */

                    $fb_desc = (($fb_desc == '') ? $meta_desc : $fb_desc);
                    $tw_desc = (($tw_desc == '') ? $meta_desc : $tw_desc);

                    /** Start updating in services */

                    mysqli_query($connection, "START TRANSACTION");

                    if ($oldURL != $url) {

                        if ($checkURL = mysqli_query($connection, "SELECT link_url FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_type='service' AND link_site_id='" . SITE_ID . "'")) {

                            if (mysqli_num_rows($checkURL) == 0) {

                                /** Add 301 redirect */

                                $addRedirect = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "link SET link_status=301 WHERE link_url='{$oldURL}' AND link_type='service' AND link_site_id='" . SITE_ID . "'");

                                /** Add new active url of the post */

                                $updateLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published, link_is_featured) VALUES('{$url}', '{$id}', 200, '{$postType}', '" . USER_ID . "', '" . SITE_ID . "', NOW(), NOW(), '{$featurePost}')");
                                $link_id = mysqli_insert_id($connection);
                            } else {

                                $addRedirect = FALSE;
                                $status = 'warning';
                                $msg = "Please change post url. Because its already in used";
                            }
                        }
                    } else {

                        $addRedirect = TRUE;

                        if ($serviceStatus == 0) {

                            $updateLink = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "link SET link_status=200, link_published=NOW(), link_modified=NOW(), link_is_featured='{$featurePost}' WHERE link_relation_id='{$id}' AND link_type='{$postType}' AND link_url='{$oldURL}' AND link_site_id='" . SITE_ID . "'");
                        } else {

                            $updateLink = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "link SET link_status=200, link_is_featured='{$featurePost}' WHERE link_relation_id='{$id}' AND link_type='{$postType}' AND link_url='{$oldURL}' AND link_site_id='" . SITE_ID . "'");
                        }
                    }

                    $updateservice = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "service SET service_image='{$featureImage}', service_thumbnail='{$thumbnailImage}', service_gallery='{$newServiceGallery}', service_title='{$title}', service_description='{$description}', service_highlight='{$highlight}', service_category='{$category}', service_owner_id='" . USER_ID . "', service_status=1, service_type='{$postType}', service_section='{$updatedServiceSection}' WHERE service_id='{$id}'");

                    $updateInventtory = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "inventory SET inventory_sku='{$sku}', inventory_currency='INR', inventory_price='{$salePrice}', inventory_mrp='{$mrp}', inventory_tax_status='{$taxStatus}', inventory_tax_rate='{$taxRate}', inventory_in_stock='{$in_stock}', inventory_current_stock='{$currentStock}', inventory_backorder='{$backorder}', inventory_stock_notice='{$stock_warning}', inventory_weight='{$weight}', inventory_lenght='{$lenght}', inventory_width='{$width}', inventory_height='{$height}', inventory_status=1 WHERE inventory_relation_id='{$id}'");

                    $updateMeta = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "meta SET meta_title='{$meta_title}', meta_description='{$meta_desc}', meta_canonical='{$canonical}', meta_keyword='{$meta_key}', meta_relation_id='{$link_id}', fb_img='{$facebookImage}', fb_title='{$fb_title}', fb_desc='{$fb_desc}', tw_img='{$twitterImage}', tw_title='{$tw_title}', tw_desc='{$tw_desc}' WHERE meta_relation_id='{$oldLinkID}'");

                    $deleteTags = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "tag WHERE tag_relation_id='{$id}' AND tag_site_id=" . SITE_ID . " AND tag_type='{$postType}'");

                    /* Insert tags into tag table */

                    if ($tags != '') {

                        $tags = explode(',', $tags);

                        for ($i = 0; $i < sizeof($tags); $i++) {

                            $tagsName = strtolower(preg_replace('~[^\p{M}\s\w,-]+~u', '', rtrim(ltrim($tags[$i], ' '), ' ')));
                            $updateTags = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagsName}', '{$id}', '" . USER_ID . "', '" . SITE_ID . "', '{$postType}')");
                        }
                    } else {

                        $updateTags = TRUE;
                    }

                    if ($updateservice && $addRedirect && $updateInventtory && $updateMeta && $updateTags) {

                        $status = 200;
                        $msg = 'Service upload successfully';
                        $redirect = "/dashboard/service/?type=edit&editor=true&id=" . base64_encode($id);
                        mysqli_query($connection, "COMMIT");
                    } else {

                        echo $updateservice . ' && ' . $addRedirect . ' && ' . $updateInventtory . ' && ' . $updateMeta . ' && ' . $updateTags;
                        $msg = 'Something is wrong while updateing service';
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

        $msg = 'Please enter service name';
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
        'redirect' => ((isset($redirect)) ? $redirect : '')
    );

    echo json_encode($result, TRUE);
}
