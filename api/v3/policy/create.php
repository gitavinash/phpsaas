<?php
/**
 * V3 Create Policy API
 * Endpoint: /api/v3/policy/create.php
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

// Inputs
$title           = trim($_POST['title'] ?? ($_REQUEST['title'] ?? ($jsonData['title'] ?? '')));
$slug            = trim($_POST['slug'] ?? ($_REQUEST['slug'] ?? ($jsonData['slug'] ?? '')));
$description     = trim($_POST['description'] ?? ($_REQUEST['description'] ?? ($jsonData['description'] ?? '')));
$status          = trim($_POST['status'] ?? ($_REQUEST['status'] ?? ($jsonData['status'] ?? '1')));
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

// Generate URL slug
if (empty($slug)) {
    $cleanSlug = strtolower(preg_replace('~[^\p{M}\w-]+~u', '-', $title));
    $cleanSlug = trim(preg_replace('/-+/', '-', $cleanSlug), '-');
} else {
    $cleanSlug = strtolower(preg_replace('~[^\p{M}\w-]+~u', '-', $slug));
    $cleanSlug = trim(preg_replace('/-+/', '-', $cleanSlug), '-');
}
$url = '/' . $cleanSlug . '/';

// Check if slug is already taken for this site
$escUrl = mysqli_real_escape_string($connection, $url);
$checkUrlQuery = mysqli_query($connection, "SELECT link_relation_id FROM {$tblPrefix}link WHERE link_url = '{$escUrl}' AND link_site_id = {$siteId} LIMIT 1");
if ($checkUrlQuery && mysqli_num_rows($checkUrlQuery) > 0) {
    // Append random number to ensure unique slug
    $url = '/' . $cleanSlug . '-' . rand(100, 999) . '/';
    $escUrl = mysqli_real_escape_string($connection, $url);
}

// Image Upload Handling via imageUploader.php
$featureImage = '';
$thumbnailImage = '';

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
$escMetaTitle    = mysqli_real_escape_string($connection, !empty($metaTitle) ? $metaTitle : $title);
$escMetaDesc     = mysqli_real_escape_string($connection, !empty($metaDescription) ? $metaDescription : mb_substr(strip_tags($description), 0, 160));
$escMetaKeys     = mysqli_real_escape_string($connection, $metaKeywords);

mysqli_begin_transaction($connection);

try {
    // 1. Insert into isw_page
    $pageQuery = "
        INSERT INTO {$tblPrefix}page (
            page_site_id, page_image, page_thumbnail, page_title,
            page_description, page_owner_id, page_status, page_type
        ) VALUES (
            {$siteId}, '{$escImage}', '{$escThumb}', '{$escTitle}',
            '{$escDesc}', {$currentUserId}, '{$escStatus}', 'policy'
        )
    ";

    if (!mysqli_query($connection, $pageQuery)) {
        throw new Exception("Error saving policy page: " . mysqli_error($connection));
    }
    $pageId = mysqli_insert_id($connection);

    // 2. Insert into isw_link
    $linkQuery = "
        INSERT INTO {$tblPrefix}link (
            link_url, link_relation_id, link_status, link_type,
            link_owner_id, link_site_id, link_creation, link_published
        ) VALUES (
            '{$escUrl}', {$pageId}, 200, 'page',
            {$currentUserId}, {$siteId}, NOW(), NOW()
        )
    ";

    if (!mysqli_query($connection, $linkQuery)) {
        throw new Exception("Error creating policy link: " . mysqli_error($connection));
    }
    $linkId = mysqli_insert_id($connection);

    // 3. Insert into isw_meta
    $metaQuery = "
        INSERT INTO {$tblPrefix}meta (
            meta_relation_id, meta_site_id, meta_title, meta_description,
            meta_keyword, meta_type
        ) VALUES (
            {$linkId}, {$siteId}, '{$escMetaTitle}', '{$escMetaDesc}',
            '{$escMetaKeys}', 'page'
        )
    ";
    mysqli_query($connection, $metaQuery);

    mysqli_commit($connection);

    echo json_encode([
        'status' => 'success',
        'msg' => 'Policy created and published successfully!',
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
