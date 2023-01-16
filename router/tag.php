<?php

$link_type = 'tag';
$tag_type = '';
$tag_description = '';

/** Pagination */

$currentPage = end($urlStructure);

if (is_numeric($currentPage)) {

    /** Remove page no from url */
    $tag_title = str_replace('-', ' ', $urlStructure[1]);
    $currentPage = str_replace('-', ' ', $urlStructure[2]);
} else {

    $tag_title = str_replace('-', ' ', $urlStructure[1]);
    $currentPage = 0;
}

$searchTag = mysqli_real_escape_string($connection, str_replace('-', ' ', $urlStructure[1]));

if ($tagList = mysqli_query($connection, "SELECT tag_title, tag_relation_id, tag_image, tag_description, tag_type FROM " . TABLE_PREFIX . "tag WHERE tag_title LIKE '%{$searchTag}%' AND tag_site_id=" . SITE_ID . " ORDER BY tag_time DESC " . PAGINATION)) {

    if (mysqli_num_rows($tagList) != 0) {

        while ($tagPostData = mysqli_fetch_assoc($tagList)) {

            $tagPost[] = $tagPostData;
        }

        extract($tagPost[0]);

        // Count total result for pagination

        if ($totalCategoryPost = mysqli_query($connection, "SELECT count(tag_id) as total_result FROM " . TABLE_PREFIX . "tag WHERE tag_title LIKE '%{$searchTag}%' AND tag_site_id=" . SITE_ID . " ORDER BY tag_time DESC")) {

            if (mysqli_num_rows($totalCategoryPost) != 0) {

                extract(mysqli_fetch_assoc($totalCategoryPost));

                define('TOTAL_PAGE', round($total_result / PER_PAGE_LIMIT));
                define('TOTAL_RESULT', $total_result);
            }
        }

        // Extract first result for page meta's

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
    $tagNameTheme = USER_PATH . "{$tag_type}-tag.php";

    /** Include Tag view template */

    if (file_exists($tagTypeTheme)) {

        require_once $tagTypeTheme;
    } else if (file_exists(($tagNameTheme))) {
        require_once $tagNameTheme;
    } else {

        require_once USER_PATH . 'tag.php';
    }
} else {

    http_response_code(404);
    require_once ERROR_404;
}