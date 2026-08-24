<?php

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : 0;

$roomTypes = [];

// 1. Master Room Types from isw_category (as in vendors/property/room.php)
$catQuery = "
    SELECT category_id, category_title, category_description 
    FROM {$tblPrefix}category 
    WHERE category_type = 'room'
    ORDER BY category_title ASC
";
if ($catRes = mysqli_query($connection, $catQuery)) {
    while ($cRow = mysqli_fetch_assoc($catRes)) {
        $cTitle = trim($cRow['category_title'] ?? '');
        if (!empty($cTitle) && !in_array($cTitle, $roomTypes, true)) {
            $roomTypes[] = $cTitle;
        }
    }
}

// 2. Room Types assigned to vendor properties in isw_room
$query = "
    SELECT DISTINCT TRIM(room_type) AS room_type 
    FROM {$tblPrefix}room 
    WHERE room_site_id = {$siteId} 
      AND room_type IS NOT NULL 
      AND TRIM(room_type) <> ''
    ORDER BY room_type ASC
";

if ($res = mysqli_query($connection, $query)) {
    while ($row = mysqli_fetch_assoc($res)) {
        $rt = trim($row['room_type']);
        if (!empty($rt) && !in_array($rt, $roomTypes, true)) {
            $roomTypes[] = $rt;
        }
    }
}

// Fallback defaults if no rooms registered yet
if (empty($roomTypes)) {
    $roomTypes = [
        'Standard Room',
        'Deluxe Room',
        'Super Deluxe Room',
        'Executive Suite',
        'Luxury Tent / Camp',
        'Family Suite',
        'Cottage / Villa',
        'Dormitory Bed'
    ];
}

echo json_encode([
    'status'     => 'success',
    'room_types' => array_values($roomTypes)
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
