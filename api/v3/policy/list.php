<?php
/**
 * V3 Policies List API
 * Endpoint: /api/v3/policy/list.php
 * Method: GET
 * Params: search, status, page, limit
 */

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId    = defined('SITE_ID') && (int)SITE_ID > 0 ? (int)SITE_ID : (isset($_SESSION['site_id']) ? (int)$_SESSION['site_id'] : 0);
if ($siteId <= 0 && isset($_COOKIE['user_site_id'])) {
    $siteId = (int)base64_decode($_COOKIE['user_site_id']);
}
if ($siteId <= 0 && isset($connection)) {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $host = preg_replace('/:\d+$/', '', $host);
    if (!empty($host)) {
        $escapedHost = mysqli_real_escape_string($connection, $host);
        $sQuery = mysqli_query($connection, "SELECT site_id FROM {$tblPrefix}site WHERE site_domain = '{$escapedHost}' OR site_url LIKE '%{$escapedHost}%' LIMIT 1");
        if ($sQuery && $sRow = mysqli_fetch_assoc($sQuery)) {
            $siteId = (int)$sRow['site_id'];
        }
    }
}

$search = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');

$whereClauses = [
    "p.page_type = 'policy'",
    "l.link_type = 'page'"
];

if ($siteId > 0) {
    $whereClauses[] = "l.link_site_id = {$siteId}";
}

if ($status !== '' && $status !== 'all') {
    $escStatus = mysqli_real_escape_string($connection, $status);
    $whereClauses[] = "p.page_status = '{$escStatus}'";
}

if (!empty($search)) {
    $escSearch = mysqli_real_escape_string($connection, $search);
    $whereClauses[] = "(p.page_title LIKE '%{$escSearch}%' OR l.link_url LIKE '%{$escSearch}%' OR p.page_description LIKE '%{$escSearch}%')";
}

$whereSql = implode(" AND ", $whereClauses);

$query = "
    SELECT 
        p.page_id,
        p.page_title,
        p.page_description,
        p.page_image,
        p.page_thumbnail,
        p.page_status,
        p.page_owner_id,
        l.link_id,
        l.link_url,
        l.link_status,
        l.link_published,
        l.link_creation
    FROM {$tblPrefix}page p
    INNER JOIN {$tblPrefix}link l ON l.link_relation_id = p.page_id
    WHERE {$whereSql}
    ORDER BY p.page_id DESC
";

$res = mysqli_query($connection, $query);

$policies = [];
$totalCount = 0;
$activeCount = 0;
$draftCount = 0;
$publishedCount = 0;

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $totalCount++;
        $pStatus = (string)$row['page_status'];
        if ($pStatus === '1' || strtolower($pStatus) === 'active') {
            $activeCount++;
        } else {
            $draftCount++;
        }
        if ((int)$row['link_status'] === 200) {
            $publishedCount++;
        }

        // Format Image URL
        $imageUrl = '';
        if (!empty($row['page_image'])) {
            if (filter_var($row['page_image'], FILTER_VALIDATE_URL)) {
                $imageUrl = $row['page_image'];
            } elseif (strpos($row['page_image'], '/') === 0) {
                $imageUrl = $row['page_image'];
            } else {
                $imageUrl = '/' . $row['page_image'];
            }
        }

        // Snippet excerpt
        $cleanDesc = strip_tags($row['page_description'] ?? '');
        $excerpt = mb_substr($cleanDesc, 0, 120);
        if (mb_strlen($cleanDesc) > 120) {
            $excerpt .= '...';
        }

        $policies[] = [
            'id' => (int)$row['page_id'],
            'encoded_id' => base64_encode($row['page_id']),
            'title' => stripslashes($row['page_title'] ?? ''),
            'slug' => $row['link_url'] ?? '',
            'excerpt' => $excerpt,
            'image' => $row['page_image'] ?? '',
            'image_url' => $imageUrl,
            'thumbnail' => $row['page_thumbnail'] ?? '',
            'status' => $pStatus,
            'link_status' => (int)$row['link_status'],
            'published_at' => $row['link_published'] ? date('d M Y, h:i A', strtotime($row['link_published'])) : '',
            'created_at' => $row['link_creation'] ? date('d M Y', strtotime($row['link_creation'])) : '',
            'public_url' => $row['link_url'] ?? '#',
            'edit_url' => '/dashboard/policy/update/?id=' . base64_encode($row['page_id'])
        ];
    }
}

// Global Summary Stats for Site
$statsQuery = "
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN p.page_status = '1' THEN 1 ELSE 0 END) as active_count,
        SUM(CASE WHEN l.link_status = 200 THEN 1 ELSE 0 END) as published_count
    FROM {$tblPrefix}page p
    INNER JOIN {$tblPrefix}link l ON l.link_relation_id = p.page_id
    WHERE p.page_type = 'policy' AND l.link_type = 'page' " . ($siteId > 0 ? "AND l.link_site_id = {$siteId}" : "");
$statsRes = mysqli_query($connection, $statsQuery);
$statsRow = $statsRes ? mysqli_fetch_assoc($statsRes) : [];

echo json_encode([
    'status' => 'success',
    'policies' => $policies,
    'summary' => [
        'total' => (int)($statsRow['total'] ?? $totalCount),
        'active' => (int)($statsRow['active_count'] ?? $activeCount),
        'published' => (int)($statsRow['published_count'] ?? $publishedCount),
        'draft' => (int)($statsRow['total'] ?? $totalCount) - (int)($statsRow['active_count'] ?? $activeCount)
    ]
]);
