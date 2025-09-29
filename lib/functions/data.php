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
            link_url,
            profile_display_name
        FROM {$table}
        INNER JOIN {$link}
            ON link_relation_id = {$postTypeEsc}_id
           AND link_status = 200
           AND link_type   = '{$postTypeEsc}'
           AND link_site_id = {$siteId}
        INNER JOIN {$profile}
            ON link_owner_id=profile_user_id
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

    return $out;
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