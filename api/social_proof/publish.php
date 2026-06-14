<?php

require_once LOGIN_CHECK;

function publish($connection, $data)
{

    extract($data);
    if ($title != '') {

        if (isset($category)) {

            $skipUpload = false;

            if (isset($remote_image)) {
                if ($remote_image != '') {
                    $skipUpload = true;
                }
            }

            if ($skipUpload == false) {
                require_once MODULES . 'imageUploader.php';
            } else {
                $image['featureImage'] = $remote_image;
                $featureImage = $remote_image;
                $thumbnailImage = $remote_image;
            }

            if (isset($image['featureImage'])) {

                // Extract the single upload image string to variables

                $facebookImage = (((isset($facebookImage)) ? $facebookImage : $featureImage));
                $twitterImage = (((isset($twitterImage)) ? $twitterImage : $featureImage));

                /** Category */

                $category = explode('_', $category);
                $categoryURL = $category[1];
                $category = $category[0];

                /** social proof page url */

                $url = strtolower($categoryURL . preg_replace('~[^\p{M}\w-]+~u', '-', $url)) . '/';

                /* define facebook and twitter meta desciption */

                $fb_desc = (($fb_desc == '') ? $meta_desc : $fb_desc);
                $tw_desc = (($tw_desc == '') ? $meta_desc : $tw_desc);

                if ($checkURL = mysqli_query($connection, "SELECT link_relation_id FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_site_id='" . SITE_ID . "'")) {

                    if (mysqli_num_rows($checkURL) == 0) {

                        require_once EDITOR_CONTENT_FILTER;

                        $link_status = 200;

                        $mediaRelation = explode('_', $link_url_ref);
                        if (count($mediaRelation) >= 2) {
                            $mediaRelationId = $mediaRelation[0];
                            $mediaRelationType = strtolower($mediaRelation[1]);
                        } else {
                            $mediaRelationId = 0;
                            $mediaRelationType = '';
                        }

                        $mediaPlatform = explode('.', preg_replace('/^www\./', '', parse_url($video_url, PHP_URL_HOST)))[0];

                        /** Start uploading the social proof */

                        mysqli_query($connection, "START TRANSACTION");

                        /* Insert data into link table */

                        $addNewSocialProof = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "social_proof(sp_site_id, sp_image, sp_thumbnail, sp_title, sp_description, sp_video_url, sp_ref_id, sp_ref_type, sp_category, sp_owner_id, sp_status, sp_type) VALUES(" . SITE_ID . ", '{$featureImage}', '{$thumbnailImage}', '{$title}', '{$description}', '{$video_url}', '{$mediaRelationId}', '{$mediaRelationType}', '{$category}', '" . USER_ID . "', '1', '{$mediaPlatform}')");
                        $socialProofId = mysqli_insert_id($connection);

                        $addNewLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_is_featured, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$socialProofId}', '{$link_status}', 'social_proof', '{$featurePost}', " . USER_ID . ", '" . SITE_ID . "', NOW() ,NOW())");
                        $linkID = mysqli_insert_id($connection);

                        $addNewMeta = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "meta(meta_relation_id, meta_site_id,  meta_title, meta_type, meta_description, meta_keyword, meta_canonical, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$linkID}', " . SITE_ID . ", '{$meta_title}', 'social_proof', '{$meta_desc}', '{$meta_key}', '{$canonical}', '{$robots}', '{$facebookImage}', '{$fb_title}', '{$fb_desc}', '{$twitterImage}', '{$tw_title}', '{$tw_desc}')");

                        /* Insert tags into tag table */

                        if ($tags != '') {

                            $tags = explode(',', $tags);

                            for ($i = 0; $i < sizeof($tags); $i++) {

                                $tagsName = strtolower(preg_replace('~[^\p{M}\s\w,-]+~u', '', rtrim(ltrim($tags[$i], ' '), ' ')));
                                $addNewTags = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagsName}', '{$socialProofId}', '" . USER_ID . "', '" . SITE_ID . "', 'social_proof')");
                            }
                        } else {

                            $addNewTags = TRUE;
                        }

                        if ($addNewSocialProof && $addNewLink && $addNewMeta && $addNewTags) {

                            $status = 200;
                            $msg = 'Social Proof add successfully';
                            $redirect = '/dashboard/social_proof/?type=edit&editor=true&id=' . base64_encode($socialProofId);
                            mysqli_query($connection, "COMMIT");
                        } else {

                            $msg = 'Something is wrong while adding new social proof';

                            mysqli_query($connection, "ROLLBACK");
                        }
                    } else {

                        $msg = 'Please change url';
                    }
                } else {

                    $msg = 'Technical errro';
                }
            } else {

                $msg = 'Please select social proof feature image.';
            }
        } else {

            $msg = 'Please select category';
        }
    } else {

        $msg = 'Please enter social proof name';
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
        'redirect' => ((isset($redirect)) ? $redirect : '')
    );

    echo json_encode($result, TRUE);
}
