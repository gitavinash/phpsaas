<?php

/**
 * V3 Master Data Copy / Clone API
 * Path: /api/v3/master/copy.php
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

// Ensure master schema exists
require_once __DIR__ . '/schema.php';
ensureMasterDataTables($connection, $tblPrefix, false);

// Parse input (supports JSON or Form POST)
$rawBody = file_get_contents('php://input');
$jsonInput = json_decode($rawBody, true);
$input = is_array($jsonInput) ? $jsonInput : $_POST;

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
        'tour_packages' => 0,
        'hotels'        => 0,
        'transporters'  => 0
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

            if (isset($existingHotels[$key])) {
                $masterHotelToSiteHotelMap[$mId] = $existingHotels[$key];
                $copiedCounts['skipped']['hotels']++;
                continue;
            }

            // Insert into property
            $thumb = mysqli_real_escape_string($connection, $mH['property_thumbnail'] ?? '');
            $gall  = mysqli_real_escape_string($connection, $mH['property_gallery'] ?? '');
            $title = mysqli_real_escape_string($connection, $mH['property_title'] ?? '');
            $email = mysqli_real_escape_string($connection, $mH['property_email'] ?? '');
            $mobile= mysqli_real_escape_string($connection, $mH['property_mobile'] ?? '');
            $desc  = mysqli_real_escape_string($connection, $mH['property_description'] ?? '');
            $addr  = mysqli_real_escape_string($connection, $mH['property_address'] ?? '');
            $city  = mysqli_real_escape_string($connection, $mH['property_city'] ?? '');
            $state = mysqli_real_escape_string($connection, $mH['property_state'] ?? '');
            $country = mysqli_real_escape_string($connection, $mH['property_country'] ?? '');
            $pin   = mysqli_real_escape_string($connection, $mH['property_pincode'] ?? '');
            $mapUrl= mysqli_real_escape_string($connection, $mH['property_map_url'] ?? '');
            $mrp   = (int)($mH['property_starting_mrp'] ?? 0);
            $price = (int)($mH['property_starting_price'] ?? 0);
            $rooms = (int)($mH['property_total_rooms'] ?? 0);
            $type  = mysqli_real_escape_string($connection, $mH['property_type'] ?? '3_star_hotel');
            $sEmail= mysqli_real_escape_string($connection, $mH['property_support_email'] ?? '');
            $sMob  = mysqli_real_escape_string($connection, $mH['property_support_mobile'] ?? '');

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

                // Copy Rooms for this hotel
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

                        $inRoomSql = "
                            INSERT INTO `{$tblPrefix}room` (
                                room_property_id, room_site_id, room_feature_image, room_gallery,
                                room_description, room_type, room_ep_mrp, room_ep_b2c, room_ep_b2b,
                                room_cp_mrp, room_cp_b2c, room_cp_b2b, room_map_mrp, room_map_b2c,
                                room_map_b2b, room_ap_mrp, room_ap_b2b, room_ap_b2c, room_status,
                                room_create, room_update
                            ) VALUES (
                                {$newHotelId}, {$siteId}, '{$rfImg}', '{$rGall}',
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
    $ePQuery = mysqli_query($connection, "SELECT LOWER(TRIM(tour_package_title)) as t FROM `{$tblPrefix}tour_package` WHERE tour_package_site_id = {$siteId}");
    if ($ePQuery) {
        while ($ep = mysqli_fetch_assoc($ePQuery)) {
            $existingPackages[$ep['t']] = true;
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

            if (isset($existingPackages[$cleanTitle])) {
                $copiedCounts['skipped']['tour_packages']++;
                continue;
            }

            // Insert tour package
            $img   = mysqli_real_escape_string($connection, $pkg['tour_package_image'] ?? '');
            $title = mysqli_real_escape_string($connection, $pkg['tour_package_title'] ?? '');
            $theme = mysqli_real_escape_string($connection, $pkg['tour_package_theme'] ?? 'standard');
            $tmpl  = mysqli_real_escape_string($connection, $pkg['tour_package_template'] ?? 'mit');
            $start = mysqli_real_escape_string($connection, $pkg['tour_package_start_from'] ?? '');
            $end   = mysqli_real_escape_string($connection, $pkg['tour_package_end_to'] ?? '');
            $city  = mysqli_real_escape_string($connection, $pkg['tour_package_city'] ?? '');
            $dur   = (int)($pkg['tour_package_duration'] ?? 1);
            $over  = mysqli_real_escape_string($connection, $pkg['tour_package_overview'] ?? '');
            $itin  = mysqli_real_escape_string($connection, $pkg['tour_package_itinerary'] ?? '');
            $inc   = mysqli_real_escape_string($connection, $pkg['tour_package_inclusions'] ?? '');
            $exc   = mysqli_real_escape_string($connection, $pkg['tour_package_exclusions'] ?? '');
            $price = mysqli_real_escape_string($connection, $pkg['tour_package_price'] ?? '');
            $hotel = mysqli_real_escape_string($connection, $pkg['tour_package_hotel'] ?? '');
            $state = mysqli_real_escape_string($connection, $pkg['tour_package_state'] ?? '');
            $cntry = mysqli_real_escape_string($connection, $pkg['tour_package_country'] ?? '');
            $type  = mysqli_real_escape_string($connection, $pkg['tour_package_type'] ?? 'domestic');
            $outVeh= mysqli_real_escape_string($connection, $pkg['tour_package_outstation_vehicle'] ?? '');
            $locVeh= mysqli_real_escape_string($connection, $pkg['tour_package_localsightseen_vehicle'] ?? '');

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
    $eTQ = mysqli_query($connection, "SELECT LOWER(TRIM(transporter_title)) as t FROM `{$tblPrefix}transporter` WHERE transporter_site_id = {$siteId}");
    if ($eTQ) {
        while ($et = mysqli_fetch_assoc($eTQ)) {
            $existingTrans[$et['t']] = true;
        }
    }

    $mTransQ = mysqli_query($connection, "SELECT * FROM `{$tblPrefix}master_transporters`");
    if ($mTransQ) {
        while ($mt = mysqli_fetch_assoc($mTransQ)) {
            $cleanTitle = strtolower(trim($mt['transporter_title'] ?? ''));
            if (empty($cleanTitle)) continue;

            if (isset($existingTrans[$cleanTitle])) {
                $copiedCounts['skipped']['transporters']++;
                continue;
            }

            $thumb = mysqli_real_escape_string($connection, $mt['transporter_thumbnail'] ?? '');
            $title = mysqli_real_escape_string($connection, $mt['transporter_title'] ?? '');
            $email = mysqli_real_escape_string($connection, $mt['transporter_email'] ?? '');
            $mobile= mysqli_real_escape_string($connection, $mt['transporter_mobile'] ?? '');
            $desc  = mysqli_real_escape_string($connection, $mt['transporter_description'] ?? '');
            $addr  = mysqli_real_escape_string($connection, $mt['transporter_address'] ?? '');
            $city  = mysqli_real_escape_string($connection, $mt['transporter_city'] ?? '');
            $pin   = mysqli_real_escape_string($connection, $mt['transporter_pincode'] ?? '');
            $sState= mysqli_real_escape_string($connection, $mt['transporter_service_state'] ?? '');

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
                $existingTrans[$cleanTitle] = true;
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
    $eDQ = mysqli_query($connection, "SELECT LOWER(TRIM(location_title)) as t FROM `{$tblPrefix}locations`");
    if ($eDQ) {
        while ($ed = mysqli_fetch_assoc($eDQ)) {
            $existingDests[$ed['t']] = true;
        }
    }

    $mDestQ = mysqli_query($connection, "SELECT * FROM `{$tblPrefix}master_travel_destinations`");
    if ($mDestQ) {
        while ($md = mysqli_fetch_assoc($mDestQ)) {
            $t = strtolower(trim($md['location_title'] ?? ''));
            if (!empty($t) && !isset($existingDests[$t])) {
                $title = mysqli_real_escape_string($connection, $md['location_title']);
                $thumb = mysqli_real_escape_string($connection, $md['location_thumbnail'] ?? '');
                $gall  = mysqli_real_escape_string($connection, $md['location_gallery'] ?? '');
                $desc  = mysqli_real_escape_string($connection, $md['location_description'] ?? '');
                $state = mysqli_real_escape_string($connection, $md['location_state'] ?? '');
                $cntry = mysqli_real_escape_string($connection, $md['location_country'] ?? '');
                $bFrom = mysqli_real_escape_string($connection, $md['location_best_from'] ?? '');
                $bTo   = mysqli_real_escape_string($connection, $md['location_best_to'] ?? '');

                mysqli_query($connection, "
                    INSERT INTO `{$tblPrefix}locations` (
                        location_title, location_thumbnail, location_gallery, location_description,
                        location_is_deletable, location_state, location_country, location_best_from, location_best_to, location_time
                    ) VALUES (
                        '{$title}', '{$thumb}', '{$gall}', '{$desc}',
                        1, '{$state}', '{$cntry}', '{$bFrom}', '{$bTo}', NOW()
                    )
                ");
                $existingDests[$t] = true;
                $copiedCounts['travel_destinations']++;
            }
        }
    }
}

// =========================================================================
// 5. ENSURE VEHICLES (Global Master)
// =========================================================================
if (isset($selectedTypes['vehicles'])) {
    $existingVehs = [];
    $eVQ = mysqli_query($connection, "SELECT LOWER(TRIM(vehicle_title)) as t FROM `{$tblPrefix}vehicles`");
    if ($eVQ) {
        while ($ev = mysqli_fetch_assoc($eVQ)) {
            $existingVehs[$ev['t']] = true;
        }
    }

    $mVehQ = mysqli_query($connection, "SELECT * FROM `{$tblPrefix}master_vehicles`");
    if ($mVehQ) {
        while ($mv = mysqli_fetch_assoc($mVehQ)) {
            $t = strtolower(trim($mv['vehicle_title'] ?? ''));
            if (!empty($t) && !isset($existingVehs[$t])) {
                $title = mysqli_real_escape_string($connection, $mv['vehicle_title']);
                $thumb = mysqli_real_escape_string($connection, $mv['vehicle_thumbnail'] ?? '');
                $desc  = mysqli_real_escape_string($connection, $mv['vehicle_description'] ?? '');
                $seat  = (int)($mv['vehicle_seating'] ?? 4);
                $type  = mysqli_real_escape_string($connection, $mv['vehicle_type'] ?? 'sedan');
                $gall  = mysqli_real_escape_string($connection, $mv['vehicle_gallery'] ?? '');
                $lug   = (int)($mv['vehicle_luggage'] ?? 0);
                $fuel  = mysqli_real_escape_string($connection, $mv['vehicle_fuel_type'] ?? '');
                $rTax  = (float)($mv['vehicle_per_km_rate_with_tax'] ?? 0);
                $rNoTax= (float)($mv['vehicle_per_km_rate_without_tax'] ?? 0);

                mysqli_query($connection, "
                    INSERT INTO `{$tblPrefix}vehicles` (
                        vehicle_thumbnail, vehicle_title, vehicle_description, vehicle_seating,
                        vehicle_is_deletable, vehicle_type, vehicle_gallery, vehicle_luggage,
                        vehicle_fuel_type, vehicle_per_km_rate_with_tax, vehicle_per_km_rate_without_tax, vehicle_time
                    ) VALUES (
                        '{$thumb}', '{$title}', '{$desc}', {$seat},
                        1, '{$type}', '{$gall}', {$lug},
                        '{$fuel}', {$rTax}, {$rNoTax}, NOW()
                    )
                ");
                $existingVehs[$t] = true;
                $copiedCounts['vehicles']++;
            }
        }
    }
}

$totalCopied = $copiedCounts['tour_packages'] + $copiedCounts['hotels'] + $copiedCounts['transporters'] + $copiedCounts['travel_destinations'] + $copiedCounts['vehicles'];

echo json_encode([
    'status'        => 'success',
    'msg'           => "Successfully copied {$totalCopied} master items into your dashboard.",
    'copied'        => $copiedCounts,
    'site_id'       => $siteId
]);
