<?php
/**
 * V3 Property Update API
 * Path: /api/v3/properties/update.php
 * POST /api/v3/properties/update/
 * Accepts multipart/form-data (supports thumbnail upload & multiple gallery uploads) or JSON.
 * Synchronizes property_status and publishes/unpublishes to the website link table.
 */

header('Content-Type: application/json; charset=utf-8');

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php'))                  require_once $docRoot . '/config.php';
    if (file_exists($docRoot . '/lib/modules/connection.php')) require_once $docRoot . '/lib/modules/connection.php';
}

if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'msg' => 'Method not allowed. Use POST.']);
    exit();
}

/** @var mysqli $connection */
if (!isset($connection) || !($connection instanceof mysqli)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Database connection unavailable.']);
    exit();
}

$prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId = defined('SITE_ID') ? (int) SITE_ID : (int) ($_SESSION['site_id'] ?? 0);
$userId = defined('USER_ID') ? (int) USER_ID : (int) ($_SESSION['user_id'] ?? 1);
if ($siteId <= 0 && !empty($_SERVER['HTTP_HOST'])) {
    $host = mysqli_real_escape_string($connection, strtolower(trim($_SERVER['HTTP_HOST'])));
    $siteQuery = mysqli_query($connection, "SELECT site_id FROM {$prefix}site WHERE LOWER(site_domain) = '{$host}' OR LOWER(site_url) = '{$host}' LIMIT 1");
    if ($siteQuery && ($siteRow = mysqli_fetch_assoc($siteQuery))) {
        $siteId = (int) $siteRow['site_id'];
    }
}

// Support both multipart POST and JSON body
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (strpos($contentType, 'application/json') !== false) {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    $data = $body;
} else {
    $data = $_POST;
}

// Resolve property ID
$rawId      = $data['property_id'] ?? ($data['id'] ?? ($data['propertyId'] ?? ''));
$propertyId = 0;
if (is_numeric($rawId) && (int)$rawId > 0) {
    $propertyId = (int)$rawId;
} elseif (!empty($rawId)) {
    $decoded = base64_decode($rawId, true);
    if ($decoded !== false && is_numeric($decoded) && (int)$decoded > 0) {
        $propertyId = (int)$decoded;
    }
}

if ($propertyId <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Valid property ID is required.']);
    exit();
}

// Read fields
$title         = trim($data['name']           ?? ($data['title']         ?? ''));
$description   = trim($data['description']    ?? '');
$address       = trim($data['address']        ?? '');
$city          = trim($data['city']           ?? '');
$pincode       = trim($data['pinCode']        ?? ($data['pincode']       ?? ''));
$email         = trim($data['email']          ?? '');
$mobile        = trim($data['mobile']         ?? '');
$supportEmail  = trim($data['supportEmail']   ?? ($data['support_email'] ?? ''));
$supportMobile = trim($data['supportMobile']  ?? ($data['support_mobile']?? ''));
$type          = strtolower(str_replace(' ', '_', trim($data['type']     ?? '')));
$mapUrl        = trim($data['mapUrl']         ?? ($data['map_url']       ?? ''));
$status        = isset($data['status']) ? (int)$data['status'] : 1;

// Parse city → state, country from "City, State, Country" format
$cityParts       = array_map('trim', explode(',', $city));
$propertyCity    = $city;
$propertyState   = count($cityParts) >= 2 ? $cityParts[count($cityParts) - 2] : '';
$propertyCountry = count($cityParts) >= 1 ? end($cityParts)                    : '';

// Validation
$errors = [];
if (empty($title))   $errors[] = 'Property name is required.';
if (empty($address)) $errors[] = 'Address is required.';
if (empty($city))    $errors[] = 'City is required.';
if (empty($pincode)) $errors[] = 'Pin code is required.';
if (empty($email))   $errors[] = 'Email is required.';
if (empty($mobile))  $errors[] = 'Contact number is required.';
if (empty($type))    $errors[] = 'Property type is required.';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => implode(' ', $errors), 'errors' => $errors]);
    exit();
}

// Verify property exists
$siteCondition = $siteId > 0 ? "AND property_site_id = {$siteId}" : "";
$checkRes = mysqli_query($connection,
    "SELECT property_thumbnail, property_gallery FROM {$prefix}property
     WHERE property_id = {$propertyId} {$siteCondition}
     LIMIT 1"
);

if (!$checkRes || mysqli_num_rows($checkRes) === 0) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'msg' => 'Property not found or access denied.']);
    exit();
}

$existing     = mysqli_fetch_assoc($checkRes);
$oldThumbnail = $existing['property_thumbnail'] ?? '';
$oldGalleryJs = $existing['property_gallery'] ?? '';

// -----------------------------------------------------------------------
// Upload Paths matching imageUploader.php (ROOT/upload/SITE_DOMAIN/Y/m/)
// -----------------------------------------------------------------------
$rootPath = defined('ROOT') ? ROOT : (!empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3));
$siteDomain = defined('SITE_DOMAIN') ? SITE_DOMAIN : '';
if (empty($siteDomain) && !empty($siteId)) {
    $sq = mysqli_query($connection, "SELECT site_domain FROM {$prefix}site WHERE site_id = {$siteId} LIMIT 1");
    if ($sq && ($sr = mysqli_fetch_assoc($sq)) && !empty($sr['site_domain'])) {
        $siteDomain = $sr['site_domain'];
    }
}
if (empty($siteDomain) && !empty($_SERVER['HTTP_HOST'])) {
    $siteDomain = preg_replace('/:[0-9]+$/', '', $_SERVER['HTTP_HOST']);
}
if (empty($siteDomain)) {
    $siteDomain = 'default';
}

$uploadPath = rtrim($rootPath, '/') . '/upload/' . $siteDomain . '/' . date('Y/m/');
$originalUploadDir = $uploadPath . 'original/';
$thumbnailUploadDir = $uploadPath . 'thumbnail/';
if (!is_dir($uploadPath)) @mkdir($uploadPath, 0755, true);
if (!is_dir($originalUploadDir)) @mkdir($originalUploadDir, 0755, true);
if (!is_dir($thumbnailUploadDir)) @mkdir($thumbnailUploadDir, 0755, true);

$validExtensions = ['png', 'gif', 'jpg', 'jpeg', 'webp'];

// -----------------------------------------------------------------------
// Thumbnail upload (optional)
// -----------------------------------------------------------------------
$newThumbnail = $oldThumbnail; // keep existing by default

if (!empty($_FILES['featureImage']['tmp_name']) && $_FILES['featureImage']['error'] === UPLOAD_ERR_OK) {
    $rawFilename = strtolower(str_replace("'", "_", str_replace('-', '_', str_replace(' ', '_', $_FILES['featureImage']['name']))));
    $ext = strtolower(pathinfo($rawFilename, PATHINFO_EXTENSION));
    if (in_array($ext, $validExtensions, true)) {
        $uniquePrefix = time() . '_' . bin2hex(random_bytes(5));
        $cleanName = strtolower(preg_replace('~[^\p{L}\p{N}\._-]+~u', '', $rawFilename));
        $destFile = $uploadPath . $uniquePrefix . '_' . $cleanName;
        $origFile = $originalUploadDir . $uniquePrefix . '_' . $cleanName;
        if (move_uploaded_file($_FILES['featureImage']['tmp_name'], $destFile)) {
            @copy($destFile, $origFile);
            $newThumbnail = str_replace($rootPath, '', $destFile);
        }
    }
}

// -----------------------------------------------------------------------
// Gallery: merge kept old images + any new uploads
// -----------------------------------------------------------------------
$oldGalleryArr = [];
if (!empty($oldGalleryJs)) {
    $parsed = json_decode($oldGalleryJs, true);
    if (is_array($parsed)) $oldGalleryArr = $parsed;
}

$newGallery = [];

if (isset($_POST['kept_gallery'])) {
    $kept = $_POST['kept_gallery'];
    if (is_string($kept)) {
        $kept = json_decode($kept, true) ?? [];
    }
    if (is_array($kept)) {
        foreach ($kept as $item) {
            if (is_array($item) && !empty($item['path'])) {
                $newGallery[] = [
                    'name' => $item['name'] ?? basename($item['path']),
                    'path' => $item['path'],
                    'type' => $item['type'] ?? pathinfo($item['path'], PATHINFO_EXTENSION),
                ];
            } elseif (is_string($item) && !empty($item)) {
                $newGallery[] = [
                    'name' => basename($item),
                    'path' => $item,
                    'type' => pathinfo($item, PATHINFO_EXTENSION),
                ];
            }
        }
    }
} elseif (!empty($_POST['oldGallery']) && is_array($_POST['oldGallery'])) {
    foreach ($_POST['oldGallery'] as $idx) {
        $j = (int)$idx - 1;
        if (isset($oldGalleryArr[$j])) $newGallery[] = $oldGalleryArr[$j];
    }
} else {
    $newGallery = $oldGalleryArr;
}

// New gallery uploads (if provided via $_FILES['gallery'])
if (!empty($_FILES['gallery']) && !empty($_FILES['gallery']['name'])) {
    $files = $_FILES['gallery'];
    $count = is_array($files['name']) ? count($files['name']) : 1;
    for ($i = 0; $i < $count; $i++) {
        $tmpName  = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
        $origName = is_array($files['name'])     ? $files['name'][$i]     : $files['name'];
        $err      = is_array($files['error'])    ? $files['error'][$i]    : $files['error'];
        if ($err !== UPLOAD_ERR_OK || empty($tmpName)) continue;

        $rawFilename = strtolower(str_replace("'", "_", str_replace('-', '_', str_replace(' ', '_', $origName))));
        $ext = strtolower(pathinfo($rawFilename, PATHINFO_EXTENSION));
        if (!in_array($ext, $validExtensions, true)) continue;

        $uniquePrefix = time() . '_' . bin2hex(random_bytes(5));
        $cleanName = strtolower(preg_replace('~[^\p{L}\p{N}\._-]+~u', '', $rawFilename));
        $destFile = $uploadPath . $uniquePrefix . '_' . $cleanName;
        $origFile = $originalUploadDir . $uniquePrefix . '_' . $cleanName;

        if (move_uploaded_file($tmpName, $destFile)) {
            @copy($destFile, $origFile);
            $webPath = str_replace($rootPath, '', $destFile);
            $newGallery[] = ['name' => $origName, 'path' => $webPath, 'type' => $ext];
        }
    }
}

$newGalleryJson = json_encode($newGallery);

// Parse amenities if provided
$newAmenitiesJson = null;
if (isset($data['property_amenities']) || isset($data['amenities'])) {
    $rawAmen = $data['property_amenities'] ?? $data['amenities'];
    if (is_string($rawAmen)) {
        $rawAmen = json_decode($rawAmen, true) ?? [];
    }
    if (is_array($rawAmen)) {
        $newAmenitiesJson = mysqli_real_escape_string($connection, json_encode($rawAmen));
    }
}

// -----------------------------------------------------------------------
// Sanitize & Update Property
// -----------------------------------------------------------------------
$esc = fn($v) => mysqli_real_escape_string($connection, (string)$v);

$amenitiesUpdateClause = $newAmenitiesJson !== null ? ", property_amenities = '{$newAmenitiesJson}'" : "";

$updateSql = "UPDATE {$prefix}property SET
    property_title          = '{$esc($title)}',
    property_description    = '{$esc($description)}',
    property_address        = '{$esc($address)}',
    property_city           = '{$esc($propertyCity)}',
    property_state          = '{$esc($propertyState)}',
    property_country        = '{$esc($propertyCountry)}',
    property_pincode        = '{$esc($pincode)}',
    property_email          = '{$esc($email)}',
    property_mobile         = '{$esc($mobile)}',
    property_support_email  = '{$esc($supportEmail)}',
    property_support_mobile = '{$esc($supportMobile)}',
    property_type           = '{$esc($type)}',
    property_map_url        = '{$esc($mapUrl)}',
    property_thumbnail      = '{$esc($newThumbnail)}',
    property_gallery        = '{$esc($newGalleryJson)}',
    property_status         = {$status}
    {$amenitiesUpdateClause}
WHERE property_id = {$propertyId}
  {$siteCondition}";

if (!mysqli_query($connection, $updateSql)) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Failed to update property. Please try again.',
        'debug'  => mysqli_error($connection),
    ]);
    exit();
}

// -----------------------------------------------------------------------
// Synchronize Website Link (Publishing on Website)
// -----------------------------------------------------------------------
function makePropertySlug($str) {
    $clean = preg_replace('~[^\p{M}\w-]+~u', '-', strtolower(trim($str)));
    $clean = preg_replace('~-+~', '-', $clean);
    return trim($clean, '-');
}

$publishToWebsite = isset($data['publishToWebsite']) ? (int)$data['publishToWebsite'] : 0;

$linkCheck = mysqli_query($connection,
    "SELECT link_id, link_url FROM {$prefix}link
     WHERE link_relation_id = {$propertyId} AND link_type = 'property'
     LIMIT 1"
);

$liveUrl = '';
$linkUrl = '';
$siteUrl = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';

if ($publishToWebsite === 1) {
    // Publish on website
    if ($linkCheck && ($linkRow = mysqli_fetch_assoc($linkCheck))) {
        $linkId  = (int)$linkRow['link_id'];
        $linkUrl = $linkRow['link_url'];
        mysqli_query($connection, "UPDATE {$prefix}link SET link_status = 200, link_published = NOW(), link_modified = NOW() WHERE link_id = {$linkId}");
    } else {
        $slug    = makePropertySlug($title . ' ' . $propertyCity) . '-' . time();
        $linkUrl = '/' . $slug . '/';
        mysqli_query($connection,
            "INSERT INTO {$prefix}link (link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published, link_modified)
             VALUES ('{$linkUrl}', {$propertyId}, 200, 'property', {$userId}, {$siteId}, NOW(), NOW(), NOW())"
        );
    }
    $liveUrl = !empty($linkUrl) ? $siteUrl . $linkUrl : '';
} else {
    // Unpublish from website (set 404)
    if ($linkCheck && ($linkRow = mysqli_fetch_assoc($linkCheck))) {
        $linkId = (int)$linkRow['link_id'];
        mysqli_query($connection, "UPDATE {$prefix}link SET link_status = 404, link_modified = NOW() WHERE link_id = {$linkId}");
    }
}

http_response_code(200);
echo json_encode([
    'status'               => 'success',
    'msg'                  => 'Property updated successfully.',
    'property_id'          => $propertyId,
    'encoded_id'           => base64_encode($propertyId),
    'thumbnail'            => $newThumbnail,
    'status_code'          => $status,
    'link_url'             => $linkUrl,
    'live_url'             => $liveUrl,
    'is_published_website' => ($publishToWebsite === 1),
]);
exit();
