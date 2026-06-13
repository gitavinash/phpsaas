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

    // Step 1: Get all tag titles for the seed post
    $sqlSeedTags = "SELECT DISTINCT tag_title FROM isw_tag WHERE tag_type = '{$postTypeEsc}' AND tag_relation_id = {$postId} AND tag_site_id = {$siteId}";
    $resSeedTags = mysqli_query($conn, $sqlSeedTags);
    if (!$resSeedTags) {
        return [];
    }
    $seedTags = [];
    while ($row = mysqli_fetch_assoc($resSeedTags)) {
        $seedTags[] = $row['tag_title'];
    }
    mysqli_free_result($resSeedTags);

    if (empty($seedTags)) {
        return [];
    }

    // Build LIKE conditions for all tags
    // Build LIKE conditions for all tags (case-insensitive)
    $likeConditions = [];
    foreach ($seedTags as $tag) {
        $escaped = $conn->real_escape_string($tag);
        $likeConditions[] = "LOWER({$postTypeEsc}_title) LIKE '%" . mb_strtolower($escaped) . "%'";
    }
    $likeSql = implode(' OR ', $likeConditions);

    // Build IN clause for tag match
    $inTags = array_map(function ($t) use ($conn) {
        return "'" . $conn->real_escape_string($t) . "'";
    }, $seedTags);
    $inTagsCsv = implode(',', $inTags);

    // Get tag frequency for scoring
    $tagFreq = [];
    $sqlTagFreq = "SELECT tag_title, COUNT(*) AS freq FROM isw_tag WHERE tag_type = '{$postTypeEsc}' AND tag_site_id = {$siteId} AND tag_title IN ({$inTagsCsv}) GROUP BY tag_title";
    $resTagFreq = mysqli_query($conn, $sqlTagFreq);
    if ($resTagFreq) {
        while ($row = mysqli_fetch_assoc($resTagFreq)) {
            $tagFreq[$row['tag_title']] = (int)$row['freq'];
        }
        mysqli_free_result($resTagFreq);
    }

    $table = TABLE_PREFIX . $postTypeEsc;
    $link  = TABLE_PREFIX . "link";
    $profile  = TABLE_PREFIX . "profile";
    $category = TABLE_PREFIX . "category";
    $idField = $postTypeEsc . '_id';
    $titleField = $postTypeEsc . '_title';
    $descField = $postType . '_description';
    $catField = $postType . '_category';

    // Fetch a large pool of candidates to ensure at least 5 matches if possible
    $sqlData = "
        SELECT 
            {$idField}   AS id,
            {$postTypeEsc}_image AS image,
            {$titleField} AS title,
            {$descField} AS description,
            LEFT(
            TRIM(
                REGEXP_REPLACE(
                    REPLACE(
                        REPLACE({$descField}, '&nbsp;', ' '),
                        '\r\n',
                        ' '
                    ),
                    '<[^>]*>',
                    ''
                )
            ),
            200
        ) AS expert,
            link_url,
            link_published,
            profile_display_name,
            profile_image,
            category_title
        FROM {$table}
        INNER JOIN {$link}
            ON link_relation_id = {$idField}
           AND link_status = 200
           AND link_type   = '{$postTypeEsc}'
           AND link_site_id = {$siteId}
        INNER JOIN {$profile}
            ON link_owner_id=profile_user_id
        INNER JOIN {$category}
            ON category_id={$catField}
            AND category_type='{$postType}'
            AND category_site_id=" . SITE_ID . "
        WHERE {$idField} <> {$postId}
          AND {$postTypeEsc}_status = 1
          AND {$postTypeEsc}_site_id = {$siteId}
          AND (
                {$idField} IN (
                    SELECT tag_relation_id FROM isw_tag WHERE tag_type = '{$postTypeEsc}' AND tag_site_id = {$siteId} AND tag_title IN ({$inTagsCsv})
                )
                OR ({$likeSql})
          )
        ORDER BY link_published DESC
        LIMIT 200
    ";

    $resData = mysqli_query($conn, $sqlData);
    if (!$resData) {
        return [];
    }

    $out = [];
    while ($row = mysqli_fetch_assoc($resData)) {
        $score = 0;
        $titleLower = mb_strtolower($row['title']);
        $matchedTags = [];
        foreach ($seedTags as $tag) {
            $tagLower = mb_strtolower($tag);
            $inTag = false;
            // Tag match
            $sqlHasTag = "SELECT 1 FROM isw_tag WHERE tag_type = '{$postTypeEsc}' AND tag_site_id = {$siteId} AND tag_relation_id = " . (int)$row['id'] . " AND tag_title = '" . $conn->real_escape_string($tag) . "' LIMIT 1";
            $hasTagRes = mysqli_query($conn, $sqlHasTag);
            if ($hasTagRes && mysqli_fetch_assoc($hasTagRes)) {
                $score += 10 + ($tagFreq[$tag] ?? 1); // boost by tag frequency
                $inTag = true;
                $matchedTags[] = $tag;
            }
            if ($hasTagRes) mysqli_free_result($hasTagRes);
            // Title match
            if (strpos($titleLower, $tagLower) !== false) {
                $score += 5;
                if (!$inTag) $matchedTags[] = $tag;
            }
        }
        // Bonus if both tag and title match
        if (count($matchedTags) > 1) $score += 5;
        $row['score'] = $score;
        $row['matched_tags'] = $matchedTags;
        $out[] = $row;
    }
    mysqli_free_result($resData);

    // Sort by score DESC, then by latest date
    usort($out, function ($a, $b) {
        if ($b['score'] === $a['score']) {
            return strcmp($b['link_published'], $a['link_published']);
        }
        return $b['score'] - $a['score'];
    });


    // Ensure at least 5 articles if possible
    $minArticles = 2;
    $finalLimit = max($pageLimit, $minArticles);
    $paged = array_slice($out, $offset, $finalLimit);

    // Find most used tag
    $mostUsedKeyword = null;
    $maxFreq = 0;
    foreach ($tagFreq as $tag => $freq) {
        if ($freq > $maxFreq) {
            $mostUsedKeyword = $tag;
            $maxFreq = $freq;
        }
    }

    return array(
        'articles' => $paged,
        'mostUserKeyword' => $mostUsedKeyword,
        'allTags' => $seedTags
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
    $sliderFilter = !empty($sliderName)
        ? "slider_title='" . strtolower(str_replace(' ', '_', $sliderName)) . "' AND "
        : '';

    $devices = [SITE_VERSION];

    if (SITE_VERSION !== 'desktop') {
        $devices[] = 'desktop';
    }

    foreach ($devices as $device) {
        $query = "SELECT *
                  FROM " . TABLE_PREFIX . "slide
                  WHERE {$sliderFilter}
                        slide_status = 1
                        AND slider_device = '{$device}'
                        AND slide_site_id = " . SITE_ID . "
                  ORDER BY slide_update DESC";

        $resData = mysqli_query($conn, $query);

        if ($resData && mysqli_num_rows($resData) > 0) {
            $out = [];

            while ($row = mysqli_fetch_assoc($resData)) {
                $out[] = $row;
            }

            mysqli_free_result($resData);
            return $out;
        }
    }

    return [];
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

    mysqli_free_result($resData);

    return $out;
}

function getLatestArticles($conn, $limit = 4, $characterLimit = 250)
{
    $query = "SELECT 
        post_id AS id,
        post_title AS title,
        LEFT(
            TRIM(
                REGEXP_REPLACE(post_description, '<[^>]*>', '')
            ),
            {$characterLimit}
        ) AS description,
        post_image AS image,
        profile_display_name AS author,
        profile_image AS author_image,
        category_title AS category,
        category_link.link_url AS category_url,
        link.link_url,
        link.link_published

    FROM " . TABLE_PREFIX . "post

    INNER JOIN " . TABLE_PREFIX . "link AS link
        ON link.link_relation_id = post_id
       AND link.link_status = 200
       AND link.link_type = 'post'
       AND link.link_site_id = " . SITE_ID . "

    INNER JOIN " . TABLE_PREFIX . "category
        ON category_id = post_category
       AND category_type = 'post'
       AND category_site_id = " . SITE_ID . "

    LEFT JOIN " . TABLE_PREFIX . "link AS category_link
        ON category_link.link_relation_id = category_id
       AND category_link.link_type = 'category'
       AND category_link.link_status = 200
       AND category_link.link_site_id = " . SITE_ID . "

    INNER JOIN " . TABLE_PREFIX . "profile
        ON link.link_owner_id = profile_user_id

    WHERE post_status = 1
      AND post_site_id = " . SITE_ID . "

    ORDER BY link.link_published DESC
    LIMIT {$limit}";

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

function getData($conn, $dataType = 'page', $childDataType = 'page', $limit = 10, $featuredOnly = false, $characterLimit = 250)
{
    $showOnlyFeatured = $featuredOnly ? " link.link_is_featured DESC " : ' link.link_is_featured ASC ';

    $query = "SELECT 
        {$dataType}_id AS id,
        {$dataType}_title AS title,
        LEFT(
            TRIM(
                REGEXP_REPLACE({$dataType}_description, '<[^>]*>', '')
            ),
            {$characterLimit}
        ) AS description,
        {$dataType}_image AS image,
        profile_display_name AS author,
        profile_image AS author_image,
        category_title AS category,
        category_link.link_url AS category_url,
        link.link_url,
        link.link_published

    FROM " . TABLE_PREFIX . "{$dataType}

    INNER JOIN " . TABLE_PREFIX . "link AS link
        ON link.link_relation_id = {$dataType}_id
       AND link.link_status = 200
       AND link.link_type = '{$dataType}'
       AND link.link_site_id = " . SITE_ID . "

    INNER JOIN " . TABLE_PREFIX . "category
        ON category_id = {$dataType}_category
       AND category_type = '{$childDataType}'
       AND category_site_id = " . SITE_ID . "

    LEFT JOIN " . TABLE_PREFIX . "link AS category_link
        ON category_link.link_relation_id = category_id
       AND category_link.link_type = 'category'
       AND category_link.link_status = 200
       AND category_link.link_site_id = " . SITE_ID . "

    INNER JOIN " . TABLE_PREFIX . "profile
        ON link.link_owner_id = profile_user_id

    WHERE {$dataType}_status = 1
    AND {$dataType}_type='{$childDataType}'
      AND {$dataType}_site_id = " . SITE_ID . "

    ORDER BY {$showOnlyFeatured}, link.link_published DESC
    LIMIT {$limit}";

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

function getCategoriesWithPosts(
    mysqli $conn,
    string $tableName = 'post',
    string $dataType = 'post',
    bool $featuredOnly = false,
    int $categoryLimit = 3,
    int $postLimit = 5
) {
    $featuredCategory = $featuredOnly
        ? "cl.link_is_featured DESC"
        : "cl.link_is_featured ASC";

    $featuredContent = $featuredOnly
        ? "pl.link_is_featured DESC"
        : "pl.link_is_featured ASC";

    // Get categories first
    $categoryQuery = "
        SELECT
            c.category_id,
            c.category_title,
            cl.link_url AS category_url

        FROM " . TABLE_PREFIX . "category c

        INNER JOIN " . TABLE_PREFIX . "link cl
            ON cl.link_relation_id = c.category_id
           AND cl.link_type = 'category'
           AND cl.link_status = 200
           AND cl.link_site_id = " . SITE_ID . "

        WHERE c.category_type = '{$dataType}'
          AND c.category_site_id = " . SITE_ID . "

        ORDER BY {$featuredCategory}, c.category_title ASC
        LIMIT {$categoryLimit}
    ";

    $categoryResult = mysqli_query($conn, $categoryQuery);

    if (!$categoryResult) {
        return [];
    }

    $categories = [];
    $categoryIds = [];

    while ($row = mysqli_fetch_assoc($categoryResult)) {

        $categoryId = (int)$row['category_id'];

        $categories[$categoryId] = [
            'category_id'    => $categoryId,
            'category_title' => $row['category_title'],
            'category_url'   => $row['category_url'],
            $tableName . 's' => []
        ];

        $categoryIds[] = $categoryId;
    }

    mysqli_free_result($categoryResult);

    if (empty($categoryIds)) {
        return [];
    }

    $categoryIdsSql = implode(',', array_map('intval', $categoryIds));

    // Get all content for selected categories
    $contentQuery = "
        SELECT
            p.{$tableName}_category AS category_id,

            p.{$tableName}_id AS content_id,
            p.{$tableName}_title AS content_title,
            p.{$tableName}_image AS content_image,
            p.{$tableName}_description AS content_description,

            pl.link_url AS content_url,
            pl.link_published

        FROM " . TABLE_PREFIX . "{$tableName} p

        INNER JOIN " . TABLE_PREFIX . "link pl
            ON pl.link_relation_id = p.{$tableName}_id
           AND pl.link_type = '{$tableName}'
           AND pl.link_status = 200
           AND pl.link_site_id = " . SITE_ID . "

        WHERE p.{$tableName}_status = 1
          AND p.{$tableName}_site_id = " . SITE_ID . "
          AND p.{$tableName}_category IN ({$categoryIdsSql})

        ORDER BY
            {$featuredContent},
            p.{$tableName}_category, 
            pl.link_published DESC
    ";

    $contentResult = mysqli_query($conn, $contentQuery);

    if (!$contentResult) {
        return array_values($categories);
    }

    while ($row = mysqli_fetch_assoc($contentResult)) {

        $categoryId = (int)$row['category_id'];

        if (!isset($categories[$categoryId])) {
            continue;
        }

        if (count($categories[$categoryId][$tableName . 's']) >= $postLimit) {
            continue;
        }

        $categories[$categoryId][$tableName . 's'][] = [
            $tableName . '_id'    => (int)$row['content_id'],
            $tableName . '_title' => $row['content_title'],
            $tableName . '_image' => $row['content_image'],
            $tableName . '_description' => $row['content_description'],
            $tableName . '_url'   => $row['content_url'],
            'link_published'      => $row['link_published']
        ];
    }

    mysqli_free_result($contentResult);

    return array_values($categories);
}


function getGalleryWithCategories(
    mysqli $conn,
    string $tableName = 'post',
    string $dataType = 'post',
    bool $featuredOnly = false,
    int $categoryLimit = 3,
    int $postLimit = 5
) {
    $featuredCategory = $featuredOnly
        ? "cl.link_is_featured DESC"
        : "cl.link_is_featured ASC";

    $featuredContent = $featuredOnly
        ? "pl.link_is_featured DESC"
        : "pl.link_is_featured ASC";

    // Get categories first
    $categoryQuery = "
        SELECT
            c.category_id,
            c.category_title,
            cl.link_url AS category_url

        FROM " . TABLE_PREFIX . "category c

        INNER JOIN " . TABLE_PREFIX . "link cl
            ON cl.link_relation_id = c.category_id
           AND cl.link_type = 'category'
           AND cl.link_status = 200
           AND cl.link_site_id = " . SITE_ID . "

        WHERE c.category_type = '{$dataType}'
          AND c.category_site_id = " . SITE_ID . "

        ORDER BY {$featuredCategory}, c.category_title ASC
        LIMIT {$categoryLimit}
    ";

    $categoryResult = mysqli_query($conn, $categoryQuery);

    if (!$categoryResult) {
        return [];
    }

    $categories = [];
    $categoryIds = [];

    while ($row = mysqli_fetch_assoc($categoryResult)) {

        $categoryId = (int)$row['category_id'];

        $categories[$categoryId] = [
            'category_id'    => $categoryId,
            'category_title' => $row['category_title'],
            'category_url'   => $row['category_url'],
            $tableName . 's' => []
        ];

        $categoryIds[] = $categoryId;
    }

    mysqli_free_result($categoryResult);

    if (empty($categoryIds)) {
        return [];
    }

    $categoryIdsSql = implode(',', array_map('intval', $categoryIds));

    // Get all content for selected categories
    $contentQuery = "
        SELECT
            p.{$tableName}_category AS category_id,

            p.{$tableName}_id AS content_id,
            p.{$tableName}_title AS content_title,
            p.{$tableName}_image AS content_image,
            p.{$tableName}_description AS content_description,
            p.{$tableName}_image_list AS content_image_list,

            pl.link_url AS content_url,
            pl.link_published

        FROM " . TABLE_PREFIX . "{$tableName} p

        INNER JOIN " . TABLE_PREFIX . "link pl
            ON pl.link_relation_id = p.{$tableName}_id
           AND pl.link_type = '{$tableName}'
           AND pl.link_status = 200
           AND pl.link_site_id = " . SITE_ID . "

        WHERE p.{$tableName}_status = 1
          AND p.{$tableName}_site_id = " . SITE_ID . "
          AND p.{$tableName}_category IN ({$categoryIdsSql})

        ORDER BY
            {$featuredContent},
            p.{$tableName}_category, 
            pl.link_published DESC
    ";

    $contentResult = mysqli_query($conn, $contentQuery);

    if (!$contentResult) {
        return array_values($categories);
    }

    while ($row = mysqli_fetch_assoc($contentResult)) {

        $categoryId = (int)$row['category_id'];

        if (!isset($categories[$categoryId])) {
            continue;
        }

        if (count($categories[$categoryId][$tableName . 's']) >= $postLimit) {
            continue;
        }

        $categories[$categoryId][$tableName . 's'][] = [
            $tableName . '_id'    => (int)$row['content_id'],
            $tableName . '_title' => $row['content_title'],
            $tableName . '_image' => $row['content_image'],
            $tableName . '_description' => $row['content_description'],
            $tableName . '_image_list' => $row['content_image_list'],
            $tableName . '_url'   => $row['content_url'],
            'link_published'      => $row['link_published']
        ];
    }

    mysqli_free_result($contentResult);

    return array_values($categories);
}
