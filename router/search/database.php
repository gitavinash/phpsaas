<?php
require_once SEARCH_FUNCTIONS;

$raw    = isset($searchKeyword) ? $searchKeyword : '';
$siteId = (int) SITE_ID;
$pageParam = $_GET['page'] ?? $_GET['pg'] ?? 1;   // ← accept both
$page = max(1, (int)$pageParam);
$limit  = 20;
$offset = ($page - 1) * $limit;

$sortBy = 'RAND()';
if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'newest':
            $sortBy = 'published_at DESC';
            break;
        case 'oldest':
            $sortBy = 'published_at ASC';
            break;
    }
}

if ($raw === '') {
    http_response_code(400);
    require_once ERROR_404;
    exit;
}

/** Get server FULLTEXT min token length (handles local vs server differences) */
function getServerFtMinLen(mysqli $conn): int
{
    $innodb = 3;  // sensible defaults
    $myisam = 4;
    if ($res = mysqli_query($conn, "SELECT @@innodb_ft_min_token_size AS innodb_min, @@ft_min_word_len AS myisam_min")) {
        if ($row = mysqli_fetch_assoc($res)) {
            if (isset($row['innodb_min']) && $row['innodb_min'] !== null) $innodb = (int)$row['innodb_min'];
            if (isset($row['myisam_min']) && $row['myisam_min'] !== null) $myisam = (int)$row['myisam_min'];
        }
    }
    return max(1, min($innodb, $myisam));
}

/** Build strict(+token*) and loose(token*) queries using server min token length */
function buildBooleanQueriesWithMin(string $q, int $minLen): array
{
    $q = trim(mb_strtolower($q));
    $q = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $q);
    $parts = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY);

    $validTokens = [];
    $allTokens   = [];
    foreach ($parts as $w) {
        $allTokens[] = $w;
        if (mb_strlen($w) >= $minLen) $validTokens[] = $w;
    }
    if (!$validTokens && $q !== '') $validTokens[] = mb_substr($q, 0, 20);

    $strict = implode(' ', array_map(fn($t) => '+' . $t . '*', $validTokens)); // all terms must appear
    $loose  = implode(' ', array_map(fn($t) =>      $t . '*',  $validTokens)); // any term may appear
    return [$strict, $loose, $allTokens];
}

$minLen = getServerFtMinLen($connection);
[$qAll, $qAny, $allTokens] = buildBooleanQueriesWithMin($raw, $minLen);

/**
 * Decide primary source by SITE_APP
 * - ecommerce/ecom_gift => product
 * - news                => news
 * - default             => post
 */
$app = defined('SITE_APP') ? strtolower(SITE_APP) : 'default';

$appSourceMap = [
    'ecommerce' => 'product',
    'ecom_gift' => 'product',
    'nms'       => 'news',
    'blog'      => 'post',
    'cms'       => 'post',
];

$source = $appSourceMap[strtolower($app)] ?? 'post';
$isNmsNewsSearch = ($app === 'nms');

if ($isNmsNewsSearch && !isset($_GET['sort'])) {
    $sortBy = 'published_at DESC';
}

$newsFallbackOrder = ($sortBy === 'published_at ASC') ? 'ASC' : 'DESC';


/**
 * Build SQL, COUNT SQL, and params based on source
 * normalized columns: item_type, title, image, url, published_at, relation_id, tag_type, rel_score
 */
function buildSqlBySource(string $source, int $siteId, string $qAll, string $qAny, int $limit, int $offset, string $sortBy = 'RAND()', bool $isNmsNewsSearch = false): array
{
    switch ($source) {
        case 'product':
            $sql = "
                SELECT
                    'product' AS item_type,
                    pr.product_title AS title,
                    pr.product_image AS image,
                    l.link_url       AS url,
                    pr.product_time  AS published_at,
                    pr.product_id    AS relation_id,
                    NULL             AS tag_type,
                    pr.product_description AS description,
                    (
                        (MATCH(pr.product_title) AGAINST (? IN BOOLEAN MODE)) * 2.2
                      + (MATCH(pr.product_title, pr.product_highlight, pr.product_description) AGAINST (? IN BOOLEAN MODE)) * 1.2
                      + (MATCH(pr.product_title) AGAINST (? IN BOOLEAN MODE)) * 0.7
                      + (MATCH(pr.product_title, pr.product_highlight, pr.product_description) AGAINST (? IN BOOLEAN MODE)) * 0.6
                    ) AS rel_score
                FROM " . TABLE_PREFIX . "product pr
                JOIN " . TABLE_PREFIX . "link l
                  ON l.link_relation_id = pr.product_id
                 AND l.link_type = 'product'
                 AND l.link_status = 200
                WHERE pr.product_site_id = ?
                  AND (
                       MATCH(pr.product_title) AGAINST (? IN BOOLEAN MODE)
                    OR MATCH(pr.product_title, pr.product_highlight, pr.product_description) AGAINST (? IN BOOLEAN MODE)
                    OR MATCH(pr.product_title) AGAINST (? IN BOOLEAN MODE)
                    OR MATCH(pr.product_title, pr.product_highlight, pr.product_description) AGAINST (? IN BOOLEAN MODE)
                  )
                ORDER BY {$sortBy},
                         ( (MATCH(pr.product_title) AGAINST (? IN BOOLEAN MODE)) * 2.2
                         + (MATCH(pr.product_title, pr.product_highlight, pr.product_description) AGAINST (? IN BOOLEAN MODE)) * 1.2 ) DESC
                LIMIT ? OFFSET ?";

            $countSql = "
                SELECT COUNT(*) AS total
                FROM " . TABLE_PREFIX . "product pr
                JOIN " . TABLE_PREFIX . "link l
                  ON l.link_relation_id = pr.product_id
                 AND l.link_type = 'product'
                 AND l.link_status = 200
                WHERE pr.product_site_id = ?
                  AND (
                       MATCH(pr.product_title) AGAINST (? IN BOOLEAN MODE)
                    OR MATCH(pr.product_title, pr.product_highlight, pr.product_description) AGAINST (? IN BOOLEAN MODE)
                    OR MATCH(pr.product_title) AGAINST (? IN BOOLEAN MODE)
                    OR MATCH(pr.product_title, pr.product_highlight, pr.product_description) AGAINST (? IN BOOLEAN MODE)
                  )";

            $params = [
                $qAll,
                $qAll,
                $qAny,
                $qAny, // rel_score bits
                $siteId,                    // site filter
                $qAll,
                $qAll,
                $qAny,
                $qAny, // WHERE repeats
                $qAll,
                $qAll,               // ORDER BY scoring
                $limit,
                $offset
            ];
            $countParams = [$siteId, $qAll, $qAll, $qAny, $qAny];
            break;

        case 'news':

            if ($isNmsNewsSearch) {
                $sql = "
        SELECT DISTINCT
                item_type,
                title,
                image,
                url,
                published_at,
                relation_id,
                tag_type,
                description,
                rel_score,
                priority
        FROM (
                /* 1️⃣ NEWS VIA TAG MATCH (HIGHEST PRIORITY) */
                SELECT
                        'news' AS item_type,
                        n.news_title       AS title,
                        n.news_image       AS image,
                        l2.link_url        AS url,
                        COALESCE(n.news_published, n.news_time) AS published_at,
                        n.news_id          AS relation_id,
                        'tag'              AS tag_type,
                        n.news_description AS description,
                        100                AS rel_score,
                        1                  AS priority
                FROM " . TABLE_PREFIX . "tag t
                JOIN " . TABLE_PREFIX . "link l1
                    ON l1.link_id = t.tag_relation_id
                 AND l1.link_status = 200
                 AND l1.link_site_id = ?
                JOIN " . TABLE_PREFIX . "news n
                    ON n.news_id = l1.link_relation_id
                 AND n.news_status = 1
                 AND n.news_is_public = 1
                 AND n.news_site_id = ?
                LEFT JOIN " . TABLE_PREFIX . "link l2
                    ON l2.link_relation_id = n.news_id
                 AND l2.link_type = 'news'
                 AND l2.link_status = 200
                 AND l2.link_site_id = ?
                WHERE t.tag_type = 'news'
                    AND t.tag_site_id = ?
                    AND MATCH(t.tag_title) AGAINST (? IN BOOLEAN MODE)

                UNION ALL

                /* 2️⃣ NEWS TITLE MATCH */
                SELECT
                        'news',
                        n.news_title,
                        n.news_image,
                        l.link_url,
                        COALESCE(n.news_published, n.news_time),
                        n.news_id,
                        NULL,
                        n.news_description,
                        MATCH(n.news_title) AGAINST (? IN BOOLEAN MODE) * 5,
                        2
                FROM " . TABLE_PREFIX . "news n
                LEFT JOIN " . TABLE_PREFIX . "link l
                    ON l.link_relation_id = n.news_id
                 AND l.link_type = 'news'
                 AND l.link_status = 200
                 AND l.link_site_id = ?
                WHERE n.news_site_id = ?
                    AND n.news_status = 1
                    AND n.news_is_public = 1
                    AND MATCH(n.news_title) AGAINST (? IN BOOLEAN MODE)
        ) AS combined_results
        ORDER BY {$sortBy}, priority ASC, rel_score DESC
        LIMIT ? OFFSET ?
        ";

                $params = [
                    // TAG → LINK → NEWS
                    $siteId,
                    $siteId,
                    $siteId,
                    $siteId,
                    $qAll,

                    // TITLE
                    $qAll,
                    $siteId,
                    $siteId,
                    $qAll,

                    // PAGINATION
                    $limit,
                    $offset
                ];

                $countSql = "
                SELECT COUNT(DISTINCT n.news_id) AS total
                FROM " . TABLE_PREFIX . "news n
                LEFT JOIN " . TABLE_PREFIX . "link l
                    ON l.link_relation_id = n.news_id
                 AND l.link_type = 'news'
                 AND l.link_status = 200
                 AND l.link_site_id = ?
                LEFT JOIN " . TABLE_PREFIX . "tag t
                    ON t.tag_relation_id = l.link_id
                 AND t.tag_type = 'news'
                 AND t.tag_site_id = ?
                WHERE n.news_site_id = ?
                    AND n.news_status = 1
                    AND n.news_is_public = 1
                    AND (
                             MATCH(t.tag_title) AGAINST (? IN BOOLEAN MODE)
                        OR MATCH(n.news_title) AGAINST (? IN BOOLEAN MODE)
                    )
        ";

                $countParams = [
                    $siteId,
                    $siteId,
                    $siteId,
                    $qAll,
                    $qAll
                ];

                break;
            }

            $sql = "
    SELECT DISTINCT
        item_type,
        title,
        image,
        url,
        published_at,
        relation_id,
        tag_type,
        description,
        rel_score,
        priority
    FROM (
        /* 1️⃣ NEWS VIA TAG MATCH (HIGHEST PRIORITY) */
        SELECT
            'news' AS item_type,
            n.news_title       AS title,
            n.news_image       AS image,
            l2.link_url        AS url,
            COALESCE(n.news_published, n.news_time) AS published_at,
            n.news_id          AS relation_id,
            'tag'              AS tag_type,
            n.news_description AS description,
            100                AS rel_score,
            1                  AS priority
        FROM " . TABLE_PREFIX . "tag t
        JOIN " . TABLE_PREFIX . "link l1
          ON l1.link_id = t.tag_relation_id
         AND l1.link_status = 200
         AND l1.link_site_id = ?
        JOIN " . TABLE_PREFIX . "news n
          ON n.news_id = l1.link_relation_id
         AND n.news_status = 1
         AND n.news_is_public = 1
         AND n.news_site_id = ?
        LEFT JOIN " . TABLE_PREFIX . "link l2
          ON l2.link_relation_id = n.news_id
         AND l2.link_type = 'news'
         AND l2.link_status = 200
         AND l2.link_site_id = ?
        WHERE t.tag_type = 'news'
          AND t.tag_site_id = ?
          AND MATCH(t.tag_title) AGAINST (? IN BOOLEAN MODE)

        UNION ALL

        /* 2️⃣ NEWS TITLE MATCH */
        SELECT
            'news',
            n.news_title,
            n.news_image,
            l.link_url,
            COALESCE(n.news_published, n.news_time),
            n.news_id,
            NULL,
            n.news_description,
            MATCH(n.news_title) AGAINST (? IN BOOLEAN MODE) * 5,
            2
        FROM " . TABLE_PREFIX . "news n
        LEFT JOIN " . TABLE_PREFIX . "link l
          ON l.link_relation_id = n.news_id
         AND l.link_type = 'news'
         AND l.link_status = 200
         AND l.link_site_id = ?
        WHERE n.news_site_id = ?
          AND n.news_status = 1
          AND n.news_is_public = 1
          AND MATCH(n.news_title) AGAINST (? IN BOOLEAN MODE)

        UNION ALL

        /* 3️⃣ NEWS CONTENT MATCH */
        SELECT
            'news',
            n.news_title,
            n.news_image,
            l.link_url,
            COALESCE(n.news_published, n.news_time),
            n.news_id,
            NULL,
            n.news_description,
            MATCH(n.news_highlight, n.news_description)
                AGAINST (? IN BOOLEAN MODE),
            3
        FROM " . TABLE_PREFIX . "news n
        LEFT JOIN " . TABLE_PREFIX . "link l
          ON l.link_relation_id = n.news_id
         AND l.link_type = 'news'
         AND l.link_status = 200
         AND l.link_site_id = ?
        WHERE n.news_site_id = ?
          AND n.news_status = 1
          AND n.news_is_public = 1
          AND MATCH(n.news_highlight, n.news_description)
              AGAINST (? IN BOOLEAN MODE)
    ) AS combined_results
    ORDER BY {$sortBy}, priority ASC, rel_score DESC
    LIMIT ? OFFSET ?
    ";

            $params = [
                // TAG → LINK → NEWS
                $siteId,  // l1.link_site_id
                $siteId,  // n.news_site_id
                $siteId,  // l2.link_site_id
                $siteId,  // t.tag_site_id
                $qAll,    // tag title match

                // TITLE
                $qAll,    // title match (score)
                $siteId,  // l.link_site_id
                $siteId,  // n.news_site_id
                $qAll,    // title match (where)

                // CONTENT
                $qAny,    // content match (score)
                $siteId,  // l.link_site_id
                $siteId,  // n.news_site_id
                $qAny,    // content match (where)

                // PAGINATION
                $limit,
                $offset
            ];

            $countSql = "
        SELECT COUNT(DISTINCT n.news_id) AS total
        FROM " . TABLE_PREFIX . "news n
        LEFT JOIN " . TABLE_PREFIX . "link l
          ON l.link_relation_id = n.news_id
         AND l.link_type = 'news'
         AND l.link_status = 200
         AND l.link_site_id = ?
        LEFT JOIN " . TABLE_PREFIX . "tag t
          ON t.tag_relation_id = l.link_id
         AND t.tag_type = 'news'
         AND t.tag_site_id = ?
        WHERE n.news_site_id = ?
          AND n.news_status = 1
          AND n.news_is_public = 1
          AND (
               MATCH(t.tag_title) AGAINST (? IN BOOLEAN MODE)
            OR MATCH(n.news_title) AGAINST (? IN BOOLEAN MODE)
            OR MATCH(n.news_highlight, n.news_description)
               AGAINST (? IN BOOLEAN MODE)
          )
    ";

            $countParams = [
                $siteId,  // l.link_site_id
                $siteId,  // t.tag_site_id
                $siteId,  // n.news_site_id
                $qAll,    // tag title match
                $qAny,    // news title match
                $qAny     // content match
            ];

            break;

        default: // post
            $sql = "
                SELECT
                    'post' AS item_type,
                    p.post_title     AS title,
                    p.post_image     AS image,
                    l.link_url       AS url,
                    p.post_time      AS published_at,
                    p.post_id        AS relation_id,
                    NULL             AS tag_type,
                    p.post_description AS description,
                    (
                        (MATCH(p.post_title) AGAINST (? IN BOOLEAN MODE)) * 2.0
                      + (MATCH(p.post_title, p.post_highlight, p.post_description) AGAINST (? IN BOOLEAN MODE)) * 1.25
                      + (MATCH(p.post_title) AGAINST (? IN BOOLEAN MODE)) * 0.6
                      + (MATCH(p.post_title, p.post_highlight, p.post_description) AGAINST (? IN BOOLEAN MODE)) * 0.5
                    ) AS rel_score
                FROM " . TABLE_PREFIX . "post p
                JOIN " . TABLE_PREFIX . "link l
                  ON l.link_relation_id = p.post_id
                 AND l.link_type = 'post'
                 AND l.link_status = 200
                WHERE p.post_site_id = ?
                  AND (
                       MATCH(p.post_title) AGAINST (? IN BOOLEAN MODE)
                    OR MATCH(p.post_title, p.post_highlight, p.post_description) AGAINST (? IN BOOLEAN MODE)
                    OR MATCH(p.post_title) AGAINST (? IN BOOLEAN MODE)
                    OR MATCH(p.post_title, p.post_highlight, p.post_description) AGAINST (? IN BOOLEAN MODE)
                  )
                ORDER BY {$sortBy},
                         ( (MATCH(p.post_title) AGAINST (? IN BOOLEAN MODE)) * 2.0
                         + (MATCH(p.post_title, p.post_highlight, p.post_description) AGAINST (? IN BOOLEAN MODE)) * 1.25 ) DESC
                LIMIT ? OFFSET ?";

            $countSql = "
                SELECT COUNT(*) AS total
                FROM " . TABLE_PREFIX . "post p
                JOIN " . TABLE_PREFIX . "link l
                  ON l.link_relation_id = p.post_id
                 AND l.link_type = 'post'
                 AND l.link_status = 200
                WHERE p.post_site_id = ?
                  AND (
                       MATCH(p.post_title) AGAINST (? IN BOOLEAN MODE)
                    OR MATCH(p.post_title, p.post_highlight, p.post_description) AGAINST (? IN BOOLEAN MODE)
                    OR MATCH(p.post_title) AGAINST (? IN BOOLEAN MODE)
                    OR MATCH(p.post_title, p.post_highlight, p.post_description) AGAINST (? IN BOOLEAN MODE)
                  )";

            $params = [
                $qAll,
                $qAll,
                $qAny,
                $qAny,
                $siteId,
                $qAll,
                $qAll,
                $qAny,
                $qAny,
                $qAll,
                $qAll,
                $limit,
                $offset
            ];
            $countParams = [$siteId, $qAll, $qAll, $qAny, $qAny];
            break;
    }

    return [$sql, $params, $countSql, $countParams];
}

[$sql, $params, $countSql, $countParams] = buildSqlBySource($source, $siteId, $qAll, $qAny, $limit, $offset, $sortBy, $isNmsNewsSearch);

/** Prepare + execute main query */
$stmt = mysqli_prepare($connection, $sql);
if (!$stmt) {
    error_log('Search prepare failed: ' . mysqli_error($connection));
    http_response_code(500);
    require_once ERROR_500;
    exit;
}

/* types */
$types = '';
foreach ($params as $p) $types .= is_int($p) ? 'i' : 's';

mysqli_stmt_bind_param($stmt, $types, ...$params);
$ok = mysqli_stmt_execute($stmt);
$res = $ok ? mysqli_stmt_get_result($stmt) : false;

$rows = [];
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        // normalize URL to start with '/'
        $row['url'] = (!empty($row['url']) && $row['url'][0] === '/') ? $row['url'] : '/' . ltrim((string)($row['url'] ?? ''), '/');
        $rows[] = $row;
    }
}

/** COUNT for pagination */
$total = 0;
$pages = 0;
if ($rows !== false) {
    $c = mysqli_prepare($connection, $countSql);
    if ($c) {
        $ctypes = '';
        foreach ($countParams as $p) $ctypes .= is_int($p) ? 'i' : 's';
        mysqli_stmt_bind_param($c, $ctypes, ...$countParams);
        mysqli_stmt_execute($c);
        $cr = mysqli_stmt_get_result($c);
        if ($cr && ($tot = mysqli_fetch_assoc($cr))) {
            $total = (int)$tot['total'];
            $pages = (int)ceil($total / $limit);
        }
    }
}

/** ---------- Robust FALLBACK (tokenized LIKE) if no rows ---------- */
if (!$rows) {
    $cleanRaw   = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', mb_strtolower($raw));
    $tokenArray = array_values(array_unique(array_filter(preg_split('/\s+/', trim($cleanRaw), -1, PREG_SPLIT_NO_EMPTY))));
    $likePhrase = '%' . mysqli_real_escape_string($connection, $raw) . '%';
    $likeTokens = array_map(fn($t) => '%' . mysqli_real_escape_string($connection, $t) . '%', $tokenArray);

    $buildLike = function (string $alias, array $fields, string $phrase, array $tokens) {
        $parts = [];
        $params = [];
        foreach ($fields as $f) {
            $parts[] = "{$alias}.{$f} LIKE ?";
            $params[] = $phrase;
        }
        foreach ($tokens as $tok) {
            foreach ($fields as $f) {
                $parts[] = "{$alias}.{$f} LIKE ?";
                $params[] = $tok;
            }
        }
        return ['(' . implode(' OR ', $parts) . ')', $params];
    };

    if ($source === 'product') {
        [$w, $p] = $buildLike('pr', ['product_title', 'product_highlight', 'product_description'], $likePhrase, $likeTokens);
        $fbSql = "
           SELECT 'product' AS item_type, pr.product_title AS title, pr.product_image AS image, l.link_url AS url,
                  pr.product_time AS published_at, pr.product_id AS relation_id, NULL AS tag_type, 0.1 AS rel_score
           FROM " . TABLE_PREFIX . "product pr
           JOIN " . TABLE_PREFIX . "link l
             ON l.link_relation_id=pr.product_id AND l.link_type='product' AND l.link_status=200
           WHERE pr.product_site_id=?
             AND {$w}
           LIMIT ? OFFSET ?";
        $fbParams = array_merge([$siteId], $p, [$limit, $offset]);
    } elseif ($source === 'news') {
        if ($isNmsNewsSearch) {
            [$wTitle, $pTitle] = $buildLike('n', ['news_title'], $likePhrase, $likeTokens);
            [$wTag, $pTag] = $buildLike('t', ['tag_title'], $likePhrase, $likeTokens);
            $fbSql = "
                     SELECT DISTINCT 'news' AS item_type, n.news_title AS title, n.news_image AS image, l.link_url AS url,
                                    COALESCE(n.news_published, n.news_time) AS published_at, n.news_id AS relation_id, NULL AS tag_type, 0.1 AS rel_score
                     FROM " . TABLE_PREFIX . "news n
                     LEFT JOIN " . TABLE_PREFIX . "link l
                         ON l.link_relation_id=n.news_id AND l.link_type='news' AND l.link_status=200
                     LEFT JOIN " . TABLE_PREFIX . "tag t
                         ON t.tag_relation_id=l.link_id AND t.tag_type='news' AND t.tag_site_id=?
                     WHERE n.news_site_id=?
                         AND n.news_status=1 AND n.news_is_public=1
                         AND ({$wTitle} OR {$wTag})
                     ORDER BY COALESCE(n.news_published, n.news_time) {$newsFallbackOrder}
                     LIMIT ? OFFSET ?";
            $fbParams = array_merge([$siteId, $siteId], $pTitle, $pTag, [$limit, $offset]);
        } else {
            [$w, $p] = $buildLike('n', ['news_title', 'news_highlight', 'news_description'], $likePhrase, $likeTokens);
            $fbSql = "
                     SELECT 'news' AS item_type, n.news_title AS title, n.news_image AS image, l.link_url AS url,
                                    COALESCE(n.news_published, n.news_time) AS published_at, n.news_id AS relation_id, NULL AS tag_type, 0.1 AS rel_score
                     FROM " . TABLE_PREFIX . "news n
                     LEFT JOIN " . TABLE_PREFIX . "link l
                         ON l.link_relation_id=n.news_id AND l.link_type='news' AND l.link_status=200
                     WHERE n.news_site_id=?
                         AND n.news_status=1 AND n.news_is_public=1
                         AND {$w}
                     LIMIT ? OFFSET ?";
            $fbParams = array_merge([$siteId], $p, [$limit, $offset]);
        }
    } else { // post
        [$w, $p] = $buildLike('p', ['post_title', 'post_highlight', 'post_description'], $likePhrase, $likeTokens);
        $fbSql = "
           SELECT 'post' AS item_type, p.post_title AS title, p.post_image AS image, l.link_url AS url,
                  p.post_time AS published_at, p.post_id AS relation_id, NULL AS tag_type, 0.1 AS rel_score
           FROM " . TABLE_PREFIX . "post p
           JOIN " . TABLE_PREFIX . "link l
             ON l.link_relation_id=p.post_id AND l.link_type='post' AND l.link_status=200
           WHERE p.post_site_id=?
             AND {$w}
           LIMIT ? OFFSET ?";
        $fbParams = array_merge([$siteId], $p, [$limit, $offset]);
    }

    $fb = mysqli_prepare($connection, $fbSql);
    if ($fb) {
        $fbTypes = '';
        foreach ($fbParams as $p) $fbTypes .= is_int($p) ? 'i' : 's';
        mysqli_stmt_bind_param($fb, $fbTypes, ...$fbParams);
        mysqli_stmt_execute($fb);
        $fbr = mysqli_stmt_get_result($fb);
        while ($row = mysqli_fetch_assoc($fbr)) {
            $row['url'] = (!empty($row['url']) && $row['url'][0] === '/') ? $row['url'] : '/' . ltrim((string)($row['url'] ?? ''), '/');
            $rows[] = $row;
        }
    }
}

/** Pagination helpers */
$hasNext  = ($page < max(1, (int)ceil($total / $limit)));
$hasPrev  = ($page > 1);
$nextPage = $hasNext ? ($page + 1) : null;
$prevPage = $hasPrev ? ($page - 1) : null;

if (!defined('TOTAL_PAGE')) {
    define('TOTAL_PAGE', $pages);
}

if (!defined('TOTAL_RESULT')) {
    define('TOTAL_RESULT', $total);
}

/** Hand off to the view */
$search_path = USER_PATH . "search.php";
$searchData  = [
    'q'         => $raw,
    'results'   => $rows,
    'page'      => $page,
    'limit'     => $limit,
    'total'     => $total,
    'pages'     => $pages,
    'has_next'  => $hasNext,
    'has_prev'  => $hasPrev,
    'next_page' => $nextPage,
    'prev_page' => $prevPage,
    'source'    => $source
];

if (file_exists($search_path)) {
    require $search_path;
} else {
    require USER_PATH . 'search.php';
}