<?php

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
    if (file_exists($docRoot . '/lib/modules/siteStatus.php')) {
        require_once $docRoot . '/lib/modules/siteStatus.php';
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId    = defined('SITE_ID') && (int)SITE_ID > 0 ? (int) SITE_ID : (isset($_SESSION['site_id']) ? (int)$_SESSION['site_id'] : 0);
$userId    = defined('USER_ID')      ? (int) USER_ID : 0;
$accessType = isset($site_access_type) ? $site_access_type : 'administrator';

if (!$connection) {
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Database connection failed.'
    ]);
    exit();
}

// 1. Date Range Handling
$daterangeInput = trim($_GET['daterange'] ?? '');
$preset = trim($_GET['preset'] ?? '');

$from = date('Y-m-d 00:00:00', strtotime('-30 days'));
$to   = date('Y-m-d 23:59:59');
$displayRange = date('d M Y', strtotime('-30 days')) . ' - ' . date('d M Y');

if ($preset === 'today') {
    $from = date('Y-m-d 00:00:00');
    $to   = date('Y-m-d 23:59:59');
    $displayRange = 'Today (' . date('d M Y') . ')';
} elseif ($preset === 'this_month') {
    $from = date('Y-m-01 00:00:00');
    $to   = date('Y-m-d 23:59:59');
    $displayRange = date('01 M Y') . ' - ' . date('d M Y');
} elseif ($preset === 'last_month') {
    $from = date('Y-m-01 00:00:00', strtotime('first day of last month'));
    $to   = date('Y-m-t 23:59:59', strtotime('last day of last month'));
    $displayRange = date('d M Y', strtotime($from)) . ' - ' . date('d M Y', strtotime($to));
} elseif ($preset === 'all_time') {
    $from = '2000-01-01 00:00:00';
    $to   = date('Y-m-d 23:59:59');
    $displayRange = 'All Time';
} elseif (!empty($daterangeInput) && $daterangeInput !== 'null') {
    $dates = explode(' - ', $daterangeInput);
    if (count($dates) === 2) {
        $fromTime = strtotime(trim($dates[0]));
        $toTime   = strtotime(trim($dates[1]));
        if ($fromTime && $toTime) {
            $from = date('Y-m-d 00:00:00', $fromTime);
            $to   = date('Y-m-d 23:59:59', $toTime);
            $displayRange = date('d M Y', $fromTime) . ' - ' . date('d M Y', $toTime);
        }
    }
}

$dateRangeQuery = "enquiry_time BETWEEN '{$from}' AND '{$to}'";
$urlDateParam = date('Y-m-d', strtotime($from)) . ' - ' . date('Y-m-d', strtotime($to));

// 2. Fetch Active Site Employees
$empSql = "
    SELECT 
        u.user_id, 
        u.user_email,
        u.user_mobile,
        p.profile_display_name, 
        p.profile_first_name,
        p.profile_last_name,
        p.profile_mobile,
        p.profile_image,
        sa.site_access_type,
        sa.site_access_status
    FROM {$tblPrefix}user u
    INNER JOIN {$tblPrefix}site_access sa ON sa.site_access_user_id = u.user_id 
    LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id 
    WHERE sa.site_access_status = 1 AND sa.site_access_site_id = {$siteId}
    ORDER BY 
        CASE WHEN sa.site_access_type = 'administrator' THEN 0 
             WHEN sa.site_access_type = 'operations' THEN 1
             ELSE 2 END, 
        u.user_id ASC
";

$empRes = mysqli_query($connection, $empSql);
if (!$empRes) {
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Failed to fetch team members.'
    ]);
    exit();
}

$employeesRaw = [];
$empIds = [];
while ($row = mysqli_fetch_assoc($empRes)) {
    $uid = (int)$row['user_id'];
    $employeesRaw[$uid] = $row;
    $empIds[] = $uid;
}

// 3. Aggregate Lead Statuses per Employee
$leadsStatsMap = [];
if (!empty($empIds)) {
    $empIdsStr = implode(',', $empIds);
    $leadsSql = "
        SELECT 
            enquiry_agent,
            enquiry_status,
            COUNT(enquiry_id) AS lead_count
        FROM {$tblPrefix}enquiry
        WHERE enquiry_site_id = {$siteId} 
          AND enquiry_agent IN ({$empIdsStr})
          AND {$dateRangeQuery}
        GROUP BY enquiry_agent, enquiry_status
    ";

    $leadsRes = mysqli_query($connection, $leadsSql);
    if ($leadsRes) {
        while ($lRow = mysqli_fetch_assoc($leadsRes)) {
            $agId = (int)$lRow['enquiry_agent'];
            $st   = (int)$lRow['enquiry_status'];
            $cnt  = (int)$lRow['lead_count'];

            if (!isset($leadsStatsMap[$agId])) {
                $leadsStatsMap[$agId] = [];
            }
            $leadsStatsMap[$agId][$st] = ($leadsStatsMap[$agId][$st] ?? 0) + $cnt;
        }
    }
}

// 4. Build Formatted Employee Performance Records
$employeesList = [];
$totalTeamMembers = count($employeesRaw);
$globalTotalLeads = 0;
$globalConfirmed  = 0;
$globalFollowups  = 0;
$globalPending    = 0;
$globalLost       = 0;

$topPerformer = null;
$maxConfirmed = -1;

$searchFilter = strtolower(trim($_GET['search'] ?? ''));
$roleFilter   = strtolower(trim($_GET['role'] ?? ''));

foreach ($employeesRaw as $uid => $emp) {
    $firstName   = trim($emp['profile_first_name'] ?? '');
    $lastName    = trim($emp['profile_last_name'] ?? '');
    $displayName = trim($emp['profile_display_name'] ?? '');
    if (empty($displayName)) {
        $displayName = trim($firstName . ' ' . $lastName);
    }
    if (empty($displayName)) {
        $displayName = 'Employee #' . $uid;
    }

    $role = trim($emp['site_access_type'] ?? 'agent');
    $email = trim($emp['user_email'] ?? '');
    $mobile = trim($emp['profile_mobile'] ?? $emp['user_mobile'] ?? '');

    // Check search filter
    if (!empty($searchFilter)) {
        $haystack = strtolower($displayName . ' ' . $email . ' ' . $mobile . ' ' . $role);
        if (strpos($haystack, $searchFilter) === false) {
            continue;
        }
    }

    // Check role filter
    if (!empty($roleFilter)) {
        if (strtolower($role) !== $roleFilter) {
            continue;
        }
    }

    $stats = $leadsStatsMap[$uid] ?? [];

    // Success & Converted: 1 (Success), 8 (Upay done), 10 (Quotation approved), 11 (Voucher issued), 12 (Payment received), 13 (Trip in progress), 14 (Trip completed)
    $confirmedCount = (int)($stats[1] ?? 0) + (int)($stats[8] ?? 0) + (int)($stats[10] ?? 0) + (int)($stats[11] ?? 0) + (int)($stats[12] ?? 0) + (int)($stats[13] ?? 0) + (int)($stats[14] ?? 0);
    // Follow-up: 3
    $followupCount  = (int)($stats[3] ?? 0);
    // In-progress & Pipeline: 0 (Initiate), 2 (Pending assigned), 6 (Payment initiated), 7 (Sales request), 9 (Itinerary sent)
    $pendingCount   = (int)($stats[0] ?? 0) + (int)($stats[2] ?? 0) + (int)($stats[6] ?? 0) + (int)($stats[7] ?? 0) + (int)($stats[9] ?? 0);
    // Drop-off / Lost: 4 (Failed), 5 (Duplicate), 15 (Closed), 16 (Cancelled), 17 (Lost), 18 (Payment refund)
    $lostCount      = (int)($stats[4] ?? 0) + (int)($stats[5] ?? 0) + (int)($stats[15] ?? 0) + (int)($stats[16] ?? 0) + (int)($stats[17] ?? 0) + (int)($stats[18] ?? 0);
    $totalEmpLeads  = array_sum($stats);

    $conversionRate = $totalEmpLeads > 0 ? round(($confirmedCount / $totalEmpLeads) * 100, 1) : 0.0;
    $pipelineCount  = $followupCount + $pendingCount;

    // Track Global Metrics
    $globalTotalLeads += $totalEmpLeads;
    $globalConfirmed  += $confirmedCount;
    $globalFollowups  += $followupCount;
    $globalPending    += $pendingCount;
    $globalLost       += $lostCount;

    // Determine performance tier
    $performanceTier = 'Starting';
    $performanceClass = 'tier-neutral';
    if ($conversionRate >= 15 || $confirmedCount >= 10) {
        $performanceTier = 'Star Performer';
        $performanceClass = 'tier-star';
    } elseif ($conversionRate >= 8 || $confirmedCount >= 5) {
        $performanceTier = 'High Achiever';
        $performanceClass = 'tier-high';
    } elseif ($pipelineCount > 10) {
        $performanceTier = 'Active Pipeline';
        $performanceClass = 'tier-active';
    } elseif ($totalEmpLeads > 0) {
        $performanceTier = 'In Progress';
        $performanceClass = 'tier-progress';
    }

    // Top performer check
    if ($confirmedCount > $maxConfirmed && $totalEmpLeads > 0) {
        $maxConfirmed = $confirmedCount;
        $topPerformer = [
            'id'              => $uid,
            'name'            => $displayName,
            'role'            => ucwords($role),
            'confirmed_leads' => $confirmedCount,
            'conversion_rate' => $conversionRate,
            'total_leads'     => $totalEmpLeads
        ];
    }

    $employeesList[] = [
        'id'                => $uid,
        'encoded_id'        => base64_encode((string)$uid),
        'name'              => $displayName,
        'role'              => $role,
        'role_label'        => ucwords($role),
        'email'             => $email,
        'mobile'            => $mobile,
        'image'             => trim($emp['profile_image'] ?? ''),
        'total_leads'       => $totalEmpLeads,
        'confirmed_leads'   => $confirmedCount,
        'followup_leads'    => $followupCount,
        'pending_leads'     => $pendingCount,
        'lost_leads'        => $lostCount,
        'pipeline_leads'    => $pipelineCount,
        'conversion_rate'   => $conversionRate,
        'performance_tier'  => $performanceTier,
        'performance_class' => $performanceClass,
        'links'             => [
            'all'       => "/dashboard/leads/list/?agentId={$uid}&daterange=" . urlencode($urlDateParam),
            'confirmed' => "/dashboard/leads/list/?agentId={$uid}&leadStatus=1&daterange=" . urlencode($urlDateParam),
            'followup'  => "/dashboard/leads/list/?agentId={$uid}&leadStatus=3&daterange=" . urlencode($urlDateParam),
            'pending'   => "/dashboard/leads/list/?agentId={$uid}&leadStatus=2&daterange=" . urlencode($urlDateParam),
            'lost'      => "/dashboard/leads/list/?agentId={$uid}&leadStatus=17&daterange=" . urlencode($urlDateParam)
        ]
    ];
}

// 5. Sort options
$sortBy = trim($_GET['sort'] ?? 'confirmed');
usort($employeesList, function ($a, $b) use ($sortBy) {
    if ($sortBy === 'leads') {
        return $b['total_leads'] <=> $a['total_leads'];
    } elseif ($sortBy === 'conversion') {
        return $b['conversion_rate'] <=> $a['conversion_rate'];
    } elseif ($sortBy === 'pipeline') {
        return $b['pipeline_leads'] <=> $a['pipeline_leads'];
    } elseif ($sortBy === 'name') {
        return strcasecmp($a['name'], $b['name']);
    } else {
        // Default: confirmed leads desc, then total leads desc
        if ($b['confirmed_leads'] === $a['confirmed_leads']) {
            return $b['total_leads'] <=> $a['total_leads'];
        }
        return $b['confirmed_leads'] <=> $a['confirmed_leads'];
    }
});

$overallConversionRate = $globalTotalLeads > 0 ? round(($globalConfirmed / $globalTotalLeads) * 100, 1) : 0.0;
$globalPipeline = $globalFollowups + $globalPending;

echo json_encode([
    'status' => 'success',
    'date_range' => [
        'from'          => date('Y-m-d', strtotime($from)),
        'to'            => date('Y-m-d', strtotime($to)),
        'display'       => $displayRange,
        'url_param'     => $urlDateParam,
        'preset'        => $preset ?: 'custom'
    ],
    'metrics' => [
        'total_employees'       => $totalTeamMembers,
        'filtered_employees'    => count($employeesList),
        'total_leads'           => $globalTotalLeads,
        'total_confirmed'       => $globalConfirmed,
        'total_followups'       => $globalFollowups,
        'total_pending'         => $globalPending,
        'total_lost'            => $globalLost,
        'total_pipeline'        => $globalPipeline,
        'overall_conversion_rate' => $overallConversionRate,
        'top_performer'         => $topPerformer
    ],
    'employees' => $employeesList
]);
exit();
