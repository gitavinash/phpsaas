<?php
require_once LOGIN_CHECK;
header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

$tblPrefix  = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId     = defined('SITE_ID')      ? (int) SITE_ID : 0;
$currentUserId = defined('USER_ID')   ? (int) USER_ID : 0;
$accessType = isset($site_access_type) ? $site_access_type : 'administrator';

if (!function_exists('longToShortUrl')) {
    if (defined('UTIL_FUNCTIONS') && file_exists(UTIL_FUNCTIONS)) {
        require_once UTIL_FUNCTIONS;
    }
}

if ($accessType === 'digital_marketing') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Access denied: Digital marketing users cannot view lead contact details.']);
    exit();
}

$rawId = isset($_GET['id']) ? trim($_GET['id']) : (isset($_POST['id']) ? trim($_POST['id']) : '');
if (empty($rawId)) {
    echo json_encode(['status' => 'error', 'message' => 'Lead ID is required.']);
    exit();
}

$decoded = base64_decode($rawId, true);
$leadId  = ($decoded !== false && is_numeric($decoded)) ? (int)$decoded : (int)$rawId;

if ($leadId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Lead ID.']);
    exit();
}

if (!isset($connection) || !$connection) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection unavailable.']);
    exit();
}

$siteFilter = ($siteId > 0) ? "e.enquiry_site_id = {$siteId}" : "1=1";
$userFilter = ($accessType === 'agent' && $currentUserId > 0)
    ? " AND (e.enquiry_agent = '{$currentUserId}')"
    : "";

// 1. Fetch Lead Core Data
$leadQuery = "SELECT 
    e.*,
    p.profile_display_name AS agent_name,
    sa.site_access_type AS agent_role
FROM {$tblPrefix}enquiry e
LEFT JOIN {$tblPrefix}profile p ON (e.enquiry_agent > 0 AND e.enquiry_agent = p.profile_user_id)
LEFT JOIN {$tblPrefix}site_access sa ON (e.enquiry_agent > 0 AND e.enquiry_agent = sa.site_access_user_id AND (sa.site_access_site_id = e.enquiry_site_id OR sa.site_access_site_id = {$siteId}))
WHERE e.enquiry_id = {$leadId} AND {$siteFilter}{$userFilter}
LIMIT 1";

$leadRes = mysqli_query($connection, $leadQuery);
if (!$leadRes || mysqli_num_rows($leadRes) === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Lead not found or access denied.']);
    exit();
}

$lead = mysqli_fetch_assoc($leadRes);

require_once __DIR__ . '/status-config.php';

$statusMeta = getLeadStatusMeta($lead['enquiry_status'] ?? 0);

// Helper to filter out tracking parameters, Google Ads, reCAPTCHA, and submission artifacts
function filterTrackingLeadData($data)
{
    if (!is_array($data)) return $data;
    $trackingExact = [
        'gclid', 'gad_source', 'wbraid', 'gbraid', 'dclid',
        'fbclid', 'msclkid', 'ttclid', 'twclid', 'li_fat_id', 'mc_eid', 'igshid', 'yclid',
        'btnsubmit', 'btn_submit', 'submit', 'submit_button', 'action'
    ];
    $filtered = [];
    foreach ($data as $k => $v) {
        $kLower = strtolower(trim((string)$k));
        if (in_array($kLower, $trackingExact, true)) continue;
        if (str_starts_with($kLower, 'utm_')) continue;
        if (str_contains($kLower, 'recaptcha')) continue;
        if (preg_match('/^(_ga|_gid|_fbp|_fbc|_gat|_gac|_clck|_clsk)/i', $kLower)) continue;

        if (is_array($v)) {
            $filtered[$k] = filterTrackingLeadData($v);
        } else {
            $filtered[$k] = $v;
        }
    }
    return $filtered;
}

// Helper to identify standard lead fields from arbitrary JSON key names
function mapLeadFieldFromKey($key)
{
    $norm = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', ' ', (string)$key)));
    if (empty($norm)) return null;

    // Names
    if (in_array($norm, ['full name', 'fullname', 'name', 'customer name', 'lead name', 'client name', 'contact name', 'user name', 'username', 'traveller name', 'traveler name', 'person name', 'your name'], true)) {
        return 'customer_name';
    }
    if (in_array($norm, ['first name', 'firstname', 'first', 'fname', 'given name'], true)) {
        return 'first_name';
    }
    if (in_array($norm, ['last name', 'lastname', 'last', 'lname', 'surname', 'family name'], true)) {
        return 'last_name';
    }
    // Mobile
    if (in_array($norm, ['phone number', 'phonenumber', 'phone', 'mobile number', 'mobilenumber', 'mobile', 'contact number', 'contact no', 'phone no', 'cell', 'cell phone', 'whatsapp number', 'wa number', 'primary phone', 'primary mobile', 'enter your phone number', 'your phone number'], true)) {
        return 'mobile';
    }
    // Alt Mobile
    if (in_array($norm, ['alternate mobile', 'alt mobile', 'alternate phone', 'alt phone', 'secondary phone', 'secondary mobile', 'emergency contact', 'alternate contact'], true)) {
        return 'alt_mobile';
    }
    // Email
    if (in_array($norm, ['email', 'email address', 'e mail', 'mail', 'customer email', 'enter your email', 'your email'], true)) {
        return 'email';
    }
    // City / Location
    if (in_array($norm, ['current city', 'city', 'current location', 'location', 'residence', 'living in', 'from city', 'departure city', 'home town', 'hometown', 'state', 'your city'], true)) {
        return 'current_location';
    }
    // Destination / Tour Package
    if (in_array($norm, ['destination', 'tour package', 'package', 'subject', 'tour', 'holiday destination', 'travel destination', 'which destination', 'destination city', 'where do you want to go', 'where to go', 'preferred destination'], true) || str_contains($norm, 'destination') || (str_contains($norm, 'trip') && !str_contains($norm, 'date') && !str_contains($norm, 'when'))) {
        return 'destination';
    }
    if (str_contains($norm, 'places to visit') || str_contains($norm, 'places you want to visit')) {
        return 'destination';
    }
    // Date of travel
    if (in_array($norm, ['date of travel', 'travel date', 'departure date', 'start date', 'trip date', 'travel month', 'tentative date', 'planning to travel', 'travel time'], true) || str_contains($norm, 'when are you planning') || str_contains($norm, 'planning your')) {
        return 'date_of_travel';
    }
    // Duration
    if (in_array($norm, ['duration', 'duration days', 'number of days', 'days', 'how many days', 'trip duration', 'total days'], true)) {
        return 'duration_days';
    }
    // Adults / Pax
    if (in_array($norm, ['adults', 'number of adults', 'no of adults', 'pax', 'number of persons', 'number of person', 'how many person', 'how many persons', 'how many people', 'how many people are travelling', 'travelers', 'travellers', 'guests', 'passengers', 'group size', 'number of travelers'], true) || str_contains($norm, 'how many people') || str_contains($norm, 'how many person')) {
        return 'adults';
    }
    // Children
    if (in_array($norm, ['children', 'number of children', 'number of childrens', 'kids', 'child', 'no of children', 'children count'], true)) {
        return 'children';
    }
    // Infants
    if (in_array($norm, ['infants', 'number of infants', 'baby', 'infant', 'infants count'], true)) {
        return 'infants';
    }
    // Pickup
    if (in_array($norm, ['pickup location', 'pickup point', 'pickup', 'boarding point', 'boarding city', 'departure point'], true)) {
        return 'pickup_location';
    }
    // Drop
    if (in_array($norm, ['drop location', 'drop point', 'drop', 'dropping point', 'destination drop'], true)) {
        return 'drop_location';
    }
    return null;
}

// Parse JSON inquiry message if present
$extraDetails = [];
if (!empty($lead['enquiry_message'])) {
    $parsed = json_decode($lead['enquiry_message'], true);
    if (is_array($parsed)) {
        $extraDetails = filterTrackingLeadData($parsed);
    }
}

// Auto-map JSON keys for empty core fields without overriding existing values
$mappedFromJson = [];
if (!empty($extraDetails) && is_array($extraDetails)) {
    // 1. Unpack field_data if present (e.g. Facebook Lead Ads form responses)
    if (!empty($extraDetails['field_data']) && is_array($extraDetails['field_data'])) {
        foreach ($extraDetails['field_data'] as $fItem) {
            if (!is_array($fItem) || empty($fItem['name'])) continue;
            $fName = (string)$fItem['name'];
            $fVal = '';
            if (isset($fItem['values']) && is_array($fItem['values'])) {
                $fVal = trim(implode(', ', $fItem['values']));
            } elseif (isset($fItem['values'])) {
                $fVal = trim((string)$fItem['values']);
            } elseif (isset($fItem['value'])) {
                $fVal = trim((string)$fItem['value']);
            }
            if ($fVal === '') continue;
            $targetField = mapLeadFieldFromKey($fName);
            if ($targetField && !isset($mappedFromJson[$targetField])) {
                $mappedFromJson[$targetField] = $fVal;
            }
        }
    }

    // 2. Map root-level keys
    foreach ($extraDetails as $jsonKey => $jsonVal) {
        if (is_array($jsonVal) || is_object($jsonVal)) continue;
        $strVal = trim((string)$jsonVal);
        if ($strVal === '') continue;

        $targetField = mapLeadFieldFromKey($jsonKey);
        if ($targetField && !isset($mappedFromJson[$targetField])) {
            $mappedFromJson[$targetField] = $strVal;
        }
    }
}

// Clean names — only auto-fill if existing name fields are empty
$firstName = ucwords(strtolower(trim($lead['enquiry_first_name'] ?? '')));
$middleName = ucwords(strtolower(trim($lead['enquiry_middle_name'] ?? '')));
$lastName  = ucwords(strtolower(trim($lead['enquiry_last_name'] ?? '')));

if (empty($firstName) && empty($lastName)) {
    if (!empty($mappedFromJson['customer_name'])) {
        $nameParts = preg_split('/\s+/', trim($mappedFromJson['customer_name']), 2);
        $firstName = ucwords(strtolower($nameParts[0]));
        $lastName = isset($nameParts[1]) ? ucwords(strtolower($nameParts[1])) : '';
    } elseif (!empty($mappedFromJson['first_name'])) {
        $firstName = ucwords(strtolower($mappedFromJson['first_name']));
        if (!empty($mappedFromJson['last_name'])) {
            $lastName = ucwords(strtolower($mappedFromJson['last_name']));
        }
    }
}

$fullName  = trim("{$firstName} {$middleName} {$lastName}");
if (empty($fullName)) $fullName = 'Lead #' . $leadId;

// Format Mobile for WhatsApp — auto-fill from mapped JSON only if mobile is empty
$rawMobile = trim($lead['enquiry_mobile'] ?? '');
if (empty($rawMobile) && !empty($mappedFromJson['mobile'])) {
    $rawMobile = $mappedFromJson['mobile'];
    $lead['enquiry_mobile'] = $rawMobile;
}
$cleanMobile = preg_replace('/[^0-9]/', '', $rawMobile);
$waMobile = '';
if (!empty($cleanMobile)) {
    if (strlen($cleanMobile) === 10) {
        $waMobile = '91' . $cleanMobile;
    } elseif (strlen($cleanMobile) >= 11) {
        $waMobile = $cleanMobile;
    }
}

// Email — auto-fill from mapped JSON only if empty
if (empty($lead['enquiry_email']) && !empty($mappedFromJson['email'])) {
    $lead['enquiry_email'] = $mappedFromJson['email'];
}

// 2. Fetch Follow-up & Activity Timeline
$timelineQuery = "SELECT 
    n.enquiry_notes_id,
    n.enquiry_notes_meta_type,
    n.enquiry_notes_meta_value,
    n.enquiry_notes_update,
    p.profile_display_name AS user_name
FROM {$tblPrefix}enquiry_notes n
LEFT JOIN {$tblPrefix}profile p ON n.enquiry_notes_user_id = p.profile_user_id
WHERE n.enquiry_notes_ref_id = '{$leadId}'
ORDER BY n.enquiry_notes_update DESC";

$timeline = [];
$payments = [];
$rawTimelineRows = [];
$quotationIds = [];

if ($tRes = mysqli_query($connection, $timelineQuery)) {
    while ($tRow = mysqli_fetch_assoc($tRes)) {
        $rawTimelineRows[] = $tRow;
        $noteType = strtolower(trim($tRow['enquiry_notes_meta_type'] ?? ''));
        $noteVal  = trim($tRow['enquiry_notes_meta_value'] ?? '');
        if ($noteType === 'itinerary' && is_numeric($noteVal) && (int)$noteVal > 0) {
            $quotationIds[] = (int)$noteVal;
        }
    }
}

// Batch fetch quotation metadata for itinerary timeline items
$quotationsMap = [];
if (!empty($quotationIds)) {
    $qIdsEsc = implode(',', array_unique($quotationIds));
    $quotesQuery = "SELECT 
        q.quotation_id,
        q.quotation_ref_id,
        q.quotation_type_id,
        q.quotation_mrp,
        q.quotation_price,
        q.quotation_description,
        q.quotation_time,
        tp.tour_package_title,
        tp.tour_package_duration,
        tp.tour_package_city,
        tp.tour_package_state,
        tp.tour_package_theme
    FROM {$tblPrefix}quotation q
    LEFT JOIN {$tblPrefix}tour_package tp ON tp.tour_package_id = q.quotation_type_id
    WHERE q.quotation_id IN ({$qIdsEsc})";

    if ($qRes = mysqli_query($connection, $quotesQuery)) {
        while ($qRow = mysqli_fetch_assoc($qRes)) {
            $qId = (int)$qRow['quotation_id'];
            $descData = [];
            if (!empty($qRow['quotation_description'])) {
                $descData = json_decode($qRow['quotation_description'], true) ?: [];
            }
            $qTitle = !empty($descData['title']) ? $descData['title'] : (!empty($qRow['tour_package_title']) ? $qRow['tour_package_title'] : 'Tour Itinerary #' . $qId);
            $qPrice = (float)($qRow['quotation_price'] ?? ($descData['total_price'] ?? 0));
            $qMrp   = (float)($qRow['quotation_mrp'] ?? 0);
            $qDuration = !empty($descData['duration']) ? $descData['duration'] : (!empty($qRow['tour_package_duration']) ? $qRow['tour_package_duration'] : '');
            $qDest = !empty($descData['destination']) ? $descData['destination'] : (!empty($qRow['tour_package_city']) ? $qRow['tour_package_city'] : (!empty($qRow['tour_package_state']) ? $qRow['tour_package_state'] : ''));
            $qTheme = !empty($descData['theme']) ? $descData['theme'] : (!empty($qRow['tour_package_theme']) ? $qRow['tour_package_theme'] : '');
            $encodedQId = base64_encode((string)$qId);

            $descData['package_id'] = (int)$qRow['quotation_type_id'];
            $descData['id']         = $qId;

            $siteDomain   = defined('SITE_DOMAIN') && !empty(SITE_DOMAIN) ? SITE_DOMAIN : ($_SERVER['HTTP_HOST'] ?? '');
            $protocol     = (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE) ? 'http://' : 'https://';
            $fullViewUrl  = $protocol . $siteDomain . "/view-itinerary/{$encodedQId}/";
            $fullPdfUrl   = $protocol . $siteDomain . "/download-itinerary/{$encodedQId}/";
            $shortViewUrl = function_exists('longToShortUrl') ? longToShortUrl($connection, $fullViewUrl) : '';
            $shortPdfUrl  = function_exists('longToShortUrl') ? longToShortUrl($connection, $fullPdfUrl) : '';

            $quotationsMap[$qId] = [
                'id'                 => $qId,
                'encoded_id'         => $encodedQId,
                'package_id'         => (int)$qRow['quotation_type_id'],
                'title'              => $qTitle,
                'price'              => $qPrice,
                'mrp'                => $qMrp,
                'duration'           => $qDuration,
                'destination'        => $qDest,
                'theme'              => $qTheme,
                'delivery'           => $descData['delivery'] ?? null,
                'content'            => $descData,
                'view_url'           => "/view-itinerary/{$encodedQId}/",
                'download_url'       => "/download-itinerary/{$encodedQId}/",
                'short_view_url'     => $shortViewUrl ?: $fullViewUrl,
                'short_download_url' => $shortPdfUrl ?: $fullPdfUrl,
            ];
        }
    }
}

foreach ($rawTimelineRows as $tRow) {
    $noteType = strtolower(trim($tRow['enquiry_notes_meta_type'] ?? ''));
    $noteVal  = trim($tRow['enquiry_notes_meta_value'] ?? '');

    // Skip redundant "Lead status updated to Itinerary..." status log so only rich itinerary and actual notes appear
    if (
        stripos($noteVal, 'Lead status updated to Itinerary') !== false ||
        (stripos($noteVal, 'Itinerary / Quotation Sent') !== false && ($noteType === 'status' || stripos($noteVal, 'Lead status') !== false))
    ) {
        continue;
    }

    $updatedBy = trim($tRow['user_name'] ?? 'System');
    $updatedAt = $tRow['enquiry_notes_update'] ?? '';

    // Time ago
    $timeAgo = '';
    if (!empty($updatedAt)) {
        $ts = strtotime($updatedAt);
        if ($ts) {
            $diff = time() - $ts;
            if ($diff < 60) $timeAgo = 'Just now';
            elseif ($diff < 3600) $timeAgo = floor($diff / 60) . 'm ago';
            elseif ($diff < 86400) $timeAgo = floor($diff / 3600) . 'h ago';
            elseif ($diff < 172800) $timeAgo = 'Yesterday';
            else $timeAgo = date('d M Y, h:i A', $ts);
        }
    }

    $category = '';
    $followupTimeFormatted = '';
    $paymentDetails = null;

    // Detect if this is a follow-up (JSON or legacy category)
    $isFollowup = false;
    $followupObj = null;

    if ($noteType === 'followup') {
        $isFollowup = true;
        $dec = json_decode($noteVal, true);
        if (is_array($dec)) {
            $followupObj = $dec;
            $category = $dec['category'] ?? 'General Follow-up';
            $itemDesc = $dec['note'] ?? '';
            $followupTimeFormatted = $dec['followup_formatted'] ?? (!empty($dec['followup_time']) ? date('d M Y, h:i A', strtotime($dec['followup_time'])) : '');
        } else {
            $category = 'General Follow-up';
            $itemDesc = $noteVal;
        }
    } elseif (
        strpos($noteType, 'follow') !== false ||
        strpos($noteType, 'call') !== false ||
        strpos($noteType, 'price') !== false ||
        strpos($noteType, 'discussion') !== false ||
        strpos($noteType, 'customization') !== false ||
        strpos($noteType, 'negotiation') !== false
    ) {
        $isFollowup = true;
        $category = ucwords(str_replace(['_', '-'], ' ', $tRow['enquiry_notes_meta_type'] ?? 'General Follow-up'));
        $itemDesc = $noteVal;
    }

    if ($noteType === 'payment') {
        $payData = json_decode($noteVal, true);
        if (is_array($payData)) {
            $rawImg = $payData['paymentImages'] ?? '';
            $imgUrl = '';
            $imgName = '';
            if (!empty($rawImg)) {
                if (is_array($rawImg)) {
                    $imgUrl  = $rawImg['path'] ?? '';
                    $imgName = $rawImg['name'] ?? '';
                } elseif (is_string($rawImg)) {
                    $dec = json_decode($rawImg, true);
                    if (is_string($dec)) {
                        $dec = json_decode($dec, true);
                    }
                    if (is_array($dec)) {
                        $imgUrl  = $dec['path'] ?? '';
                        $imgName = $dec['name'] ?? '';
                    } else {
                        $imgUrl = $rawImg;
                    }
                }
            }

            $pStatus = strtolower(trim($payData['paymentStatus'] ?? 'verified'));
            $payments[] = [
                'id'               => (int)$tRow['enquiry_notes_id'],
                'amount'           => $payData['paymentAmount'] ?? '0',
                'method'           => $payData['paymentMethod'] ?? 'Standard',
                'date'             => $payData['paymentDate'] ?? date('d-m-Y', strtotime($updatedAt)),
                'reference'        => $payData['paymentReference'] ?? '',
                'notes'            => $payData['paymentMsg'] ?? '',
                'status'           => $pStatus,
                'status_label'     => ($pStatus === 'pending') ? 'Pending Verification' : (($pStatus === 'rejected') ? 'Rejected' : 'Verified'),
                'image'            => $rawImg,
                'image_url'        => $imgUrl,
                'image_name'       => $imgName,
                'recorded_by'      => $updatedBy,
                'recorded_by_role' => $payData['recordedByRole'] ?? '',
                'verified_by'      => $payData['verifiedBy'] ?? null,
                'verified_at'      => $payData['verifiedAt'] ?? null,
                'created_at'       => $updatedAt,
            ];
            $paymentDetails = [
                'amount'    => (float)($payData['paymentAmount'] ?? 0),
                'method'    => $payData['paymentMethod'] ?? 'Standard',
                'reference' => $payData['paymentReference'] ?? '',
                'status'    => $pStatus
            ];
            $itemDesc = "Recorded payment of ₹ " . number_format((float)($payData['paymentAmount'] ?? 0), 2) . " via " . ($payData['paymentMethod'] ?? 'Standard') . (!empty($payData['paymentReference']) ? " (Ref: {$payData['paymentReference']})" : "");
        } else {
            $itemDesc = $noteVal;
        }
    } elseif (!$isFollowup) {
        $itemDesc = $noteVal;
        if ($noteType === 'update' && ($itemDesc === 'User' || empty($itemDesc))) {
            $itemDesc = 'Updated lead details and status.';
        }
    }

    // Timeline item icon and title formatting
    $icon = 'fi fi-rr-comment-alt';
    $itemTitle = ucfirst($noteType);
    $finalType = $noteType;

    if ($isFollowup) {
        $finalType = 'followup';
        $icon = 'fi fi-rr-alarm-clock';
        $itemTitle = 'Follow-Up';
    } else {
        switch ($noteType) {
            case 'assign':
                $icon = 'fi fi-rr-user-add';
                $itemTitle = 'Lead Assigned';
                break;
            case 'payment':
                $icon = 'fi fi-rr-indian-rupee-sign';
                $itemTitle = 'Payment Recorded';
                break;
            case 'generate':
                $icon = 'fi fi-rr-plus';
                $itemTitle = 'Lead Created';
                break;
            case 'update':
                $icon = 'fi fi-rr-refresh';
                $itemTitle = 'Lead Updated';
                break;
            case 'sales':
                $icon = 'fi fi-rr-trophy-star';
                $itemTitle = 'Converted to Sales';
                break;
            case 'duplicate':
                $icon = 'fi fi-rr-copy';
                $itemTitle = 'Marked Duplicate';
                break;
            case 'itinerary':
                $icon = 'fi fi-rr-paper-plane';
                $itemTitle = 'Itinerary Sent';
                break;
            case 'payment_link':
                $icon = 'fi fi-rr-link';
                $itemTitle = 'Payment Link Sent';
                $finalType = 'payment_link';
                $category = 'Payment Request';
                $pLinkData = json_decode($noteVal, true) ?: [];
                $amt = (float)($pLinkData['amount'] ?? 0);
                $itemDesc = "Generated payment link for ₹ " . number_format($amt, 2);
                if (!empty($pLinkData['notes'])) {
                    $itemDesc .= " (" . $pLinkData['notes'] . ")";
                }
                break;
            case 'vehicle_photos':
                $icon = 'fi fi-rr-camera';
                $itemTitle = 'Vehicle Photos Sent';
                $decoded = json_decode($noteVal, true);
                if (is_array($decoded)) {
                    $vehNames = !empty($decoded['vehicles']) ? implode(', ', (array)$decoded['vehicles']) : 'fleet';
                    $chan = !empty($decoded['channels']) ? implode(' & ', (array)$decoded['channels']) : 'customer';
                    $itemDesc = "Shared vehicle photos for {$vehNames} via {$chan}.";
                    if (!empty($decoded['custom_note'])) {
                        $itemDesc .= " Note: \"{$decoded['custom_note']}\"";
                    }
                }
                break;
            default:
                $icon = 'fi fi-rr-comment-alt';
                $itemTitle = 'Note';
                $finalType = 'note';
                break;
        }
    }

    $quoteInfo = null;
    if ($noteType === 'itinerary') {
        if (is_numeric($noteVal) && isset($quotationsMap[(int)$noteVal])) {
            $quoteInfo = $quotationsMap[(int)$noteVal];
            $itemDesc  = $quoteInfo['title'];
        } elseif (is_numeric($noteVal) && (int)$noteVal > 0) {
            $encId        = base64_encode((string)$noteVal);
            $siteDomain   = defined('SITE_DOMAIN') && !empty(SITE_DOMAIN) ? SITE_DOMAIN : ($_SERVER['HTTP_HOST'] ?? '');
            $protocol     = (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE) ? 'http://' : 'https://';
            $fullViewUrl  = $protocol . $siteDomain . "/view-itinerary/{$encId}/";
            $fullPdfUrl   = $protocol . $siteDomain . "/download-itinerary/{$encId}/";
            $shortViewUrl = function_exists('longToShortUrl') ? longToShortUrl($connection, $fullViewUrl) : '';
            $shortPdfUrl  = function_exists('longToShortUrl') ? longToShortUrl($connection, $fullPdfUrl) : '';

            $quoteInfo = [
                'id'                 => (int)$noteVal,
                'encoded_id'         => $encId,
                'title'              => 'Tour Itinerary #' . $noteVal,
                'price'              => 0,
                'mrp'                => 0,
                'duration'           => '',
                'destination'        => '',
                'theme'              => '',
                'view_url'           => "/view-itinerary/{$encId}/",
                'download_url'       => "/download-itinerary/{$encId}/",
                'short_view_url'     => $shortViewUrl ?: $fullViewUrl,
                'short_download_url' => $shortPdfUrl ?: $fullPdfUrl,
            ];
            $itemDesc = 'Tour Itinerary #' . $noteVal;
        }
    }

    $timeline[] = [
        'id'                => (int)$tRow['enquiry_notes_id'],
        'type'              => $finalType,
        'title'             => $itemTitle,
        'category'          => $category,
        'followup_time'     => $followupTimeFormatted,
        'description'       => $itemDesc,
        'payment_data'      => $paymentDetails,
        'quotation'         => $quoteInfo,
        'payment_link_data' => ($finalType === 'payment_link') ? ($pLinkData ?? null) : null,
        'whatsapp_data'     => null,
        'user_name'         => $updatedBy,
        'time'              => $updatedAt ? date('d M Y, h:i A', strtotime($updatedAt)) : '',
        'time_relative'     => $timeAgo,
        'icon'              => $icon,
        'timestamp'         => !empty($updatedAt) ? strtotime($updatedAt) : 0,
    ];
}

// 2b. Fetch WhatsApp Messages for this lead to merge chronologically in the timeline stream
$chatMeta = null;
$chatCleanPhone = preg_replace('/[^0-9]/', '', (string)($lead['enquiry_mobile'] ?? ''));
$chatLast10 = strlen($chatCleanPhone) >= 10 ? substr($chatCleanPhone, -10) : $chatCleanPhone;
$chatConds = ["c.chat_lead_id = {$leadId}"];
if (!empty($chatCleanPhone)) {
    $waPhoneVariants = "'" . mysqli_real_escape_string($connection, $chatCleanPhone) . "'";
    if (strlen($chatCleanPhone) === 10) {
        $waPhoneVariants .= ",'91" . mysqli_real_escape_string($connection, $chatCleanPhone) . "'";
    }
    $chatConds[] = "c.chat_phone IN ({$waPhoneVariants})";
    if (strlen($chatLast10) >= 8) {
        $escLast10 = mysqli_real_escape_string($connection, $chatLast10);
        $chatConds[] = "RIGHT(REPLACE(REPLACE(c.chat_phone, '+', ''), ' ', ''), 10) = '{$escLast10}'";
    }
}
$chatWhere = implode(' OR ', $chatConds);

$waChatTblChk = @mysqli_query($connection, "SHOW TABLES LIKE '{$tblPrefix}whatsapp_chat'");
if ($waChatTblChk && mysqli_num_rows($waChatTblChk) > 0) {
    $chatIds = [];
    $siteFilterWa = ($siteId > 0) ? " AND (c.chat_site_id = {$siteId} OR c.chat_site_id = 0)" : "";
    $chatRes = @mysqli_query($connection, "SELECT c.chat_id, c.chat_phone, c.chat_name, c.chat_pushname, c.chat_avatar, c.chat_lead_id FROM {$tblPrefix}whatsapp_chat c WHERE ({$chatWhere}){$siteFilterWa}");
    if ($chatRes) {
        while ($cRow = mysqli_fetch_assoc($chatRes)) {
            $chatIds[] = (int)$cRow['chat_id'];
            if (!$chatMeta) {
                $chatMeta = $cRow;
            }
            if (empty($cRow['chat_lead_id'])) {
                @mysqli_query($connection, "UPDATE {$tblPrefix}whatsapp_chat SET chat_lead_id = {$leadId} WHERE chat_id = " . (int)$cRow['chat_id']);
            }
        }
    }

    if (!empty($chatIds)) {
        $cIdsEsc = implode(',', array_unique($chatIds));
        $waMsgsQuery = "SELECT 
            m.message_id,
            m.message_chat_id,
            m.message_wa_id,
            m.message_direction,
            m.message_sender,
            m.message_receiver,
            m.message_type,
            m.message_text,
            m.message_media_url,
            m.message_status,
            m.message_created_at
        FROM {$tblPrefix}whatsapp_message m
        WHERE m.message_chat_id IN ({$cIdsEsc})
        ORDER BY m.message_created_at DESC
        LIMIT 250";

        if ($wmRes = @mysqli_query($connection, $waMsgsQuery)) {
            $customerDisplayName = !empty($chatMeta['chat_pushname']) ? trim($chatMeta['chat_pushname']) : (!empty($lead['enquiry_first_name']) ? trim($lead['enquiry_first_name'] . ' ' . ($lead['enquiry_last_name'] ?? '')) : 'Customer');
            $upDir = (defined('ROOT') ? ROOT : dirname(__DIR__, 3)) . '/upload/whatsapp';

            while ($wmRow = mysqli_fetch_assoc($wmRes)) {
                $mCreatedAt = $wmRow['message_created_at'] ?? '';
                $mTs = !empty($mCreatedAt) ? strtotime($mCreatedAt) : 0;

                // Time ago
                $mTimeAgo = '';
                if ($mTs) {
                    $diff = time() - $mTs;
                    if ($diff < 60) $mTimeAgo = 'Just now';
                    elseif ($diff < 3600) $mTimeAgo = floor($diff / 60) . 'm ago';
                    elseif ($diff < 86400) $mTimeAgo = floor($diff / 3600) . 'h ago';
                    elseif ($diff < 172800) $mTimeAgo = 'Yesterday';
                    else $mTimeAgo = date('d M Y, h:i A', $mTs);
                }

                $mediaUrl = $wmRow['message_media_url'] ?? '';
                $mType = strtolower(trim($wmRow['message_type'] ?? 'text'));
                if (empty($mediaUrl) && !empty($wmRow['message_wa_id'])) {
                    foreach (['jpg', 'jpeg', 'png', 'webp', 'pdf', 'mp4', 'ogg'] as $chkExt) {
                        $chkFile = $upDir . '/wa_' . $wmRow['message_wa_id'] . '.' . $chkExt;
                        if (file_exists($chkFile) && filesize($chkFile) > 0) {
                            $mediaUrl = '/upload/whatsapp/wa_' . $wmRow['message_wa_id'] . '.' . $chkExt;
                            if ($mType === 'text') {
                                $mType = in_array($chkExt, ['jpg', 'jpeg', 'png', 'webp']) ? 'image' : ($chkExt === 'pdf' ? 'document' : 'media');
                            }
                            break;
                        }
                    }
                }

                $isIncoming = ($wmRow['message_direction'] === 'incoming');
                $senderName = $isIncoming ? $customerDisplayName : 'Agent / System';
                $waItemTitle = 'WhatsApp';
                $waCategory = $isIncoming ? 'Inbound' : 'Outbound';

                $wmText = trim((string)($wmRow['message_text'] ?? ''));
                $contactData = null;
                $locationData = null;

                // 1. JSON-encoded structured payload
                if (str_starts_with($wmText, '{') && str_ends_with($wmText, '}')) {
                    $json = json_decode($wmText, true);
                    if (is_array($json) && isset($json['type'])) {
                        if ($json['type'] === 'contact') {
                            $contactData = [
                                'name'  => trim($json['name'] ?? ''),
                                'phone' => trim($json['phone'] ?? ''),
                                'vcard' => $json['vcard'] ?? ''
                            ];
                            $mType = 'contact';
                        } elseif ($json['type'] === 'contacts') {
                            $contactData = [
                                'name'     => trim($json['name'] ?? 'Contacts'),
                                'phone'    => '',
                                'contacts' => $json['contacts'] ?? []
                            ];
                            $mType = 'contact';
                        } elseif ($json['type'] === 'location' || $json['type'] === 'live_location') {
                            $lat = (float)($json['latitude'] ?? 0);
                            $lng = (float)($json['longitude'] ?? 0);
                            $lName = trim($json['name'] ?? '');
                            $lAddr = trim($json['address'] ?? '');
                            $lUrl = !empty($json['url']) ? $json['url'] : ($lat && $lng ? "https://www.google.com/maps/search/?api=1&query={$lat},{$lng}" : "");
                            $locationData = [
                                'name'      => $lName ?: ($lAddr ?: 'Shared Location'),
                                'address'   => $lAddr,
                                'latitude'  => $lat,
                                'longitude' => $lng,
                                'url'       => $lUrl,
                                'media_url' => $mediaUrl
                            ];
                            $mType = 'location';
                        }
                    }
                }

                // 2. Legacy Contact message: [Contact: Display Name]
                if (!$contactData && preg_match('/^\[Contact:\s*(.*?)\]$/i', $wmText, $cMatch)) {
                    $cName = trim($cMatch[1]);
                    $cPhone = '';
                    $digitsOnly = preg_replace('/[^0-9]/', '', $cName);
                    if (strlen($digitsOnly) >= 10 && strlen($digitsOnly) <= 13) {
                        $cPhone = '+' . $digitsOnly;
                    }
                    $contactData = [
                        'name'  => $cName ?: 'Contact',
                        'phone' => $cPhone,
                        'vcard' => ''
                    ];
                    $mType = 'contact';
                }

                // 3. Legacy Location message: [Location: Place/Address] or [Location: ] or [Live Location]
                if (!$locationData && (preg_match('/^\[Location:\s*(.*?)\]$/i', $wmText, $lMatch) || preg_match('/^\[Live Location\]$/i', $wmText))) {
                    $locStr = isset($lMatch[1]) ? trim($lMatch[1]) : '';
                    $locUrl = '';
                    if (!empty($locStr)) {
                        $locUrl = "https://www.google.com/maps/search/?api=1&query=" . urlencode($locStr);
                    }
                    $locationData = [
                        'name'      => $locStr ?: 'Shared Location',
                        'address'   => $locStr,
                        'latitude'  => 0,
                        'longitude' => 0,
                        'url'       => $locUrl,
                        'media_url' => $mediaUrl
                    ];
                    $mType = 'location';
                }

                $timeline[] = [
                    'id'                => 'wa_' . (int)$wmRow['message_id'],
                    'type'              => 'whatsapp',
                    'title'             => $waItemTitle,
                    'category'          => $waCategory,
                    'followup_time'     => '',
                    'description'       => $wmRow['message_text'] ?? '',
                    'payment_data'      => null,
                    'quotation'         => null,
                    'payment_link_data' => null,
                    'whatsapp_data'     => [
                        'message_id'    => (int)$wmRow['message_id'],
                        'chat_id'       => (int)$wmRow['message_chat_id'],
                        'wa_id'         => $wmRow['message_wa_id'] ?? '',
                        'direction'     => $wmRow['message_direction'],
                        'type'          => $mType,
                        'text'          => $wmRow['message_text'] ?? '',
                        'media_url'     => $mediaUrl,
                        'contact_data'  => $contactData,
                        'location_data' => $locationData,
                        'status'        => $wmRow['message_status'],
                        'sender'        => $wmRow['message_sender'],
                        'receiver'      => $wmRow['message_receiver'],
                        'created_at'    => $mCreatedAt,
                    ],
                    'user_name'         => $senderName,
                    'time'              => $mCreatedAt ? date('d M Y, h:i A', $mTs) : '',
                    'time_relative'     => $mTimeAgo,
                    'icon'              => 'fi fi-brands-whatsapp',
                    'timestamp'         => $mTs,
                ];
            }
        }
    }
}

// Sort combined timeline stream chronologically (newest first)
usort($timeline, function($a, $b) {
    return ($b['timestamp'] ?? 0) <=> ($a['timestamp'] ?? 0);
});

// 3. Fetch Active Quotation if available
$quotation = [
    'id' => 0,
    'price' => 0,
    'min_payment' => 0,
    'title' => '',
];

$quoteSql = "SELECT quotation_id, quotation_price, quotation_description, quotation_type_id 
             FROM {$tblPrefix}quotation 
             WHERE quotation_ref_id = '{$leadId}' 
               AND quotation_ref_type = 'enquiry' 
               " . ($siteId > 0 ? "AND (quotation_site_id = {$siteId} OR quotation_site_id = 0)" : "") . "
             ORDER BY quotation_id DESC 
             LIMIT 1";

if ($qRes = mysqli_query($connection, $quoteSql)) {
    if ($qRow = mysqli_fetch_assoc($qRes)) {
        $qDescJson = !empty($qRow['quotation_description']) ? json_decode($qRow['quotation_description'], true) : [];
        $qPrice = (float)($qRow['quotation_price'] ?? ($qDescJson['total_price'] ?? 0));
        $qTitle = !empty($qDescJson['title']) ? trim($qDescJson['title']) : (!empty($qDescJson['package_name']) ? trim($qDescJson['package_name']) : '');
        $minPercent = defined('MINIMUM_PAYMENT') ? (float)MINIMUM_PAYMENT : 30;
        $quotation = [
            'id' => (int)$qRow['quotation_id'],
            'price' => $qPrice,
            'min_payment' => round(($qPrice * $minPercent) / 100, 2),
            'title' => $qTitle,
        ];
    }
}

// Fallback to itinerary quotation price if direct quotation record is 0
if ($quotation['price'] <= 0 && !empty($quotationsMap)) {
    foreach ($quotationsMap as $qm) {
        if (!empty($qm['price']) && (float)$qm['price'] > 0) {
            $quotation['price'] = (float)$qm['price'];
            $quotation['id']    = (int)($qm['id'] ?? 0);
            $quotation['title'] = (string)($qm['title'] ?? '');
            $minPercent = defined('MINIMUM_PAYMENT') ? (float)MINIMUM_PAYMENT : 30;
            $quotation['min_payment'] = round(($quotation['price'] * $minPercent) / 100, 2);
            break;
        }
    }
}

// Determine if itinerary has been sent
$hasItinerarySent = false;
if (!empty($quotation['price']) && (float)$quotation['price'] > 0) {
    $hasItinerarySent = true;
} elseif (!empty($quotationsMap)) {
    $hasItinerarySent = true;
} else {
    foreach ($rawTimelineRows as $rtr) {
        if (strtolower(trim($rtr['enquiry_notes_meta_type'] ?? '')) === 'itinerary') {
            $hasItinerarySent = true;
            break;
        }
    }
    $stId = (int)($lead['enquiry_status'] ?? 0);
    if (!$hasItinerarySent && in_array($stId, [9, 23, 24, 25, 26, 27], true)) {
        $hasItinerarySent = true;
    }
}
$lead['has_itinerary_sent'] = $hasItinerarySent;

// 4. Fetch All Active Agents for inline re-assign
$agents = [];
$agentsQuery = "SELECT 
    u.user_id,
    p.profile_display_name,
    sa.site_access_type
FROM {$tblPrefix}user u
INNER JOIN {$tblPrefix}profile p ON u.user_id = p.profile_user_id
INNER JOIN {$tblPrefix}site_access sa ON u.user_id = sa.site_access_user_id
WHERE sa.site_access_status = 1 AND sa.site_access_site_id = {$siteId}
ORDER BY p.profile_display_name ASC";

if ($aRes = mysqli_query($connection, $agentsQuery)) {
    while ($aRow = mysqli_fetch_assoc($aRes)) {
        $role = !empty($aRow['site_access_type']) ? ucwords(str_replace('_', ' ', $aRow['site_access_type'])) : 'Representative';
        $agents[] = [
            'id'      => (int)$aRow['user_id'],
            'user_id' => (int)$aRow['user_id'],
            'name'    => trim($aRow['profile_display_name'] ?? 'Agent'),
            'role'    => $role,
        ];
    }
}

// 5. Fetch Enquiry Profile (Travel Logistics)
$profileQuery = "SELECT * FROM {$tblPrefix}enquiry_profile WHERE enquiry_profile_ref_id = '{$leadId}' ORDER BY status DESC, enquiry_profile_id DESC LIMIT 1";
$enquiryProfile = [];
if ($pRes = mysqli_query($connection, $profileQuery)) {
    if ($pRow = mysqli_fetch_assoc($pRes)) {
        $enquiryProfile = $pRow;
    }
}

// Format Dates
$travelDateRaw = !empty($enquiryProfile['date_of_travel']) && $enquiryProfile['date_of_travel'] !== '0000-00-00'
    ? $enquiryProfile['date_of_travel']
    : ($extraDetails['date_of_travel'] ?? ($extraDetails['travel_date'] ?? ($mappedFromJson['date_of_travel'] ?? '')));

$travelDate = (!empty($travelDateRaw) && strtotime($travelDateRaw) && date('Y', strtotime($travelDateRaw)) > 1970)
    ? date('d M Y', strtotime($travelDateRaw))
    : trim((string)$travelDateRaw);

$durationDays = isset($enquiryProfile['number_of_days']) && (int)$enquiryProfile['number_of_days'] > 0
    ? (int)$enquiryProfile['number_of_days']
    : (int)($extraDetails['number_of_days'] ?? 0);
if ($durationDays <= 0 && !empty($mappedFromJson['duration_days'])) {
    if (preg_match('/(\d+)/', $mappedFromJson['duration_days'], $mDur)) {
        $durationDays = (int)$mDur[1];
    }
}

$adults = isset($enquiryProfile['number_of_adults']) && (int)$enquiryProfile['number_of_adults'] > 0
    ? (int)$enquiryProfile['number_of_adults']
    : (isset($extraDetails['number_of_adults']) && (int)$extraDetails['number_of_adults'] > 0 ? (int)$extraDetails['number_of_adults'] : 0);
if ($adults <= 0 && !empty($mappedFromJson['adults'])) {
    if (preg_match('/(\d+)/', $mappedFromJson['adults'], $mAd)) {
        $adults = (int)$mAd[1];
    }
}
if ($adults <= 0) $adults = 2;

$children = isset($enquiryProfile['number_of_childrens'])
    ? (int)$enquiryProfile['number_of_childrens']
    : (int)($extraDetails['number_of_childrens'] ?? ($extraDetails['number_of_children'] ?? 0));
if ($children <= 0 && !empty($mappedFromJson['children'])) {
    if (preg_match('/(\d+)/', $mappedFromJson['children'], $mCh)) {
        $children = (int)$mCh[1];
    }
}

$infants = isset($enquiryProfile['number_of_infants'])
    ? (int)$enquiryProfile['number_of_infants']
    : (int)($extraDetails['number_of_infants'] ?? 0);
if ($infants <= 0 && !empty($mappedFromJson['infants'])) {
    if (preg_match('/(\d+)/', $mappedFromJson['infants'], $mInf)) {
        $infants = (int)$mInf[1];
    }
}

$currentLocation = trim($enquiryProfile['current_location'] ?? ($extraDetails['current_location'] ?? ($lead['enquiry_city'] ?? '')));
if (empty($currentLocation) && !empty($mappedFromJson['current_location'])) {
    $currentLocation = $mappedFromJson['current_location'];
}

$pickupLocation = trim($enquiryProfile['pickup_location'] ?? ($extraDetails['pickup_location'] ?? ''));
if (empty($pickupLocation) && !empty($mappedFromJson['pickup_location'])) {
    $pickupLocation = $mappedFromJson['pickup_location'];
}

$dropLocation = trim($enquiryProfile['drop_location'] ?? ($extraDetails['drop_location'] ?? ''));
if (empty($dropLocation) && !empty($mappedFromJson['drop_location'])) {
    $dropLocation = $mappedFromJson['drop_location'];
}
$requirements = trim($enquiryProfile['requirements'] ?? ($extraDetails['requirements'] ?? ($lead['enquiry_message'] ?? '')));
if (!empty($requirements)) {
    $parsedReq = json_decode($requirements, true);
    if (is_array($parsedReq)) {
        $cleanedReq = filterTrackingLeadData($parsedReq);
        $hasMeaningfulData = false;
        foreach ($cleanedReq as $k => $v) {
            if (is_array($v)) {
                if (!empty($v)) { $hasMeaningfulData = true; break; }
            } else if (trim((string)$v) !== '' && trim((string)$v) !== '#') {
                $hasMeaningfulData = true;
                break;
            }
        }
        $requirements = $hasMeaningfulData ? json_encode($cleanedReq) : '';
    }
}

$createdAt = !empty($lead['enquiry_time']) && $lead['enquiry_time'] !== '0000-00-00 00:00:00' ? date('d M Y, h:i A', strtotime($lead['enquiry_time'])) : '';
$updatedAt = !empty($lead['enquiry_update']) && $lead['enquiry_update'] !== '0000-00-00 00:00:00' ? date('d M Y, h:i A', strtotime($lead['enquiry_update'])) : $createdAt;

// Infer Destination from extraDetails, enquiry_city, or matching enquiry_subject with known cities/states
$destinationCity = trim($extraDetails['destination'] ?? ($extraDetails['city'] ?? ($lead['enquiry_city'] ?? '')));
if (empty($destinationCity) && !empty($lead['enquiry_subject'])) {
    $rawSubj = trim($lead['enquiry_subject']);
    $cleanSubj = trim(preg_replace('/\b(\d+N\/?\d*D|\d+\s*(nights?|days?)|tour|package|holiday|trip|honeymoon|special|custom|itinerary|vacation|group|couple|family|deluxe|standard|premium|budget|super|luxury|stay|resort|hotel|leads?|form|v\d+|travel)\b/i', '', $rawSubj));
    $cleanSubj = trim(preg_replace('/[^a-zA-Z\s]/', ' ', $cleanSubj));
    $cleanSubj = trim(preg_replace('/\s+/', ' ', $cleanSubj));

    $isGeneric = preg_match('/\b(whatsapp|web\s*inquiry|general\s*enquiry|tour\s*package\s*enquiry|website)\b/i', $rawSubj);

    if (!empty($cleanSubj) && !$isGeneric) {
        $cleanSubjEsc = mysqli_real_escape_string($connection, $cleanSubj);
        // Check exact city match
        $cityMatchQ = "SELECT city_name FROM {$tblPrefix}city WHERE LOWER(TRIM(city_name)) = LOWER('{$cleanSubjEsc}') LIMIT 1";
        if ($cmRes = mysqli_query($connection, $cityMatchQ)) {
            if ($cmRow = mysqli_fetch_assoc($cmRes)) {
                $destinationCity = trim($cmRow['city_name']);
            }
        }
        // Check exact state match
        if (empty($destinationCity)) {
            $stateMatchQ = "SELECT state_name FROM {$tblPrefix}state WHERE LOWER(TRIM(state_name)) = LOWER('{$cleanSubjEsc}') LIMIT 1";
            if ($smRes = mysqli_query($connection, $stateMatchQ)) {
                if ($smRow = mysqli_fetch_assoc($smRes)) {
                    $destinationCity = trim($smRow['state_name']);
                }
            }
        }
        // If no exact match found in city/state tables, preserve clean subject or raw subject as destination
        if (empty($destinationCity)) {
            $destinationCity = !empty($cleanSubj) ? ucwords(strtolower($cleanSubj)) : ucwords(strtolower($rawSubj));
        }
    } elseif (!$isGeneric) {
        $destinationCity = ucwords(strtolower($rawSubj));
    }
}

// Calculate KPI 1: Next Follow-Up
$nextFollowup = [
    'is_scheduled' => false,
    'datetime' => '',
    'time_formatted' => 'Not Scheduled',
    'status_badge' => 'none',
    'label' => 'Not Scheduled'
];
if (!empty($lead['enquiry_followup']) && $lead['enquiry_followup'] !== '0000-00-00 00:00:00') {
    $fTs = strtotime($lead['enquiry_followup']);
    if ($fTs && date('Y', $fTs) > 2000) {
        $now = time();
        $isToday = (date('Y-m-d', $fTs) === date('Y-m-d', $now));
        $isOverdue = ($fTs < $now && !$isToday);
        
        $badge = 'upcoming';
        $badgeLabel = 'Upcoming';
        if ($isOverdue) {
            $badge = 'overdue';
            $badgeLabel = 'Overdue';
        } elseif ($isToday) {
            $badge = 'today';
            $badgeLabel = 'Due Today';
        }

        $nextFollowup = [
            'is_scheduled' => true,
            'datetime' => date('Y-m-d H:i:s', $fTs),
            'raw_time' => date('Y-m-d H:i:s', $fTs),
            'time_formatted' => date('d M Y, h:i A', $fTs),
            'status_badge' => $badge,
            'label' => $badgeLabel
        ];
    }
}

// Calculate KPI 2: Lead Age
$leadAgeDays = 0;
$leadAgeText = 'Active Today';
if (!empty($lead['enquiry_time']) && $lead['enquiry_time'] !== '0000-00-00 00:00:00') {
    $cTs = strtotime($lead['enquiry_time']);
    if ($cTs && $cTs > 0) {
        $diffSec = max(0, time() - $cTs);
        $leadAgeDays = floor($diffSec / 86400);
        if ($leadAgeDays >= 365) {
            $yrs = floor($leadAgeDays / 365);
            $leadAgeText = $yrs . ($yrs === 1 ? ' Year Active' : ' Years Active');
        } elseif ($leadAgeDays >= 30) {
            $mos = floor($leadAgeDays / 30);
            $leadAgeText = $mos . ($mos === 1 ? ' Month Active' : ' Months Active');
        } elseif ($leadAgeDays >= 1) {
            $leadAgeText = $leadAgeDays . ($leadAgeDays === 1 ? ' Day Active' : ' Days Active');
        } else {
            $hrs = floor($diffSec / 3600);
            if ($hrs >= 1) {
                $leadAgeText = $hrs . ($hrs === 1 ? ' Hour Active' : ' Hours Active');
            } else {
                $mins = max(1, floor($diffSec / 60));
                $leadAgeText = $mins . ($mins === 1 ? ' Min Active' : ' Mins Active');
            }
        }
    }
}

// Calculate KPI 3: Touchpoints & Interactions
$totalTouchpoints = count($timeline);
$notesCount = 0;
$itinerariesCount = 0;
$paymentsCount = count($payments);
foreach ($timeline as $tItem) {
    if (($tItem['type'] ?? '') === 'itinerary') {
        $itinerariesCount++;
    } elseif (!in_array($tItem['type'] ?? '', ['generate', 'payment'])) {
        $notesCount++;
    }
}

// Interest Level
$interestLevel = !empty($lead['enquiry_interest_level']) ? $lead['enquiry_interest_level'] : 'Unknown';
$interestMeta = getInterestLevelMeta($interestLevel);

// Response Tracking & WhatsApp Integration
$lastContactedAt = $lead['enquiry_last_contacted_at'] ?? null;
$lastResponseAt  = $lead['enquiry_last_customer_response_at'] ?? null;

// Reconcile with WhatsApp messages if available
$chatCleanPhone = preg_replace('/[^0-9]/', '', (string)($lead['enquiry_mobile'] ?? ''));
if (!empty($chatCleanPhone)) {
    $waPhoneVariants = "'" . mysqli_real_escape_string($connection, $chatCleanPhone) . "'";
    if (strlen($chatCleanPhone) === 10) {
        $waPhoneVariants .= ",'91" . mysqli_real_escape_string($connection, $chatCleanPhone) . "'";
    }
    $waQ = "SELECT 
        MAX(CASE WHEN m.message_direction = 'incoming' THEN m.message_created_at ELSE NULL END) AS wa_last_inbound,
        MAX(CASE WHEN m.message_direction = 'outgoing' THEN m.message_created_at ELSE NULL END) AS wa_last_outbound
    FROM {$tblPrefix}whatsapp_chat c
    INNER JOIN {$tblPrefix}whatsapp_message m ON m.message_chat_id = c.chat_id
    WHERE c.chat_lead_id = {$leadId} OR c.chat_phone IN ({$waPhoneVariants})";
    if ($waRes = mysqli_query($connection, $waQ)) {
        if ($waRow = mysqli_fetch_assoc($waRes)) {
            if (!empty($waRow['wa_last_inbound']) && (empty($lastResponseAt) || strtotime($waRow['wa_last_inbound']) > strtotime($lastResponseAt))) {
                $lastResponseAt = $waRow['wa_last_inbound'];
            }
            if (!empty($waRow['wa_last_outbound']) && (empty($lastContactedAt) || strtotime($waRow['wa_last_outbound']) > strtotime($lastContactedAt))) {
                $lastContactedAt = $waRow['wa_last_outbound'];
            }
        }
    }
}

$responseTracking = calculateCustomerResponseStatus($lastContactedAt, $lastResponseAt, (int)($lead['enquiry_status'] ?? 0));

// Fetch Follow-up Tasks from isw_enquiry_task
$tasksList = [];
$taskTblChk = @mysqli_query($connection, "SHOW TABLES LIKE '{$tblPrefix}enquiry_task'");
if ($taskTblChk && mysqli_num_rows($taskTblChk) > 0) {
    $tasksQuery = "SELECT t.*, p.profile_display_name AS agent_name, cp.profile_display_name AS completed_by_name
                   FROM {$tblPrefix}enquiry_task t
                   LEFT JOIN {$tblPrefix}profile p ON t.task_agent_id = p.profile_user_id
                   LEFT JOIN {$tblPrefix}profile cp ON t.task_completed_by = cp.profile_user_id
                   WHERE t.task_enquiry_id = {$leadId}
                   ORDER BY CASE WHEN t.task_status = 'pending' THEN 0 ELSE 1 END, t.task_due_at ASC";
    if ($tRes = @mysqli_query($connection, $tasksQuery)) {
    while ($tRow = mysqli_fetch_assoc($tRes)) {
        $tDueTs = strtotime($tRow['task_due_at']);
        $tIsToday = (date('Y-m-d', $tDueTs) === date('Y-m-d'));
        $tIsOverdue = ($tDueTs < time() && !$tIsToday && $tRow['task_status'] === 'pending');
        $tasksList[] = [
            'id'             => (int)$tRow['task_id'],
            'type'           => $tRow['task_type'],
            'due_at'         => $tRow['task_due_at'],
            'due_formatted'  => date('d M Y, h:i A', $tDueTs),
            'notes'          => $tRow['task_notes'] ?? '',
            'next_action'    => $tRow['task_next_action'] ?? '',
            'status'         => $tRow['task_status'],
            'outcome'        => $tRow['task_outcome'] ?? '',
            'completed_at'   => !empty($tRow['task_completed_at']) ? date('d M Y, h:i A', strtotime($tRow['task_completed_at'])) : null,
            'completed_by'   => $tRow['completed_by_name'] ?? '',
            'agent_id'       => (int)$tRow['task_agent_id'],
            'agent_name'     => $tRow['agent_name'] ?? 'Agent',
            'is_overdue'     => $tIsOverdue,
            'is_today'       => $tIsToday,
        ];
    }
}
}

// Fetch Linked Booking Information
$bookingInfo = null;
$bookingQuery = "SELECT booking_id, booking_date, booking_time, booking_service, booking_type, booking_status, booking_update
                 FROM {$tblPrefix}booking
                 WHERE booking_enquiry_id = {$leadId}" . ($siteId > 0 ? " AND booking_site_id = {$siteId}" : "") . "
                 ORDER BY booking_id DESC LIMIT 1";
if ($bRes = mysqli_query($connection, $bookingQuery)) {
    if ($bRow = mysqli_fetch_assoc($bRes)) {
        $bSt = (int)$bRow['booking_status'];
        $bStatusLabel = ($bSt === 1) ? 'Confirmed' : (($bSt === 0) ? 'Pending' : 'Cancelled');
        $bookingInfo = [
            'id'           => (int)$bRow['booking_id'],
            'service'      => $bRow['booking_service'] ?: 'Travel Package',
            'type'         => $bRow['booking_type'],
            'date'         => $bRow['booking_date'],
            'status'       => $bSt,
            'status_label' => $bStatusLabel,
            'updated_at'   => $bRow['booking_update'],
        ];
    }
}

$kpis = [
    'next_followup' => $nextFollowup,
    'lead_age' => [
        'days' => (int)$leadAgeDays,
        'text' => $leadAgeText,
        'created_at' => $createdAt,
    ],
    'touchpoints' => [
        'total' => $totalTouchpoints,
        'notes_count' => $notesCount,
        'itineraries_count' => $itinerariesCount,
        'payments_count' => $paymentsCount,
        'summary' => "{$totalTouchpoints} Touchpoint" . ($totalTouchpoints === 1 ? '' : 's')
    ],
    'response_indicator' => $responseTracking,
    'interest_level'     => $interestMeta,
];

$response = [
    'status' => 'success',
    'data' => [
        'id' => $leadId,
        'encoded_id' => base64_encode((string)$leadId),
        'name' => $fullName,
        'first_name' => $firstName,
        'middle_name' => $middleName,
        'last_name' => $lastName,
        'email' => trim($lead['enquiry_email'] ?? ''),
        'mobile' => trim($lead['enquiry_mobile'] ?? ''),
        'alt_mobile' => trim($lead['enquiry_alt_mobile'] ?? ''),
        'wa_mobile' => $waMobile,
        'status' => (int)($lead['enquiry_status'] ?? 0),
        'status_label' => $statusMeta['label'],
        'status_class' => $statusMeta['class'],
        'status_color' => $statusMeta['color'],
        'status_icon'  => $statusMeta['icon'],
        'interest_level' => $interestLevel,
        'interest_meta'  => $interestMeta,
        'response_tracking' => $responseTracking,
        'last_contacted_at' => $lastContactedAt ? date('d M Y, h:i A', strtotime($lastContactedAt)) : null,
        'last_customer_response_at' => $lastResponseAt ? date('d M Y, h:i A', strtotime($lastResponseAt)) : null,
        'tasks' => $tasksList,
        'booking' => $bookingInfo,
        'all_statuses' => getAleyaLeadStatuses(),
        'all_interests' => getAleyaInterestLevels(),
        'all_response_statuses' => getAleyaResponseStatuses(),
        'subject' => !empty($lead['enquiry_subject']) ? ucwords(strtolower(trim($lead['enquiry_subject']))) : 'Tour Package Enquiry',
        'source' => !empty($lead['enquiry_source']) ? ucfirst($lead['enquiry_source']) : 'Direct',
        'medium' => trim($lead['enquiry_medium'] ?? ''),
        'campaign' => trim($lead['enquiry_campaign'] ?? ''),
        'created_at' => $createdAt,
        'updated_at' => $updatedAt,
        'agent_id' => (int)($lead['enquiry_agent'] ?? 0),
        'agent_name' => !empty($lead['agent_name']) ? $lead['agent_name'] : 'Unassigned',
        'agent_role' => !empty($lead['agent_role']) ? ucwords(str_replace('_', ' ', $lead['agent_role'])) : 'Representative',

        // Key Performance Indicators (KPIs)
        'kpis' => $kpis,

        // Travel & Inquiry Details
        'travel_details' => [
            'date_of_travel' => $travelDate,
            'date_of_travel_raw' => (!empty($travelDateRaw) && strtotime($travelDateRaw) && date('Y', strtotime($travelDateRaw)) > 1970) ? date('Y-m-d', strtotime($travelDateRaw)) : '',
            'duration_days' => (int)$durationDays,
            'adults' => (int)$adults,
            'children' => (int)$children,
            'infants' => (int)$infants,
            'current_location' => $currentLocation,
            'pickup_location' => $pickupLocation,
            'drop_location' => $dropLocation,
            'destination' => $destinationCity,
            'requirements' => $requirements,
            'trip_type' => $extraDetails['trip_type'] ?? 'outstation_round',
            'vehicle_type' => $extraDetails['vehicle_type'] ?? ($extraDetails['vehicle']['type'] ?? ''),
            'vehicle_model' => $extraDetails['vehicle_model'] ?? ($extraDetails['vehicle']['title'] ?? ''),
            'luggage_bags' => (int)($extraDetails['luggage_bags'] ?? 0),
            'custom_fields' => $extraDetails,
        ],

        // Related Collections
        'quotation' => $quotation,
        'has_itinerary_sent' => $hasItinerarySent,
        'timeline' => $timeline,
        'payments' => $payments,
        'agents' => $agents,
        'access_type' => $accessType,
        'is_calling_agent' => in_array(strtolower(trim($accessType)), ['agent', 'calling_agent', 'crm_calling_agent', 'crm calling agent', 'sales_executive']),
        'whatsapp_chat' => $chatMeta,
        'current_user_id' => (int)$currentUserId,
        'site_package_id' => defined('SITE_PACKAGE_ID') ? (int)SITE_PACKAGE_ID : (isset($site_package_id) ? (int)$site_package_id : 7),
    ]
];

// Fetch live app & lead view configurations directly from site_configuration table
$siteConfigs = [];
$effectiveSiteId = ($siteId > 0) ? $siteId : (int)($lead['enquiry_site_id'] ?? 0);
if ($effectiveSiteId <= 0 && isset($_SESSION['site_id'])) {
    $effectiveSiteId = (int)$_SESSION['site_id'];
}
if ($effectiveSiteId <= 0 && isset($_COOKIE['user_site_id'])) {
    $effectiveSiteId = (int)base64_decode($_COOKIE['user_site_id']);
}
if ($effectiveSiteId <= 0) {
    $effectiveSiteId = 1;
}

if ($effectiveSiteId > 0 && isset($connection)) {
    $cfgKeys = [
        'lead_view_show_payment_link',
        'lead_view_show_record_payment',
        'lead_view_show_convert_sale',
        'lead_view_show_call_button',
        'lead_view_show_whatsapp_button',
        'lead_view_show_send_itinerary',
        'lead_view_show_send_quotation',
        'lead_view_show_send_photos',
        'company_nature',
        'currency_symbol',
        'currency_default',
        'advance_payment_percent',
        'transporter_billing_terms'
    ];
    $cfgKeyList = "'" . implode("','", $cfgKeys) . "'";
    $cfgRes = mysqli_query($connection, "SELECT site_configuration_key, site_configuration_value FROM {$tblPrefix}site_configuration WHERE site_configuration_site_id = {$effectiveSiteId} AND site_configuration_key IN ({$cfgKeyList}) AND site_configuration_status = 1");
    if ($cfgRes && mysqli_num_rows($cfgRes) > 0) {
        while ($cfgRow = mysqli_fetch_assoc($cfgRes)) {
            $siteConfigs[$cfgRow['site_configuration_key']] = $cfgRow['site_configuration_value'];
        }
    }
}

$effectiveCompanyNature = !empty($siteConfigs['company_nature']) ? strtolower($siteConfigs['company_nature']) : (defined('COMPANY_NATURE') ? strtolower(COMPANY_NATURE) : 'travel_agency');
$effectiveCurrencySymbol = !empty($siteConfigs['currency_symbol']) ? $siteConfigs['currency_symbol'] : (defined('CURRENCY_SYMBOL') ? CURRENCY_SYMBOL : '₹');
$effectiveCurrencyDefault = !empty($siteConfigs['currency_default']) ? $siteConfigs['currency_default'] : (defined('CURRENCY_DEFAULT') ? CURRENCY_DEFAULT : 'INR');
$effectiveAdvancePercent = !empty($siteConfigs['advance_payment_percent']) ? (float)$siteConfigs['advance_payment_percent'] : (defined('ADVANCE_PAYMENT_PERCENT') ? (float)ADVANCE_PAYMENT_PERCENT : 25);
$effectiveTransporterTerms = !empty($siteConfigs['transporter_billing_terms']) ? json_decode($siteConfigs['transporter_billing_terms'], true) : null;

// Resolve active WhatsApp device for current user & site to guide UI toggles
require_once dirname(__DIR__) . '/whatsapp/device_helper.php';
$resolvedWaDevice = resolveSenderWhatsAppDevice($connection, $effectiveSiteId, $currentUserId);

// Check if current logged-in user specifically has an assigned WhatsApp device on this site
$userDevQ = mysqli_query($connection, "SELECT device_id, device_name, device_phone, device_status FROM {$tblPrefix}whatsapp_device WHERE device_site_id = {$effectiveSiteId} AND device_assigned_user_id = {$currentUserId} LIMIT 1");
$userAssignedDevice = ($userDevQ && mysqli_num_rows($userDevQ) > 0) ? mysqli_fetch_assoc($userDevQ) : null;
$hasAssignedDevice = !empty($userAssignedDevice);
$isAdminUser = in_array(strtolower(trim($accessType)), ['administrator', 'admin', 'superadmin', 'super_admin'], true);
$canUseCrmWhatsapp = ($isAdminUser || $hasAssignedDevice);

// Resolve current user display name for intro message
$currentUserName = '';
if ($currentUserId > 0) {
    $currUserQ = mysqli_query($connection, "SELECT p.profile_display_name, p.profile_first_name, p.profile_last_name, u.user_username FROM {$tblPrefix}user u LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id WHERE u.user_id = {$currentUserId} LIMIT 1");
    if ($currUserQ && $currUserRow = mysqli_fetch_assoc($currUserQ)) {
        $currentUserName = !empty($currUserRow['profile_display_name']) ? trim($currUserRow['profile_display_name']) : (!empty($currUserRow['profile_first_name']) ? trim($currUserRow['profile_first_name'] . ' ' . ($currUserRow['profile_last_name'] ?? '')) : trim($currUserRow['user_username']));
    }
}
if (empty($currentUserName)) {
    $currentUserName = 'Our Team';
}

$effectiveCompanyName = defined('SITE_NAME') ? SITE_NAME : '';
if (empty($effectiveCompanyName) && !empty($siteConfigs['company_name'])) {
    $effectiveCompanyName = trim($siteConfigs['company_name']);
}
if (empty($effectiveCompanyName)) {
    $effectiveCompanyName = 'Our Team';
}

$customerFirstName = trim($lead['first_name'] ?? '');
$customerFullName = trim($lead['full_name'] ?? ($lead['name'] ?? $customerFirstName));
$customerSalutation = !empty($customerFirstName) ? $customerFirstName : (!empty($customerFullName) ? $customerFullName : 'there');
$leadDestination = trim($destinationCity ?? ($lead['enquiry_subject'] ?? ''));
$destPlaceholder = !empty($leadDestination) ? $leadDestination : 'your upcoming trip';
$datesPlaceholder = !empty($travelDate) ? $travelDate : 'your upcoming dates';

// Retrieve or construct predefined intro message
$introTpl = null;
if (!empty($siteConfigs['whatsapp_intro_message'])) {
    $introTpl = trim($siteConfigs['whatsapp_intro_message']);
} elseif (!empty($siteConfigs['company_whatsapp_intro_message'])) {
    $introTpl = trim($siteConfigs['company_whatsapp_intro_message']);
} else {
    $tplQ = mysqli_query($connection, "SELECT template_content FROM {$tblPrefix}whatsapp_template WHERE template_site_id IN ({$effectiveSiteId}, 0) AND template_category IN ('manual_quick_reply', 'lead_welcome') AND template_status = 1 ORDER BY (template_site_id = {$effectiveSiteId}) DESC, template_id ASC LIMIT 1");
    if ($tplQ && $tplRow = mysqli_fetch_assoc($tplQ)) {
        $introTpl = trim($tplRow['template_content']);
    }
}

if (empty($introTpl)) {
    if ($effectiveCompanyNature === 'transporter') {
        $introTpl = "Hello {customer_name}! 👋 Welcome to *{company_name}*.\n\nI am *{agent_name}*. I am reaching out regarding your vehicle rental and travel requirement for *{destination}*.\n\nHow can I assist you today? Please feel free to share your travel dates, route, or preferred vehicle! 🚗";
    } else {
        $introTpl = "Hello {customer_name}! 👋 Welcome to *{company_name}*.\n\nI am *{agent_name}*, your dedicated Travel Specialist. I am reaching out regarding your trip inquiry for *{destination}*.\n\nHow can I assist you today? Feel free to share your travel dates, group size, or preferred holiday style! ✈️";
    }
}

$replacements = [
    '{customer_name}' => $customerSalutation,
    '{name}'          => $customerSalutation,
    '{company_name}'  => $effectiveCompanyName,
    '{company}'       => $effectiveCompanyName,
    '{agent_name}'    => $currentUserName,
    '{destination}'   => $destPlaceholder,
    '{dates}'         => $datesPlaceholder,
    '{lead_id}'       => (string)$leadId,
];
$introMessage = str_replace(array_keys($replacements), array_values($replacements), $introTpl);

// Format clean phone for WhatsApp
$leadWaPhone = !empty($lead['wa_mobile']) ? $lead['wa_mobile'] : (!empty($lead['mobile']) ? $lead['mobile'] : '');
$cleanWaDigits = preg_replace('/[^0-9]/', '', (string)$leadWaPhone);
if (strlen($cleanWaDigits) === 10) {
    $cleanWaDigits = '91' . $cleanWaDigits;
}

$hasExistingChat = !empty($chatMeta) && !empty($chatMeta['chat_id']);
$existingChatId = $hasExistingChat ? (int)$chatMeta['chat_id'] : 0;

$waMeUrl = !empty($cleanWaDigits) ? ('https://wa.me/' . $cleanWaDigits . '?text=' . rawurlencode($introMessage)) : '';
$crmChatUrl = '';
if (!empty($cleanWaDigits)) {
    if ($hasExistingChat) {
        $crmChatUrl = '/dashboard/whatsapp/?phone=' . $cleanWaDigits . '&chat_id=' . $existingChatId;
    } else {
        $crmChatUrl = '/dashboard/whatsapp/?phone=' . $cleanWaDigits . '&name=' . rawurlencode($customerFullName) . '&lead_id=' . rawurlencode((string)$rawId) . '&new_chat=1';
    }
}

$response['data']['whatsapp_device'] = [
    'available'    => $resolvedWaDevice ? true : false,
    'phone_number' => $userAssignedDevice ? ($userAssignedDevice['device_phone'] ?? '') : ($resolvedWaDevice['device_phone'] ?? ($resolvedWaDevice['device_phone_number'] ?? '')),
    'name'         => $userAssignedDevice ? ($userAssignedDevice['device_name'] ?? '') : ($resolvedWaDevice['device_name'] ?? ''),
    'reason'       => $hasAssignedDevice ? 'user_assigned' : ($resolvedWaDevice['resolved_reason'] ?? 'default'),
    'is_assigned'  => $hasAssignedDevice,
    'status'       => $userAssignedDevice ? ($userAssignedDevice['device_status'] ?? 'disconnected') : ($resolvedWaDevice['device_status'] ?? 'disconnected'),
];

$response['data']['whatsapp_action'] = [
    'can_use_crm_whatsapp' => $canUseCrmWhatsapp,
    'is_assigned'          => $hasAssignedDevice,
    'is_admin'             => $isAdminUser,
    'has_existing_chat'    => $hasExistingChat,
    'existing_chat_id'     => $existingChatId,
    'intro_message'        => $introMessage,
    'wa_me_url'            => $waMeUrl,
    'crm_chat_url'         => $crmChatUrl,
    'target_url'           => $canUseCrmWhatsapp ? $crmChatUrl : $waMeUrl,
    'is_wame'              => !$canUseCrmWhatsapp,
];


$response['data']['company_nature'] = $effectiveCompanyNature;
$response['data']['currency_symbol'] = $effectiveCurrencySymbol;
$response['data']['currency_default'] = $effectiveCurrencyDefault;
$response['data']['advance_payment_percent'] = $effectiveAdvancePercent;
$response['data']['transporter_billing_terms'] = $effectiveTransporterTerms;
$response['data']['lead_view_options'] = [
    'show_payment_link'    => isset($siteConfigs['lead_view_show_payment_link']) ? ($siteConfigs['lead_view_show_payment_link'] !== '0') : (defined('LEAD_VIEW_SHOW_PAYMENT_LINK') ? (LEAD_VIEW_SHOW_PAYMENT_LINK !== '0') : true),
    'show_record_payment'  => isset($siteConfigs['lead_view_show_record_payment']) ? ($siteConfigs['lead_view_show_record_payment'] !== '0') : (defined('LEAD_VIEW_SHOW_RECORD_PAYMENT') ? (LEAD_VIEW_SHOW_RECORD_PAYMENT !== '0') : true),
    'show_convert_sale'    => isset($siteConfigs['lead_view_show_convert_sale']) ? ($siteConfigs['lead_view_show_convert_sale'] !== '0') : (defined('LEAD_VIEW_SHOW_CONVERT_SALE') ? (LEAD_VIEW_SHOW_CONVERT_SALE !== '0') : true),
    'show_call_button'     => isset($siteConfigs['lead_view_show_call_button']) ? ($siteConfigs['lead_view_show_call_button'] !== '0') : (defined('LEAD_VIEW_SHOW_CALL_BUTTON') ? (LEAD_VIEW_SHOW_CALL_BUTTON !== '0') : true),
    'show_whatsapp_button' => isset($siteConfigs['lead_view_show_whatsapp_button']) ? ($siteConfigs['lead_view_show_whatsapp_button'] !== '0') : (defined('LEAD_VIEW_SHOW_WHATSAPP_BUTTON') ? (LEAD_VIEW_SHOW_WHATSAPP_BUTTON !== '0') : true),
    'show_send_itinerary'  => isset($siteConfigs['lead_view_show_send_itinerary']) ? ($siteConfigs['lead_view_show_send_itinerary'] !== '0') : (defined('LEAD_VIEW_SHOW_SEND_ITINERARY') ? (LEAD_VIEW_SHOW_SEND_ITINERARY !== '0') : true),
    'show_send_quotation'  => isset($siteConfigs['lead_view_show_send_quotation']) ? ($siteConfigs['lead_view_show_send_quotation'] !== '0') : (defined('LEAD_VIEW_SHOW_SEND_QUOTATION') ? (LEAD_VIEW_SHOW_SEND_QUOTATION !== '0') : true),
    'show_send_photos'     => isset($siteConfigs['lead_view_show_send_photos']) ? ($siteConfigs['lead_view_show_send_photos'] !== '0') : (defined('LEAD_VIEW_SHOW_SEND_PHOTOS') ? (LEAD_VIEW_SHOW_SEND_PHOTOS !== '0') : true),
];

echo json_encode($response);
exit();
