<?php

/**
 * Find related items by matching tag titles of a seed relation.
 * - Uses only mysqli_query.
 * - Page number is ZERO-based.
 * - Scopes to SITE_ID and same $postType.
 *
 * @param mysqli $conn
 * @param int    $postId
 * @param string $postType
 * @param int    $pageLimit
 * @param int    $pageNumber
 * @return array               List of ['image','title','link_url','id'] rows (empty if none)
 */
function similarArticles($conn, $postId, $postType = 'post', $pageLimit = 10, $pageNumber = 0)
{
    $postId     = (int)$postId;
    $pageLimit  = max(1, (int)$pageLimit);
    $pageNumber = max(0, (int)$pageNumber);
    $offset     = $pageNumber * $pageLimit;
    $mostUsedKeyword = null;

    $postTypeEsc = $conn->real_escape_string($postType);
    $siteId      = (int)SITE_ID;

    // Step 1: collect related relation_ids by tag-title overlap (exclude seed)
    $sqlIds = "
        WITH seed AS (
            SELECT DISTINCT tag_title
            FROM isw_tag
            WHERE tag_type = '{$postTypeEsc}'
              AND tag_relation_id = {$postId}
              AND tag_site_id = {$siteId}
        )
        SELECT DISTINCT t.tag_relation_id
        FROM isw_tag t
        JOIN seed s ON t.tag_title = s.tag_title
        WHERE t.tag_type = '{$postTypeEsc}'
          AND t.tag_site_id = {$siteId}
          AND t.tag_relation_id <> {$postId}
        ORDER BY t.tag_relation_id ASC
        LIMIT {$pageLimit} OFFSET {$offset}
    ";

    $resIds = mysqli_query($conn, $sqlIds);
    if (!$resIds) {
        // echo mysqli_error($conn); // uncomment for debugging
        return [];
    }

    $ids = [];
    while ($r = mysqli_fetch_assoc($resIds)) {
        $ids[] = (int)$r['tag_relation_id'];
    }
    mysqli_free_result($resIds);

    if (!$ids) {
        return [];
    }

    // Step 2: fetch exact data for those IDs from {TABLE_PREFIX}{$postType} + link
    // Build CSV and keep order via FIELD() so output order matches the IDs list
    $idsCsv   = implode(',', $ids);
    $orderSql = "FIELD({$postTypeEsc}_id, {$idsCsv})";

    $table = TABLE_PREFIX . $postTypeEsc;
    $link  = TABLE_PREFIX . "link";
    $profile  = TABLE_PREFIX . "profile";

    $sqlData = "
        SELECT 
            {$postTypeEsc}_id   AS id,
            {$postTypeEsc}_image AS image,
            {$postTypeEsc}_title AS title,
            {$postType}_description AS description,
            link_url,
            link_published,
            profile_display_name,
            category_title
        FROM {$table}
        INNER JOIN {$link}
            ON link_relation_id = {$postTypeEsc}_id
           AND link_status = 200
           AND link_type   = '{$postTypeEsc}'
           AND link_site_id = {$siteId}
        INNER JOIN {$profile}
            ON link_owner_id=profile_user_id
        INNER JOIN " . TABLE_PREFIX . "category
            ON category_id={$postType}_category
            AND category_type='{$postType}'
            AND category_site_id=" . SITE_ID . "
        WHERE {$postTypeEsc}_id IN ({$idsCsv})
          AND {$postTypeEsc}_status = 1
          AND {$postTypeEsc}_site_id = {$siteId}
        ORDER BY {$orderSql}
    ";

    $resData = mysqli_query($conn, $sqlData);
    if (!$resData) {
        // echo mysqli_error($conn); // uncomment for debugging
        return [];
    }

    $out = [];
    while ($row = mysqli_fetch_assoc($resData)) {
        $out[] = $row;
    }
    mysqli_free_result($resData);

    if (!empty($ids)) {
        $idsCsvForTag = implode(',', $ids);
        $sqlKeyword = "
            SELECT tag_title, COUNT(*) AS tag_count
            FROM isw_tag
            WHERE tag_type = '{$postTypeEsc}'
              AND tag_site_id = {$siteId}
              AND tag_relation_id IN ({$idsCsvForTag})
            GROUP BY tag_title
            ORDER BY tag_count DESC
            LIMIT 1
        ";

        $resKeyword = mysqli_query($conn, $sqlKeyword);
        if ($resKeyword && $row = mysqli_fetch_assoc($resKeyword)) {
            $mostUsedKeyword = $row['tag_title'];
        }
        mysqli_free_result($resKeyword);
    }

    return array(
        'articles' => $out,
        'mostUserKeyword' => $mostUsedKeyword
    );
}

function authorInfo($conn, $user_id, $postType = 'post')
{
    $user_id   = (int)$user_id;
    $site_id   = (int)SITE_ID;
    $postType  = $conn->real_escape_string($postType);

    $checkUserQuery = "SELECT 
            profile_image, 
            profile_display_name, 
            user_reg_time, 
            COUNT({$postType}_id) AS totalPost 
        FROM " . TABLE_PREFIX . "profile
        INNER JOIN " . TABLE_PREFIX . "user 
            ON profile_user_id = user_id
        LEFT JOIN " . TABLE_PREFIX . "{$postType} 
            ON {$postType}_owner_id = profile_user_id
            AND {$postType}_status = 1
            AND {$postType}_site_id = {$site_id}
        WHERE profile_user_id = {$user_id}";

    $resData = mysqli_query($conn, $checkUserQuery);
    if (!$resData) {
        // Debugging only, comment out in production
        // echo mysqli_error($conn);
        return [];
    }

    $out = [];
    while ($row = mysqli_fetch_assoc($resData)) {
        $out[] = $row;
    }
    mysqli_free_result($resData);

    return $out;
}

function popularTags($conn, $type = 'post', $pageLimit = 10)
{
    $query = "SELECT 
    tag_title,
    COUNT(*) AS total_usage
FROM isw_tag
WHERE tag_site_id = " . SITE_ID . "
  AND tag_type = '{$type}'
GROUP BY tag_title
ORDER BY total_usage DESC
LIMIT {$pageLimit}";

    $resData = mysqli_query($conn, $query);
    if (!$resData) {
        return [];
    }

    $out = [];
    while ($row = mysqli_fetch_assoc($resData)) {
        $out[] = $row;
    }
    mysqli_free_result($resData);

    return $out;
}

function testimonialsList($conn, $limit = 5, $featuredOnly = 0)
{

    $query = "SELECT * FROM " . TABLE_PREFIX . "category WHERE category_type='testimonial' AND category_status=1 AND category_site_id=" . SITE_ID . " LIMIT 0, {$limit}";

    $resData = mysqli_query($conn, $query);
    if (!$resData) {
        return [];
    }

    $out = [];
    while ($row = mysqli_fetch_assoc($resData)) {
        $out[] = $row;
    }
    mysqli_free_result($resData);

    return $out;
}

function getSlider($conn, $sliderName = '')
{

    $sliderFilter = (($sliderName != '') ? "slider_title='" . strtolower(str_replace(' ', '_', $sliderName)) . "' AND " : '');
    $query = "SELECT * FROM " . TABLE_PREFIX . "slide WHERE {$sliderFilter} slide_status=1 AND slider_device='" . SITE_VERSION . "' AND slide_site_id=" . SITE_ID . " ORDER BY slide_update DESC";

    $resData = mysqli_query($conn, $query);
    if (!$resData) {
        return [];
    }

    $out = [];
    while ($row = mysqli_fetch_assoc($resData)) {
        $out[] = $row;
    }
    mysqli_free_result($resData);

    return $out;
}

function getHomepageContent($conn)
{
    $query = "SELECT page_description as homepageContent FROM " . TABLE_PREFIX . "page WHERE page_title='homepage' AND page_status=1 AND page_site_id=" . SITE_ID;

    $resData = mysqli_query($conn, $query);
    if (!$resData) {
        return [];
    } else {
        $out = mysqli_fetch_assoc($resData);
    }

    mysqli_free_result($resData);

    return $out;
}

function getCategoryPost($conn, $categoryId = 0, $categoryType = 'post', $sortBy = 'new', $dateFilter = 'all', $pageNo = 0)
{
    if ($categoryId == 0) {
        return [];
    }

    switch ($sortBy) {
        case 'old':
            $orderBy = 'link_published ASC';
            break;
        case 'popular':
            $orderBy = 'post_viewed DESC, link_published DESC';
            break;
        case 'replies':
            $orderBy = 'post_replies DESC, link_published DESC';
            break;
        case 'likes':
            $orderBy = 'post_likes DESC, link_published DESC';
            break;
        default:
            $orderBy = 'link_published DESC';
            break;
    }

    switch ($dateFilter) {
        case 'today':
            // From today 00:00:00 to today 23:59:59
            $dateSql = "AND link_published BETWEEN CONCAT(CURDATE(), ' 00:00:00') 
                    AND CONCAT(CURDATE(), ' 23:59:59')";
            break;

        case 'week':
            // From 7 days ago 00:00:00 to today 23:59:59
            $dateSql = "AND link_published BETWEEN DATE_SUB(CONCAT(CURDATE(), ' 00:00:00'), INTERVAL 7 DAY) 
                    AND CONCAT(CURDATE(), ' 23:59:59')";
            break;

        case 'month':
            // From 1 month ago 00:00:00 to today 23:59:59
            $dateSql = "AND link_published BETWEEN DATE_SUB(CONCAT(CURDATE(), ' 00:00:00'), INTERVAL 1 MONTH) 
                    AND CONCAT(CURDATE(), ' 23:59:59')";
            break;

        case 'year':
            // From 1 year ago 00:00:00 to today 23:59:59
            $dateSql = "AND link_published BETWEEN DATE_SUB(CONCAT(CURDATE(), ' 00:00:00'), INTERVAL 1 YEAR) 
                    AND CONCAT(CURDATE(), ' 23:59:59')";
            break;

        default:
            $dateSql = "";
            break;
    }

    $categoryQuery = "SELECT * FROM " . TABLE_PREFIX . "post WHERE post_category={$categoryId} AND post_type='{$categoryType}' AND post_status=1 AND post_site_id=" . SITE_ID;

    $resData = mysqli_query($conn, $categoryQuery);
    if (!$resData) {
        return [];
    } else {
        $out = mysqli_fetch_assoc($resData);
    }

    echo $categoryQuery;
    mysqli_free_result($resData);

    return $out;
}

function getCategoryDiscussion($conn, $categoryId = 0, $categoryType = 'discussion', $sortBy = 'new', $dateFilter = 'all', $pageNo = 0)
{
    if ($categoryId == 0) {
        return [];
    }

    switch ($sortBy) {
        case 'old':
            $orderBy = 'link_published ASC';
            break;
        case 'popular':
            $orderBy = 'post_viewed DESC, link_published DESC';
            break;
        case 'replies':
            $orderBy = 'post_replies DESC, link_published DESC';
            break;
        case 'likes':
            $orderBy = 'post_likes DESC, link_published DESC';
            break;
        default:
            $orderBy = 'link_published DESC';
            break;
    }

    switch ($dateFilter) {
        case 'today':
            // From today 00:00:00 to today 23:59:59
            $dateSql = "AND link_published BETWEEN CONCAT(CURDATE(), ' 00:00:00') 
                    AND CONCAT(CURDATE(), ' 23:59:59')";
            break;

        case 'week':
            // From 7 days ago 00:00:00 to today 23:59:59
            $dateSql = "AND link_published BETWEEN DATE_SUB(CONCAT(CURDATE(), ' 00:00:00'), INTERVAL 7 DAY) 
                    AND CONCAT(CURDATE(), ' 23:59:59')";
            break;

        case 'month':
            // From 1 month ago 00:00:00 to today 23:59:59
            $dateSql = "AND link_published BETWEEN DATE_SUB(CONCAT(CURDATE(), ' 00:00:00'), INTERVAL 1 MONTH) 
                    AND CONCAT(CURDATE(), ' 23:59:59')";
            break;

        case 'year':
            // From 1 year ago 00:00:00 to today 23:59:59
            $dateSql = "AND link_published BETWEEN DATE_SUB(CONCAT(CURDATE(), ' 00:00:00'), INTERVAL 1 YEAR) 
                    AND CONCAT(CURDATE(), ' 23:59:59')";
            break;

        default:
            $dateSql = "";
            break;
    }

    $categoryQuery = "SELECT * FROM " . TABLE_PREFIX . "discussion WHERE discussion_category={$categoryId} AND discussion_type='{$categoryType}' AND discussion_status=1 AND discussion_site_id=" . SITE_ID;

    $resData = mysqli_query($conn, $categoryQuery);
    if (!$resData) {
        return [];
    } else {
        $out = mysqli_fetch_assoc($resData);
    }

    echo $categoryQuery;
    mysqli_free_result($resData);

    return $out;
}