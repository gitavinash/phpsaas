<?php

function update($connection, $data)
{

    extract($data);

    if ($title != '') {

        if (isset($category)) {

            if ($checkservice = mysqli_query($connection, "SELECT link_id, meta_relation_id as oldLinkID, service_section FROM " . TABLE_PREFIX . "service INNER JOIN " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "meta ON meta_relation_id=link_id WHERE link_url='{$oldURL}' AND meta_type='service' AND service_id='{$id}'")) {

                if (mysqli_num_rows($checkservice) != 0) {

                    extract(mysqli_fetch_assoc($checkservice));

                    $serviceSection = json_decode($service_section, true);

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
                        'treatment_steps' => array(
                            'header' => mysqli_real_escape_string($connection, htmlspecialchars($treatment_header)),
                            'description' => mysqli_real_escape_string($connection, htmlspecialchars($treatment_description)),
                            'steps' => array(
                                array(
                                    'image' => (($treatmentImage_1 != '') ? $treatmentImage_1 : $serviceSection['treatment_steps']['steps'][0]['image']),
                                    'header' => mysqli_real_escape_string($connection, htmlspecialchars($treatmentImageHeader_1)),
                                    'description' => mysqli_real_escape_string($connection, htmlspecialchars($treatmentImageDescription_1))
                                ),
                                array(
                                    'image' => (($treatmentImage_2 != '') ? $treatmentImage_2 : $serviceSection['treatment_steps']['steps'][1]['image']),
                                    'header' => mysqli_real_escape_string($connection, htmlspecialchars($treatmentImageHeader_2)),
                                    'description' => mysqli_real_escape_string($connection, htmlspecialchars($treatmentImageDescription_2))
                                ),
                                array(
                                    'image' => (($treatmentImage_3 != '') ? $treatmentImage_3 : $serviceSection['treatment_steps']['steps'][2]['image']),
                                    'header' => mysqli_real_escape_string($connection, htmlspecialchars($treatmentImageHeader_3)),
                                    'description' => mysqli_real_escape_string($connection, htmlspecialchars($treatmentImageDescription_3))
                                )
                            )
                        ),
                        'why_choose_us' => array(
                            'image' => (($whyChooseUs != '') ? $whyChooseUs : $serviceSection['why_choose_us']['image']),
                            'header' => mysqli_real_escape_string($connection, htmlspecialchars($whyChooseTitle)),
                            'description' => mysqli_real_escape_string($connection, htmlspecialchars($whyChooseDescription)),
                            'steps' => array(
                                array(
                                    'icon' => (($stepIcon_1 != '') ? $stepIcon_1 : $serviceSection['why_choose_us']['steps'][0]['icon']),
                                    'description' => mysqli_real_escape_string($connection, htmlspecialchars($stepHeader_1))
                                ),
                                array(
                                    'icon' => (($stepIcon_2 != '') ? $stepIcon_2 : $serviceSection['why_choose_us']['steps'][1]['icon']),
                                    'description' => mysqli_real_escape_string($connection, htmlspecialchars($stepHeader_2))
                                ),
                                array(
                                    'icon' => (($stepIcon_3 != '') ? $stepIcon_3 : $serviceSection['why_choose_us']['steps'][2]['icon']),
                                    'description' => mysqli_real_escape_string(
                                        $connection,
                                        htmlspecialchars($stepHeader_30)
                                    ),
                                )
                            ),
                        ),
                        'before_after' => array(
                            'header' => mysqli_real_escape_string($connection, htmlspecialchars($berforeAfterHeader)),
                            'subHeader' => mysqli_real_escape_string($connection, htmlspecialchars($berforeAfterSubHeader)),
                            'description' => mysqli_real_escape_string($connection, htmlspecialchars($berforeAfterDescription)),
                            'videoLink' => mysqli_real_escape_string($connection, htmlspecialchars($berforeAfterVideLink)),
                            'images' => array(
                                'before' => mysqli_real_escape_string($connection, (($before != '') ? $before : $serviceSection['before_after']['images']['before'])),
                                'after' => mysqli_real_escape_string($connection, (($after != '') ? $after : $serviceSection['before_after']['images']['after']))
                            )
                        ),
                        'watch_video' => array(
                            'image' => (($watchVideoImage != '') ? $watchVideoImage : $serviceSection['watch_video']['image']),
                            'header' => mysqli_real_escape_string($connection, htmlspecialchars($watchVideoHeader)),
                            'description' => mysqli_real_escape_string($connection, htmlspecialchars($watchVideoDescription)),
                            'subDescription' => mysqli_real_escape_string($connection, htmlspecialchars($watchVideoSubDescription)),
                            'videoLink' => mysqli_real_escape_string($connection, htmlspecialchars($watchVideoLink))
                        ),
                        'our_commitment' => array(
                            'image' => mysqli_real_escape_string($connection, (($commitmentImage != '') ? $commitmentImage : $serviceSection['our_commitment']['image'])),
                            'header' => mysqli_real_escape_string($connection, htmlspecialchars($commitmentHeader)),
                            'subHeader' => mysqli_real_escape_string($connection, htmlspecialchars($commitmentSubHeader)),
                            'descripton' => mysqli_real_escape_string($connection, htmlspecialchars($commitmentDescription))
                        ),
                        'faq' => array(
                            0 => array(
                                'question' => mysqli_real_escape_string($connection, htmlspecialchars($faqQuestion1)),
                                'answer' => mysqli_real_escape_string($connection, htmlspecialchars($faqAnswer1))
                            ),
                            1 => array(
                                'question' => mysqli_real_escape_string($connection, htmlspecialchars($faqQuestion2)),
                                'answer' => mysqli_real_escape_string($connection, htmlspecialchars($faqAnswer2))
                            ),
                            2 => array(
                                'question' => mysqli_real_escape_string($connection, htmlspecialchars($faqQuestion3)),
                                'answer' => mysqli_real_escape_string($connection, htmlspecialchars($faqAnswer3))
                            ),
                            3 => array(
                                'question' => mysqli_real_escape_string($connection, htmlspecialchars($faqQuestion4)),
                                'answer' => mysqli_real_escape_string($connection, htmlspecialchars($faqAnswer4))
                            ),
                            4 => array(
                                'question' => mysqli_real_escape_string($connection, htmlspecialchars($faqQuestion5)),
                                'answer' => mysqli_real_escape_string($connection, htmlspecialchars($faqAnswer5))
                            )
                        )
                    ), TRUE);

                    if (isset($_FILES['gallery'])) {

                        require_once MODULES . 'galleryUploader.php';
                    } else {

                        $gallery = $oldGallery;
                    }

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

                        if ($postStatus == 0) {

                            $updateLink = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "link SET link_status=200, link_published=NOW(), link_modified=NOW(), link_is_featured='{$featurePost}' WHERE link_relation_id='{$id}' AND link_type='{$postType}' AND link_url='{$oldURL}' AND link_site_id='" . SITE_ID . "'");
                        } else {

                            $updateLink = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "link SET link_status=200, link_is_featured='{$featurePost}' WHERE link_relation_id='{$id}' AND link_type='{$postType}' AND link_url='{$oldURL}' AND link_site_id='" . SITE_ID . "'");
                        }
                    }

                    $updateservice = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "service SET service_image='{$featureImage}', service_thumbnail='{$thumbnailImage}', service_gallery='{$gallery}', service_title='{$title}', service_description='{$description}', service_highlight='{$highlight}', service_category='{$category}', service_owner_id='" . USER_ID . "', service_status=1, service_type='{$postType}', service_section='{$updatedServiceSection}' WHERE service_id='{$id}'");

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