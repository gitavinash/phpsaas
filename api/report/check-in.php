<?php

// require_once LOGIN_CHECK;

// ------------------------------
// INPUT DATE RANGE
// ------------------------------
$daterange = $_GET['daterange'] ?? null;

if (!empty($daterange) && $daterange !== 'null') {
    $dates = explode(" - ", $daterange);

    $startDateRaw = trim($dates[0]);
    $endDateRaw   = trim($dates[1]);

    $isSameDay = (date("Y-m-d", strtotime($startDateRaw)) === date("Y-m-d", strtotime($endDateRaw)));

    $startDate = date("Y-m-d 00:00:00", strtotime($startDateRaw));
    $endDate   = date("Y-m-d 23:59:59", strtotime($endDateRaw));
} else {
    // Default: last 1 month
    $isSameDay = false;
    $startDate = date("Y-m-d 00:00:00", strtotime("-1 month"));
    $endDate   = date("Y-m-d 23:59:59");
}


// ------------------------------
// USER ID FILTER
// ------------------------------
$userFilter = "";
if (!empty($userId)) {
    $decodedUser = intval(base64_decode($userId));
    $userFilter = " AND user_id = {$decodedUser} ";
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

    $data = [];

    if ($query = mysqli_query($connection, $amountQuery)) {
        while ($row = mysqli_fetch_assoc($query)) {
            $hourKey = str_pad($row['hour'], 2, "0", STR_PAD_LEFT) . ":00";
            $data[$hourKey] = (int)$row['totalVisits'];
        }
    }

    // Fill hours 08 → 22
    $result = [];
    for ($h = 8; $h <= 22; $h++) {
        $oldKey = str_pad($h, 2, "0", STR_PAD_LEFT) . ":00";
        $newKey = date("h:00 A", strtotime($oldKey));

        $result[$newKey] = $data[$oldKey] ?? 0;
    }

    echo json_encode($result, true);
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

$data = [];

if ($query = mysqli_query($connection, $amountQuery)) {
    while ($row = mysqli_fetch_assoc($query)) {
        $formattedDay = date("d-m-Y", strtotime($row['day']));
        $data[$formattedDay] = (int)$row['totalVisits'];
    }
}


// Fill missing dates
$result = [];
$cur = strtotime(substr($startDate, 0, 10));
$end = strtotime(substr($endDate, 0, 10));

while ($cur <= $end) {
    $dayKey = date("d-m-Y", $cur);
    $result[$dayKey] = $data[$dayKey] ?? 0;
    $cur = strtotime("+1 day", $cur);
}

echo json_encode($result, true);