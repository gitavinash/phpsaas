<?php

// require_once LOGIN_CHECK;

header('Content-Type: application/json; charset=utf-8');

// ------------------------------
// INPUT DATE RANGE
// ------------------------------
$daterange = $_GET['daterange'] ?? null;

if (!empty($daterange) && $daterange !== 'null') {
    $dates = explode(' - ', $daterange);

    $startDateRaw = trim($dates[0] ?? '');
    $endDateRaw   = trim($dates[1] ?? $startDateRaw);

    $isSameDay = (
        date('Y-m-d', strtotime($startDateRaw)) ===
        date('Y-m-d', strtotime($endDateRaw))
    );

    $startDate = date('Y-m-d 00:00:00', strtotime($startDateRaw));
    $endDate   = date('Y-m-d 23:59:59', strtotime($endDateRaw));
} else {
    // Default: last 1 month
    $isSameDay = false;
    $startDate = date('Y-m-d 00:00:00', strtotime('-1 month'));
    $endDate   = date('Y-m-d 23:59:59');
}

// ------------------------------
// USER ID FILTER
// ------------------------------
$userFilter = '';
if (!empty($userId)) {
    $decodedUser = base64_decode($userId, true);
    $decodedUser = $decodedUser !== false ? (int)$decodedUser : 0;

    if ($decodedUser > 0) {
        $userFilter = ' AND user_id = ' . $decodedUser . ' ';
    }
}

// ======================================================
//  SAME DAY → HOURLY VISIT COUNT
// ======================================================
if ($isSameDay) {
    $amountQuery = "
        SELECT 
            HOUR(visit_time) AS hour,
            COUNT(*) AS totalVisits
        FROM 
            " . TABLE_PREFIX . "membership_visits
        WHERE 
            visit_status = 1
            AND visit_time BETWEEN '{$startDate}' AND '{$endDate}'
            {$userFilter}
            AND visit_site_id = " . SITE_ID . "
        GROUP BY 
            HOUR(visit_time)
        ORDER BY 
            hour ASC
    ";

    $rawData = [];

    if ($query = mysqli_query($connection, $amountQuery)) {
        while ($row = mysqli_fetch_assoc($query)) {
            $hourKey = str_pad((int)$row['hour'], 2, '0', STR_PAD_LEFT) . ':00';
            $rawData[$hourKey] = (int)$row['totalVisits'];
        }
    }

    // Normalized result: 00:00 → 23:00 (12-hr format keys)
    $data = [];
    for ($h = 0; $h <= 23; $h++) {
        $oldKey = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00'; // "08:00"
        $newKey = date('h:00 A', strtotime($oldKey));        // "08:00 AM"

        $data[$newKey] = $rawData[$oldKey] ?? 0;
    }

    $total = array_sum($data);

    echo json_encode(
        [
            'total' => $total,
            'data'  => $data,
        ],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit;
}

// ======================================================
//  DEFAULT RANGE → DAY-WISE VISIT COUNT
// ======================================================
$amountQuery = "
    SELECT 
        DATE(visit_time) AS day,
        COUNT(*) AS totalVisits
    FROM 
        " . TABLE_PREFIX . "membership_visits
    WHERE 
        visit_status = 1
        AND visit_time BETWEEN '{$startDate}' AND '{$endDate}'
        {$userFilter}
        AND visit_site_id = " . SITE_ID . "
    GROUP BY 
        DATE(visit_time)
    ORDER BY 
        day ASC
";

$rawData = [];

if ($query = mysqli_query($connection, $amountQuery)) {
    while ($row = mysqli_fetch_assoc($query)) {
        $formattedDay = date('d-m-Y', strtotime($row['day']));
        $rawData[$formattedDay] = (int)$row['totalVisits'];
    }
}

// Fill missing dates
$data = [];
$cur = strtotime(substr($startDate, 0, 10));
$end = strtotime(substr($endDate, 0, 10));

while ($cur <= $end) {
    $dayKey = date('d-m-Y', $cur);
    $data[$dayKey] = $rawData[$dayKey] ?? 0;
    $cur = strtotime('+1 day', $cur);
}

$total = array_sum($data);

echo json_encode(
    [
        'total' => $total,
        'data'  => $data,
    ],
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
);