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

// Helper for status badge metadata
function getLeadStatusMeta($status)
{
    switch ((int)$status) {
        case 0:
            return ['label' => 'Pending', 'class' => 'badge-fresh', 'color' => '#0284c7'];
        case 1:
            return ['label' => 'Confirmed', 'class' => 'badge-confirmed', 'color' => '#10b981'];
        case 2:
            return ['label' => 'Itinerary Sent', 'class' => 'badge-pending', 'color' => '#6366f1'];
        case 3:
            return ['label' => 'Follow-Up', 'class' => 'badge-followup', 'color' => '#f59e0b'];
        case 4:
            return ['label' => 'Cancelled / Lost', 'class' => 'badge-cancelled', 'color' => '#ef4444'];
        case 5:
            return ['label' => 'Duplicate', 'class' => 'badge-duplicate', 'color' => '#8b5cf6'];
        case 6:
            return ['label' => 'Payment Initiated', 'class' => 'badge-payment-initiated', 'color' => '#06b6d4'];
        case 8:
            return ['label' => 'Closed', 'class' => 'badge-closed', 'color' => '#475569'];
        default:
            return ['label' => 'Pending', 'class' => 'badge-fresh', 'color' => '#0284c7'];
    }
}

$statusMeta = getLeadStatusMeta($lead['enquiry_status'] ?? 0);

// Parse JSON inquiry message if present
$extraDetails = [];
if (!empty($lead['enquiry_message'])) {
    $parsed = json_decode($lead['enquiry_message'], true);
    if (is_array($parsed)) {
        $extraDetails = $parsed;
    }
}

// Clean names
$firstName = ucwords(strtolower(trim($lead['enquiry_first_name'] ?? '')));
$middleName = ucwords(strtolower(trim($lead['enquiry_middle_name'] ?? '')));
$lastName  = ucwords(strtolower(trim($lead['enquiry_last_name'] ?? '')));
$fullName  = trim("{$firstName} {$middleName} {$lastName}");
if (empty($fullName)) $fullName = 'Lead #' . $leadId;

// Format Mobile for WhatsApp
$cleanMobile = preg_replace('/[^0-9]/', '', $lead['enquiry_mobile'] ?? '');
$waMobile = '';
if (!empty($cleanMobile)) {
    if (strlen($cleanMobile) === 10) {
        $waMobile = '91' . $cleanMobile;
    } elseif (strlen($cleanMobile) >= 11) {
        $waMobile = $cleanMobile;
    }
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

            $quotationsMap[$qId] = [
                'id'           => $qId,
                'encoded_id'   => $encodedQId,
                'title'        => $qTitle,
                'price'        => $qPrice,
                'mrp'          => $qMrp,
                'duration'     => $qDuration,
                'destination'  => $qDest,
                'theme'        => $qTheme,
                'view_url'     => "/view-itinerary/{$encodedQId}/",
                'download_url' => "/download-itinerary/{$encodedQId}/",
            ];
        }
    }
}

foreach ($rawTimelineRows as $tRow) {
    $noteType = strtolower(trim($tRow['enquiry_notes_meta_type'] ?? ''));
    $noteVal  = trim($tRow['enquiry_notes_meta_value'] ?? '');
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
            $encId = base64_encode((string)$noteVal);
            $quoteInfo = [
                'id'           => (int)$noteVal,
                'encoded_id'   => $encId,
                'title'        => 'Tour Itinerary #' . $noteVal,
                'price'        => 0,
                'mrp'          => 0,
                'duration'     => '',
                'destination'  => '',
                'theme'        => '',
                'view_url'     => "/view-itinerary/{$encId}/",
                'download_url' => "/download-itinerary/{$encId}/",
            ];
            $itemDesc = 'Tour Itinerary #' . $noteVal;
        }
    }

    $timeline[] = [
        'id'            => (int)$tRow['enquiry_notes_id'],
        'type'          => $finalType,
        'title'         => $itemTitle,
        'category'      => $category,
        'followup_time' => $followupTimeFormatted,
        'description'   => $itemDesc,
        'payment_data'  => $paymentDetails,
        'quotation'     => $quoteInfo,
        'user_name'     => $updatedBy,
        'time'          => $updatedAt ? date('d M Y, h:i A', strtotime($updatedAt)) : '',
        'time_relative' => $timeAgo,
        'icon'          => $icon,
    ];
}

// 3. Fetch Active Quotation if available
$quotation = [
    'id' => 0,
    'price' => 0,
    'min_payment' => 0,
];

$quoteSql = "SELECT quotation_id, quotation_price 
             FROM {$tblPrefix}quotation 
             WHERE quotation_ref_id = '{$leadId}' 
               AND quotation_ref_type = 'enquiry' 
               AND quotation_site_id = {$siteId} 
             ORDER BY quotation_id DESC 
             LIMIT 1";

if ($qRes = mysqli_query($connection, $quoteSql)) {
    if ($qRow = mysqli_fetch_assoc($qRes)) {
        $qPrice = (float)($qRow['quotation_price'] ?? 0);
        $minPercent = defined('MINIMUM_PAYMENT') ? (float)MINIMUM_PAYMENT : 30;
        $quotation = [
            'id' => (int)$qRow['quotation_id'],
            'price' => $qPrice,
            'min_payment' => round(($qPrice * $minPercent) / 100, 2),
        ];
    }
}

// Fallback to itinerary quotation price if direct quotation record is 0
if ($quotation['price'] <= 0 && !empty($quotationsMap)) {
    foreach ($quotationsMap as $qm) {
        if (!empty($qm['price']) && (float)$qm['price'] > 0) {
            $quotation['price'] = (float)$qm['price'];
            $quotation['id']    = (int)($qm['id'] ?? 0);
            $minPercent = defined('MINIMUM_PAYMENT') ? (float)MINIMUM_PAYMENT : 30;
            $quotation['min_payment'] = round(($quotation['price'] * $minPercent) / 100, 2);
            break;
        }
    }
}

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
    : ($extraDetails['date_of_travel'] ?? ($extraDetails['travel_date'] ?? ''));

$travelDate = (!empty($travelDateRaw) && strtotime($travelDateRaw) && date('Y', strtotime($travelDateRaw)) > 1970)
    ? date('d M Y', strtotime($travelDateRaw))
    : '';

$durationDays = isset($enquiryProfile['number_of_days']) && (int)$enquiryProfile['number_of_days'] > 0
    ? (int)$enquiryProfile['number_of_days']
    : (int)($extraDetails['number_of_days'] ?? 0);

$adults = isset($enquiryProfile['number_of_adults']) && (int)$enquiryProfile['number_of_adults'] > 0
    ? (int)$enquiryProfile['number_of_adults']
    : (isset($extraDetails['number_of_adults']) && (int)$extraDetails['number_of_adults'] > 0 ? (int)$extraDetails['number_of_adults'] : 2);

$children = isset($enquiryProfile['number_of_childrens'])
    ? (int)$enquiryProfile['number_of_childrens']
    : (int)($extraDetails['number_of_childrens'] ?? ($extraDetails['number_of_children'] ?? 0));

$infants = isset($enquiryProfile['number_of_infants'])
    ? (int)$enquiryProfile['number_of_infants']
    : (int)($extraDetails['number_of_infants'] ?? 0);

$currentLocation = trim($enquiryProfile['current_location'] ?? ($extraDetails['current_location'] ?? ($lead['enquiry_city'] ?? '')));
$pickupLocation = trim($enquiryProfile['pickup_location'] ?? ($extraDetails['pickup_location'] ?? ''));
$dropLocation = trim($enquiryProfile['drop_location'] ?? ($extraDetails['drop_location'] ?? ''));
$requirements = trim($enquiryProfile['requirements'] ?? ($extraDetails['requirements'] ?? ($lead['enquiry_message'] ?? '')));

$createdAt = !empty($lead['enquiry_time']) && $lead['enquiry_time'] !== '0000-00-00 00:00:00' ? date('d M Y, h:i A', strtotime($lead['enquiry_time'])) : '';
$updatedAt = !empty($lead['enquiry_update']) && $lead['enquiry_update'] !== '0000-00-00 00:00:00' ? date('d M Y, h:i A', strtotime($lead['enquiry_update'])) : $createdAt;

// Infer Destination from extraDetails, enquiry_city, or matching enquiry_subject with known cities/states
$destinationCity = trim($extraDetails['destination'] ?? ($extraDetails['city'] ?? ($lead['enquiry_city'] ?? '')));
if (empty($destinationCity) && !empty($lead['enquiry_subject'])) {
    $cleanSubj = trim(preg_replace('/\b(\d+N\/?\d*D|\d+\s*(nights?|days?)|tour|package|holiday|trip|honeymoon|special|custom|itinerary|vacation|group|couple|family|deluxe|standard|premium|budget|super|luxury|stay|resort|hotel)\b/i', '', $lead['enquiry_subject']));
    $cleanSubj = trim(preg_replace('/[^a-zA-Z\s]/', ' ', $cleanSubj));
    if (!empty($cleanSubj)) {
        $cleanSubjEsc = mysqli_real_escape_string($connection, $cleanSubj);
        $cityMatchQ = "SELECT city_name FROM {$tblPrefix}city WHERE city_name LIKE '%{$cleanSubjEsc}%' OR '{$cleanSubjEsc}' LIKE CONCAT('%', city_name, '%') ORDER BY CHAR_LENGTH(city_name) DESC LIMIT 1";
        if ($cmRes = mysqli_query($connection, $cityMatchQ)) {
            if ($cmRow = mysqli_fetch_assoc($cmRes)) {
                $destinationCity = trim($cmRow['city_name']);
            }
        }
        if (empty($destinationCity)) {
            $stateMatchQ = "SELECT state_name FROM {$tblPrefix}state WHERE state_name LIKE '%{$cleanSubjEsc}%' OR '{$cleanSubjEsc}' LIKE CONCAT('%', state_name, '%') ORDER BY CHAR_LENGTH(state_name) DESC LIMIT 1";
            if ($smRes = mysqli_query($connection, $stateMatchQ)) {
                if ($smRow = mysqli_fetch_assoc($smRes)) {
                    $destinationCity = trim($smRow['state_name']);
                }
            }
        }
    }
    if (empty($destinationCity)) {
        $destinationCity = trim($lead['enquiry_subject']);
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
    ]
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
            'custom_fields' => $extraDetails,
        ],

        // Related Collections
        'quotation' => $quotation,
        'timeline' => $timeline,
        'payments' => $payments,
        'agents' => $agents,
        'access_type' => $accessType,
        'current_user_id' => (int)$currentUserId,
    ]
];

echo json_encode($response);
exit();
