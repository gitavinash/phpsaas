<?php
/**
 * V3 Vehicles Update API
 * Path: /api/v3/vehicles/update.php
 * Handles updating an existing vehicle's attributes
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

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'POST');
if ($method !== 'POST' && $method !== 'PUT') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'msg' => 'Method Not Allowed. Use POST or PUT.']);
    exit;
}

// Read JSON or form body
$input = file_get_contents('php://input');
$json  = json_decode($input, true);
$data  = is_array($json) ? array_merge($_POST, $json) : $_POST;

$rawId = $data['vehicle_id'] ?? ($data['id'] ?? ($_GET['id'] ?? ($_GET['vehicle_id'] ?? 0)));
$vehicleId = 0;
if (is_numeric($rawId) && (int)$rawId > 0) {
    $vehicleId = (int)$rawId;
} else if (!empty($rawId)) {
    $decoded = base64_decode((string)$rawId, true);
    if ($decoded !== false && is_numeric($decoded)) {
        $vehicleId = (int)$decoded;
    }
}

if ($vehicleId <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Invalid or missing vehicle ID.']);
    exit;
}

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

// 1. Handle Feature Image Upload (feature_image or icon)
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

// 2. Handle Gallery Images (both existing retained list and newly uploaded files)
$galleryItems = [];
if (isset($data['existing_gallery'])) {
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

// Check vehicle existence
$checkSql = "SELECT * FROM {$tblPrefix}vehicles WHERE vehicle_id = {$vehicleId} LIMIT 1";
$checkRes = mysqli_query($connection, $checkSql);
if (!$checkRes || mysqli_num_rows($checkRes) === 0) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'msg' => 'Vehicle record not found.']);
    exit;
}

$existing = mysqli_fetch_assoc($checkRes);
if (empty($thumbnail)) {
    $thumbnail = $existing['vehicle_thumbnail'] ?? '';
    // If still empty and gallery has items, use the first gallery image
    if (empty($thumbnail) && !empty($cleanedGallery)) {
        $thumbnail = $cleanedGallery[0];
    }
}

// If existing_gallery was not sent at all in the request, keep what was in DB
if (!isset($data['existing_gallery']) && empty($_FILES['gallery_files']['name'])) {
    $galleryJson = $existing['vehicle_gallery'] ?? '[]';
    $decodedEx = json_decode($galleryJson, true);
    if (is_array($decodedEx)) {
        $cleanedGallery = $decodedEx;
    }
} else {
    $galleryJson = json_encode($cleanedGallery);
}

$titleEsc       = mysqli_real_escape_string($connection, $title);
$categoryEsc    = mysqli_real_escape_string($connection, $category);
$descEsc        = mysqli_real_escape_string($connection, $description);
$thumbnailEsc   = mysqli_real_escape_string($connection, $thumbnail);
$galleryEsc     = mysqli_real_escape_string($connection, $galleryJson);
$fuelTypeEsc    = mysqli_real_escape_string($connection, $fuelType);

// Check duplicate title in same category for different vehicle
$dupSql = "SELECT vehicle_id FROM {$tblPrefix}vehicles 
           WHERE vehicle_title = '{$titleEsc}' AND vehicle_type = '{$categoryEsc}' AND vehicle_id != {$vehicleId} 
           LIMIT 1";
$dupRes = mysqli_query($connection, $dupSql);
if ($dupRes && mysqli_num_rows($dupRes) > 0) {
    http_response_code(409);
    echo json_encode([
        'status' => 'error',
        'msg'    => "Another vehicle named '{$title}' already exists in this category."
    ]);
    exit;
}

$updateSql = "UPDATE {$tblPrefix}vehicles SET 
                vehicle_title = '{$titleEsc}',
                vehicle_type = '{$categoryEsc}',
                vehicle_seating = {$seating},
                vehicle_luggage = {$luggage},
                vehicle_per_km_rate_with_tax = {$rateWithTax},
                vehicle_per_km_rate_without_tax = {$rateWithoutTax},
                vehicle_fuel_type = '{$fuelTypeEsc}',
                vehicle_description = '{$descEsc}',
                vehicle_thumbnail = '{$thumbnailEsc}',
                vehicle_gallery = '{$galleryEsc}'
              WHERE vehicle_id = {$vehicleId}";

if (mysqli_query($connection, $updateSql)) {
    echo json_encode([
        'status'     => 'success',
        'msg'        => 'Vehicle updated successfully!',
        'vehicle_id' => $vehicleId,
        'data'       => [
            'id'                      => $vehicleId,
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
        'msg'    => 'Failed to update vehicle record in database.',
        'debug'  => mysqli_error($connection)
    ]);
}
