<?php

// require_once LOGIN_CHECK;

header('Content-Type: application/json; charset=utf-8');

// ------------------------------
// INPUT DATE RANGE
// ------------------------------
$daterange = $_GET['daterange'] ?? 'null';

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
//  SAME DAY → HOURLY SUM(transaction_amount)
// ======================================================
if ($isSameDay) {

    $amountQuery = "
        SELECT 
            HOUR(transaction_time) AS hour,
            SUM(transaction_amount) AS totalTransactionValue
        FROM 
            " . TABLE_PREFIX . "transaction
        WHERE 
            transaction_status = 1
            AND transaction_time BETWEEN '{$startDate}' AND '{$endDate}'
            {$userFilter}
            AND transaction_site_id = " . SITE_ID . "
        GROUP BY 
            HOUR(transaction_time)
        ORDER BY 
            hour ASC
    ";

    $rawData = [];

    if ($res = mysqli_query($connection, $amountQuery)) {
        while ($row = mysqli_fetch_assoc($res)) {
            $hourKey = str_pad((int)$row['hour'], 2, '0', STR_PAD_LEFT) . ':00';
            $rawData[$hourKey] = (float)$row['totalTransactionValue'];
        }
    }

    // Fill hours 00 → 23 (12-hr format keys)
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
//  DEFAULT RANGE → DAY-WISE SUM(transaction_amount)
// ======================================================
$amountQuery = "
    SELECT 
        DATE(transaction_time) AS day,
        SUM(transaction_amount) AS totalTransactionValue
    FROM 
        " . TABLE_PREFIX . "transaction
    WHERE 
        transaction_status = 1
        AND transaction_time BETWEEN '{$startDate}' AND '{$endDate}'
        {$userFilter}
        AND transaction_site_id = " . SITE_ID . "
    GROUP BY 
        DATE(transaction_time)
    ORDER BY 
        day ASC
";

$rawData = [];

if ($res = mysqli_query($connection, $amountQuery)) {
    while ($row = mysqli_fetch_assoc($res)) {
        $formattedDay = date('d-m-Y', strtotime($row['day']));
        $rawData[$formattedDay] = (float)$row['totalTransactionValue'];
    }
}

// Fill missing dates
$data = [];
$current = strtotime(substr($startDate, 0, 10));
$end     = strtotime(substr($endDate, 0, 10));

while ($current <= $end) {
    $dayKey = date('d-m-Y', $current);
    $data[$dayKey] = $rawData[$dayKey] ?? 0;
    $current = strtotime('+1 day', $current);
}

$total = array_sum($data);

echo json_encode(
    [
        'total' => $total,
        'data'  => $data,
    ],
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
);