<?php
/**
 * V3 Transporters Master Resource API
 * Path: /api/v3/transporters/index.php
 * Routes GET (list), POST (create/update), PUT, and DELETE
 */

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

// Read JSON input to check if updating
$input = file_get_contents('php://input');
$data  = json_decode($input, true) ?? $_POST;
$actionParam = $_GET['action'] ?? ($data['action'] ?? ($_POST['action'] ?? ''));
$isUpdate = !empty($data['transporter_id']) || !empty($data['id']) || !empty($_GET['id']) || !empty($_POST['transporter_id']) || ($actionParam === 'update');

if ($method === 'PUT' || ($method === 'POST' && $isUpdate)) {
    require_once __DIR__ . '/update.php';
} elseif ($method === 'POST') {
    require_once __DIR__ . '/create.php';
} elseif ($method === 'DELETE') {
    require_once __DIR__ . '/delete.php';
} else {
    require_once __DIR__ . '/list.php';
}
