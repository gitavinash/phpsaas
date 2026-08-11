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

    // Step 1: Get all tag titles for the seed post
    $sqlSeedTags = "SELECT DISTINCT tag_title 
                    FROM isw_tag 
                    WHERE tag_type = '{$postTypeEsc}' 
                      AND tag_relation_id = {$postId} 
                      AND tag_site_id = {$siteId}";

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

    // Step 2: Build IN clause for exact DB tag matching
    $inTags = array_map(function ($t) use ($conn) {
        return "'" . $conn->real_escape_string($t) . "'";
    }, $seedTags);
    $inTagsCsv = implode(',', $inTags);

    // Step 3: Get tag frequency for scoring
    $tagFreq = [];
    $sqlTagFreq = "SELECT tag_title, COUNT(*) AS freq 
                   FROM isw_tag 
                   WHERE tag_type = '{$postTypeEsc}' 
                     AND tag_site_id = {$siteId} 
                     AND tag_title IN ({$inTagsCsv}) 
                   GROUP BY tag_title";

    $resTagFreq = mysqli_query($conn, $sqlTagFreq);
    if ($resTagFreq) {
        while ($row = mysqli_fetch_assoc($resTagFreq)) {
            $tagFreq[$row['tag_title']] = (int)$row['freq'];
        }
        mysqli_free_result($resTagFreq);
    }

    $table    = TABLE_PREFIX . $postTypeEsc;
    $link     = TABLE_PREFIX . "link";
    $profile  = TABLE_PREFIX . "profile";
    $category = TABLE_PREFIX . "category";

    $idField    = $postTypeEsc . '_id';
    $titleField = $postTypeEsc . '_title';
    $descField  = $postType . '_description';
    $catField   = $postType . '_category';

    // Step 4: Dynamically construct SQL calculation rules
    $poolLikeConditions  = [];
    $whenClauses         = [];
    $titleScoreExprs     = [];
    $matchCountExprs     = [];
    $matchedTagsCsvExprs = [];

    foreach ($seedTags as $tag) {
        $tagEsc   = $conn->real_escape_string($tag);
        $tagLower = mb_strtolower($tagEsc);
        $freq     = $tagFreq[$tag] ?? 1;

        // 1. LIKE conditions for candidate pool WHERE clause (evaluated on 'main' alias)
        $poolLikeConditions[] = "LOWER(main.{$titleField}) LIKE '%{$tagLower}%'";

        // 2. CTE dynamic score
        $whenClauses[] = "WHEN '{$tagEsc}' THEN " . (10 + $freq);

        // 3. Title matching score (evaluated on 'cp' alias in ScoredResults)
        $inTitle = "(LOWER(cp.title) LIKE '%{$tagLower}%')";
        $titleScoreExprs[] = "IF({$inTitle}, 5, 0)";

        // 4. Safely check if tag exists in either DB tags OR title
        $inDb = "(tm.db_tags IS NOT NULL AND LOCATE('|||{$tagEsc}|||', CONCAT('|||', tm.db_tags, '|||')) > 0)";
        $isMatched = "({$inDb} OR {$inTitle})";

        // Track unique matches
        $matchCountExprs[] = "IF({$isMatched}, 1, 0)";
        $matchedTagsCsvExprs[] = "IF({$isMatched}, '{$tagEsc}', NULL)";
    }

    $poolLikeSql       = implode(' OR ', $poolLikeConditions);
    $whenSql           = implode(' ', $whenClauses);
    $titleScoreSql     = implode(' + ', $titleScoreExprs);
    $matchCountSql     = implode(' + ', $matchCountExprs);
    $matchedTagsCsvSql = "CONCAT_WS('|||', " . implode(', ', $matchedTagsCsvExprs) . ")";

    // Highly optimized subquery to locate tags for the candidate pool limit
    $existsTagSql = "EXISTS (
        SELECT 1 
        FROM isw_tag t 
        WHERE t.tag_relation_id = main.{$idField} 
          AND t.tag_type = '{$postTypeEsc}' 
          AND t.tag_site_id = {$siteId} 
          AND t.tag_title IN ({$inTagsCsv})
    )";

    $minArticles = 2;
    $finalLimit  = max($pageLimit, $minArticles);

    // Step 5: Unified super-query resolving the UI sorting bug
    $sqlData = "
        WITH CandidatePool AS (
            -- Phase 1: Only look at the 200 most recent articles to prevent surfacing old data
            SELECT 
                main.{$idField} AS id,
                main.{$postTypeEsc}_image AS image,
                main.{$titleField} AS title,
                main.{$descField} AS description,
                l.link_url,
                l.link_published,
                p.profile_display_name,
                p.profile_image,
                c.category_title
            FROM {$table} main
            INNER JOIN {$link} l
                ON l.link_relation_id = main.{$idField}
               AND l.link_status = 200
               AND l.link_type = '{$postTypeEsc}'
               AND l.link_site_id = {$siteId}
            INNER JOIN {$profile} p
                ON l.link_owner_id = p.profile_user_id
            INNER JOIN {$category} c
                ON c.category_id = main.{$catField}
               AND c.category_type = '{$postType}'
               AND c.category_site_id = {$siteId}
            WHERE main.{$idField} <> {$postId}
              AND main.{$postTypeEsc}_status = 1
              AND main.{$postTypeEsc}_site_id = {$siteId}
              AND (
                  {$existsTagSql}
                  OR ({$poolLikeSql})
              )
            ORDER BY l.link_published DESC
            LIMIT 200
        ),
        TagMatches AS (
            -- Phase 2: Calculate matches efficiently against the small 200-item pool
            SELECT 
                t.tag_relation_id,
                SUM(CASE t.tag_title {$whenSql} ELSE 0 END) AS tag_score,
                GROUP_CONCAT(t.tag_title SEPARATOR '|||') AS db_tags
            FROM isw_tag t
            INNER JOIN CandidatePool cp ON t.tag_relation_id = cp.id
            WHERE t.tag_type = '{$postTypeEsc}' 
              AND t.tag_site_id = {$siteId} 
              AND t.tag_title IN ({$inTagsCsv})
            GROUP BY t.tag_relation_id
        ),
        ScoredResults AS (
            -- Phase 3: Pick the highest-scoring matches out of the candidate pool
            SELECT 
                cp.id,
                cp.image,
                cp.title,
                cp.description,
                cp.link_url,
                cp.link_published,
                cp.profile_display_name,
                cp.profile_image,
                cp.category_title,
                (COALESCE(tm.tag_score, 0) + ({$titleScoreSql}) + IF(({$matchCountSql}) > 1, 5, 0)) AS score,
                ({$matchedTagsCsvSql}) AS matched_tags_csv
            FROM CandidatePool cp
            LEFT JOIN TagMatches tm ON cp.id = tm.tag_relation_id
            ORDER BY score DESC, cp.link_published DESC
            LIMIT {$offset}, {$finalLimit}
        )
        -- Phase 4: Guarantee UI chronolgy. Re-sort the finalized page purely by date.
        SELECT 
            sr.id,
            sr.image,
            sr.title,
            sr.description,
            LEFT(TRIM(REGEXP_REPLACE(REPLACE(REPLACE(sr.description, '&nbsp;', ' '), '\r\n', ' '), '<[^>]*>', '')), 200) AS expert,
            sr.link_url,
            sr.link_published,
            sr.profile_display_name,
            sr.profile_image,
            sr.category_title,
            sr.score,
            sr.matched_tags_csv
        FROM ScoredResults sr
        ORDER BY sr.link_published DESC
    ";

    $resData = mysqli_query($conn, $sqlData);
    if (!$resData) {
        return [];
    }

    $paged = [];
    while ($row = mysqli_fetch_assoc($resData)) {
        // Expand SQL's concatenated matches back to an array
        $row['matched_tags'] = !empty($row['matched_tags_csv']) ? explode('|||', $row['matched_tags_csv']) : [];
        unset($row['matched_tags_csv']);

        $row['score'] = (int)$row['score'];
        $paged[] = $row;
    }
    mysqli_free_result($resData);

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

function getNewsCategoryPost($conn, $categoryId = 0, $categoryType = 'post', $sortBy = 'new', $dateFilter = 'all', $pageNo = 0, $limit = 10)
{
    if ($categoryId == 0) {
        return [];
    }

    $categoryIds = is_array($categoryId)
        ? implode(',', $categoryId)
        : (is_object($categoryId) ? ($categoryId->id ?? 0) : $categoryId);

    switch ($sortBy) {
        case 'old':
            $orderBy = 'ORDER BY news_published ASC';
            break;
        case 'popular':
            $orderBy = 'ORDER BY post_viewed DESC, news_published DESC';
            break;
        case 'replies':
            $orderBy = 'ORDER BY post_replies DESC, news_published DESC';
            break;
        case 'likes':
            $orderBy = 'ORDER BY post_likes DESC, news_published DESC';
            break;
        default:
            $orderBy = 'ORDER BY news_published DESC';
            break;
    }

    switch ($dateFilter) {
        case 'today':
            // From today 00:00:00 to today 23:59:59
            $dateSql = "AND news_published BETWEEN CONCAT(CURDATE(), ' 00:00:00') 
                    AND CONCAT(CURDATE(), ' 23:59:59')";
            break;

        case 'week':
            // From 7 days ago 00:00:00 to today 23:59:59
            $dateSql = "AND news_published BETWEEN DATE_SUB(CONCAT(CURDATE(), ' 00:00:00'), INTERVAL 7 DAY) 
                    AND CONCAT(CURDATE(), ' 23:59:59')";
            break;

        case 'month':
            // From 1 month ago 00:00:00 to today 23:59:59
            $dateSql = "AND news_published BETWEEN DATE_SUB(CONCAT(CURDATE(), ' 00:00:00'), INTERVAL 1 MONTH) 
                    AND CONCAT(CURDATE(), ' 23:59:59')";
            break;

        case 'year':
            // From 1 year ago 00:00:00 to today 23:59:59
            $dateSql = "AND news_published BETWEEN DATE_SUB(CONCAT(CURDATE(), ' 00:00:00'), INTERVAL 1 YEAR) 
                    AND CONCAT(CURDATE(), ' 23:59:59')";
            break;

        default:
            $dateSql = "";
            break;
    }

    $categoryQuery = "SELECT news_image, news_title, LEFT(REGEXP_REPLACE(news_description, '<[^>]*>', ''), 500) AS news_description, news_paid, news_published, {$categoryType}_owner_id AS author_id, profile_display_name, category_title, link_url, meta_description FROM " . TABLE_PREFIX . "{$categoryType} INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id={$categoryType}_id AND link_status=200 AND link_site_id=" . SITE_ID . " LEFT JOIN " . TABLE_PREFIX . "meta ON meta_relation_id=link_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id={$categoryType}_owner_id INNER JOIN " . TABLE_PREFIX . "category ON category_id={$categoryType}_category AND category_site_id=" . SITE_ID . " WHERE {$categoryType}_category IN ({$categoryIds}) {$dateSql} AND {$categoryType}_site_id=" . SITE_ID . " {$orderBy} LIMIT {$pageNo}, {$limit} ";

    $resData = mysqli_query($conn, $categoryQuery);
    if (!$resData) {
        return [];
    } else {
        $out = [];
        while ($row = mysqli_fetch_assoc($resData)) {
            $out[] = $row;
        }
    }

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


function getSocialProofsData($conn, $type = '', $limit = 10, $featuredOnly = false, $characterLimit = 250)
{
    $showOnlyFeatured = $featuredOnly ? " link.link_is_featured DESC " : ' link.link_is_featured ASC ';

    $typeCondition = '';

    if (!empty($type) && $type !== 'social_proof') {
        $type = mysqli_real_escape_string($conn, $type);
        $typeCondition = " AND sp.sp_type = '{$type}' ";
    }

    $query = "
SELECT
    sp.sp_id AS id,
    sp.sp_title AS title,

    LEFT(
        TRIM(
            REGEXP_REPLACE(
                COALESCE(sp.sp_description, ''),
                '<[^>]*>',
                ''
            )
        ),
        " . (int)$characterLimit . "
    ) AS description,

    sp.sp_image AS image,

    profile.profile_display_name AS author,
    profile.profile_image AS author_image,

    link.link_url,
    link.link_published,
    link.link_is_featured,

    sp.sp_ref_type,
    sp.sp_ref_id,
    sp.sp_type,
    sp.sp_video_url,

    ref_link.link_url AS ref_url,
    s.service_title AS ref_title,
    s.service_image AS ref_image,
    LEFT(TRIM(REGEXP_REPLACE(COALESCE(s.service_description, ''), '<[^>]*>', '')), 120) AS ref_description

FROM " . TABLE_PREFIX . "social_proof sp

INNER JOIN " . TABLE_PREFIX . "link link
    ON link.link_relation_id = sp.sp_id
    AND link.link_type = 'social_proof'
    AND link.link_status = 200
    AND link.link_site_id = " . SITE_ID . "

INNER JOIN " . TABLE_PREFIX . "profile profile
    ON profile.profile_user_id = link.link_owner_id

LEFT JOIN " . TABLE_PREFIX . "link ref_link
    ON ref_link.link_relation_id = sp.sp_ref_id
    AND ref_link.link_type = sp.sp_ref_type COLLATE utf8mb4_unicode_ci
    AND ref_link.link_status = 200
    AND ref_link.link_site_id = " . SITE_ID . "

LEFT JOIN " . TABLE_PREFIX . "service s
    ON s.service_id = sp.sp_ref_id
    AND sp.sp_ref_type = 'service'
    AND s.service_site_id = " . SITE_ID . "

WHERE
    sp.sp_status = 1
    AND sp.sp_site_id = " . SITE_ID . "
    {$typeCondition}

ORDER BY
    link.link_is_featured " . ($featuredOnly ? 'DESC' : 'ASC') . ",
    link.link_published DESC

LIMIT " . (int)$limit;

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

        WHERE c.category_type = 'sp_'
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

        WHERE c.category_type = 'sp_'
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

function getEvents($conn, $categoryId = '', $futureEvents = false, $limit = 5, $sortTypes = 'event_start_date', $sortBy = 'ASC', $characterLimit = 250)
{

    $futureEventsCondition = (($futureEvents) ? " AND event_start_date >= NOW() " : "");

    $applySorting = "ORDER BY {$sortTypes} {$sortBy} ";

    $query = "SELECT 
        event_id AS id,
        event_title AS title,
        LEFT(
            TRIM(
                REGEXP_REPLACE(event_description, '<[^>]*>', '')
            ),
            {$characterLimit}
        ) AS description,
        event_image AS image,
        event_location AS location,
        event_start_date AS event_date,
        profile_display_name AS author,
        profile_image AS author_image,
        category_title AS category,
        category_link.link_url AS category_url,
        link.link_url,
        link.link_published

    FROM " . TABLE_PREFIX . "event

    INNER JOIN " . TABLE_PREFIX . "link AS link
        ON link.link_relation_id = event_id
       AND link.link_status = 200
       AND link.link_type = 'event'
       AND link.link_site_id = " . SITE_ID . "

    INNER JOIN " . TABLE_PREFIX . "category
        ON category_id = event_category
       AND category_type = 'event'
       AND category_site_id = " . SITE_ID . "

    LEFT JOIN " . TABLE_PREFIX . "link AS category_link
        ON category_link.link_relation_id = category_id
       AND category_link.link_type = 'category'
       AND category_link.link_status = 200
       AND category_link.link_site_id = " . SITE_ID . "

    INNER JOIN " . TABLE_PREFIX . "profile
        ON link.link_owner_id = profile_user_id

    WHERE event_status = 1
      AND event_site_id = " . SITE_ID . "
      " . (!empty($categoryId) ? "AND event_category = " . (int)$categoryId : "") . "
      " . $futureEventsCondition . "
    {$applySorting}
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

function getTestimonials($conn, $latest = true, $limit = 5, $characterLimit = 250)
{

    $latestTestimonialsCondition = $latest ? "ORDER BY category_time DESC" : "ORDER BY category_time ASC";

    $query = "SELECT category_title as title, category_image as profile_image, LEFT(
            TRIM(
                REGEXP_REPLACE(category_description, '<[^>]*>', '')
            ),
            {$characterLimit}
        ) AS description FROM " . TABLE_PREFIX . "category WHERE category_type='testimonial' AND category_status = 1 AND category_site_id=" . SITE_ID . " {$latestTestimonialsCondition} LIMIT {$limit}";

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


function getCategories(mysqli $conn, string $type = 'post', bool $latest = true, int $limit = 5, bool $characterCondition = true, int $characterLimit = 250): array
{
    $type = strtolower(trim($type));
    if (!preg_match('/^[a-z0-9_]+$/', $type)) {
        $type = 'post';
    }

    $latestCategoriesCondition = $latest ? "ORDER BY c.category_time DESC" : "ORDER BY c.category_time ASC";
    $descriptionQuery = $characterCondition
        ? "LEFT(
            TRIM(
                REGEXP_REPLACE(c.category_description, '<[^>]*>', '')
            ),
            {$characterLimit}
        ) AS description"
        : "c.category_description AS description";

    $query = "SELECT
            c.category_title AS title,
            c.category_image AS profile_image,
            {$descriptionQuery},
            l.link_url,
            COUNT(t.{$type}_id) AS article_count
        FROM " . TABLE_PREFIX . "category c
        LEFT JOIN " . TABLE_PREFIX . "link l
            ON c.category_id = l.link_relation_id
           AND l.link_site_id = " . SITE_ID . "
           AND l.link_type = 'category'
           AND l.link_status = 200
        LEFT JOIN " . TABLE_PREFIX . "{$type} t
            ON t.{$type}_category = c.category_id
           AND t.{$type}_status = 1
           AND t.{$type}_site_id = " . SITE_ID . "
        WHERE c.category_type = '{$type}'
          AND c.category_status = 1
          AND c.category_site_id = " . SITE_ID . "
        GROUP BY c.category_id, c.category_title, c.category_image, c.category_description, l.link_url
        {$latestCategoriesCondition}
        LIMIT {$limit}";

    $resData = mysqli_query($conn, $query);
    if (!$resData) {
        return [];
    }

    $out = [];
    while ($row = mysqli_fetch_assoc($resData)) {
        $row['article_count'] = (int)$row['article_count'];
        $out[] = $row;
    }

    mysqli_free_result($resData);

    return $out;
}

function timeToRead($content, $wordsPerMinute = 200)
{
    $plainText = trim(strip_tags((string) $content));

    if ($plainText === '') {
        return '1 min read';
    }

    $wordCount = str_word_count($plainText);
    $readMins = max(1, (int) ceil($wordCount / max(1, (int) $wordsPerMinute)));

    return (($readMins == 1) ? '1 min read' : $readMins . ' mins read');
}

function publishedTimeAgo($publishedDate)
{
    $timestamp = strtotime($publishedDate);

    if (!$timestamp) {
        return '';
    }

    $diff = time() - $timestamp;

    if ($diff < 60) {
        return 'Just now';
    }

    if ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' min' . ($mins > 1 ? 's' : '') . ' ago';
    }

    if ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    }

    if ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    }

    if ($diff < 2592000) {
        $weeks = floor($diff / 604800);
        return $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
    }

    if ($diff < 31536000) {
        $months = floor($diff / 2592000);
        return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
    }

    $years = floor($diff / 31536000);
    return $years . ' year' . ($years > 1 ? 's' : '') . ' ago';
}


function newsAuthorInfo(
    mysqli $connection,
    int $relationId,
    array $defaultAuthor
): array {

    $authors = [];

    $query = "SELECT
                    authors.profile_user_id,
                    profile.profile_display_name,
                    profile.profile_image
              FROM " . TABLE_PREFIX . "authors AS authors
              INNER JOIN " . TABLE_PREFIX . "profile AS profile
                    ON profile.profile_user_id = authors.profile_user_id
              WHERE authors.rel_id = {$relationId}
                AND authors.rel_type = 'news'
              ORDER BY authors.seq_no ASC";

    if ($result = mysqli_query($connection, $query)) {

        while ($row = mysqli_fetch_assoc($result)) {

            $name = trim($row['profile_display_name']);

            $authors[] = [
                'id'        => (int)$row['profile_user_id'],
                'name'      => $name,
                'character' => strtoupper(mb_substr($name, 0, 1)),
                'image'     => $row['profile_image'],
                'link'      => '/author/' .
                    $row['profile_user_id'] . '-' .
                    urlencode(strtolower(str_replace(' ', '-', $name))) .
                    '-articles/'
            ];
        }
    }

    // No mapped author, use default author
    if (empty($authors)) {

        $name = trim($defaultAuthor['name']);

        $authors[] = [
            'id'        => $defaultAuthor['id'],
            'name'      => $name,
            'character' => strtoupper(mb_substr($name, 0, 1)),
            'image'     => $defaultAuthor['image'] ?? '',
            'link'      => '/author/' .
                $defaultAuthor['id'] . '-' .
                urlencode(strtolower(str_replace(' ', '-', $name))) .
                '-articles/'
        ];
    }

    return [
        'primary' => [
            'id'        => $authors[0]['id'],
            'name'      => $authors[0]['name'],
            'character' => $authors[0]['character'],
            'image'     => $authors[0]['image'],
            'bio'       => $defaultAuthor['bio'] ?? '',
            'link'      => $authors[0]['link']
        ],
        'authors' => $authors
    ];
}

function removeInlineStyles($html, $removeFirstP = false)
{
    if (empty(trim($html))) {
        return '';
    }

    // Suppress warnings for malformed HTML 
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();

    // Prepend XML encoding declaration to force UTF-8 parsing safely
    $htmlWrapped = '<?xml encoding="UTF-8"><div id="temp_wrapper">' . $html . '</div>';

    $dom->loadHTML($htmlWrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    $xpath = new DOMXPath($dom);

    // 1. Remove inline styles and classes
    $nodes = $xpath->query('//*[@style or @class or @face]');
    foreach ($nodes as $node) {
        $node->removeAttribute('style');
        $node->removeAttribute('class');
        $node->removeAttribute('face');
    }

    // 2. SMART CONVERT/UNWRAP: Convert layout tags, but don't cause nested <p> errors
    $tagsToConvert = ['div', 'article', 'section', 'aside', 'header', 'footer'];
    $blockTags = ['p', 'ul', 'ol', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'table', 'blockquote'];

    foreach ($tagsToConvert as $tag) {
        $elements = $dom->getElementsByTagName($tag);
        for ($i = $elements->length - 1; $i >= 0; $i--) {
            $node = $elements->item($i);

            if ($node->getAttribute('id') === 'temp_wrapper') {
                continue;
            }

            // Check if this layout tag contains other block-level elements (like a <p>)
            $hasBlockChildren = false;
            foreach ($blockTags as $bTag) {
                if ($node->getElementsByTagName($bTag)->length > 0) {
                    $hasBlockChildren = true;
                    break;
                }
            }

            if ($hasBlockChildren) {
                // If it already holds block tags, just UNWRAP it (remove the div but keep contents)
                $fragment = $dom->createDocumentFragment();
                while ($node->childNodes->length > 0) {
                    $fragment->appendChild($node->childNodes->item(0));
                }
                $node->parentNode->replaceChild($fragment, $node);
            } else {
                // If it only holds text, CONVERT it into a <p> tag
                $pNode = $dom->createElement('p');
                while ($node->childNodes->length > 0) {
                    $pNode->appendChild($node->childNodes->item(0));
                }
                $node->parentNode->replaceChild($pNode, $node);
            }
        }
    }

    // 3. Unwrap useless inline formatting tags (span, font, o:p)
    $tagsToUnwrap = ['span', 'font', 'o:p'];
    foreach ($tagsToUnwrap as $tag) {
        $elements = $dom->getElementsByTagName($tag);
        for ($i = $elements->length - 1; $i >= 0; $i--) {
            $node = $elements->item($i);
            $fragment = $dom->createDocumentFragment();
            while ($node->childNodes->length > 0) {
                $fragment->appendChild($node->childNodes->item(0));
            }
            $node->parentNode->replaceChild($fragment, $node);
        }
    }

    // 4. Remove empty <p> tags at the DOM level
    $paragraphs = $dom->getElementsByTagName('p');
    for ($i = $paragraphs->length - 1; $i >= 0; $i--) {
        $p = $paragraphs->item($i);
        // Clean invisible spaces, zero-width spaces, and normal spaces to check if it's truly empty
        $text = str_replace(["\xC2\xA0", "\xE2\x80\x8B", " "], '', $p->textContent);

        if (trim($text) === '' && $p->getElementsByTagName('img')->length === 0 && $p->getElementsByTagName('iframe')->length === 0) {
            $p->parentNode->removeChild($p);
        }
    }

    // 5. UNWRAP THE FIRST <p> TAG IF REQUESTED
    if ($removeFirstP) {
        $firstP = $dom->getElementsByTagName('p')->item(0);
        if ($firstP) {
            $fragment = $dom->createDocumentFragment();
            while ($firstP->childNodes->length > 0) {
                $fragment->appendChild($firstP->childNodes->item(0));
            }
            $firstP->parentNode->replaceChild($fragment, $firstP);
        }
    }

    // 6. Extract the cleaned HTML back out of our temporary wrapper
    $wrapper = $dom->getElementById('temp_wrapper');
    $clean_html = '';
    if ($wrapper) {
        foreach ($wrapper->childNodes as $child) {
            $clean_html .= $dom->saveHTML($child);
        }
    }

    // 7. AGGRESSIVE FAILSAFE: Strip any literal empty <p> tags left in the string
    // This loops until it has destroyed every <p></p>, <p>&nbsp;</p>, <p><br></p>, or <p>  </p>
    do {
        $clean_html = preg_replace('/<p[^>]*>(?:\s|&nbsp;|\xC2\xA0|\xE2\x80\x8B|<br\s*\/?>)*<\/p>/iu', '', $clean_html, -1, $count);
    } while ($count > 0);

    // Clear memory of parsing errors
    libxml_clear_errors();

    return trim($clean_html);
}

function trimParagraph($content, $characterLimit = 150)
{
    $description = trim(strip_tags($content));

    if (mb_strlen($description) > $characterLimit) {
        $description = mb_substr($description, 0, $characterLimit);
        $description = mb_substr($description, 0, mb_strrpos($description, ' '));
        $description .= '...';
    }

    return $description;
}
