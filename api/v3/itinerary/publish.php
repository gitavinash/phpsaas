<?php
/**
 * V3 Itinerary Publish API
 * Endpoint: /api/v3/itinerary/publish.php
 * Method: POST
 */

header("Content-type: application/json; charset=utf-8");

$docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
if (!isset($connection)) {
    $configPath = $docRoot . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

if (!defined('LOGIN_CHECK')) {
    define('LOGIN_CHECK', $docRoot . '/lib/modules/isLogin.php');
}
if (file_exists(LOGIN_CHECK)) {
    require_once LOGIN_CHECK;
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : 0;
$userId    = defined('USER_ID')      ? (int) USER_ID : 0;

// Get raw POST JSON or form / query data
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);
if (!is_array($data) || empty($data)) {
    $data = !empty($_POST) ? $_POST : $_GET;
}

$rawPkgId = isset($data['id']) ? trim((string)$data['id']) : (isset($data['package_id']) ? trim((string)$data['package_id']) : '0');
$packageId = is_numeric($rawPkgId) ? (int)$rawPkgId : 0;

$rawRefId = isset($data['refId']) ? trim((string)$data['refId']) : (isset($data['ref_id']) ? trim((string)$data['ref_id']) : (isset($data['lead_id']) ? trim((string)$data['lead_id']) : ''));
if (empty($rawRefId) && isset($_GET['refId'])) {
    $rawRefId = trim((string)$_GET['refId']);
}
$decodedRefId = base64_decode($rawRefId, true);
$refId = ($decodedRefId !== false && is_numeric($decodedRefId)) ? (int)$decodedRefId : (int)$rawRefId;

if (!function_exists('cleanItineraryUnicode')) {
    function cleanItineraryUnicode($val)
    {
        if (is_array($val)) {
            foreach ($val as $k => $v) {
                $val[$k] = cleanItineraryUnicode($v);
            }
            return $val;
        }
        if (!is_string($val) || $val === '') {
            return $val;
        }

        // 1. Unicode escape sequences: \u0026, \u2013, \u2014, etc.
        $val = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($m) {
            return mb_convert_encoding(pack('H*', $m[1]), 'UTF-8', 'UCS-2BE');
        }, $val);

        // 2. Erroneously unescaped hex artifacts
        $val = preg_replace('/\bu0026\b/i', '&', $val);
        $val = preg_replace('/\bu0040\b/i', '@', $val);
        $val = preg_replace('/\bu2013\b/i', '–', $val);
        $val = preg_replace('/\bu2014\b/i', '—', $val);

        // 3. Repeatedly collapse double-encoded &amp;amp;
        while (str_contains($val, '&amp;amp;')) {
            $val = str_replace('&amp;amp;', '&amp;', $val);
        }

        // 4. Normalize HTML entities
        $maxLoops = 3;
        while ($maxLoops-- > 0 && preg_match('/&[a-zA-Z0-9#]+;/', $val)) {
            $decoded = html_entity_decode($val, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if ($decoded === $val) break;
            $val = $decoded;
        }

        // 5. Standalone or erroneous "amp;" with missing leading ampersand
        $val = preg_replace('/(?<=\s|^)amp;(?=\s|[.,!?]|$)/i', '&', $val);
        $val = preg_replace('/\bamp;\b/i', '&', $val);

        return $val;
    }
}

$refType   = isset($data['refType']) ? mysqli_real_escape_string($connection, trim($data['refType'])) : 'enquiry';
$mrp       = isset($data['mrp']) ? (float)$data['mrp'] : 0;
$content   = isset($data['content']) && is_array($data['content']) ? cleanItineraryUnicode($data['content']) : [];
$isDownload = !empty($data['isDownload']);
$saveAsNewPackage = !empty($data['save_as_new_package']) || !empty($data['saveAsNewPackage']);

if ($refId <= 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Missing or invalid Lead reference ID.'
    ]);
    exit;
}

// Extract and sanitize pricing accurately for each person type
$cleanPrice = function($val) {
    if (is_numeric($val)) return (float)$val;
    $val = str_replace(',', '', (string)$val);
    if (preg_match('/\d+(\.\d+)?/', $val, $m)) {
        return (float)$m[0];
    }
    return 0.0;
};

$adultPrice = $cleanPrice($content['adult_per_person'] ?? ($content['adultPrice'] ?? ($content['adult_price'] ?? '')));
$childPrice = $cleanPrice($content['child_per_person'] ?? ($content['childPrice'] ?? ($content['child_price'] ?? '')));
$infantPrice = $cleanPrice($content['infant_per_person'] ?? ($content['infantPrice'] ?? ($content['infant_price'] ?? '')));
$totalPrice = $cleanPrice($content['total_price'] ?? ($content['totalPrice'] ?? ($content['price'] ?? '')));

$adultsCount = max(1, (int)($content['adults_count'] ?? ($content['adults'] ?? 1)));
$childrenCount = max(0, (int)($content['children_count'] ?? ($content['children'] ?? 0)));
$infantsCount = max(0, (int)($content['infants_count'] ?? ($content['infants'] ?? 0)));

// If total price was not explicitly provided, calculate from individual person rates
if ($totalPrice <= 0) {
    $totalPrice = ($adultPrice * $adultsCount) + ($childPrice * $childrenCount) + ($infantPrice * $infantsCount);
}

// Ensure adultPrice represents the per-adult price, NOT the entire group total amount
if ($adultPrice >= $totalPrice && $adultsCount > 1 && $totalPrice > 0) {
    $rem = max(0, $totalPrice - ($childPrice * $childrenCount) - ($infantPrice * $infantsCount));
    $adultPrice = round($rem / $adultsCount, 2);
} elseif ($adultPrice <= 0 && $totalPrice > 0 && $adultsCount > 0) {
    $rem = max(0, $totalPrice - ($childPrice * $childrenCount) - ($infantPrice * $infantsCount));
    $adultPrice = round($rem / $adultsCount, 2);
}

// Keep content array synced with resolved rates
$content['adult_per_person'] = $adultPrice;
$content['child_per_person'] = $childPrice;
$content['infant_per_person'] = $infantPrice;
$content['total_price'] = $totalPrice;

$itineraryJson = mysqli_real_escape_string($connection, json_encode($content, JSON_UNESCAPED_UNICODE));

// Check if previous quotation exists to establish parent relationship
$parentCheckQuery = "
    SELECT quotation_id AS itineraryParentId 
    FROM {$tblPrefix}quotation 
    WHERE quotation_ref_id = '{$refId}' 
      AND quotation_ref_type = '{$refType}' 
      AND quotation_type_id = {$packageId} 
      AND quotation_type = 'tour_package' 
      " . ($siteId > 0 ? "AND quotation_site_id = {$siteId}" : "") . "
    ORDER BY quotation_id ASC 
    LIMIT 1
";

$itineraryParentId = 0;
$checkParentRes = mysqli_query($connection, $parentCheckQuery);
if ($checkParentRes && mysqli_num_rows($checkParentRes) > 0) {
    $pRow = mysqli_fetch_assoc($checkParentRes);
    $itineraryParentId = (int)($pRow['itineraryParentId'] ?? 0);
}

// Insert new quotation record
$insertQuery = "
    INSERT INTO {$tblPrefix}quotation (
        quotation_parent_id,
        quotation_ref_id,
        quotation_ref_type,
        quotation_type_id,
        quotation_type,
        quotation_site_id,
        quotation_sent_by,
        quotation_mrp,
        quotation_per_adult,
        quotation_per_child,
        quotation_per_infant,
        quotation_price,
        quotation_description,
        quotation_status,
        quotation_time
    ) VALUES (
        {$itineraryParentId},
        '{$refId}',
        '{$refType}',
        {$packageId},
        'tour_package',
        {$siteId},
        {$userId},
        {$mrp},
        {$adultPrice},
        {$childPrice},
        {$infantPrice},
        {$totalPrice},
        '{$itineraryJson}',
        0,
        NOW()
    )
";

if (mysqli_query($connection, $insertQuery)) {
    $quotationId = mysqli_insert_id($connection);
    $encodedId = base64_encode((string)$quotationId);

    // Deactivate previous quotations for this lead/package
    mysqli_query($connection, "
        UPDATE {$tblPrefix}quotation 
        SET quotation_status = 2 
        WHERE quotation_id != {$quotationId} 
          AND quotation_ref_id = '{$refId}' 
          AND quotation_ref_type = '{$refType}' 
          AND quotation_type = 'tour_package' 
          " . ($siteId > 0 ? "AND quotation_site_id = {$siteId}" : "") . "
    ");

    // Log in enquiry_notes
    mysqli_query($connection, "
        INSERT INTO {$tblPrefix}enquiry_notes (
            enquiry_notes_ref_id, 
            enquiry_notes_user_id, 
            enquiry_notes_meta_type, 
            enquiry_notes_meta_value
        ) VALUES (
            {$refId}, 
            {$userId}, 
            'itinerary', 
            '{$quotationId}'
        )
    ");

    // If requested, save customized itinerary as a new tour package template
    $newPackageId = 0;
    if ($saveAsNewPackage) {
        $rawPkgTitle = !empty($data['package_name']) ? trim((string)$data['package_name']) : (!empty($content['new_package_title']) ? trim((string)$content['new_package_title']) : trim((string)($content['title'] ?? 'Custom Tour Package')));
        $pkgTitle = mysqli_real_escape_string($connection, $rawPkgTitle);
        $pkgTheme = mysqli_real_escape_string($connection, trim((string)($content['travel_theme'] ?? ($content['theme'] ?? 'standard'))));
        $pkgTemplate = mysqli_real_escape_string($connection, trim((string)($content['template'] ?? 'mit')));
        $pkgStartFrom = mysqli_real_escape_string($connection, trim((string)($content['pickup_location'] ?? '')));
        $pkgEndTo = mysqli_real_escape_string($connection, trim((string)($content['drop_location'] ?? '')));
        
        $destRaw = trim((string)($content['city'] ?? ($content['destination'] ?? '')));
        $destParts = array_filter(array_map('trim', explode(',', $destRaw)));
        $destParts = array_values($destParts);
        $cityName = $destParts[0] ?? '';
        $stateName = (count($destParts) > 2) ? $destParts[count($destParts) - 2] : '';
        $countryName = (count($destParts) > 1) ? end($destParts) : 'India';

        if ((empty($stateName) || empty($countryName)) && !empty($cityName)) {
            $escapedCity = mysqli_real_escape_string($connection, $cityName);
            try {
                $geoCheck = mysqli_query($connection, "
                    SELECT c.city_name, s.state_name, co.country_name 
                    FROM {$tblPrefix}city c
                    LEFT JOIN {$tblPrefix}state s ON s.state_id = c.city_state_id
                    LEFT JOIN {$tblPrefix}country co ON co.country_id = s.state_country_id
                    WHERE c.city_name = '{$escapedCity}'
                    LIMIT 1
                ");
                if ($geoCheck && mysqli_num_rows($geoCheck) > 0) {
                    $gRow = mysqli_fetch_assoc($geoCheck);
                    if (empty($stateName) && !empty($gRow['state_name'])) $stateName = $gRow['state_name'];
                    if (empty($countryName) && !empty($gRow['country_name'])) $countryName = $gRow['country_name'];
                }
            } catch (Throwable $e) {
                // Geo lookup error gracefully caught
            }
        }

        $cityNameEscaped = mysqli_real_escape_string($connection, $cityName);
        $stateNameEscaped = mysqli_real_escape_string($connection, $stateName);
        $countryNameEscaped = mysqli_real_escape_string($connection, $countryName);

        $pkgDuration = (int)($content['duration_days'] ?? (is_array($content['itinerary'] ?? null) ? count($content['itinerary']) : 1));
        $pkgOverview = mysqli_real_escape_string($connection, trim((string)($content['overview'] ?? '')));
        
        $pkgItinArr = is_array($content['itinerary'] ?? null) ? $content['itinerary'] : [];
        $pkgItinJson = mysqli_real_escape_string($connection, json_encode($pkgItinArr, JSON_UNESCAPED_UNICODE));
        
        $pkgInclusions = mysqli_real_escape_string($connection, trim((string)($content['inclusions'] ?? '')));
        $pkgExclusions = mysqli_real_escape_string($connection, trim((string)($content['exclusions'] ?? '')));
        $pkgHotel = mysqli_real_escape_string($connection, trim((string)($content['hotel'] ?? '')));
        $pkgVehicle = mysqli_real_escape_string($connection, trim((string)($content['vehicle'] ?? ($content['outstation_vehicle'] ?? ''))));
        
        $pkgImage = '';
        if (!empty($pkgItinArr[0]['image'])) {
            $pkgImage = trim((string)$pkgItinArr[0]['image']);
        }
        $pkgImageEscaped = mysqli_real_escape_string($connection, $pkgImage);

        $pkgType = (stripos($countryName, 'india') !== false || empty($countryName)) ? 'domestic' : 'international';

        $insertPkgQuery = "
            INSERT INTO {$tblPrefix}tour_package (
                tour_package_site_id,
                tour_package_created_by,
                tour_package_image,
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
                tour_package_price,
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
                '{$pkgImageEscaped}',
                '{$pkgTitle}',
                '{$pkgTheme}',
                '{$pkgTemplate}',
                '{$pkgStartFrom}',
                '{$pkgEndTo}',
                '{$cityNameEscaped}',
                {$pkgDuration},
                '{$pkgOverview}',
                '{$pkgItinJson}',
                '{$pkgInclusions}',
                '{$pkgExclusions}',
                '',
                '{$pkgHotel}',
                '{$stateNameEscaped}',
                '{$countryNameEscaped}',
                '{$pkgType}',
                '{$pkgVehicle}',
                '{$pkgVehicle}',
                1,
                NOW()
            )
        ";

        if (mysqli_query($connection, $insertPkgQuery)) {
            $newPackageId = (int)mysqli_insert_id($connection);

            // Insert into tour_package_price
            $today = date('Y-m-d');
            $nextYear = date('Y-m-d', strtotime('+1 year'));
            $insertPriceQuery = "
                INSERT INTO {$tblPrefix}tour_package_price (
                    tour_package_price_parent_id,
                    tour_package_price_site_id,
                    tour_package_price_user_id,
                    tour_package_price_date_start,
                    tour_package_price_date_end,
                    tour_package_price_net_adult,
                    tour_package_price_net_child,
                    tour_package_price_mrp_adult,
                    tour_package_price_mrp_child,
                    tour_package_price_selling_adult,
                    tour_package_price_selling_child,
                    tour_package_price_agent_adult,
                    tour_package_price_agent_child,
                    tour_package_price_type,
                    tour_package_price_status,
                    tour_package_price_created_at
                ) VALUES (
                    {$newPackageId},
                    {$siteId},
                    {$userId},
                    '{$today}',
                    '{$nextYear}',
                    {$adultPrice},
                    {$childPrice},
                    {$adultPrice},
                    {$childPrice},
                    {$adultPrice},
                    {$childPrice},
                    {$adultPrice},
                    {$childPrice},
                    'double',
                    1,
                    NOW()
                )
            ";
            mysqli_query($connection, $insertPriceQuery);

            // Insert link & meta
            $slug = preg_replace('/[^A-Za-z0-9\s]/', '_', (string)($content['title'] ?? 'package'));
            $slug = strtolower('/' . preg_replace('/\s+/', '_', trim($slug)) . '-' . time() . '/');
            $insertLink = "
                INSERT INTO {$tblPrefix}link (
                    link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published
                ) VALUES (
                    '{$slug}', {$newPackageId}, 200, 'tour_package', {$userId}, {$siteId}, NOW(), NOW()
                )
            ";
            if (mysqli_query($connection, $insertLink)) {
                $linkId = (int)mysqli_insert_id($connection);
                if ($linkId > 0) {
                    mysqli_query($connection, "INSERT INTO {$tblPrefix}meta (meta_relation_id, meta_site_id, meta_type, meta_robots) VALUES ({$linkId}, {$siteId}, 'tour_package', 'index, follow')");
                }
            }

            // Update quotation type id to point to the newly created package
            mysqli_query($connection, "UPDATE {$tblPrefix}quotation SET quotation_type_id = {$newPackageId} WHERE quotation_id = {$quotationId}");
        }
    }

    // Fetch customer details from enquiry
    $customerQuery = mysqli_query($connection, "
        SELECT * FROM {$tblPrefix}enquiry 
        WHERE enquiry_id = {$refId} 
          " . ($siteId > 0 ? "AND enquiry_site_id = {$siteId}" : "") . "
        LIMIT 1
    ");

    if (!$customerQuery || mysqli_num_rows($customerQuery) === 0) {
        // Fallback query without site filter
        $customerQuery = mysqli_query($connection, "
            SELECT * FROM {$tblPrefix}enquiry 
            WHERE enquiry_id = {$refId} 
            LIMIT 1
        ");
    }

    $customerData = ($customerQuery && mysqli_num_rows($customerQuery) > 0) ? mysqli_fetch_assoc($customerQuery) : [];

    // Extract enquiry_email and enquiry_mobile
    $enquiryEmail  = trim($customerData['enquiry_email'] ?? '');
    $enquiryMobile = trim($customerData['enquiry_mobile'] ?? '');

    // Fallbacks from input data
    if (empty($enquiryEmail) && !empty($data['email'])) {
        $enquiryEmail = trim((string)$data['email']);
    }
    if (empty($enquiryEmail) && !empty($data['enquiry_email'])) {
        $enquiryEmail = trim((string)$data['enquiry_email']);
    }

    if (empty($enquiryMobile) && !empty($data['mobile'])) {
        $enquiryMobile = trim((string)$data['mobile']);
    }
    if (empty($enquiryMobile) && !empty($data['enquiry_mobile'])) {
        $enquiryMobile = trim((string)$data['enquiry_mobile']);
    }

    // Customer name extraction
    $firstName  = trim($customerData['enquiry_first_name'] ?? '');
    $middleName = trim($customerData['enquiry_middle_name'] ?? '');
    $lastName   = trim($customerData['enquiry_last_name'] ?? '');
    $customerName = trim(implode(' ', array_filter([$firstName, $middleName, $lastName])));

    // Fallbacks from enquiry_message if stored as JSON
    if (empty($enquiryEmail) || empty($enquiryMobile) || empty($customerName)) {
        $rawMsg = $customerData['enquiry_message'] ?? '';
        if (!empty($rawMsg) && ($rawMsg[0] === '{' || $rawMsg[0] === '[')) {
            $msgJson = json_decode($rawMsg, true);
            if (is_array($msgJson)) {
                if (empty($enquiryEmail)) {
                    $enquiryEmail = trim($msgJson['email'] ?? ($msgJson['enquiry_email'] ?? ''));
                }
                if (empty($enquiryMobile)) {
                    $enquiryMobile = trim($msgJson['mobile'] ?? ($msgJson['phone'] ?? ($msgJson['enquiry_mobile'] ?? '')));
                }
                if (empty($customerName)) {
                    $customerName = trim($msgJson['name'] ?? ($msgJson['customer_name'] ?? ''));
                }
            }
        }
    }

    if (empty($customerName)) {
        $customerName = 'Valued Customer';
    }

    // Persist email/mobile and update lead status to 9 (Itinerary Sent) when dispatched
    if (!empty($customerData) && $refType === 'enquiry') {
        $updateFields = [];
        $currentLeadStatus = (int)($customerData['enquiry_status'] ?? 0);
        $shouldUpdateStatus = !in_array($currentLeadStatus, [1, 6, 4, 5, 7, 15, 16, 17, 18], true);

        if ($shouldUpdateStatus) {
            $updateFields[] = "enquiry_status = 9";
            $updateFields[] = "enquiry_update = NOW()";
        }
        if (empty($customerData['enquiry_email']) && !empty($enquiryEmail)) {
            $escEmail = mysqli_real_escape_string($connection, $enquiryEmail);
            $updateFields[] = "enquiry_email = '{$escEmail}'";
        }
        if (empty($customerData['enquiry_mobile']) && !empty($enquiryMobile)) {
            $escMobile = mysqli_real_escape_string($connection, $enquiryMobile);
            $updateFields[] = "enquiry_mobile = '{$escMobile}'";
        }
        if (!empty($updateFields)) {
            mysqli_query($connection, "
                UPDATE {$tblPrefix}enquiry 
                SET " . implode(', ', $updateFields) . " 
                WHERE enquiry_id = {$refId}
            ");
        }
    }

    $siteDomain  = defined('SITE_DOMAIN') && !empty(SITE_DOMAIN) ? SITE_DOMAIN : ($_SERVER['HTTP_HOST'] ?? '');
    $protocol    = (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE) ? 'http://' : 'https://';
    $fullViewUrl = $protocol . $siteDomain . "/view-itinerary/{$encodedId}/";
    $fullPdfUrl  = $protocol . $siteDomain . "/download-itinerary/{$encodedId}/";

    if (!function_exists('longToShortUrl')) {
        if (defined('UTIL_FUNCTIONS') && file_exists(UTIL_FUNCTIONS)) {
            require_once UTIL_FUNCTIONS;
        } elseif (file_exists($docRoot . '/lib/functions/utils.php')) {
            require_once $docRoot . '/lib/functions/utils.php';
        }
    }

    $shortViewUrl = function_exists('longToShortUrl') ? longToShortUrl($connection, $fullViewUrl) : '';
    $shortPdfUrl  = function_exists('longToShortUrl') ? longToShortUrl($connection, $fullPdfUrl) : '';

    $viewUrl = !empty($shortViewUrl) ? $shortViewUrl : $fullViewUrl;
    $pdfUrl  = !empty($shortPdfUrl) ? $shortPdfUrl : $fullPdfUrl;

    $emailSent = false;
    $emailError = null;
    $whatsappSent = false;
    $whatsappError = null;

    if (!$isDownload) {
        $itineraryArr = isset($content['itinerary']) && is_array($content['itinerary']) ? $content['itinerary'] : [];
        $totalDays    = count($itineraryArr);

        // Extract highlights for email proposal summary
        $highlights = [];
        foreach ($itineraryArr as $d) {
            $raw = trim((string)($d['day'] ?? ''));
            if ($raw === '') continue;
            $parts = explode(' - ', $raw, 2);
            $title = isset($parts[1]) ? $parts[1] : $raw;
            $title = trim($title);
            if ($title !== '') $highlights[] = $title;
            if (count($highlights) >= 3) break;
        }

        $firstTitle = $highlights[0] ?? null;
        $lastTitle  = null;
        if ($totalDays > 1) {
            $lastRaw = trim((string)($itineraryArr[$totalDays - 1]['day'] ?? ''));
            if ($lastRaw !== '') {
                $parts = explode(' - ', $lastRaw, 2);
                $lastTitle = isset($parts[1]) ? trim($parts[1]) : $lastRaw;
            }
        }

        $safeName     = htmlspecialchars($customerName, ENT_QUOTES, 'UTF-8');
        $safeTour     = htmlspecialchars((string)($content['title'] ?? 'Your Trip'), ENT_QUOTES, 'UTF-8');
        $safeSiteName = htmlspecialchars(defined('SITE_NAME') ? SITE_NAME : 'Aleya', ENT_QUOTES, 'UTF-8');
        $itineraryPrice = $totalPrice;
        $tourDuration = ($totalDays > 1 ? ($totalDays - 1) . ' Nights / ' : '') . $totalDays . ' Days';

        $shouldSendEmail = isset($data['send_email']) ? (!empty($data['send_email']) && $data['send_email'] !== 'false' && $data['send_email'] !== '0') : true;
        $shouldSendWhatsApp = isset($data['send_whatsapp']) ? (!empty($data['send_whatsapp']) && $data['send_whatsapp'] !== 'false' && $data['send_whatsapp'] !== '0') : true;

        // 1. Dispatch Itinerary via Email using enquiry_email (if toggle ON)
        $toAddress = $enquiryEmail;
        if ($shouldSendEmail) {
            if (!empty($toAddress)) {
                $emailTemplateFile = '';
                if (defined('USER_EMAIL_TEMPLATE') && file_exists(USER_EMAIL_TEMPLATE . 'send-itinerary.php')) {
                    $emailTemplateFile = USER_EMAIL_TEMPLATE . 'send-itinerary.php';
                } elseif (defined('EMAIL_TEMPLATE')) {
                    $siteApp = defined('SITE_APP') ? SITE_APP : 'aleya';
                    $emailCands = [
                        EMAIL_TEMPLATE . $siteApp . '/agent/send-itinerary.php',
                        EMAIL_TEMPLATE . $siteApp . '/administrator/send-itinerary.php',
                        EMAIL_TEMPLATE . $siteApp . '/operations/send-itinerary.php',
                    ];
                    foreach ($emailCands as $cand) {
                        if (file_exists($cand)) {
                            $emailTemplateFile = $cand;
                            break;
                        }
                    }
                }

                if (!empty($emailTemplateFile)) {
                    // Buffer output to catch any PHPMailer / echo outputs and prevent corrupting JSON
                    ob_start();
                    try {
                        require_once $emailTemplateFile;
                        $emailSent = true;
                    } catch (\Throwable $e) {
                        $emailError = $e->getMessage();
                    }
                    $emailOut = ob_get_clean();
                    if (!empty($emailOut) && (stripos($emailOut, 'Mailer Error') !== false || stripos($emailOut, 'Exception') !== false)) {
                        $emailError = trim(strip_tags($emailOut));
                        $emailSent = false;
                    }
                } else {
                    $emailError = 'Email template not found on server.';
                }
            } else {
                $emailError = 'Customer email address (enquiry_email) is missing.';
            }
        } else {
            $emailError = 'Skipped by user toggle.';
        }

        // 2. Dispatch Itinerary via WhatsApp using enquiry_mobile (if toggle ON)
        $cleanMobile = preg_replace('/[^0-9]/', '', (string)$enquiryMobile);
        $mobile = !empty($cleanMobile) ? $cleanMobile : $enquiryMobile;

        if ($shouldSendWhatsApp) {
            if (!empty($mobile)) {
                require_once dirname(__DIR__) . '/whatsapp/device_helper.php';

                $siteTitle = defined('SITE_NAME') ? SITE_NAME : 'Aleya';
                $tourName = !empty($content['title']) ? trim((string)$content['title']) : 'Tour Proposal';
                $cName = !empty($customerName) ? trim((string)$customerName) : 'Valued Customer';
                $curSym = defined('DEFAULT_CURRENCY_SYMBOL') ? DEFAULT_CURRENCY_SYMBOL : '₹';

                $dest = !empty($content['destination']) ? trim((string)$content['destination']) : (!empty($content['city']) ? trim((string)$content['city']) : '');
                $travelDateRaw = !empty($content['travel_date']) ? trim((string)$content['travel_date']) : '';
                $adults = !empty($content['adults_count']) ? (int)$content['adults_count'] : (!empty($content['adults']) ? (int)$content['adults'] : 0);
                $children = !empty($content['children_count']) ? (int)$content['children_count'] : (!empty($content['children']) ? (int)$content['children'] : 0);
                if ($refId > 0 && (empty($dest) || empty($travelDateRaw) || ($adults === 0 && $children === 0))) {
                    try {
                        $profRes = mysqli_query($connection, "SELECT date_of_travel, number_of_adults, number_of_childrens FROM {$tblPrefix}enquiry_profile WHERE enquiry_profile_ref_id = {$refId} LIMIT 1");
                        if ($profRes && mysqli_num_rows($profRes) > 0) {
                            $pRow = mysqli_fetch_assoc($profRes);
                            if (empty($travelDateRaw) && !empty($pRow['date_of_travel'])) {
                                $travelDateRaw = trim((string)$pRow['date_of_travel']);
                            }
                            if ($adults === 0 && !empty($pRow['number_of_adults'])) {
                                $adults = (int)$pRow['number_of_adults'];
                            }
                            if ($children === 0 && !empty($pRow['number_of_childrens'])) {
                                $children = (int)$pRow['number_of_childrens'];
                            }
                        }
                    } catch (\Throwable $e) {
                        // Safe fallback if enquiry_profile query fails
                    }
                    if (empty($dest) && !empty($customerData['enquiry_subject'])) {
                        $dest = trim((string)$customerData['enquiry_subject']);
                    }
                }

                $waLines = [];
                $waLines[] = "🌟 *Tour Proposal & Itinerary*";
                $waLines[] = "";
                $waLines[] = "Dear {$cName},";
                $waLines[] = "";
                $waLines[] = "Greetings from *{$siteTitle}*!";
                $waLines[] = "We are pleased to share your customized tour proposal for *{$tourName}*.";
                $waLines[] = "";
                if (!empty($dest)) {
                    $waLines[] = "📍 *Destination:* {$dest}";
                }
                if (!empty($travelDateRaw)) {
                    $fmtTravelDate = date('d M, Y', strtotime($travelDateRaw));
                    $waLines[] = "📅 *Travel Date:* {$fmtTravelDate}" . (!empty($tourDuration) ? " ({$tourDuration})" : "");
                } elseif (!empty($tourDuration)) {
                    $waLines[] = "📅 *Duration:* {$tourDuration}";
                }
                $paxArr = [];
                if ($adults > 0) $paxArr[] = $adults . ' Adult' . ($adults > 1 ? 's' : '');
                if ($children > 0) $paxArr[] = $children . ' Child' . ($children > 1 ? 'ren' : '');
                if (!empty($paxArr)) {
                    $waLines[] = "👥 *No. of Persons:* " . implode(', ', $paxArr);
                }
                if (!empty($itineraryPrice)) {
                    $waLines[] = "💰 *Total Package Price:* {$curSym}" . number_format((float)$itineraryPrice, 2);
                }
                $waLines[] = "";
                $waLines[] = "Feel free to reply directly here for any modifications or questions. Have a wonderful journey!";
                $waLines[] = "";
                $waLines[] = "—";
                $waLines[] = "_Powered by Aleya.in_";
                $waText = implode("\n", $waLines);
                if (!empty($data['whatsapp_text'])) {
                    $customWa = trim((string)$data['whatsapp_text']);
                    $customWa = preg_replace('/(?:📄|🌐)?\s*\*(?:Download|View)[^*]*\*\s*\n\S+/u', '', $customWa);
                    $customWa = preg_replace('/https?:\/\/\S+/i', '', $customWa);
                    $customWa = preg_replace('/www\.\S+/i', '', $customWa);
                    $customWa = trim(preg_replace("/\n{3,}/", "\n\n", $customWa));
                    if (!empty($customWa)) {
                        $waText = $customWa;
                    }
                }

                // Resolve or generate physical PDF document for WhatsApp attachment
                $uploadDir = $docRoot . '/upload/itineraries';
                if (!is_dir($uploadDir)) {
                    @mkdir($uploadDir, 0777, true);
                }
                $itinHash = substr(md5('isw_itin_' . $quotationId), 0, 8);
                $pdfFileName = 'itinerary_' . $quotationId . '_' . $itinHash . '.pdf';
                $pdfDiskPath = $uploadDir . '/' . $pdfFileName;
                $pdfRelUrl   = '/upload/itineraries/' . $pdfFileName;

                // 0. Accept client-rendered PDF data (Fastest & 100% reliable across all hosting environments)
                $clientPdf = $data['pdf_data'] ?? ($postData['pdf_data'] ?? ($data['pdf_base64'] ?? ($postData['pdf_base64'] ?? ($_POST['pdf_data'] ?? ''))));
                if (!empty($clientPdf) && is_string($clientPdf)) {
                    if (strpos($clientPdf, 'base64,') !== false) {
                        $clientPdf = substr($clientPdf, strpos($clientPdf, 'base64,') + 7);
                    }
                    $cleanB64 = str_replace([' ', "\r", "\n", "\t"], ['+', '', '', ''], trim($clientPdf));
                    $pdfDecoded = base64_decode($cleanB64);
                    if ($pdfDecoded !== false && strlen($pdfDecoded) > 200 && strpos($pdfDecoded, '%PDF') !== false && strpos($pdfDecoded, '%PDF') <= 50) {
                        @file_put_contents($pdfDiskPath, $pdfDecoded);
                        if (file_exists($pdfDiskPath) && filesize($pdfDiskPath) > 200) {
                            $hasValidPdf = true;
                            $pdfRelUrl = '/upload/itineraries/' . $pdfFileName;
                            error_log("[ISW V3 Itinerary PDF] Client PDF accepted. Size: " . strlen($pdfDecoded) . " bytes -> Saved to " . $pdfDiskPath);
                        }
                    } else {
                        error_log("[ISW V3 Itinerary PDF] Client PDF payload present but decode/validation failed. Decoded length: " . ($pdfDecoded !== false ? strlen($pdfDecoded) : 0));
                    }
                }

                if (!$hasValidPdf && file_exists($pdfDiskPath) && filesize($pdfDiskPath) > 500) {
                    $fp = @fopen($pdfDiskPath, 'rb');
                    $head = $fp ? fread($fp, 5) : '';
                    if ($fp) fclose($fp);
                    if (strpos($head, '%PDF') === 0) {
                        $hasValidPdf = true;
                    }
                }

                if (!$hasValidPdf) {
                    $existingPdfs = glob($uploadDir . '/itinerary_' . $quotationId . '_*.pdf');
                    if (!empty($existingPdfs)) {
                        $cand = end($existingPdfs);
                        if (file_exists($cand) && filesize($cand) > 500) {
                            $pdfDiskPath = $cand;
                            $pdfRelUrl = '/upload/itineraries/' . basename($cand);
                            $hasValidPdf = true;
                        }
                    }
                }

                if (!$hasValidPdf && function_exists('generateHeadlessPdf')) {
                    try {
                        // 1. Direct Local HTML Render (Bypasses Cloudflare, NAT loopbacks, and domain SSL issues on production)
                        $renderedProposalHtml = '';
                        if (!defined('ITINERARY_SHOW_RENDER_ONLY')) {
                            define('ITINERARY_SHOW_RENDER_ONLY', true);
                        }
                        $prevId = $_GET['id'] ?? null;
                        $prevQId = $_GET['quotation_id'] ?? null;
                        $prevFormat = $_GET['format'] ?? null;
                        $prevHideActions = $_GET['hide_actions'] ?? null;

                        $_GET['id'] = $quotationId;
                        $_GET['quotation_id'] = $quotationId;
                        $_GET['format'] = 'html';
                        $_GET['hide_actions'] = '1';

                        $showPath = $docRoot . '/api/v3/itinerary/show.php';
                        if (file_exists($showPath)) {
                            ob_start();
                            $incRes = include $showPath;
                            $captured = ob_get_clean();
                            if (is_string($incRes) && !empty($incRes)) {
                                $renderedProposalHtml = $incRes;
                            } elseif (!empty($captured)) {
                                $renderedProposalHtml = $captured;
                            }
                        }

                        // Restore $_GET state
                        if ($prevId !== null) $_GET['id'] = $prevId; else unset($_GET['id']);
                        if ($prevQId !== null) $_GET['quotation_id'] = $prevQId; else unset($_GET['quotation_id']);
                        if ($prevFormat !== null) $_GET['format'] = $prevFormat; else unset($_GET['format']);
                        if ($prevHideActions !== null) $_GET['hide_actions'] = $prevHideActions; else unset($_GET['hide_actions']);

                        if (!empty($renderedProposalHtml)) {
                            $tmpHtmlFile = $uploadDir . '/tmp_itin_' . $quotationId . '_' . $itinHash . '.html';
                            file_put_contents($tmpHtmlFile, $renderedProposalHtml);

                            generateHeadlessPdf('file://' . $tmpHtmlFile, $pdfDiskPath);
                            @unlink($tmpHtmlFile);
                        }

                        // 2. Loopback HTTP Fallback if local file render didn't yield a valid PDF
                        if (!file_exists($pdfDiskPath) || filesize($pdfDiskPath) <= 500) {
                            $genHost = $_SERVER['HTTP_HOST'] ?? (defined('SITE_DOMAIN') ? SITE_DOMAIN : 'localhost');
                            $genUrl = $protocol . $genHost . "/view-itinerary/{$encodedId}/";
                            generateHeadlessPdf($genUrl, $pdfDiskPath);
                            if (!file_exists($pdfDiskPath) || filesize($pdfDiskPath) <= 500) {
                                if ($genHost !== 'localhost' && $genHost !== '127.0.0.1') {
                                    generateHeadlessPdf("http://localhost/view-itinerary/{$encodedId}/", $pdfDiskPath);
                                }
                            }
                        }

                        if (file_exists($pdfDiskPath) && filesize($pdfDiskPath) > 500) {
                            $hasValidPdf = true;
                            $pdfRelUrl = '/upload/itineraries/' . $pdfFileName;
                        }
                    } catch (\Throwable $e) {
                        error_log("[ISW V3 Itinerary PDF] Generation exception: " . $e->getMessage());
                    }
                }

                $safeDocTitle = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $tourName);
                $safeDocTitle = trim(preg_replace('/_+/', '_', $safeDocTitle), '_');
                if (empty($safeDocTitle)) $safeDocTitle = 'Tour_Proposal';
                $docAttachmentName = "{$safeDocTitle}_Itinerary.pdf";

                $waMsgType = $hasValidPdf ? 'document' : 'text';
                $waMediaUrl = $hasValidPdf ? $pdfRelUrl : '';

                // Safe fallback: If physical PDF couldn't be generated on server, ensure recipient still gets direct link
                if (!$hasValidPdf && !empty($viewUrl) && !str_contains($waText, $viewUrl)) {
                    $waText .= "\n\n🌐 *View / Download Proposal:* {$viewUrl}";
                }

                $waRes = sendWhatsAppDirectMessage(
                    $connection,
                    $siteId,
                    $userId,
                    $mobile,
                    $waText,
                    [
                        'lead_id'   => $refId,
                        'type'      => $waMsgType,
                        'media_url' => $waMediaUrl,
                        'disk_path' => ($hasValidPdf ? $pdfDiskPath : ''),
                        'file_name' => $docAttachmentName
                    ]
                );

                if (isset($waRes['status']) && $waRes['status'] === 'success') {
                    $whatsappSent = true;
                    $whatsappError = null;
                } else {
                    // Fallback to legacy BhashSMS only if site has NO WhatsApp Web devices configured
                    $hasAnyWaWebDev = false;
                    $devChkQ = mysqli_query($connection, "SELECT device_id FROM " . TABLE_PREFIX . "whatsapp_device WHERE device_site_id = {$siteId} LIMIT 1");
                    if ($devChkQ && mysqli_num_rows($devChkQ) > 0) {
                        $hasAnyWaWebDev = true;
                    }

                    $whatsappTemplateFile = '';
                    if (!$hasAnyWaWebDev && defined('WHATSAPP_TEMPLATE_USER') && file_exists(WHATSAPP_TEMPLATE_USER . 'send-itinerary.php')) {
                        $whatsappTemplateFile = WHATSAPP_TEMPLATE_USER . 'send-itinerary.php';
                    } elseif (defined('WHATSAPP_TEMPLATE')) {
                        $siteApp = defined('SITE_APP') ? SITE_APP : 'aleya';
                        $waCands = [
                            WHATSAPP_TEMPLATE . $siteApp . '/agent/send-itinerary.php',
                            WHATSAPP_TEMPLATE . $siteApp . '/administrator/send-itinerary.php',
                        ];
                        foreach ($waCands as $cand) {
                            if (file_exists($cand)) {
                                $whatsappTemplateFile = $cand;
                                break;
                            }
                        }
                    }
                    if (!empty($whatsappTemplateFile)) {
                        ob_start();
                        try {
                            require_once $whatsappTemplateFile;
                            $whatsappSent = true;
                            $whatsappError = null;
                        } catch (\Throwable $e) {
                            // Keep previous error
                        }
                        $waOut = ob_get_clean();
                        if (!empty($waOut) && stripos($waOut, 'failed') !== false) {
                            $whatsappError = trim(strip_tags($waOut));
                            $whatsappSent = false;
                        }
                    }
                }
            } else {
                $whatsappError = 'Customer mobile number (enquiry_mobile) is missing.';
            }
        } else {
            $whatsappError = 'Skipped by user toggle.';
        }

        // Record delivery metadata in quotation content
        $sentChannels = [];
        if ($whatsappSent) {
            $sentChannels[] = 'WhatsApp';
        }
        if ($emailSent) {
            $sentChannels[] = 'Email';
        }

        $content['delivery'] = [
            'email'          => $emailSent,
            'whatsapp'       => $whatsappSent,
            'email_error'    => $emailError,
            'whatsapp_error' => $whatsappError,
            'channels'       => $sentChannels,
            'sent_at'        => date('Y-m-d H:i:s')
        ];
        $updDescJson = mysqli_real_escape_string($connection, json_encode($content));
        mysqli_query($connection, "UPDATE {$tblPrefix}quotation SET quotation_description = '{$updDescJson}' WHERE quotation_id = {$quotationId}");

        // Record timeline note for delivery
        if (!empty($sentChannels)) {
            $summaryNote = "Itinerary proposal sent to customer via " . implode(' & ', $sentChannels);
            $escSummaryNote = mysqli_real_escape_string($connection, $summaryNote);
            @mysqli_query($connection, "INSERT INTO {$tblPrefix}enquiry_notes (
                enquiry_notes_ref_id,
                enquiry_notes_user_id,
                enquiry_notes_meta_type,
                enquiry_notes_meta_value
            ) VALUES (
                {$refId},
                {$userId},
                'itinerary_sent',
                '{$escSummaryNote}'
            )");
        }
    }

    $msg = 'Itinerary published successfully.';
    if (!$isDownload) {
        if ($saveAsNewPackage && $newPackageId > 0) {
            $msg = 'Itinerary saved as new tour package' . (!empty($sentChannels) ? ' and sent via ' . implode(' & ', $sentChannels) : '!') . '.';
        } elseif ($emailSent && $whatsappSent) {
            $msg = 'Itinerary published and sent to customer via Email and WhatsApp!';
        } elseif ($whatsappSent) {
            $msg = 'Itinerary published and sent to customer via WhatsApp!';
        } elseif ($emailSent) {
            $msg = 'Itinerary published and sent to customer via Email!';
        } elseif (!$shouldSendEmail && !$shouldSendWhatsApp) {
            $msg = 'Itinerary published successfully (delivery skipped by toggle).';
        } else {
            $errs = array_filter([$emailError ? "Email: {$emailError}" : null, $whatsappError ? "WhatsApp: {$whatsappError}" : null]);
            $msg = 'Itinerary published, but could not deliver: ' . implode(' | ', $errs);
        }
    }

    http_response_code(200);
    echo json_encode([
        'status'         => 'success',
        'httpStatus'     => 200,
        'msg'            => $msg,
        'itineraryId'    => $quotationId,
        'encodedId'      => $encodedId,
        'newPackageId'   => $newPackageId,
        'viewUrl'        => $viewUrl ?? '',
        'fullViewUrl'    => $fullViewUrl ?? '',
        'shortViewUrl'   => $shortViewUrl ?? '',
        'pdfUrl'         => $pdfUrl ?? '',
        'fullPdfUrl'     => $fullPdfUrl ?? '',
        'shortPdfUrl'    => $shortPdfUrl ?? '',
        'emailSent'      => $emailSent,
        'whatsappSent'   => $whatsappSent,
        'emailError'     => $emailError,
        'whatsappError'  => $whatsappError,
        'sentChannels'   => $sentChannels ?? [],
        'enquiry_email'  => $enquiryEmail,
        'enquiry_mobile' => $enquiryMobile
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'status'     => 'error',
        'httpStatus' => 500,
        'msg'        => (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE) ? mysqli_error($connection) : 'Failed to publish itinerary.'
    ]);
}
