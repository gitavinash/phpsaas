<?php
function getCategoryWithTotalPost($connection, $postType = 'post', $pageLimit = 10)
{
    $siteId = (int) SITE_ID;
    $limit  = max(1, min(100, (int) $pageLimit));
    $prefix = TABLE_PREFIX;

    $sql = "
        SELECT
            c.category_title,
            c.category_image,
            l.link_url,
            COUNT(d.discussion_id) AS totalCount
        FROM {$prefix}category AS c
        JOIN {$prefix}link AS l
          ON l.link_relation_id = c.category_id
         AND l.link_status      = 200
         AND l.link_type        = 'category'
         AND l.link_site_id     = ?
        LEFT JOIN {$prefix}discussion AS d
          ON d.discussion_category = c.category_id
         AND d.discussion_status   = 1
         AND d.discussion_site_id  = ?
        WHERE c.category_status = 1
          AND c.category_type   = ?
          AND c.category_site_id= ?
        GROUP BY
            c.category_id, c.category_title, l.link_url
        ORDER BY
            l.link_is_featured DESC,
            totalCount DESC,
            c.category_title ASC
        LIMIT ?
    ";

    if (!$stmt = mysqli_prepare($connection, $sql)) {
        return [];
    }

    mysqli_stmt_bind_param($stmt, 'iisii', $siteId, $siteId, $postType, $siteId, $limit);

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return [];
    }

    $result = mysqli_stmt_get_result($stmt);
    $rows   = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $row['totalCount'] = (int) ($row['totalCount'] ?? 0);
            $rows[] = $row;
        }
    }
    mysqli_stmt_close($stmt);

    return $rows;
}

function getAllCategoryList($connection, $postType = 'post', $pageLimit = 1000)
{
    $categoryListData = array();
    if ($categoryList = mysqli_query($connection, "SELECT category_id, category_title, link_url, category_type, link_is_featured, link_published, link_modified FROM " . TABLE_PREFIX . "category INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=category_id AND link_site_id=" . SITE_ID . " AND link_status=200 AND link_type='category' WHERE category_site_id=" . SITE_ID . " AND category_status=1 ORDER BY category_title ASC")) {
        if (mysqli_num_rows($categoryList) != 0) {
            while ($categoryData = mysqli_fetch_assoc($categoryList)) {
                $categoryListData[] = $categoryData;
            }
        }
    }

    return $categoryListData;
}