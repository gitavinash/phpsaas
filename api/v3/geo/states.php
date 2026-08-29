<?php
/**
 * V3 States Listing API
 * Endpoint: /api/v3/geo/states.php
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
$countryId = (int)($_GET['country_id'] ?? 101); // Default India

$query = "SELECT state_id as id, state_name as name FROM {$tblPrefix}state WHERE state_country_id = {$countryId} AND state_status = 1 ORDER BY state_name ASC";
$result = mysqli_query($connection, $query);

$states = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $states[] = [
            'id'   => (int)$row['id'],
            'name' => stripslashes($row['name'])
        ];
    }
}

echo json_encode([
    'status' => 'success',
    'total'  => count($states),
    'states' => $states
]);
