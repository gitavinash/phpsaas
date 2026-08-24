<?php

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : 0;

$rawPkgId = $_GET['package_id'] ?? ($_GET['id'] ?? 0);
$packageId = 0;
if (is_numeric($rawPkgId) && (int)$rawPkgId > 0) {
    $packageId = (int)$rawPkgId;
} else if (!empty($rawPkgId)) {
    $decoded = base64_decode((string)$rawPkgId, true);
    if ($decoded !== false && is_numeric($decoded)) {
        $packageId = (int)$decoded;
    }
}

$stateParam = trim($_GET['state'] ?? '');
$cityParam  = trim($_GET['city'] ?? '');
$typeParam  = trim($_GET['category'] ?? ($_GET['type'] ?? ''));
$searchParam = trim($_GET['search'] ?? ($_GET['q'] ?? ''));

$pkgTitle = '';
$pkgCity = '';
$pkgState = $stateParam;
$pkgItineraryText = '';

// If package_id provided and no explicit state/city provided, fetch from package
if ($packageId > 0) {
    $pkgSql = "
        SELECT tour_package_title, tour_package_city, tour_package_state, tour_package_itinerary 
        FROM {$tblPrefix}tour_package 
        WHERE tour_package_id = '{$packageId}' AND tour_package_site_id = {$siteId}
        LIMIT 1
    ";
    if ($pkgRes = mysqli_query($connection, $pkgSql)) {
        if ($pkgRow = mysqli_fetch_assoc($pkgRes)) {
            $pkgTitle = $pkgRow['tour_package_title'] ?? '';
            $pkgCity = trim($pkgRow['tour_package_city'] ?? '');
            if (empty($pkgState)) {
                $pkgState = trim($pkgRow['tour_package_state'] ?? '');
            }
            $pkgItineraryText = $pkgRow['tour_package_itinerary'] ?? '';
        }
    }
}

// 1. Fetch Distinct Cities & States in Inventory (State-First Priority)
$targetState = strtoupper(trim($stateParam ?: $pkgState));
$stateCities = [];
$otherCities = [];
$allDbCities = [];

$allCitiesQuery = "
    SELECT 
        TRIM(property_city) AS city, 
        TRIM(property_state) AS state, 
        COUNT(property_id) AS hotel_count
    FROM {$tblPrefix}property 
    WHERE property_site_id = {$siteId} AND property_status = 1 AND property_city IS NOT NULL AND property_city <> ''
    GROUP BY TRIM(property_city), TRIM(property_state)
    ORDER BY hotel_count DESC, city ASC
";
if ($cRes = mysqli_query($connection, $allCitiesQuery)) {
    while ($cRow = mysqli_fetch_assoc($cRes)) {
        $cName = trim($cRow['city']);
        $cState = strtoupper(trim($cRow['state'] ?? ''));
        if (empty($cName)) continue;

        if (!empty($targetState) && $cState === $targetState) {
            if (!in_array($cName, $stateCities, true)) {
                $stateCities[] = $cName;
            }
        } else {
            if (!in_array($cName, $otherCities, true)) {
                $otherCities[] = $cName;
            }
        }
    }
}
$otherCities = array_values(array_diff($otherCities, $stateCities));
$allDbCities = array_merge($stateCities, $otherCities);

// 2. Build Where Conditions
$whereConditions = [
    "p.property_site_id = {$siteId}",
    "p.property_status = 1"
];

// City Filter
if (!empty($cityParam) && strtolower($cityParam) !== 'all') {
    $cityTokens = preg_split('/[,;\/|&]+/u', $cityParam);
    $escapedCities = [];
    foreach ($cityTokens as $ct) {
        $ctTrim = trim($ct);
        if (!empty($ctTrim)) {
            $escapedCities[] = "'" . mysqli_real_escape_string($connection, strtoupper($ctTrim)) . "'";
        }
    }
    if (!empty($escapedCities)) {
        $whereConditions[] = "UPPER(TRIM(p.property_city)) IN (" . implode(',', $escapedCities) . ")";
    }
}

// State Filter
if (!empty($stateParam) && strtolower($stateParam) !== 'all') {
    $escapedState = mysqli_real_escape_string($connection, strtoupper($stateParam));
    $whereConditions[] = "UPPER(TRIM(p.property_state)) = '{$escapedState}'";
}

// Property Type Filter
if (!empty($typeParam) && !in_array(strtolower($typeParam), ['all', 'custom', 'transit', 'none'], true)) {
    $escapedType = mysqli_real_escape_string($connection, strtolower($typeParam));
    // Normalize aliases
    if ($escapedType === 'resort') $escapedType = 'resorts';
    if ($escapedType === 'homestay') $escapedType = 'home_stay';
    if ($escapedType === 'camp') $escapedType = 'tent';
    if ($escapedType === 'guesthouse') $escapedType = 'guest_house';

    $whereConditions[] = "LOWER(TRIM(p.property_type)) = '{$escapedType}'";
}

// Search Filter
if (!empty($searchParam)) {
    $escapedSearch = mysqli_real_escape_string($connection, $searchParam);
    $whereConditions[] = "(p.property_title LIKE '%{$escapedSearch}%' OR p.property_city LIKE '%{$escapedSearch}%' OR p.property_address LIKE '%{$escapedSearch}%')";
}

// If only package_id was provided without specific city or state, restrict to package state & itinerary cities
if (empty($cityParam) && empty($stateParam) && $packageId > 0) {
    $combinedSearchText = strtolower($pkgTitle . ' ' . $pkgCity . ' ' . $pkgState . ' ' . $pkgItineraryText);
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
        if (!$isMatch && !empty($combinedSearchText) && stripos($combinedSearchText, $dbCityLower) !== false) {
            $isMatch = true;
        }
        if ($isMatch) {
            $mentionedCities[] = $dbCity;
        }
    }

    $packageLocationFilters = [];
    if (!empty($mentionedCities)) {
        $escapedMentionedCities = array_map(function ($c) use ($connection) {
            return "'" . mysqli_real_escape_string($connection, strtoupper(trim($c))) . "'";
        }, $mentionedCities);
        $packageLocationFilters[] = "UPPER(TRIM(p.property_city)) IN (" . implode(',', $escapedMentionedCities) . ")";
    }
    if (!empty($pkgState)) {
        $escapedPkgState = mysqli_real_escape_string($connection, strtoupper($pkgState));
        $packageLocationFilters[] = "UPPER(TRIM(p.property_state)) = '{$escapedPkgState}'";
    }

    if (!empty($packageLocationFilters)) {
        $whereConditions[] = "(" . implode(" OR ", $packageLocationFilters) . ")";
    }
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
    p.property_type
FROM {$tblPrefix}property p
{$whereClause}
GROUP BY p.property_id
ORDER BY p.property_city ASC, p.property_title ASC
";

$hotelsList = [];
$matchedCities = [];

if ($res = mysqli_query($connection, $query)) {
    while ($hRow = mysqli_fetch_assoc($res)) {
        $c = trim($hRow['property_city'] ?? '');
        if (!empty($c) && !in_array($c, $matchedCities, true)) {
            $matchedCities[] = $c;
        }

        $hotelsList[] = [
            'id'             => (int)$hRow['property_id'],
            'title'          => $hRow['property_title'],
            'city'           => $hRow['property_city'],
            'state'          => $hRow['property_state'],
            'country'        => $hRow['property_country'] ?? 'India',
            'address'        => $hRow['property_address'] ?? '',
            'type'           => $hRow['property_type'] ?? 'hotel',
            'thumbnail'      => $hRow['property_thumbnail'] ?? ''
        ];
    }
}

echo json_encode([
    'status'           => 'success',
    'total'            => count($hotelsList),
    'state'            => $stateParam ?: $pkgState,
    'city'             => $cityParam,
    'state_cities'     => $stateCities,
    'other_cities'     => $otherCities,
    'suggested_cities' => $allDbCities,
    'cities'           => !empty($matchedCities) ? $matchedCities : $allDbCities,
    'hotels'           => $hotelsList
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
