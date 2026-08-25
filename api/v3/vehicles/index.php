<?php
/**
 * V3 Vehicles Master Resource API Router
 * Path: /api/v3/vehicles/index.php
 * Routes GET (list), POST (create/update), PUT, and DELETE
 */

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

// Read JSON or multipart / form-data
$input = file_get_contents('php://input');
$jsonData = json_decode($input, true);
$data = is_array($jsonData) ? array_merge($_POST, $jsonData) : $_POST;

$actionParam = $_GET['action'] ?? ($data['action'] ?? ($_POST['action'] ?? ''));
$isUpdate = !empty($data['vehicle_id']) || !empty($_POST['vehicle_id']) || ($actionParam === 'update');
$isDelete = ($actionParam === 'delete') || $method === 'DELETE' || !empty($_GET['delete']);

if ($isDelete) {
    require_once __DIR__ . '/delete.php';
} elseif ($method === 'PUT' || ($method === 'POST' && $isUpdate)) {
    require_once __DIR__ . '/update.php';
} elseif ($method === 'POST') {
    require_once __DIR__ . '/create.php';
} else {
    require_once __DIR__ . '/list.php';
}
