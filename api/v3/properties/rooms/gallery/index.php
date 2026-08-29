<?php
/**
 * V3 Hotel Property Room Types Master Gallery API
 * Path: /API/v3/properties/rooms/gallery/index.php
 * GET: Retrieve master cover and gallery photos grouped by room type
 * POST: Update master cover and gallery photos for a specific room type across all seasonal slabs
 */

header('Content-Type: application/json; charset=utf-8');

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 5);
    if (file_exists($docRoot . '/config.php'))                  require_once $docRoot . '/config.php';
    if (file_exists($docRoot . '/lib/modules/connection.php')) require_once $docRoot . '/lib/modules/connection.php';
}

/** @var mysqli $connection */
if (!isset($connection) || !($connection instanceof mysqli)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Database connection unavailable.']);
    exit();
}

$prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId = defined('SITE_ID') ? (int) SITE_ID : (int) ($_SESSION['site_id'] ?? 0);
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

// Helper to decode ID
function resolveId($rawId) {
    if (is_numeric($rawId) && (int)$rawId > 0) return (int)$rawId;
    if (!empty($rawId)) {
        $str = trim((string)$rawId, " \t\n\r\0\x0B\"'");
        if (is_numeric($str) && (int)$str > 0) return (int)$str;
        $dec = base64_decode($str, true);
        if ($dec !== false && is_numeric($dec) && (int)$dec > 0) return (int)$dec;
        $dec2 = base64_decode($str);
        if ($dec2 !== false && is_numeric($dec2) && (int)$dec2 > 0) return (int)$dec2;
    }
    return 0;
}

// -----------------------------------------------------------------------------
// GET: Retrieve Room Types & Photo Galleries
// -----------------------------------------------------------------------------
if ($method === 'GET') {
    $propIdRaw = $_GET['property_id'] ?? ($_GET['id'] ?? ($_GET['propertyId'] ?? ($_REQUEST['property_id'] ?? ($_REQUEST['id'] ?? 0))));
    $propId    = resolveId($propIdRaw);

    if ($propId <= 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Valid property ID is required.']);
        exit();
    }

    $siteCond = $siteId > 0 ? "AND room_site_id = {$siteId}" : "";
    $sql = "SELECT room_type, room_feature_image, room_gallery, COUNT(*) as slab_count
            FROM {$prefix}room
            WHERE room_property_id = {$propId} {$siteCond}
            GROUP BY room_type
            ORDER BY room_type ASC";
    $res = mysqli_query($connection, $sql);

    $roomTypes = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $gallery = [];
            if (!empty($row['room_gallery'])) {
                $g = json_decode($row['room_gallery'], true);
                if (is_array($g)) $gallery = $g;
            }

            $type = $row['room_type'] ?? '';
            $roomTypes[$type] = [
                'type'          => $type,
                'type_label'    => ucwords(str_replace('_', ' ', $type)),
                'feature_image' => $row['room_feature_image'] ?? '',
                'gallery'       => $gallery,
                'slab_count'    => (int)($row['slab_count'] ?? 0),
            ];
        }
    }

    http_response_code(200);
    echo json_encode([
        'status'     => 'success',
        'room_types' => array_values($roomTypes),
    ]);
    exit();
}

// -----------------------------------------------------------------------------
// POST: Save Master Photos for a Room Type
// -----------------------------------------------------------------------------
if ($method === 'POST') {
    $propIdRaw = $_POST['property_id'] ?? ($_POST['propertyId'] ?? ($_POST['prop_id'] ?? ($_GET['property_id'] ?? ($_GET['id'] ?? 0))));
    $propId    = resolveId($propIdRaw);

    if ($propId <= 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Valid property ID is required.']);
        exit();
    }

    $roomType = strtolower(str_replace(' ', '_', trim($_POST['room_type'] ?? ($_POST['roomType'] ?? ''))));
    if (empty($roomType)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Please select a room type to manage its gallery.']);
        exit();
    }

    $typeEsc = mysqli_real_escape_string($connection, $roomType);

    // Existing photos
    $oldFeatureImg = '';
    $oldGallery    = [];
    $chk = mysqli_query($connection, "SELECT room_feature_image, room_gallery FROM {$prefix}room WHERE room_property_id = {$propId} AND room_type = '{$typeEsc}' ORDER BY (room_feature_image != '' OR (room_gallery != '' AND room_gallery != '[]')) DESC, room_id ASC LIMIT 1");
    if ($chk && ($rRow = mysqli_fetch_assoc($chk))) {
        $oldFeatureImg = $rRow['room_feature_image'] ?? '';
        if (!empty($rRow['room_gallery'])) {
            $parsed = json_decode($rRow['room_gallery'], true);
            if (is_array($parsed)) $oldGallery = $parsed;
        }
    }

    // Cover image
    $newFeatureImg = $oldFeatureImg;
    if (isset($_POST['kept_feature_image'])) {
        $newFeatureImg = trim($_POST['kept_feature_image']);
    }
    if (!empty($_FILES['feature_image']['tmp_name']) && $_FILES['feature_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = defined('UPLOAD_PATH') ? UPLOAD_PATH : ($_SERVER['DOCUMENT_ROOT'] . '/uploads/property/rooms/');
        if (!is_dir($uploadDir)) @mkdir($uploadDir, 0755, true);
        $ext = strtolower(pathinfo($_FILES['feature_image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','gif','webp'], true)) {
            $fn = 'room_master_' . $propId . '_' . $roomType . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['feature_image']['tmp_name'], rtrim($uploadDir, '/') . '/' . $fn)) {
                $newFeatureImg = '/uploads/property/rooms/' . $fn;
            }
        }
    }

    // Gallery images (kept + newly uploaded)
    $newGallery = [];
    if (isset($_POST['kept_gallery'])) {
        $kept = json_decode($_POST['kept_gallery'], true);
        if (is_array($kept)) {
            foreach ($kept as $item) {
                if (is_array($item) && !empty($item['path'])) {
                    $newGallery[] = $item;
                } elseif (is_string($item) && !empty($item)) {
                    $newGallery[] = ['name' => basename($item), 'path' => $item, 'type' => pathinfo($item, PATHINFO_EXTENSION)];
                }
            }
        }
    } else {
        $newGallery = $oldGallery;
    }

    if (!empty($_FILES['gallery_files']) && !empty($_FILES['gallery_files']['name'])) {
        $uploadDir = defined('UPLOAD_PATH') ? UPLOAD_PATH : ($_SERVER['DOCUMENT_ROOT'] . '/uploads/property/rooms/');
        if (!is_dir($uploadDir)) @mkdir($uploadDir, 0755, true);
        $allowed = ['jpg','jpeg','png','gif','webp'];
        $files = $_FILES['gallery_files'];
        $count = is_array($files['name']) ? count($files['name']) : 1;
        for ($i = 0; $i < $count; $i++) {
            $tmpName  = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
            $origName = is_array($files['name'])     ? $files['name'][$i]     : $files['name'];
            $err      = is_array($files['error'])    ? $files['error'][$i]    : $files['error'];
            if ($err !== UPLOAD_ERR_OK || empty($tmpName)) continue;
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed, true)) continue;
            $fn   = 'room_gal_' . $propId . '_' . $roomType . '_' . time() . '_' . $i . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($tmpName, rtrim($uploadDir, '/') . '/' . $fn)) {
                $newGallery[] = ['name' => $origName, 'path' => '/uploads/property/rooms/' . $fn, 'type' => $ext];
            }
        }
    }

    $featImgSql  = mysqli_real_escape_string($connection, $newFeatureImg);
    $galleryJson = mysqli_real_escape_string($connection, json_encode($newGallery));

    // Check if any record for this room_type exists in this property
    $siteCond = $siteId > 0 ? "AND room_site_id = {$siteId}" : "";
    $chk = mysqli_query($connection, "SELECT room_id FROM {$prefix}room WHERE room_property_id = {$propId} AND room_type = '{$typeEsc}' {$siteCond} LIMIT 1");
    if ($chk && mysqli_num_rows($chk) > 0) {
        // Update all existing records of this room type
        $updateSql = "UPDATE {$prefix}room
                      SET room_feature_image = '{$featImgSql}',
                          room_gallery = '{$galleryJson}',
                          room_update = NOW()
                      WHERE room_property_id = {$propId}
                        AND room_type = '{$typeEsc}'
                        {$siteCond}";
        mysqli_query($connection, $updateSql);
    } else {
        // Insert a new base record for this room type so its master gallery is saved
        $insertSql = "INSERT INTO {$prefix}room (
            room_property_id, room_site_id, room_type, room_feature_image, room_gallery,
            room_description, room_season_name, room_status, room_create, room_update
        ) VALUES (
            {$propId}, {$siteId}, '{$typeEsc}', '{$featImgSql}', '{$galleryJson}',
            '', '', 1, NOW(), NOW()
        )";
        mysqli_query($connection, $insertSql);
    }

    $roomTypeLabel = ucwords(preg_replace('/[^a-zA-Z0-9]+/', ' ', trim($roomType)));

    http_response_code(200);
    echo json_encode([
        'status'          => 'success',
        'msg'             => 'Master gallery for ' . $roomTypeLabel . ' updated successfully!',
        'room_type'       => $roomType,
        'room_type_label' => $roomTypeLabel,
        'feature_image'   => $newFeatureImg,
        'gallery'         => $newGallery,
    ]);
    exit();
}
