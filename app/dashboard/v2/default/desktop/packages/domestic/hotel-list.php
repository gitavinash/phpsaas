<?php

$idParam = isset($_GET['id']) ? $_GET['id'] : '0';
$tourPackageId = is_numeric($idParam) ? (int)$idParam : (int)base64_decode(mysqli_real_escape_string($connection, $idParam));

// Fetch Tour Package Details
$pkgTitle = 'Tour Package';
$pkgCity = '';
$pkgState = '';
$pkgCountry = '';
$pkgDuration = 0;
$pkgHotelType = '';
$pkgItinerary = '';

$pkgQuery = "SELECT tour_package_title, tour_package_city, tour_package_state, tour_package_country, tour_package_duration, tour_package_hotel, tour_package_itinerary 
            FROM " . TABLE_PREFIX . "tour_package 
            WHERE tour_package_id = '{$tourPackageId}' AND tour_package_site_id = " . SITE_ID;

if ($pkgRes = mysqli_query($connection, $pkgQuery)) {
    if (mysqli_num_rows($pkgRes) > 0) {
        $pkgRow = mysqli_fetch_assoc($pkgRes);
        $pkgTitle = $pkgRow['tour_package_title'] ?? 'Tour Package';
        $pkgCity = trim($pkgRow['tour_package_city'] ?? '');
        $pkgState = trim($pkgRow['tour_package_state'] ?? '');
        $pkgCountry = trim($pkgRow['tour_package_country'] ?? '');
        $pkgDuration = max(1, (int)($pkgRow['tour_package_duration'] ?? 0));
        $pkgHotelType = trim($pkgRow['tour_package_hotel'] ?? '');
        $pkgItinerary = trim($pkgRow['tour_package_itinerary'] ?? '');
    }
}

// Fetch Existing Hotel Night Assignments for this Package
$assignedNightMap = [];
$hotelAssignmentMap = [];

$assignedQuery = "SELECT tph_id, tph_hotel_id, tph_room_type_id, tph_room_type, tph_night 
                  FROM " . TABLE_PREFIX . "tour_package_hotel 
                  WHERE tph_package_id = '{$tourPackageId}' AND tph_site_id = " . SITE_ID;

if ($assignedRes = mysqli_query($connection, $assignedQuery)) {
    while ($aRow = mysqli_fetch_assoc($assignedRes)) {
        $n = (int)$aRow['tph_night'];
        $hId = (int)$aRow['tph_hotel_id'];
        $assignedNightMap[$n] = $aRow;
        $hotelAssignmentMap[$hId] = $aRow;
    }
}

// SMART CITY DETECTION: Extract Mentioned Cities & Nearby Destinations
$dbCityStateQuery = "SELECT DISTINCT TRIM(property_city) AS city, TRIM(property_state) AS state 
                     FROM " . TABLE_PREFIX . "property 
                     WHERE property_site_id = " . SITE_ID . " AND property_status = 1 AND property_city <> ''";

$allDbCities = [];
if ($cRes = mysqli_query($connection, $dbCityStateQuery)) {
    while ($cRow = mysqli_fetch_assoc($cRes)) {
        if (!empty($cRow['city'])) {
            $allDbCities[] = $cRow['city'];
        }
    }
}
$allDbCities = array_values(array_unique($allDbCities));

$combinedSearchText = strtolower($pkgTitle . ' ' . $pkgCity . ' ' . $pkgState . ' ' . $pkgItinerary);
$pkgCityTokens = preg_split('/[,;\-\/|&]+/u', strtolower($pkgCity));
$pkgCityTokens = array_map('trim', array_filter($pkgCityTokens));

$mentionedCities = [];
foreach ($allDbCities as $dbCity) {
    $dbCityLower = strtolower($dbCity);
    if (empty($dbCityLower)) continue;

    $isMatch = false;
    foreach ($pkgCityTokens as $token) {
        if ($token === $dbCityLower || stripos($token, $dbCityLower) !== false || stripos($dbCityLower, $token) !== false) {
            $isMatch = true;
            break;
        }
    }

    if (!$isMatch && (stripos($combinedSearchText, $dbCityLower) !== false)) {
        $isMatch = true;
    }

    if ($isMatch) {
        $mentionedCities[] = $dbCity;
    }
}
$mentionedCities = array_values(array_unique($mentionedCities));

// Build SQL WHERE Clause to ONLY show Mentioned Cities or Nearby State Hotels (plus assigned hotels)
$whereConditions = [];
$whereConditions[] = "p.property_site_id = " . SITE_ID;
$whereConditions[] = "p.property_status = 1";

$cityOrStateFilters = [];

if (!empty($hotelAssignmentMap)) {
    $assignedHotelIds = array_keys($hotelAssignmentMap);
    $assignedIdsStr = implode(',', array_map('intval', $assignedHotelIds));
    $cityOrStateFilters[] = "p.property_id IN ({$assignedIdsStr})";
} else {
    $assignedIdsStr = "0";
}

if (!empty($mentionedCities)) {
    $escapedMentionedCities = array_map(function ($c) use ($connection) {
        return "'" . mysqli_real_escape_string($connection, strtoupper(trim($c))) . "'";
    }, $mentionedCities);
    $citiesInStr = implode(',', $escapedMentionedCities);
    $cityOrStateFilters[] = "UPPER(TRIM(p.property_city)) IN ({$citiesInStr})";
} else {
    $citiesInStr = "''";
}

if (!empty($pkgState)) {
    $escapedState = mysqli_real_escape_string($connection, strtoupper(trim($pkgState)));
    $cityOrStateFilters[] = "UPPER(TRIM(p.property_state)) = '{$escapedState}'";
} else {
    $escapedState = "''";
}

// If we have specific filters, restrict query to them
if (!empty($cityOrStateFilters)) {
    $whereConditions[] = "(" . implode(" OR ", $cityOrStateFilters) . ")";
}

$whereClause = "WHERE " . implode(" AND ", $whereConditions);

$query = "
SELECT 
    p.property_id,
    p.property_title, 
    p.property_thumbnail, 
    p.property_address, 
    TRIM(p.property_city)     AS property_city, 
    TRIM(p.property_state)    AS property_state, 
    TRIM(p.property_country)  AS property_country, 
    p.property_type,
    CASE 
      WHEN p.property_id IN ({$assignedIdsStr}) THEN 1
      WHEN UPPER(TRIM(p.property_city)) IN ({$citiesInStr}) THEN 2
      WHEN UPPER(TRIM(p.property_state)) = '{$escapedState}' THEN 3
      ELSE 99
    END AS match_priority,
    GROUP_CONCAT(
      DISTINCT CONCAT(r.room_id, ':', r.room_type) 
      ORDER BY r.room_id SEPARATOR ','
    ) AS rooms
FROM " . TABLE_PREFIX . "property p
LEFT JOIN " . TABLE_PREFIX . "room r
  ON r.room_property_id = p.property_id
 AND r.room_site_id     = " . SITE_ID . "
{$whereClause}
GROUP BY p.property_id
ORDER BY match_priority ASC, p.property_title ASC
";

$hotelList = array();
$cityCountsMap = [];

if ($hotelData = mysqli_query($connection, $query)) {
    if (mysqli_num_rows($hotelData) != 0) {
        while ($row = mysqli_fetch_assoc($hotelData)) {
            $hId = (int)$row['property_id'];
            $cName = trim($row['property_city']);
            if (!empty($cName)) {
                $cityCountsMap[$cName] = ($cityCountsMap[$cName] ?? 0) + 1;
            }

            if (isset($hotelAssignmentMap[$hId])) {
                $row['assigned_night'] = (int)$hotelAssignmentMap[$hId]['tph_night'];
                $row['assigned_room_id'] = (int)$hotelAssignmentMap[$hId]['tph_room_type_id'];
                $row['assigned_room_type'] = $hotelAssignmentMap[$hId]['tph_room_type'];
            } else {
                $row['assigned_night'] = 0;
                $row['assigned_room_id'] = 0;
                $row['assigned_room_type'] = '';
            }
            $hotelList[] = $row;
        }
    }
}

// Build occupied night to hotel name map
$occupiedNightHotelNameMap = [];
foreach ($assignedNightMap as $n => $info) {
    $hId = (int)$info['tph_hotel_id'];
    foreach ($hotelList as $h) {
        if ((int)$h['property_id'] === $hId) {
            $occupiedNightHotelNameMap[$n] = $h['property_title'];
            break;
        }
    }
}

// Sort hotels: Assigned nights (1, 2, 3...) first, then mentioned city priority
usort($hotelList, function ($a, $b) {
    $nightA = isset($a['assigned_night']) && (int)$a['assigned_night'] > 0 ? (int)$a['assigned_night'] : 999999;
    $nightB = isset($b['assigned_night']) && (int)$b['assigned_night'] > 0 ? (int)$b['assigned_night'] : 999999;
    if ($nightA !== $nightB) {
        return ($nightA < $nightB) ? -1 : 1;
    }

    $prioA = (int)($a['match_priority'] ?? 99);
    $prioB = (int)($b['match_priority'] ?? 99);
    if ($prioA !== $prioB) {
        return ($prioA < $prioB) ? -1 : 1;
    }

    return strcmp($a['property_title'], $b['property_title']);
});

// Count night coverage stats
$nightsCoveredCount = count($assignedNightMap);
$nightsMissingCount = max(0, $pkgDuration - $nightsCoveredCount);

$assignedHotelsCount = 0;
$unassignedHotelsCount = 0;
foreach ($hotelList as $h) {
    if (isset($h['assigned_night']) && (int)$h['assigned_night'] > 0) {
        $assignedHotelsCount++;
    } else {
        $unassignedHotelsCount++;
    }
}
$totalHotelsCount = count($hotelList);

?>

<style>
    /* Theme Integration Styles */
    .dashboard-gallery-container {
        width: 100%;
        padding: 10px 0;
    }

    .gallery-sub-header {
        display: flex;
        flex-direction: column;
        gap: 14px;
        background: #ffffff;
        padding: 18px 20px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        margin-bottom: 20px;
    }

    .gallery-sub-header-top {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .gallery-stats-pills {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .theme-stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #cbd5e1;
    }

    .theme-stat-pill.is-green {
        background: #ecfdf5;
        color: #047857;
        border-color: #a7f3d0;
    }

    .theme-stat-pill.is-orange {
        background: #fff7ed;
        color: #c2410c;
        border-color: #ffedd5;
    }

    .theme-stat-pill.is-red {
        background: #fef2f2;
        color: #dc2626;
        border-color: #fca5a5;
    }

    .theme-stat-pill.is-purple {
        background: #f5f3ff;
        color: #6d28d9;
        border-color: #ddd6fe;
    }

    .gallery-filter-tabs {
        display: flex;
        align-items: center;
        gap: 6px;
        background: #f8fafc;
        padding: 4px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        flex-wrap: wrap;
    }

    .theme-filter-btn {
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        border: none;
        background: transparent;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .theme-filter-btn:hover {
        color: #0f172a;
        background: #ffffff;
    }

    .theme-filter-btn.active {
        background: var(--brand-700, #2563eb);
        color: #ffffff !important;
    }

    /* City Filter Chips */
    .city-filter-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        padding-top: 10px;
        border-top: 1px dashed #e2e8f0;
    }

    .city-filter-title {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-right: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .city-chip-btn {
        padding: 4px 12px;
        border-radius: 16px;
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .city-chip-btn:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .city-chip-btn.active {
        background: #0284c7;
        color: #ffffff;
        border-color: #0284c7;
    }

    /* Dashboard Gallery / Hotel Grid Layout */
    .dashboard-gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        margin-top: 10px;
    }

    .dash-gallery-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        transition: all 0.25s ease-in-out;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .dash-gallery-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }

    .dash-gallery-card.is-assigned {
        border: 2px solid #10b981;
        background: #fafdfb;
    }

    .dash-thumb-container {
        position: relative;
        width: 100%;
        height: 190px;
        background: #0f172a;
        overflow: hidden;
    }

    .dash-thumb-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .dash-gallery-card:hover .dash-thumb-container img {
        transform: scale(1.05);
    }

    .dash-card-badges {
        position: absolute;
        top: 8px;
        left: 8px;
        right: 8px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 6px;
        z-index: 2;
        pointer-events: none;
    }

    .dash-badge-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 14px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        backdrop-filter: blur(6px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    .dash-badge-chip.badge-day {
        background: rgba(16, 185, 129, 0.95);
        color: #ffffff;
    }

    .dash-badge-chip.badge-none {
        background: rgba(100, 116, 139, 0.85);
        color: #ffffff;
    }

    .dash-badge-chip.badge-type {
        background: rgba(2, 132, 199, 0.9);
        color: #ffffff;
    }

    .dash-thumb-overlay {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s ease;
        z-index: 3;
    }

    .dash-gallery-card:hover .dash-thumb-overlay {
        opacity: 1;
    }

    .dash-preview-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #ffffff;
        color: #0f172a;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
        transition: all 0.2s ease;
    }

    .dash-preview-btn:hover {
        transform: scale(1.1);
        background: var(--brand-700, #2563eb);
        color: #ffffff;
    }

    .dash-card-body {
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .hotel-card-header h3 {
        margin: 0 0 2px 0;
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
    }

    .hotel-location-tag {
        font-size: 12px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .dash-day-select-wrap label {
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 6px;
    }

    .dash-day-select-wrap .select2-container {
        width: 100% !important;
    }

    .dash-day-select-wrap .select2-container--default .select2-selection--single {
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        height: 36px;
        display: flex;
        align-items: center;
    }

    .dash-day-select-wrap .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 34px;
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        padding-left: 10px;
    }

    .dash-day-select-wrap .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 34px;
    }

    .dash-btn-featured {
        width: 100%;
        padding: 9px 12px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        text-align: center;
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        color: #334155;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.2s ease;
    }

    .dash-btn-featured:hover {
        background: var(--brand-700, #2563eb);
        color: #ffffff;
        border-color: var(--brand-700, #2563eb);
    }

    .dash-btn-featured.is-assigned-btn {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #047857;
    }

    .dash-btn-featured.is-assigned-btn:hover {
        background: #059669;
        color: #ffffff;
        border-color: #059669;
    }

    /* Lightbox Preview Modal */
    .theme-lightbox-modal {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(6px);
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .theme-lightbox-modal.active {
        display: flex;
    }

    .theme-lightbox-content {
        background: #ffffff;
        border-radius: 14px;
        max-width: 850px;
        width: 100%;
        max-height: 90vh;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        display: flex;
        flex-direction: column;
    }

    .theme-lightbox-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .theme-lightbox-header h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
    }

    .theme-lightbox-close {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: none;
        background: #e2e8f0;
        color: #475569;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .theme-lightbox-close:hover {
        background: #cbd5e1;
        color: #0f172a;
    }

    .theme-lightbox-body {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #0f172a;
        padding: 16px;
        max-height: 60vh;
    }

    .theme-lightbox-body img {
        max-width: 100%;
        max-height: 55vh;
        object-fit: contain;
    }

    .theme-lightbox-footer {
        padding: 14px 20px;
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
</style>

<h2 class="heading">
    <?php echo ucwords($pkgTitle); ?>'s Included Hotels
    <span>Only displaying hotels from mentioned cities & nearby destinations in your package itinerary</span>
</h2>

<div class="box-1">
    <div class="widget box-1 no-padding">
        <div class="main-box">
            <section class="dashboard-gallery-container">

                <!-- Sub Header Controls & Filters -->
                <div class="gallery-sub-header">
                    <div class="gallery-sub-header-top">
                        <div class="gallery-stats-pills">
                            <div class="theme-stat-pill">
                                <i class="fi fi-rr-building"></i> <b id="statTotalHotels"><?php echo $totalHotelsCount; ?></b> Nearby Hotels Found
                            </div>
                            <?php if (!empty($mentionedCities)) { ?>
                                <div class="theme-stat-pill is-purple">
                                    <i class="fi fi-rr-marker"></i> <b><?php echo count($mentionedCities); ?></b> Mentioned Cities
                                </div>
                            <?php } ?>
                            <div class="theme-stat-pill is-green">
                                <i class="fi fi-rr-moon-stars"></i> <b id="statNightsCovered"><?php echo $nightsCoveredCount; ?> / <?php echo $pkgDuration; ?></b> Nights Covered
                            </div>
                            <div class="theme-stat-pill <?php echo ($nightsMissingCount > 0) ? 'is-red' : 'is-green'; ?>" id="statNightsMissingPill">
                                <?php if ($nightsMissingCount > 0) { ?>
                                    <i class="fi fi-rr-exclamation"></i> <b><?php echo $nightsMissingCount; ?></b> <?php echo ($nightsMissingCount === 1) ? 'Night' : 'Nights'; ?> Missing Hotel
                                <?php } else { ?>
                                    <i class="fi fi-rr-check-circle"></i> All Nights Assigned!
                                <?php } ?>
                            </div>
                        </div>

                        <div class="gallery-filter-tabs">
                            <button class="theme-filter-btn active" data-filter="all">All (<?php echo $totalHotelsCount; ?>)</button>
                            <button class="theme-filter-btn" data-filter="assigned">Assigned Nights (<?php echo $assignedHotelsCount; ?>)</button>
                            <button class="theme-filter-btn" data-filter="unassigned">Unassigned (<?php echo $unassignedHotelsCount; ?>)</button>
                        </div>
                    </div>

                    <!-- Mentioned Cities Filter Chips Bar -->
                    <?php if (!empty($cityCountsMap)) { ?>
                        <div class="city-filter-bar">
                            <span class="city-filter-title"><i class="fi fi-rr-map"></i> Filter by City:</span>
                            <button type="button" class="city-chip-btn active" data-city="all">All Cities</button>
                            <?php foreach ($cityCountsMap as $cName => $cCount) { ?>
                                <button type="button" class="city-chip-btn" data-city="<?php echo htmlspecialchars($cName); ?>">
                                    <?php echo htmlspecialchars($cName); ?> (<?php echo $cCount; ?>)
                                </button>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>

                <!-- Hotel Cards Grid Layout -->
                <div class="dashboard-gallery-grid" id="hotelGrid">
                    <?php
                    if (empty($hotelList)) {
                        echo "<div style='grid-column: 1 / -1; padding: 40px; text-align: center; background: #fff; border-radius: 8px; color: #64748b;'>No matching hotels found in database for mentioned cities (" . htmlspecialchars(implode(', ', $mentionedCities ?: [$pkgCity ?: $pkgState])) . "). Click the floating <b>+</b> button at bottom right to add a new hotel vendor property.</div>";
                    }

                    foreach ($hotelList as $index => $hotel) {
                        $path = !empty($hotel['property_thumbnail']) ? $hotel['property_thumbnail'] : ADMIN_IMAGES . 'default-picture.png';
                        $hotelId = (int)$hotel['property_id'];
                        $assignedNight = (int)$hotel['assigned_night'];
                        $assignedRoomId = (int)$hotel['assigned_room_id'];
                        $assignedRoomType = $hotel['assigned_room_type'];
                        $propTitle = $hotel['property_title'];
                        $propCity = $hotel['property_city'];
                        $locationStr = implode(', ', array_filter([$propCity, $hotel['property_state']]));

                        $cardClasses = 'dash-gallery-card dash-hotel-card';
                        if ($assignedNight > 0) $cardClasses .= ' is-assigned';
                    ?>
                        <div class="<?php echo $cardClasses; ?>"
                            data-hotel-id="<?php echo $hotelId; ?>"
                            data-night="<?php echo $assignedNight; ?>"
                            data-city="<?php echo htmlspecialchars($propCity); ?>"
                            data-title="<?php echo htmlspecialchars($propTitle); ?>"
                            data-image="<?php echo htmlspecialchars($path); ?>">

                            <div class="dash-thumb-container">
                                <img src="<?php echo htmlspecialchars($path); ?>" alt="<?php echo htmlspecialchars($propTitle); ?>" loading="lazy">

                                <div class="dash-card-badges">
                                    <div class="badge-night-wrap">
                                        <?php if ($assignedNight > 0) { ?>
                                            <span class="dash-badge-chip badge-day"><i class="fi fi-rr-moon"></i> Night <?php echo $assignedNight; ?></span>
                                        <?php } else { ?>
                                            <span class="dash-badge-chip badge-none">Unassigned</span>
                                        <?php } ?>
                                    </div>
                                    <div>
                                        <span class="dash-badge-chip badge-type"><i class="fi fi-rr-building"></i> <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $hotel['property_type'] ?? 'Hotel'))); ?></span>
                                    </div>
                                </div>

                                <div class="dash-thumb-overlay">
                                    <button type="button" class="dash-preview-btn btn-preview-img"
                                        data-image="<?php echo htmlspecialchars($path); ?>"
                                        data-name="<?php echo htmlspecialchars($propTitle); ?>"
                                        title="View Preview">
                                        <i class="fi fi-rr-search"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="dash-card-body">
                                <div class="hotel-card-header">
                                    <h3><?php echo htmlspecialchars($propTitle); ?></h3>
                                    <?php if (!empty($locationStr)) { ?>
                                        <span class="hotel-location-tag"><i class="fi fi-rr-marker"></i> <?php echo htmlspecialchars($locationStr); ?></span>
                                    <?php } ?>
                                </div>

                                <!-- Assign Night Dropdown -->
                                <div class="dash-day-select-wrap">
                                    <label><i class="fi fi-rr-moon"></i> Assign Night:</label>
                                    <select class="selectJS form-control assign-night-select">
                                        <option value="0">-- Unassigned --</option>
                                        <?php for ($n = 1; $n <= $pkgDuration; $n++) {
                                            $selected = ($assignedNight === $n) ? 'selected' : '';
                                            $occupiedBy = $occupiedNightHotelNameMap[$n] ?? '';
                                            $isOccupiedByOther = (!empty($occupiedBy) && $occupiedBy !== $propTitle);
                                            $labelSuffix = $isOccupiedByOther ? (' (Occupied by ' . $occupiedBy . ')') : '';
                                        ?>
                                            <option value="<?php echo $n; ?>" <?php echo $selected; ?>>
                                                Night <?php echo $n . $labelSuffix; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <!-- Assign Room Type Dropdown -->
                                <div class="dash-day-select-wrap">
                                    <label><i class="fi fi-rr-bed"></i> Room Type:</label>
                                    <select class="selectJS form-control assign-room-select">
                                        <option value="#">-- Select Room Type --</option>
                                        <?php
                                        $roomsRaw = $hotel['rooms'] ?? '';
                                        if (!empty($roomsRaw)) {
                                            $roomList = explode(',', $roomsRaw);
                                            foreach ($roomList as $rItem) {
                                                $rParts = explode(':', $rItem);
                                                $rId = (int)($rParts[0] ?? 0);
                                                $rType = $rParts[1] ?? '';
                                                $rVal = $rId . ':' . $rType;
                                                $rSelected = ($rId === $assignedRoomId || $rType === $assignedRoomType) ? 'selected' : '';
                                                echo "<option value='{$rVal}' {$rSelected}>" . htmlspecialchars(ucwords(str_replace('_', ' ', $rType))) . "</option>";
                                            }
                                        } else {
                                            echo "<option value='0:Standard Room' selected>Standard Room</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <button type="button" class="dash-btn-featured assignHotelBtn <?php echo ($assignedNight > 0) ? 'is-assigned-btn' : ''; ?>">
                                    <i class="fi fi-rr-check"></i> <?php echo ($assignedNight > 0) ? 'Update Assignment' : 'Assign to Package'; ?>
                                </button>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <!-- Floating Shortcut Action to Add New Hotel -->
                <div class="shortcut">
                    <a href="/dashboard/vendors/property/add/" title="Add New Hotel Vendor"><i class="fi fi-rr-plus"></i></a>
                </div>

            </section>
        </div>
    </div>

    <!-- Lightbox Image Preview Modal -->
    <div class="theme-lightbox-modal" id="lightboxModal">
        <div class="theme-lightbox-content">
            <div class="theme-lightbox-header">
                <h3 id="lightboxTitle">Hotel Preview</h3>
                <button type="button" class="theme-lightbox-close" id="lightboxClose">&times;</button>
            </div>
            <div class="theme-lightbox-body">
                <img id="lightboxImg" src="" alt="Hotel Preview">
            </div>
            <div class="theme-lightbox-footer">
                <span class="theme-stat-pill is-green" id="lightboxNightBadge"><i class="fi fi-rr-building"></i> Hotel Property</span>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const totalDurationNights = <?php echo $pkgDuration; ?>;
        const currentTourPackageId = "<?php echo $tourPackageId; ?>";

        let activeStatusFilter = "all";
        let activeCityFilter = "all";

        // Initialize Select2 on night & room selector dropdowns
        function initHotelSelect2() {
            if ($.fn.select2) {
                $(".assign-night-select, .assign-room-select").select2({
                    minimumResultsForSearch: Infinity,
                    width: '100%'
                });
            }
        }
        initHotelSelect2();

        // Combined Filter Engine (Status + City)
        function applyCombinedFilters() {
            $(".dash-hotel-card").each(function() {
                const $card = $(this);
                const night = parseInt($card.data("night")) || 0;
                const cardCity = ($card.data("city") || "").toString().toLowerCase().trim();

                let statusPass = true;
                if (activeStatusFilter === "assigned" && night === 0) statusPass = false;
                if (activeStatusFilter === "unassigned" && night > 0) statusPass = false;

                let cityPass = true;
                if (activeCityFilter !== "all" && cardCity !== activeCityFilter.toLowerCase().trim()) {
                    cityPass = false;
                }

                if (statusPass && cityPass) {
                    $card.show();
                } else {
                    $card.hide();
                }
            });
        }

        // Update real-time stats pills in sub-header
        function updateHotelStatsPills() {
            const assignedNightsSet = new Set();
            let totalHotels = 0;

            $(".dash-hotel-card").each(function() {
                totalHotels++;
                const night = parseInt($(this).data("night")) || 0;
                if (night > 0) {
                    assignedNightsSet.add(night);
                }
            });

            const nightsCovered = assignedNightsSet.size;
            const nightsMissing = Math.max(0, totalDurationNights - nightsCovered);

            $("#statTotalHotels").text(totalHotels);
            $("#statNightsCovered").text(nightsCovered + " / " + totalDurationNights);

            if (nightsMissing > 0) {
                $("#statNightsMissingPill")
                    .removeClass("is-green").addClass("is-red")
                    .html('<i class="fi fi-rr-exclamation"></i> <b>' + nightsMissing + '</b> ' + (nightsMissing === 1 ? 'Night' : 'Nights') + ' Missing Hotel');
            } else {
                $("#statNightsMissingPill")
                    .removeClass("is-red").addClass("is-green")
                    .html('<i class="fi fi-rr-check-circle"></i> All Nights Assigned!');
            }
        }

        // STATUS TAB FILTERING
        $(".theme-filter-btn").click(function() {
            $(".theme-filter-btn").removeClass("active");
            $(this).addClass("active");
            activeStatusFilter = $(this).data("filter");
            applyCombinedFilters();
        });

        // CITY CHIP FILTERING
        $(".city-chip-btn").click(function() {
            $(".city-chip-btn").removeClass("active");
            $(this).addClass("active");
            activeCityFilter = $(this).data("city");
            applyCombinedFilters();
        });

        // Assign / Update Hotel Night Action
        $(document).on("click", ".assignHotelBtn", function(e) {
            e.preventDefault();
            const $btn = $(this);
            const $card = $btn.closest(".dash-hotel-card");
            const hotelId = $card.data("hotel-id");
            const hotelTitle = $card.data("title");
            const chosenNight = parseInt($card.find(".assign-night-select").val()) || 0;
            const chosenRoom = $card.find(".assign-room-select").val();

            if (chosenNight > 0 && (!chosenRoom || chosenRoom === "#")) {
                popUpMessage("center", "Please select a valid Room Type before assigning.", "warning");
                return;
            }

            $btn.prop("disabled", true).html('<i class="fi fi-rr-spinner spinner"></i> Saving...');

            $.ajax({
                url: "/api/property/add-hotel-to-package/",
                type: "POST",
                data: {
                    property_id: hotelId,
                    tour_id: currentTourPackageId,
                    night: chosenNight,
                    roomType: chosenRoom
                },
                dataType: "json",
                success: function(res) {
                    $btn.prop("disabled", false);
                    if (res.status === "success") {
                        popUpMessage("center", res.msg, "success");

                        // If chosenNight > 0, unassign any other hotel previously assigned to this night
                        if (chosenNight > 0) {
                            $(".dash-hotel-card").each(function() {
                                const $otherCard = $(this);
                                if (parseInt($otherCard.data("hotel-id")) !== parseInt(hotelId)) {
                                    const $otherSelect = $otherCard.find(".assign-night-select");
                                    if (parseInt($otherSelect.val()) === chosenNight) {
                                        $otherSelect.val("0").trigger("change.select2");
                                        $otherCard.data("night", 0).removeClass("is-assigned");
                                        $otherCard.find(".badge-night-wrap")
                                            .html('<span class="dash-badge-chip badge-none">Unassigned</span>');
                                        $otherCard.find(".assignHotelBtn")
                                            .removeClass("is-assigned-btn")
                                            .html('<i class="fi fi-rr-check"></i> Assign to Package');
                                    }
                                }
                            });
                        }

                        // Update current card UI
                        $card.data("night", chosenNight);
                        if (chosenNight > 0) {
                            $card.addClass("is-assigned");
                            $card.find(".badge-night-wrap")
                                .html('<span class="dash-badge-chip badge-day"><i class="fi fi-rr-moon"></i> Night ' + chosenNight + '</span>');
                            $btn.addClass("is-assigned-btn").html('<i class="fi fi-rr-check"></i> Update Assignment');
                        } else {
                            $card.removeClass("is-assigned");
                            $card.find(".badge-night-wrap")
                                .html('<span class="dash-badge-chip badge-none">Unassigned</span>');
                            $btn.removeClass("is-assigned-btn").html('<i class="fi fi-rr-check"></i> Assign to Package');
                        }

                        // Update dropdown occupancy text across all cards
                        updateAllNightDropdownOccupiedLabels();

                        // DOM Auto Sorting: assigned night hotels first
                        sortHotelCardsInDOM();

                        // Recalculate stats pills
                        updateHotelStatsPills();
                    } else {
                        $btn.html('<i class="fi fi-rr-check"></i> Assign to Package');
                        popUpMessage("center", res.msg || "Failed to assign hotel", "error");
                    }
                },
                error: function(err) {
                    $btn.prop("disabled", false).html('<i class="fi fi-rr-check"></i> Assign to Package');
                    const msg = err.responseJSON && err.responseJSON.msg ? err.responseJSON.msg : "An error occurred";
                    popUpMessage("center", msg, "error");
                }
            });
        });

        // Function to update "Occupied by [Hotel Name]" in night dropdown options
        function updateAllNightDropdownOccupiedLabels() {
            const occupiedMap = {};
            $(".dash-hotel-card").each(function() {
                const title = $(this).data("title");
                const night = parseInt($(this).data("night")) || 0;
                if (night > 0) {
                    occupiedMap[night] = title;
                }
            });

            $(".dash-hotel-card").each(function() {
                const $card = $(this);
                const currentTitle = $card.data("title");
                const $select = $card.find(".assign-night-select");

                $select.find("option").each(function() {
                    const n = parseInt($(this).val());
                    if (n > 0) {
                        const occupiedBy = occupiedMap[n];
                        const isOccupiedByOther = occupiedBy && occupiedBy !== currentTitle;
                        $(this).text("Night " + n + (isOccupiedByOther ? (" (Occupied by " + occupiedBy + ")") : ""));
                    }
                });

                if ($.fn.select2) {
                    $select.trigger("change.select2");
                }
            });
        }

        // DOM Card Sorting Function: Assigned nights (1, 2, 3...) first, then unassigned
        function sortHotelCardsInDOM() {
            const $grid = $("#hotelGrid");
            const $cards = $grid.find(".dash-hotel-card").get();

            $cards.sort(function(a, b) {
                const nightA = parseInt($(a).data("night")) || 999999;
                const nightB = parseInt($(b).data("night")) || 999999;
                if (nightA === nightB) return 0;
                return nightA < nightB ? -1 : 1;
            });

            $.each($cards, function(idx, elem) {
                $grid.append(elem);
            });

            initHotelSelect2();
        }

        // Lightbox Preview Modal Logic
        $(document).on("click", ".btn-preview-img", function(e) {
            e.preventDefault();
            const src = $(this).data("image");
            const name = $(this).data("name");
            $("#lightboxImg").attr("src", src);
            $("#lightboxTitle").text(name);
            $("#lightboxModal").addClass("active");
        });

        $("#lightboxClose").click(function() {
            $("#lightboxModal").removeClass("active");
        });

        $(document).keydown(function(e) {
            if ($("#lightboxModal").hasClass("active") && e.key === "Escape") {
                $("#lightboxModal").removeClass("active");
            }
        });
    });
</script>