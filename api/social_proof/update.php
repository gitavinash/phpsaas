<?php

/* Update Social proof */

require_once LOGIN_CHECK;

function update($connection, $data)
{

    extract($data);

    if ($title != '') {

        if (isset($category)) {

            if ($checkSocialProof = mysqli_query($connection, "SELECT link_id FROM " . TABLE_PREFIX . "social_proof INNER JOIN " . TABLE_PREFIX . "link WHERE link_url='{$oldURL}' AND sp_id='{$id}' AND link_site_id=" . SITE_ID)) {

                if (mysqli_num_rows($checkSocialProof) != 0) {

                    extract(mysqli_fetch_assoc($checkSocialProof));

                    require_once MODULES . 'imageUploader.php';

                    if (isset($image)) {

                        /** when image is upload by user */
                        $featureImage = (((isset($featureImage)) ? $featureImage : $oldFeatureImage));
                        $thumbnailImage = (((isset($thumbnailImage)) ? $featureImage : $oldThumbnailImage));
                        $facebookImage = (((isset($facebookImage)) ? $facebookImage : $oldFacebookImage));
                        $twitterImage = (((isset($twitterImage)) ? $twitterImage : $oldTwitterImage));
                    } else {

                        /** When image is not upload by user */

                        $featureImage = $oldFeatureImage;
                        $thumbnailImage = $featureImage;
                        $facebookImage = (($oldFacebookImage == '') ? $oldFeatureImage : $oldFacebookImage);
                        $twitterImage = (($oldTwitterImage == '') ? $oldFeatureImage : $oldTwitterImage);
                    }

                    /** Fetch category url */

                    $category = explode('_', $category);
                    $categoryURL = str_replace('/category/', '', $category[1]);
                    $category = $category[0];

                    $url = strtolower($categoryURL . preg_replace('~[^\p{M}\w-]+~u', '-', $url)) . '/';
                    $linkID = $link_id;

                    $mediaRelation = explode('_', $link_url_ref);
                    if (count($mediaRelation) >= 2) {
                        $mediaRelationId = $mediaRelation[0];
                        $mediaRelationType = strtolower($mediaRelation[1]);
                    } else {
                        $mediaRelationId = 0;
                        $mediaRelationType = '';
                    }

                    mysqli_query($connection, "START TRANSACTION");

                    if ($oldURL != $url) {

                        if ($checkURL = mysqli_query($connection, "SELECT link_url FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_type='social_proof' AND link_site_id='" . SITE_ID . "'")) {

                            if (mysqli_num_rows($checkURL) == 0) {

                                /** Add 301 redirect */

                                $addRedirect = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "link SET link_status=301 WHERE link_url='{$oldURL}' AND link_site_id='" . SITE_ID . "'");

                                /** Add new active url of the social proof */

                                $updateLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published, link_is_featured) VALUES('{$url}', '{$id}', 200, '{$postType}', '" . USER_ID . "', '" . SITE_ID . "', NOW(), NOW(), '{$featurePost}')");
                                $linkID = mysqli_insert_id($connection);
                            } else {

                                $addRedirect = FALSE;
                                $status = 'warning';
                                $msg = "Please change social prrof url. Because its already in used";
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

                    /** Update social proof table */

                    $updateSocialProof = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "social_proof SET sp_image='{$featureImage}', sp_thumbnail='{$thumbnailImage}', sp_title='{$title}', sp_description='{$description}', sp_category='{$category}', sp_status='1', sp_ref_id='{$mediaRelationId}', sp_ref_type='{$mediaRelationType}' WHERE sp_id='{$id}'");

                    /** Update meta table */

                    $updateMeta = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "meta SET meta_title='{$meta_title}', meta_description='{$meta_desc}', meta_canonical='{$canonical}', meta_keyword='{$meta_key}', meta_relation_id='{$linkID}', fb_img='{$facebookImage}', fb_title='{$fb_title}', fb_desc='{$fb_desc}', tw_img='{$twitterImage}', tw_title='{$tw_title}', tw_desc='{$tw_desc}' WHERE meta_relation_id='{$link_id}'");

                    /** Manage Tags  */

                    $deleteTags = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "tag WHERE tag_relation_id='{$id}' AND tag_site_id=" . SITE_ID . " AND tag_type='{$postType}'");
                    $tags = explode(',', $tags);

                    /* Insert tags into tag table */

                    for ($i = 0; $i < sizeof($tags); $i++) {

                        if ($tags != '') {

                            $tagName = strtolower($tags[$i]);
                            $tagData = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagName}', '{$id}', '" . USER_ID . "', '" . SITE_ID . "', '{$postType}')");
                        } else {

                            $tagData = '';
                        }
                    }

                    if ($addRedirect && $updateLink && $updateSocialProof && $updateMeta && $tagData) {

                        mysqli_query($connection, 'COMMIT');

                        $status = 200;
                        $msg = 'Social proof is update successfully';
                        $redirect = '/dashboard/' . $postType . '/?type=edit&editor=true&id=' . base64_encode($id);
                    } else {

                        $msg = "Somthing is wrong while updating {$postType}";
                        mysqli_query($connection, 'ROLLBACK');
                    }
                } else {

                    $msg = 'No social proof information found';
                }
            }
        } else {

            $msg = 'Please select category';
        }
    } else {

        $msg = 'Please enter social proof title';
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 500),
        'msg' => ((isset($msg)) ? $msg : 'Something is wrong while updating ' . $postType),
        'redirect' => ((isset($redirect)) ? $redirect : '')
    );

    echo json_encode($result, TRUE);
}
