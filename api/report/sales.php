<?php

// require_once LOGIN_CHECK;

$daterange = (isset($_GET['daterange'])) ? $_GET['daterange'] : 'null';

if ($daterange != 'null') {
    $dates = explode(" - ", $daterange);
    // FIXED SAME-DAY DETECTION
    $isSameDay = (date("Y-m-d", strtotime($dates[0])) === date("Y-m-d", strtotime($dates[1])));

    $startDate = date('Y-m-d 00:00:00', strtotime($dates[0]));
    $endDate = date('Y-m-d 23:59:59', strtotime($dates[1]));
} else {
    $isSameDay = false;
    $startDate = date("Y-m-d 00:00:00", strtotime("-1 month"));
    $endDate = date("Y-m-d 23:59:59");
}

if (isset($userId)) {
    $decodedUser = base64_decode($userId);
    $userId = " AND user_id = '" . intval($decodedUser) . "' ";
} else {
    $userId = " AND ";
}

/* ========= IF SAME DATE → HOURLY QUERY ========= */
if ($isSameDay) {

    $amountQuery = "SELECT 
        HOUR(transaction_time) AS hour,
        SUM(transaction_amount) AS totalTransactionValue
    FROM 
        " . TABLE_PREFIX . "transaction
    WHERE 
        transaction_status = 1
        AND transaction_time BETWEEN '{$startDate}' AND '{$endDate}'
        {$userId}
        transaction_site_id = " . SITE_ID . "
    GROUP BY 
        HOUR(transaction_time)
    ORDER BY 
        hour ASC";

    $data = array();

    if ($transaction = mysqli_query($connection, $amountQuery)) {
        while ($row = mysqli_fetch_assoc($transaction)) {
            $hourKey = str_pad($row['hour'], 2, "0", STR_PAD_LEFT) . ":00";
            $data[$hourKey] = $row['totalTransactionValue'];
        }
    }

    // Fill hours 08 → 12
    $result = array();
    for ($h = 0; $h <= 24; $h++) {
        // Original format "08:00"
        $oldKey = str_pad($h, 2, "0", STR_PAD_LEFT) . ":00";

        // New format "08 AM"
        $newKey = date("h:00 A", strtotime($oldKey));

        // Transfer value using old key
        $result[$newKey] = isset($data[$oldKey]) ? $data[$oldKey] : 0;
    }

    echo json_encode($result, TRUE);
    exit;
}

/* ========= DEFAULT: DAY WISE (YOUR ORIGINAL BEHAVIOR) ========= */

$amountQuery = "SELECT 
    DATE(transaction_time) AS day,
    SUM(transaction_amount) AS totalTransactionValue
FROM 
    " . TABLE_PREFIX . "transaction
WHERE 
    transaction_status = 1
    AND transaction_time BETWEEN '{$startDate}' AND '{$endDate}'
    {$userId}
    transaction_site_id = " . SITE_ID . "
GROUP BY 
    DATE(transaction_time)
ORDER BY 
    day ASC";

$data = array();

if ($transaction = mysqli_query($connection, $amountQuery)) {
    while ($row = mysqli_fetch_assoc($transaction)) {
        $formattedDay = date("d-m-Y", strtotime($row['day']));
        $data[$formattedDay] = $row['totalTransactionValue'];
    }
}

/* Fill missing dates */
$result = array();
$current = strtotime(substr($startDate, 0, 10));
$end = strtotime(substr($endDate, 0, 10));

while ($current <= $end) {
    $dayKey = date("d-m-Y", $current);
    $result[$dayKey] = isset($data[$dayKey]) ? $data[$dayKey] : 0;
    $current = strtotime("+1 day", $current);
}

echo json_encode($result, TRUE);