<?php

require_once LOGIN_CHECK;
require_once FUNCTIONS . 'duplicate.php';

function publish($connection, $data)
{
    extract($data);

    if (isset($title) && $title != '') {

        $sku = ((isset($sku)) ? (string)$sku : '');
        $salePrice = iswNormalizeDecimal($salePrice ?? 0);
        $mrp = iswNormalizeDecimal($mrp ?? 0);
        $taxStatus = iswNormalizeInt($taxStatus ?? 0);
        $taxRate = iswNormalizeInt($taxRate ?? 0);
        $stock_warning = iswNormalizeInt($stock_warning ?? 0);
        $weight = ((isset($weight)) ? (string)$weight : '');
        $height = ((isset($height)) ? (string)$height : '');
        $width = ((isset($width)) ? (string)$width : '');
        $lenght = ((isset($lenght)) ? (string)$lenght : '');
        $highlight = ((isset($highlight)) ? (string)$highlight : '');
        $description = ((isset($description)) ? $description : '');
        $featurePost = iswNormalizeFlag($featurePost ?? 0);

        $image = array();
        $thumbnailImage = '';
        $images = array();
        $gallery = '';
        require_once MODULES . 'imageUploader.php';

        $uploadedImgs = (isset($image) && is_array($image)) ? $image : array();
        $resolvedImages = iswResolveDuplicateImages(
            $connection,
            $data,
            $uploadedImgs,
            isset($thumbnailImage) ? (string)$thumbnailImage : ''
        );
        $featureImage = $resolvedImages['featureImage'];
        $thumbnailImage = $resolvedImages['thumbnailImage'];
        $facebookImage = $resolvedImages['facebookImage'];
        $twitterImage = $resolvedImages['twitterImage'];

        if ($featureImage != '') {

            $postType = ((isset($postType) && $postType != '') ? $postType : ($type ?? 'service'));
            $currentStock = iswNormalizeInt($currentStock ?? 0);

            if (isset($_FILES['gallery'])) {

                require_once MODULES . 'galleryUploader.php';
            }

            $gallery = iswMergeDuplicateGallery(
                $connection,
                'service',
                $_POST,
                (isset($images) && is_array($images)) ? $images : array(),
                SITE_ID,
                false
            );
            $gallery = mysqli_real_escape_string($connection, $gallery);

            $treatmentImage_1 = iswResolveUploadedImage($connection, $data, 'treatmentImage_1', 'oldTreatmentImage_1', '', $uploadedImgs);
            $treatmentImage_2 = iswResolveUploadedImage($connection, $data, 'treatmentImage_2', 'oldTreatmentImage_2', '', $uploadedImgs);
            $treatmentImage_3 = iswResolveUploadedImage($connection, $data, 'treatmentImage_3', 'oldTreatmentImage_3', '', $uploadedImgs);
            $whyChooseUs = iswResolveUploadedImage($connection, $data, 'whyChooseUs', 'oldWhyChooseUs', '', $uploadedImgs);
            $stepIcon_1 = iswResolveUploadedImage($connection, $data, 'stepIcon_1', 'oldStepIcon_1', '', $uploadedImgs);
            $stepIcon_2 = iswResolveUploadedImage($connection, $data, 'stepIcon_2', 'oldStepIcon_2', '', $uploadedImgs);
            $stepIcon_3 = iswResolveUploadedImage($connection, $data, 'stepIcon_3', 'oldStepIcon_3', '', $uploadedImgs);
            $before = iswResolveUploadedImage($connection, $data, 'before', 'oldBeforeImage', '', $uploadedImgs);
            $after = iswResolveUploadedImage($connection, $data, 'after', 'oldAfterImage', '', $uploadedImgs);
            $watchVideoImage = iswResolveUploadedImage($connection, $data, 'watchVideoImage', 'oldWatchVideoImage', '', $uploadedImgs);
            $commitmentImage = iswResolveUploadedImage($connection, $data, 'commitmentImage', 'oldCommitmentImage', '', $uploadedImgs);

            $service_section = json_encode(array(
                'quote' => array(
                    'quote' => ((isset($quote)) ? $quote : '')
                ),
                'treatment_steps' => array(
                    'header' => ((isset($treatment_header)) ? $treatment_header : ''),
                    'description' => ((isset($treatment_description)) ? $treatment_description : ''),
                    'steps' => array(
                        array(
                            'image' => $treatmentImage_1,
                            'header' => ((isset($treatmentImageHeader_1)) ? $treatmentImageHeader_1 : ''),
                            'description' => ((isset($treatmentImageDescription_1)) ? $treatmentImageDescription_1 : '')
                        ),
                        array(
                            'image' => $treatmentImage_2,
                            'header' => ((isset($treatmentImageHeader_2)) ? $treatmentImageHeader_2 : ''),
                            'description' => ((isset($treatmentImageDescription_2)) ? $treatmentImageDescription_2 : '')
                        ),
                        array(
                            'image' => $treatmentImage_3,
                            'header' => ((isset($treatmentImageHeader_3)) ? $treatmentImageHeader_3 : ''),
                            'description' => ((isset($treatmentImageDescription_3)) ? $treatmentImageDescription_3 : '')
                        )
                    )
                ),
                'why_choose_us' => array(
                    'image' => $whyChooseUs,
                    'header' => ((isset($whyChooseTitle)) ? $whyChooseTitle : ''),
                    'description' => ((isset($whyChooseDescription)) ? $whyChooseDescription : ''),
                    'steps' => array(
                        array(
                            'icon' => $stepIcon_1,
                            'description' => ((isset($stepHeader_1)) ? $stepHeader_1 : '')
                        ),
                        array(
                            'icon' => $stepIcon_2,
                            'description' => ((isset($stepHeader_2)) ? $stepHeader_2 : '')
                        ),
                        array(
                            'icon' => $stepIcon_3,
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
                        'before' => $before,
                        'after' => $after
                    )
                ),
                'watch_video' => array(
                    'image' => $watchVideoImage,
                    'header' => ((isset($watchVideoHeader)) ? $watchVideoHeader : ''),
                    'description' => ((isset($watchVideoDescription)) ? $watchVideoDescription : ''),
                    'videoLink' => ((isset($watchVideoLink)) ? $watchVideoLink : '')
                ),
                'our_commitment' => array(
                    'image' => $commitmentImage,
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
            ));

            $service_section = mysqli_real_escape_string($connection, base64_encode(str_replace('\r\n', '<br />', (string)$service_section)));

            /** Category */

            if (isset($category)) {

                $category = explode('_', $category);
                $categoryURL = $category[1];
                $category = $category[0];
            } else {

                $category = 0;
                $categoryURL = '/';
            }

            /** service page url */

            $slugClean = trim((string)preg_replace('~[^\p{M}\w-]+~u', '-', trim((string)($url ?? ''), '/')), '-');
            if ($slugClean === '') {
                $slugClean = trim((string)preg_replace('~[^\p{M}\w-]+~u', '-', trim((string)$title)), '-');
            }
            $url = strtolower('/service' . $categoryURL . $slugClean) . '/';

            /* define facebook and twitter meta desciption */

            $meta_title = isset($meta_title) ? $meta_title : '';
            $meta_desc = isset($meta_desc) ? $meta_desc : '';
            $meta_key = isset($meta_key) ? $meta_key : '';
            $canonical = isset($canonical) ? $canonical : '';
            $robots = isset($robots) ? $robots : 'index, follow';
            $fb_title = isset($fb_title) ? $fb_title : '';
            $fb_desc = ((!isset($fb_desc) || $fb_desc == '') ? $meta_desc : $fb_desc);
            $tw_title = isset($tw_title) ? $tw_title : '';
            $tw_desc = ((!isset($tw_desc) || $tw_desc == '') ? $meta_desc : $tw_desc);

            if ($checkURL = mysqli_query($connection, "SELECT link_relation_id FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_site_id='" . SITE_ID . "'")) {

                if (mysqli_num_rows($checkURL) == 0) {

                    require_once EDITOR_CONTENT_FILTER;

                    /** Start uploading the service */

                    mysqli_query($connection, "START TRANSACTION");

                    /* Insert data into link table */

                    $addNewservice = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "service(service_site_id, service_image, service_thumbnail, service_gallery, service_title, service_description, service_highlight, service_category, service_owner_id, service_status, service_type, service_section) VALUES(" . SITE_ID . ", '{$featureImage}', '{$thumbnailImage}', '{$gallery}', '{$title}', '{$description}', '{$highlight}', '{$category}', " . USER_ID . ",'1', 'service', '{$service_section}')");
                    $serviceID = mysqli_insert_id($connection);

                    $addNewLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_is_featured, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$serviceID}', '200', 'service', '{$featurePost}', " . USER_ID . ", '" . SITE_ID . "', NOW() ,NOW())");
                    $linkID = mysqli_insert_id($connection);

                    $addNewInventory = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "inventory(inventory_relation_id, inventory_site_id, inventory_sku, inventory_currency, inventory_price, inventory_mrp, inventory_weight, inventory_lenght, inventory_width, inventory_height, inventory_type, inventory_status, inventory_update) VALUES ('{$serviceID}', '" . SITE_ID . "', '{$sku}', 'INR', '{$salePrice}', '{$mrp}', '{$weight}', '{$lenght}', '{$width}', '{$height}', 'service', '1', current_timestamp())");

                    $addNewMeta = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "meta(meta_relation_id, meta_site_id,  meta_title, meta_type, meta_description, meta_keyword, meta_canonical, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$linkID}', " . SITE_ID . ", '{$meta_title}', 'service', '{$meta_desc}', '{$meta_key}', '{$canonical}', '{$robots}', '{$facebookImage}', '{$fb_title}', '{$fb_desc}', '{$twitterImage}', '{$tw_title}', '{$tw_desc}')");

                    /* Insert tags into tag table */

                    $addNewTags = TRUE;
                    $tagsStr = isset($tags) ? trim((string)$tags) : '';
                    if ($tagsStr != '') {

                        $tagsArr = explode(',', $tagsStr);

                        for ($i = 0; $i < sizeof($tagsArr); $i++) {

                            $tagsName = strtolower(preg_replace('~[^\p{M}\s\w,-]+~u', '', trim($tagsArr[$i])));
                            if ($tagsName !== '') {
                                $tagInserted = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagsName}', '{$serviceID}', '" . USER_ID . "', '" . SITE_ID . "', '{$postType}')");
                                if (!$tagInserted) {
                                    $addNewTags = FALSE;
                                }
                            }
                        }
                    }

                    if ($addNewservice && $addNewLink && $addNewInventory && $addNewMeta && $addNewTags) {

                        $status = 200;
                        $msg = 'Service add successfully';
                        $redirect = '/dashboard/service/?type=edit&editor=true&id=' . base64_encode($serviceID);
                        mysqli_query($connection, "COMMIT");
                    } else {

                        $msg = 'Something is wrong while adding new service';
                        mysqli_query($connection, "ROLLBACK");
                    }
                } else {

                    $msg = 'Please change the service url, because this url is already exists.';
                }
            } else {

                $msg = 'Technical error';
            }
        } else {

            $msg = 'Please select service feature image.';
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
