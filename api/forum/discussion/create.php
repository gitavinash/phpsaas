<?php

require_once LOGIN_CHECK;

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!empty($_FILES)) {

        if (isset($_FILES['files'])) {
            require_once MODULES . 'fileUploader.php';
            $fileOutput = json_decode($files, TRUE);
        }

        if (isset($_FILES['beforeImage'])) {
            require_once MODULES . 'imageUploader.php';
        } else {
            $beforeImage = '';
            $afterImage = '';
        }
    } else {

        $beforeImage = '';
        $afterImage = '';
    }
    // Extract the single upload image string to variables

    $featureDiscussion = 0;

    $featureImage = ((isset($fileOutput[0]['path'])) ? $fileOutput[0]['path'] : '');
    $thumbnailImage = $featureImage;

    $meta_desc = '';
    $meta_title = '';

    $tagsJson = '["sfasdf","adsf","sadf","dsaf"]'; // this comes from your input, for example $_POST['tags']

    $tagsArray = json_decode($tagsJson, true); // decode JSON to PHP array

    if (json_last_error() === JSON_ERROR_NONE && is_array($tagsArray)) {
        $meta_key = implode(", ", $tagsArray);
    } else {
        $meta_key = '';
    }
    $fb_title = '';
    $facebookImage = '';
    $tw_title = '';
    $twitterImage = '';
    $loggedInUserId = USER_ID;

    /** discussion page url */

    $urlTitle = '/' . time() . '-' . strtolower(preg_replace('~[^\p{M}\w-]+~u', '-', trim($title))) . '/';
    $url = '/' . str_replace('_', '-', $post_type) . $urlTitle;
    $canonical = $url;
    $robots = '';

    /* define facebook and twitter meta desciption */

    $fb_desc = ((!isset($fb_desc)) ? $meta_desc : $fb_desc);
    $tw_desc = ((!isset($tw_desc)) ? $meta_desc : $tw_desc);

    if ($checkURL = mysqli_query($connection, "SELECT link_relation_id FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_site_id='" . SITE_ID . "'")) {

        if (mysqli_num_rows($checkURL) == 0) {

            $description = $content_html;
            require_once EDITOR_CONTENT_FILTER;

            $link_status = 200;

            /** Start uploading the discussion */

            mysqli_query($connection, "START TRANSACTION");

            // Dicussion gallery

            if (isset($fileOutput[0])) {
                $addNewGallery = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "gallery(gallery_site_id, gallery_image, gallery_thumbnail, gallery_title, gallery_description, gallery_image_list, gallery_category, gallery_owner_id, gallery_status, gallery_type) VALUES('" . SITE_ID . "', '{$featureImage}', '{$thumbnailImage}', '{$title}', '{$description}', '{$files}', '{$category}', '{$loggedInUserId}', '1', 'gallery')");
                $galleryID = mysqli_insert_id($connection);

                $galleryUrl = '/gallery/' . $urlTitle;
                $addNewGalleryLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_is_featured, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$galleryUrl}', '{$galleryID}', '{$link_status}', 'gallery', '0', '{$loggedInUserId}', '" . SITE_ID . "', NOW() ,NOW())");
                $galleryLinkID = mysqli_insert_id($connection);

                $addNewGalleryMeta = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "meta(meta_relation_id, meta_site_id,  meta_title, meta_type, meta_description, meta_keyword, meta_canonical, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$galleryLinkID}', " . APP_SITE_ID . ", '{$meta_title}', 'gallery', '{$meta_desc}', '{$meta_key}', '{$canonical}', '{$robots}', '{$facebookImage}', '{$fb_title}', '{$fb_desc}', '{$twitterImage}', '{$tw_title}', '{$tw_desc}')");
            } else {
                $galleryID = 0;
                $addNewGallery = true;
                $addNewGalleryLink = true;
                $addNewGalleryMeta = true;
            }

            /* Insert data into link table */

            $addNewDiscussion = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "discussion(discussion_site_id, discussion_image, discussion_thumbnail, discussion_title, discussion_description, discussion_category, discussion_gallery_id, discussion_before_image, discussion_after_image, discussion_owner_id, discussion_comment, discussion_anonymous, discussion_status, discussion_type) VALUES(" . SITE_ID . ", '{$featureImage}', '{$thumbnailImage}', '{$title}', '{$description}', '{$category}', '{$galleryID}', '{$beforeImage}', '{$afterImage}', '{$loggedInUserId}', '{$allow_comments}', '{$anonymous}', '0', '{$post_type}')");
            $discussionID = mysqli_insert_id($connection);

            $addNewLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_is_featured, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$discussionID}', '{$link_status}', 'discussion', '{$featureDiscussion}', {$loggedInUserId}, '" . SITE_ID . "', NOW() ,NOW())");
            $linkID = mysqli_insert_id($connection);

            $addNewMeta = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "meta(meta_relation_id, meta_site_id,  meta_title, meta_type, meta_description, meta_keyword, meta_canonical, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$linkID}', " . SITE_ID . ", '{$meta_title}', 'discussion', '{$meta_desc}', '{$meta_key}', '{$canonical}', '{$robots}', '{$facebookImage}', '{$fb_title}', '{$fb_desc}', '{$twitterImage}', '{$tw_title}', '{$tw_desc}')");



            /* Insert tags into tag table */

            if ($tags != '') {

                $tags = explode(',', $tags);

                for ($i = 0; $i < sizeof($tags); $i++) {

                    $tagsName = strtolower(preg_replace('~[^\p{M}\s\w,-]+~u', '', rtrim(ltrim($tags[$i], ' '), ' ')));
                    $addNewTags = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagsName}', '{$discussionID}', '" . USER_ID . "', '" . SITE_ID . "', 'discussion')");
                }
            } else {

                $addNewTags = TRUE;
            }

            if ($addNewDiscussion && $addNewLink && $addNewMeta && $addNewTags) {
                $httpStatus = 201;
                $status = 'success';
                $msg = ucwords(str_replace('_', ' ', $post_type)) . ' add successfully';
                $redirect = $url;
                mysqli_query($connection, "COMMIT");
            } else {

                $msg = 'Something is wrong while adding new discussion';

                mysqli_query($connection, "ROLLBACK");
            }
        } else {

            $msg = 'Please change url';
        }
    } else {

        $msg = 'Technical errro';
    }
} else {
    $httpStatus = 400;
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
    'redirectUrl' => ((isset($redirect)) ? $redirect : '/'),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);