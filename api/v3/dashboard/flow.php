<?php

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

$tblPrefix  = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId     = defined('SITE_ID')      ? (int) SITE_ID : 0;
$userId     = defined('USER_ID')      ? (int) USER_ID : 0;
$accessType = isset($site_access_type) ? $site_access_type : 'administrator';

$siteFilter = ($siteId > 0) ? "enquiry_site_id = {$siteId}" : "1=1";
$userFilter = ($accessType !== 'administrator' && $userId > 0)
    ? " AND (enquiry_agent = '{$userId}')"
    : "";

$t1 = date('Y-m-d 00:00:00', strtotime('-30 days'));
$t2 = date('Y-m-d 23:59:59');

if (isset($_GET['daterange']) && !empty($_GET['daterange']) && $_GET['daterange'] !== 'null') {
    $dates = explode(' - ', trim($_GET['daterange']));
    if (count($dates) == 2) {
        $parsedT1 = strtotime($dates[0]);
        $parsedT2 = strtotime($dates[1]);
        if ($parsedT1 && $parsedT2) {
            if ($parsedT1 > $parsedT2) {
                $tmp = $parsedT1;
                $parsedT1 = $parsedT2;
                $parsedT2 = $tmp;
            }
            $t1 = date('Y-m-d 00:00:00', $parsedT1);
            $t2 = date('Y-m-d 23:59:59', $parsedT2);
        }
    }
}

$startTs = strtotime($t1);
$endTs   = strtotime($t2);
$diffDays = (int) ceil(abs($endTs - $startTs) / 86400);

$timeSlots = [];

if ($diffDays <= 2) {
    // Hourly granularity (e.g. today / 2-day period)
    $sqlFmt = "DATE_FORMAT(enquiry_time, '%Y-%m-%d %H:00')";
    $curr = strtotime(date('Y-m-d H:00:00', $startTs));
    $endHour = strtotime(date('Y-m-d H:00:00', $endTs));
    while ($curr <= $endHour) {
        $slotKey = date('Y-m-d H:00', $curr);
        $label   = ($diffDays > 1) ? date('d M H:00', $curr) : date('H:00', $curr);
        $timeSlots[$slotKey] = ['label' => $label, 'count' => 0, 'confirmed' => 0];
        $curr += 3600;
    }
} elseif ($diffDays <= 90) {
    // Daily granularity (e.g. 7 days, 30 days, 3 months)
    $sqlFmt = "DATE_FORMAT(enquiry_time, '%Y-%m-%d')";
    $curr = strtotime(date('Y-m-d 00:00:00', $startTs));
    $endDay = strtotime(date('Y-m-d 00:00:00', $endTs));
    while ($curr <= $endDay) {
        $slotKey = date('Y-m-d', $curr);
        $label   = date('d M', $curr);
        $timeSlots[$slotKey] = ['label' => $label, 'count' => 0, 'confirmed' => 0];
        $curr = strtotime('+1 day', $curr);
    }
} else {
    // Monthly granularity (e.g. > 90 days / multi-month view)
    $sqlFmt = "DATE_FORMAT(enquiry_time, '%Y-%m')";
    $curr = strtotime(date('Y-m-01 00:00:00', $startTs));
    $endMonth = strtotime(date('Y-m-01 00:00:00', $endTs));
    while ($curr <= $endMonth) {
        $slotKey = date('Y-m', $curr);
        $label   = date('M Y', $curr);
        $timeSlots[$slotKey] = ['label' => $label, 'count' => 0, 'confirmed' => 0];
        $curr = strtotime('+1 month', $curr);
    }
}

$daterangeFilter = " AND enquiry_time BETWEEN '{$t1}' AND '{$t2}'";

$sqlFlow = "SELECT {$sqlFmt} AS slot_key, 
                   COUNT(*) AS count,
                   SUM(CASE WHEN enquiry_status = 1 THEN 1 ELSE 0 END) AS confirmed_count
            FROM {$tblPrefix}enquiry
            WHERE {$siteFilter}{$userFilter}{$daterangeFilter}
            GROUP BY slot_key
            ORDER BY MIN(enquiry_time) ASC";

if (isset($connection) && $connection && ($resFlow = mysqli_query($connection, $sqlFlow))) {
    while ($row = mysqli_fetch_assoc($resFlow)) {
        $key = $row['slot_key'];
        if (isset($timeSlots[$key])) {
            $timeSlots[$key]['count']     = (int) $row['count'];
            $timeSlots[$key]['confirmed'] = (int) $row['confirmed_count'];
        }
    }
}

$flowLabels    = [];
$flowData      = [];
$confirmedData = [];

foreach ($timeSlots as $slot) {
    $flowLabels[]    = $slot['label'];
    $flowData[]      = $slot['count'];
    $confirmedData[] = $slot['confirmed'];
}

$totalEnquiries = array_sum($flowData);
$totalConfirmed = array_sum($confirmedData);
$conversionRate = ($totalEnquiries > 0) ? round(($totalConfirmed / $totalEnquiries) * 100, 1) : 0;

echo json_encode([
    'status'          => 200,
    'labels'          => $flowLabels,
    'data'            => $flowData,
    'enquiry_data'    => $flowData,
    'confirmed_data'  => $confirmedData,
    'total'           => $totalEnquiries,
    'total_enquiries' => $totalEnquiries,
    'total_confirmed' => $totalConfirmed,
    'conversion_rate' => $conversionRate,
]);
exit();
