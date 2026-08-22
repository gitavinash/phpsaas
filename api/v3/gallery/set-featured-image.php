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

$httpStatus = 400;
$status = 'error';
$msg = 'Invalid request';

// Only handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $galleryIdRaw = $_POST['galleryId'] ?? 0;
    $galleryId = (int)$galleryIdRaw;

    $categoryIdRaw = $_POST['categoryId'] ?? ($_POST['packageId'] ?? 0);
    $categoryId = is_numeric($categoryIdRaw) ? (int)$categoryIdRaw : (int)base64_decode($categoryIdRaw);

    $featureImage = isset($_POST['featureImage']) ? trim($_POST['featureImage']) : '';
    $action = isset($_POST['action']) ? trim($_POST['action']) : '';

    $isRemove = ($action === 'remove' || $featureImage === '');

    $whereClause = "";
    if ($galleryId > 0) {
        $whereClause = "gallery_id = '{$galleryId}' AND gallery_site_id = {$siteId}";
    } else {
        $whereClause = "gallery_category = '{$categoryId}' AND gallery_type = 'tour_package' AND gallery_site_id = {$siteId}";
    }

    if ($checkGallery = mysqli_query($connection, "SELECT gallery_id FROM {$tblPrefix}gallery WHERE " . $whereClause . " LIMIT 1")) {
        if (mysqli_num_rows($checkGallery) > 0) {
            $row = mysqli_fetch_assoc($checkGallery);
            $gId = (int)$row['gallery_id'];

            if ($isRemove) {
                $updateQuery = "UPDATE {$tblPrefix}gallery SET gallery_image = '', gallery_thumbnail = '' WHERE gallery_id = '{$gId}' AND gallery_site_id = {$siteId}";
                if ($categoryId > 0) {
                    mysqli_query($connection, "UPDATE {$tblPrefix}tour_package SET tour_package_image = '' WHERE tour_package_id = '{$categoryId}' AND tour_package_site_id = {$siteId}");
                }
                $successMsg = 'Removed from featured image successfully';
            } else {
                $escapedImage = mysqli_real_escape_string($connection, $featureImage);
                $updateQuery = "UPDATE {$tblPrefix}gallery SET gallery_image = '{$escapedImage}', gallery_thumbnail = '{$escapedImage}' WHERE gallery_id = '{$gId}' AND gallery_site_id = {$siteId}";
                if ($categoryId > 0) {
                    mysqli_query($connection, "UPDATE {$tblPrefix}tour_package SET tour_package_image = '{$escapedImage}' WHERE tour_package_id = '{$categoryId}' AND tour_package_site_id = {$siteId}");
                }
                $successMsg = 'Featured image set successfully';
            }

            if (mysqli_query($connection, $updateQuery)) {
                $httpStatus = 200;
                $status = 'success';
                $msg = $successMsg;
            } else {
                $httpStatus = 500;
                $status = 'error';
                $msg = 'Database update failed: ' . mysqli_error($connection);
            }
        } else {
            $httpStatus = 404;
            $msg = 'Gallery record not found';
        }
    } else {
        $msg = 'Invalid gallery query';
    }
}

http_response_code($httpStatus);
echo json_encode([
    'status'  => $status,
    'message' => $msg,
    'msg'     => $msg
], JSON_UNESCAPED_SLASHES);
