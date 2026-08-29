<?php
/**
 * V3 Update Policy API
 * Endpoint: /api/v3/policy/update.php
 * Method: POST (multipart/form-data or json)
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

$currentUserId = defined('USER_ID') ? (int)USER_ID : (int)($_SESSION['user_id'] ?? 0);
if ($currentUserId <= 0 && isset($_COOKIE['user_id'])) {
    $currentUserId = (int)base64_decode($_COOKIE['user_id']);
}

$rawInput = file_get_contents('php://input');
$jsonData = json_decode($rawInput, true) ?: [];

// Get ID
$rawId = $_POST['id'] ?? ($_POST['policy_id'] ?? ($_REQUEST['id'] ?? ($jsonData['id'] ?? ($jsonData['policy_id'] ?? ''))));
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
    echo json_encode(['status' => 'error', 'msg' => 'A valid policy ID is required.']);
    exit;
}

// Fetch existing record
$siteFilter = $siteId > 0 ? "AND l.link_site_id = {$siteId}" : "";
$checkQuery = mysqli_query($connection, "
    SELECT p.*, l.link_id, l.link_url
    FROM {$tblPrefix}page p
    INNER JOIN {$tblPrefix}link l ON l.link_relation_id = p.page_id
    WHERE p.page_id = {$pageId} AND p.page_type = 'policy' AND l.link_type = 'page' {$siteFilter}
    LIMIT 1
");

if (!$checkQuery || mysqli_num_rows($checkQuery) === 0) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'msg' => 'Policy record not found.']);
    exit;
}

$existing = mysqli_fetch_assoc($checkQuery);
$linkId   = (int)$existing['link_id'];

// Inputs
$title           = trim($_POST['title'] ?? ($_REQUEST['title'] ?? ($jsonData['title'] ?? '')));
$slug            = trim($_POST['slug'] ?? ($_REQUEST['slug'] ?? ($jsonData['slug'] ?? '')));
$description     = trim($_POST['description'] ?? ($_REQUEST['description'] ?? ($jsonData['description'] ?? '')));
$status          = trim($_POST['status'] ?? ($_REQUEST['status'] ?? ($jsonData['status'] ?? $existing['page_status'])));
$metaTitle       = trim($_POST['meta_title'] ?? ($_REQUEST['meta_title'] ?? ($jsonData['meta_title'] ?? '')));
$metaDescription = trim($_POST['meta_description'] ?? ($_REQUEST['meta_description'] ?? ($jsonData['meta_description'] ?? '')));
$metaKeywords    = trim($_POST['meta_keywords'] ?? ($_REQUEST['meta_keywords'] ?? ($jsonData['meta_keywords'] ?? '')));

if (empty($title)) {
    http_response_code(422);
    echo json_encode(['status' => 'error', 'msg' => 'Policy title is required.', 'field' => 'title']);
    exit;
}

if (empty($description)) {
    http_response_code(422);
    echo json_encode(['status' => 'error', 'msg' => 'Policy description content is required.', 'field' => 'description']);
    exit;
}

// Handle URL slug
if (!empty($slug)) {
    $cleanSlug = strtolower(preg_replace('~[^\p{M}\w-]+~u', '-', $slug));
    $cleanSlug = trim(preg_replace('/-+/', '-', $cleanSlug), '-');
    $url = '/' . $cleanSlug . '/';
} else {
    $url = $existing['link_url'];
}

// Image Upload via imageUploader.php if present
$featureImage   = $existing['page_image'] ?? '';
$thumbnailImage = $existing['page_thumbnail'] ?? '';

if (!empty($_FILES['featureImage']['tmp_name']) && is_uploaded_file($_FILES['featureImage']['tmp_name'])) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);

    if (!defined('ROOT')) define('ROOT', $docRoot);
    if (!defined('MODULES')) define('MODULES', $docRoot . '/lib/modules/');
    if (!defined('LOGIN_CHECK')) define('LOGIN_CHECK', $docRoot . '/lib/modules/isLogin.php');
    if (!defined('PNG_IMAGE')) define('PNG_IMAGE', 'image/png');
    if (!defined('JPEG_IMAGE')) define('JPEG_IMAGE', 'image/jpeg');
    if (!defined('JPG_IMAGE')) define('JPG_IMAGE', 'image/jpg');
    if (!defined('GIF_IMAGE')) define('GIF_IMAGE', 'image/gif');
    if (!defined('IMAGE_COMPRESSION_QUALITY')) define('IMAGE_COMPRESSION_QUALITY', 80);
    if (!defined('IMAGE_THUMBNAIL_WIDTH')) define('IMAGE_THUMBNAIL_WIDTH', 400);
    if (!defined('IMAGE_THUMBNAIL_HEIGHT')) define('IMAGE_THUMBNAIL_HEIGHT', 250);
    if (!defined('SITE_ID')) define('SITE_ID', $siteId);
    if (!defined('SITE_DOMAIN')) {
        $siteDomain = !empty($_SERVER['HTTP_HOST']) ? preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST']) : 'default';
        define('SITE_DOMAIN', $siteDomain);
    }

    if (file_exists(MODULES . 'imageUploader.php')) {
        require_once MODULES . 'imageUploader.php';
        if (!empty($image['featureImage'])) {
            $featureImage = $image['featureImage'];
        }
        if (!empty($image['thumbnailImage'])) {
            $thumbnailImage = $image['thumbnailImage'];
        }
    }
}

// Escapes
$escTitle        = mysqli_real_escape_string($connection, $title);
$escDesc         = mysqli_real_escape_string($connection, $description);
$escStatus       = mysqli_real_escape_string($connection, $status);
$escImage        = mysqli_real_escape_string($connection, $featureImage);
$escThumb        = mysqli_real_escape_string($connection, $thumbnailImage);
$escUrl          = mysqli_real_escape_string($connection, $url);
$escMetaTitle    = mysqli_real_escape_string($connection, !empty($metaTitle) ? $metaTitle : $title);
$escMetaDesc     = mysqli_real_escape_string($connection, !empty($metaDescription) ? $metaDescription : mb_substr(strip_tags($description), 0, 160));
$escMetaKeys     = mysqli_real_escape_string($connection, $metaKeywords);

mysqli_begin_transaction($connection);

try {
    // 1. Update isw_page
    $updatePage = "
        UPDATE {$tblPrefix}page SET
            page_title = '{$escTitle}',
            page_description = '{$escDesc}',
            page_image = '{$escImage}',
            page_thumbnail = '{$escThumb}',
            page_status = '{$escStatus}'
        WHERE page_id = {$pageId}
    ";
    if (!mysqli_query($connection, $updatePage)) {
        throw new Exception("Error updating policy page: " . mysqli_error($connection));
    }

    // 2. Update isw_link
    $updateLink = "
        UPDATE {$tblPrefix}link SET
            link_url = '{$escUrl}',
            link_published = NOW()
        WHERE link_id = {$linkId}
    ";
    mysqli_query($connection, $updateLink);

    // 3. Update or Insert isw_meta
    $metaCheck = mysqli_query($connection, "SELECT meta_id FROM {$tblPrefix}meta WHERE meta_relation_id = {$linkId} AND meta_type = 'page' LIMIT 1");
    if ($metaCheck && mysqli_num_rows($metaCheck) > 0) {
        $metaRow = mysqli_fetch_assoc($metaCheck);
        $updateMeta = "
            UPDATE {$tblPrefix}meta SET
                meta_title = '{$escMetaTitle}',
                meta_description = '{$escMetaDesc}',
                meta_keyword = '{$escMetaKeys}'
            WHERE meta_id = {$metaRow['meta_id']}
        ";
        mysqli_query($connection, $updateMeta);
    } else {
        $insertMeta = "
            INSERT INTO {$tblPrefix}meta (
                meta_relation_id, meta_site_id, meta_title, meta_description,
                meta_keyword, meta_type
            ) VALUES (
                {$linkId}, {$siteId}, '{$escMetaTitle}', '{$escMetaDesc}',
                '{$escMetaKeys}', 'page'
            )
        ";
        mysqli_query($connection, $insertMeta);
    }

    mysqli_commit($connection);

    echo json_encode([
        'status' => 'success',
        'msg' => 'Policy updated successfully!',
        'policy_id' => $pageId,
        'encoded_id' => base64_encode($pageId),
        'url' => $url
    ]);
} catch (Exception $e) {
    mysqli_rollback($connection);
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg' => $e->getMessage()
    ]);
}
