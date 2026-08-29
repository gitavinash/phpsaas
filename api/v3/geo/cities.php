<?php
/**
 * V3 Cities Listing API
 * Endpoint: /api/v3/geo/cities.php
 */

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$stateId = (int)($_GET['state_id'] ?? 0);

if ($stateId <= 0) {
    echo json_encode([
        'status' => 'success',
        'total'  => 0,
        'cities' => []
    ]);
    exit;
}

$query = "SELECT city_id as id, city_name as name FROM {$tblPrefix}city WHERE city_state_id = {$stateId} AND city_status = 1 ORDER BY city_name ASC";
$result = mysqli_query($connection, $query);

$cities = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $cities[] = [
            'id'   => (int)$row['id'],
            'name' => stripslashes($row['name'])
        ];
    }
}

echo json_encode([
    'status' => 'success',
    'total'  => count($cities),
    'cities' => $cities
]);
