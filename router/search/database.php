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

/** Build strict(+word*) and loose(word*) boolean queries */
if (!function_exists('buildBooleanQueries')) {
    function buildBooleanQueries(string $q): array
    {
        $q = trim(mb_strtolower($q));
        $q = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $q);
        $parts = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY);
        $tokens = [];
        foreach ($parts as $w) {
            if (mb_strlen($w) >= 2) $tokens[] = $w;
        }
        if (!$tokens && $q !== '') $tokens[] = mb_substr($q, 0, 20);
        $strict = implode(' ', array_map(fn($t) => '+' . $t . '*', $tokens)); // all terms
        $loose  = implode(' ', array_map(fn($t) =>      $t . '*',  $tokens)); // any term
        return [$strict, $loose];
    }
}
[$qAll, $qAny] = buildBooleanQueries($raw);

/**
 * NORMALIZED RESULT SHAPE
 * - item_type: 'tag' | 'post' | 'product'
 * - title, image, url, published_at
 * - relation_id: product_id when item is a product, or tag_relation_id when tag points to a product; NULL otherwise
 * - tag_type: 'product' for product tags, NULL for others
 * - rel_score: weighted relevance (strict + loose)
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
CROSS JOIN (SELECT NOW() AS nowref) AS _t
WHERE rel_score > 0
ORDER BY (rel_score * POW(0.98, DATEDIFF(CURDATE(), DATE(u.published_at)))) DESC, u.published_at DESC
LIMIT ? OFFSET ?";

/* Bind params in order of appearance */
$params = [
    /* TAGS score */
    $qAll,
    $qAny,
    /* TAGS where */
    $siteId,
    $qAll,
    $qAny,

    /* POSTS score */
    $qAll,
    $qAll,
    $qAny,
    $qAny,
    /* POSTS where */
    $siteId,
    $qAll,
    $qAll,
    $qAny,
    $qAny,

    /* PRODUCTS score */
    $qAll,
    $qAll,
    $qAll,
    $qAny,
    $qAny,
    $qAny,
    /* PRODUCTS where */
    $siteId,
    $qAll,
    $qAll,
    $qAll,
    $qAny,
    $qAny,
    $qAny,

    /* LIMIT/OFFSET */
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

/* Auto-build $types string */
$types = '';
foreach ($params as $p) {
    $types .= is_int($p) ? 'i' : 's';
}

mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$results = [];
while ($row = mysqli_fetch_assoc($res)) {
    $row['url'] = (!empty($row['url']) && $row['url'][0] === '/') ? $row['url'] : '/' . ltrim((string)($row['url'] ?? ''), '/');
    $results[] = $row;
}

/* ---------- Fallback (LIKE) with AUTO types to avoid mismatches ---------- */
if (!$results) {
    $like = '%' . mysqli_real_escape_string($connection, $raw) . '%';
    $fallbackSql = "
        SELECT 'tag' AS item_type, tag_title AS title, tag_image AS image, '' AS url, tag_time AS published_at,
               tag_relation_id AS relation_id, tag_type AS tag_type, 0.1 AS rel_score
        FROM " . TABLE_PREFIX . "tag
        WHERE tag_site_id = ? AND (tag_title LIKE ? OR tag_description LIKE ?)
        UNION ALL
        SELECT 'post', post_title, post_image, l.link_url, post_time,
               NULL AS relation_id, NULL AS tag_type, 0.1
        FROM " . TABLE_PREFIX . "post p
        JOIN " . TABLE_PREFIX . "link l ON l.link_relation_id=p.post_id AND l.link_type='post' AND l.link_status=200
        WHERE p.post_site_id = ? AND (p.post_title LIKE ? OR p.post_highlight LIKE ? OR p.post_description LIKE ?)
        UNION ALL
        SELECT 'product', pr.product_title, pr.product_image, l.link_url, pr.product_time,
               pr.product_id AS relation_id, NULL AS tag_type, 0.1
        FROM " . TABLE_PREFIX . "product pr
        JOIN " . TABLE_PREFIX . "link l ON l.link_relation_id=pr.product_id AND l.link_type='product' AND l.link_status=200
        LEFT JOIN " . TABLE_PREFIX . "tag tprod
          ON tprod.tag_type='product'
         AND tprod.tag_relation_id=pr.product_id
         AND tprod.tag_site_id=pr.product_site_id
        WHERE pr.product_site_id = ?
          AND (pr.product_title LIKE ? OR pr.product_highlight LIKE ? OR pr.product_description LIKE ?
               OR tprod.tag_title LIKE ? OR tprod.tag_description LIKE ?)
        LIMIT ? OFFSET ?";

    $fb = mysqli_prepare($connection, $fallbackSql);
    if ($fb) {
        /* Build params & types dynamically so counts always match */
        $fbParams = [
            $siteId,
            $like,
            $like,
            $siteId,
            $like,
            $like,
            $like,
            $siteId,
            $like,
            $like,
            $like,
            $like,
            $like,
            $limit,
            $offset
        ];
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

/* Hand off to template */
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