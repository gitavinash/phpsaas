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
LEFT JOIN {$tblPrefix}profile p ON e.enquiry_agent = p.profile_user_id
LEFT JOIN {$tblPrefix}site_access sa ON (e.enquiry_agent = sa.site_access_user_id AND sa.site_access_site_id = {$siteId})
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
            return ['label' => 'Fresh Lead', 'class' => 'badge-fresh', 'color' => '#0284c7'];
        case 1:
            return ['label' => 'Confirmed', 'class' => 'badge-confirmed', 'color' => '#10b981'];
        case 2:
            return ['label' => 'Pending', 'class' => 'badge-pending', 'color' => '#64748b'];
        case 3:
            return ['label' => 'Follow-Up', 'class' => 'badge-followup', 'color' => '#f59e0b'];
        case 4:
            return ['label' => 'Cancelled', 'class' => 'badge-cancelled', 'color' => '#ef4444'];
        case 5:
            return ['label' => 'Duplicate', 'class' => 'badge-duplicate', 'color' => '#8b5cf6'];
        case 6:
            return ['label' => 'Payment Initiated', 'class' => 'badge-payment-initiated', 'color' => '#06b6d4'];
        case 8:
            return ['label' => 'Closed', 'class' => 'badge-closed', 'color' => '#475569'];
        default:
            return ['label' => 'Active', 'class' => 'badge-fresh', 'color' => '#0284c7'];
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

if ($tRes = mysqli_query($connection, $timelineQuery)) {
    while ($tRow = mysqli_fetch_assoc($tRes)) {
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

        if ($noteType === 'payment') {
            $payData = json_decode($noteVal, true);
            if (is_array($payData)) {
                $payments[] = [
                    'id' => (int)$tRow['enquiry_notes_id'],
                    'amount' => $payData['paymentAmount'] ?? '0',
                    'method' => $payData['paymentMethod'] ?? 'Standard',
                    'date' => $payData['paymentDate'] ?? date('d-m-Y', strtotime($updatedAt)),
                    'reference' => $payData['paymentReference'] ?? '',
                    'notes' => $payData['paymentMsg'] ?? '',
                    'image' => $payData['paymentImages'] ?? '',
                    'recorded_by' => $updatedBy,
                    'created_at' => $updatedAt,
                ];
            }
        }

        // Timeline item formatting
        $icon = 'fi fi-rr-comment-alt';
        $itemTitle = ucfirst($noteType);
        $itemDesc = $noteVal;

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
                $icon = 'fi fi-rr-comment-dots';
                $itemTitle = !empty($noteType) ? ucwords(str_replace('_', ' ', $noteType)) : 'Note';
                break;
        }

        $timeline[] = [
            'id' => (int)$tRow['enquiry_notes_id'],
            'type' => $noteType,
            'title' => $itemTitle,
            'description' => $itemDesc,
            'user_name' => $updatedBy,
            'time' => $updatedAt ? date('d M Y, h:i A', strtotime($updatedAt)) : '',
            'time_relative' => $timeAgo,
            'icon' => $icon,
        ];
    }
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
               AND quotation_status = 0 
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
            'id'   => (int)$aRow['user_id'],
            'name' => trim($aRow['profile_display_name'] ?? 'Agent'),
            'role' => $role,
        ];
    }
}

// Format Dates
$travelDate = !empty($extraDetails['date_of_travel']) ? date('d M Y', strtotime($extraDetails['date_of_travel'])) : '';
if ($travelDate === '01 Jan 1970' || empty($extraDetails['date_of_travel'])) {
    $travelDate = '';
}

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
        'created_at' => !empty($lead['enquiry_time']) ? date('d M Y, h:i A', strtotime($lead['enquiry_time'])) : '',
        'updated_at' => !empty($lead['enquiry_update']) ? date('d M Y, h:i A', strtotime($lead['enquiry_update'])) : '',
        'agent_id' => (int)($lead['enquiry_agent'] ?? 0),
        'agent_name' => !empty($lead['agent_name']) ? $lead['agent_name'] : 'Unassigned',
        'agent_role' => !empty($lead['agent_role']) ? ucwords(str_replace('_', ' ', $lead['agent_role'])) : 'Representative',

        // Travel & Inquiry Details
        'travel_details' => [
            'date_of_travel' => $travelDate,
            'duration_days' => (int)($extraDetails['number_of_days'] ?? 0),
            'adults' => (int)($extraDetails['number_of_adults'] ?? 0),
            'children' => (int)($extraDetails['number_of_childrens'] ?? 0),
            'infants' => (int)($extraDetails['number_of_infants'] ?? 0),
            'current_location' => trim($extraDetails['current_location'] ?? ''),
            'pickup_location' => trim($extraDetails['pickup_location'] ?? ''),
            'drop_location' => trim($extraDetails['drop_location'] ?? ''),
            'requirements' => trim($extraDetails['requirements'] ?? ''),
            'custom_fields' => $extraDetails,
        ],

        // Related Collections
        'quotation' => $quotation,
        'timeline' => $timeline,
        'payments' => $payments,
        'agents' => $agents,
        'access_type' => $accessType,
    ]
];

echo json_encode($response);
exit();
