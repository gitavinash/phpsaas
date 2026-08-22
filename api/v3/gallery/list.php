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

$rawId = $_GET['packageId'] ?? ($_GET['id'] ?? 0);
if (is_numeric($rawId) && (int)$rawId > 0) {
    $packageId = (int)$rawId;
} else {
    $decoded = base64_decode($rawId, true);
    $packageId = ($decoded !== false && is_numeric($decoded)) ? (int)$decoded : 0;
}

if ($packageId <= 0) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Invalid or missing package ID.'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

$query = "
    SELECT tp.tour_package_id, tp.tour_package_title, tp.tour_package_city, tp.tour_package_country,
           tp.tour_package_theme, tp.tour_package_duration, tp.tour_package_itinerary, tp.tour_package_status,
           g.gallery_image_list, g.gallery_id, g.gallery_image,
           l.link_url
    FROM {$tblPrefix}tour_package AS tp
    LEFT JOIN {$tblPrefix}gallery AS g
        ON g.gallery_category = tp.tour_package_id
        AND g.gallery_site_id = {$siteId}
        AND g.gallery_type = 'tour_package'
        AND g.gallery_status = 1
    LEFT JOIN {$tblPrefix}link AS l
        ON l.link_relation_id = tp.tour_package_id
        AND l.link_type = 'tour_package'
        AND l.link_site_id = {$siteId}
    WHERE tp.tour_package_id = '{$packageId}'
    AND tp.tour_package_site_id = {$siteId}
    LIMIT 1
";

$res = mysqli_query($connection, $query);
if (!$res || mysqli_num_rows($res) === 0) {
    http_response_code(404);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Package not found or access denied.'
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

$row = mysqli_fetch_assoc($res);

$title       = $row['tour_package_title'] ?? 'Tour Package';
$city        = $row['tour_package_city'] ?? '';
$country     = $row['tour_package_country'] ?? '';
$theme       = $row['tour_package_theme'] ?? 'standard';
$duration    = (int)($row['tour_package_duration'] ?? 0);
$itinerary   = $row['tour_package_itinerary'] ?? '';
$galleryId   = (int)($row['gallery_id'] ?? 0);
$featuredImg = $row['gallery_image'] ?? '';
$linkUrl     = $row['link_url'] ?? '';

$themeClean = strtolower(trim($theme ?: 'standard'));
$themeClass = 'pkg-theme-' . preg_replace('/[^a-z0-9_-]/', '_', $themeClean);
$themeLabel = ucwords(str_replace('_', ' ', $themeClean));

$itineraryArr = [];
if (!empty($itinerary)) {
    $itineraryArr = json_decode($itinerary, true) ?? [];
}
$totalDays = max(count($itineraryArr), $duration + 1, 1);

$galleryImagesList = [];
if (!empty($row['gallery_image_list'])) {
    $galleryImagesList = json_decode($row['gallery_image_list'], true) ?? [];
}

// Build assigned map & count assigned/unassigned
$assignedDayMap = [];
$assignedPhotosCount = 0;
$unassignedPhotosCount = 0;

$formattedImages = [];
foreach ($galleryImagesList as $idx => $img) {
    $imgPath = $img['path'] ?? '';
    $imgDay  = isset($img['day']) ? (int)$img['day'] : 0;
    $imgName = $img['name'] ?? ('Image ' . ($idx + 1));
    $isFeat  = (!empty($featuredImg) && $featuredImg === $imgPath);

    if ($imgDay > 0) {
        $assignedDayMap[$imgDay] = $imgPath;
        $assignedPhotosCount++;
    } else {
        $unassignedPhotosCount++;
    }

    $formattedImages[] = [
        'path'       => $imgPath,
        'day'        => $imgDay,
        'name'       => $imgName,
        'isFeatured' => $isFeat
    ];
}

// Sort images: Day 1..N first, then unassigned
usort($formattedImages, function ($a, $b) {
    $dayA = $a['day'] > 0 ? $a['day'] : 999999;
    $dayB = $b['day'] > 0 ? $b['day'] : 999999;
    if ($dayA === $dayB) return 0;
    return ($dayA < $dayB) ? -1 : 1;
});

// Count unique days covered
$daysAssignedCount = 0;
for ($d = 1; $d <= $totalDays; $d++) {
    if (isset($assignedDayMap[$d]) && !empty($assignedDayMap[$d])) {
        $daysAssignedCount++;
    }
}
$daysMissingCount = max(0, $totalDays - $daysAssignedCount);

$defaultSearchQuery = !empty($city) ? $city : (!empty($country) ? $country : $title);

echo json_encode([
    'status' => 'success',
    'package' => [
        'id'                 => $packageId,
        'encodedId'          => base64_encode($packageId),
        'title'              => $title,
        'city'               => $city,
        'country'            => $country,
        'theme'              => $theme,
        'themeClass'         => $themeClass,
        'themeLabel'         => $themeLabel,
        'duration'           => $duration,
        'totalDays'          => $totalDays,
        'linkUrl'            => $linkUrl,
        'defaultSearchQuery' => $defaultSearchQuery
    ],
    'galleryId' => $galleryId,
    'stats' => [
        'totalPhotos'      => count($formattedImages),
        'daysCovered'      => $daysAssignedCount,
        'daysMissing'      => $daysMissingCount,
        'totalDays'        => $totalDays,
        'assignedPhotos'   => $assignedPhotosCount,
        'unassignedPhotos' => $unassignedPhotosCount,
        'isFeaturedSet'    => !empty($featuredImg),
        'featuredImage'    => $featuredImg
    ],
    'assignedDayMap' => $assignedDayMap,
    'images'         => $formattedImages
], JSON_UNESCAPED_SLASHES);
