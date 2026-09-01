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

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : 0;

// Fetch company profile / contact / banking / policy configurations if DB is connected
$company_social_media = [];
$bankDetailsMap = [];
if (isset($connection) && $siteId > 0) {
    $cfgRes = @mysqli_query($connection, "
        SELECT site_configuration_key, site_configuration_value 
        FROM {$tblPrefix}site_configuration 
        WHERE site_configuration_site_id = {$siteId} 
          AND site_configuration_key IN (
              'company_name', 'site_mobile', 'company_mobile', 'site_email', 'company_email', 
              'company_whatsapp_number', 'company_address', 'company_site_domain', 'company_social_media',
              'bank_name', 'bank_ac_name', 'bank_ac_number', 'bank_ifsc', 'bank_address', 'bank_details', 
              'upi_id', 'upi_details', 'pg_upi_address',
              'payment_policy', 'cancellation_policy', 'terms_and_conditions'
          )
          AND site_configuration_status = 1
    ");
    $dbConfigMap = [];
    if ($cfgRes) {
        while ($cRow = mysqli_fetch_assoc($cfgRes)) {
            $dbConfigMap[$cRow['site_configuration_key']] = $cRow['site_configuration_value'];
        }
    }

    if (!defined('COMPANY_WHATSAPP_NUMBER')) {
        $cWa = $dbConfigMap['company_whatsapp_number'] ?? ($dbConfigMap['site_mobile'] ?? ($dbConfigMap['company_mobile'] ?? ''));
        define('COMPANY_WHATSAPP_NUMBER', $cWa);
    }
    if (!defined('COMPANY_ADDRESS')) {
        $cAddr = $dbConfigMap['company_address'] ?? '';
        define('COMPANY_ADDRESS', $cAddr);
    }
    if (!defined('COMPANY_SITE_DOMAIN')) {
        $cDom = $dbConfigMap['company_site_domain'] ?? (defined('SITE_DOMAIN') ? SITE_DOMAIN : '');
        define('COMPANY_SITE_DOMAIN', $cDom);
    }
    if (!empty($dbConfigMap['company_social_media'])) {
        $company_social_media = json_decode($dbConfigMap['company_social_media'], true) ?: $dbConfigMap['company_social_media'];
    }

    // Company Bank Account Details
    if (!defined('COMPANY_BANK_NAME') && !empty($dbConfigMap['bank_name'])) define('COMPANY_BANK_NAME', $dbConfigMap['bank_name']);
    if (!defined('COMPANY_BANK_AC_NAME') && !empty($dbConfigMap['bank_ac_name'])) define('COMPANY_BANK_AC_NAME', $dbConfigMap['bank_ac_name']);
    if (!defined('COMPANY_BANK_AC_NUMBER') && !empty($dbConfigMap['bank_ac_number'])) define('COMPANY_BANK_AC_NUMBER', $dbConfigMap['bank_ac_number']);
    if (!defined('COMPANY_BANK_IFSC') && !empty($dbConfigMap['bank_ifsc'])) define('COMPANY_BANK_IFSC', $dbConfigMap['bank_ifsc']);
    if (!defined('COMPANY_BANK_ADDRESS') && !empty($dbConfigMap['bank_address'])) define('COMPANY_BANK_ADDRESS', $dbConfigMap['bank_address']);
    if (!defined('COMPANY_BANK_DETAILS') && !empty($dbConfigMap['bank_details'])) define('COMPANY_BANK_DETAILS', $dbConfigMap['bank_details']);
    
    $resolvedUpi = $dbConfigMap['upi_id'] ?? ($dbConfigMap['upi_details'] ?? ($dbConfigMap['pg_upi_address'] ?? ''));
    if (!defined('COMPANY_UPI_ID') && !empty($resolvedUpi)) define('COMPANY_UPI_ID', $resolvedUpi);

    // Standard Policies from site configuration
    if (!defined('COMPANY_PAYMENT_POLICY') && !empty($dbConfigMap['payment_policy'])) define('COMPANY_PAYMENT_POLICY', $dbConfigMap['payment_policy']);
    if (!defined('COMPANY_CANCELLATION_POLICY') && !empty($dbConfigMap['cancellation_policy'])) define('COMPANY_CANCELLATION_POLICY', $dbConfigMap['cancellation_policy']);
    if (!defined('COMPANY_TERMS_AND_CONDITIONS') && !empty($dbConfigMap['terms_and_conditions'])) define('COMPANY_TERMS_AND_CONDITIONS', $dbConfigMap['terms_and_conditions']);

    // Fetch Policy Pages from DB (tbl_page where page_type = 'policy' matching api/v3/policy API)
    $dbPolicyPages = [
        'payment_policy'       => $dbConfigMap['payment_policy'] ?? '',
        'cancellation_policy'  => $dbConfigMap['cancellation_policy'] ?? '',
        'terms_and_conditions' => $dbConfigMap['terms_and_conditions'] ?? '',
        'itinerary_policy'     => '',
        'privacy_policy'       => ''
    ];

    $polQuery = @mysqli_query($connection, "
        SELECT p.page_id, p.page_title, p.page_description, l.link_url 
        FROM {$tblPrefix}page p
        INNER JOIN {$tblPrefix}link l ON l.link_relation_id = p.page_id
        WHERE p.page_type = 'policy' 
          AND l.link_type = 'page' 
          AND p.page_status = '1' 
          " . ($siteId > 0 ? "AND l.link_site_id = {$siteId}" : "") . "
        ORDER BY p.page_id ASC
    ");

    if ($polQuery && mysqli_num_rows($polQuery) > 0) {
        while ($pRow = mysqli_fetch_assoc($polQuery)) {
            $pTitle = strtolower(trim($pRow['page_title'] ?? ''));
            $pSlug  = strtolower(trim($pRow['link_url'] ?? ''));
            $pDesc  = trim($pRow['page_description'] ?? '');
            if (empty($pDesc)) continue;

            if (strpos($pTitle, 'payment') !== false || strpos($pSlug, 'payment') !== false) {
                $dbPolicyPages['payment_policy'] = $pDesc;
            } elseif (strpos($pTitle, 'cancellation') !== false || strpos($pTitle, 'refund') !== false || strpos($pSlug, 'cancellation') !== false || strpos($pSlug, 'refund') !== false) {
                $dbPolicyPages['cancellation_policy'] = $pDesc;
            } elseif (strpos($pTitle, 'terms') !== false || strpos($pTitle, 'condition') !== false || strpos($pSlug, 'terms') !== false) {
                $dbPolicyPages['terms_and_conditions'] = $pDesc;
            } elseif (strpos($pSlug, 'itinenary-policy') !== false || strpos($pSlug, 'itinerary-policy') !== false || strpos($pTitle, 'itinenary') !== false || strpos($pTitle, 'itinerary policy') !== false) {
                $dbPolicyPages['itinerary_policy'] = $pDesc;
            } elseif (strpos($pTitle, 'privacy') !== false || strpos($pSlug, 'privacy') !== false) {
                $dbPolicyPages['privacy_policy'] = $pDesc;
            }
        }
    }

    $payment_policy       = !empty($dbPolicyPages['payment_policy']) ? $dbPolicyPages['payment_policy'] : (defined('COMPANY_PAYMENT_POLICY') ? COMPANY_PAYMENT_POLICY : '');
    $cancellation_policy  = !empty($dbPolicyPages['cancellation_policy']) ? $dbPolicyPages['cancellation_policy'] : (defined('COMPANY_CANCELLATION_POLICY') ? COMPANY_CANCELLATION_POLICY : '');
    $terms_and_conditions = !empty($dbPolicyPages['terms_and_conditions']) ? $dbPolicyPages['terms_and_conditions'] : (defined('COMPANY_TERMS_AND_CONDITIONS') ? COMPANY_TERMS_AND_CONDITIONS : '');
    $itinerary_policy     = $dbPolicyPages['itinerary_policy'] ?? '';

    $bankDetailsMap = [
        'bank_name'       => $dbConfigMap['bank_name'] ?? '',
        'bank_ac_name'    => $dbConfigMap['bank_ac_name'] ?? '',
        'bank_ac_number'  => $dbConfigMap['bank_ac_number'] ?? '',
        'bank_ifsc'       => $dbConfigMap['bank_ifsc'] ?? '',
        'bank_address'    => $dbConfigMap['bank_address'] ?? '',
        'bank_details'    => $dbConfigMap['bank_details'] ?? '',
        'upi_id'          => $resolvedUpi,
        'upi_details'     => $resolvedUpi
    ];
}

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

// Hide floating actions bar when previewing inside modal
$hideActions = isset($postData['hideActions']) ? !empty($postData['hideActions']) : (!empty($_GET['hide_actions']) || !empty($_GET['preview']) || !empty($postData['preview']) || $format === 'preview');
$isPreview = $hideActions;

// Parse auto-download parameter (?download=true|false or ?pdf=1)
$downloadParam = $postData['download'] ?? ($_GET['download'] ?? ($postData['pdf'] ?? ($_GET['pdf'] ?? 'false')));
$autoDownload = ($downloadParam === true || $downloadParam === 'true' || $downloadParam === '1' || $downloadParam === 'yes' || !empty($_GET['auto_download']));
$isDownload = $autoDownload;

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
            e.enquiry_message,
            e.enquiry_agent
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

    // Fetch salesperson (assigned enquiry_agent / quotation_sent_by / session user / site consultant)
    $salesPersonName     = '';
    $salesPersonEmail    = '';
    $salesPersonMobile   = '';
    $salesPersonWhatsapp = '';
    $salesPersonImage    = '';
    $salesPersonDesig    = 'Travel Consultant';

    // Priority 1: Assigned agent on enquiry
    $assignedAgentId = (int)($quotationData['enquiry_agent'] ?? 0);
    if ($assignedAgentId === 0 && !empty($quotationData['quotation_ref_id'])) {
        $enqRefId = (int)$quotationData['quotation_ref_id'];
        $eRes = @mysqli_query($connection, "SELECT enquiry_agent FROM {$tblPrefix}enquiry WHERE enquiry_id = {$enqRefId} LIMIT 1");
        if ($eRes && $eRow = mysqli_fetch_assoc($eRes)) {
            $assignedAgentId = (int)($eRow['enquiry_agent'] ?? 0);
        }
    }
    // Priority 2: Quotation sent by
    if ($assignedAgentId === 0 && !empty($quotationData['quotation_sent_by'])) {
        $assignedAgentId = (int)$quotationData['quotation_sent_by'];
    }
    // Priority 3: Current session user
    if ($assignedAgentId === 0 && !empty($_SESSION['user_id'])) {
        $assignedAgentId = (int)$_SESSION['user_id'];
    }
    if ($assignedAgentId === 0 && defined('USER_ID') && (int)USER_ID > 0) {
        $assignedAgentId = (int)USER_ID;
    }

    if ($assignedAgentId > 0 && isset($connection)) {
        $spRes = @mysqli_query($connection, "
            SELECT p.profile_display_name, p.profile_first_name, p.profile_last_name,
                   p.profile_email, p.profile_mobile, p.profile_image
            FROM {$tblPrefix}profile p
            WHERE p.profile_user_id = {$assignedAgentId}
            LIMIT 1
        ");
        if ($spRes && mysqli_num_rows($spRes) > 0) {
            $spRow = mysqli_fetch_assoc($spRes);
            $salesPersonName   = trim($spRow['profile_display_name'] ?? trim(($spRow['profile_first_name'] ?? '') . ' ' . ($spRow['profile_last_name'] ?? '')));
            $salesPersonEmail    = trim($spRow['profile_email'] ?? '');
            $salesPersonMobile   = trim($spRow['profile_mobile'] ?? '');
            $salesPersonWhatsapp = $salesPersonMobile;
            $rawSpImg = $spRow['profile_image'] ?? '';
            if (!empty($rawSpImg)) {
                $siteBase = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
                $salesPersonImage = filter_var($rawSpImg, FILTER_VALIDATE_URL) ? $rawSpImg : $siteBase . '/' . ltrim($rawSpImg, '/');
            }
        }
    }

    // Fallback: company consultant desk if no specific salesperson found
    if (empty($salesPersonName)) {
        $salesPersonName     = !empty($company_name) ? $company_name . ' Travel Desk' : 'Dedicated Travel Specialist';
        $salesPersonEmail    = $site_email ?? ($company_email ?? '');
        $salesPersonMobile   = $site_mobile ?? ($company_mobile ?? '');
        $salesPersonWhatsapp = $salesPersonMobile;
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

    $itineraries = $parsedDescription['itinerary'] ?? ($quotationData['tour_package_itinerary'] ?? []);
    if (is_string($itineraries)) {
        $itineraries = json_decode($itineraries, true) ?: [];
    }

    $tour_package_city = !empty($parsedDescription['city'])
        ? $parsedDescription['city']
        : (!empty($parsedDescription['destination']) ? $parsedDescription['destination'] : (!empty($parsedDescription['tour_package_city']) ? $parsedDescription['tour_package_city'] : ($quotationData['tour_package_city'] ?? '')));

    // Auto-discover stay city from itineraries if missing
    if ((empty($tour_package_city) || strtolower($tour_package_city) === 'destination' || strtolower($tour_package_city) === 'tour destination') && !empty($itineraries) && is_array($itineraries)) {
        foreach ($itineraries as $day) {
            $c = trim((string)($day['city'] ?? ($day['stay_city'] ?? '')));
            $cLower = strtolower($c);
            if ($c !== '' && !str_contains($cLower, 'overnight') && !str_contains($cLower, 'transit') && !str_contains($cLower, 'tour end') && !str_contains($cLower, 'trip end') && !str_contains($cLower, 'no stay') && !str_contains($cLower, 'no hotel')) {
                $tour_package_city = $c;
                break;
            }
        }
    }
    if (empty($tour_package_city)) {
        $tour_package_city = 'Tour Destination';
    }

    $tour_package_state = $parsedDescription['state'] ?? ($quotationData['tour_package_state'] ?? '');
    $tour_package_country = $parsedDescription['country'] ?? ($quotationData['tour_package_country'] ?? '');
    $tour_package_hotel = $parsedDescription['hotel'] ?? ($parsedDescription['tour_package_hotel'] ?? ($quotationData['tour_package_hotel'] ?? ''));
    $tour_package_theme = $parsedDescription['travel_theme'] ?? ($parsedDescription['theme'] ?? ($quotationData['tour_package_theme'] ?? 'Standard'));
    $pickup_location = $parsedDescription['pickup_location'] ?? ($parsedDescription['pickupLocation'] ?? ($postData['pickupLocation'] ?? ($postData['pickup_location'] ?? '')));
    $drop_location = $parsedDescription['drop_location'] ?? ($parsedDescription['dropLocation'] ?? ($postData['dropLocation'] ?? ($postData['drop_location'] ?? '')));
    $pickupLocation = $pickup_location;
    $dropLocation = $drop_location;
    $tour_package_outstation_vehicle = $parsedDescription['outstation_vehicle'] ?? ($parsedDescription['vehicle'] ?? ($parsedDescription['outstationVehicle'] ?? ($quotationData['tour_package_outstation_vehicle'] ?? '')));
    $tour_package_localsightseen_vehicle = $parsedDescription['localsightseen_vehicle'] ?? ($parsedDescription['localSightseeingVehicle'] ?? ($quotationData['tour_package_localsightseen_vehicle'] ?? ''));
    $itinerary_overview = $parsedDescription['overview'] ?? ($quotationData['tour_package_overview'] ?? '');
    $tour_package_overview = $itinerary_overview;

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
    $travelDate = $parsedDescription['travel_date'] ?? ($parsedDescription['travelDate'] ?? ($quotationData['quotation_travel_date'] ?? ''));
    $numberOfDays = (int)($quotationData['tour_package_duration'] ?? (count($itineraries) > 0 ? (count($itineraries) > 1 ? count($itineraries) - 1 : 1) : 0));
    $tour_package_duration = $numberOfDays;

    $price = (float)($quotationData['quotation_price'] ?? 0);
    $tour_package_price_selling_adult = (float)($quotationData['quotation_per_adult'] ?? ($parsedDescription['adult_per_person'] ?? ($parsedDescription['adultPrice'] ?? ($parsedDescription['adult_price'] ?? ($price > 0 ? $price : 0)))));
    $tour_package_price_selling_child = (float)($quotationData['quotation_per_child'] ?? ($parsedDescription['child_per_person'] ?? ($parsedDescription['childPrice'] ?? ($parsedDescription['child_price'] ?? 0))));
    $tour_package_price_selling_infant = (float)($quotationData['quotation_per_infant'] ?? ($parsedDescription['infant_per_person'] ?? ($parsedDescription['infantPrice'] ?? ($parsedDescription['infant_price'] ?? 0))));
    $selling_price = $price;
    $adults = (int)($parsedDescription['adults_count'] ?? ($parsedDescription['adults'] ?? 1));
    $children = (int)($parsedDescription['children_count'] ?? ($parsedDescription['children'] ?? 0));
    $infants = (int)($parsedDescription['infants_count'] ?? ($parsedDescription['infants'] ?? 0));
    $payment_link = $parsedDescription['payment_url'] ?? ($parsedDescription['payment_link'] ?? ($parsedDescription['pay_url'] ?? ''));

    $gstPercent = defined('GST_PERCENTAGE') ? (float)GST_PERCENTAGE : 5.0;
    $basePrice = $price > 0 ? round($price / (1 + ($gstPercent / 100)), 2) : 0;
    $quotationPriceWithOutGST = number_format($basePrice, 2);
    $gstAmount = number_format($price - $basePrice, 2);
    $quotationPriceWithGST = number_format($price, 2);
    $total_discount = number_format((float)($quotationData['quotation_discount'] ?? 0), 2);

    $passangerInfo = 'Custom Quotation';
    if (!empty($quotationData['enquiry_first_name'])) {
        $passangerInfo = trim($quotationData['enquiry_first_name'] . ' ' . ($quotationData['enquiry_last_name'] ?? ''));
    }
} else {
    // If raw draft content was posted without a quotation ID (Preview Mode)
    $templateToUse = in_array(strtolower($requestedTemplate), $availableTemplates) ? strtolower($requestedTemplate) : 'mit';

    $itinerary_title   = $postData['title'] ?? ($postData['package_title'] ?? 'Tour Itinerary Proposal');
    $tour_package_title = $itinerary_title;

    $itineraries = $postData['days'] ?? ($postData['itinerary'] ?? []);
    if (is_string($itineraries)) {
        $itineraries = json_decode($itineraries, true) ?: [];
    }

    $tour_package_city = trim((string)($postData['city'] ?? ($postData['destination'] ?? ($postData['tour_package_city'] ?? ''))));

    // Auto-discover stay city from itineraries if missing or default
    if ((empty($tour_package_city) || strtolower($tour_package_city) === 'destination' || strtolower($tour_package_city) === 'tour destination') && !empty($itineraries) && is_array($itineraries)) {
        foreach ($itineraries as $day) {
            $c = trim((string)($day['city'] ?? ($day['stay_city'] ?? '')));
            $cLower = strtolower($c);
            if ($c !== '' && !str_contains($cLower, 'overnight') && !str_contains($cLower, 'transit') && !str_contains($cLower, 'tour end') && !str_contains($cLower, 'trip end') && !str_contains($cLower, 'no stay') && !str_contains($cLower, 'no hotel')) {
                $tour_package_city = $c;
                break;
            }
        }
    }
    if (empty($tour_package_city)) {
        $tour_package_city = 'Tour Destination';
    }

    $tour_package_state = $postData['state'] ?? '';
    $tour_package_country = $postData['country'] ?? '';
    $tour_package_hotel = $postData['hotel'] ?? ($postData['tour_package_hotel'] ?? '');
    $tour_package_theme = $postData['travel_theme'] ?? ($postData['travelTheme'] ?? ($postData['theme'] ?? 'Standard'));
    $pickup_location = $postData['pickupLocation'] ?? ($postData['pickup_location'] ?? '');
    $drop_location = $postData['dropLocation'] ?? ($postData['drop_location'] ?? '');
    $pickupLocation = $pickup_location;
    $dropLocation = $drop_location;
    $tour_package_outstation_vehicle = $postData['outstationVehicle'] ?? ($postData['outstation_vehicle'] ?? ($postData['vehicle'] ?? ''));
    $tour_package_localsightseen_vehicle = $postData['localSightseeingVehicle'] ?? ($postData['localsightseen_vehicle'] ?? '');
    $itinerary_overview = $postData['overview'] ?? '';
    $tour_package_overview = $itinerary_overview;

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
    $travelDate = $postData['travelDate'] ?? ($postData['travel_date'] ?? date('Y-m-d', strtotime('+7 days')));

    $numberOfDays = count($itineraries) > 0 ? (count($itineraries) > 1 ? count($itineraries) - 1 : 1) : 1;
    if (!empty($postData['number_of_days']) && is_numeric($postData['number_of_days'])) {
        $numberOfDays = (int)$postData['number_of_days'];
    } elseif (!empty($postData['duration_days']) && is_numeric($postData['duration_days'])) {
        $numberOfDays = (int)$postData['duration_days'] > 1 ? (int)$postData['duration_days'] - 1 : 1;
    }
    $tour_package_duration = $numberOfDays;

    $price = (float)($postData['totalPrice'] ?? ($postData['total_price'] ?? ($postData['price'] ?? 0)));
    $tour_package_price_selling_adult = (float)($postData['adultPrice'] ?? ($postData['adult_price'] ?? ($postData['adult_per_person'] ?? ($price > 0 ? $price : 0))));
    $tour_package_price_selling_child = (float)($postData['childPrice'] ?? ($postData['child_price'] ?? ($postData['child_per_person'] ?? 0)));
    $tour_package_price_selling_infant = (float)($postData['infantPrice'] ?? ($postData['infant_price'] ?? ($postData['infant_per_person'] ?? 0)));
    $selling_price = $price;
    $adults = (int)($postData['adults_count'] ?? ($postData['adults'] ?? 1));
    $children = (int)($postData['children_count'] ?? ($postData['children'] ?? 0));
    $infants = (int)($postData['infants_count'] ?? ($postData['infants'] ?? 0));
    $payment_link = $postData['payment_url'] ?? ($postData['payment_link'] ?? ($postData['pay_url'] ?? ($postData['paymentUrl'] ?? '')));

    $gstPercent = defined('GST_PERCENTAGE') ? (float)GST_PERCENTAGE : 5.0;
    $basePrice = $price > 0 ? round($price / (1 + ($gstPercent / 100)), 2) : 0;
    $quotationPriceWithOutGST = number_format($basePrice, 2);
    $gstAmount = number_format($price - $basePrice, 2);
    $quotationPriceWithGST = number_format($price, 2);
    $total_discount = '0.00';
    $passangerInfo = !empty($postData['clientName']) ? $postData['clientName'] : 'Valued Traveler';

    // Resolve assigned salesperson in preview mode
    $salesPersonName     = '';
    $salesPersonEmail    = '';
    $salesPersonMobile   = '';
    $salesPersonWhatsapp = '';
    $salesPersonImage    = '';
    $salesPersonDesig    = 'Travel Consultant';

    $rawRefId = $postData['lead_id'] ?? ($postData['enquiry_id'] ?? ($postData['refId'] ?? ($postData['ref_id'] ?? 0)));
    $leadRefId = 0;
    if (is_numeric($rawRefId) && (int)$rawRefId > 0) {
        $leadRefId = (int)$rawRefId;
    } elseif (!empty($rawRefId)) {
        $dec = @base64_decode((string)$rawRefId, true);
        if ($dec !== false && is_numeric($dec)) {
            $leadRefId = (int)$dec;
        }
    }

    $previewAgentId = 0;
    // Priority 1: Check assigned agent directly on the enquiry
    if ($leadRefId > 0 && isset($connection)) {
        $eRes = @mysqli_query($connection, "SELECT enquiry_agent FROM {$tblPrefix}enquiry WHERE enquiry_id = {$leadRefId} LIMIT 1");
        if ($eRes && $eRow = mysqli_fetch_assoc($eRes)) {
            $assignedOnEnq = (int)($eRow['enquiry_agent'] ?? 0);
            if ($assignedOnEnq > 0) {
                $previewAgentId = $assignedOnEnq;
            }
        }
    }

    // Priority 2: Check posted agent_id / assigned_agent
    if ($previewAgentId === 0) {
        $previewAgentId = (int)($postData['agent_id'] ?? ($postData['assigned_agent'] ?? ($postData['enquiry_agent'] ?? 0)));
    }

    // Priority 3: Current session user
    if ($previewAgentId === 0 && !empty($_SESSION['user_id'])) {
        $previewAgentId = (int)$_SESSION['user_id'];
    }
    if ($previewAgentId === 0 && defined('USER_ID') && (int)USER_ID > 0) {
        $previewAgentId = (int)USER_ID;
    }

    if ($previewAgentId > 0 && isset($connection)) {
        $spRes = @mysqli_query($connection, "
            SELECT p.profile_display_name, p.profile_first_name, p.profile_last_name,
                   p.profile_email, p.profile_mobile, p.profile_image
            FROM {$tblPrefix}profile p
            WHERE p.profile_user_id = {$previewAgentId}
            LIMIT 1
        ");
        if ($spRes && mysqli_num_rows($spRes) > 0) {
            $spRow = mysqli_fetch_assoc($spRes);
            $salesPersonName     = trim($spRow['profile_display_name'] ?? trim(($spRow['profile_first_name'] ?? '') . ' ' . ($spRow['profile_last_name'] ?? '')));
            $salesPersonEmail    = trim($spRow['profile_email'] ?? '');
            $salesPersonMobile   = trim($spRow['profile_mobile'] ?? '');
            $salesPersonWhatsapp = $salesPersonMobile;
            $rawSpImg = $spRow['profile_image'] ?? '';
            if (!empty($rawSpImg)) {
                $siteBase = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
                $salesPersonImage = filter_var($rawSpImg, FILTER_VALIDATE_URL) ? $rawSpImg : $siteBase . '/' . ltrim($rawSpImg, '/');
            }
        }
    }

    // Fallback: company consultant desk if no specific salesperson found
    if (empty($salesPersonName)) {
        $salesPersonName     = !empty($company_name) ? $company_name . ' Travel Desk' : 'Dedicated Travel Specialist';
        $salesPersonEmail    = $site_email ?? ($company_email ?? '');
        $salesPersonMobile   = $site_mobile ?? ($company_mobile ?? '');
        $salesPersonWhatsapp = $salesPersonMobile;
    }

    // Resolve package and gallery images in preview mode
    $packageId = (int)($postData['package_id'] ?? ($postData['tour_package_id'] ?? ($postData['id'] ?? 0)));
    $tour_package_id = $packageId;
    $gallery_image_list = $postData['gallery_image_list'] ?? '';

    if ($packageId === 0 && $leadRefId > 0 && isset($connection)) {
        $pqRes = @mysqli_query($connection, "SELECT quotation_type_id FROM {$tblPrefix}quotation WHERE quotation_ref_id = {$leadRefId} AND quotation_type_id > 0 ORDER BY quotation_id DESC LIMIT 1");
        if ($pqRes && $pqRow = mysqli_fetch_assoc($pqRes)) {
            $packageId = (int)$pqRow['quotation_type_id'];
            $tour_package_id = $packageId;
        }
    }

    if ($packageId > 0 && isset($connection)) {
        if (empty($gallery_image_list)) {
            $gRes = @mysqli_query($connection, "
                SELECT gallery_image_list, gallery_image 
                FROM {$tblPrefix}gallery 
                WHERE gallery_category = {$packageId} 
                  AND gallery_site_id = {$siteId} 
                  AND gallery_type = 'tour_package' 
                  AND gallery_status = 1 
                LIMIT 1
            ");
            if ($gRes && $gRow = mysqli_fetch_assoc($gRes)) {
                $gallery_image_list = $gRow['gallery_image_list'];
                if (empty($tour_package_image) && !empty($gRow['gallery_image'])) {
                    $tour_package_image = $gRow['gallery_image'];
                }
            }
        }
    }
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

if (!empty($hideActions) || !empty($isPreview)) {
    $renderedHtml = preg_replace('/<div\s+class=["\']v3-pdf-actions-bar["\'].*?<\/div>/is', '', $renderedHtml);
}

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
        'view_url'     => (defined('SITE_DOMAIN') ? 'https://' . SITE_DOMAIN : '') . "/itinerary/{$encodedId}/",
        'pdf_url'      => (defined('SITE_DOMAIN') ? 'https://' . SITE_DOMAIN : '') . "/itinerary/{$encodedId}/?download=true",
        'download_url' => (defined('SITE_DOMAIN') ? 'https://' . SITE_DOMAIN : '') . "/download-itinerary/{$encodedId}/",
        'available_templates' => $availableTemplates
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} else {
    header("Content-type: text/html; charset=utf-8");
    echo $renderedHtml;
}
