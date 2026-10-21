<?php

header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}
require_once LOGIN_CHECK;

$tblPrefix  = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId     = defined('SITE_ID')      ? (int) SITE_ID : 0;
$userId     = defined('USER_ID')      ? (int) USER_ID : 0;
$accessType = isset($site_access_type) ? $site_access_type : 'administrator';
$isCallingAgent = in_array(strtolower($accessType), ['agent', 'calling_agent', 'sales_executive'], true);

if ($accessType === 'digital_marketing') {
    http_response_code(403);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Access denied. Leads page is restricted for digital marketing.'
    ]);
    exit;
}

$userRole = strtolower(isset($site_access_type) ? $site_access_type : ($accessType ?? ''));
$isPrivilegedUser = in_array($userRole, ['administrator', 'admin', 'superadmin', 'operations', 'owner'], true);
$isCallingAgent = in_array($userRole, ['agent', 'calling_agent', 'sales_executive'], true);

$siteFilter = ($siteId > 0) ? "e.enquiry_site_id = {$siteId}" : "1=1";
$siteQuotationFilter = ($siteId > 0) ? " AND quotation_site_id = {$siteId}" : "";
$userFilter = (!$isPrivilegedUser && $userId > 0)
    ? " AND (e.enquiry_agent = '{$userId}')"
    : "";

// Specific agent filter from request
$reqAgent = $_GET['agentId'] ?? ($_GET['agent_id'] ?? ($_GET['agent'] ?? ''));
if ($reqAgent !== '' && $reqAgent !== 'all') {
    if ($reqAgent === '0' || $reqAgent === 'unassigned') {
        $userFilter .= " AND (e.enquiry_agent = '0' OR e.enquiry_agent = '' OR e.enquiry_agent IS NULL)";
    } elseif (is_numeric($reqAgent) && (int)$reqAgent > 0) {
        $filterAgentId = (int)$reqAgent;
        $userFilter .= " AND e.enquiry_agent = '{$filterAgentId}'";
    }
}

$daterangeFilter = "";
$hasExplicitDate = false;
$t1 = "";
$t2 = "";
if (isset($_GET['daterange']) && !empty($_GET['daterange']) && $_GET['daterange'] !== 'null') {
    $dr = trim($_GET['daterange']);
    if (($ampPos = strpos($dr, '&')) !== false) {
        $dr = substr($dr, 0, $ampPos);
    }
    if ($dr === 'all') {
        $daterangeFilter = "";
        $hasExplicitDate = true;
    } else {
        $dates = explode(' - ', $dr);
        if (count($dates) == 2) {
            $parsedT1 = strtotime(trim($dates[0]));
            $parsedT2 = strtotime(trim($dates[1]));
            if ($parsedT1 && $parsedT2) {
                $t1 = date('Y-m-d 00:00:00', $parsedT1);
                $t2 = date('Y-m-d 23:59:59', $parsedT2);
                $daterangeFilter = " AND e.enquiry_time BETWEEN '{$t1}' AND '{$t2}'";
                $hasExplicitDate = true;
            }
        }
    }
}

// By default show last 30 days of data if no explicit date range is specified and not performing an open search
if (!$hasExplicitDate && (!isset($_GET['search']) || trim($_GET['search']) === '')) {
    $t1 = date('Y-m-d 00:00:00', strtotime('-29 days'));
    $t2 = date('Y-m-d 23:59:59');
    $daterangeFilter = " AND e.enquiry_time BETWEEN '{$t1}' AND '{$t2}'";
}

$searchFilter = "";
if (isset($_GET['search']) && trim($_GET['search']) !== '') {
    $searchKey = mysqli_real_escape_string($connection, trim($_GET['search']));
    if ($accessType === 'digital_marketing') {
        $searchFilter = " AND (e.enquiry_first_name LIKE '%{$searchKey}%' 
                           OR e.enquiry_last_name LIKE '%{$searchKey}%'
                           OR e.enquiry_subject LIKE '%{$searchKey}%' 
                           OR e.enquiry_source LIKE '%{$searchKey}%'
                           OR e.enquiry_id = '{$searchKey}')";
    } else {
        $searchFilter = " AND (e.enquiry_first_name LIKE '%{$searchKey}%' 
                           OR e.enquiry_last_name LIKE '%{$searchKey}%'
                           OR e.enquiry_mobile LIKE '%{$searchKey}%' 
                           OR e.enquiry_email LIKE '%{$searchKey}%' 
                           OR e.enquiry_subject LIKE '%{$searchKey}%' 
                           OR e.enquiry_source LIKE '%{$searchKey}%'
                           OR e.enquiry_id = '{$searchKey}')";
    }
    // When specific search is provided without explicit daterange, search across all time
    if (!$hasExplicitDate) {
        $daterangeFilter = "";
    }
}

// Source Filter
$sourceFilter = "";
if (isset($_GET['source']) && trim($_GET['source']) !== '' && $_GET['source'] !== 'all') {
    $sourceKey = mysqli_real_escape_string($connection, trim($_GET['source']));
    $sourceFilter = " AND e.enquiry_source LIKE '%{$sourceKey}%'";
}

function cleanLeadSource($src)
{
    if (empty($src) || trim($src) === '') return 'Direct';
    $c = trim($src);
    $c = preg_replace('#^https?://#i', '', $c);
    $c = preg_replace('#^www\.#i', '', $c);
    $c = rtrim($c, '/');
    return ucfirst($c);
}

require_once __DIR__ . '/status-config.php';
require_once dirname(__DIR__) . '/whatsapp/device_helper.php';

$attachWaActions = function(&$items) use ($connection, $siteId, $userId, $accessType, $tblPrefix) {
    if (empty($items)) return;
    $leadIds = array_filter(array_column($items, 'id'));
    $leadPhones = [];
    foreach ($items as $it) {
        $cleanP = preg_replace('/[^0-9]/', '', (string)($it['mobile'] ?? ''));
        if (!empty($cleanP)) {
            $leadPhones[] = (strlen($cleanP) === 10) ? ('91' . $cleanP) : $cleanP;
        }
    }
    $leadPhones = array_unique(array_filter($leadPhones));
    $chatMap = [];
    $conds = [];
    if (!empty($leadIds)) {
        $conds[] = "chat_lead_id IN (" . implode(',', array_map('intval', $leadIds)) . ")";
    }
    if (!empty($leadPhones)) {
        $escPhones = array_map(function($p) use ($connection) {
            return "'" . mysqli_real_escape_string($connection, $p) . "'";
        }, $leadPhones);
        $conds[] = "chat_phone IN (" . implode(',', $escPhones) . ")";
        $plusPhones = array_map(function($p) use ($connection) {
            return "'+' . '" . mysqli_real_escape_string($connection, $p) . "'";
        }, $leadPhones);
        $conds[] = "chat_phone IN (" . implode(',', $plusPhones) . ")";
    }
    if (!empty($conds)) {
        $cQ = @mysqli_query($connection, "SELECT chat_id, chat_phone, chat_lead_id FROM {$tblPrefix}whatsapp_chat WHERE (chat_site_id = {$siteId} OR chat_site_id = 0) AND (" . implode(' OR ', $conds) . ")");
        if ($cQ) {
            while ($cRow = mysqli_fetch_assoc($cQ)) {
                $cId = (int)$cRow['chat_id'];
                if (!empty($cRow['chat_lead_id'])) {
                    $chatMap['lead_' . (int)$cRow['chat_lead_id']] = $cId;
                }
                $cleanP = preg_replace('/[^0-9]/', '', (string)$cRow['chat_phone']);
                if (!empty($cleanP)) {
                    $chatMap['phone_' . $cleanP] = $cId;
                    if (strlen($cleanP) === 10) {
                        $chatMap['phone_91' . $cleanP] = $cId;
                    } elseif (strlen($cleanP) === 12 && str_starts_with($cleanP, '91')) {
                        $chatMap['phone_' . substr($cleanP, 2)] = $cId;
                    }
                }
            }
        }
    }

    foreach ($items as &$ld) {
        $existingChatId = 0;
        $mClean = preg_replace('/[^0-9]/', '', (string)($ld['mobile'] ?? ''));
        $waP = (strlen($mClean) === 10) ? ('91' . $mClean) : $mClean;
        if (!empty($ld['id']) && isset($chatMap['lead_' . (int)$ld['id']])) {
            $existingChatId = $chatMap['lead_' . (int)$ld['id']];
        } elseif (!empty($waP) && isset($chatMap['phone_' . $waP])) {
            $existingChatId = $chatMap['phone_' . $waP];
        }
        $ld['whatsapp_action'] = resolveWhatsAppLeadAction(
            $connection,
            $siteId,
            $userId,
            $accessType,
            $waP,
            $ld['first_name'] ?: $ld['name'],
            $ld['id'],
            $ld['subject'] ?? '',
            $existingChatId
        );
    }
    unset($ld);
};

// Case 1: Fetching single column status (e.g. Load More pagination)
if (isset($_GET['status']) && $_GET['status'] !== '') {
    $targetStatus = intval($_GET['status']);
    $offset = isset($_GET['offset']) ? max(0, intval($_GET['offset'])) : 0;
    $limit  = isset($_GET['limit'])  ? min(max(1, intval($_GET['limit'])), 50) : 10;

    // Allowed active kanban columns: 0 (New Leads), 2 (Assigned), 3 (Follow Up), 9 (Itinerary Sent), 6 (Payment Requested), 1 (Confirmed)
    // Calling agents do not have access to Column 0 (New Leads)
    $allowedStatuses = $isCallingAgent ? [1, 2, 3, 6, 9] : [0, 1, 2, 3, 6, 9];
    if (!in_array($targetStatus, $allowedStatuses, true)) {
        echo json_encode([
            'status' => 200,
            'data'   => [],
            'offset' => $offset,
            'limit'  => $limit,
            'total'  => 0,
        ]);
        exit();
    }

    if ($targetStatus === 9) {
        $statusClause = "(e.enquiry_status = 9 OR (e.enquiry_status NOT IN (1, 6, 4, 5, 7, 15, 16, 17, 18) AND q.quotation_id IS NOT NULL))";
    } elseif (in_array($targetStatus, [0, 2, 3], true)) {
        $statusClause = "e.enquiry_status = {$targetStatus} AND q.quotation_id IS NULL";
    } else {
        $statusClause = "e.enquiry_status = {$targetStatus}";
    }

    $sql = "SELECT e.enquiry_id, e.enquiry_first_name, e.enquiry_last_name, e.enquiry_mobile, e.enquiry_email,
                   e.enquiry_subject, e.enquiry_time, e.enquiry_status, e.enquiry_source
            FROM {$tblPrefix}enquiry e
            LEFT JOIN (
                SELECT quotation_ref_id, MIN(quotation_id) AS quotation_id
                FROM {$tblPrefix}quotation
                WHERE quotation_status = 0{$siteQuotationFilter}
                GROUP BY quotation_ref_id
            ) q ON e.enquiry_id = q.quotation_ref_id
            WHERE {$siteFilter}{$userFilter}{$daterangeFilter}{$searchFilter}{$sourceFilter}
              AND {$statusClause}
              AND e.enquiry_status NOT IN (4, 5, 7, 15, 16, 17, 18)
            ORDER BY e.enquiry_time DESC
            LIMIT {$offset}, {$limit}";

    $leads = [];
    if (isset($connection) && $connection && ($res = mysqli_query($connection, $sql))) {
        while ($row = mysqli_fetch_assoc($res)) {
            $effectiveStatus = $targetStatus;
            $meta = getLeadStatusMeta($effectiveStatus);
            $fullName = trim(($row['enquiry_first_name'] ?? '') . ' ' . ($row['enquiry_last_name'] ?? ''));
            if (empty($fullName)) {
                $fullName = 'Lead #' . $row['enquiry_id'];
            }
            $isDigMkt = ($accessType === 'digital_marketing');
            $leads[] = [
                'id'           => (int)$row['enquiry_id'],
                'encoded_id'   => base64_encode($row['enquiry_id']),
                'name'         => $fullName,
                'first_name'   => $row['enquiry_first_name'] ?? '',
                'last_name'    => $row['enquiry_last_name'] ?? '',
                'mobile'       => ($isDigMkt || empty($row['enquiry_mobile'])) ? '' : $row['enquiry_mobile'],
                'email'        => ($isDigMkt || empty($row['enquiry_email']))  ? '' : $row['enquiry_email'],
                'subject'      => !empty($row['enquiry_subject']) ? $row['enquiry_subject'] : 'General Enquiry',
                'source'       => cleanLeadSource($row['enquiry_source']),
                'time'         => date('d M, h:i A', strtotime($row['enquiry_time'])),
                'status'       => $effectiveStatus,
                'status_label' => $meta['label'],
                'status_class' => $meta['class'],
                'status_color' => $meta['color'],
            ];
        }
    }

    $attachWaActions($leads);

    echo json_encode([
        'status' => 200,
        'data'   => $leads,
        'offset' => $offset,
        'limit'  => $limit,
        'total'  => count($leads),
    ]);
    exit();
}

// Case 2: Full Board Load (for calling agents, status 0 'New Leads' is excluded)
$allowedStatuses = $isCallingAgent ? [2, 3, 9, 6, 1] : [0, 2, 3, 9, 6, 1];
$grouped = [];
foreach ($allowedStatuses as $st) {
    $grouped[(string)$st] = [];
}
$flatLeads = [];

$limitClause = !empty($searchFilter) ? "LIMIT 200" : "LIMIT 350";

$sql = "SELECT e.enquiry_id, e.enquiry_first_name, e.enquiry_last_name, e.enquiry_mobile, e.enquiry_email,
               e.enquiry_subject, e.enquiry_time, e.enquiry_status, e.enquiry_source,
               CASE
                   WHEN e.enquiry_status = 1 THEN 1
                   WHEN e.enquiry_status = 6 THEN 6
                   WHEN (e.enquiry_status = 9 OR q.quotation_id IS NOT NULL) THEN 9
                   WHEN e.enquiry_status = 3 THEN 3
                   WHEN e.enquiry_status = 2 THEN 2
                   WHEN e.enquiry_status = 0 THEN 0
                   ELSE e.enquiry_status
               END AS effective_status
        FROM {$tblPrefix}enquiry e
        LEFT JOIN (
            SELECT quotation_ref_id, MIN(quotation_id) AS quotation_id
            FROM {$tblPrefix}quotation
            WHERE quotation_status = 0{$siteQuotationFilter}
            GROUP BY quotation_ref_id
        ) q ON e.enquiry_id = q.quotation_ref_id
        WHERE {$siteFilter}{$userFilter}{$daterangeFilter}{$searchFilter}{$sourceFilter}
          AND e.enquiry_status NOT IN (4, 5, 7, 15, 16, 17, 18)
        HAVING effective_status IN (" . implode(', ', $allowedStatuses) . ")
        ORDER BY e.enquiry_time DESC
        {$limitClause}";

if (isset($connection) && $connection && ($res = mysqli_query($connection, $sql))) {
    while ($row = mysqli_fetch_assoc($res)) {
        $stInt = (int)$row['effective_status'];
        if (!in_array($stInt, $allowedStatuses, true)) {
            continue;
        }
        $meta = getLeadStatusMeta($stInt);
        $groupKey = (string)$stInt;
        $fullName = trim(($row['enquiry_first_name'] ?? '') . ' ' . ($row['enquiry_last_name'] ?? ''));
        if (empty($fullName)) {
            $fullName = 'Lead #' . $row['enquiry_id'];
        }
        $isDigMkt = ($accessType === 'digital_marketing');
        $item = [
            'id'         => (int)$row['enquiry_id'],
            'encoded_id' => base64_encode($row['enquiry_id']),
            'name'       => $fullName,
            'first_name' => $row['enquiry_first_name'] ?? '',
            'last_name'  => $row['enquiry_last_name'] ?? '',
            'mobile'     => ($isDigMkt || empty($row['enquiry_mobile'])) ? '' : $row['enquiry_mobile'],
            'email'      => ($isDigMkt || empty($row['enquiry_email']))  ? '' : $row['enquiry_email'],
            'subject'    => !empty($row['enquiry_subject']) ? $row['enquiry_subject'] : 'General Enquiry',
            'source'     => cleanLeadSource($row['enquiry_source']),
            'time'       => date('d M, h:i A', strtotime($row['enquiry_time'])),
            'status'       => $stInt,
            'status_label' => $meta['label'],
            'status_class' => $meta['class'],
            'status_color' => $meta['color'],
        ];
        $flatLeads[] = $item;
        if (isset($grouped[$groupKey])) {
            $grouped[$groupKey][] = $item;
        }
    }
}

$statusCounts = [];
foreach ($allowedStatuses as $st) {
    $statusCounts[(string)$st] = 0;
}
$countSql = "SELECT 
                 CASE 
                     WHEN e.enquiry_status = 1 THEN 1
                     WHEN e.enquiry_status = 6 THEN 6
                     WHEN (e.enquiry_status = 9 OR q.quotation_id IS NOT NULL) THEN 9
                     WHEN e.enquiry_status = 3 THEN 3
                     WHEN e.enquiry_status = 2 THEN 2
                     WHEN e.enquiry_status = 0 THEN 0
                     ELSE e.enquiry_status
                 END AS effective_status,
                 COUNT(DISTINCT e.enquiry_id) as cnt
             FROM {$tblPrefix}enquiry e
             LEFT JOIN (
                 SELECT quotation_ref_id, MIN(quotation_id) AS quotation_id
                 FROM {$tblPrefix}quotation
                 WHERE quotation_status = 0{$siteQuotationFilter}
                 GROUP BY quotation_ref_id
             ) q ON e.enquiry_id = q.quotation_ref_id
             WHERE {$siteFilter}{$userFilter}{$daterangeFilter}{$searchFilter}{$sourceFilter}
               AND e.enquiry_status NOT IN (4, 5, 7, 15, 16, 17, 18)
             GROUP BY effective_status";

if (isset($connection) && $connection && ($cRes = mysqli_query($connection, $countSql))) {
    while ($cRow = mysqli_fetch_assoc($cRes)) {
        $stVal = (int)$cRow['effective_status'];
        $cnt   = (int)$cRow['cnt'];
        $stKey = (string)$stVal;
        if (isset($statusCounts[$stKey])) {
            $statusCounts[$stKey] = $cnt;
        }
    }
}

$attachWaActions($flatLeads);
$grouped = [];
foreach ($allowedStatuses as $st) {
    $grouped[(string)$st] = [];
}
foreach ($flatLeads as $fItem) {
    $gKey = (string)$fItem['status'];
    if (isset($grouped[$gKey])) {
        $grouped[$gKey][] = $fItem;
    }
}

echo json_encode([
    'status'  => 200,
    'data'    => $grouped,
    'grouped' => $grouped,
    'counts'  => $statusCounts,
    'leads'   => $flatLeads,
    'total'   => count($flatLeads),
]);
exit();
