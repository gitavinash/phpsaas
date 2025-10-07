<?php
require_once SEARCH_FUNCTIONS;

$raw    = isset($searchKeyword) ? $searchKeyword : '';
$siteId = (int) SITE_ID;
$page   = max(1, (int)($_GET['page'] ?? 1));
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
    $innodb = 3;  // defaults that work well locally
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
    $allTokens   = []; // includes short tokens for LIKE fallback
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
 * NORMALIZED RESULT SHAPE returned to the template:
 * - item_type: 'tag' | 'post' | 'product'
 * - title, image, url, published_at
 * - relation_id: product_id when item is a product, or tag_relation_id when tag points to a product; NULL otherwise
 * - tag_type: 'product' for product tags, NULL for others
 * - rel_score: weighted relevance (strict + loose + tag matches)
 */
$sql = "
SELECT * FROM (
    /* ---------- TAGS ---------- */
    SELECT
        'tag' AS item_type,
        t.tag_title       AS title,
        t.tag_image       AS image,
        CONCAT('/tag/', l.link_url) AS url,
        t.tag_time        AS published_at,
        t.tag_relation_id AS relation_id,
        t.tag_type        AS tag_type,
        (
            (MATCH(t.tag_title, t.tag_description) AGAINST (? IN BOOLEAN MODE)) * 2.0
          + (MATCH(t.tag_title, t.tag_description) AGAINST (? IN BOOLEAN MODE)) * 0.7
        ) AS rel_score
    FROM " . TABLE_PREFIX . "tag t
    LEFT JOIN " . TABLE_PREFIX . "link l
        ON l.link_relation_id = t.tag_relation_id
       AND l.link_type = 'tag'
       AND l.link_status = 200
    WHERE t.tag_site_id = ?
      AND (
           MATCH(t.tag_title, t.tag_description) AGAINST (? IN BOOLEAN MODE)
        OR MATCH(t.tag_title, t.tag_description) AGAINST (? IN BOOLEAN MODE)
      )

    UNION ALL

    /* ---------- POSTS ---------- */
    SELECT
        'post' AS item_type,
        p.post_title     AS title,
        p.post_image     AS image,
        l.link_url       AS url,
        p.post_time      AS published_at,
        NULL             AS relation_id,
        NULL             AS tag_type,
        (
            (MATCH(p.post_title) AGAINST (? IN BOOLEAN MODE)) * 2.0
          + (MATCH(p.post_title, p.post_highlight, p.post_description) AGAINST (? IN BOOLEAN MODE)) * 1.25
          + (MATCH(p.post_title) AGAINST (? IN BOOLEAN MODE)) * 0.6
          + (MATCH(p.post_title, p.post_highlight, p.post_description) AGAINST (? IN BOOLEAN MODE)) * 0.5
        ) AS rel_score
    FROM " . TABLE_PREFIX . "post p
    INNER JOIN " . TABLE_PREFIX . "link l
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

    UNION ALL

    /* ---------- PRODUCTS (title/desc + product TAGS) ---------- */
    SELECT
        'product'        AS item_type,
        pr.product_title AS title,
        pr.product_image AS image,
        l.link_url       AS url,
        pr.product_time  AS published_at,
        pr.product_id    AS relation_id,
        NULL             AS tag_type,
        (
            (MATCH(pr.product_title) AGAINST (? IN BOOLEAN MODE)) * 2.2
          + (MATCH(pr.product_title, pr.product_highlight, pr.product_description) AGAINST (? IN BOOLEAN MODE)) * 1.2
          + IFNULL((MATCH(tprod.tag_title, tprod.tag_description) AGAINST (? IN BOOLEAN MODE)) * 1.8, 0)

          + (MATCH(pr.product_title) AGAINST (? IN BOOLEAN MODE)) * 0.7
          + (MATCH(pr.product_title, pr.product_highlight, pr.product_description) AGAINST (? IN BOOLEAN MODE)) * 0.6
          + IFNULL((MATCH(tprod.tag_title, tprod.tag_description) AGAINST (? IN BOOLEAN MODE)) * 0.7, 0)
        ) AS rel_score
    FROM " . TABLE_PREFIX . "product pr
    INNER JOIN " . TABLE_PREFIX . "link l
        ON l.link_relation_id = pr.product_id
       AND l.link_type = 'product'
       AND l.link_status = 200
    LEFT JOIN " . TABLE_PREFIX . "tag tprod
      ON tprod.tag_type = 'product'
     AND tprod.tag_relation_id = pr.product_id
     AND tprod.tag_site_id = pr.product_site_id
    WHERE pr.product_site_id = ?
      AND (
           MATCH(pr.product_title) AGAINST (? IN BOOLEAN MODE)
        OR MATCH(pr.product_title, pr.product_highlight, pr.product_description) AGAINST (? IN BOOLEAN MODE)
        OR (tprod.tag_id IS NOT NULL AND MATCH(tprod.tag_title, tprod.tag_description) AGAINST (? IN BOOLEAN MODE))

        OR MATCH(pr.product_title) AGAINST (? IN BOOLEAN MODE)
        OR MATCH(pr.product_title, pr.product_highlight, pr.product_description) AGAINST (? IN BOOLEAN MODE)
        OR (tprod.tag_id IS NOT NULL AND MATCH(tprod.tag_title, tprod.tag_description) AGAINST (? IN BOOLEAN MODE))
      )
) AS u
/* final score = relevance * mild freshness */
CROSS JOIN (SELECT NOW() AS nowref) AS _t
WHERE rel_score > 0
ORDER BY (rel_score * POW(0.98, DATEDIFF(CURDATE(), DATE(u.published_at)))) DESC, u.published_at DESC
LIMIT ? OFFSET ?";

/* Bind params in left-to-right order */
$params = [
    // TAGS (rel_score + WHERE)
    $qAll,
    $qAny,
    $siteId,
    $qAll,
    $qAny,

    // POSTS (rel_score + WHERE)
    $qAll,
    $qAll,
    $qAny,
    $qAny,
    $siteId,
    $qAll,
    $qAll,
    $qAny,
    $qAny,

    // PRODUCTS (rel_score strict+loose)
    $qAll,
    $qAll,
    $qAll,
    $qAny,
    $qAny,
    $qAny,
    // PRODUCTS WHERE
    $siteId,
    $qAll,
    $qAll,
    $qAll,
    $qAny,
    $qAny,
    $qAny,

    // LIMIT/OFFSET
    $limit,
    $offset
];

$stmt = mysqli_prepare($connection, $sql);
if (!$stmt) {
    error_log('Search prepare failed: ' . mysqli_error($connection));
    http_response_code(500);
    require_once ERROR_500;
    exit;
}

/* Auto-build $types and execute (prevents bind count mismatches) */
$types = '';
foreach ($params as $p) {
    $types .= is_int($p) ? 'i' : 's';
}
mysqli_stmt_bind_param($stmt, $types, ...$params);

$ok = mysqli_stmt_execute($stmt);
if (!$ok) {
    error_log('Search execute failed: ' . mysqli_stmt_error($stmt));
}
$res = $ok ? mysqli_stmt_get_result($stmt) : false;

$results = [];
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $row['url'] = (!empty($row['url']) && $row['url'][0] === '/') ? $row['url'] : '/' . ltrim((string)($row['url'] ?? ''), '/');
        $results[] = $row;
    }
}

/* ---------- Robust FALLBACK (tokenized LIKE) for server FT differences ---------- */
if (!$results) {
    // tokens for LIKE across fields (covers short tokens and stopwords)
    $cleanRaw   = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', mb_strtolower($raw));
    $tokenArray = array_values(array_unique(array_filter(preg_split('/\s+/', trim($cleanRaw), -1, PREG_SPLIT_NO_EMPTY))));
    $likePhrase = '%' . mysqli_real_escape_string($connection, $raw) . '%';
    $likeTokens = array_map(function ($t) use ($connection) {
        return '%' . mysqli_real_escape_string($connection, $t) . '%';
    }, $tokenArray);

    // helper: (alias.field1 LIKE ? OR alias.field2 LIKE ? ... for phrase & tokens)
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

    [$tagWhere,  $tagParams]  = $buildLike('t',   ['tag_title', 'tag_description'],                          $likePhrase, $likeTokens);
    [$postWhere, $postParams] = $buildLike('p',   ['post_title', 'post_highlight', 'post_description'],       $likePhrase, $likeTokens);
    [$prodWhere, $prodParams] = $buildLike('pr',  ['product_title', 'product_highlight', 'product_description'], $likePhrase, $likeTokens);
    [$ptagWhere, $ptagParams] = $buildLike('tprod', ['tag_title', 'tag_description'],                        $likePhrase, $likeTokens);

    $fallbackSql = "
        SELECT 'tag' AS item_type, t.tag_title AS title, t.tag_image AS image, '' AS url, t.tag_time AS published_at,
               t.tag_relation_id AS relation_id, t.tag_type AS tag_type, 0.1 AS rel_score
        FROM " . TABLE_PREFIX . "tag t
        WHERE t.tag_site_id = ?
          AND {$tagWhere}

        UNION ALL

        SELECT 'post', p.post_title, p.post_image, l.link_url, p.post_time,
               NULL AS relation_id, NULL AS tag_type, 0.1
        FROM " . TABLE_PREFIX . "post p
        JOIN " . TABLE_PREFIX . "link l
             ON l.link_relation_id=p.post_id
            AND l.link_type='post'
            AND l.link_status=200
        WHERE p.post_site_id = ?
          AND {$postWhere}

        UNION ALL

        SELECT 'product', pr.product_title, pr.product_image, l.link_url, pr.product_time,
               pr.product_id AS relation_id, NULL AS tag_type, 0.1
        FROM " . TABLE_PREFIX . "product pr
        JOIN " . TABLE_PREFIX . "link l
             ON l.link_relation_id=pr.product_id
            AND l.link_type='product'
            AND l.link_status=200
        LEFT JOIN " . TABLE_PREFIX . "tag tprod
               ON tprod.tag_type='product'
              AND tprod.tag_relation_id=pr.product_id
              AND tprod.tag_site_id=pr.product_site_id
        WHERE pr.product_site_id = ?
          AND ( {$prodWhere} OR {$ptagWhere} )
        LIMIT ? OFFSET ?";

    $fb = mysqli_prepare($connection, $fallbackSql);
    if ($fb) {
        // Build params dynamically so counts always match
        $fbParams = [];
        $fbParams[] = $siteId; // TAGS site_id
        $fbParams   = array_merge($fbParams, $tagParams);

        $fbParams[] = $siteId; // POSTS site_id
        $fbParams   = array_merge($fbParams, $postParams);

        $fbParams[] = $siteId; // PRODUCTS site_id
        $fbParams   = array_merge($fbParams, $prodParams, $ptagParams);

        $fbParams[] = $limit;
        $fbParams[] = $offset;

        $fbTypes = '';
        foreach ($fbParams as $p) {
            $fbTypes .= is_int($p) ? 'i' : 's';
        }

        mysqli_stmt_bind_param($fb, $fbTypes, ...$fbParams);
        mysqli_stmt_execute($fb);
        $fbr = mysqli_stmt_get_result($fb);
        while ($row = mysqli_fetch_assoc($fbr)) {
            $row['url'] = (!empty($row['url']) && $row['url'][0] === '/') ? $row['url'] : '/' . ltrim((string)($row['url'] ?? ''), '/');
            $results[] = $row;
        }
    } else {
        error_log('Fallback prepare failed: ' . mysqli_error($connection));
    }
}

/* Hand off to the view */
$search_path = USER_PATH . "search.php";
$searchData  = [
    'q'       => $raw,
    'results' => $results,
    'page'    => $page,
    'limit'   => $limit
];

if (file_exists($search_path)) {
    require $search_path;
} else {
    require USER_PATH . 'search.php';
}