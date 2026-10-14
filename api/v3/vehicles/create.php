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
$luggage     = (int)($data['luggage'] ?? ($data['vehicle_luggage'] ?? 0));
$rateWithTax    = (float)($data['rate_per_km_with_tax'] ?? ($data['vehicle_per_km_rate_with_tax'] ?? 0));
$rateWithoutTax = (float)($data['rate_per_km_without_tax'] ?? ($data['vehicle_per_km_rate_without_tax'] ?? 0));
$fuelType    = trim($data['fuel_type'] ?? ($data['vehicle_fuel_type'] ?? ''));
$description = trim($data['description'] ?? '');
$thumbnail   = trim($data['thumbnail'] ?? ($data['icon'] ?? ($data['feature_image'] ?? '')));

$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/upload/' . date('Y/m/');
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0777, true);
}

// 1. Handle Feature Image Upload (icon or feature_image)
$featureUpload = $_FILES['feature_image'] ?? ($_FILES['icon'] ?? null);
if ($featureUpload && ($featureUpload['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($featureUpload['name'], PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'])) {
        $filename = time() . '_' . uniqid() . '.' . $ext;
        if (move_uploaded_file($featureUpload['tmp_name'], $uploadDir . $filename)) {
            $thumbnail = '/upload/' . date('Y/m/') . '/' . $filename;
        }
    }
}

// 2. Handle Gallery Images (both existing list and newly uploaded files)
$galleryItems = [];
if (!empty($data['existing_gallery'])) {
    if (is_array($data['existing_gallery'])) {
        $galleryItems = $data['existing_gallery'];
    } else {
        $decoded = json_decode((string)$data['existing_gallery'], true);
        if (is_array($decoded)) {
            $galleryItems = $decoded;
        }
    }
}

$cleanedGallery = [];
foreach ($galleryItems as $item) {
    if (is_string($item) && trim($item) !== '') {
        $cleanedGallery[] = trim($item);
    } elseif (is_array($item) && !empty($item['path'])) {
        $cleanedGallery[] = trim($item['path']);
    }
}

// Check newly uploaded gallery files
if (!empty($_FILES['gallery_files']['name'])) {
    $names    = is_array($_FILES['gallery_files']['name']) ? $_FILES['gallery_files']['name'] : [$_FILES['gallery_files']['name']];
    $tmpNames = is_array($_FILES['gallery_files']['tmp_name']) ? $_FILES['gallery_files']['tmp_name'] : [$_FILES['gallery_files']['tmp_name']];
    $errors   = is_array($_FILES['gallery_files']['error']) ? $_FILES['gallery_files']['error'] : [$_FILES['gallery_files']['error']];

    for ($i = 0; $i < count($names); $i++) {
        if (($errors[$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK && !empty($tmpNames[$i])) {
            $ext = strtolower(pathinfo($names[$i], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'])) {
                $filename = time() . '_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($tmpNames[$i], $uploadDir . $filename)) {
                    $cleanedGallery[] = '/upload/' . date('Y/m/') . '/' . $filename;
                }
            }
        }
    }
}

$cleanedGallery = array_values(array_unique($cleanedGallery));
if (empty($thumbnail) && !empty($cleanedGallery)) {
    $thumbnail = $cleanedGallery[0];
}

$galleryJson = json_encode($cleanedGallery);

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
$galleryEsc     = mysqli_real_escape_string($connection, $galleryJson);
$fuelTypeEsc    = mysqli_real_escape_string($connection, $fuelType);

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
              (vehicle_thumbnail, vehicle_gallery, vehicle_title, vehicle_description, vehicle_seating, vehicle_per_km_rate_with_tax, vehicle_per_km_rate_without_tax, vehicle_luggage, vehicle_fuel_type, vehicle_type, vehicle_is_deletable) 
              VALUES ('{$thumbnailEsc}', '{$galleryEsc}', '{$titleEsc}', '{$descEsc}', {$seating}, {$rateWithTax}, {$rateWithoutTax}, {$luggage}, '{$fuelTypeEsc}', '{$categoryEsc}', 1)";

if (mysqli_query($connection, $insertSql)) {
    $newId = mysqli_insert_id($connection);
    echo json_encode([
        'status'     => 'success',
        'msg'        => 'Vehicle added successfully!',
        'vehicle_id' => $newId,
        'data'       => [
            'id'                      => $newId,
            'title'                   => $title,
            'type'                    => $category,
            'seating'                 => $seating,
            'luggage'                 => $luggage,
            'rate_per_km_with_tax'    => $rateWithTax,
            'rate_per_km_without_tax' => $rateWithoutTax,
            'fuel_type'               => $fuelType,
            'description'             => $description,
            'thumbnail'               => $thumbnail,
            'gallery'                 => $cleanedGallery,
            'gallery_count'           => count($cleanedGallery)
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
