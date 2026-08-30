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

// Restrict access: Only administrator, accountant, operations, manager can view all accounts transactions
if (!in_array($userRole, ['administrator', 'accountant', 'operations', 'manager'])) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Unauthorized. Only administrators and accountants can access Accounts ledger.',
    ]);
    exit();
}

$page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit  = isset($_GET['limit']) ? min(100, max(5, (int)$_GET['limit'])) : 20;
$offset = ($page - 1) * $limit;

$search    = isset($_GET['search']) ? trim(mysqli_real_escape_string($connection, $_GET['search'])) : '';
$statusReq = isset($_GET['status']) ? strtolower(trim($_GET['status'])) : 'all';
$agentId   = isset($_GET['agent_id']) && is_numeric($_GET['agent_id']) ? (int)$_GET['agent_id'] : 0;
$methodReq = isset($_GET['method']) ? strtolower(trim($_GET['method'])) : '';
$fromDate  = isset($_GET['from_date']) ? trim($_GET['from_date']) : '';
$toDate    = isset($_GET['to_date']) ? trim($_GET['to_date']) : '';
$dateRange = isset($_GET['daterange']) ? trim($_GET['daterange']) : (isset($_GET['date_range']) ? trim($_GET['date_range']) : '');

if (!empty($dateRange)) {
    $parts = explode(' - ', $dateRange);
    if (count($parts) === 2) {
        $fromTs = strtotime(trim($parts[0]));
        $toTs   = strtotime(trim($parts[1]));
        if ($fromTs && $toTs) {
            $fromDate = date('Y-m-d', $fromTs);
            $toDate   = date('Y-m-d', $toTs);
        }
    }
}

$siteFilter = ($siteId > 0) ? " AND e.enquiry_site_id = {$siteId}" : "";

// Base SQL query for all payment notes
$sql = "SELECT 
    n.enquiry_notes_id,
    n.enquiry_notes_ref_id AS lead_id,
    n.enquiry_notes_user_id AS recorded_user_id,
    n.enquiry_notes_meta_value AS pay_data,
    n.enquiry_notes_update AS recorded_at,
    e.enquiry_first_name,
    e.enquiry_last_name,
    e.enquiry_mobile,
    e.enquiry_email,
    e.enquiry_status,
    p.profile_display_name AS agent_name,
    sa.site_access_type AS agent_role
FROM {$tblPrefix}enquiry_notes n
INNER JOIN {$tblPrefix}enquiry e ON n.enquiry_notes_ref_id = e.enquiry_id
LEFT JOIN {$tblPrefix}profile p ON n.enquiry_notes_user_id = p.profile_user_id
LEFT JOIN {$tblPrefix}site_access sa ON (n.enquiry_notes_user_id = sa.site_access_user_id AND sa.site_access_site_id = {$siteId})
WHERE n.enquiry_notes_meta_type = 'payment' {$siteFilter}";

if ($agentId > 0) {
    $sql .= " AND n.enquiry_notes_user_id = {$agentId}";
}

if (!empty($fromDate)) {
    $fromEsc = mysqli_real_escape_string($connection, $fromDate);
    $sql .= " AND DATE(n.enquiry_notes_update) >= '{$fromEsc}'";
}

if (!empty($toDate)) {
    $toEsc = mysqli_real_escape_string($connection, $toDate);
    $sql .= " AND DATE(n.enquiry_notes_update) <= '{$toEsc}'";
}

$sql .= " ORDER BY n.enquiry_notes_update DESC";

$allRows = [];
$res = mysqli_query($connection, $sql);

$totalVerifiedAmount = 0.0;
$totalPendingAmount  = 0.0;
$todayVerifiedAmount = 0.0;
$pendingCount        = 0;
$verifiedCount       = 0;
$todayDateStr        = date('Y-m-d');

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $payJson = json_decode($row['pay_data'] ?? '', true);
        if (!is_array($payJson)) continue;

        $pAmount = (float)($payJson['paymentAmount'] ?? 0);
        $pStatus = strtolower(trim($payJson['paymentStatus'] ?? 'verified'));
        $pMethod = trim($payJson['paymentMethod'] ?? 'Standard');
        $pRef    = trim($payJson['paymentReference'] ?? '');
        $pDate   = trim($payJson['paymentDate'] ?? date('d-m-Y', strtotime($row['recorded_at'])));
        $pDateYmd = date('Y-m-d', strtotime($row['recorded_at']));

        $firstName = trim($row['enquiry_first_name'] ?? '');
        $lastName  = trim($row['enquiry_last_name'] ?? '');
        $fullName  = trim($firstName . ' ' . $lastName);
        if (empty($fullName)) $fullName = 'Customer #' . $row['lead_id'];

        // Calculate KPI totals across all matched payments
        if ($pStatus === 'verified') {
            $totalVerifiedAmount += $pAmount;
            $verifiedCount++;
            if ($pDateYmd === $todayDateStr) {
                $todayVerifiedAmount += $pAmount;
            }
        } elseif ($pStatus === 'pending') {
            $totalPendingAmount += $pAmount;
            $pendingCount++;
        }

        // Apply in-memory filters for status, method, and text search if needed
        if ($statusReq !== 'all' && $pStatus !== $statusReq) {
            continue;
        }

        if (!empty($methodReq) && strtolower($pMethod) !== $methodReq) {
            continue;
        }

        if (!empty($search)) {
            $searchLower = strtolower($search);
            $cName   = strtolower($fullName);
            $cMobile = strtolower($row['enquiry_mobile'] ?? '');
            $cEmail  = strtolower($row['enquiry_email'] ?? '');
            $aName   = strtolower($row['agent_name'] ?? '');
            $refNum  = strtolower($pRef);
            $amtStr  = (string)$pAmount;

            if (
                strpos($cName, $searchLower) === false &&
                strpos($cMobile, $searchLower) === false &&
                strpos($cEmail, $searchLower) === false &&
                strpos($aName, $searchLower) === false &&
                strpos($refNum, $searchLower) === false &&
                strpos($amtStr, $searchLower) === false
            ) {
                continue;
            }
        }

        // Image parsing
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

        $allRows[] = [
            'note_id'          => (int)$row['enquiry_notes_id'],
            'lead_id'          => (int)$row['lead_id'],
            'customer_name'    => $fullName,
            'customer_mobile'  => $row['enquiry_mobile'] ?: '-',
            'customer_email'   => $row['enquiry_email'] ?: '-',
            'lead_status'      => (int)($row['enquiry_status'] ?? 0),
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
            'recorded_by'      => $row['agent_name'] ?: 'Representative',
            'recorded_by_role' => $row['agent_role'] ?: 'agent',
            'verified_by'      => $payJson['verifiedBy'] ?? null,
            'verified_at'      => $payJson['verifiedAt'] ?? null,
            'recorded_at'      => $row['recorded_at'],
        ];
    }
}

$totalFiltered = count($allRows);
$paginatedList = array_slice($allRows, $offset, $limit);

// Fetch list of agents for filter dropdown
$agentsList = [];
$agentsSql = "SELECT u.user_id, p.profile_display_name, sa.site_access_type
              FROM {$tblPrefix}user u
              INNER JOIN {$tblPrefix}site_access sa ON (u.user_id = sa.site_access_user_id AND sa.site_access_site_id = {$siteId})
              INNER JOIN {$tblPrefix}profile p ON u.user_id = p.profile_user_id
              WHERE sa.site_access_status = 1
              ORDER BY p.profile_display_name ASC";
$aRes = mysqli_query($connection, $agentsSql);
if ($aRes) {
    while ($aRow = mysqli_fetch_assoc($aRes)) {
        $agentsList[] = [
            'id'   => (int)$aRow['user_id'],
            'name' => $aRow['profile_display_name'],
            'role' => ucwords(str_replace('_', ' ', $aRow['site_access_type'] ?? '')),
        ];
    }
}

echo json_encode([
    'status' => 'success',
    'kpis'   => [
        'total_verified_amount' => $totalVerifiedAmount,
        'total_pending_amount'  => $totalPendingAmount,
        'today_verified_amount' => $todayVerifiedAmount,
        'total_count'           => $verifiedCount + $pendingCount,
        'pending_count'         => $pendingCount,
        'verified_count'        => $verifiedCount,
    ],
    'pagination' => [
        'page'       => $page,
        'limit'      => $limit,
        'total'      => $totalFiltered,
        'total_pages'=> ceil($totalFiltered / $limit),
    ],
    'transactions' => $paginatedList,
    'agents'       => $agentsList,
]);
exit();
