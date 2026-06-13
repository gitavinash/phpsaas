<?php

$link_type = 'tag';
$tag_type = '';
$tag_description = '';

/** Pagination */

$currentPage = end($urlStructure);

if (is_numeric($currentPage)) {

    /** Remove page no from url */
    $tag_title = str_replace('-', ' ', $urlStructure[1]);
    $currentPage = (int)str_replace('-', ' ', $urlStructure[2]);
} else {

    $tag_title = str_replace('-', ' ', $urlStructure[1]);
    $currentPage = 1;
}

$currentPage = max(1, (int)$currentPage);

$sortBy = 'tag_time DESC, tag_id DESC';
if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'price_low':
            $sortBy = 'inventory_price ASC';
            break;
        case 'price_high':
            $sortBy = 'inventory_price DESC';
            break;
        case 'newest':
            $sortBy = 'tag_time DESC, tag_id DESC';
            break;
        case 'oldest':
            $sortBy = 'tag_time ASC, tag_id ASC';
            break;
    }
}


$searchTag = mysqli_real_escape_string($connection, str_replace('-', ' ', $urlStructure[1]));

// Count total result for pagination
if ($totalTagPost = mysqli_query($connection, "SELECT COUNT(DISTINCT tag_relation_id) AS total_result FROM " . TABLE_PREFIX . "tag INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=tag_relation_id AND link_site_id=" . SITE_ID . " LEFT JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=tag_relation_id AND inventory_site_id=" . SITE_ID . " LEFT JOIN " . TABLE_PREFIX . "product ON product_id=tag_relation_id AND product_site_id=" . SITE_ID . " AND product_status=1 WHERE tag_title LIKE '%{$searchTag}%' AND tag_site_id=" . SITE_ID)) {

    if (mysqli_num_rows($totalTagPost) != 0) {

        $totalRow = mysqli_fetch_assoc($totalTagPost);
        $total_result = (int)$totalRow['total_result'];
        $totalPages = ($total_result > 0) ? (int)ceil($total_result / PER_PAGE_LIMIT) : 0;

        define('TOTAL_PAGE', $totalPages);
        define('TOTAL_RESULT', $total_result);
    }
}

if ($tagList = mysqli_query($connection, "SELECT tag_title, tag_relation_id, tag_image, tag_description, tag_type FROM " . TABLE_PREFIX . "tag INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=tag_relation_id AND link_site_id=" . SITE_ID . " LEFT JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=tag_relation_id AND inventory_site_id=" . SITE_ID . " LEFT JOIN " . TABLE_PREFIX . "product ON product_id=tag_relation_id AND product_site_id=" . SITE_ID . " AND product_status=1 WHERE tag_title LIKE '%{$searchTag}%' AND tag_site_id=" . SITE_ID . " GROUP BY tag_relation_id ORDER BY {$sortBy} " . PAGINATION)) {

    if (mysqli_num_rows($tagList) != 0) {

        while ($tagPostData = mysqli_fetch_assoc($tagList)) {

            $tagPost[] = $tagPostData;
        }

        extract($tagPost[0]);

        if ($pageDetails = mysqli_query($connection, "SELECT tag_id, tag_title, tag_image, tag_description, tag_type FROM " . TABLE_PREFIX . "tag WHERE tag_title LIKE '%{$searchTag}%' AND tag_site_id=" . SITE_ID)) {

            if (mysqli_num_rows($pageDetails) != 0) {

                extract(mysqli_fetch_assoc($pageDetails));

                // Fetch tag seo data from database

                if ($checkTagMeta = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "meta WHERE meta_relation_tag='{$tag_title}' AND meta_type='tag' AND meta_site_id=" . SITE_ID)) {

                    if (mysqli_num_rows($checkTagMeta) != 0) {

                        extract(mysqli_fetch_assoc($checkTagMeta));
                    }
                }
            }
        }
    } else {

        $tagPost = '';
    }

    /** Include dedicated tag theme template */

    $tagTypeTheme = USER_PATH . "{$tag_type}-tag.php";
    $tagNameTheme = USER_PATH . str_replace(' ', '_', $searchTag) . "-tag.php";

    /** Include Tag view template */

    if (file_exists($tagNameTheme)) {

        require_once $tagNameTheme;
    } else if (file_exists(($tagTypeTheme))) {

        require_once $tagTypeTheme;
    } else {

        require_once USER_PATH . 'tag.php';
    }
} else {

    http_response_code(404);
    require_once ERROR_404;
}
