<?php

/**
 * V3 Master Data Copy / Clone API
 * Path: /api/v3/master/copy/
 * Clones master tour packages, destinations, vehicles, hotels, and transporters into the client workspace.
 */

header('Content-Type: application/json; charset=utf-8');

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
}

/** @var mysqli $connection */
if (!($connection instanceof mysqli)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Database connection is unavailable.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'msg' => 'Method not allowed. Use POST.']);
    exit;
}

if (!defined('LOGIN_CHECK')) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/lib/modules/isLogin.php')) {
        define('LOGIN_CHECK', $docRoot . '/lib/modules/isLogin.php');
    } elseif (file_exists($docRoot . '/lib/session/index.php')) {
        define('LOGIN_CHECK', $docRoot . '/lib/session/index.php');
    }
}
if (defined('LOGIN_CHECK') && file_exists(LOGIN_CHECK)) {
    @require_once LOGIN_CHECK;
}

$userRole = strtolower(trim(isset($site_access_type) ? (string)$site_access_type : (defined('USER_TYPE') ? (string)USER_TYPE : '')));
$isAdmin = (isset($site_access_type) && in_array($site_access_type, ['administrator', 'admin'], true)) || in_array($userRole, ['administrator', 'admin', 'superadmin'], true);

if (php_sapi_name() !== 'cli' && !$isAdmin) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'msg' => 'Access denied. Administrator privileges required.']);
    exit;
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId    = defined('SITE_ID') ? (int)SITE_ID : (int)($_SESSION['site_id'] ?? 0);
$userId    = defined('USER_ID') ? (int)USER_ID : (int)($_SESSION['user_id'] ?? 0);

// Resolve site ID from session/domain if not defined
if ($siteId <= 0 && !empty($_SESSION['site_id'])) {
    $siteId = (int)$_SESSION['site_id'];
}
if ($siteId <= 0 && !empty($_SERVER['HTTP_HOST'])) {
    $host = mysqli_real_escape_string($connection, strtolower(trim($_SERVER['HTTP_HOST'])));
    $siteQ = mysqli_query($connection, "SELECT site_id FROM {$tblPrefix}site WHERE LOWER(site_domain) = '{$host}' OR LOWER(site_url) = '{$host}' LIMIT 1");
    if ($siteQ && ($sRow = mysqli_fetch_assoc($siteQ))) {
        $siteId = (int)$sRow['site_id'];
    }
}

if ($siteId <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Invalid or missing target workspace Site ID.']);
    exit;
}

if ($userId <= 0 && !empty($_SESSION['user_id'])) {
    $userId = (int)$_SESSION['user_id'];
}
if ($userId <= 0) {
    $userId = 1;
}

// Ensure master schema exists
require_once __DIR__ . '/schema.php';
ensureMasterDataTables($connection, $tblPrefix, false);

// Parse input (supports JSON or Form POST)
$rawBody = file_get_contents('php://input');
$jsonInput = json_decode($rawBody, true);
$input = is_array($jsonInput) ? $jsonInput : $_POST;

$migrationMode = strtolower(trim($input['migration_mode'] ?? ''));
$skipExistingParam = $input['skip_existing'] ?? 1;

if ($migrationMode === 'overwrite') {
    $skipExisting = 0;
} elseif ($migrationMode === 'safe') {
    $skipExisting = 1;
} else {
    $skipExisting = ($skipExistingParam === 0 || $skipExistingParam === '0' || $skipExistingParam === false) ? 0 : 1;
}

$rawTypes = $input['data_types'] ?? ($input['types'] ?? 'all');
if (is_string($rawTypes)) {
    if ($rawTypes === 'all') {
        $types = ['tour_package', 'travel_destinations', 'vehicles', 'hotels', 'transporter'];
    } else {
        $types = array_values(array_filter(array_map('trim', explode(',', $rawTypes))));
    }
} elseif (is_array($rawTypes)) {
    $types = $rawTypes;
} else {
    $types = ['tour_package', 'travel_destinations', 'vehicles', 'hotels', 'transporter'];
}

// Normalize type names
$selectedTypes = [];
foreach ($types as $t) {
    $tNorm = strtolower(trim($t));
    if (in_array($tNorm, ['tour_package', 'tour_packages', 'packages', 'package'], true)) {
        $selectedTypes['tour_package'] = true;
    }
    if (in_array($tNorm, ['travel_destinations', 'destinations', 'locations', 'destination'], true)) {
        $selectedTypes['travel_destinations'] = true;
    }
    if (in_array($tNorm, ['vehicles', 'vehicle', 'fleet'], true)) {
        $selectedTypes['vehicles'] = true;
    }
    if (in_array($tNorm, ['hotels', 'hotel', 'property', 'properties'], true)) {
        $selectedTypes['hotels'] = true;
    }
    if (in_array($tNorm, ['transporter', 'transporters'], true)) {
        $selectedTypes['transporter'] = true;
    }
}

// Destination filters for tour packages
$destFilter = $input['destinations'] ?? [];
if (is_string($destFilter)) {
    $destFilter = array_values(array_filter(array_map('trim', explode(',', $destFilter))));
}
$destCleanMap = [];
if (!empty($destFilter)) {
    foreach ($destFilter as $df) {
        $cleanDf = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$df));
        if (!empty($cleanDf) && $cleanDf !== 'all') {
            $destCleanMap[$cleanDf] = true;
        }
    }
}

$copiedCounts = [
    'tour_packages'       => 0,
    'tour_package_prices' => 0,
    'tour_package_hotels' => 0,
    'travel_destinations' => 0,
    'vehicles'            => 0,
    'hotels'              => 0,
    'rooms'               => 0,
    'transporters'        => 0,
    'skipped'             => [
        'tour_packages'       => 0,
        'hotels'              => 0,
        'transporters'        => 0,
        'vehicles'            => 0,
        'travel_destinations' => 0
    ]
];

// =========================================================================
// 1. COPY MASTER HOTELS & ROOMS
// =========================================================================
$masterHotelToSiteHotelMap = [];

if (isset($selectedTypes['hotels'])) {
    // Check existing hotels for this site
    $existingHotels = [];
    $eHQuery = mysqli_query($connection, "SELECT LOWER(TRIM(property_title)) as t, LOWER(TRIM(COALESCE(property_city, ''))) as c, property_id FROM `{$tblPrefix}property` WHERE property_site_id = {$siteId}");
    if ($eHQuery) {
        while ($er = mysqli_fetch_assoc($eHQuery)) {
            $key = $er['t'] . '|' . $er['c'];
            $existingHotels[$key] = (int)$er['property_id'];
        }
    }

    $mHotelsQ = mysqli_query($connection, "SELECT * FROM `{$tblPrefix}master_hotels` WHERE property_status = 1 OR property_status IS NULL");
    if ($mHotelsQ) {
        while ($mH = mysqli_fetch_assoc($mHotelsQ)) {
            $mId = (int)$mH['property_id'];
            $cleanTitle = strtolower(trim($mH['property_title'] ?? ''));
            $cleanCity  = strtolower(trim($mH['property_city'] ?? ''));
            $key = $cleanTitle . '|' . $cleanCity;

            $thumb = mysqli_real_escape_string($connection, $mH['property_thumbnail'] ?? '');
            $gall  = mysqli_real_escape_string($connection, $mH['property_gallery'] ?? '');
            $title = mysqli_real_escape_string($connection, $mH['property_title'] ?? '');
            $email = mysqli_real_escape_string($connection, $mH['property_email'] ?? '');
            $mobile = mysqli_real_escape_string($connection, $mH['property_mobile'] ?? '');
            $desc  = mysqli_real_escape_string($connection, $mH['property_description'] ?? '');
            $addr  = mysqli_real_escape_string($connection, $mH['property_address'] ?? '');
            $city  = mysqli_real_escape_string($connection, $mH['property_city'] ?? '');
            $state = mysqli_real_escape_string($connection, $mH['property_state'] ?? '');
            $country = mysqli_real_escape_string($connection, $mH['property_country'] ?? '');
            $pin   = mysqli_real_escape_string($connection, $mH['property_pincode'] ?? '');
            $mapUrl = mysqli_real_escape_string($connection, $mH['property_map_url'] ?? '');
            $mrp   = (int)($mH['property_starting_mrp'] ?? 0);
            $price = (int)($mH['property_starting_price'] ?? 0);
            $rooms = (int)($mH['property_total_rooms'] ?? 0);
            $type  = mysqli_real_escape_string($connection, $mH['property_type'] ?? '3_star_hotel');
            $sEmail = mysqli_real_escape_string($connection, $mH['property_support_email'] ?? '');
            $sMob  = mysqli_real_escape_string($connection, $mH['property_support_mobile'] ?? '');

            if (isset($existingHotels[$key])) {
                $siteHotelId = $existingHotels[$key];
                $masterHotelToSiteHotelMap[$mId] = $siteHotelId;

                if ($skipExisting == 1) {
                    $copiedCounts['skipped']['hotels']++;
                    continue;
                } else {
                    // Overwrite mode: Update existing property
                    mysqli_query($connection, "
                        UPDATE `{$tblPrefix}property` SET
                            property_thumbnail = '{$thumb}',
                            property_gallery = '{$gall}',
                            property_starting_mrp = {$mrp},
                            property_starting_price = {$price},
                            property_total_rooms = {$rooms},
                            property_description = '{$desc}',
                            property_address = '{$addr}',
                            property_type = '{$type}',
                            property_status = 1
                        WHERE property_id = {$siteHotelId} AND property_site_id = {$siteId}
                    ");
                    $copiedCounts['hotels']++;
                    $targetHotelId = $siteHotelId;
                }
            } else {
                // Insert into property
                $inPropSql = "
                    INSERT INTO `{$tblPrefix}property` (
                        property_site_id, property_thumbnail, property_gallery, property_title,
                        property_email, property_mobile, property_description, property_address,
                        property_city, property_state, property_country, property_pincode,
                        property_map_url, property_starting_mrp, property_starting_price, property_total_rooms,
                        property_type, property_support_email, property_support_mobile, property_created_by,
                        property_time, property_status
                    ) VALUES (
                        {$siteId}, '{$thumb}', '{$gall}', '{$title}',
                        '{$email}', '{$mobile}', '{$desc}', '{$addr}',
                        '{$city}', '{$state}', '{$country}', '{$pin}',
                        '{$mapUrl}', {$mrp}, {$price}, {$rooms},
                        '{$type}', '{$sEmail}', '{$sMob}', {$userId},
                        NOW(), 1
                    )
                ";

                if (mysqli_query($connection, $inPropSql)) {
                    $newHotelId = (int)mysqli_insert_id($connection);
                    $existingHotels[$key] = $newHotelId;
                    $masterHotelToSiteHotelMap[$mId] = $newHotelId;
                    $copiedCounts['hotels']++;
                    $targetHotelId = $newHotelId;
                } else {
                    continue;
                }
            }

            // Copy / Refresh Rooms for this hotel
            $mRoomsQ = mysqli_query($connection, "SELECT * FROM `{$tblPrefix}master_rooms` WHERE room_property_id = {$mId}");
            if ($mRoomsQ && mysqli_num_rows($mRoomsQ) > 0) {
                while ($mR = mysqli_fetch_assoc($mRoomsQ)) {
                    $rfImg = mysqli_real_escape_string($connection, $mR['room_feature_image'] ?? '');
                    $rGall = mysqli_real_escape_string($connection, $mR['room_gallery'] ?? '');
                    $rDesc = mysqli_real_escape_string($connection, $mR['room_description'] ?? '');
                    $rType = mysqli_real_escape_string($connection, $mR['room_type'] ?? 'deluxe');
                    $epMrp = (int)($mR['room_ep_mrp'] ?? 0);
                    $epB2c = (int)($mR['room_ep_b2c'] ?? 0);
                    $epB2b = (int)($mR['room_ep_b2b'] ?? 0);
                    $cpMrp = (int)($mR['room_cp_mrp'] ?? 0);
                    $cpB2c = (int)($mR['room_cp_b2c'] ?? 0);
                    $cpB2b = (int)($mR['room_cp_b2b'] ?? 0);
                    $mapMrp = (int)($mR['room_map_mrp'] ?? 0);
                    $mapB2c = (int)($mR['room_map_b2c'] ?? 0);
                    $mapB2b = (int)($mR['room_map_b2b'] ?? 0);
                    $apMrp = (int)($mR['room_ap_mrp'] ?? 0);
                    $apB2c = (int)($mR['room_ap_b2c'] ?? 0);
                    $apB2b = (int)($mR['room_ap_b2b'] ?? 0);

                    // If hotel already existed and in overwrite mode, check if room type exists
                    $existingRoomId = 0;
                    if (isset($existingHotels[$key]) && $skipExisting == 0) {
                        $chkRoomQ = mysqli_query($connection, "SELECT room_id FROM `{$tblPrefix}room` WHERE room_property_id = {$targetHotelId} AND room_site_id = {$siteId} AND room_type = '{$rType}' LIMIT 1");
                        if ($chkRoomQ && ($rRow = mysqli_fetch_assoc($chkRoomQ))) {
                            $existingRoomId = (int)$rRow['room_id'];
                        }
                    }

                    if ($existingRoomId > 0) {
                        mysqli_query($connection, "
                            UPDATE `{$tblPrefix}room` SET
                                room_feature_image = '{$rfImg}', room_gallery = '{$rGall}',
                                room_description = '{$rDesc}', room_ep_mrp = {$epMrp}, room_ep_b2c = {$epB2c},
                                room_ep_b2b = {$epB2b}, room_cp_mrp = {$cpMrp}, room_cp_b2c = {$cpB2c},
                                room_cp_b2b = {$cpB2b}, room_map_mrp = {$mapMrp}, room_map_b2c = {$mapB2c},
                                room_map_b2b = {$mapB2b}, room_ap_mrp = {$apMrp}, room_ap_b2b = {$apB2b},
                                room_ap_b2c = {$apB2c}, room_status = 1, room_update = NOW()
                            WHERE room_id = {$existingRoomId}
                        ");
                        $copiedCounts['rooms']++;
                    } else {
                        $inRoomSql = "
                            INSERT INTO `{$tblPrefix}room` (
                                room_property_id, room_site_id, room_feature_image, room_gallery,
                                room_description, room_type, room_ep_mrp, room_ep_b2c, room_ep_b2b,
                                room_cp_mrp, room_cp_b2c, room_cp_b2b, room_map_mrp, room_map_b2c,
                                room_map_b2b, room_ap_mrp, room_ap_b2b, room_ap_b2c, room_status,
                                room_create, room_update
                            ) VALUES (
                                {$targetHotelId}, {$siteId}, '{$rfImg}', '{$rGall}',
                                '{$rDesc}', '{$rType}', {$epMrp}, {$epB2c}, {$epB2b},
                                {$cpMrp}, {$cpB2c}, {$cpB2b}, {$mapMrp}, {$mapB2c},
                                {$mapB2b}, {$apMrp}, {$apB2b}, {$apB2c}, 1,
                                NOW(), NOW()
                            )
                        ";
                        if (mysqli_query($connection, $inRoomSql)) {
                            $copiedCounts['rooms']++;
                        }
                    }
                }
            }
        }
    }
}

// =========================================================================
// 2. COPY MASTER TOUR PACKAGES, PRICES, HOTELS & SEO LINKS
// =========================================================================
if (isset($selectedTypes['tour_package'])) {
    // Check existing packages on this site
    $existingPackages = [];
    $ePQuery = mysqli_query($connection, "SELECT LOWER(TRIM(tour_package_title)) as t, tour_package_id FROM `{$tblPrefix}tour_package` WHERE tour_package_site_id = {$siteId}");
    if ($ePQuery) {
        while ($ep = mysqli_fetch_assoc($ePQuery)) {
            $existingPackages[$ep['t']] = (int)$ep['tour_package_id'];
        }
    }

    $mPkgQuery = mysqli_query($connection, "SELECT * FROM `{$tblPrefix}master_tour_package` WHERE tour_package_status = 1 OR tour_package_status IS NULL");
    if ($mPkgQuery) {
        while ($pkg = mysqli_fetch_assoc($mPkgQuery)) {
            $mPkgId = (int)$pkg['tour_package_id'];
            $cleanTitle = strtolower(trim($pkg['tour_package_title'] ?? ''));

            if (empty($cleanTitle)) continue;

            // Apply destination filter if specified
            if (!empty($destCleanMap)) {
                $cityClean  = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $pkg['tour_package_city'] ?? ''));
                $stateClean = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $pkg['tour_package_state'] ?? ''));
                $titleClean = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $cleanTitle));

                $matchedDest = false;
                foreach (array_keys($destCleanMap) as $dfKey) {
                    if (strpos($cityClean, $dfKey) !== false || strpos($stateClean, $dfKey) !== false || strpos($titleClean, $dfKey) !== false) {
                        $matchedDest = true;
                        break;
                    }
                }
                if (!$matchedDest) {
                    continue;
                }
            }

            // Extract and sanitize package fields for both update and insert
            $img    = mysqli_real_escape_string($connection, $pkg['tour_package_image'] ?? '');
            $title  = mysqli_real_escape_string($connection, $pkg['tour_package_title'] ?? '');
            $theme  = mysqli_real_escape_string($connection, $pkg['tour_package_theme'] ?? 'standard');
            $tmpl   = mysqli_real_escape_string($connection, $pkg['tour_package_template'] ?? 'mit');
            $start  = mysqli_real_escape_string($connection, $pkg['tour_package_start_from'] ?? '');
            $end    = mysqli_real_escape_string($connection, $pkg['tour_package_end_to'] ?? '');
            $city   = mysqli_real_escape_string($connection, $pkg['tour_package_city'] ?? '');
            $dur    = (int)($pkg['tour_package_duration'] ?? 1);
            $over   = mysqli_real_escape_string($connection, $pkg['tour_package_overview'] ?? '');
            $itin   = mysqli_real_escape_string($connection, $pkg['tour_package_itinerary'] ?? '');
            $inc    = mysqli_real_escape_string($connection, $pkg['tour_package_inclusions'] ?? '');
            $exc    = mysqli_real_escape_string($connection, $pkg['tour_package_exclusions'] ?? '');
            $price  = mysqli_real_escape_string($connection, $pkg['tour_package_price'] ?? '');
            $hotel  = mysqli_real_escape_string($connection, $pkg['tour_package_hotel'] ?? '');
            $state  = mysqli_real_escape_string($connection, $pkg['tour_package_state'] ?? '');
            $cntry  = mysqli_real_escape_string($connection, $pkg['tour_package_country'] ?? '');
            $type   = mysqli_real_escape_string($connection, $pkg['tour_package_type'] ?? 'domestic');
            $outVeh = mysqli_real_escape_string($connection, $pkg['tour_package_outstation_vehicle'] ?? '');
            $locVeh = mysqli_real_escape_string($connection, $pkg['tour_package_localsightseen_vehicle'] ?? '');

            if (isset($existingPackages[$cleanTitle])) {
                if ($skipExisting == 1) {
                    $copiedCounts['skipped']['tour_packages']++;
                    continue;
                } else {
                    // Overwrite / Refresh existing package
                    $sitePkgId = $existingPackages[$cleanTitle];
                    if ($sitePkgId > 0) {
                        mysqli_query($connection, "
                            UPDATE `{$tblPrefix}tour_package` SET
                                tour_package_image = '{$img}',
                                tour_package_theme = '{$theme}',
                                tour_package_template = '{$tmpl}',
                                tour_package_start_from = '{$start}',
                                tour_package_end_to = '{$end}',
                                tour_package_city = '{$city}',
                                tour_package_state = '{$state}',
                                tour_package_country = '{$cntry}',
                                tour_package_type = '{$type}',
                                tour_package_duration = {$dur},
                                tour_package_overview = '{$over}',
                                tour_package_itinerary = '{$itin}',
                                tour_package_inclusions = '{$inc}',
                                tour_package_exclusions = '{$exc}',
                                tour_package_price = '{$price}',
                                tour_package_hotel = '{$hotel}',
                                tour_package_outstation_vehicle = '{$outVeh}',
                                tour_package_localsightseen_vehicle = '{$locVeh}',
                                tour_package_status = 1,
                                tour_package_updated_at = NOW()
                            WHERE tour_package_id = {$sitePkgId} AND tour_package_site_id = {$siteId}
                        ");
                        $newPkgId = $sitePkgId;
                        $copiedCounts['tour_packages']++;

                        // Refresh prices
                        mysqli_query($connection, "DELETE FROM `{$tblPrefix}tour_package_price` WHERE tour_package_price_parent_id = {$newPkgId} AND tour_package_price_site_id = {$siteId}");
                        $inPriceSql = "
                            INSERT INTO `{$tblPrefix}tour_package_price` (
                                tour_package_price_parent_id, tour_package_price_site_id, tour_package_price_user_id,
                                tour_package_price_date_start, tour_package_price_date_end, tour_package_price_net_adult,
                                tour_package_price_net_child, tour_package_price_mrp_adult, tour_package_price_mrp_child,
                                tour_package_price_selling_adult, tour_package_price_selling_child, tour_package_price_agent_adult,
                                tour_package_price_agent_child, tour_package_price_type, tour_package_price_status,
                                tour_package_price_created_at, tour_package_price_updated_at
                            )
                            SELECT 
                                {$newPkgId}, {$siteId}, {$userId},
                                tour_package_price_date_start, tour_package_price_date_end, tour_package_price_net_adult,
                                tour_package_price_net_child, tour_package_price_mrp_adult, tour_package_price_mrp_child,
                                tour_package_price_selling_adult, tour_package_price_selling_child, tour_package_price_agent_adult,
                                tour_package_price_agent_child, tour_package_price_type, 1,
                                NOW(), NOW()
                            FROM `{$tblPrefix}master_tour_package_price`
                            WHERE tour_package_price_parent_id = {$mPkgId}
                        ";
                        if (mysqli_query($connection, $inPriceSql)) {
                            $copiedCounts['tour_package_prices'] += (int)mysqli_affected_rows($connection);
                        }

                        // Refresh hotel nights
                        mysqli_query($connection, "DELETE FROM `{$tblPrefix}tour_package_hotel` WHERE tph_package_id = {$newPkgId} AND tph_site_id = {$siteId}");
                        $mPkgHotelQ = mysqli_query($connection, "SELECT * FROM `{$tblPrefix}master_tour_package_hotel` WHERE tph_package_id = {$mPkgId}");
                        if ($mPkgHotelQ) {
                            while ($mph = mysqli_fetch_assoc($mPkgHotelQ)) {
                                $origHotelId = (int)$mph['tph_hotel_id'];
                                $targetHotelId = $masterHotelToSiteHotelMap[$origHotelId] ?? $origHotelId;
                                $rtId = (int)$mph['tph_room_type_id'];
                                $rType = mysqli_real_escape_string($connection, $mph['tph_room_type'] ?? 'deluxe');
                                $night = (int)$mph['tph_night'];

                                mysqli_query($connection, "
                                    INSERT INTO `{$tblPrefix}tour_package_hotel` (
                                        tph_hotel_id, tph_package_id, tph_room_type_id, tph_room_type, tph_night, tph_site_id, tph_status
                                    ) VALUES (
                                        {$targetHotelId}, {$newPkgId}, {$rtId}, '{$rType}', {$night}, {$siteId}, 1
                                    )
                                ");
                                $copiedCounts['tour_package_hotels']++;
                            }
                        }
                        continue;
                    }
                }
            }

            $inPkgSql = "
                INSERT INTO `{$tblPrefix}tour_package` (
                    tour_package_site_id, tour_package_created_by, tour_package_image, tour_package_title,
                    tour_package_theme, tour_package_template, tour_package_start_from, tour_package_end_to,
                    tour_package_city, tour_package_duration, tour_package_overview, tour_package_itinerary,
                    tour_package_inclusions, tour_package_exclusions, tour_package_price, tour_package_hotel,
                    tour_package_state, tour_package_country, tour_package_type, tour_package_outstation_vehicle,
                    tour_package_localsightseen_vehicle, tour_package_status, tour_package_created_at, tour_package_updated_at
                ) VALUES (
                    {$siteId}, {$userId}, '{$img}', '{$title}',
                    '{$theme}', '{$tmpl}', '{$start}', '{$end}',
                    '{$city}', {$dur}, '{$over}', '{$itin}',
                    '{$inc}', '{$exc}', '{$price}', '{$hotel}',
                    '{$state}', '{$cntry}', '{$type}', '{$outVeh}',
                    '{$locVeh}', 1, NOW(), NOW()
                )
            ";

            if (mysqli_query($connection, $inPkgSql)) {
                $newPkgId = (int)mysqli_insert_id($connection);
                $existingPackages[$cleanTitle] = true;
                $copiedCounts['tour_packages']++;

                // Clone price rows
                $inPriceSql = "
                    INSERT INTO `{$tblPrefix}tour_package_price` (
                        tour_package_price_parent_id, tour_package_price_site_id, tour_package_price_user_id,
                        tour_package_price_date_start, tour_package_price_date_end, tour_package_price_net_adult,
                        tour_package_price_net_child, tour_package_price_mrp_adult, tour_package_price_mrp_child,
                        tour_package_price_selling_adult, tour_package_price_selling_child, tour_package_price_agent_adult,
                        tour_package_price_agent_child, tour_package_price_type, tour_package_price_status,
                        tour_package_price_created_at, tour_package_price_updated_at
                    )
                    SELECT 
                        {$newPkgId}, {$siteId}, {$userId},
                        tour_package_price_date_start, tour_package_price_date_end, tour_package_price_net_adult,
                        tour_package_price_net_child, tour_package_price_mrp_adult, tour_package_price_mrp_child,
                        tour_package_price_selling_adult, tour_package_price_selling_child, tour_package_price_agent_adult,
                        tour_package_price_agent_child, tour_package_price_type, 1,
                        NOW(), NOW()
                    FROM `{$tblPrefix}master_tour_package_price`
                    WHERE tour_package_price_parent_id = {$mPkgId}
                ";
                if (mysqli_query($connection, $inPriceSql)) {
                    $copiedCounts['tour_package_prices'] += (int)mysqli_affected_rows($connection);
                }

                // Clone hotel night assignments
                $mPkgHotelQ = mysqli_query($connection, "SELECT * FROM `{$tblPrefix}master_tour_package_hotel` WHERE tph_package_id = {$mPkgId}");
                if ($mPkgHotelQ) {
                    while ($mph = mysqli_fetch_assoc($mPkgHotelQ)) {
                        $origHotelId = (int)$mph['tph_hotel_id'];
                        $targetHotelId = $masterHotelToSiteHotelMap[$origHotelId] ?? $origHotelId;
                        $rtId = (int)$mph['tph_room_type_id'];
                        $rType = mysqli_real_escape_string($connection, $mph['tph_room_type'] ?? 'deluxe');
                        $night = (int)$mph['tph_night'];

                        mysqli_query($connection, "
                            INSERT INTO `{$tblPrefix}tour_package_hotel` (
                                tph_hotel_id, tph_package_id, tph_room_type_id, tph_room_type, tph_night, tph_site_id, tph_status
                            ) VALUES (
                                {$targetHotelId}, {$newPkgId}, {$rtId}, '{$rType}', {$night}, {$siteId}, 1
                            )
                        ");
                        $copiedCounts['tour_package_hotels']++;
                    }
                }

                // Create SEO Link in isw_link
                $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $pkg['tour_package_title'] ?? ''), '-'));
                if (empty($slug)) $slug = 'tour-package-' . $newPkgId;
                $chkLink = mysqli_query($connection, "SELECT link_id FROM `{$tblPrefix}link` WHERE link_url = '{$slug}' AND link_site_id = {$siteId} LIMIT 1");
                if ($chkLink && mysqli_num_rows($chkLink) > 0) {
                    $slug .= '-' . $newPkgId;
                }
                mysqli_query($connection, "
                    INSERT INTO `{$tblPrefix}link` (
                        link_url, link_relation_id, link_status, link_type, link_is_featured, link_index, link_owner_id, link_site_id, link_creation, link_published
                    ) VALUES (
                        '{$slug}', {$newPkgId}, 1, 'tour_package', 0, 'index, follow', {$userId}, {$siteId}, NOW(), NOW()
                    )
                ");
            }
        }
    }
}

// =========================================================================
// 3. COPY MASTER TRANSPORTERS
// =========================================================================
if (isset($selectedTypes['transporter'])) {
    $existingTrans = [];
    $eTQ = mysqli_query($connection, "SELECT LOWER(TRIM(transporter_title)) as t, transporter_id FROM `{$tblPrefix}transporter` WHERE transporter_site_id = {$siteId}");
    if ($eTQ) {
        while ($et = mysqli_fetch_assoc($eTQ)) {
            $existingTrans[$et['t']] = (int)$et['transporter_id'];
        }
    }

    $mTransQ = mysqli_query($connection, "SELECT * FROM `{$tblPrefix}master_transporters`");
    if ($mTransQ) {
        while ($mt = mysqli_fetch_assoc($mTransQ)) {
            $cleanTitle = strtolower(trim($mt['transporter_title'] ?? ''));
            if (empty($cleanTitle)) continue;

            $thumb = mysqli_real_escape_string($connection, $mt['transporter_thumbnail'] ?? '');
            $title = mysqli_real_escape_string($connection, $mt['transporter_title'] ?? '');
            $email = mysqli_real_escape_string($connection, $mt['transporter_email'] ?? '');
            $mobile = mysqli_real_escape_string($connection, $mt['transporter_mobile'] ?? '');
            $desc  = mysqli_real_escape_string($connection, $mt['transporter_description'] ?? '');
            $addr  = mysqli_real_escape_string($connection, $mt['transporter_address'] ?? '');
            $city  = mysqli_real_escape_string($connection, $mt['transporter_city'] ?? '');
            $pin   = mysqli_real_escape_string($connection, $mt['transporter_pincode'] ?? '');
            $sState = mysqli_real_escape_string($connection, $mt['transporter_service_state'] ?? '');

            if (isset($existingTrans[$cleanTitle])) {
                if ($skipExisting == 1) {
                    $copiedCounts['skipped']['transporters']++;
                    continue;
                } else {
                    $siteTransId = $existingTrans[$cleanTitle];
                    mysqli_query($connection, "
                        UPDATE `{$tblPrefix}transporter` SET
                            transporter_thumbnail = '{$thumb}',
                            transporter_email = '{$email}',
                            transporter_mobile = '{$mobile}',
                            transporter_description = '{$desc}',
                            transporter_address = '{$addr}',
                            transporter_city = '{$city}',
                            transporter_pincode = '{$pin}',
                            transporter_service_state = '{$sState}'
                        WHERE transporter_id = {$siteTransId} AND transporter_site_id = {$siteId}
                    ");
                    $copiedCounts['transporters']++;
                    continue;
                }
            }

            $inTransSql = "
                INSERT INTO `{$tblPrefix}transporter` (
                    transporter_site_id, transporter_thumbnail, transporter_title, transporter_email,
                    transporter_mobile, transporter_description, transporter_address, transporter_city,
                    transporter_pincode, transporter_service_state, transporter_created_by, transporter_time
                ) VALUES (
                    {$siteId}, '{$thumb}', '{$title}', '{$email}',
                    '{$mobile}', '{$desc}', '{$addr}', '{$city}',
                    '{$pin}', '{$sState}', {$userId}, NOW()
                )
            ";

            if (mysqli_query($connection, $inTransSql)) {
                $existingTrans[$cleanTitle] = (int)mysqli_insert_id($connection);
                $copiedCounts['transporters']++;
            }
        }
    }
}

// =========================================================================
// 4. ENSURE TRAVEL DESTINATIONS (Global Master)
// =========================================================================
if (isset($selectedTypes['travel_destinations'])) {
    $existingDests = [];
    $eDQ = mysqli_query($connection, "SELECT LOWER(TRIM(location_title)) as t, location_id FROM `{$tblPrefix}locations`");
    if ($eDQ) {
        while ($ed = mysqli_fetch_assoc($eDQ)) {
            $existingDests[$ed['t']] = (int)$ed['location_id'];
        }
    }

    $mDestQ = mysqli_query($connection, "SELECT * FROM `{$tblPrefix}master_travel_destinations`");
    if ($mDestQ) {
        while ($md = mysqli_fetch_assoc($mDestQ)) {
            $t = strtolower(trim($md['location_title'] ?? ''));
            if (!empty($t)) {
                $title = mysqli_real_escape_string($connection, $md['location_title']);
                $thumb = mysqli_real_escape_string($connection, $md['location_thumbnail'] ?? '');
                $gall  = mysqli_real_escape_string($connection, $md['location_gallery'] ?? '');
                $desc  = mysqli_real_escape_string($connection, $md['location_description'] ?? '');
                $state = mysqli_real_escape_string($connection, $md['location_state'] ?? '');
                $cntry = mysqli_real_escape_string($connection, $md['location_country'] ?? '');
                $bFrom = mysqli_real_escape_string($connection, $md['location_best_from'] ?? '');
                $bTo   = mysqli_real_escape_string($connection, $md['location_best_to'] ?? '');

                if (isset($existingDests[$t])) {
                    if ($migrationMode === 'overwrite') {
                        $exLocId = $existingDests[$t];
                        mysqli_query($connection, "
                            UPDATE `{$tblPrefix}locations` SET
                                location_thumbnail = '{$thumb}',
                                location_gallery = '{$gall}',
                                location_description = '{$desc}',
                                location_state = '{$state}',
                                location_country = '{$cntry}',
                                location_best_from = '{$bFrom}',
                                location_best_to = '{$bTo}'
                            WHERE location_id = {$exLocId}
                        ");
                        $copiedCounts['travel_destinations']++;
                    } else {
                        $copiedCounts['skipped']['travel_destinations']++;
                    }
                } else {
                    mysqli_query($connection, "
                        INSERT INTO `{$tblPrefix}locations` (
                            location_title, location_thumbnail, location_gallery, location_description,
                            location_is_deletable, location_state, location_country, location_best_from, location_best_to, location_time
                        ) VALUES (
                            '{$title}', '{$thumb}', '{$gall}', '{$desc}',
                            1, '{$state}', '{$cntry}', '{$bFrom}', '{$bTo}', NOW()
                        )
                    ");
                    $existingDests[$t] = (int)mysqli_insert_id($connection);
                    $copiedCounts['travel_destinations']++;
                }
            }
        }
    }
}

// =========================================================================
// 5. ENSURE VEHICLES (Workspace Scoped)
// =========================================================================
if (isset($selectedTypes['vehicles'])) {
    $existingVehs = [];
    $eVQ = mysqli_query($connection, "SELECT LOWER(TRIM(vehicle_title)) as t, vehicle_id FROM `{$tblPrefix}vehicles` WHERE vehicle_site_id = {$siteId}");
    if ($eVQ) {
        while ($ev = mysqli_fetch_assoc($eVQ)) {
            $existingVehs[$ev['t']] = (int)$ev['vehicle_id'];
        }
    }

    $mVehQ = mysqli_query($connection, "SELECT * FROM `{$tblPrefix}master_vehicles`");
    if ($mVehQ) {
        while ($mv = mysqli_fetch_assoc($mVehQ)) {
            $t = strtolower(trim($mv['vehicle_title'] ?? ''));
            if (!empty($t)) {
                $title = mysqli_real_escape_string($connection, $mv['vehicle_title']);
                $thumb = mysqli_real_escape_string($connection, $mv['vehicle_thumbnail'] ?? '');
                $desc  = mysqli_real_escape_string($connection, $mv['vehicle_description'] ?? '');
                $seat  = (int)($mv['vehicle_seating'] ?? 4);
                $type  = mysqli_real_escape_string($connection, $mv['vehicle_type'] ?? 'sedan');
                $gall  = mysqli_real_escape_string($connection, $mv['vehicle_gallery'] ?? '');
                $lug   = (int)($mv['vehicle_luggage'] ?? 0);
                $fuel  = mysqli_real_escape_string($connection, $mv['vehicle_fuel_type'] ?? '');
                $rTax  = (float)($mv['vehicle_per_km_rate_with_tax'] ?? 0);
                $rNoTax = (float)($mv['vehicle_per_km_rate_without_tax'] ?? 0);

                if (isset($existingVehs[$t])) {
                    if ($migrationMode === 'overwrite') {
                        $exVehId = $existingVehs[$t];
                        mysqli_query($connection, "
                            UPDATE `{$tblPrefix}vehicles` SET
                                vehicle_thumbnail = '{$thumb}',
                                vehicle_description = '{$desc}',
                                vehicle_seating = {$seat},
                                vehicle_type = '{$type}',
                                vehicle_gallery = '{$gall}',
                                vehicle_luggage = {$lug},
                                vehicle_fuel_type = '{$fuel}',
                                vehicle_per_km_rate_with_tax = {$rTax},
                                vehicle_per_km_rate_without_tax = {$rNoTax}
                            WHERE vehicle_id = {$exVehId} AND vehicle_site_id = {$siteId}
                        ");
                        $copiedCounts['vehicles']++;
                    } else {
                        $copiedCounts['skipped']['vehicles']++;
                    }
                } else {
                    mysqli_query($connection, "
                        INSERT INTO `{$tblPrefix}vehicles` (
                            vehicle_thumbnail, vehicle_title, vehicle_description, vehicle_seating,
                            vehicle_is_deletable, vehicle_type, vehicle_gallery, vehicle_luggage,
                            vehicle_fuel_type, vehicle_per_km_rate_with_tax, vehicle_per_km_rate_without_tax,
                            vehicle_site_id, vehicle_time
                        ) VALUES (
                            '{$thumb}', '{$title}', '{$desc}', {$seat},
                            1, '{$type}', '{$gall}', {$lug},
                            '{$fuel}', {$rTax}, {$rNoTax},
                            {$siteId}, NOW()
                        )
                    ");
                    $existingVehs[$t] = (int)mysqli_insert_id($connection);
                    $copiedCounts['vehicles']++;
                }
            }
        }
    }
}

$totalCopied = $copiedCounts['tour_packages'] + $copiedCounts['hotels'] + $copiedCounts['transporters'] + $copiedCounts['travel_destinations'] + $copiedCounts['vehicles'];
$totalSkipped = $copiedCounts['skipped']['tour_packages'] + $copiedCounts['skipped']['hotels'] + $copiedCounts['skipped']['transporters'] + ($copiedCounts['skipped']['vehicles'] ?? 0) + ($copiedCounts['skipped']['travel_destinations'] ?? 0);

$msg = "";
if ($totalCopied > 0 && $totalSkipped > 0) {
    $msg = "Successfully copied {$totalCopied} master items into your workspace ({$totalSkipped} duplicates skipped).";
} elseif ($totalCopied > 0) {
    $msg = "Successfully copied {$totalCopied} master items into your workspace.";
} else {
    $msg = "Your workspace is already synchronized with master data ({$totalSkipped} existing items kept safe).";
}

echo json_encode([
    'status'  => 'success',
    'msg'     => $msg,
    'summary' => [
        'packages_copied'      => $copiedCounts['tour_packages'],
        'destinations_copied'  => $copiedCounts['travel_destinations'],
        'vehicles_copied'      => $copiedCounts['vehicles'],
        'hotels_copied'        => $copiedCounts['hotels'],
        'transporters_copied'  => $copiedCounts['transporters'],
        'rooms_copied'         => $copiedCounts['rooms'],
        'prices_copied'        => $copiedCounts['tour_package_prices'],
        'packages_skipped'     => $copiedCounts['skipped']['tour_packages'],
        'hotels_skipped'       => $copiedCounts['skipped']['hotels'],
        'transporters_skipped' => $copiedCounts['skipped']['transporters'],
        'vehicles_skipped'     => $copiedCounts['skipped']['vehicles'] ?? 0,
        'destinations_skipped' => $copiedCounts['skipped']['travel_destinations'] ?? 0,
        'total_copied'         => $totalCopied,
        'total_skipped'        => $totalSkipped,
        'mode'                 => $migrationMode
    ],
    'copied'  => $copiedCounts,
    'site_id' => $siteId
]);
