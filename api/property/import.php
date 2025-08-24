<?php
require_once LOGIN_CHECK;

header('Content-Type: application/json; charset=utf-8');

$out = [
    'status'  => 'error',
    'summary' => [
        'processed_rows' => 0,
        'properties' => [
            'created' => 0,
            'updated' => 0,
            'failed'  => 0,
        ],
        'rooms' => [
            'created' => 0,
            'failed'  => 0,
        ],
        'errors' => [],
    ],
];

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    $out['summary']['errors'][] = 'CSV file is required.';
    echo json_encode($out, JSON_UNESCAPED_UNICODE);
    exit;
}

$fileTmp = $_FILES['file']['tmp_name'];
$handle  = @fopen($fileTmp, 'r');
if (!$handle) {
    http_response_code(400);
    $out['summary']['errors'][] = 'Unable to open uploaded CSV file.';
    echo json_encode($out, JSON_UNESCAPED_UNICODE);
    exit;
}

// --- helpers (no schema/column changes) ---
function esc($v)
{
    return str_replace("'", "\\'", (string)$v);
}
function s($v)
{
    return esc(trim((string)$v));
}
function num_or_null($v)
{
    if (!isset($v)) return '0';
    $s = preg_replace('/[^\d.\-]/', '', (string)$v);
    if ($s === '' || !is_numeric($s)) return '0';
    return (string)(0 + $s); // numeric literal
}
function nonempty($v)
{
    return isset($v) && trim((string)$v) !== '';
}

$rowNum       = 0;
$skipRows     = 2;        // skip headers
$plans        = ['EP', 'CP', 'MAP', 'AP']; // kept, though we insert per room-type with all plans
$maxRoomTypes = 7;
$roomBlockSz  = 13;       // [name, EP(3), CP(3), MAP(3), AP(3)]

mysqli_query($connection, "START TRANSACTION");

while (($row = fgetcsv($handle, 0, ',', '"', "\\")) !== false) {
    $rowNum++;
    if ($rowNum <= $skipRows) continue;

    // require at least hotel name OR city (your original guard used indices 0/2; you later mapped city at 3—support both)
    if ((empty($row[0]) || trim($row[0]) === '') && (empty($row[2]) || trim($row[2]) === '') && (empty($row[3]) || trim($row[3]) === '')) {
        continue;
    }

    $out['summary']['processed_rows']++;

    // --- base fields (keep your indices & addslashes semantics) ---
    $hotelName         = addslashes(trim($row[0]));
    $aboutProperty     = addslashes(trim($row[1]));
    $address           = addslashes(trim($row[2]));
    $city              = addslashes(trim($row[3]));
    $state             = addslashes(trim($row[4]));
    $pinCode           = addslashes(trim($row[5]));
    $country           = addslashes(trim($row[6]));
    $reservationEmail  = addslashes(trim($row[7]));
    $reservationMobile = addslashes(trim($row[8]));
    $supportEmail      = addslashes(trim($row[9]));
    $supportMobile     = addslashes(trim($row[10]));
    $propertyType      = strtolower(str_replace(' ', '_', addslashes(trim($row[11]))));
    $googleMapUrl      = addslashes(trim($row[12]));

    // --- find existing property (you asked to check by hotel name, site_id, city, pin_code; including state/country/type is safer) ---
    $tblProp = TABLE_PREFIX . "property";
    $tblRoom = TABLE_PREFIX . "room";

    $whereKey = "property_site_id=" . SITE_ID .
        " AND property_title='{$hotelName}'" .
        " AND property_city='{$city}'" .
        " AND property_pincode='{$pinCode}'" .
        " AND property_state='{$state}'" .
        " AND property_country='{$country}'" .
        " AND property_type='{$propertyType}'";

    $propertyId = 0;
    $existsRes  = mysqli_query($connection, "SELECT property_id FROM {$tblProp} WHERE {$whereKey} LIMIT 1");

    if ($existsRes && mysqli_num_rows($existsRes)) {
        // --- UPDATE existing (no column name changes) ---
        $rowId = mysqli_fetch_assoc($existsRes);
        $propertyId = (int)$rowId['property_id'];

        $sqlUpdate = "UPDATE {$tblProp} SET " .
            "property_email='{$reservationEmail}', " .
            "property_mobile='{$reservationMobile}', " .
            "property_description='{$aboutProperty}', " .
            "property_address='{$address}', " .
            "property_city='{$city}', " .
            "property_state='{$state}', " .
            "property_country='{$country}', " .
            "property_pincode='{$pinCode}', " .
            "property_map_url='{$googleMapUrl}', " .
            "property_type='{$propertyType}', " .
            "property_support_email='{$supportEmail}', " .
            "property_support_mobile='{$supportMobile}', " .
            "property_created_by=" . USER_ID . ", " .
            "property_status=1 " .
            "WHERE property_id={$propertyId} LIMIT 1";

        if (!mysqli_query($connection, $sqlUpdate)) {
            $out['summary']['properties']['failed']++;
            $out['summary']['errors'][] = "Property update failed ({$hotelName}): " . mysqli_error($connection);
            continue;
        } else {
            $out['summary']['properties']['updated']++;
        }
    } else {
        // --- INSERT new (your original INSERT kept as-is) ---
        $insertProperty = mysqli_query(
            $connection,
            "INSERT INTO " . TABLE_PREFIX . "property(" .
                "property_site_id, property_thumbnail, property_gallery, property_title, property_email, property_mobile, " .
                "property_description, property_address, property_city, property_state, property_country, property_pincode, " .
                "property_map_url, property_starting_mrp, property_starting_price, property_total_rooms, property_type, " .
                "property_support_email, property_support_mobile, property_created_by, property_status" .
                ") VALUES(" .
                SITE_ID . ", '', '', '{$hotelName}', '{$reservationEmail}', '{$reservationMobile}', '{$aboutProperty}', '{$address}', '{$city}', '{$state}', '{$country}', '{$pinCode}', '{$googleMapUrl}', '0', '0', '0', '{$propertyType}', '{$supportEmail}', '{$supportMobile}', " . USER_ID . ", 1)"
        );

        if (!$insertProperty) {
            $out['summary']['properties']['failed']++;
            $out['summary']['errors'][] = "Property insert failed ({$hotelName}): " . mysqli_error($connection);
            continue;
        }
        $propertyId = mysqli_insert_id($connection);
        $out['summary']['properties']['created']++;
    }

    // --- Rooms: clear old and insert fresh (your column names kept) ---
    mysqli_query($connection, "DELETE FROM {$tblRoom} WHERE room_property_id={$propertyId}");

    $start = 14;

    for ($i = 1; $i <= $maxRoomTypes; $i++) {
        $roomTypeName = isset($row[$start]) ? trim($row[$start]) : '';
        if ($roomTypeName === '') {
            $start += $roomBlockSz;
            continue;
        }

        $roomTypeName = strtolower(str_replace(' ', '_', addslashes($roomTypeName)));
        $c = $start + 1;

        // EP/CP/MAP/AP × (mrp, b2c, b2b) — 12 numbers
        $ep_mrp  = num_or_null($row[$c++] ?? 0);
        $ep_b2c  = num_or_null($row[$c++] ?? 0);
        $ep_b2b  = num_or_null($row[$c++] ?? 0);

        $cp_mrp  = num_or_null($row[$c++] ?? 0);
        $cp_b2c  = num_or_null($row[$c++] ?? 0);
        $cp_b2b  = num_or_null($row[$c++] ?? 0);

        $map_mrp = num_or_null($row[$c++] ?? 0);
        $map_b2c = num_or_null($row[$c++] ?? 0);
        $map_b2b = num_or_null($row[$c++] ?? 0);

        $ap_mrp  = num_or_null($row[$c++] ?? 0);
        $ap_b2c  = num_or_null($row[$c++] ?? 0);
        $ap_b2b  = num_or_null($row[$c++] ?? 0);

        // skip if the whole block is empty
        $vals = [$ep_mrp, $ep_b2c, $ep_b2b, $cp_mrp, $cp_b2c, $cp_b2b, $map_mrp, $map_b2c, $map_b2b, $ap_mrp, $ap_b2c, $ap_b2b];
        $allNull = true;
        foreach ($vals as $v) {
            if ($v !== 'NULL') {
                $allNull = false;
                break;
            }
        }
        if ($allNull) {
            $start += $roomBlockSz;
            continue;
        }

        // one insert per room-type (keeps your column order)
        $sqlRoom = "INSERT INTO " . TABLE_PREFIX . "room(" .
            "room_property_id, room_site_id, room_feature_image, room_gallery, room_description, room_type, " .
            "room_ep_mrp, room_ep_b2c, room_ep_b2b, " .
            "room_cp_mrp, room_cp_b2c, room_cp_b2b, " .
            "room_map_mrp, room_map_b2c, room_map_b2b, " .
            "room_ap_mrp, room_ap_b2c, room_ap_b2b, " .
            "room_status, room_create" .
            ") VALUES(" .
            $propertyId . ", " . SITE_ID . ", " .
            "'' , '' , '' , '{$roomTypeName}', " .
            "{$ep_mrp}, {$ep_b2c}, {$ep_b2b}, " .
            "{$cp_mrp}, {$cp_b2c}, {$cp_b2b}, " .
            "{$map_mrp}, {$map_b2c}, {$map_b2b}, " .
            "{$ap_mrp}, {$ap_b2c}, {$ap_b2b}, " .
            "1, NOW()" .
            ")";

        if (!mysqli_query($connection, $sqlRoom)) {
            $out['summary']['rooms']['failed']++;
            $out['summary']['errors'][] = "Room insert failed ({$hotelName} / {$roomTypeName}): " . mysqli_error($connection);
        } else {
            $out['summary']['rooms']['created']++;
        }

        $start += $roomBlockSz; // move to next block of 13 cols
    }
}

fclose($handle);
mysqli_query($connection, "COMMIT");

$out['status'] = 'success';
http_response_code(200);
echo json_encode($out, JSON_UNESCAPED_UNICODE);