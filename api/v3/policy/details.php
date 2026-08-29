<?php
/**
 * V3 Policy Details API
 * Endpoint: /api/v3/policy/details.php
 * Method: GET
 * Params: id (encoded or numeric page_id)
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

$rawId = $_GET['id'] ?? ($_GET['page_id'] ?? '');
$pageId = 0;

if (is_numeric($rawId) && (int)$rawId > 0) {
    $pageId = (int)$rawId;
} elseif (!empty($rawId)) {
    $decoded = base64_decode($rawId, true);
    if ($decoded && is_numeric($decoded)) {
        $pageId = (int)$decoded;
    }
}

if ($pageId <= 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'A valid policy ID is required.'
    ]);
    exit;
}

$siteFilter = $siteId > 0 ? "AND l.link_site_id = {$siteId}" : "";

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
        l.link_creation,
        m.meta_title,
        m.meta_description,
        m.meta_keyword
    FROM {$tblPrefix}page p
    INNER JOIN {$tblPrefix}link l ON l.link_relation_id = p.page_id
    LEFT JOIN {$tblPrefix}meta m ON m.meta_relation_id = l.link_id AND m.meta_type = 'page'
    WHERE p.page_id = {$pageId} AND p.page_type = 'policy' AND l.link_type = 'page' {$siteFilter}
    LIMIT 1
";

$res = mysqli_query($connection, $query);

if (!$res || mysqli_num_rows($res) === 0) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Policy record not found.'
    ]);
    exit;
}

$row = mysqli_fetch_assoc($res);

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

$cleanSlug = trim($row['link_url'] ?? '', '/');

echo json_encode([
    'status' => 'success',
    'policy' => [
        'id' => (int)$row['page_id'],
        'encoded_id' => base64_encode($row['page_id']),
        'title' => stripslashes($row['page_title'] ?? ''),
        'slug' => $cleanSlug,
        'full_slug' => '/' . $cleanSlug . '/',
        'description' => stripslashes($row['page_description'] ?? ''),
        'status' => (string)($row['page_status'] ?? '1'),
        'image' => $row['page_image'] ?? '',
        'image_url' => $imageUrl,
        'thumbnail' => $row['page_thumbnail'] ?? '',
        'meta_title' => stripslashes($row['meta_title'] ?? ''),
        'meta_description' => stripslashes($row['meta_description'] ?? ''),
        'meta_keywords' => stripslashes($row['meta_keyword'] ?? ''),
        'published_at' => $row['link_published'] ?? '',
        'created_at' => $row['link_creation'] ?? ''
    ]
]);
