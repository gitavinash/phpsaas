<?php

/**
 * V3 Package Itinerary Preview API with Template
 * Endpoint: /api/v3/packages/render.php (and /api/v3/packages/preview.php)
 * Method: GET, POST
 * Purpose: Renders and previews a package itinerary in any chosen template (MIT, Standard, Modern, Aleya)
 *          used during package editing / previewing.
 */

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
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : 0;

// Read JSON or GET/POST inputs
$rawInput = file_get_contents('php://input');
$postData = json_decode($rawInput, true);
if (!is_array($postData) || empty($postData)) {
    $postData = !empty($_POST) ? $_POST : $_GET;
}

$rawId = $postData['id'] ?? ($postData['packageId'] ?? ($postData['package_id'] ?? ($_GET['id'] ?? ($_GET['packageId'] ?? 0))));
$packageId = 0;
if (is_numeric($rawId) && (int)$rawId > 0) {
    $packageId = (int)$rawId;
} else {
    $decoded = base64_decode((string)$rawId, true);
    if ($decoded !== false && is_numeric($decoded)) {
        $packageId = (int)$decoded;
    }
}

$requestedTemplate = trim((string)($postData['template'] ?? ($_GET['template'] ?? '')));
$format = strtolower(trim((string)($postData['format'] ?? ($_GET['format'] ?? 'html'))));

// List of available templates
$docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : (defined('ROOT') ? ROOT : realpath(__DIR__ . '/../../../'));
$availableTemplates = [];
$templatesBaseDir = $docRoot . '/app/dashboard/v3/default/desktop/templates/itinerary';
if (is_dir($templatesBaseDir)) {
    $items = scandir($templatesBaseDir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..' || !is_dir($templatesBaseDir . '/' . $item)) continue;
        if (file_exists($templatesBaseDir . '/' . $item . '/index.php')) {
            $availableTemplates[] = strtolower($item);
        }
    }
}
if (empty($availableTemplates)) {
    $availableTemplates = ['mit', 'standard', 'modern', 'aleya'];
}

$packageRow = [];
if ($packageId > 0) {
    $query = "
        SELECT 
            tp.*,
            g.gallery_image,
            g.gallery_image_list
        FROM {$tblPrefix}tour_package tp
        LEFT JOIN {$tblPrefix}gallery g
            ON g.gallery_category = tp.tour_package_id
            AND g.gallery_site_id = {$siteId}
            AND g.gallery_type = 'tour_package'
            AND g.gallery_status = 1
        WHERE tp.tour_package_id = {$packageId}
          AND tp.tour_package_site_id = {$siteId}
        LIMIT 1
    ";

    $res = mysqli_query($connection, $query);
    if ($res && mysqli_num_rows($res) > 0) {
        $packageRow = mysqli_fetch_assoc($res);
    }
}

// Select template
$templateToUse = !empty($requestedTemplate) ? strtolower($requestedTemplate) : '';
if (empty($templateToUse)) {
    $templateToUse = strtolower($postData['template'] ?? ($packageRow['tour_package_template'] ?? 'mit'));
}
if (!in_array($templateToUse, $availableTemplates)) {
    $templateToUse = 'mit';
}

// Extract or overlay package fields
$tour_package_title = !empty($postData['title']) ? trim($postData['title']) : ($packageRow['tour_package_title'] ?? 'Tour Package Itinerary');
$itinerary_title = $tour_package_title;

$tour_package_city = !empty($postData['city']) ? trim($postData['city']) : ($packageRow['tour_package_city'] ?? 'Destination');
$tour_package_state = $postData['state'] ?? ($packageRow['tour_package_state'] ?? '');
$tour_package_country = $postData['country'] ?? ($packageRow['tour_package_country'] ?? '');
$tour_package_hotel = !empty($postData['hotel']) ? trim($postData['hotel']) : ($packageRow['tour_package_hotel'] ?? '');
$tour_package_theme = !empty($postData['theme']) ? trim($postData['theme']) : ($packageRow['tour_package_theme'] ?? 'Standard');

$tour_package_outstation_vehicle = !empty($postData['outstationVehicle']) ? trim($postData['outstationVehicle']) : ($postData['outstation_vehicle'] ?? ($packageRow['tour_package_outstation_vehicle'] ?? ''));
$tour_package_localsightseen_vehicle = !empty($postData['localSightseeingVehicle']) ? trim($postData['localSightseeingVehicle']) : ($postData['localsightseen_vehicle'] ?? ($packageRow['tour_package_localsightseen_vehicle'] ?? ''));

$tour_package_overview = isset($postData['overview']) ? $postData['overview'] : ($packageRow['tour_package_overview'] ?? '');
$itinerary_overview = $tour_package_overview;

$galleryImageList = [];
if (!empty($packageRow['gallery_image_list'])) {
    $galleryImageList = json_decode($packageRow['gallery_image_list'], true) ?: [];
}

// Days list
$itineraries = isset($postData['days']) ? $postData['days'] : (isset($postData['itinerary']) ? $postData['itinerary'] : ($packageRow['tour_package_itinerary'] ?? []));
if (is_string($itineraries)) {
    $itineraries = json_decode($itineraries, true) ?: [];
}

foreach ($itineraries as $idx => &$dayItem) {
    if (empty($dayItem['feature_image']) && !empty($dayItem['image'])) {
        $dayItem['feature_image'] = $dayItem['image'];
    }
    if (empty($dayItem['feature_image']) && !empty($galleryImageList[$idx]['path'])) {
        $dayItem['feature_image'] = $galleryImageList[$idx]['path'];
    }
}
unset($dayItem);

// Inclusions list
$inclusions = isset($postData['inclusions']) ? $postData['inclusions'] : ($packageRow['tour_package_inclusions'] ?? []);

// Exclusions list
$exclusions = isset($postData['exclusions']) ? $postData['exclusions'] : ($packageRow['tour_package_exclusions'] ?? []);

// Image handling
$tour_package_image = !empty($postData['coverImage']) ? $postData['coverImage'] : (!empty($postData['image']) ? $postData['image'] : (!empty($packageRow['gallery_image']) ? $packageRow['gallery_image'] : (!empty($packageRow['tour_package_image']) ? $packageRow['tour_package_image'] : (!empty($galleryImageList[0]['path']) ? $galleryImageList[0]['path'] : ''))));

$gallery_image = $tour_package_image;
$gallery_image_list = $packageRow['gallery_image_list'] ?? '';

$totalDaysCount = !empty($itineraries) ? count($itineraries) : 1;
$totalNightsCount = max(1, $totalDaysCount - 1);
if (!empty($postData['duration']) || !empty($packageRow['tour_package_duration'])) {
    $dur = (int)(!empty($postData['duration']) ? $postData['duration'] : $packageRow['tour_package_duration']);
    if ($dur > 0) {
        $totalNightsCount = $dur;
        $totalDaysCount = $dur + 1;
    }
}
$tour_package_duration = $totalNightsCount;
$numberOfDays = $totalNightsCount;

$quoteId = '#PKG-' . ($packageId > 0 ? $packageId : 'PREVIEW');
$quotation_time = date('Y-m-d H:i:s');
$travelDate = !empty($postData['travelDate']) ? $postData['travelDate'] : (!empty($postData['travel_date']) ? $postData['travel_date'] : date('Y-m-d', strtotime('+14 days')));
$quotationPriceWithOutGST = '0.00';
$gstPercent = 5;
$gstAmount = '0.00';
$quotationPriceWithGST = '0.00';
$total_discount = '0.00';
$passangerInfo = 'Package Preview';

// Locate template file
$templateFile = $templatesBaseDir . '/' . $templateToUse . '/index.php';
if (!file_exists($templateFile)) {
    $templateFile = $templatesBaseDir . '/' . $templateToUse . '.php';
}
if (!file_exists($templateFile)) {
    $templateFile = $templatesBaseDir . '/mit/index.php';
}

// Render template using output buffering
ob_start();
include $templateFile;
$renderedHtml = ob_get_clean();

// Deliver output according to format
if ($format === 'json') {
    $previewHtml = preg_replace('/<div\s+class=["\']v3-pdf-actions-bar["\'].*?<\/div>/is', '', $renderedHtml);
    header("Content-type: application/json; charset=utf-8");
    echo json_encode([
        'status'              => 'success',
        'package_id'          => $packageId,
        'template'            => $templateToUse,
        'title'               => $tour_package_title,
        'html'                => $previewHtml,
        'available_templates' => $availableTemplates
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} else {
    header("Content-type: text/html; charset=utf-8");
    echo $renderedHtml;
}
