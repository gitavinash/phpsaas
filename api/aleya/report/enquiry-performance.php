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

if ($enquiry = mysqli_query($connection, "SELECT 
    CASE enquiry_status
        WHEN 0 THEN 'fresh'
        WHEN 1 THEN 'confirm'
        WHEN 2 THEN 'pending'
        WHEN 3 THEN 'follow up'
        WHEN 4 THEN 'cancel'
        ELSE 'unknown'
    END AS status_name,
    COUNT(*) AS total_count
FROM 
    isw_enquiry
WHERE 
    enquiry_time BETWEEN '{$startDate}' AND '{$endDate}'
GROUP BY 
    enquiry_status;
")) {

    if (mysqli_num_rows($enquiry) != 0) {
        while ($sourceData = mysqli_fetch_assoc($enquiry)) {
            $enquiry_source = $sourceData['status_name'];
            $totalLeads = $sourceData['total_count'];
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