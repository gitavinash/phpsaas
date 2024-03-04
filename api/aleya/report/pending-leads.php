<?php

    require_once LOGIN_CHECK;

    if($daterange!='null') {
        $dates = explode(" - ", $daterange);
        $startDate = date('Y-m-d 00:00:00', strtotime($dates[0]));
        $endDate = date('Y-m-d 23:59:59', strtotime($dates[1]));
    } else {
        $startDate = date("Y-m-01 00:00:00");
        $endDate = date("Y-m-31 23:59:59");
    }
    
    if(isset($userId)) {
        $userId = base64_decode($userId);
        $userId = "AND enquiry_agent = '{$userId}' AND ";
    } else {
        $userId=' AND ';
    }
    
    if($enquiry=mysqli_query($connection, "SELECT count(enquiry_id) as totalLeads FROM ".TABLE_PREFIX."enquiry WHERE enquiry_status=2 AND enquiry_time BETWEEN '{$startDate}' AND '{$endDate}' {$userId} enquiry_site_id=".SITE_ID)) {

        if(mysqli_num_rows($enquiry)!=0) {
            extract(mysqli_fetch_assoc($enquiry));
        }

    }

    $result = array(
        'totalLeads' => ((isset($totalLeads)) ? $totalLeads : 0)
    );

    echo json_encode($result, TRUE);

?>