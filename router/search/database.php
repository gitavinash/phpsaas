<?php
require_once SEARCH_FUNCTIONS;

$raw    = isset($searchKeyword) ? $searchKeyword : '';
$siteId = (int) SITE_ID;
$pageParam = $_GET['page'] ?? $_GET['page'] ?? 1;   // ← accept both
$page = max(1, (int)$pageParam);
$limit  = 10;
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

/** Build strict/loose boolean queries using server min token length */
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

    if (count($validTokens) > 1) {
        $phrase = '"' . implode(' ', $validTokens) . '"';
        $strict = $phrase;
        $loose  = $phrase;
    } else {
        $strict = implode(' ', array_map(fn($t) => '+' . $t, $validTokens)); // exact token
        $loose  = implode(' ', array_map(fn($t) =>      $t,  $validTokens)); // exact token
    }
    return [$strict, $loose, $allTokens];
}

$minLen = getServerFtMinLen($connection);
[$qAll, $qAny, $allTokens] = buildBooleanQueriesWithMin($raw, $minLen);

$hasShortToken = false;
foreach ($allTokens as $token) {
    if (mb_strlen((string)$token) < $minLen) {
        $hasShortToken = true;
        break;
    }
}

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
                    'product'        AS item_type,
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

        case 'news':
            return buildNewsSql($siteId, $qAll, $qAny, $limit, $offset, $sortBy, $isNmsNewsSearch);
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

function buildNewsSql(
    int $siteId,
    string $qAll,
    string $qAny,
    int $limit,
    int $offset,
    string $sortBy = 'published_at DESC',
    bool $isNmsNewsSearch = false
): array {
    $newsTable     = TABLE_PREFIX . 'news';
    $linkTable     = TABLE_PREFIX . 'link';
    $tagTable      = TABLE_PREFIX . 'tag';
    $categoryTable = TABLE_PREFIX . 'category';
    $profileTable  = TABLE_PREFIX . 'profile';

    /*
     * News search is restricted to:
     *
     *   1. news.news_title
     *   2. tag.tag_title
     *
     * Tag matches can belong to:
     *   - news_id
     *   - link_id
     *
     * We intentionally do NOT search:
     *   - news_description
     *   - category_title
     *   - author/profile
     *   - URL
     *   - any other column
     */

    $allowedSorts = [
        'published_at DESC',
        'published_at ASC',
        'rel_score DESC',
        'relation_id DESC',
        'relation_id ASC',
    ];

    if (!in_array($sortBy, $allowedSorts, true)) {
        $sortBy = 'published_at DESC';
    }

    /*
     * Search candidates first.
     *
     * This is considerably cheaper than joining category/profile/link
     * before performing the FULLTEXT search.
     *
     * The three branches are intentionally separate:
     *
     * 1. news_title MATCH
     * 2. tag_title MATCH against news_id
     * 3. tag_title MATCH against link_id
     *
     * This avoids:
     *
     *     t.tag_relation_id = n.news_id
     *     OR
     *     t.tag_relation_id = l.link_id
     *
     * which is difficult for MySQL to optimize.
     */
    $candidateSql = "
        SELECT
            x.news_id,
            MAX(x.rel_score) AS rel_score,
            MIN(x.priority) AS priority
        FROM (
            /* ---------------------------------------------------------
             * NEWS TITLE MATCH
             * --------------------------------------------------------- */
            SELECT
                n.news_id,
                (
                    MATCH(n.news_title)
                    AGAINST (? IN BOOLEAN MODE)
                ) * 5 AS rel_score,
                1 AS priority
            FROM {$newsTable} n
            WHERE n.news_site_id = ?
              AND n.news_status = 1
              AND n.news_is_public = 1
              AND MATCH(n.news_title)
                  AGAINST (? IN BOOLEAN MODE)

            UNION ALL

            /* ---------------------------------------------------------
             * TAG MATCH -> NEWS ID
             * --------------------------------------------------------- */
            SELECT
                t.tag_relation_id AS news_id,
                100 AS rel_score,
                2 AS priority
            FROM {$tagTable} t
            INNER JOIN {$newsTable} n
                ON n.news_id = t.tag_relation_id
               AND n.news_site_id = ?
               AND n.news_status = 1
               AND n.news_is_public = 1
            WHERE t.tag_type = 'news'
              AND t.tag_site_id = ?
              AND MATCH(t.tag_title)
                  AGAINST (? IN BOOLEAN MODE)

            UNION ALL

            /* ---------------------------------------------------------
             * TAG MATCH -> LINK ID -> NEWS ID
             * --------------------------------------------------------- */
            SELECT
                l.link_relation_id AS news_id,
                100 AS rel_score,
                2 AS priority
            FROM {$tagTable} t
            INNER JOIN {$linkTable} l
                ON l.link_id = t.tag_relation_id
               AND l.link_type = 'news'
               AND l.link_status = 200
               AND l.link_site_id = ?
            INNER JOIN {$newsTable} n
                ON n.news_id = l.link_relation_id
               AND n.news_site_id = ?
               AND n.news_status = 1
               AND n.news_is_public = 1
            WHERE t.tag_type = 'news'
              AND t.tag_site_id = ?
              AND MATCH(t.tag_title)
                  AGAINST (? IN BOOLEAN MODE)

        ) x
        GROUP BY x.news_id
    ";

    /*
     * NMS requires category + author information.
     * Normal news search only requires the core news information.
     */
    if ($isNmsNewsSearch) {
        $sql = "
            WITH matched_news AS (
                {$candidateSql}
            )
            SELECT
                'news' AS item_type,
                n.news_title AS title,
                n.news_image AS image,
                l.link_url AS url,
                COALESCE(n.news_published, n.news_time) AS published_at,
                n.news_id AS relation_id,
                CASE
                    WHEN mn.priority = 2 THEN 'tag'
                    ELSE NULL
                END AS tag_type,
                n.news_description AS description,
                c.category_title,
                cl.link_url AS category_url,
                p.profile_display_name AS author_name,
                n.news_owner_id AS author_id,
                n.news_paid,
                mn.rel_score,
                mn.priority
            FROM matched_news mn
            INNER JOIN {$newsTable} n
                ON n.news_id = mn.news_id
            INNER JOIN {$linkTable} l
                ON l.link_relation_id = n.news_id
               AND l.link_type = 'news'
               AND l.link_status = 200
               AND l.link_site_id = ?
            INNER JOIN {$categoryTable} c
                ON c.category_id = n.news_category
               AND c.category_type = 'news'
               AND c.category_site_id = ?
            LEFT JOIN {$linkTable} cl
                ON cl.link_relation_id = c.category_id
               AND cl.link_type = 'category'
               AND cl.link_status = 200
               AND cl.link_site_id = ?
            LEFT JOIN {$profileTable} p
                ON p.profile_user_id = n.news_owner_id
            ORDER BY {$sortBy},
                     mn.priority ASC,
                     mn.rel_score DESC,
                     published_at DESC
            LIMIT ? OFFSET ?
        ";

        /*
         * Count only the matching news IDs.
         * No category/profile joins are required.
         */
        $countSql = "
            SELECT COUNT(*) AS total
            FROM (
                {$candidateSql}
            ) matched
        ";

        $params = [
            // TITLE MATCH
            $qAll,
            $siteId,
            $qAll,

            // TAG -> NEWS
            $siteId,
            $siteId,
            $qAll,

            // TAG -> LINK -> NEWS
            $siteId,
            $siteId,
            $siteId,
            $qAll,

            // Result metadata
            $siteId,
            $siteId,
            $siteId,

            // Pagination
            $limit,
            $offset,
        ];

        $countParams = [
            // TITLE MATCH
            $qAll,
            $siteId,
            $qAll,

            // TAG -> NEWS
            $siteId,
            $siteId,
            $qAll,

            // TAG -> LINK -> NEWS
            $siteId,
            $siteId,
            $siteId,
            $qAll,
        ];

        return [$sql, $params, $countSql, $countParams];
    }

    /*
     * Normal NEWS search.
     *
     * Only news_title and tag_title participate in matching.
     */
    $sql = "
        WITH matched_news AS (
            {$candidateSql}
        )
        SELECT
            'news' AS item_type,
            n.news_title AS title,
            n.news_image AS image,
            l.link_url AS url,
            COALESCE(n.news_published, n.news_time) AS published_at,
            n.news_id AS relation_id,
            CASE
                WHEN mn.priority = 2 THEN 'tag'
                ELSE NULL
            END AS tag_type,
            n.news_description AS description,
            mn.rel_score,
            mn.priority
        FROM matched_news mn
        INNER JOIN {$newsTable} n
            ON n.news_id = mn.news_id
        INNER JOIN {$linkTable} l
            ON l.link_relation_id = n.news_id
           AND l.link_type = 'news'
           AND l.link_status = 200
           AND l.link_site_id = ?
        ORDER BY {$sortBy},
                 mn.priority ASC,
                 mn.rel_score DESC,
                 published_at DESC
        LIMIT ? OFFSET ?
    ";

    $countSql = "
        SELECT COUNT(*) AS total
        FROM (
            {$candidateSql}
        ) matched
    ";

    $params = [
        // TITLE MATCH
        $qAll,
        $siteId,
        $qAll,

        // TAG -> NEWS
        $siteId,
        $siteId,
        $qAll,

        // TAG -> LINK -> NEWS
        $siteId,
        $siteId,
        $siteId,
        $qAll,

        // Result link
        $siteId,

        // Pagination
        $limit,
        $offset,
    ];

    $countParams = [
        // TITLE MATCH
        $qAll,
        $siteId,
        $qAll,

        // TAG -> NEWS
        $siteId,
        $siteId,
        $qAll,

        // TAG -> LINK -> NEWS
        $siteId,
        $siteId,
        $siteId,
        $qAll,
    ];

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
        if (!empty($row['category_url'])) {
            $row['category_url'] = ($row['category_url'][0] === '/') ? $row['category_url'] : '/' . ltrim((string)$row['category_url'], '/');
        }
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

/** ---------- Robust FALLBACK (tokenized LIKE) if no rows OR short news tokens ---------- */
$shouldAugmentNewsWithLike = ($source === 'news' && $hasShortToken);
$shouldReplaceNewsWithLike = ($source === 'news' && $hasShortToken);
if (!$rows || $shouldAugmentNewsWithLike) {
    $cleanRaw   = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', mb_strtolower($raw));
    $tokenArray = array_values(array_unique(array_filter(preg_split('/\s+/', trim($cleanRaw), -1, PREG_SPLIT_NO_EMPTY))));
    $isSingleTokenSearch = (count($tokenArray) === 1);
    $singleToken = $isSingleTokenSearch ? mb_strtolower((string)$tokenArray[0]) : '';
    $useAcronymPattern = ($isSingleTokenSearch && mb_strlen($singleToken) >= 3);
    $acronymPattern = $useAcronymPattern ? implode('% ', preg_split('//u', $singleToken, -1, PREG_SPLIT_NO_EMPTY)) . '%' : '';
    $isAiToken = ($singleToken === 'ai');
    $likePhrase = '%' . mysqli_real_escape_string($connection, $raw) . '%';
    $likeTokens = array_map(fn($t) => '%' . mysqli_real_escape_string($connection, $t) . '%', $tokenArray);

    $buildExactOrAcronymLike = function (string $alias, array $fields, string $token, string $acronymLike, bool $allowAcronym = false, bool $allowAiPhrase = false) {
        $parts = [];
        $params = [];
        foreach ($fields as $f) {
            $fieldChecks = [
                "LOWER({$alias}.{$f}) = ?",
                "LOWER({$alias}.{$f}) LIKE ?",
                "LOWER({$alias}.{$f}) LIKE ?",
                "LOWER({$alias}.{$f}) LIKE ?",
                "LOWER({$alias}.{$f}) LIKE ?",
                "LOWER({$alias}.{$f}) LIKE ?",
                "LOWER({$alias}.{$f}) LIKE ?",
                "LOWER({$alias}.{$f}) LIKE ?"
            ];
            $params[] = $token;
            $params[] = $token . ' %';
            $params[] = '% ' . $token;
            $params[] = '% ' . $token . ' %';
            $params[] = '%' . $token;
            $params[] = $token . ',%';
            $params[] = '%,' . $token;
            $params[] = '%,' . $token . ',%';

            if ($allowAcronym && $acronymLike !== '') {
                $fieldChecks[] = "LOWER({$alias}.{$f}) LIKE ?";
                $params[] = $acronymLike;
            }

            if ($allowAiPhrase) {
                $fieldChecks[] = "LOWER({$alias}.{$f}) LIKE ?";
                $params[] = '%artificial intelligence%';
            }

            $parts[] = '(' . implode(' OR ', $fieldChecks) . ')';
        }
        return ['(' . implode(' OR ', $parts) . ')', $params];
    };

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
            if ($isSingleTokenSearch && $singleToken !== '') {
                [$wTitle, $pTitle] = $buildExactOrAcronymLike('n', ['news_title'], $singleToken, $acronymPattern, $useAcronymPattern, $isAiToken);
                [$wTagLink, $pTagLink] = $buildExactOrAcronymLike('t', ['tag_title'], $singleToken, $acronymPattern, $useAcronymPattern, $isAiToken);
                [$wTagNews, $pTagNews] = $buildExactOrAcronymLike('tn', ['tag_title'], $singleToken, $acronymPattern, $useAcronymPattern, $isAiToken);
            } else {
                [$wTitle, $pTitle] = $buildLike('n', ['news_title'], $likePhrase, $likeTokens);
                [$wTagLink, $pTagLink] = $buildLike('t', ['tag_title'], $likePhrase, $likeTokens);
                [$wTagNews, $pTagNews] = $buildLike('tn', ['tag_title'], $likePhrase, $likeTokens);
            }
            $fbSql = "
                     SELECT DISTINCT
                        'news' AS item_type,
                        n.news_title AS title,
                        n.news_image AS image,
                        l.link_url AS url,
                        COALESCE(n.news_published, n.news_time) AS published_at,
                        n.news_id AS relation_id,
                        NULL AS tag_type,
                        n.news_description AS description,
                        c.category_title,
                        cl.link_url AS category_url,
                        p.profile_display_name AS author_name,
                        n.news_owner_id AS author_id,
                        n.news_paid,
                        0.1 AS rel_score
                     FROM " . TABLE_PREFIX . "news n
                     LEFT JOIN " . TABLE_PREFIX . "link l
                         ON l.link_relation_id=n.news_id AND l.link_type='news' AND l.link_status=200
                     INNER JOIN " . TABLE_PREFIX . "category c
                         ON c.category_id = n.news_category
                        AND c.category_type = 'news'
                        AND c.category_site_id = ?
                     LEFT JOIN " . TABLE_PREFIX . "link cl
                         ON cl.link_relation_id = c.category_id
                        AND cl.link_type = 'category'
                        AND cl.link_status = 200
                        AND cl.link_site_id = ?
                     LEFT JOIN " . TABLE_PREFIX . "profile p
                         ON p.profile_user_id = n.news_owner_id
                     LEFT JOIN " . TABLE_PREFIX . "tag t
                         ON t.tag_relation_id=l.link_id AND t.tag_type='news' AND t.tag_site_id=?
                     LEFT JOIN " . TABLE_PREFIX . "tag tn
                         ON tn.tag_relation_id=n.news_id AND tn.tag_type='news' AND tn.tag_site_id=?
                     WHERE n.news_site_id=?
                         AND n.news_status=1 AND n.news_is_public=1
                         AND ({$wTitle} OR {$wTagLink} OR {$wTagNews})
                     ORDER BY COALESCE(n.news_published, n.news_time) {$newsFallbackOrder}
                     LIMIT ? OFFSET ?";
            $fbParams = array_merge([$siteId, $siteId, $siteId, $siteId, $siteId], $pTitle, $pTagLink, $pTagNews, [$limit, $offset]);

            $fbCountSql = "
                     SELECT COUNT(DISTINCT n.news_id) AS total
                     FROM " . TABLE_PREFIX . "news n
                     LEFT JOIN " . TABLE_PREFIX . "link l
                         ON l.link_relation_id=n.news_id AND l.link_type='news' AND l.link_status=200
                     INNER JOIN " . TABLE_PREFIX . "category c
                         ON c.category_id = n.news_category
                        AND c.category_type = 'news'
                        AND c.category_site_id = ?
                     LEFT JOIN " . TABLE_PREFIX . "link cl
                         ON cl.link_relation_id = c.category_id
                        AND cl.link_type = 'category'
                        AND cl.link_status = 200
                        AND cl.link_site_id = ?
                     LEFT JOIN " . TABLE_PREFIX . "profile p
                         ON p.profile_user_id = n.news_owner_id
                     LEFT JOIN " . TABLE_PREFIX . "tag t
                         ON t.tag_relation_id=l.link_id AND t.tag_type='news' AND t.tag_site_id=?
                     LEFT JOIN " . TABLE_PREFIX . "tag tn
                         ON tn.tag_relation_id=n.news_id AND tn.tag_type='news' AND tn.tag_site_id=?
                     WHERE n.news_site_id=?
                         AND n.news_status=1 AND n.news_is_public=1
                         AND ({$wTitle} OR {$wTagLink} OR {$wTagNews})";
            $fbCountParams = array_merge([$siteId, $siteId, $siteId, $siteId, $siteId], $pTitle, $pTagLink, $pTagNews);
        } else {
            if ($isSingleTokenSearch && $singleToken !== '') {
                [$wExactTitle, $pExactTitle] = $buildExactOrAcronymLike('n', ['news_title'], $singleToken, $acronymPattern, $useAcronymPattern, $isAiToken);
                if (mb_strlen($singleToken) <= 3) {
                    $w = $wExactTitle;
                    $p = $pExactTitle;
                } else {
                    $w = $wExactTitle;
                    $p = $pExactTitle;
                }
            } else {
                [$w, $p] = $buildLike('n', ['news_title'], $likePhrase, $likeTokens);
            }
            $fbSql = "
                     SELECT
                        'news' AS item_type,
                        n.news_title AS title,
                        n.news_image AS image,
                        l.link_url AS url,
                        COALESCE(n.news_published, n.news_time) AS published_at,
                        n.news_id AS relation_id,
                        NULL AS tag_type,
                        n.news_description AS description,
                        c.category_title,
                        cl.link_url AS category_url,
                        p.profile_display_name AS author_name,
                        n.news_owner_id AS author_id,
                        n.news_paid,
                        0.1 AS rel_score
                     FROM " . TABLE_PREFIX . "news n
                     LEFT JOIN " . TABLE_PREFIX . "link l
                         ON l.link_relation_id=n.news_id AND l.link_type='news' AND l.link_status=200
                     INNER JOIN " . TABLE_PREFIX . "category c
                         ON c.category_id = n.news_category
                        AND c.category_type = 'news'
                        AND c.category_site_id = ?
                     LEFT JOIN " . TABLE_PREFIX . "link cl
                         ON cl.link_relation_id = c.category_id
                        AND cl.link_type = 'category'
                        AND cl.link_status = 200
                        AND cl.link_site_id = ?
                     LEFT JOIN " . TABLE_PREFIX . "profile p
                         ON p.profile_user_id = n.news_owner_id
                     WHERE n.news_site_id=?
                         AND n.news_status=1 AND n.news_is_public=1
                         AND {$w}
                     LIMIT ? OFFSET ?";
            $fbParams = array_merge([$siteId, $siteId, $siteId], $p, [$limit, $offset]);

            $fbCountSql = "
                     SELECT COUNT(DISTINCT n.news_id) AS total
                     FROM " . TABLE_PREFIX . "news n
                     LEFT JOIN " . TABLE_PREFIX . "link l
                         ON l.link_relation_id=n.news_id AND l.link_type='news' AND l.link_status=200
                     WHERE n.news_site_id=?
                         AND n.news_status=1 AND n.news_is_public=1
                         AND {$w}";
            $fbCountParams = array_merge([$siteId], $p);
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

    if ($shouldReplaceNewsWithLike) {
        $rows = [];
    }

    $existingResultKeys = [];
    foreach ($rows as $existingRow) {
        $existingResultKeys[$existingRow['item_type'] . ':' . $existingRow['relation_id']] = true;
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
            $resultKey = $row['item_type'] . ':' . $row['relation_id'];
            if (!isset($existingResultKeys[$resultKey])) {
                $rows[] = $row;
                $existingResultKeys[$resultKey] = true;
            }
        }
    }

    if ($shouldReplaceNewsWithLike && isset($fbCountSql, $fbCountParams)) {
        $countStmt = mysqli_prepare($connection, $fbCountSql);
        if ($countStmt) {
            $countTypes = '';
            foreach ($fbCountParams as $param) $countTypes .= is_int($param) ? 'i' : 's';
            mysqli_stmt_bind_param($countStmt, $countTypes, ...$fbCountParams);
            mysqli_stmt_execute($countStmt);
            $countRes = mysqli_stmt_get_result($countStmt);
            if ($countRes && ($countRow = mysqli_fetch_assoc($countRes))) {
                $total = (int)($countRow['total'] ?? 0);
                $pages = (int)ceil($total / $limit);
            }
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
