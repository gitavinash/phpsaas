<?php

    $leadId = base64_decode($leadId);
    $userId = base64_decode($userId);

    $userData=explode("_", $userId);
    $userId=$userData[0];
    $userName=$userData[1];

    if($checkLead=mysqli_query($connection, "SELECT enquiry_id FROM ".TABLE_PREFIX."enquiry WHERE enquiry_id='{$leadId}' AND enquiry_site_id=".SITE_ID)) {
        if(mysqli_num_rows($checkLead)!=0) {

            extract(mysqli_fetch_assoc($checkLead));

            if($updateLead=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."enquiry SET enquiry_agent='{$userId}' WHERE enquiry_id='{$leadId}' AND enquiry_status=2 AND enquiry_site_id=".SITE_ID)) {
                $status='success';
                $responseMsg="Lead assing to {$userName} successfully";
            } else {
                $status='error';
                $responseMsg='Something went wrong';
            }

        } else {
            $status='warning';
            $responseMsg='Invalid lead information';
        }
    } else {
        $status='error';
        $responseMsg='Something went wrong';
    }

    $status=urlencode($status);
    $responseMsg=urlencode($responseMsg);
    
    header("Location: /dashboard/leads/list/?status={$status}&responseMsg={$responseMsg}", TRUE, 301);
    exit();