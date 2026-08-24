<?php
/**
 * V3 Tour Package Publish to Website API
 * Endpoint: /api/v3/packages/publish-website/
 * Method: POST
 */

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

if (!defined('LOGIN_CHECK')) {
    define('LOGIN_CHECK', $_SERVER['DOCUMENT_ROOT'] . '/lib/session/index.php');
}
if (file_exists(LOGIN_CHECK)) {
    require_once LOGIN_CHECK;
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : 0;
$userId    = defined('USER_ID')      ? (int) USER_ID : 0;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Method Not Allowed. Use POST.'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);
if (!is_array($data) || empty($data)) {
    $data = !empty($_POST) ? $_POST : $_GET;
}

$packageIdRaw = $data['packageId'] ?? ($data['id'] ?? ($data['tourPackageId'] ?? 0));
if (is_numeric($packageIdRaw) && (int)$packageIdRaw > 0) {
    $packageId = (int)$packageIdRaw;
} else {
    $decoded = base64_decode((string)$packageIdRaw, true);
    $packageId = ($decoded !== false && is_numeric($decoded)) ? (int)$decoded : 0;
}

if ($packageId <= 0) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Invalid or missing tour package ID.'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

$action = isset($data['action']) ? trim($data['action']) : 'toggle';

// 1. Fetch current package data
$pkgQuery = "
    SELECT 
        tp.tour_package_id,
        tp.tour_package_title,
        tp.tour_package_status,
        l.link_id,
        l.link_url,
        l.link_status
    FROM {$tblPrefix}tour_package AS tp
    LEFT JOIN {$tblPrefix}link AS l
        ON l.link_relation_id = tp.tour_package_id
        AND l.link_type = 'tour_package'
        AND l.link_site_id = {$siteId}
    WHERE tp.tour_package_id = '{$packageId}'
      AND tp.tour_package_site_id = {$siteId}
    LIMIT 1
";

$res = mysqli_query($connection, $pkgQuery);
if (!$res || mysqli_num_rows($res) === 0) {
    http_response_code(404);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Tour package not found or access denied.'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

$pkgRow = mysqli_fetch_assoc($res);
$currentStatus = (int)($pkgRow['tour_package_status'] ?? 0);
$linkId = !empty($pkgRow['link_id']) ? (int)$pkgRow['link_id'] : 0;
$existingLinkUrl = $pkgRow['link_url'] ?? '';
$linkStatus = (int)($pkgRow['link_status'] ?? 0);

$isCurrentlyPublished = (!empty($existingLinkUrl) && $linkStatus === 200 && $currentStatus === 1);

// Determine desired state
if ($action === 'publish') {
    $shouldPublish = true;
} elseif ($action === 'unpublish') {
    $shouldPublish = false;
} else {
    // Toggle
    $shouldPublish = !$isCurrentlyPublished;
}

function convertToSlug($string) {
    $string = preg_replace('/[^A-Za-z0-9\s]/', '_', $string);
    $string = preg_replace('/\s+/', '_', $string);
    return strtolower(trim($string, '_'));
}

mysqli_query($connection, "START TRANSACTION");

if ($shouldPublish) {
    if (empty($existingLinkUrl)) {
        $slug = convertToSlug($pkgRow['tour_package_title'] ?? 'package');
        if (empty($slug)) {
            $slug = 'tour_package';
        }
        $finalUrl = '/' . $slug . '-' . time() . '/';

        $insertLink = "
            INSERT INTO {$tblPrefix}link (
                link_url, 
                link_relation_id, 
                link_status, 
                link_type, 
                link_owner_id, 
                link_site_id, 
                link_creation, 
                link_published
            ) VALUES (
                '{$finalUrl}', 
                '{$packageId}', 
                200, 
                'tour_package', 
                {$userId}, 
                {$siteId}, 
                NOW(), 
                NOW()
            )
        ";
        $linkSuccess = mysqli_query($connection, $insertLink);
        $newLinkId = mysqli_insert_id($connection);

        $insertMeta = "
            INSERT INTO {$tblPrefix}meta (
                meta_relation_id, 
                meta_site_id, 
                meta_type, 
                meta_robots
            ) VALUES (
                '{$newLinkId}', 
                {$siteId}, 
                'tour_package', 
                'index, follow'
            )
        ";
        mysqli_query($connection, $insertMeta);
        $linkUrl = $finalUrl;
    } else {
        $updateLink = "
            UPDATE {$tblPrefix}link
            SET link_status = 200,
                link_published = NOW(),
                link_modified = NOW()
            WHERE link_id = {$linkId}
              AND link_site_id = {$siteId}
            LIMIT 1
        ";
        $linkSuccess = mysqli_query($connection, $updateLink);
        $linkUrl = $existingLinkUrl;
    }

    $updatePkg = "
        UPDATE {$tblPrefix}tour_package
        SET tour_package_status = 1
        WHERE tour_package_id = '{$packageId}'
          AND tour_package_site_id = {$siteId}
        LIMIT 1
    ";
    $pkgSuccess = mysqli_query($connection, $updatePkg);

    if ($linkSuccess && $pkgSuccess) {
        mysqli_query($connection, "COMMIT");
        echo json_encode([
            'status'       => 'success',
            'is_published' => true,
            'link_url'     => $linkUrl,
            'message'      => 'Tour package is now live on your website!'
        ], JSON_UNESCAPED_SLASHES);
    } else {
        mysqli_query($connection, "ROLLBACK");
        http_response_code(500);
        echo json_encode([
            'status'  => 'error',
            'message' => 'Failed to publish tour package. ' . mysqli_error($connection)
        ], JSON_UNESCAPED_SLASHES);
    }
} else {
    $updateLink = "
        UPDATE {$tblPrefix}link
        SET link_status = 404,
            link_modified = NOW()
        WHERE link_relation_id = '{$packageId}'
          AND link_type = 'tour_package'
          AND link_site_id = {$siteId}
    ";
    $linkSuccess = mysqli_query($connection, $updateLink);

    $updatePkg = "
        UPDATE {$tblPrefix}tour_package
        SET tour_package_status = 0
        WHERE tour_package_id = '{$packageId}'
          AND tour_package_site_id = {$siteId}
        LIMIT 1
    ";
    $pkgSuccess = mysqli_query($connection, $updatePkg);

    if ($linkSuccess && $pkgSuccess) {
        mysqli_query($connection, "COMMIT");
        echo json_encode([
            'status'       => 'success',
            'is_published' => false,
            'link_url'     => $existingLinkUrl,
            'message'      => 'Tour package is now unpublished and hidden from website.'
        ], JSON_UNESCAPED_SLASHES);
    } else {
        mysqli_query($connection, "ROLLBACK");
        http_response_code(500);
        echo json_encode([
            'status'  => 'error',
            'message' => 'Failed to unpublish tour package. ' . mysqli_error($connection)
        ], JSON_UNESCAPED_SLASHES);
    }
}
