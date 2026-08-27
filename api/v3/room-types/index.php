<?php

header('Content-Type: application/json; charset=utf-8');
if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 4);
    if (file_exists($docRoot . '/config.php')) require_once $docRoot . '/config.php';
    if (file_exists($docRoot . '/lib/modules/connection.php')) require_once $docRoot . '/lib/modules/connection.php';
}
/** @var mysqli $connection */

$prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId = defined('SITE_ID') ? (int) SITE_ID : (int) ($_SESSION['site_id'] ?? 0);
$type = defined('SITE_APP') && SITE_APP === 'spa' ? 'spa_room' : 'room';
$typeEscaped = mysqli_real_escape_string($connection, $type);
$search = mysqli_real_escape_string($connection, trim($_GET['search'] ?? ''));
$where = "category_type = '{$typeEscaped}'";

if ($search !== '') $where .= " AND (category_title LIKE '%{$search}%' OR category_description LIKE '%{$search}%')";

$query = "SELECT category_id, category_title, category_image, category_description, category_deletable FROM {$prefix}category WHERE {$where} ORDER BY category_title ASC";

$result = mysqli_query($connection, $query);
$roomTypes = [];
$withDescriptions = 0;
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $title = trim($row['category_title'] ?? 'Untitled room type');
        $description = trim($row['category_description'] ?? '');
        if ($description !== '') $withDescriptions++;
        $roomTypes[] = [
            'id' => (int) $row['category_id'],
            'title' => $title,
            'image' => trim($row['category_image'] ?? ''),
            'description' => $description,
            'deletable' => (int) ($row['category_deletable'] ?? 1),
        ];
    }
}
$total = count($roomTypes);
echo json_encode(['status' => 'success', 'metrics' => ['total' => $total, 'descriptions' => $withDescriptions, 'categories' => $total], 'room_types' => $roomTypes], JSON_UNESCAPED_SLASHES);
