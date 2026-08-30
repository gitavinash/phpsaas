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
$userRole   = strtolower(trim(isset($site_access_type) ? $site_access_type : (defined('USER_TYPE') ? USER_TYPE : 'agent')));

if (!in_array($userRole, ['administrator', 'accountant', 'operations', 'manager'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access to transaction details.']);
    exit();
}

$rawNoteId = isset($_GET['id']) ? $_GET['id'] : (isset($_GET['note_id']) ? $_GET['note_id'] : '');
$noteId = (int)$rawNoteId;

if ($noteId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Valid transaction ID is required.']);
    exit();
}

$siteFilter = ($siteId > 0) ? " AND e.enquiry_site_id = {$siteId}" : "";

// 1. Fetch Current Transaction & Lead Info
$txSql = "SELECT 
    n.enquiry_notes_id,
    n.enquiry_notes_ref_id AS lead_id,
    n.enquiry_notes_user_id AS recorded_user_id,
    n.enquiry_notes_meta_value AS pay_data,
    n.enquiry_notes_update AS recorded_at,
    e.enquiry_id,
    e.enquiry_first_name,
    e.enquiry_last_name,
    e.enquiry_mobile,
    e.enquiry_email,
    e.enquiry_status,
    e.enquiry_source,
    e.enquiry_subject,
    e.enquiry_time,
    e.enquiry_agent,
    p.profile_display_name AS agent_name,
    p.profile_email AS agent_profile_email,
    p.profile_mobile AS agent_profile_mobile,
    u.user_email AS agent_user_email,
    u.user_mobile AS agent_user_mobile,
    sa.site_access_type AS agent_role
FROM {$tblPrefix}enquiry_notes n
INNER JOIN {$tblPrefix}enquiry e ON n.enquiry_notes_ref_id = e.enquiry_id
LEFT JOIN {$tblPrefix}profile p ON n.enquiry_notes_user_id = p.profile_user_id
LEFT JOIN {$tblPrefix}user u ON n.enquiry_notes_user_id = u.user_id
LEFT JOIN {$tblPrefix}site_access sa ON (n.enquiry_notes_user_id = sa.site_access_user_id AND sa.site_access_site_id = {$siteId})
WHERE n.enquiry_notes_id = {$noteId} AND n.enquiry_notes_meta_type = 'payment' {$siteFilter}
LIMIT 1";

$txRes = mysqli_query($connection, $txSql);
if (!$txRes || mysqli_num_rows($txRes) === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Transaction not found.']);
    exit();
}

$row = mysqli_fetch_assoc($txRes);
$leadId = (int)$row['lead_id'];
$payJson = json_decode($row['pay_data'] ?? '', true) ?: [];

$firstName = trim($row['enquiry_first_name'] ?? '');
$lastName  = trim($row['enquiry_last_name'] ?? '');
$fullName  = trim($firstName . ' ' . $lastName);
if (empty($fullName)) $fullName = 'Customer #' . $leadId;

$pAmount = (float)($payJson['paymentAmount'] ?? 0);
$pStatus = strtolower(trim($payJson['paymentStatus'] ?? 'verified'));
$pMethod = trim($payJson['paymentMethod'] ?? 'Standard');
$pRef    = trim($payJson['paymentReference'] ?? '');
$pDate   = trim($payJson['paymentDate'] ?? date('d-m-Y', strtotime($row['recorded_at'])));

$rawImg  = $payJson['paymentImages'] ?? '';
$imgUrl  = '';
$imgName = '';
if (!empty($rawImg)) {
    if (is_array($rawImg)) {
        $imgUrl  = $rawImg['path'] ?? '';
        $imgName = $rawImg['name'] ?? '';
    } elseif (is_string($rawImg)) {
        $dec = json_decode($rawImg, true);
        if (is_string($dec)) $dec = json_decode($dec, true);
        if (is_array($dec)) {
            $imgUrl  = $dec['path'] ?? '';
            $imgName = $dec['name'] ?? '';
        } else {
            $imgUrl = $rawImg;
        }
    }
}

// 2. Fetch Quotation & Package details for this Lead
$quotePrice = 0.0;
$quoteTitle = '';
$quoteSql = "SELECT quotation_price, quotation_mrp, quotation_description, tp.tour_package_title
             FROM {$tblPrefix}quotation q
             LEFT JOIN {$tblPrefix}tour_package tp ON tp.tour_package_id = q.quotation_type_id
             WHERE q.quotation_ref_id = {$leadId}
             ORDER BY q.quotation_id DESC LIMIT 1";
$qRes = mysqli_query($connection, $quoteSql);
if ($qRes && mysqli_num_rows($qRes) > 0) {
    $qRow = mysqli_fetch_assoc($qRes);
    $quotePrice = (float)($qRow['quotation_price'] ?? 0);
    $quoteTitle = $qRow['tour_package_title'] ?? '';
    if (empty($quoteTitle) && !empty($qRow['quotation_description'])) {
        $desc = json_decode($qRow['quotation_description'], true);
        if (is_array($desc) && !empty($desc['title'])) {
            $quoteTitle = $desc['title'];
        }
    }
}

// 3. Fetch All Transactions / History for this Customer Lead
$historySql = "SELECT 
    n.enquiry_notes_id,
    n.enquiry_notes_meta_value AS pay_data,
    n.enquiry_notes_update AS recorded_at,
    p.profile_display_name AS agent_name
FROM {$tblPrefix}enquiry_notes n
LEFT JOIN {$tblPrefix}profile p ON n.enquiry_notes_user_id = p.profile_user_id
WHERE n.enquiry_notes_ref_id = {$leadId} AND n.enquiry_notes_meta_type = 'payment'
ORDER BY n.enquiry_notes_update DESC";

$historyList = [];
$totalPaid = 0.0;
$hRes = mysqli_query($connection, $historySql);
if ($hRes) {
    while ($hRow = mysqli_fetch_assoc($hRes)) {
        $hPay = json_decode($hRow['pay_data'] ?? '', true);
        if (!is_array($hPay)) continue;

        $hAmt = (float)($hPay['paymentAmount'] ?? 0);
        $hStatus = strtolower(trim($hPay['paymentStatus'] ?? 'verified'));
        if ($hStatus === 'verified') {
            $totalPaid += $hAmt;
        }

        $hRawImg = $hPay['paymentImages'] ?? '';
        $hImgUrl = '';
        if (!empty($hRawImg)) {
            if (is_array($hRawImg)) $hImgUrl = $hRawImg['path'] ?? '';
            elseif (is_string($hRawImg)) {
                $hDec = json_decode($hRawImg, true);
                if (is_array($hDec)) $hImgUrl = $hDec['path'] ?? '';
                else $hImgUrl = $hRawImg;
            }
        }

        $historyList[] = [
            'note_id'          => (int)$hRow['enquiry_notes_id'],
            'amount'           => $hAmt,
            'amount_formatted' => '₹ ' . number_format($hAmt, 2),
            'method'           => $hPay['paymentMethod'] ?? 'Standard',
            'reference'        => $hPay['paymentReference'] ?? 'None',
            'date'             => $hPay['paymentDate'] ?? date('d-m-Y', strtotime($hRow['recorded_at'])),
            'status'           => $hStatus,
            'status_label'     => ($hStatus === 'pending') ? 'Pending' : (($hStatus === 'rejected') ? 'Rejected' : 'Verified'),
            'image_url'        => $hImgUrl,
            'recorded_by'      => $hRow['agent_name'] ?: 'Representative',
            'recorded_at'      => $hRow['recorded_at'],
            'is_current'       => ((int)$hRow['enquiry_notes_id'] === $noteId),
        ];
    }
}

$pendingBalance = max(0, $quotePrice - $totalPaid);

echo json_encode([
    'status' => 'success',
    'transaction' => [
        'note_id'          => (int)$row['enquiry_notes_id'],
        'amount'           => $pAmount,
        'amount_formatted' => '₹ ' . number_format($pAmount, 2),
        'method'           => $pMethod,
        'reference'        => $pRef ?: 'None',
        'date'             => $pDate,
        'notes'            => $payJson['paymentMsg'] ?? '',
        'status'           => $pStatus,
        'status_label'     => ($pStatus === 'pending') ? 'Pending Verification' : (($pStatus === 'rejected') ? 'Rejected' : 'Verified'),
        'image'            => $rawImg,
        'image_url'        => $imgUrl,
        'image_name'       => $imgName,
        'recorded_by'        => $row['agent_name'] ?: 'Representative',
        'recorded_by_role'   => $row['agent_role'] ?: 'agent',
        'recorded_by_email'  => !empty($row['agent_profile_email']) ? $row['agent_profile_email'] : ($row['agent_user_email'] ?? ''),
        'recorded_by_mobile' => !empty($row['agent_profile_mobile']) ? $row['agent_profile_mobile'] : ($row['agent_user_mobile'] ?? ''),
        'verified_by'        => $payJson['verifiedBy'] ?? null,
        'verified_at'        => $payJson['verifiedAt'] ?? null,
        'rejected_by'        => $payJson['rejectedBy'] ?? null,
        'rejected_at'        => $payJson['rejectedAt'] ?? null,
        'rejection_reason'   => $payJson['rejectionReason'] ?? null,
        'recorded_at'        => $row['recorded_at'],
    ],
    'customer' => [
        'lead_id'          => $leadId,
        'lead_id_base64'   => base64_encode((string)$leadId),
        'name'             => $fullName,
        'mobile'           => $row['enquiry_mobile'] ?: '-',
        'email'            => $row['enquiry_email'] ?: '-',
        'lead_status'      => (int)($row['enquiry_status'] ?? 0),
        'source'           => $row['enquiry_source'] ?: 'Direct',
        'subject'          => $row['enquiry_subject'] ?: 'Tour Booking',
        'created_at'       => $row['enquiry_time'],
        'agent_name'       => $row['agent_name'] ?: 'Unassigned',
    ],
    'package' => [
        'title'            => $quoteTitle ?: 'Custom Tour Package',
        'quote_price'      => $quotePrice,
        'quote_formatted'  => '₹ ' . number_format($quotePrice, 2),
        'total_paid'       => $totalPaid,
        'paid_formatted'   => '₹ ' . number_format($totalPaid, 2),
        'pending_balance'  => $pendingBalance,
        'pending_formatted'=> '₹ ' . number_format($pendingBalance, 2),
        'is_paid_in_full'  => ($quotePrice > 0 && $pendingBalance <= 0),
    ],
    'history' => $historyList,
]);
exit();
