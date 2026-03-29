<?php

require_once LOGIN_CHECK;

function normalizeNewsScheduleAt($scheduleAt)
{
    $scheduleAt = trim((string) $scheduleAt);
    if ($scheduleAt == '') {
        return false;
    }

    $timestamp = strtotime($scheduleAt);
    if ($timestamp === false) {
        return false;
    }

    return date('Y-m-d H:i:s', $timestamp);
}

function isFutureNewsScheduleAt($scheduleAt)
{
    $timestamp = strtotime((string) $scheduleAt);
    if ($timestamp === false) {
        return false;
    }

    return $timestamp > time();
}

function schedule($connection, $data)
{
    extract($data);

    $scheduleDateTime = normalizeNewsScheduleAt(isset($schedule_at) ? $schedule_at : '');
    if ($scheduleDateTime === false) {
        echo json_encode([
            'status' => 'warning',
            'msg' => 'Please select a valid schedule date and time',
            'redirect' => '',
        ], TRUE);
        return;
    }

    if (!isFutureNewsScheduleAt($scheduleDateTime)) {
        echo json_encode([
            'status' => 'warning',
            'msg' => 'Please choose a future schedule date and time',
            'redirect' => '',
        ], TRUE);
        return;
    }

    if (isset($id) && $id != '') {
        require_once API_MODULES . 'news/update.php';
        updateNewsWorkflow($connection, $data, 'schedule', $scheduleDateTime);
        return;
    }

    require_once MODULES . 'imageUploader.php';

    if ($title == '') {
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

    if (!isset($image['featureImage'])) {
        echo json_encode([
            'status' => 'warning',
            'msg' => 'Please select news feature image.',
            'redirect' => '',
        ], TRUE);
        return;
    }

    if (isset($_FILES['gallery'])) {
        require_once MODULES . 'galleryUploader.php';
    } else {
        $gallery = '';
    }

    $featurePost = ((isset($featurePost)) ? 1 : 0);
    $facebookImage = (((isset($facebookImage)) ? $facebookImage : $featureImage));
    $twitterImage = (((isset($twitterImage)) ? $twitterImage : $featureImage));

    $category = explode('_', $category);
    $categoryURL = $category[1];
    $category = $category[0];
    $url = strtolower($categoryURL . preg_replace('~[^\p{M}\w-]+~u', '-', $url)) . '/';
    $fb_desc = (($fb_desc == '') ? $meta_desc : $fb_desc);
    $tw_desc = (($tw_desc == '') ? $meta_desc : $tw_desc);

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
    $newsOwnerId = ((isset($authorId)) ? (($authorId == '') ? $currentUserId : $authorId) : $currentUserId);
    $addNewPost = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "news(news_site_id, news_image, news_gallery, news_thumbnail, news_title, news_description, news_category, news_owner_id, news_paid, news_mrp, news_price, news_status, news_type, news_published) VALUES(" . SITE_ID . ", '{$featureImage}', '{$gallery}', '{$thumbnailImage}', '{$title}', '{$description}', '{$category}', {$newsOwnerId}, '{$paid}', '{$amount}', '{$amount}', '2', 'news', '{$scheduleDateTime}')");
    $postID = mysqli_insert_id($connection);

    $addNewLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_is_featured, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$postID}', '206', 'news', '{$featurePost}', {$currentUserId}, '" . SITE_ID . "', NOW(), '{$scheduleDateTime}')");
    $linkID = mysqli_insert_id($connection);

    $addNewMeta = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "meta(meta_relation_id, meta_site_id, meta_title, meta_type, meta_description, meta_keyword, meta_canonical, meta_robots, fb_img, fb_title, fb_desc, tw_img, tw_title, tw_desc) VALUES('{$linkID}', " . SITE_ID . ", '{$meta_title}', 'news', '{$meta_desc}', '{$meta_key}', '{$canonical}', '{$robots}', '{$facebookImage}', '{$fb_title}', '{$fb_desc}', '{$twitterImage}', '{$tw_title}', '{$tw_desc}')");

    if ($tags != '') {
        $tags = explode(',', $tags);
        foreach ($tags as $singleTag) {
            $tagsName = strtolower(preg_replace('~[^\p{M}\s\w,-]+~u', '', trim($singleTag)));
            $addNewTags = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagsName}', '{$postID}', '{$currentUserId}', '" . SITE_ID . "', 'news')");
        }
    } else {
        $addNewTags = TRUE;
    }

    if ($addNewPost && $addNewLink && $addNewMeta && $addNewTags) {
        mysqli_query($connection, "COMMIT");
        echo json_encode([
            'status' => 200,
            'msg' => 'News scheduled successfully',
            'redirect' => '/dashboard/news/?type=edit&editor=true&id=' . base64_encode($postID) . ((isset($newsType) && $newsType == 'gallery') ? '&theme=' . $newsType : ''),
        ], TRUE);
        return;
    }

    mysqli_query($connection, "ROLLBACK");
    echo json_encode([
        'status' => 'error',
        'msg' => 'Something is wrong while scheduling news',
        'redirect' => '',
    ], TRUE);
}
