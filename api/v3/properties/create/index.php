<?php
/**
 * V3 Property Create API
 * Path: /api/v3/properties/create/index.php
 * POST /api/v3/properties/create/
 * Accepts multipart/form-data (supports thumbnail upload & multiple gallery uploads) or JSON.
 * Creates a new property record and generates the initial website link.
 */

header('Content-Type: application/json; charset=utf-8');

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 4);
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
$type          = strtolower(str_replace(' ', '_', trim($data['type']     ?? '3_star_hotel')));
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
if (empty($email))   $errors[] = 'Reservation email is required.';
if (empty($mobile))  $errors[] = 'Contact number is required.';
if (empty($type))    $errors[] = 'Property type is required.';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => implode(' ', $errors), 'errors' => $errors]);
    exit();
}

$esc = fn($v) => mysqli_real_escape_string($connection, (string)$v);

// Insert Base Property Record
$insertSql = "INSERT INTO {$prefix}property (
    property_site_id, property_title, property_description, property_address,
    property_city, property_state, property_country, property_pincode,
    property_email, property_mobile, property_support_email, property_support_mobile,
    property_type, property_map_url, property_thumbnail, property_gallery,
    property_amenities, property_status, property_created_by
) VALUES (
    {$siteId}, '{$esc($title)}', '{$esc($description)}', '{$esc($address)}',
    '{$esc($propertyCity)}', '{$esc($propertyState)}', '{$esc($propertyCountry)}', '{$esc($pincode)}',
    '{$esc($email)}', '{$esc($mobile)}', '{$esc($supportEmail)}', '{$esc($supportMobile)}',
    '{$esc($type)}', '{$esc($mapUrl)}', '', '[]',
    '[]', {$status}, {$userId}
)";

if (!mysqli_query($connection, $insertSql)) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Failed to create property in database.',
        'debug'  => mysqli_error($connection),
    ]);
    exit();
}

$propertyId = mysqli_insert_id($connection);

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
// Thumbnail Upload
// -----------------------------------------------------------------------
$newThumbnail = '';
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
// Multi-photo Gallery Upload
// -----------------------------------------------------------------------
$newGallery = [];
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

// Update property with uploaded media
if (!empty($newThumbnail) || !empty($newGallery)) {
    $mediaUpdateSql = "UPDATE {$prefix}property
                       SET property_thumbnail = '{$esc($newThumbnail)}',
                           property_gallery = '{$esc($newGalleryJson)}'
                       WHERE property_id = {$propertyId}";
    mysqli_query($connection, $mediaUpdateSql);
}

// -----------------------------------------------------------------------
// Synchronize Website Link (Slug / URL)
// -----------------------------------------------------------------------
function makePropertySlug($str) {
    $clean = preg_replace('~[^\p{M}\w-]+~u', '-', strtolower(trim($str)));
    $clean = preg_replace('~-+~', '-', $clean);
    return trim($clean, '-');
}

$publishToWebsite = isset($data['publishToWebsite']) ? (int)$data['publishToWebsite'] : 1;
$linkStatus       = ($status === 1 && $publishToWebsite === 1) ? 200 : 0;
$desiredSlug      = makePropertySlug($title);
if (empty($desiredSlug)) $desiredSlug = 'property-' . $propertyId;

// Check if slug is unique, append ID if taken
$slugCheck = mysqli_query($connection, "SELECT link_id FROM {$prefix}link WHERE link_url = '{$esc($desiredSlug)}' AND link_site_id = {$siteId} LIMIT 1");
if ($slugCheck && mysqli_num_rows($slugCheck) > 0) {
    $desiredSlug .= '-' . $propertyId;
}

$linkInsertSql = "INSERT INTO {$prefix}link (
    link_url, link_relation_id, link_status, link_type,
    link_owner_id, link_site_id, link_is_featured,
    link_creation, link_published, link_modified
) VALUES (
    '{$esc($desiredSlug)}', {$propertyId}, {$linkStatus}, 'property',
    {$userId}, {$siteId}, 0,
    NOW(), NOW(), NOW()
)";
mysqli_query($connection, $linkInsertSql);

http_response_code(201);
echo json_encode([
    'status'      => 'success',
    'msg'         => 'Property created successfully!',
    'propertyId'  => $propertyId,
    'encodedId'   => base64_encode((string)$propertyId),
    'property_id' => $propertyId,
    'slug'        => $desiredSlug,
]);
exit();
