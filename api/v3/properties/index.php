<?php
header('Content-Type: application/json; charset=utf-8');
if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php')) require_once $docRoot . '/config.php';
    if (file_exists($docRoot . '/lib/modules/connection.php')) require_once $docRoot . '/lib/modules/connection.php';
}
/** @var mysqli $connection */
if (!($connection instanceof mysqli)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Database connection is unavailable.', 'properties' => []]);
    exit;
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
$search = mysqli_real_escape_string($connection, trim($_GET['search'] ?? ''));
$status = trim($_GET['status'] ?? '');
$propertyType = mysqli_real_escape_string($connection, trim($_GET['property_type'] ?? ''));
$location = mysqli_real_escape_string($connection, trim($_GET['location'] ?? ''));
$roomType = mysqli_real_escape_string($connection, trim($_GET['room_type'] ?? ''));

$where = ['p.property_site_id = ' . $siteId];

if ($search !== '') $where[] = "(p.property_title LIKE '%{$search}%' OR p.property_email LIKE '%{$search}%' OR p.property_mobile LIKE '%{$search}%' OR p.property_address LIKE '%{$search}%' OR p.property_city LIKE '%{$search}%')";

if ($status === 'active') $where[] = 'p.property_status = 1';

if ($status === 'inactive') $where[] = '(p.property_status = 0 OR p.property_status IS NULL)';

if ($propertyType !== '') $where[] = "p.property_type = '{$propertyType}'";

if ($location !== '') $where[] = "p.property_city = '{$location}'";
if ($roomType !== '') $where[] = "EXISTS (SELECT 1 FROM {$prefix}room filter_room WHERE filter_room.room_property_id = p.property_id AND filter_room.room_site_id = {$siteId} AND filter_room.room_type = '{$roomType}')";
$query = "SELECT p.property_id, p.property_title, p.property_thumbnail, p.property_email, p.property_mobile, p.property_address, p.property_city, p.property_pincode, p.property_type, COALESCE(p.property_status, 0) AS property_status, COALESCE(l.link_is_featured, 0) AS is_featured, (SELECT COUNT(*) FROM {$prefix}room r WHERE r.room_property_id = p.property_id AND r.room_site_id = {$siteId}) AS room_count FROM {$prefix}property p LEFT JOIN {$prefix}link l ON l.link_relation_id = p.property_id AND l.link_type = 'property' AND l.link_site_id = {$siteId} WHERE " . implode(' AND ', $where) . ' ORDER BY p.property_id DESC';

$result = mysqli_query($connection, $query);
if ($result === false) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Unable to load properties.', 'properties' => []]);
    exit;
}
$properties = [];
$locations = [];
$rooms = 0;
$propertyTypes = [];
if ($result) while ($row = mysqli_fetch_assoc($result)) {
    $id = (int) $row['property_id'];
    $city = trim($row['property_city'] ?? '');
    $roomCount = (int) $row['room_count'];
    $rooms += $roomCount;
    if ($city !== '' && !in_array($city, $locations, true)) $locations[] = $city;
    $type = trim($row['property_type'] ?? 'hotel');
    if ($type !== '' && !in_array($type, $propertyTypes, true)) $propertyTypes[] = $type;
    $properties[] = ['id' => $id, 'encoded_id' => base64_encode((string) $id), 'title' => $row['property_title'] ?: 'Untitled property', 'thumbnail' => trim($row['property_thumbnail'] ?? ''), 'email' => $row['property_email'] ?? '', 'mobile' => $row['property_mobile'] ?? '', 'address' => trim($row['property_address'] ?? ''), 'city' => $city, 'pincode' => trim($row['property_pincode'] ?? ''), 'type' => $row['property_type'] ?: 'hotel', 'status' => (int) $row['property_status'], 'is_featured' => (int) $row['is_featured'], 'room_count' => $roomCount];
}
$totalQuery = mysqli_query($connection, "SELECT COUNT(*) AS total, SUM(property_status = 1) AS active FROM {$prefix}property WHERE property_site_id = {$siteId}");
$totalRow = $totalQuery ? mysqli_fetch_assoc($totalQuery) : [];
sort($locations);
sort($propertyTypes);
echo json_encode(['status' => 'success', 'metrics' => ['total' => (int) ($totalRow['total'] ?? 0), 'active' => (int) ($totalRow['active'] ?? 0), 'rooms' => $rooms, 'locations' => count($locations), 'filtered' => count($properties)], 'locations' => $locations, 'property_types' => $propertyTypes, 'properties' => $properties], JSON_UNESCAPED_SLASHES);
