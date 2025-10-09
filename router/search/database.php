<?php
require_once SEARCH_FUNCTIONS;

$raw    = isset($searchKeyword) ? $searchKeyword : '';
$siteId = (int) SITE_ID;
$pageParam = $_GET['page'] ?? $_GET['pg'] ?? 1;   // ← accept both
$page = max(1, (int)$pageParam);
$limit  = 20;
$offset = ($page - 1) * $limit;

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


/**
 * Build SQL, COUNT SQL, and params based on source
 * normalized columns: item_type, title, image, url, published_at, relation_id, tag_type, rel_score
 */
function buildSqlBySource(string $source, int $siteId, string $qAll, string $qAny, int $limit, int $offset): array
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
                ORDER BY ( (MATCH(pr.product_title) AGAINST (? IN BOOLEAN MODE)) * 2.2
                         + (MATCH(pr.product_title, pr.product_highlight, pr.product_description) AGAINST (? IN BOOLEAN MODE)) * 1.2 ) DESC,
                         pr.product_time DESC
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
            $sql = "
                SELECT
                    'news' AS item_type,
                    n.news_title     AS title,
                    n.news_image     AS image,
                    l.link_url       AS url,
                    n.news_time      AS published_at,
                    n.news_id        AS relation_id,
                    NULL             AS tag_type,
                    n.news_description AS description,
                    (
                        (MATCH(n.news_title) AGAINST (? IN BOOLEAN MODE)) * 2.0
                      + (MATCH(n.news_title, n.news_highlight, n.news_description) AGAINST (? IN BOOLEAN MODE)) * 1.25
                      + (MATCH(n.news_title) AGAINST (? IN BOOLEAN MODE)) * 0.6
                      + (MATCH(n.news_title, n.news_highlight, n.news_description) AGAINST (? IN BOOLEAN MODE)) * 0.5
                    ) AS rel_score
                FROM " . TABLE_PREFIX . "news n
                LEFT JOIN " . TABLE_PREFIX . "link l
                  ON l.link_relation_id = n.news_id
                 AND l.link_type = 'news'
                 AND l.link_status = 200
                WHERE n.news_site_id = ?
                  AND n.news_status = 1
                  AND n.news_is_public = 1
                  AND (
                       MATCH(n.news_title) AGAINST (? IN BOOLEAN MODE)
                    OR MATCH(n.news_title, n.news_highlight, n.news_description) AGAINST (? IN BOOLEAN MODE)
                    OR MATCH(n.news_title) AGAINST (? IN BOOLEAN MODE)
                    OR MATCH(n.news_title, n.news_highlight, n.news_description) AGAINST (? IN BOOLEAN MODE)
                  )
                ORDER BY ( (MATCH(n.news_title) AGAINST (? IN BOOLEAN MODE)) * 2.0
                         + (MATCH(n.news_title, n.news_highlight, n.news_description) AGAINST (? IN BOOLEAN MODE)) * 1.25 ) DESC,
                         COALESCE(n.news_published, n.news_time) DESC
                LIMIT ? OFFSET ?";

            $countSql = "
                SELECT COUNT(*) AS total
                FROM " . TABLE_PREFIX . "news n
                LEFT JOIN " . TABLE_PREFIX . "link l
                  ON l.link_relation_id = n.news_id
                 AND l.link_type = 'news'
                 AND l.link_status = 200
                WHERE n.news_site_id = ?
                  AND n.news_status = 1
                  AND n.news_is_public = 1
                  AND (
                       MATCH(n.news_title) AGAINST (? IN BOOLEAN MODE)
                    OR MATCH(n.news_title, n.news_highlight, n.news_description) AGAINST (? IN BOOLEAN MODE)
                    OR MATCH(n.news_title) AGAINST (? IN BOOLEAN MODE)
                    OR MATCH(n.news_title, n.news_highlight, n.news_description) AGAINST (? IN BOOLEAN MODE)
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
                ORDER BY ( (MATCH(p.post_title) AGAINST (? IN BOOLEAN MODE)) * 2.0
                         + (MATCH(p.post_title, p.post_highlight, p.post_description) AGAINST (? IN BOOLEAN MODE)) * 1.25 ) DESC,
                         p.post_time DESC
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

[$sql, $params, $countSql, $countParams] = buildSqlBySource($source, $siteId, $qAll, $qAny, $limit, $offset);

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
        [$w, $p] = $buildLike('n', ['news_title', 'news_highlight', 'news_description'], $likePhrase, $likeTokens);
        $fbSql = "
           SELECT 'news' AS item_type, n.news_title AS title, n.news_image AS image, l.link_url AS url,
                  n.news_time AS published_at, n.news_id AS relation_id, NULL AS tag_type, 0.1 AS rel_score
           FROM " . TABLE_PREFIX . "news n
           LEFT JOIN " . TABLE_PREFIX . "link l
             ON l.link_relation_id=n.news_id AND l.link_type='news' AND l.link_status=200
           WHERE n.news_site_id=?
             AND n.news_status=1 AND n.news_is_public=1
             AND {$w}
           LIMIT ? OFFSET ?";
        $fbParams = array_merge([$siteId], $p, [$limit, $offset]);
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