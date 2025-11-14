<?php

// require_once LOGIN_CHECK;

$daterange = (isset($_GET['daterange'])) ? $_GET['daterange'] : 'null';

if ($daterange != 'null') {
    $dates = explode(" - ", $daterange);
    $startDate = date('Y-m-d 00:00:00', strtotime($dates[0]));
    $endDate = date('Y-m-d 23:59:59', strtotime($dates[1]));
} else {
    $startDate = date("Y-m-d 00:00:00", strtotime("-1 month"));
    $endDate = date("Y-m-d 23:59:59");
}

if (isset($userId)) {
    $userId = base64_decode($userId);
    $userId = "AND enquiry_agent = '{$userId}' AND ";
} else {
    $userId = ' AND ';
}

if ($transaction = mysqli_query($connection, "SELECT sum(transaction_amount) as transactionValue FROM " . TABLE_PREFIX . "transaction WHERE transaction_status=1 AND transaction_time BETWEEN '{$startDate}' AND '{$endDate}' {$userId} transaction_site_id=" . SITE_ID)) {

    if (mysqli_num_rows($transaction) != 0) {
        extract(mysqli_fetch_assoc($transaction));
    }
}

$result = array(
    'transactionValue' => ((isset($transactionValue)) ? $transactionValue : 0)
);

echo json_encode($result, TRUE);