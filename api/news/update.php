<?php

require_once LOGIN_CHECK;

function normalizeNewsScheduleValue($scheduleAt)
{
    $scheduleAt = trim((string) $scheduleAt);
    if ($scheduleAt === '') {
        return false;
    }

    $timestamp = strtotime($scheduleAt);
    if ($timestamp === false) {
        return false;
    }

    return date('Y-m-d H:i:s', $timestamp);
}

function isFutureNewsScheduleValue($scheduleAt)
{
    $timestamp = strtotime((string) $scheduleAt);
    if ($timestamp === false) {
        return false;
    }

    return $timestamp > time();
}

function updateNewsWorkflow($connection, $data, $workflow = 'publish', $scheduledAt = null)
{
    extract($data);

    if ($title == '') {
        echo json_encode([
            'status' => 500,
            'msg' => 'Please enter news title',
            'redirect' => '',
        ], TRUE);
        return;
    }

    if (!isset($category)) {
        echo json_encode([
            'status' => 500,
            'msg' => 'Please select category',
            'redirect' => '',
        ], TRUE);
        return;
    }

    if ($workflow === 'schedule') {
        $scheduledAt = normalizeNewsScheduleValue(
            $scheduledAt !== null ? $scheduledAt : (isset($schedule_at) ? $schedule_at : ''),
        );
        if ($scheduledAt === false) {
            echo json_encode([
                'status' => 'warning',
                'msg' => 'Please select a valid schedule date and time',
                'redirect' => '',
            ], TRUE);
            return;
        }

        if (!isFutureNewsScheduleValue($scheduledAt)) {
            echo json_encode([
                'status' => 'warning',
                'msg' => 'Please choose a future schedule date and time',
                'redirect' => '',
            ], TRUE);
            return;
        }
    }

    $targetNewsStatus = 1;
    $targetLinkStatus = 200;
    if ($workflow === 'draft') {
        $targetNewsStatus = 0;
        $targetLinkStatus = 206;
    } elseif ($workflow === 'schedule') {
        $targetNewsStatus = 2;
        $targetLinkStatus = 206;
    }

    $postQuery = "SELECT link_id, news_gallery FROM " . TABLE_PREFIX . "news INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id AND link_type='news' WHERE link_url='{$oldURL}' AND news_id='{$id}' LIMIT 1";
    $checkPost = mysqli_query($connection, $postQuery);
    if (!$checkPost) {
        echo json_encode([
            'status' => 500,
            'msg' => mysqli_error($connection),
            'redirect' => '',
        ], TRUE);
        return;
    }

    if (mysqli_num_rows($checkPost) == 0) {
        echo json_encode([
            'status' => 500,
            'msg' => 'Invalid activity found',
            'redirect' => '',
        ], TRUE);
        return;
    }

    extract(mysqli_fetch_assoc($checkPost));

    require_once MODULES . 'imageUploader.php';

    $currentUserId = defined('USER_ID') ? USER_ID : 0;
    $featurePost = ((isset($featurePost)) ? 1 : 0);
    $newsOwnerId = ((isset($authorId)) ? (($authorId == '') ? $currentUserId : $authorId) : $currentUserId);

    if (isset($image)) {
        $featureImage = (((isset($featureImage)) ? $featureImage : $oldFeatureImage));
        $thumbnailImage = (((isset($thumbnailImage)) ? $featureImage : $oldThumbnailImage));
        $facebookImage = (((isset($facebookImage)) ? $facebookImage : $oldFacebookImage));
        $twitterImage = (((isset($twitterImage)) ? $twitterImage : $oldTwitterImage));
    } else {
        $featureImage = $oldFeatureImage;
        $thumbnailImage = $featureImage;
        $facebookImage = (($oldFacebookImage == '') ? $oldFeatureImage : $oldFacebookImage);
        $twitterImage = (($oldTwitterImage == '') ? $oldFeatureImage : $oldTwitterImage);
    }

    if (
        $workflow === 'publish' &&
        isset($postStatus) &&
        (int) $postStatus === 0 &&
        trim((string) $featureImage) === ''
    ) {
        echo json_encode([
            'status' => 'warning',
            'msg' => 'Please select news feature image before publishing',
            'redirect' => '',
        ], TRUE);
        return;
    }

    if (isset($_FILES['gallery'])) {
        require_once MODULES . 'galleryUploader.php';
        if ($gallery != '') {
            $isGalleryUpdate = TRUE;
        }
    }

    $oldGallery = json_decode($news_gallery, TRUE);
    $newGallery = array();
    if (isset($_POST['oldGallery'])) {
        for ($i = 0; $i < sizeof($_POST['oldGallery']); $i++) {
            $j = $_POST['oldGallery'][$i] - 1;
            if (isset($oldGallery[$j])) {
                $newGallery[] = $oldGallery[$j];
            }
        }
    }
    if (isset($isGalleryUpdate)) {
        $newGallery = array_merge($newGallery, $images);
    }
    $newGallery = json_encode($newGallery, TRUE);

    $category = explode('_', $category);
    $categoryURL = str_replace('/category/', '', $category[1]);
    $category = $category[0];
    $url = strtolower($categoryURL . preg_replace('~[^\p{M}\w-]+~u', '-', $url)) . '/';
    $linkID = $link_id;

    mysqli_query($connection, 'START TRANSACTION');

    $addRedirect = TRUE;
    $updateLink = FALSE;

    if ($oldURL != $url) {
        $checkURL = mysqli_query($connection, "SELECT link_url FROM " . TABLE_PREFIX . "link WHERE link_url='{$url}' AND link_type='news' AND link_site_id='" . SITE_ID . "' AND link_status!=301");
        if ($checkURL && mysqli_num_rows($checkURL) == 0) {
            $addRedirect = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "link SET link_status=301 WHERE link_url='{$oldURL}' AND link_site_id='" . SITE_ID . "'");
            $linkPublishedValue = ($workflow === 'schedule') ? "'{$scheduledAt}'" : 'NOW()';
            $updateLink = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published, link_is_featured) VALUES('{$url}', '{$id}', '{$targetLinkStatus}', '{$postType}', '{$currentUserId}', '" . SITE_ID . "', NOW(), {$linkPublishedValue}, '{$featurePost}')");
            $linkID = mysqli_insert_id($connection);
        } else {
            $addRedirect = FALSE;
            $status = 'warning';
            $msg = 'Please change news url. Because its already in used';
        }
    } else {
        if ($workflow === 'publish') {
            if ((int) $postStatus === 0 || (int) $postStatus === 2) {
                $updateLink = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "link SET link_status=200, link_published=NOW(), link_modified=NOW(), link_is_featured='{$featurePost}' WHERE link_relation_id='{$id}' AND link_type='{$postType}' AND link_url='{$oldURL}' AND link_site_id='" . SITE_ID . "'");
            } else {
                $updateLink = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "link SET link_status=200, link_modified=NOW(), link_is_featured='{$featurePost}' WHERE link_relation_id='{$id}' AND link_type='{$postType}' AND link_url='{$oldURL}' AND link_site_id='" . SITE_ID . "'");
            }
        } elseif ($workflow === 'draft') {
            $updateLink = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "link SET link_status=206, link_modified=NOW(), link_is_featured='{$featurePost}' WHERE link_relation_id='{$id}' AND link_type='{$postType}' AND link_url='{$oldURL}' AND link_site_id='" . SITE_ID . "'");
        } else {
            $updateLink = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "link SET link_status=206, link_published='{$scheduledAt}', link_modified=NOW(), link_is_featured='{$featurePost}' WHERE link_relation_id='{$id}' AND link_type='{$postType}' AND link_url='{$oldURL}' AND link_site_id='" . SITE_ID . "'");
        }
    }

    $newsPublishedValue = ($workflow === 'schedule') ? "'{$scheduledAt}'" : 'NOW()';
    $updatePost = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "news SET news_image='{$featureImage}', news_thumbnail='{$thumbnailImage}', news_gallery='{$newGallery}', news_owner_id='{$newsOwnerId}', news_title='{$title}', news_description='{$description}', news_category='{$category}', news_mrp='{$amount}', news_price='{$amount}', news_status='{$targetNewsStatus}', news_paid='{$paid}', news_published={$newsPublishedValue} WHERE news_id='{$id}'");

    $updateMeta = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "meta SET meta_title='{$meta_title}', meta_description='{$meta_desc}', meta_canonical='{$canonical}', meta_keyword='{$meta_key}', meta_relation_id='{$linkID}', fb_img='{$facebookImage}', fb_title='{$fb_title}', fb_desc='{$fb_desc}', tw_img='{$twitterImage}', tw_title='{$tw_title}', tw_desc='{$tw_desc}' WHERE meta_relation_id='{$link_id}'");

    mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "tag WHERE tag_relation_id='{$id}' AND tag_site_id=" . SITE_ID . " AND tag_type='{$postType}'");

    $tagData = TRUE;
    $tags = explode(',', $tags);
    for ($i = 0; $i < sizeof($tags); $i++) {
        $tagName = trim($tags[$i]);
        if ($tagName !== '') {
            $tagName = strtolower($tagName);
            $tagData = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "tag(tag_title, tag_relation_id, tag_owner_id, tag_site_id, tag_type) VALUES('{$tagName}', '{$id}', '{$currentUserId}', '" . SITE_ID . "', '{$postType}')");
        }
    }

    // Bulk insert authors with seq_no (credit order)
    if (
        isset($_POST['authorId'], $_POST['authorOrder']) &&
        is_array($_POST['authorId']) && is_array($_POST['authorOrder']) &&
        count($_POST['authorId']) > 0
    ) {
        $values = [];
        // Map authorId to its order from authorOrder[]
        for ($i = 0; $i < count($_POST['authorId']); $i++) {
            $uid = mysqli_real_escape_string($connection, $_POST['authorId'][$i]);
            // Find the corresponding order for this authorId
            $seq = '';
            if (isset($_POST['authorOrder'][$i])) {
                $seq = mysqli_real_escape_string($connection, $_POST['authorOrder'][$i]);
            }
            if ($uid && $seq) {
                $values[] = "('{$id}', 'news', '{$uid}', '{$seq}')";
            }
        }
        if (!empty($values)) {
            // Remove any previous authors for this post (for safety)
            mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "authors WHERE rel_id='{$id}' AND rel_type='news'");
            $sql = "INSERT INTO " . TABLE_PREFIX . "authors (rel_id, rel_type, profile_user_id, seq_no) VALUES " . implode(',', $values);
            mysqli_query($connection, $sql);
        }
    }

    if ($addRedirect && $updateLink && $updatePost && $updateMeta && $tagData) {
        mysqli_query($connection, 'COMMIT');

        $successMessage = 'News is update successfully';
        if ($workflow === 'draft') {
            $successMessage = 'News draft saved successfully';
        } elseif ($workflow === 'schedule') {
            $successMessage = 'News scheduled successfully';
        }

        echo json_encode([
            'status' => 200,
            'msg' => $successMessage,
            'redirect' => '/dashboard/' . $postType . '/?type=edit&editor=true&id=' . base64_encode($id) . ((isset($newsType) && $newsType == 'gallery') ? '&theme=' . $newsType : ''),
        ], TRUE);
        return;
    }

    mysqli_query($connection, 'ROLLBACK');
    echo json_encode([
        'status' => ((isset($status)) ? $status : 500),
        'msg' => ((isset($msg)) ? $msg : 'Somthing is wrong while updating ' . $postType),
        'redirect' => '',
    ], TRUE);
}

function update($connection, $data)
{
    if (isset($data['postStatus']) && (int) $data['postStatus'] === 2) {
        updateNewsWorkflow($connection, $data, 'schedule');
        return;
    }

    updateNewsWorkflow($connection, $data, 'publish');
}
