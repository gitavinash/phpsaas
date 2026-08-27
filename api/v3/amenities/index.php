<?php

header('Content-Type: application/json; charset=utf-8');

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 4);
    if (file_exists($docRoot . '/config.php')) require_once $docRoot . '/config.php';
    if (file_exists($docRoot . '/lib/modules/connection.php')) require_once $docRoot . '/lib/modules/connection.php';
}
/** @var mysqli $connection */

$table = (defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_') . 'amenities';
$search = mysqli_real_escape_string($connection, trim($_GET['search'] ?? ''));
$category = mysqli_real_escape_string($connection, trim($_GET['category'] ?? ''));
$where = [];

if ($search !== '') {
    $where[] = "(amenitie_title LIKE '%{$search}%' OR amenitie_description LIKE '%{$search}%' OR amenitie_type LIKE '%{$search}%')";
}
if ($category !== '') $where[] = "amenitie_type = '{$category}'";

$query = "SELECT amenitie_id, amenitie_title, amenitie_description, amenitie_thumbnail, amenitie_is_deletable, amenitie_type FROM {$table}";
if ($where) $query .= ' WHERE ' . implode(' AND ', $where);
$query .= ' ORDER BY amenitie_title ASC';

$result = mysqli_query($connection, $query);
$amenities = [];
$categories = [];
$categoryCounts = [];
$withIcons = 0;

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $type = trim($row['amenitie_type'] ?? 'other_facilities');
        $title = trim($row['amenitie_title'] ?? 'Untitled amenity');
        $categoryCounts[$type] = ($categoryCounts[$type] ?? 0) + 1;
        if (!in_array($type, $categories, true)) $categories[] = $type;
        if (!empty($row['amenitie_thumbnail'])) $withIcons++;
        $amenities[] = [
            'id' => (int) $row['amenitie_id'],
            'title' => $title,
            'description' => trim($row['amenitie_description'] ?? ''),
            'thumbnail' => trim($row['amenitie_thumbnail'] ?? ''),
            'type' => $type,
            'type_label' => ucwords(str_replace('_', ' ', $type)),
            'is_deletable' => (int) ($row['amenitie_is_deletable'] ?? 1),
        ];
    }
}

arsort($categoryCounts);
$topCategory = $categoryCounts ? (string) array_key_first($categoryCounts) : '';
$totalQuery = mysqli_query($connection, "SELECT COUNT(*) AS total, SUM(amenitie_thumbnail <> '') AS with_icons FROM {$table}");
$totalRow = $totalQuery ? mysqli_fetch_assoc($totalQuery) : [];

echo json_encode([
    'status' => 'success',
    'metrics' => [
        'total' => (int) ($totalRow['total'] ?? count($amenities)),
        'categories' => count($categories),
        'with_icons' => (int) ($totalRow['with_icons'] ?? $withIcons),
        'top_category' => ucwords(str_replace('_', ' ', $topCategory)),
        'filtered' => count($amenities),
    ],
    'categories' => $categories,
    'amenities' => $amenities,
], JSON_UNESCAPED_SLASHES);
