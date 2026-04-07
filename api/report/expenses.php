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
// EXPENSE USER FILTER (optional, expects ?user_id=...)
$userFilter = '';
$userId = $_GET['user_id'] ?? '';
if (!empty($userId)) {
    $decodedUser = base64_decode($userId, true);
    $decodedUser = $decodedUser !== false ? (int)$decodedUser : 0;
    if ($decodedUser > 0) {
        $userFilter = ' AND expense_user_id = ' . $decodedUser . ' ';
    }
}


// ======================================================
//  SAME DAY → HOURLY SUM(expense_amount)
// ======================================================
if ($isSameDay) {
    $amountQuery = "
        SELECT 
            HOUR(expense_time) AS hour,
            SUM(expense_amount) AS totalExpenseValue
        FROM 
            " . TABLE_PREFIX . "expense
        WHERE 
            expense_status = 1
            AND CONCAT(DATE(expense_time), ' ', TIME(expense_time)) BETWEEN '{$startDate}' AND '{$endDate}'
            {$userFilter}
            AND expense_site_id = " . SITE_ID . "
        GROUP BY 
            HOUR(expense_time)
        ORDER BY 
            hour ASC
    ";

    $rawData = [];

    if ($res = mysqli_query($connection, $amountQuery)) {
        while ($row = mysqli_fetch_assoc($res)) {
            $hourKey = str_pad((int)$row['hour'], 2, '0', STR_PAD_LEFT) . ':00';
            $rawData[$hourKey] = (float)$row['totalExpenseValue'];
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

//  DEFAULT RANGE → DAY-WISE SUM(expense_amount)
// ======================================================
$amountQuery = "
    SELECT 
        DATE(expense_time) AS day,
        SUM(expense_amount) AS totalExpenseValue
    FROM 
        " . TABLE_PREFIX . "expense
    WHERE 
        expense_status = 1
        AND CONCAT(DATE(expense_time), ' ', TIME(expense_time)) BETWEEN '{$startDate}' AND '{$endDate}'
        {$userFilter}
        AND expense_site_id = " . SITE_ID . "
    GROUP BY 
        DATE(expense_time)
    ORDER BY 
        day ASC
";

$rawData = [];

if ($res = mysqli_query($connection, $amountQuery)) {
    while ($row = mysqli_fetch_assoc($res)) {
        $formattedDay = date('d-m-Y', strtotime($row['day']));
        $rawData[$formattedDay] = (float)$row['totalExpenseValue'];
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