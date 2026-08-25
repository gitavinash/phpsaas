<?php
/**
 * V3 Vehicles Create API
 * Path: /api/v3/vehicles/create.php
 * Handles adding a new vehicle to the fleet
 */

header("Content-type: application/json; charset=utf-8");

if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__, 3);
}
if (!isset($connection)) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/config.php')) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
    }
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/lib/modules/connection.php')) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/modules/connection.php';
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : (int)($_SESSION['site_id'] ?? 0);
$userId    = defined('USER_ID')      ? (int) USER_ID : (int)($_SESSION['user_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'msg' => 'Method Not Allowed. Use POST.']);
    exit;
}

// Read JSON or multipart / form-data
$input = file_get_contents('php://input');
$json  = json_decode($input, true);
$data  = is_array($json) ? array_merge($_POST, $json) : $_POST;

$title       = trim($data['title'] ?? ($data['name'] ?? ''));
$category    = strtolower(trim($data['category'] ?? ($data['type'] ?? '')));
$category    = str_replace(' ', '_', $category);
$seating     = (int)($data['seating'] ?? ($data['vehicle_seating'] ?? 0));
$description = trim($data['description'] ?? '');
$thumbnail   = trim($data['thumbnail'] ?? ($data['icon'] ?? ''));

// Handle file upload if present
if (isset($_FILES['icon']) && $_FILES['icon']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/upload/' . date('Y/m/');
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0777, true);
    }
    $ext = pathinfo($_FILES['icon']['name'], PATHINFO_EXTENSION);
    $filename = time() . '_' . uniqid() . '.' . $ext;
    $targetPath = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['icon']['tmp_name'], $targetPath)) {
        $thumbnail = '/upload/' . date('Y/m/') . '/' . $filename;
    }
}

$errors = [];
if (empty($title)) {
    $errors[] = 'Vehicle model name is required.';
}
if (empty($category)) {
    $errors[] = 'Vehicle category/type is required.';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg'    => implode(' ', $errors),
        'errors' => $errors
    ]);
    exit;
}

// Check duplicates
$titleEsc       = mysqli_real_escape_string($connection, $title);
$categoryEsc    = mysqli_real_escape_string($connection, $category);
$descEsc        = mysqli_real_escape_string($connection, $description);
$thumbnailEsc   = mysqli_real_escape_string($connection, $thumbnail);

$checkSql = "SELECT vehicle_id FROM {$tblPrefix}vehicles 
             WHERE vehicle_title = '{$titleEsc}' AND vehicle_type = '{$categoryEsc}' 
             LIMIT 1";
$checkRes = mysqli_query($connection, $checkSql);

if ($checkRes && mysqli_num_rows($checkRes) > 0) {
    http_response_code(409);
    echo json_encode([
        'status' => 'error',
        'msg'    => "Vehicle '{$title}' already exists under the selected category."
    ]);
    exit;
}

// Insert new vehicle
$insertSql = "INSERT INTO {$tblPrefix}vehicles 
              (vehicle_thumbnail, vehicle_title, vehicle_description, vehicle_seating, vehicle_type, vehicle_is_deletable) 
              VALUES ('{$thumbnailEsc}', '{$titleEsc}', '{$descEsc}', {$seating}, '{$categoryEsc}', 1)";

if (mysqli_query($connection, $insertSql)) {
    $newId = mysqli_insert_id($connection);
    echo json_encode([
        'status'     => 'success',
        'msg'        => 'Vehicle added successfully!',
        'vehicle_id' => $newId,
        'data'       => [
            'id'          => $newId,
            'title'       => $title,
            'type'        => $category,
            'seating'     => $seating,
            'description' => $description,
            'thumbnail'   => $thumbnail
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Failed to create vehicle record in database.',
        'debug'  => mysqli_error($connection)
    ]);
}
