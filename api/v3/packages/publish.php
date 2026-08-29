<?php
/**
 * V3 Tour Package Create & Publish API
 * Endpoint: /api/v3/packages/publish/ or /api/v3/packages/create/
 * Method: POST
 * Description: Creates a new tour package itinerary with V3 standards, link generation, and meta records.
 */

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

if (!defined('LOGIN_CHECK')) {
    define('LOGIN_CHECK', $_SERVER['DOCUMENT_ROOT'] . '/lib/session/index.php');
}
if (file_exists(LOGIN_CHECK)) {
    require_once LOGIN_CHECK;
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : 1;
$userId    = defined('USER_ID')      ? (int) USER_ID : 1;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status'  => 'error',
        'msg' => 'Method Not Allowed. Use POST.'
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

// 1. Read & Sanitize Basic Info
$packageName            = isset($_POST['packageName']) ? mysqli_real_escape_string($connection, stripslashes(trim((string)$_POST['packageName']))) : '';
$travelStyle            = isset($_POST['travelStyle']) && $_POST['travelStyle'] !== 'select' ? mysqli_real_escape_string($connection, stripslashes(trim((string)$_POST['travelStyle']))) : 'standard';
$template               = isset($_POST['tourPackageTemplate']) && $_POST['tourPackageTemplate'] !== 'select' ? mysqli_real_escape_string($connection, trim((string)$_POST['tourPackageTemplate'])) : 'mit';
if (empty($template)) {
    $template = 'mit';
}
$packageType            = isset($_POST['packageType']) ? mysqli_real_escape_string($connection, trim((string)$_POST['packageType'])) : 'domestic';
$hotel                  = isset($_POST['hotel']) && $_POST['hotel'] !== 'select' ? mysqli_real_escape_string($connection, trim((string)$_POST['hotel'])) : '3_star_hotel';
$outstationVehicle      = isset($_POST['outstationVehicle']) && $_POST['outstationVehicle'] !== 'select' ? mysqli_real_escape_string($connection, strtolower(stripslashes(trim((string)$_POST['outstationVehicle'])))) : '';
$localSighseeignVehicle = isset($_POST['localSighseeignVehicle']) && $_POST['localSighseeignVehicle'] !== 'select' ? mysqli_real_escape_string($connection, strtolower(stripslashes(trim((string)$_POST['localSighseeignVehicle'])))) : '';
$overview               = isset($_POST['overview']) ? mysqli_real_escape_string($connection, stripslashes(trim((string)$_POST['overview']))) : '';

// Function to clean HTML markup to comma text
function cleanV3HtmlToCommaText($html, $connection) {
    if (empty($html)) return '';
    $text = strip_tags((string)$html, '<li>');
    $items = preg_split('/<\/?li>/i', $text, -1, PREG_SPLIT_NO_EMPTY);
    $cleanedItems = array_values(array_filter(array_map('trim', $items)));
    if (!empty($cleanedItems)) {
        return mysqli_real_escape_string($connection, implode(', ', $cleanedItems));
    }
    $plain = trim(strip_tags((string)$html));
    return mysqli_real_escape_string($connection, $plain);
}

$inclusion = cleanV3HtmlToCommaText(stripslashes((string)($_POST['inclusion'] ?? '')), $connection);
$exclusion = cleanV3HtmlToCommaText(stripslashes((string)($_POST['exclusion'] ?? '')), $connection);

$startCity = isset($_POST['startCity']) ? mysqli_real_escape_string($connection, stripslashes(trim((string)$_POST['startCity']))) : '';
$endCity   = isset($_POST['endCity']) ? mysqli_real_escape_string($connection, stripslashes(trim((string)$_POST['endCity']))) : $startCity;

if (empty($packageName)) {
    http_response_code(422);
    echo json_encode([
        'status'  => 'error',
        'msg' => 'Package Title / Tour Name is required.'
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

// 2. Parse Location & Destination City, State and Country
$packageType = isset($_POST['packageType']) ? strtolower(trim((string)$_POST['packageType'])) : 'domestic';

$locationRaw = isset($_POST['location']) ? stripslashes((string)$_POST['location']) : '';
if (empty($locationRaw) && isset($_POST['destination'])) {
    if (is_array($_POST['destination'])) {
        $locationRaw = implode(', ', $_POST['destination']);
    } else {
        $locationRaw = (string)$_POST['destination'];
    }
}
$placeInfo = array_values(array_filter(array_map('trim', explode(',', $locationRaw))));
$cityName = $placeInfo[0] ?? ($startCity ?: 'Destination');
$stateName = isset($placeInfo[1]) ? $placeInfo[1] : (count($placeInfo) > 2 ? $placeInfo[count($placeInfo) - 2] : '');

if ($packageType === 'domestic') {
    $countryName = 'India';
} else {
    // For international packages, user-selected country has highest priority
    $userCountry = isset($_POST['country']) ? stripslashes(trim((string)$_POST['country'])) : '';
    if (!empty($userCountry) && strtolower($userCountry) !== 'select') {
        $countryName = $userCountry;
    } else {
        $countryName = !empty($placeInfo) && count($placeInfo) > 1 ? end($placeInfo) : 'International';
    }
}

$cityNameEsc    = mysqli_real_escape_string($connection, stripslashes($cityName));
$stateNameEsc   = mysqli_real_escape_string($connection, stripslashes($stateName));
$countryNameEsc = mysqli_real_escape_string($connection, stripslashes($countryName));

// 3. Process Day-Wise Itinerary Items
$dayTitles       = isset($_POST['dayTitle']) && is_array($_POST['dayTitle']) ? $_POST['dayTitle'] : [];
$dayContents     = isset($_POST['dayContent']) && is_array($_POST['dayContent']) ? $_POST['dayContent'] : [];
$dayStays        = isset($_POST['dayStay']) && is_array($_POST['dayStay']) ? $_POST['dayStay'] : [];
$dayFeatureImgs  = isset($_POST['dayFeatureImage']) && is_array($_POST['dayFeatureImage']) ? $_POST['dayFeatureImage'] : [];
$dayGalleries    = isset($_POST['dayGallery']) && is_array($_POST['dayGallery']) ? $_POST['dayGallery'] : [];

$itineraryList = [];
$totalDays = count($dayTitles);

if ($totalDays > 0) {
    for ($i = 0; $i < $totalDays; $i++) {
        $dayNum     = $i + 1;
        $title      = stripslashes(trim($dayTitles[$i] ?? "Day {$dayNum}"));
        $content    = stripslashes(trim($dayContents[$i] ?? ""));
        $stay       = stripslashes(trim($dayStays[$i] ?? ""));
        $featureImg = trim($dayFeatureImgs[$i] ?? "");
        
        $rawGal = $dayGalleries[$i] ?? [];
        if (is_string($rawGal)) {
            $parsedGal = json_decode($rawGal, true);
            $gallery = is_array($parsedGal) ? $parsedGal : array_filter(array_map('trim', explode(',', $rawGal)));
        } else {
            $gallery = is_array($rawGal) ? $rawGal : [];
        }

        $itineraryList[] = [
            'day'           => $title,
            'overview'      => $content,
            'stay'          => $stay,
            'feature_image' => $featureImg,
            'gallery'       => array_values($gallery)
        ];
    }
}

$encodedItinerary = mysqli_real_escape_string($connection, json_encode($itineraryList, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

// Calculate duration (nights)
$duration = isset($_POST['duration']) ? (int)$_POST['duration'] : max(0, $totalDays - 1);
if ($duration <= 0 && $totalDays > 1) {
    $duration = $totalDays - 1;
}

// 4. Execute Transaction to create Tour Package, Link, and Meta
mysqli_query($connection, "START TRANSACTION");

$insertQuery = "
    INSERT INTO {$tblPrefix}tour_package (
        tour_package_site_id,
        tour_package_created_by,
        tour_package_title,
        tour_package_theme,
        tour_package_template,
        tour_package_start_from,
        tour_package_end_to,
        tour_package_city,
        tour_package_duration,
        tour_package_overview,
        tour_package_itinerary,
        tour_package_inclusions,
        tour_package_exclusions,
        tour_package_hotel,
        tour_package_state,
        tour_package_country,
        tour_package_type,
        tour_package_outstation_vehicle,
        tour_package_localsightseen_vehicle,
        tour_package_status,
        tour_package_created_at
    ) VALUES (
        {$siteId},
        {$userId},
        '{$packageName}',
        '{$travelStyle}',
        '{$template}',
        '{$startCity}',
        '{$endCity}',
        '{$cityNameEsc}',
        '{$duration}',
        '{$overview}',
        '{$encodedItinerary}',
        '{$inclusion}',
        '{$exclusion}',
        '{$hotel}',
        '{$stateNameEsc}',
        '{$countryNameEsc}',
        '{$packageType}',
        '{$outstationVehicle}',
        '{$localSighseeignVehicle}',
        2,
        NOW()
    )
";

$insertResult = mysqli_query($connection, $insertQuery);
$packageId = mysqli_insert_id($connection);

if (!$insertResult || $packageId <= 0) {
    $err = DEVELOPMENT_MODE ? mysqli_error($connection) : 'Database insert failed.';
    mysqli_query($connection, "ROLLBACK");
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'msg' => 'Failed to create tour package: ' . $err
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

// Helper for SEO friendly slug
function generateV3PackageSlug($string) {
    $string = preg_replace('/[^A-Za-z0-9\s]/', '_', (string)$string);
    $string = preg_replace('/\s+/', '_', $string);
    return strtolower('/' . trim($string, '_') . '-' . time() . '/');
}

$urlSlug = generateV3PackageSlug($packageName);

$insertLink = "
    INSERT INTO {$tblPrefix}link (
        link_url,
        link_relation_id,
        link_status,
        link_type,
        link_owner_id,
        link_site_id,
        link_creation,
        link_published
    ) VALUES (
        '{$urlSlug}',
        '{$packageId}',
        200,
        'tour_package',
        {$userId},
        {$siteId},
        NOW(),
        NOW()
    )
";
$linkRes = mysqli_query($connection, $insertLink);
$linkId = mysqli_insert_id($connection);

$insertMeta = "
    INSERT INTO {$tblPrefix}meta (
        meta_relation_id,
        meta_site_id,
        meta_type,
        meta_robots
    ) VALUES (
        '{$linkId}',
        {$siteId},
        'tour_package',
        'index, follow'
    )
";
$metaRes = mysqli_query($connection, $insertMeta);

if ($linkRes && $metaRes) {
    mysqli_query($connection, "COMMIT");
    http_response_code(201);
    echo json_encode([
        'status'    => 'success',
        'msg'       => 'Tour Package created successfully.',
        'id'        => base64_encode((string)$packageId),
        'packageId' => $packageId,
        'duration'  => $duration,
        'days'      => $totalDays
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} else {
    mysqli_query($connection, "ROLLBACK");
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'msg' => 'Failed to generate public links for the new package.'
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}
