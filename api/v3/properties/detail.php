<?php
/**
 * V3 Property Detail API
 * Path: /api/v3/properties/detail.php
 * GET /api/v3/properties/detail/?id=<base64_encoded_property_id>
 * Returns a single property record including live website link status and selected amenities.
 */

header('Content-Type: application/json; charset=utf-8');

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php'))                  require_once $docRoot . '/config.php';
    if (file_exists($docRoot . '/lib/modules/connection.php')) require_once $docRoot . '/lib/modules/connection.php';
}

/** @var mysqli $connection */
if (!isset($connection) || !($connection instanceof mysqli)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Database connection unavailable.']);
    exit();
}

$prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId = defined('SITE_ID') ? (int) SITE_ID : (int) ($_SESSION['site_id'] ?? 0);
if ($siteId <= 0 && !empty($_SERVER['HTTP_HOST'])) {
    $host = mysqli_real_escape_string($connection, strtolower(trim($_SERVER['HTTP_HOST'])));
    $siteQuery = mysqli_query($connection, "SELECT site_id FROM {$prefix}site WHERE LOWER(site_domain) = '{$host}' OR LOWER(site_url) = '{$host}' LIMIT 1");
    if ($siteQuery && ($siteRow = mysqli_fetch_assoc($siteQuery))) {
        $siteId = (int) $siteRow['site_id'];
    }
}

// Resolve ID — accept encoded or raw
$rawId      = $_GET['id'] ?? ($_GET['property_id'] ?? '');
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

$siteCondition = $siteId > 0 ? "AND p.property_site_id = {$siteId}" : "";

$sql = "SELECT
            p.property_id, p.property_title, p.property_description, p.property_address,
            p.property_city, p.property_state, p.property_country, p.property_pincode,
            p.property_email, p.property_mobile, p.property_support_email, p.property_support_mobile,
            p.property_type, p.property_map_url, p.property_thumbnail, p.property_gallery,
            p.property_amenities,
            COALESCE(p.property_status, 0) AS property_status,
            l.link_id, l.link_url, COALESCE(l.link_status, 0) AS link_status
        FROM {$prefix}property p
        LEFT JOIN {$prefix}link l
          ON l.link_relation_id = p.property_id
         AND l.link_type = 'property'
        WHERE p.property_id = {$propertyId}
          {$siteCondition}
        LIMIT 1";

$res = mysqli_query($connection, $sql);

if (!$res) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Database query failed.', 'debug' => mysqli_error($connection)]);
    exit();
}

if (mysqli_num_rows($res) === 0) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'msg' => 'Property not found or access denied.']);
    exit();
}

$row = mysqli_fetch_assoc($res);

// Parse gallery JSON safely
$gallery = [];
if (!empty($row['property_gallery'])) {
    $parsed = json_decode($row['property_gallery'], true);
    if (is_array($parsed)) $gallery = $parsed;
}

// Parse amenities JSON safely
$amenities = [];
if (!empty($row['property_amenities'])) {
    $parsed = json_decode($row['property_amenities'], true);
    if (is_array($parsed)) $amenities = $parsed;
}

$linkStatus           = (int)($row['link_status'] ?? 0);
$propStatus           = (int)$row['property_status'];
$isPublishedOnWebsite = (!empty($row['link_url']) && $linkStatus === 200);
$linkUrl              = $row['link_url'] ?? '';
$siteUrl              = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
$liveUrl              = !empty($linkUrl) ? $siteUrl . $linkUrl : '';

http_response_code(200);
echo json_encode([
    'status'   => 'success',
    'property' => [
        'id'                    => (int)$row['property_id'],
        'encoded_id'            => base64_encode($row['property_id']),
        'title'                 => $row['property_title'] ?? '',
        'description'           => $row['property_description'] ?? '',
        'address'               => $row['property_address'] ?? '',
        'city'                  => $row['property_city'] ?? '',
        'state'                 => $row['property_state'] ?? '',
        'country'               => $row['property_country'] ?? '',
        'pincode'               => $row['property_pincode'] ?? '',
        'email'                 => $row['property_email'] ?? '',
        'mobile'                => $row['property_mobile'] ?? '',
        'support_email'         => $row['property_support_email'] ?? '',
        'support_mobile'        => $row['property_support_mobile'] ?? '',
        'type'                  => $row['property_type'] ?? '3_star_hotel',
        'map_url'               => $row['property_map_url'] ?? '',
        'thumbnail'             => $row['property_thumbnail'] ?? '',
        'gallery'               => $gallery,
        'amenities'             => $amenities,
        'status'                => $propStatus,
        'is_published_website'  => $isPublishedOnWebsite,
        'link_url'              => $linkUrl,
        'live_url'              => $liveUrl,
    ],
]);
exit();
