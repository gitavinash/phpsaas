<?php

require_once LOGIN_CHECK;

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
    $userId = "AND request_agent = '{$userId}' AND ";
} else {
    $userId = ' AND ';
}

if ($request = mysqli_query($connection, "SELECT count(request_id) as totalLeads FROM " . TABLE_PREFIX . "request WHERE request_status=0 AND request_time BETWEEN '{$startDate}' AND '{$endDate}' {$userId} request_site_id=" . SITE_ID)) {

    if (mysqli_num_rows($request) != 0) {
        extract(mysqli_fetch_assoc($request));
    }
}

$result = array(
    'totalLeads' => ((isset($totalLeads)) ? $totalLeads : 0)
);

echo json_encode($result, TRUE);
