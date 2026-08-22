<?php

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

if (defined('LOGIN_CHECK')) {
    require_once LOGIN_CHECK;
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : 0;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Method not allowed. Only POST is accepted.'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

$packageIdRaw = $_POST['packageId'] ?? ($_POST['id'] ?? 0);
if (is_numeric($packageIdRaw) && (int)$packageIdRaw > 0) {
    $packageId = (int)$packageIdRaw;
} else {
    $decoded = base64_decode($packageIdRaw, true);
    $packageId = ($decoded !== false && is_numeric($decoded)) ? (int)$decoded : 0;
}

$galleryId = isset($_POST['galleryId']) ? (int)$_POST['galleryId'] : 0;

// Collect images to delete (single or multiple)
$imagesToDelete = [];
if (isset($_POST['images']) && is_array($_POST['images'])) {
    foreach ($_POST['images'] as $img) {
        $clean = trim((string)$img);
        if ($clean !== '') {
            $imagesToDelete[] = $clean;
        }
    }
} elseif (isset($_POST['imagePath']) && trim($_POST['imagePath']) !== '') {
    $imagesToDelete[] = trim($_POST['imagePath']);
} elseif (isset($_POST['images']) && is_string($_POST['images'])) {
    $exploded = explode(',', $_POST['images']);
    foreach ($exploded as $img) {
        $clean = trim($img);
        if ($clean !== '') {
            $imagesToDelete[] = $clean;
        }
    }
}

if (($packageId <= 0 && $galleryId <= 0) || empty($imagesToDelete)) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Missing package ID or image paths to delete.'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

$whereClause = "";
if ($galleryId > 0) {
    $whereClause = "gallery_id = '{$galleryId}' AND gallery_site_id = {$siteId}";
} else {
    $whereClause = "gallery_category = '{$packageId}' AND gallery_type = 'tour_package' AND gallery_site_id = {$siteId}";
}

$query = "SELECT gallery_id, gallery_image, gallery_thumbnail, gallery_image_list FROM {$tblPrefix}gallery WHERE {$whereClause} LIMIT 1";
$res = mysqli_query($connection, $query);

if (!$res || mysqli_num_rows($res) === 0) {
    http_response_code(404);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Gallery record not found.'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

$row = mysqli_fetch_assoc($res);
$gId = (int)$row['gallery_id'];
$currentList = json_decode($row['gallery_image_list'] ?? '[]', true) ?? [];
$featuredImg = $row['gallery_image'] ?? '';
$thumbImg    = $row['gallery_thumbnail'] ?? '';

$deletedCount = 0;
$newList = [];

// Filter out deleted images
foreach ($currentList as $item) {
    $path = $item['path'] ?? '';
    if (in_array($path, $imagesToDelete, true)) {
        $deletedCount++;
        // Remove file from disk if under uploads directory
        if (!empty($path) && strpos($path, '/uploads/') !== false) {
            $fullPath = $_SERVER['DOCUMENT_ROOT'] . $path;
            if (file_exists($fullPath) && is_file($fullPath)) {
                @unlink($fullPath);
            }
        }
    } else {
        $newList[] = $item;
    }
}

// Re-check featured image
$newFeatured = $featuredImg;
$newThumb    = $thumbImg;
if (in_array($featuredImg, $imagesToDelete, true)) {
    if (!empty($newList)) {
        $newFeatured = $newList[0]['path'] ?? '';
        $newThumb    = $newList[0]['path'] ?? '';
    } else {
        $newFeatured = '';
        $newThumb    = '';
    }
}

$jsonEncoded = mysqli_real_escape_string($connection, json_encode(array_values($newList), JSON_UNESCAPED_SLASHES));
$escapedFeatured = mysqli_real_escape_string($connection, $newFeatured);
$escapedThumb    = mysqli_real_escape_string($connection, $newThumb);

$updateQuery = "
    UPDATE {$tblPrefix}gallery
    SET gallery_image_list = '{$jsonEncoded}',
        gallery_image = '{$escapedFeatured}',
        gallery_thumbnail = '{$escapedThumb}'
    WHERE gallery_id = '{$gId}' AND gallery_site_id = {$siteId}
";

if (mysqli_query($connection, $updateQuery)) {
    $word = $deletedCount === 1 ? 'image' : 'images';
    echo json_encode([
        'status'       => 'success',
        'deletedCount' => $deletedCount,
        'message'      => "{$deletedCount} {$word} deleted successfully."
    ], JSON_UNESCAPED_SLASHES);
} else {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Database error while deleting images: ' . mysqli_error($connection)
    ], JSON_UNESCAPED_SLASHES);
}
