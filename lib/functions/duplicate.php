<?php

/**
 * Shared read-only duplication and POST sanitization helpers for administrator modules.
 * Nothing in this file performs database writes or file copies on GET.
 */

if (!function_exists('iswEscHtml')) {
    function iswEscHtml($value)
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('iswPreviewImageUrl')) {
    function iswPreviewImageUrl($path)
    {
        $clean = trim((string)($path ?? ''));
        if ($clean === '') {
            return '';
        }

        if (preg_match('/^(https?:)?\/\//i', $clean)) {
            return iswEscHtml($clean);
        }

        if (defined('SITE_DOMAIN') && SITE_DOMAIN !== '') {
            return iswEscHtml('//' . SITE_DOMAIN . '/' . ltrim($clean, '/'));
        }

        return iswEscHtml('/' . ltrim($clean, '/'));
    }
}

if (!function_exists('iswDecodeIdString')) {
    function iswDecodeIdString($rawValue)
    {
        if (!is_string($rawValue) && !is_numeric($rawValue)) {
            return 0;
        }

        $trimmed = trim((string)$rawValue);
        if ($trimmed === '') {
            return 0;
        }

        $decoded = base64_decode($trimmed, true);
        if ($decoded === false || !ctype_digit($decoded)) {
            return 0;
        }

        $id = (int)$decoded;
        return ($id > 0) ? $id : 0;
    }
}

if (!function_exists('iswGetDuplicateSourceId')) {
    function iswGetDuplicateSourceId($paramName = 'duplicate_id')
    {
        if (!isset($_GET[$paramName])) {
            return 0;
        }

        return iswDecodeIdString($_GET[$paramName]);
    }
}

if (!function_exists('iswNormalizeFlag')) {
    function iswNormalizeFlag($value)
    {
        return (!empty($value) && (string)$value !== '0') ? 1 : 0;
    }
}

if (!function_exists('iswNormalizeInt')) {
    function iswNormalizeInt($value, $default = 0)
    {
        if (is_numeric($value)) {
            return (int)$value;
        }

        return (int)$default;
    }
}

if (!function_exists('iswNormalizeDecimal')) {
    function iswNormalizeDecimal($value, $default = 0.0)
    {
        if (is_numeric($value)) {
            return number_format((float)$value, 2, '.', '');
        }

        return number_format((float)$default, 2, '.', '');
    }
}

if (!function_exists('iswFormatTimeInput')) {
    function iswFormatTimeInput($timeValue)
    {
        $raw = trim((string)($timeValue ?? ''));
        if ($raw === '' || $raw === '00:00:00' || $raw === '00:00') {
            return '';
        }

        $timestamp = strtotime($raw);
        if ($timestamp === false) {
            return '';
        }

        return date('H:i', $timestamp);
    }
}

if (!function_exists('iswFormatTimeDb')) {
    function iswFormatTimeDb($timeValue)
    {
        $raw = trim((string)($timeValue ?? ''));
        if ($raw === '') {
            return '00:00:00';
        }

        if (preg_match('/^\d{2}:\d{2}$/', $raw)) {
            return $raw . ':00';
        }

        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $raw)) {
            return $raw;
        }

        $timestamp = strtotime($raw);
        if ($timestamp === false) {
            return '00:00:00';
        }

        return date('H:i:s', $timestamp);
    }
}

if (!function_exists('iswSanitizeOldMediaPath')) {
    function iswSanitizeOldMediaPath($connection, $path)
    {
        $raw = trim((string)($path ?? ''));
        if ($raw === '') {
            return '';
        }

        // Reject ASCII control chars or null bytes
        if (preg_match('/[\x00-\x1F\x7F]/', $raw)) {
            return '';
        }

        // Reject directory traversal segments
        if (strpos($raw, '..') !== false) {
            return '';
        }

        // Reject dangerous URI schemes while allowing root/relative paths and http(s) URLs
        if (preg_match('/^[a-zA-Z][a-zA-Z0-9+.-]*:/', $raw)) {
            if (!preg_match('/^https?:\/\//i', $raw)) {
                return '';
            }
        }

        // Disallow angle brackets or quotes that break attributes if reflected
        if (preg_match('/[<>"\']/', $raw)) {
            return '';
        }

        return mysqli_real_escape_string($connection, $raw);
    }
}

if (!function_exists('iswResolveUploadedImage')) {
    function iswResolveUploadedImage($connection, $data, $uploadKey, $oldPostKey, $fallback = '', $uploadedImages = array())
    {
        if (is_array($uploadedImages) && isset($uploadedImages[$uploadKey]) && trim((string)$uploadedImages[$uploadKey]) !== '') {
            return mysqli_real_escape_string($connection, trim((string)$uploadedImages[$uploadKey]));
        }

        $oldCandidate = '';
        if (isset($_POST[$oldPostKey]) && trim((string)$_POST[$oldPostKey]) !== '') {
            $oldCandidate = (string)$_POST[$oldPostKey];
        } elseif (isset($data[$oldPostKey]) && trim((string)$data[$oldPostKey]) !== '') {
            $oldCandidate = (string)$data[$oldPostKey];
        }

        if ($oldCandidate !== '') {
            $safeOld = iswSanitizeOldMediaPath($connection, $oldCandidate);
            if ($safeOld !== '') {
                return $safeOld;
            }
        }

        return (string)$fallback;
    }
}

if (!function_exists('iswResolveDuplicateImages')) {
    function iswResolveDuplicateImages($connection, $data, $uploadedImages = array(), $uploadedThumbnail = '')
    {
        $featureImage = iswResolveUploadedImage($connection, $data, 'featureImage', 'oldFeatureImage', '', $uploadedImages);

        if (trim((string)$uploadedThumbnail) !== '') {
            $thumbnailImage = mysqli_real_escape_string($connection, trim((string)$uploadedThumbnail));
        } elseif (is_array($uploadedImages) && !empty($uploadedImages['featureImage'])) {
            $thumbnailImage = $featureImage;
        } else {
            $oldThumb = '';
            if (isset($_POST['oldThumbnailImage']) && trim((string)$_POST['oldThumbnailImage']) !== '') {
                $oldThumb = iswSanitizeOldMediaPath($connection, $_POST['oldThumbnailImage']);
            } elseif (isset($data['oldThumbnailImage']) && trim((string)$data['oldThumbnailImage']) !== '') {
                $oldThumb = iswSanitizeOldMediaPath($connection, $data['oldThumbnailImage']);
            }
            $thumbnailImage = ($oldThumb !== '') ? $oldThumb : $featureImage;
        }

        $facebookImage = iswResolveUploadedImage($connection, $data, 'facebookImage', 'oldFacebookImage', $featureImage, $uploadedImages);
        $twitterImage = iswResolveUploadedImage($connection, $data, 'twitterImage', 'oldTwitterImage', $featureImage, $uploadedImages);

        return array(
            'featureImage' => $featureImage,
            'thumbnailImage' => $thumbnailImage,
            'facebookImage' => $facebookImage,
            'twitterImage' => $twitterImage,
        );
    }
}

if (!function_exists('iswFormatDuplicateTitle')) {
    function iswFormatDuplicateTitle($title)
    {
        $clean = trim((string)($title ?? ''));
        if ($clean === '') {
            return '';
        }

        $base = preg_replace('/\s*\(Copy(?:\s+\d+)?\)$/i', '', $clean);
        if ($base === null || $base === '') {
            $base = $clean;
        }

        return $base . ' (Copy)';
    }
}

if (!function_exists('iswGenerateUniqueDuplicateSlug')) {
    function iswGenerateUniqueDuplicateSlug($connection, $sourceLinkUrl, $fallbackTitle, $rebuildPrefix, $siteId, $isPageModule = false)
    {
        $trimmedUrl = trim((string)($sourceLinkUrl ?? ''), '/');
        $leafSlug = '';

        if ($trimmedUrl !== '') {
            $segments = explode('/', $trimmedUrl);
            $leafSlug = (string)end($segments);
        }

        if ($leafSlug === '') {
            $leafSlug = preg_replace('~[^\p{M}\w-]+~u', '-', strtolower(trim((string)$fallbackTitle)));
            $leafSlug = trim((string)$leafSlug, '-');
        }

        if ($leafSlug === '') {
            $leafSlug = 'item';
        }

        $baseSlug = preg_replace('/-copy(?:-\d+)?$/i', '', $leafSlug);
        $baseSlug = trim((string)$baseSlug, '-');
        if ($baseSlug === '') {
            $baseSlug = 'item';
        }

        $prefix = (string)$rebuildPrefix;
        if ($isPageModule) {
            $prefix = '/';
        } else {
            if ($prefix === '') {
                $prefix = '/';
            }
            if ($prefix[0] !== '/') {
                $prefix = '/' . $prefix;
            }
            if (substr($prefix, -1) !== '/') {
                $prefix .= '/';
            }
        }

        $siteIdInt = (int)$siteId;

        for ($attempt = 1; $attempt <= 50; $attempt++) {
            $candidateSlug = strtolower(($attempt === 1) ? ($baseSlug . '-copy') : ($baseSlug . '-copy-' . $attempt));
            $candidateUrl = strtolower($prefix . $candidateSlug) . '/';
            if ($isPageModule) {
                $candidateUrl = '/' . strtolower($candidateSlug) . '/';
            }

            $escapedCandidate = mysqli_real_escape_string($connection, $candidateUrl);
            $check = mysqli_query(
                $connection,
                "SELECT link_id FROM " . TABLE_PREFIX . "link WHERE link_url='{$escapedCandidate}' AND link_site_id={$siteIdInt} LIMIT 1"
            );

            if ($check && mysqli_num_rows($check) === 0) {
                return $candidateSlug;
            }
        }

        return strtolower($baseSlug . '-copy-' . time());
    }
}

if (!function_exists('iswBuildPreloadedGallery')) {
    function iswBuildPreloadedGallery($galleryJson)
    {
        $raw = trim((string)($galleryJson ?? ''));
        if ($raw === '') {
            return array();
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return array();
        }

        $preloaded = array();
        foreach ($decoded as $index => $item) {
            if (!is_array($item) || empty($item['path'])) {
                continue;
            }

            $preloaded[] = array(
                'id' => count($preloaded) + 1,
                'source_index' => (int)$index + 1,
                'name' => isset($item['name']) ? (string)$item['name'] : basename((string)$item['path']),
                'src' => (string)$item['path'],
                'type' => isset($item['type']) ? (string)$item['type'] : '',
            );
        }

        return $preloaded;
    }
}

if (!function_exists('iswMergeDuplicateGallery')) {
    function iswMergeDuplicateGallery($connection, $module, $postData, $uploadedImages, $siteId, $returnEmptyAsJsonArray = false)
    {
        $siteIdInt = (int)$siteId;
        $keptGallery = array();

        $rawSource = isset($postData['duplicateGallerySource']) ? trim((string)$postData['duplicateGallerySource']) : '';
        $sourceId = iswDecodeIdString($rawSource);

        if ($sourceId > 0 && isset($postData['oldGallery']) && is_array($postData['oldGallery'])) {
            $sql = '';
            switch ($module) {
                case 'event':
                    $sql = "SELECT e.event_gallery AS gallery_json FROM " . TABLE_PREFIX . "event e INNER JOIN " . TABLE_PREFIX . "link l ON l.link_relation_id=e.event_id AND l.link_type='event' AND l.link_status!=301 WHERE e.event_id={$sourceId} AND l.link_site_id={$siteIdInt} LIMIT 1";
                    break;
                case 'news':
                    $sql = "SELECT n.news_gallery AS gallery_json FROM " . TABLE_PREFIX . "news n INNER JOIN " . TABLE_PREFIX . "link l ON l.link_relation_id=n.news_id AND l.link_type='news' AND l.link_status!=301 WHERE n.news_id={$sourceId} AND l.link_site_id={$siteIdInt} LIMIT 1";
                    break;
                case 'packages':
                    $sql = "SELECT p.product_gallery AS gallery_json FROM " . TABLE_PREFIX . "product p INNER JOIN " . TABLE_PREFIX . "link l ON l.link_relation_id=p.product_id AND l.link_type='product' AND l.link_status!=301 WHERE p.product_id={$sourceId} AND p.product_type='packages' AND l.link_site_id={$siteIdInt} LIMIT 1";
                    break;
                case 'product':
                    $sql = "SELECT p.product_gallery AS gallery_json FROM " . TABLE_PREFIX . "product p INNER JOIN " . TABLE_PREFIX . "link l ON l.link_relation_id=p.product_id AND l.link_type='product' AND l.link_status!=301 WHERE p.product_id={$sourceId} AND p.product_type='product' AND l.link_site_id={$siteIdInt} LIMIT 1";
                    break;
                case 'service':
                    $sql = "SELECT s.service_gallery AS gallery_json FROM " . TABLE_PREFIX . "service s INNER JOIN " . TABLE_PREFIX . "link l ON l.link_relation_id=s.service_id AND l.link_type='service' AND l.link_status!=301 WHERE s.service_id={$sourceId} AND s.service_type='service' AND l.link_site_id={$siteIdInt} LIMIT 1";
                    break;
                case 'treatment':
                    $sql = "SELECT t.treatment_gallery AS gallery_json FROM " . TABLE_PREFIX . "treatment t INNER JOIN " . TABLE_PREFIX . "link l ON l.link_relation_id=t.treatment_id AND l.link_type='treatment' AND l.link_status!=301 WHERE t.treatment_id={$sourceId} AND t.treatment_type='treatment' AND l.link_site_id={$siteIdInt} LIMIT 1";
                    break;
            }

            if ($sql !== '' && ($res = mysqli_query($connection, $sql)) && mysqli_num_rows($res) > 0) {
                $row = mysqli_fetch_assoc($res);
                $decoded = json_decode((string)($row['gallery_json'] ?? ''), true);
                if (is_array($decoded)) {
                    // Build filtered list matching preloaded order (skipping invalid empty-path entries)
                    $validItems = array();
                    foreach ($decoded as $item) {
                        if (is_array($item) && !empty($item['path'])) {
                            $validItems[] = $item;
                        }
                    }

                    foreach ($postData['oldGallery'] as $oneBasedIndex) {
                        $idx = (int)$oneBasedIndex - 1;
                        if ($idx >= 0 && isset($validItems[$idx])) {
                            $keptGallery[] = $validItems[$idx];
                        }
                    }
                }
            }
        }

        if (is_array($uploadedImages) && !empty($uploadedImages)) {
            $keptGallery = array_merge($keptGallery, $uploadedImages);
        }

        if (empty($keptGallery)) {
            return $returnEmptyAsJsonArray ? '[]' : '';
        }

        $encoded = json_encode($keptGallery);
        return ($encoded !== false) ? $encoded : ($returnEmptyAsJsonArray ? '[]' : '');
    }
}

if (!function_exists('iswFetchDuplicateTags')) {
    function iswFetchDuplicateTags($connection, $relationId, $parentId, $tagTypes, $siteId)
    {
        $tagTargetId = ((int)$parentId > 0) ? (int)$parentId : (int)$relationId;
        $siteIdInt = (int)$siteId;
        $escapedTypes = array();
        foreach ((array)$tagTypes as $t) {
            $escapedTypes[] = "'" . mysqli_real_escape_string($connection, (string)$t) . "'";
        }

        if (empty($escapedTypes)) {
            return '';
        }

        $inClause = implode(',', $escapedTypes);
        $tags = array();
        $query = mysqli_query(
            $connection,
            "SELECT tag_title FROM " . TABLE_PREFIX . "tag WHERE tag_relation_id={$tagTargetId} AND tag_site_id={$siteIdInt} AND tag_type IN ({$inClause}) ORDER BY tag_id ASC"
        );

        if ($query && mysqli_num_rows($query) > 0) {
            while ($tagRow = mysqli_fetch_assoc($query)) {
                $title = trim((string)($tagRow['tag_title'] ?? ''));
                if ($title !== '' && !in_array($title, $tags, true)) {
                    $tags[] = $title;
                }
            }
        }

        return implode(',', $tags);
    }
}

if (!function_exists('iswResolveCategoryPrefix')) {
    function iswResolveCategoryPrefix($connection, $categoryId, $sourceLinkUrl, $modulePrefix, $siteId)
    {
        $catIdInt = (int)$categoryId;
        $siteIdInt = (int)$siteId;
        $catPrefix = '';

        if ($catIdInt > 0) {
            $res = mysqli_query(
                $connection,
                "SELECT l.link_url FROM " . TABLE_PREFIX . "link l WHERE l.link_relation_id={$catIdInt} AND l.link_type='category' AND l.link_site_id={$siteIdInt} AND l.link_status!=301 ORDER BY l.link_id DESC LIMIT 1"
            );
            if ($res && ($row = mysqli_fetch_assoc($res))) {
                $catPrefix = str_replace('/category', '', (string)$row['link_url']);
            }
        }

        if ($catPrefix !== '') {
            if ($modulePrefix !== '') {
                return rtrim($modulePrefix, '/') . '/' . ltrim($catPrefix, '/');
            }
            return $catPrefix;
        }

        $trimmed = trim((string)$sourceLinkUrl, '/');
        if ($trimmed !== '' && strpos($trimmed, '/') !== false) {
            $parts = explode('/', $trimmed);
            array_pop($parts);
            return '/' . implode('/', $parts) . '/';
        }

        return ($modulePrefix !== '') ? rtrim($modulePrefix, '/') . '/' : '/';
    }
}

if (!function_exists('iswGetDuplicateRecord')) {
    function iswGetDuplicateRecord($connection, $module, $sourceId, $siteId)
    {
        $id = (int)$sourceId;
        $siteIdInt = (int)$siteId;
        if ($id <= 0 || $siteIdInt <= 0) {
            return null;
        }

        $record = null;
        $categoryId = 0;
        $modulePrefix = '';
        $isPageModule = false;
        $fallbackTitle = '';
        $tagsCsv = '';
        $preloadedGallery = array();
        $productMeta = array();
        $authors = array();
        $section = array();

        switch ($module) {
            case 'event':
                $res = mysqli_query(
                    $connection,
                    "SELECT e.*, l.link_id, l.link_url, l.link_is_featured, l.link_status, m.meta_title, m.meta_description, m.meta_keyword, m.meta_canonical, m.meta_robots, m.fb_img, m.fb_title, m.fb_desc, m.tw_img, m.tw_title, m.tw_desc FROM " . TABLE_PREFIX . "event e INNER JOIN " . TABLE_PREFIX . "link l ON l.link_relation_id=e.event_id AND l.link_type='event' AND l.link_status!=301 LEFT JOIN " . TABLE_PREFIX . "meta m ON m.meta_relation_id=l.link_id WHERE e.event_id={$id} AND l.link_site_id={$siteIdInt} LIMIT 1"
                );
                if ($res && mysqli_num_rows($res) > 0) {
                    $record = mysqli_fetch_assoc($res);
                    $categoryId = (int)($record['event_category'] ?? 0);
                    $fallbackTitle = (string)($record['event_title'] ?? '');
                    $tagsCsv = iswFetchDuplicateTags($connection, $id, (int)($record['event_parent_id'] ?? 0), array('event', 'post'), $siteIdInt);
                    $preloadedGallery = iswBuildPreloadedGallery($record['event_gallery'] ?? '');
                }
                break;

            case 'news':
                $res = mysqli_query(
                    $connection,
                    "SELECT n.*, l.link_id, l.link_url, l.link_is_featured, l.link_status, m.meta_title, m.meta_description, m.meta_keyword, m.meta_canonical, m.meta_robots, m.fb_img, m.fb_title, m.fb_desc, m.tw_img, m.tw_title, m.tw_desc FROM " . TABLE_PREFIX . "news n INNER JOIN " . TABLE_PREFIX . "link l ON l.link_relation_id=n.news_id AND l.link_type='news' AND l.link_status!=301 LEFT JOIN " . TABLE_PREFIX . "meta m ON m.meta_relation_id=l.link_id WHERE n.news_id={$id} AND l.link_site_id={$siteIdInt} LIMIT 1"
                );
                if ($res && mysqli_num_rows($res) > 0) {
                    $record = mysqli_fetch_assoc($res);
                    $categoryId = (int)($record['news_category'] ?? 0);
                    $fallbackTitle = (string)($record['news_title'] ?? '');
                    $tagsCsv = iswFetchDuplicateTags($connection, $id, (int)($record['news_parent_id'] ?? 0), array('news'), $siteIdInt);
                    $preloadedGallery = iswBuildPreloadedGallery($record['news_gallery'] ?? '');

                    $authRes = mysqli_query(
                        $connection,
                        "SELECT profile_user_id, seq_no FROM " . TABLE_PREFIX . "authors WHERE rel_id={$id} AND rel_type='news' ORDER BY seq_no ASC"
                    );
                    if ($authRes) {
                        while ($authRow = mysqli_fetch_assoc($authRes)) {
                            $aid = (int)($authRow['profile_user_id'] ?? 0);
                            if ($aid > 0) {
                                $authors[$aid] = (int)($authRow['seq_no'] ?? 0);
                            }
                        }
                    }
                }
                break;

            case 'offer':
                $res = mysqli_query(
                    $connection,
                    "SELECT p.*, l.link_id, l.link_url, l.link_is_featured, l.link_status, m.meta_title, m.meta_description, m.meta_keyword, m.meta_canonical, m.meta_robots, m.fb_img, m.fb_title, m.fb_desc, m.tw_img, m.tw_title, m.tw_desc FROM " . TABLE_PREFIX . "page p INNER JOIN " . TABLE_PREFIX . "link l ON l.link_relation_id=p.page_id AND l.link_type='page' AND l.link_status!=301 LEFT JOIN " . TABLE_PREFIX . "meta m ON m.meta_relation_id=l.link_id WHERE p.page_id={$id} AND p.page_type='offer' AND l.link_site_id={$siteIdInt} LIMIT 1"
                );
                if ($res && mysqli_num_rows($res) > 0) {
                    $record = mysqli_fetch_assoc($res);
                    $modulePrefix = '/offer/';
                    $fallbackTitle = (string)($record['page_title'] ?? '');
                }
                break;

            case 'page':
                $res = mysqli_query(
                    $connection,
                    "SELECT p.*, l.link_id, l.link_url, l.link_is_featured, l.link_status, m.meta_title, m.meta_description, m.meta_keyword, m.meta_canonical, m.meta_robots, m.fb_img, m.fb_title, m.fb_desc, m.tw_img, m.tw_title, m.tw_desc FROM " . TABLE_PREFIX . "page p INNER JOIN " . TABLE_PREFIX . "link l ON l.link_relation_id=p.page_id AND l.link_type='page' AND l.link_status!=301 LEFT JOIN " . TABLE_PREFIX . "meta m ON m.meta_relation_id=l.link_id WHERE p.page_id={$id} AND p.page_type='page' AND l.link_site_id={$siteIdInt} AND (p.page_site_id={$siteIdInt} OR p.page_site_id=0) LIMIT 1"
                );
                if ($res && mysqli_num_rows($res) > 0) {
                    $record = mysqli_fetch_assoc($res);
                    $modulePrefix = '/';
                    $isPageModule = true;
                    $fallbackTitle = (string)($record['page_title'] ?? '');
                }
                break;

            case 'package':
            case 'post':
            case 'question':
                $postTypeSql = mysqli_real_escape_string($connection, $module);
                $postTagTypes = ($module === 'post') ? array('post') : array($module, 'post');
                $res = mysqli_query(
                    $connection,
                    "SELECT p.*, l.link_id, l.link_url, l.link_is_featured, l.link_status, m.meta_title, m.meta_description, m.meta_keyword, m.meta_canonical, m.meta_robots, m.fb_img, m.fb_title, m.fb_desc, m.tw_img, m.tw_title, m.tw_desc FROM " . TABLE_PREFIX . "post p INNER JOIN " . TABLE_PREFIX . "link l ON l.link_relation_id=p.post_id AND l.link_type='post' AND l.link_status!=301 LEFT JOIN " . TABLE_PREFIX . "meta m ON m.meta_relation_id=l.link_id WHERE p.post_id={$id} AND p.post_type='{$postTypeSql}' AND l.link_site_id={$siteIdInt} LIMIT 1"
                );
                if ($res && mysqli_num_rows($res) > 0) {
                    $record = mysqli_fetch_assoc($res);
                    $categoryId = (int)($record['post_category'] ?? 0);
                    $fallbackTitle = (string)($record['post_title'] ?? '');
                    $tagsCsv = iswFetchDuplicateTags($connection, $id, (int)($record['post_parent_id'] ?? 0), $postTagTypes, $siteIdInt);
                }
                break;

            case 'packages':
            case 'product':
                $productTypeSql = mysqli_real_escape_string($connection, $module);
                $tagTypes = ($module === 'packages') ? array('packages', 'product') : array('product', '');
                $res = mysqli_query(
                    $connection,
                    "SELECT p.*, i.inventory_sku, i.inventory_currency, i.inventory_price, i.inventory_mrp, i.inventory_tax_status, i.inventory_tax_rate, i.inventory_in_stock, i.inventory_current_stock, i.inventory_backorder, i.inventory_stock_notice, i.inventory_weight, i.inventory_lenght, i.inventory_width, i.inventory_height, i.inventory_sell_online, i.inventory_variation, i.inventory_addons, l.link_id, l.link_url, l.link_is_featured, l.link_status, m.meta_title, m.meta_description, m.meta_keyword, m.meta_canonical, m.meta_robots, m.fb_img, m.fb_title, m.fb_desc, m.tw_img, m.tw_title, m.tw_desc FROM " . TABLE_PREFIX . "product p INNER JOIN " . TABLE_PREFIX . "link l ON l.link_relation_id=p.product_id AND l.link_type='product' AND l.link_status!=301 LEFT JOIN " . TABLE_PREFIX . "meta m ON m.meta_relation_id=l.link_id LEFT JOIN " . TABLE_PREFIX . "inventory i ON i.inventory_relation_id=p.product_id AND i.inventory_type='product' AND i.inventory_status=1 WHERE p.product_id={$id} AND p.product_type='{$productTypeSql}' AND l.link_site_id={$siteIdInt} ORDER BY i.inventory_id DESC LIMIT 1"
                );
                if ($res && mysqli_num_rows($res) > 0) {
                    $record = mysqli_fetch_assoc($res);
                    $categoryId = (int)($record['product_category'] ?? 0);
                    $modulePrefix = '/product';
                    $fallbackTitle = (string)($record['product_title'] ?? '');
                    $tagsCsv = iswFetchDuplicateTags($connection, $id, (int)($record['product_parent_id'] ?? 0), $tagTypes, $siteIdInt);
                    $preloadedGallery = iswBuildPreloadedGallery($record['product_gallery'] ?? '');

                    if ($module === 'product') {
                        $pmRes = mysqli_query(
                            $connection,
                            "SELECT product_meta_key, product_meta_value FROM " . TABLE_PREFIX . "product_meta WHERE product_meta_relation_id={$id} AND product_meta_site_id={$siteIdInt}"
                        );
                        if ($pmRes) {
                            while ($pmRow = mysqli_fetch_assoc($pmRes)) {
                                $k = (string)($pmRow['product_meta_key'] ?? '');
                                if ($k !== '') {
                                    $productMeta[$k] = (string)($pmRow['product_meta_value'] ?? '');
                                }
                            }
                        }
                    }
                }
                break;

            case 'service':
                $res = mysqli_query(
                    $connection,
                    "SELECT s.*, i.inventory_sku, i.inventory_currency, i.inventory_price, i.inventory_mrp, i.inventory_tax_status, i.inventory_tax_rate, i.inventory_in_stock, i.inventory_current_stock, i.inventory_backorder, i.inventory_stock_notice, i.inventory_weight, i.inventory_lenght, i.inventory_width, i.inventory_height, i.inventory_sell_online, l.link_id, l.link_url, l.link_is_featured, l.link_status, m.meta_title, m.meta_description, m.meta_keyword, m.meta_canonical, m.meta_robots, m.fb_img, m.fb_title, m.fb_desc, m.tw_img, m.tw_title, m.tw_desc FROM " . TABLE_PREFIX . "service s INNER JOIN " . TABLE_PREFIX . "link l ON l.link_relation_id=s.service_id AND l.link_type='service' AND l.link_status!=301 LEFT JOIN " . TABLE_PREFIX . "meta m ON m.meta_relation_id=l.link_id LEFT JOIN " . TABLE_PREFIX . "inventory i ON i.inventory_relation_id=s.service_id AND i.inventory_type='service' WHERE s.service_id={$id} AND s.service_type='service' AND l.link_site_id={$siteIdInt} ORDER BY i.inventory_id DESC LIMIT 1"
                );
                if ($res && mysqli_num_rows($res) > 0) {
                    $record = mysqli_fetch_assoc($res);
                    $categoryId = (int)($record['service_category'] ?? 0);
                    $modulePrefix = '/service';
                    $fallbackTitle = (string)($record['service_title'] ?? '');
                    $tagsCsv = iswFetchDuplicateTags($connection, $id, (int)($record['service_parent_id'] ?? 0), array('service'), $siteIdInt);
                    $preloadedGallery = iswBuildPreloadedGallery($record['service_gallery'] ?? '');

                    $rawSec = (string)($record['service_section'] ?? '');
                    if ($rawSec !== '') {
                        $decodedSec = json_decode(base64_decode($rawSec, true) ?: '', true);
                        if (!is_array($decodedSec)) {
                            $decodedSec = json_decode($rawSec, true);
                        }
                        if (is_array($decodedSec)) {
                            $section = $decodedSec;
                        }
                    }
                }
                break;

            case 'treatment':
                $res = mysqli_query(
                    $connection,
                    "SELECT t.*, i.inventory_sku, i.inventory_currency, i.inventory_price, i.inventory_mrp, i.inventory_tax_status, i.inventory_tax_rate, i.inventory_in_stock, i.inventory_current_stock, i.inventory_backorder, i.inventory_stock_notice, i.inventory_weight, i.inventory_lenght, i.inventory_width, i.inventory_height, i.inventory_sell_online, l.link_id, l.link_url, l.link_is_featured, l.link_status, m.meta_title, m.meta_description, m.meta_keyword, m.meta_canonical, m.meta_robots, m.fb_img, m.fb_title, m.fb_desc, m.tw_img, m.tw_title, m.tw_desc FROM " . TABLE_PREFIX . "treatment t INNER JOIN " . TABLE_PREFIX . "link l ON l.link_relation_id=t.treatment_id AND l.link_type='treatment' AND l.link_status!=301 LEFT JOIN " . TABLE_PREFIX . "meta m ON m.meta_relation_id=l.link_id LEFT JOIN " . TABLE_PREFIX . "inventory i ON i.inventory_relation_id=t.treatment_id AND i.inventory_type='treatment' WHERE t.treatment_id={$id} AND t.treatment_type='treatment' AND l.link_site_id={$siteIdInt} ORDER BY i.inventory_id DESC LIMIT 1"
                );
                if ($res && mysqli_num_rows($res) > 0) {
                    $record = mysqli_fetch_assoc($res);
                    $categoryId = (int)($record['treatment_category'] ?? 0);
                    $modulePrefix = '/treatment';
                    $fallbackTitle = (string)($record['treatment_title'] ?? '');
                    $tagsCsv = iswFetchDuplicateTags($connection, $id, (int)($record['treatment_parent_id'] ?? 0), array('treatment'), $siteIdInt);
                    $preloadedGallery = iswBuildPreloadedGallery($record['treatment_gallery'] ?? '');

                    $rawSec = (string)($record['treatment_section'] ?? '');
                    if ($rawSec !== '') {
                        $decodedSec = json_decode(base64_decode($rawSec, true) ?: '', true);
                        if (!is_array($decodedSec)) {
                            $decodedSec = json_decode($rawSec, true);
                        }
                        if (is_array($decodedSec)) {
                            $section = $decodedSec;
                        }
                    }
                }
                break;
        }

        if ($record === null) {
            return null;
        }

        $sourceUrl = (string)($record['link_url'] ?? '');
        if ($module === 'offer') {
            $rebuildPrefix = '/offer/';
        } elseif ($isPageModule) {
            $rebuildPrefix = '/';
        } else {
            $rebuildPrefix = iswResolveCategoryPrefix($connection, $categoryId, $sourceUrl, $modulePrefix, $siteIdInt);
        }

        $duplicateSlug = iswGenerateUniqueDuplicateSlug($connection, $sourceUrl, $fallbackTitle, $rebuildPrefix, $siteIdInt, $isPageModule);
        $duplicateTitle = iswFormatDuplicateTitle($fallbackTitle);

        return array(
            'source_id' => $id,
            'encoded_source_id' => base64_encode((string)$id),
            'module' => $module,
            'row' => $record,
            'source_title' => $fallbackTitle,
            'duplicate_title' => $duplicateTitle,
            'duplicate_slug' => $duplicateSlug,
            'tags_csv' => $tagsCsv,
            'preloaded_gallery' => $preloadedGallery,
            'product_meta' => $productMeta,
            'authors' => $authors,
            'section' => $section,
        );
    }
}
