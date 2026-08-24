<?php

/**
 * V3 Itinerary API with Template
 * Endpoint: /api/v3/itinerary/show.php (and /api/v3/itinerary/render.php)
 * Method: GET, POST
 * Purpose: Renders and serves an itinerary quotation using a specified or stored template (MIT, Standard, Modern, Aleya).
 *          Used when sending itineraries to customers or generating quotation views/PDFs.
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

$rawId = $postData['id'] ?? ($postData['quotation_id'] ?? ($postData['quotationId'] ?? ($_GET['id'] ?? ($_GET['quotation_id'] ?? 0))));
$quotationId = 0;
if (is_numeric($rawId) && (int)$rawId > 0) {
    $quotationId = (int)$rawId;
} else {
    $decoded = base64_decode((string)$rawId, true);
    if ($decoded !== false && is_numeric($decoded)) {
        $quotationId = (int)$decoded;
    }
}

$requestedTemplate = trim((string)($postData['template'] ?? ($_GET['template'] ?? '')));
$format = strtolower(trim((string)($postData['format'] ?? ($_GET['format'] ?? 'html'))));

// List of available template directories
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

$quotationData = [];

if ($quotationId > 0) {
    $query = "
        SELECT 
            q.*,
            tp.tour_package_title,
            tp.tour_package_city,
            tp.tour_package_state,
            tp.tour_package_country,
            tp.tour_package_duration,
            tp.tour_package_theme,
            tp.tour_package_template,
            tp.tour_package_hotel,
            tp.tour_package_outstation_vehicle,
            tp.tour_package_localsightseen_vehicle,
            tp.tour_package_overview,
            tp.tour_package_itinerary,
            tp.tour_package_inclusions,
            tp.tour_package_exclusions,
            g.gallery_image_list,
            g.gallery_image,
            e.enquiry_first_name,
            e.enquiry_last_name,
            e.enquiry_mobile,
            e.enquiry_email,
            e.enquiry_message
        FROM {$tblPrefix}quotation q
        LEFT JOIN {$tblPrefix}tour_package tp
            ON tp.tour_package_id = q.quotation_type_id
        LEFT JOIN {$tblPrefix}gallery g
            ON g.gallery_category = tp.tour_package_id
            AND g.gallery_site_id = {$siteId}
            AND g.gallery_type = 'tour_package'
            AND g.gallery_status = 1
        LEFT JOIN {$tblPrefix}enquiry e
            ON e.enquiry_id = q.quotation_ref_id
        WHERE q.quotation_id = {$quotationId}
          AND q.quotation_site_id = {$siteId}
        LIMIT 1
    ";

    $res = mysqli_query($connection, $query);
    if ($res && mysqli_num_rows($res) > 0) {
        $quotationData = mysqli_fetch_assoc($res);
    }
}

// If quotation found, populate template variables
if (!empty($quotationData)) {
    $parsedDescription = [];
    if (!empty($quotationData['quotation_description'])) {
        $parsedDescription = json_decode($quotationData['quotation_description'], true) ?: [];
    }

    $templateToUse = !empty($requestedTemplate) ? strtolower($requestedTemplate) : '';
    if (empty($templateToUse)) {
        $templateToUse = strtolower($parsedDescription['template'] ?? ($quotationData['tour_package_template'] ?? 'mit'));
    }
    if (!in_array($templateToUse, $availableTemplates)) {
        $templateToUse = 'mit';
    }

    // Set standard template variables
    $itinerary_title   = $parsedDescription['title'] ?? ($quotationData['tour_package_title'] ?? 'Tour Itinerary');
    $tour_package_title = $itinerary_title;
    $tour_package_city = $quotationData['tour_package_city'] ?? '';
    $tour_package_state = $quotationData['tour_package_state'] ?? '';
    $tour_package_country = $quotationData['tour_package_country'] ?? '';
    $tour_package_hotel = $parsedDescription['hotel'] ?? ($quotationData['tour_package_hotel'] ?? '');
    $tour_package_theme = $parsedDescription['theme'] ?? ($quotationData['tour_package_theme'] ?? 'Standard');
    $tour_package_outstation_vehicle = $parsedDescription['outstation_vehicle'] ?? ($quotationData['tour_package_outstation_vehicle'] ?? '');
    $tour_package_localsightseen_vehicle = $parsedDescription['localsightseen_vehicle'] ?? ($quotationData['tour_package_localsightseen_vehicle'] ?? '');
    $itinerary_overview = $parsedDescription['overview'] ?? ($quotationData['tour_package_overview'] ?? '');
    $tour_package_overview = $itinerary_overview;

    $itineraries = $parsedDescription['itinerary'] ?? ($quotationData['tour_package_itinerary'] ?? []);
    if (is_string($itineraries)) {
        $itineraries = json_decode($itineraries, true) ?: [];
    }

    $inclusions = $parsedDescription['inclusions'] ?? ($quotationData['tour_package_inclusions'] ?? []);
    if (is_string($inclusions)) {
        $inclusions = array_filter(array_map('trim', explode("\n", strip_tags(str_replace(['<br>', '</li>', '<p>', ','], "\n", $inclusions)))));
    }

    $exclusions = $parsedDescription['exclusions'] ?? ($quotationData['tour_package_exclusions'] ?? []);
    if (is_string($exclusions)) {
        $exclusions = array_filter(array_map('trim', explode("\n", strip_tags(str_replace(['<br>', '</li>', '<p>', ','], "\n", $exclusions)))));
    }

    $gallery_image = $quotationData['gallery_image'] ?? '';
    $gallery_image_list = $quotationData['gallery_image_list'] ?? '';
    $tour_package_image = !empty($gallery_image) ? (defined('SITE_URL') ? rtrim(SITE_URL, '/') . '/' . ltrim($gallery_image, '/') : $gallery_image) : '';

    $quoteId = '#' . (defined('SITE_NAME') ? substr(strtoupper(preg_replace('/[^a-zA-Z]/', '', SITE_NAME)), 0, 3) : 'MIT') . '/' . $quotationId;
    $quotation_time = $quotationData['quotation_time'] ?? date('Y-m-d H:i:s');
    $travelDate = $quotationData['quotation_travel_date'] ?? '';
    $numberOfDays = (int)($quotationData['tour_package_duration'] ?? (count($itineraries) > 0 ? count($itineraries) - 1 : 0));
    $tour_package_duration = $numberOfDays;

    $price = (float)($quotationData['quotation_price'] ?? 0);
    $quotationPriceWithOutGST = number_format($price, 2);
    $gstPercent = defined('GST_PERCENTAGE') ? (float)GST_PERCENTAGE : 5.0;
    $gstAmount = number_format($price * ($gstPercent / 100), 2);
    $quotationPriceWithGST = number_format($price + ($price * ($gstPercent / 100)), 2);
    $total_discount = number_format((float)($quotationData['quotation_discount'] ?? 0), 2);

    $passangerInfo = 'Custom Quotation';
    if (!empty($quotationData['enquiry_first_name'])) {
        $passangerInfo = trim($quotationData['enquiry_first_name'] . ' ' . ($quotationData['enquiry_last_name'] ?? ''));
    }

} else {
    // If raw draft content was posted without a quotation ID
    $templateToUse = in_array(strtolower($requestedTemplate), $availableTemplates) ? strtolower($requestedTemplate) : 'mit';
    
    $itinerary_title   = $postData['title'] ?? 'Tour Itinerary Proposal';
    $tour_package_title = $itinerary_title;
    $tour_package_city = $postData['city'] ?? 'Destination';
    $tour_package_state = $postData['state'] ?? '';
    $tour_package_country = $postData['country'] ?? '';
    $tour_package_hotel = $postData['hotel'] ?? '';
    $tour_package_theme = $postData['theme'] ?? 'Standard';
    $tour_package_outstation_vehicle = $postData['outstationVehicle'] ?? ($postData['outstation_vehicle'] ?? '');
    $tour_package_localsightseen_vehicle = $postData['localSightseeingVehicle'] ?? ($postData['localsightseen_vehicle'] ?? '');
    $itinerary_overview = $postData['overview'] ?? '';
    $tour_package_overview = $itinerary_overview;

    $itineraries = $postData['days'] ?? ($postData['itinerary'] ?? []);
    if (is_string($itineraries)) {
        $itineraries = json_decode($itineraries, true) ?: [];
    }

    $inclusions = $postData['inclusions'] ?? [];
    if (is_string($inclusions)) {
        $inclusions = array_filter(array_map('trim', explode("\n", strip_tags(str_replace(['<br>', '</li>', '<p>', ','], "\n", $inclusions)))));
    }

    $exclusions = $postData['exclusions'] ?? [];
    if (is_string($exclusions)) {
        $exclusions = array_filter(array_map('trim', explode("\n", strip_tags(str_replace(['<br>', '</li>', '<p>', ','], "\n", $exclusions)))));
    }

    $tour_package_image = $postData['coverImage'] ?? ($postData['image'] ?? '');
    $quoteId = '#DRAFT-PROPOSAL';
    $quotation_time = date('Y-m-d H:i:s');
    $travelDate = date('Y-m-d', strtotime('+7 days'));
    $numberOfDays = count($itineraries) > 0 ? count($itineraries) - 1 : 1;
    $tour_package_duration = $numberOfDays;

    $quotationPriceWithOutGST = '0.00';
    $gstPercent = 5;
    $gstAmount = '0.00';
    $quotationPriceWithGST = '0.00';
    $total_discount = '0.00';
    $passangerInfo = 'Valued Traveler';
}

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

// Deliver output according to requested format
if ($format === 'json') {
    $previewHtml = preg_replace('/<div\s+class=["\']v3-pdf-actions-bar["\'].*?<\/div>/is', '', $renderedHtml);
    header("Content-type: application/json; charset=utf-8");
    $encodedId = base64_encode((string)$quotationId);
    echo json_encode([
        'status'       => 'success',
        'quotation_id' => $quotationId,
        'encoded_id'   => $encodedId,
        'template'     => $templateToUse,
        'title'        => $itinerary_title,
        'html'         => $previewHtml,
        'view_url'     => (defined('SITE_DOMAIN') ? 'https://' . SITE_DOMAIN : '') . "/view-itinerary/{$encodedId}/",
        'pdf_url'      => (defined('SITE_DOMAIN') ? 'https://' . SITE_DOMAIN : '') . "/download-itinerary/{$encodedId}/",
        'available_templates' => $availableTemplates
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} else {
    header("Content-type: text/html; charset=utf-8");
    echo $renderedHtml;
}
