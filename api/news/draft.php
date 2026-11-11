<?php

require_once LOGIN_CHECK;
require_once FUNCTIONS . 'duplicate.php';

function draft($connection, $data)
{
    extract($data);

    if (!isset($title) || $title == '') {
        echo json_encode([
            'status' => 'warning',
            'msg' => 'Please enter news title',
            'redirect' => '',
        ], TRUE);
        return;
    }

    if (!isset($category)) {
        echo json_encode([
            'status' => 'warning',
            'msg' => 'Please select category',
            'redirect' => '',
        ], TRUE);
        return;
    }

    if (isset($id) && $id != '') {
        require_once API_MODULES . 'news/update.php';
        updateNewsWorkflow($connection, $data, 'draft');
        return;
    }

    $news_audio = '';
    $files = array();
    if (isset($_FILES['audioFile']) && $_FILES['audioFile']['error'] === UPLOAD_ERR_OK) {
        $_FILES['files'] = array(
            'name' => array($_FILES['audioFile']['name']),
            'type' => array($_FILES['audioFile']['type']),
            'tmp_name' => array($_FILES['audioFile']['tmp_name']),
            'error' => array($_FILES['audioFile']['error']),
            'size' => array($_FILES['audioFile']['size'])
        );

        require_once MODULES . 'fileUploader.php';

        if (!empty($files) && is_string($files)) {
            $uploadedFiles = json_decode($files, true);
            if (is_array($uploadedFiles) && isset($uploadedFiles[0]['path'])) {
                $news_audio = mysqli_real_escape_string($connection, (string)$uploadedFiles[0]['path']);
            }
        }

        unset($_FILES['files']);
    }
    if ($news_audio === '') {
        $news_audio = iswResolveUploadedImage($connection, $data, 'audioFile', 'oldAudioFile', '');
    }

    $image = array();
    $thumbnailImage = '';
    $images = array();
    $gallery = '';
    require_once MODULES . 'imageUploader.php';

    $resolvedImages = iswResolveDuplicateImages(
        $connection,
        $data,
        (isset($image) && is_array($image)) ? $image : array(),
        isset($thumbnailImage) ? (string)$thumbnailImage : ''
    );
    $featureImage = $resolvedImages['featureImage'];
    $thumbnailImage = $resolvedImages['thumbnailImage'];
    $facebookImage = $resolvedImages['facebookImage'];
    $twitterImage = $resolvedImages['twitterImage'];

    if (isset($_FILES['gallery'])) {
        require_once MODULES . 'galleryUploader.php';
    }

    $gallery = iswMergeDuplicateGallery(
        $connection,
        'news',
        $_POST,
        (isset($images) && is_array($images)) ? $images : array(),
        SITE_ID,
        false
    );
    $gallery = mysqli_real_escape_string($connection, $gallery);

    $description = isset($description) ? $description : '';
    $featurePost = iswNormalizeFlag($featurePost ?? 0);
    $paid = iswNormalizeFlag($paid ?? 0);
    $amount = iswNormalizeDecimal($amount ?? 0);

    $category = explode('_', $category);
    $categoryURL = $category[1];
    $category = $category[0];

    $slugClean = trim((string)preg_replace('~[^\p{M}\w-]+~u', '-', trim((string)($url ?? ''), '/')), '-');
    if ($slugClean === '') {
        $slugClean = trim((string)preg_replace('~[^\p{M}\w-]+~u', '-', trim((string)$title)), '-');
    }
    $url = strtolower($categoryURL . $slugClean) . '/';

    $meta_title = isset($meta_title) ? $meta_title : '';
    $meta_desc = isset($meta_desc) ? $meta_desc : '';
    $meta_key = isset($meta_key) ? $meta_key : '';
    $canonical = isset($canonical) ? $canonical : '';
    $robots = isset($robots) ? $robots : 'index, follow';
    $fb_title = isset($fb_title) ? $fb_title : '';
    $fb_desc = ((!isset($fb_desc) || $fb_desc == '') ? $meta_desc : $fb_desc);
    $tw_title = isset($tw_title) ? $tw_title : '';
    $tw_desc = ((!isset($tw_desc) || $tw_desc == '') ? $meta_desc : $tw_desc);

    if ($checkURL = mysqli_query($connection, "SELECT link_relation_id FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_site_id='" . SITE_ID . "' AND link_status!=301")) {
        if (mysqli_num_rows($checkURL) != 0) {
            echo json_encode([
                'status' => 'warning',
                'msg' => 'Please change url',
                'redirect' => '',
            ], TRUE);
            return;
        }
    }

    require_once EDITOR_CONTENT_FILTER;
    mysqli_query($connection, "START TRANSACTION");

    $currentUserId = defined('USER_ID') ? USER_ID : 0;
    $newsOwnerId = ((isset($_POST['authorId'][0]) && is_numeric($_POST['authorId'][0]) && (int)$_POST['authorId'][0] > 0) ? (int)$_POST['authorId'][0] : (int)USER_ID);

    $addNewPost = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "news(news_site_id, news_image, news_gallery, news_thumbnail, news_title, news_description, news_category, news_owner_id, news_paid, news_mrp, news_price, news_audio, news_status, news_type, news_published) VALUES(" . SITE_ID . ", '{$featureImage}', '{$gallery}', '{$thumbnailImage}', '{$title}', '{$description}', '{$category}', {$newsOwnerId}, '{$paid}', '{$amount}', '{$amount}', '{$news_audio}', '0', 'news', NOW())");
    $postID = mysqli_insert_id($connection);

    $addNewLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_is_featured, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$postID}', '206', 'news', '{$featurePost}', {$currentUserId}, '" . SITE_ID . "', NOW(), NOW())");
    $linkID = mysqli_insert_id($connection);

    $addNewMeta = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "meta(meta_relation_id, meta_site_id, meta_title, meta_type, meta_description, meta_keyword, meta_canonical, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$linkID}', " . SITE_ID . ", '{$meta_title}', 'news', '{$meta_desc}', '{$meta_key}', '{$canonical}', '{$robots}', '{$facebookImage}', '{$fb_title}', '{$fb_desc}', '{$twitterImage}', '{$tw_title}', '{$tw_desc}')");

    $addNewTags = TRUE;
    $tagsStr = isset($tags) ? trim((string)$tags) : '';
    if ($tagsStr != '') {
        $tagsArr = explode(',', $tagsStr);
        foreach ($tagsArr as $singleTag) {
            $tagsName = strtolower(preg_replace('~[^\p{M}\s\w,-]+~u', '', trim($singleTag)));
            if ($tagsName !== '') {
                $tagInserted = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagsName}', '{$postID}', '{$currentUserId}', '" . SITE_ID . "', 'news')");
                if (!$tagInserted) {
                    $addNewTags = FALSE;
                }
            }
        }
    }

    if ($addNewPost && $addNewLink && $addNewMeta && $addNewTags) {
        // Bulk insert authors with seq_no (credit order)
        if (
            isset($_POST['authorId'], $_POST['authorOrder']) &&
            is_array($_POST['authorId']) && is_array($_POST['authorOrder']) &&
            count($_POST['authorId']) === count($_POST['authorOrder']) && count($_POST['authorId']) > 0
        ) {
            $values = [];
            for ($i = 0; $i < count($_POST['authorId']); $i++) {
                $uid = is_numeric($_POST['authorId'][$i]) ? (int)$_POST['authorId'][$i] : 0;
                $seq = is_numeric($_POST['authorOrder'][$i]) ? (int)$_POST['authorOrder'][$i] : 0;
                if ($uid > 0 && $seq > 0) {
                    $values[] = "('{$postID}', 'news', '{$uid}', '{$seq}')";
                }
            }
            if (!empty($values)) {
                // Remove any previous authors for this post (for safety)
                mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "authors WHERE rel_id='{$postID}' AND rel_type='news'");
                $sql = "INSERT INTO " . TABLE_PREFIX . "authors (rel_id, rel_type, profile_user_id, seq_no) VALUES " . implode(',', $values);
                mysqli_query($connection, $sql);
            }
        }
        mysqli_query($connection, "COMMIT");
        echo json_encode([
            'status' => 200,
            'msg' => 'News draft saved successfully',
            'redirect' => '/dashboard/news/?type=edit&editor=true&id=' . base64_encode($postID) . ((isset($newsType) && $newsType == 'gallery') ? '&theme=' . $newsType : ''),
        ], TRUE);
        return;
    }

    mysqli_query($connection, "ROLLBACK");
    echo json_encode([
        'status' => 'error',
        'msg' => 'Something is wrong while saving draft',
        'redirect' => '',
    ], TRUE);
}
