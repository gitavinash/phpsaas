<?php

    require_once LOGIN_CHECK;
if ($daterange != 'null') {
    $dates = explode(" - ", $daterange);
    $startDate = date('Y-m-d 00:00:00', strtotime($dates[0]));
    $endDate = date('Y-m-d 23:59:59', strtotime($dates[1]));
} else {
    $currentMonth=date('m');
    $startDate = date("Y-{$currentMonth}-01 00:00:00");
    $endDate = date("Y-{$currentMonth}-31 23:59:59");
}

if(isset($userId)) {
        $userId = base64_decode($userId);
    $userId = "AND enquiry_agent = '{$userId}'";
} else {
        $userId='';
    }

$labels = array(); // Initialize labels array
$data = array(); // Initialize data array

if ($enquiry = mysqli_query($connection, "SELECT enquiry_status 
        WHEN 0 THEN 'pending'
        WHEN 1 THEN 'confirm'
        ELSE 'Unknown'
    END AS status_label, COUNT(*) AS totalLeads FROM " . TABLE_PREFIX . "enquiry WHERE enquiry_time BETWEEN '{$startDate}' AND '{$endDate}' AND {$userId} enquiry_site_id=" . SITE_ID . " GROUP BY enquiry_status")) {

    if (mysqli_num_rows($enquiry) != 0) {
        while ($sourceData = mysqli_fetch_assoc($enquiry)) {
            $enquiry_source = $sourceData['enquiry_status'];
            $totalLeads = $sourceData['totalLeads'];
            // Append label and data to respective arrays
            $labels[] = ucwords($enquiry_source);
            $data[] = $totalLeads;
        }
    }
}

// Output the JSON
$output = array(
    'labels' => $labels,
    'data' => $data
);

echo json_encode($output, TRUE);