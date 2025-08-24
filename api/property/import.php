<?php
require_once LOGIN_CHECK;

header('Content-Type: application/json; charset=utf-8');

// avoid throwing exceptions so we can handle 1062 gracefully
if (function_exists('mysqli_report')) {
    @mysqli_report(MYSQLI_REPORT_OFF);
}

$out = [
    'status'  => 'error',
    'summary' => [
        'processed_rows' => 0,
        'properties' => ['created' => 0, 'updated' => 0, 'failed' => 0],
        'rooms'      => ['created' => 0, 'failed' => 0],
        'errors'     => [],
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

// helpers
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
    return (string)(0 + $s);
}

$rowNum = 0;
$skipRows = 2;
$maxRoomTypes = 7;
$roomBlockSz = 13;

mysqli_query($connection, "START TRANSACTION");

while (($row = fgetcsv($handle, 0, ',', '"', "\\")) !== false) {
    $rowNum++;
    if ($rowNum <= $skipRows) continue;
    if ((empty($row[0]) || trim($row[0]) === '') && (empty($row[2]) || trim($row[2]) === '') && (empty($row[3]) || trim($row[3]) === '')) continue;

    $out['summary']['processed_rows']++;

    // base fields
    $hotelName         = addslashes(trim($row[0]));
    $aboutProperty     = addslashes(trim($row[1]));
    $address           = addslashes(trim($row[2]));
    $city              = addslashes(trim($row[3]));
    $state             = addslashes(trim($row[4]));
    $pinCode           = addslashes(trim($row[5])); // may be empty ''
    $country           = addslashes(trim($row[6]));
    $reservationEmail  = addslashes(trim($row[7]));
    $reservationMobile = addslashes(trim($row[8]));
    $supportEmail      = addslashes(trim($row[9]));
    $supportMobile     = addslashes(trim($row[10]));
    $propertyType      = strtolower(str_replace(' ', '_', addslashes(trim($row[11]))));
    $googleMapUrl      = addslashes(trim($row[12]));

    $tblProp = TABLE_PREFIX . "property";
    $tblRoom = TABLE_PREFIX . "room";

    // --------- EXISTENCE CHECK: match ONLY the unique index (site_id, title, city, pincode) with TRIM/UPPER ----------
    $whereKeyUnique =
        "property_site_id=" . SITE_ID .
        " AND UPPER(TRIM(property_title))=UPPER(TRIM('{$hotelName}'))" .
        " AND UPPER(TRIM(property_city)) =UPPER(TRIM('{$city}'))" .
        " AND TRIM(property_pincode)     =TRIM('{$pinCode}')";

    $propertyId = 0;
    $existsRes  = mysqli_query($connection, "SELECT property_id FROM {$tblProp} WHERE {$whereKeyUnique} LIMIT 1");

    if ($existsRes && mysqli_num_rows($existsRes)) {
        // ---------------- UPDATE existing ----------------
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
        // ---------------- INSERT new; on 1062, fall back to UPDATE ----------------
        $sqlInsertProp = "INSERT INTO {$tblProp}(" .
            "property_site_id, property_thumbnail, property_gallery, property_title, property_email, property_mobile, " .
            "property_description, property_address, property_city, property_state, property_country, property_pincode, " .
            "property_map_url, property_starting_mrp, property_starting_price, property_total_rooms, property_type, " .
            "property_support_email, property_support_mobile, property_created_by, property_status" .
            ") VALUES(" .
            SITE_ID . ", '', '', '{$hotelName}', '{$reservationEmail}', '{$reservationMobile}', '{$aboutProperty}', '{$address}', '{$city}', '{$state}', '{$country}', '{$pinCode}', '{$googleMapUrl}', '0', '0', '0', '{$propertyType}', '{$supportEmail}', '{$supportMobile}', " . USER_ID . ", 1" .
            ")";

        $ok = mysqli_query($connection, $sqlInsertProp);
        if (!$ok) {
            if (mysqli_errno($connection) == 1062) {
                // fetch existing by unique key and update instead
                $res2 = mysqli_query($connection, "SELECT property_id FROM {$tblProp} WHERE {$whereKeyUnique} LIMIT 1");
                if ($res2 && mysqli_num_rows($res2)) {
                    $rowId = mysqli_fetch_assoc($res2);
                    $propertyId = (int)$rowId['property_id'];

                    $sqlUpdate2 = "UPDATE {$tblProp} SET " .
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

                    if (!mysqli_query($connection, $sqlUpdate2)) {
                        $out['summary']['properties']['failed']++;
                        $out['summary']['errors'][] = "Property update (after duplicate) failed ({$hotelName}): " . mysqli_error($connection);
                        continue;
                    } else {
                        $out['summary']['properties']['updated']++;
                    }
                } else {
                    $out['summary']['properties']['failed']++;
                    $out['summary']['errors'][] = "Duplicate found but existing row not retrievable ({$hotelName}).";
                    continue;
                }
            } else {
                $out['summary']['properties']['failed']++;
                $out['summary']['errors'][] = "Property insert failed ({$hotelName}): " . mysqli_error($connection);
                continue;
            }
        } else {
            $propertyId = mysqli_insert_id($connection);
            $out['summary']['properties']['created']++;
        }
    }

    // -------- Rooms: clear & insert --------
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

        // skip fully-empty block (all zeros treated as empty? if you truly want skip-only-NULL, adjust)
        $vals = [$ep_mrp, $ep_b2c, $ep_b2b, $cp_mrp, $cp_b2c, $cp_b2b, $map_mrp, $map_b2c, $map_b2b, $ap_mrp, $ap_b2c, $ap_b2b];
        $allZero = true;
        foreach ($vals as $v) {
            if ($v !== '0') {
                $allZero = false;
                break;
            }
        }
        if ($allZero) {
            $start += $roomBlockSz;
            continue;
        }

        $sqlRoom = "INSERT INTO {$tblRoom}(" .
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

        $start += $roomBlockSz;
    }
}

fclose($handle);
mysqli_query($connection, "COMMIT");

$out['status'] = 'success';
http_response_code(200);
echo json_encode($out, JSON_UNESCAPED_UNICODE);