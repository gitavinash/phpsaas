<?php
/**
 * V3 Hotel Property Rooms API
 * Path: /api/v3/properties/rooms/index.php
 * GET: List all rooms for a property
 * POST: Create or Update a room with meal plan rates, price duration/season, and photo gallery
 */

header('Content-Type: application/json; charset=utf-8');

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 4);
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
// GET: List Rooms
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
    $sql = "SELECT * FROM {$prefix}room
            WHERE room_property_id = {$propId} {$siteCond}
            ORDER BY room_id ASC";
    $res = mysqli_query($connection, $sql);

    $rooms = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $gallery = [];
            if (!empty($row['room_gallery'])) {
                $g = json_decode($row['room_gallery'], true);
                if (is_array($g)) $gallery = $g;
            }

            $rooms[] = [
                'id'            => (int)$row['room_id'],
                'property_id'   => (int)$row['room_property_id'],
                'type'          => $row['room_type'] ?? '',
                'type_label'    => ucwords(str_replace('_', ' ', $row['room_type'] ?? '')),
                'price_from'    => !empty($row['room_price_from']) ? $row['room_price_from'] : '',
                'price_to'      => !empty($row['room_price_to']) ? $row['room_price_to'] : '',
                'season_name'   => $row['room_season_name'] ?? '',
                'description'   => $row['room_description'] ?? '',
                'feature_image' => $row['room_feature_image'] ?? '',
                'gallery'       => $gallery,
                'status'        => (int)($row['room_status'] ?? 1),
                'rates' => [
                    'ep' => [
                        'mrp' => (int)($row['room_ep_mrp'] ?? 0),
                        'b2c' => (int)($row['room_ep_b2c'] ?? 0),
                        'b2b' => (int)($row['room_ep_b2b'] ?? 0),
                    ],
                    'cp' => [
                        'mrp' => (int)($row['room_cp_mrp'] ?? 0),
                        'b2c' => (int)($row['room_cp_b2c'] ?? 0),
                        'b2b' => (int)($row['room_cp_b2b'] ?? 0),
                    ],
                    'map' => [
                        'mrp' => (int)($row['room_map_mrp'] ?? 0),
                        'b2c' => (int)($row['room_map_b2c'] ?? 0),
                        'b2b' => (int)($row['room_map_b2b'] ?? 0),
                    ],
                    'ap' => [
                        'mrp' => (int)($row['room_ap_mrp'] ?? 0),
                        'b2c' => (int)($row['room_ap_b2c'] ?? 0),
                        'b2b' => (int)($row['room_ap_b2b'] ?? 0),
                    ],
                ],
            ];
        }
    }

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'rooms'  => $rooms,
        'total'  => count($rooms),
    ]);
    exit();
}

// -----------------------------------------------------------------------------
// POST: Create or Update Room
// -----------------------------------------------------------------------------
if ($method === 'POST') {
    $roomId    = (int)($_POST['room_id'] ?? ($_POST['roomId'] ?? 0));
    $propIdRaw = $_POST['property_id'] ?? ($_POST['propertyId'] ?? ($_POST['prop_id'] ?? ($_GET['property_id'] ?? ($_GET['id'] ?? 0))));
    $propId    = resolveId($propIdRaw);

    // If property ID was not passed or 0 on update, resolve from existing room record
    if ($propId <= 0 && $roomId > 0) {
        $rChk = mysqli_query($connection, "SELECT room_property_id FROM {$prefix}room WHERE room_id = {$roomId} LIMIT 1");
        if ($rChk && ($rRow = mysqli_fetch_assoc($rChk))) {
            $propId = (int)$rRow['room_property_id'];
        }
    }

    if ($propId <= 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Valid property ID is required.']);
        exit();
    }

    $roomType    = strtolower(str_replace(' ', '_', trim($_POST['room_type'] ?? ($_POST['roomType'] ?? ''))));
    $description = trim($_POST['description'] ?? ($_POST['comment'] ?? ''));
    $status      = isset($_POST['room_status']) ? (int)$_POST['room_status'] : 1;

    // Price Duration & Season
    $priceFromRaw = trim($_POST['price_from'] ?? '');
    $priceToRaw   = trim($_POST['price_to'] ?? '');
    $seasonName   = mysqli_real_escape_string($connection, trim($_POST['season_name'] ?? ''));

    $priceFromSql = !empty($priceFromRaw) ? "'" . mysqli_real_escape_string($connection, $priceFromRaw) . "'" : "NULL";
    $priceToSql   = !empty($priceToRaw)   ? "'" . mysqli_real_escape_string($connection, $priceToRaw) . "'"   : "NULL";

    if (empty($roomType)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Please select a room type.']);
        exit();
    }

    // Rate plans
    $epMrp = (int)($_POST['priceMRP']   ?? ($_POST['ep_mrp'] ?? 0));
    $epB2c = (int)($_POST['priceB2C']   ?? ($_POST['ep_b2c'] ?? 0));
    $epB2b = (int)($_POST['priceB2B']   ?? ($_POST['ep_b2b'] ?? 0));

    $cpMrp = (int)($_POST['priceCP']    ?? ($_POST['cp_mrp'] ?? 0));
    $cpB2c = (int)($_POST['priceCPB2C'] ?? ($_POST['cp_b2c'] ?? 0));
    $cpB2b = (int)($_POST['priceCPB2B'] ?? ($_POST['cp_b2b'] ?? 0));

    $mapMrp = (int)($_POST['priceMAP']    ?? ($_POST['map_mrp'] ?? 0));
    $mapB2c = (int)($_POST['priceMAPB2C'] ?? ($_POST['map_b2c'] ?? 0));
    $mapB2b = (int)($_POST['priceMAPB2B'] ?? ($_POST['map_b2b'] ?? 0));

    $apMrp = (int)($_POST['priceAP']    ?? ($_POST['ap_mrp'] ?? 0));
    $apB2c = (int)($_POST['priceAPB2C'] ?? ($_POST['ap_b2c'] ?? 0));
    $apB2b = (int)($_POST['priceAPB2B'] ?? ($_POST['ap_b2b'] ?? 0));

    // Existing room check if update
    $oldFeatureImg = '';
    $oldGallery    = [];
    if ($roomId > 0) {
        $chk = mysqli_query($connection, "SELECT room_feature_image, room_gallery FROM {$prefix}room WHERE room_id = {$roomId} LIMIT 1");
        if ($chk && ($rRow = mysqli_fetch_assoc($chk))) {
            $oldFeatureImg = $rRow['room_feature_image'] ?? '';
            if (!empty($rRow['room_gallery'])) {
                $parsed = json_decode($rRow['room_gallery'], true);
                if (is_array($parsed)) $oldGallery = $parsed;
            }
        }
    }

    // Setup upload paths matching imageUploader.php (ROOT/upload/SITE_DOMAIN/Y/m/)
    $rootPath = defined('ROOT') ? ROOT : (!empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 4));
    $siteDomain = defined('SITE_DOMAIN') ? SITE_DOMAIN : '';
    if (empty($siteDomain) && !empty($siteId)) {
        $sq = mysqli_query($connection, "SELECT site_domain FROM {$prefix}site WHERE site_id = {$siteId} LIMIT 1");
        if ($sq && ($sr = mysqli_fetch_assoc($sq)) && !empty($sr['site_domain'])) {
            $siteDomain = $sr['site_domain'];
        }
    }
    if (empty($siteDomain) && !empty($_SERVER['HTTP_HOST'])) {
        $siteDomain = preg_replace('/:[0-9]+$/', '', $_SERVER['HTTP_HOST']);
    }
    if (empty($siteDomain)) {
        $siteDomain = 'default';
    }

    $uploadPath = rtrim($rootPath, '/') . '/upload/' . $siteDomain . '/' . date('Y/m/');
    $originalUploadDir = $uploadPath . 'original/';
    $thumbnailUploadDir = $uploadPath . 'thumbnail/';
    if (!is_dir($uploadPath)) @mkdir($uploadPath, 0755, true);
    if (!is_dir($originalUploadDir)) @mkdir($originalUploadDir, 0755, true);
    if (!is_dir($thumbnailUploadDir)) @mkdir($thumbnailUploadDir, 0755, true);

    $validExtensions = ['png', 'gif', 'jpg', 'jpeg', 'webp'];

    // Handle Room Feature Image Upload
    $newFeatureImg = $oldFeatureImg;
    if (!empty($_FILES['room_feature_image']['tmp_name']) && $_FILES['room_feature_image']['error'] === UPLOAD_ERR_OK) {
        $rawFilename = strtolower(str_replace("'", "_", str_replace('-', '_', str_replace(' ', '_', $_FILES['room_feature_image']['name']))));
        $ext = strtolower(pathinfo($rawFilename, PATHINFO_EXTENSION));
        if (in_array($ext, $validExtensions, true)) {
            $uniquePrefix = time() . '_' . bin2hex(random_bytes(5));
            $cleanName = strtolower(preg_replace('~[^\p{L}\p{N}\._-]+~u', '', $rawFilename));
            $destFile = $uploadPath . $uniquePrefix . '_' . $cleanName;
            $origFile = $originalUploadDir . $uniquePrefix . '_' . $cleanName;
            if (move_uploaded_file($_FILES['room_feature_image']['tmp_name'], $destFile)) {
                @copy($destFile, $origFile);
                $newFeatureImg = str_replace($rootPath, '', $destFile);
            }
        }
    } elseif (empty($newFeatureImg) && !empty($_POST['inherited_feature_image'])) {
        $newFeatureImg = trim($_POST['inherited_feature_image']);
    }

    // Handle Room Gallery (kept old + new uploads)
    $newGallery = $oldGallery;
    if (isset($_POST['room_kept_gallery'])) {
        $kept = json_decode($_POST['room_kept_gallery'], true);
        if (is_array($kept)) {
            $newGallery = [];
            foreach ($kept as $item) {
                if (is_array($item) && !empty($item['path'])) {
                    $newGallery[] = $item;
                } elseif (is_string($item) && !empty($item)) {
                    $newGallery[] = ['name' => basename($item), 'path' => $item, 'type' => pathinfo($item, PATHINFO_EXTENSION)];
                }
            }
        }
    }

    // Multi-file upload for room gallery
    if (!empty($_FILES['room_gallery']) && !empty($_FILES['room_gallery']['name'])) {
        $files = $_FILES['room_gallery'];
        $count = is_array($files['name']) ? count($files['name']) : 1;
        for ($i = 0; $i < $count; $i++) {
            $tmpName  = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
            $origName = is_array($files['name'])     ? $files['name'][$i]     : $files['name'];
            $err      = is_array($files['error'])    ? $files['error'][$i]    : $files['error'];
            if ($err !== UPLOAD_ERR_OK || empty($tmpName)) continue;

            $rawFilename = strtolower(str_replace("'", "_", str_replace('-', '_', str_replace(' ', '_', $origName))));
            $ext = strtolower(pathinfo($rawFilename, PATHINFO_EXTENSION));
            if (!in_array($ext, $validExtensions, true)) continue;

            $uniquePrefix = time() . '_' . bin2hex(random_bytes(5));
            $cleanName = strtolower(preg_replace('~[^\p{L}\p{N}\._-]+~u', '', $rawFilename));
            $destFile = $uploadPath . $uniquePrefix . '_' . $cleanName;
            $origFile = $originalUploadDir . $uniquePrefix . '_' . $cleanName;

            if (move_uploaded_file($tmpName, $destFile)) {
                @copy($destFile, $origFile);
                $webPath = str_replace($rootPath, '', $destFile);
                $newGallery[] = ['name' => $origName, 'path' => $webPath, 'type' => $ext];
            }
        }
    }

    // Auto-inherit master feature image & gallery from existing room type records if not set
    if (empty($newFeatureImg) || empty($newGallery)) {
        $typeForInherit = mysqli_real_escape_string($connection, $roomType);
        $fChk = mysqli_query($connection, "SELECT room_feature_image, room_gallery FROM {$prefix}room WHERE room_property_id = {$propId} AND room_type = '{$typeForInherit}' AND (room_feature_image != '' OR (room_gallery != '' AND room_gallery != '[]')) ORDER BY room_id DESC LIMIT 1");
        if ($fChk && ($fRow = mysqli_fetch_assoc($fChk))) {
            if (empty($newFeatureImg) && !empty($fRow['room_feature_image'])) {
                $newFeatureImg = $fRow['room_feature_image'];
            }
            if (empty($newGallery) && !empty($fRow['room_gallery'])) {
                $gDec = json_decode($fRow['room_gallery'], true);
                if (is_array($gDec) && !empty($gDec)) {
                    $newGallery = $gDec;
                }
            }
        }
    }

    $galleryJson = mysqli_real_escape_string($connection, json_encode($newGallery));
    $descEsc     = mysqli_real_escape_string($connection, $description);
    $typeEsc     = mysqli_real_escape_string($connection, $roomType);
    $featImgEsc  = mysqli_real_escape_string($connection, $newFeatureImg);

    if ($roomId > 0) {
        // UPDATE
        $updateSql = "UPDATE {$prefix}room SET
            room_type          = '{$typeEsc}',
            room_price_from    = {$priceFromSql},
            room_price_to      = {$priceToSql},
            room_season_name   = '{$seasonName}',
            room_description   = '{$descEsc}',
            room_feature_image = '{$featImgEsc}',
            room_gallery       = '{$galleryJson}',
            room_ep_mrp        = {$epMrp},
            room_ep_b2c        = {$epB2c},
            room_ep_b2b        = {$epB2b},
            room_cp_mrp        = {$cpMrp},
            room_cp_b2c        = {$cpB2c},
            room_cp_b2b        = {$cpB2b},
            room_map_mrp       = {$mapMrp},
            room_map_b2c       = {$mapB2c},
            room_map_b2b       = {$mapB2b},
            room_ap_mrp        = {$apMrp},
            room_ap_b2c        = {$apB2c},
            room_ap_b2b        = {$apB2b},
            room_status        = {$status},
            room_update        = NOW()
        WHERE room_id = {$roomId} AND room_property_id = {$propId}";

        if (mysqli_query($connection, $updateSql)) {
            http_response_code(200);
            echo json_encode([
                'status'  => 'success',
                'msg'     => 'Room details updated successfully.',
                'room_id' => $roomId,
            ]);
            exit();
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => 'Failed to update room.', 'debug' => mysqli_error($connection)]);
            exit();
        }
    } else {
        // INSERT
        $insertSql = "INSERT INTO {$prefix}room (
            room_property_id, room_site_id, room_feature_image, room_gallery,
            room_description, room_type, room_price_from, room_price_to, room_season_name,
            room_ep_mrp, room_ep_b2c, room_ep_b2b,
            room_cp_mrp, room_cp_b2c, room_cp_b2b, room_map_mrp, room_map_b2c,
            room_map_b2b, room_ap_mrp, room_ap_b2c, room_ap_b2b, room_status,
            room_create, room_update
        ) VALUES (
            {$propId}, {$siteId}, '{$featImgEsc}', '{$galleryJson}',
            '{$descEsc}', '{$typeEsc}', {$priceFromSql}, {$priceToSql}, '{$seasonName}',
            {$epMrp}, {$epB2c}, {$epB2b},
            {$cpMrp}, {$cpB2c}, {$cpB2b}, {$mapMrp}, {$mapB2c},
            {$mapB2b}, {$apMrp}, {$apB2c}, {$apB2b}, {$status},
            NOW(), NOW()
        )";

        if (mysqli_query($connection, $insertSql)) {
            $newId = mysqli_insert_id($connection);
            http_response_code(201);
            echo json_encode([
                'status'  => 'success',
                'msg'     => 'New room added successfully.',
                'room_id' => $newId,
            ]);
            exit();
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => 'Failed to add new room.', 'debug' => mysqli_error($connection)]);
            exit();
        }
    }
}
