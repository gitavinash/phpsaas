<?php

header("Content-Type: application/json; charset=utf-8");

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
$userId    = defined('USER_ID')      ? (int) USER_ID : 0;

$packageIdRaw = $_POST['packageId'] ?? ($_POST['id'] ?? 0);
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
        'msg'     => 'Invalid package ID provided.',
        'message' => 'Invalid package ID provided.'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

// Step 1: Fetch tour package and its gallery info
$query = "
    SELECT g.gallery_image_list, g.gallery_id, tp.tour_package_title
    FROM {$tblPrefix}tour_package AS tp
    LEFT JOIN {$tblPrefix}gallery AS g
        ON g.gallery_category = tp.tour_package_id
        AND g.gallery_site_id = {$siteId}
        AND g.gallery_type = 'tour_package'
        AND g.gallery_status = 1
    WHERE tp.tour_package_id = '{$packageId}'
    AND tp.tour_package_site_id = {$siteId}
    LIMIT 1
";

$res = mysqli_query($connection, $query);
if (!$res || mysqli_num_rows($res) === 0) {
    http_response_code(404);
    echo json_encode([
        'status'  => 'error',
        'msg'     => 'Tour package not found.',
        'message' => 'Tour package not found.'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

$row = mysqli_fetch_assoc($res);
$existingGallery = json_decode($row['gallery_image_list'] ?? '[]', true) ?? [];
$packageTitle = $row['tour_package_title'] ?? 'Tour Package Gallery';

// Step 2: Handle uploaded files
if (isset($_FILES['gallery'])) {
    if (defined('MODULES') && file_exists(MODULES . 'galleryUploader.php')) {
        require_once MODULES . 'galleryUploader.php'; // populates $images
    }
    $newGallery = $images ?? [];

    if (empty($newGallery)) {
        http_response_code(400);
        echo json_encode([
            'status'  => 'error',
            'msg'     => 'Failed to process uploaded images.',
            'message' => 'Failed to process uploaded images.'
        ], JSON_UNESCAPED_SLASHES);
        exit;
    }

    // Step 3: Merge old and new images
    $mergedGallery = array_merge($existingGallery, $newGallery);
    $mergedGalleryJson = mysqli_real_escape_string($connection, json_encode($mergedGallery, JSON_UNESCAPED_SLASHES));

    // Step 4: Update or Insert based on gallery existence
    if (!empty($row['gallery_id'])) {
        $gId = (int)$row['gallery_id'];
        $updateQuery = "
            UPDATE {$tblPrefix}gallery
            SET gallery_image_list = '{$mergedGalleryJson}'
            WHERE gallery_id = '{$gId}'
            AND gallery_site_id = {$siteId}
        ";
        $execute = mysqli_query($connection, $updateQuery);
    } else {
        $firstPath = mysqli_real_escape_string($connection, $newGallery[0]['path'] ?? '');
        $firstName = mysqli_real_escape_string($connection, $newGallery[0]['name'] ?? $packageTitle);
        $insertQuery = "
            INSERT INTO {$tblPrefix}gallery
            (gallery_image, gallery_thumbnail, gallery_title, gallery_category, gallery_site_id, gallery_type, gallery_image_list, gallery_owner_id, gallery_status)
            VALUES
            ('{$firstPath}', '{$firstPath}', '{$firstName}', '{$packageId}', {$siteId}, 'tour_package', '{$mergedGalleryJson}', '{$userId}', 1)
        ";
        $execute = mysqli_query($connection, $insertQuery);
    }

    if ($execute) {
        http_response_code(200);
        echo json_encode([
            'status'  => 'success',
            'msg'     => 'Images have been successfully saved to the gallery.',
            'message' => 'Images have been successfully saved to the gallery.',
            'count'   => count($newGallery)
        ], JSON_UNESCAPED_SLASHES);
    } else {
        http_response_code(500);
        echo json_encode([
            'status'  => 'error',
            'msg'     => 'Database error while storing the gallery: ' . mysqli_error($connection),
            'message' => 'Database error while storing the gallery: ' . mysqli_error($connection)
        ], JSON_UNESCAPED_SLASHES);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'msg'     => 'No gallery images uploaded.',
        'message' => 'No gallery images uploaded.'
    ], JSON_UNESCAPED_SLASHES);
}
