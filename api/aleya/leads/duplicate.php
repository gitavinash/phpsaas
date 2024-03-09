<?php

    require_once LOGIN_CHECK;

    $leadId = base64_decode($id);

    mysqli_query($connection, "START TRANSACTION");

    if($checkLead=mysqli_query($connection, "SELECT enquiry_id FROM ".TABLE_PREFIX."enquiry WHERE enquiry_id='{$leadId}' AND enquiry_status=0 AND enquiry_site_id=".SITE_ID)) {
        if(mysqli_num_rows($checkLead)!=0) {

            extract(mysqli_fetch_assoc($checkLead));

            if($updateLead=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."enquiry SET enquiry_status=5 WHERE enquiry_id='{$leadId}' AND enquiry_site_id=".SITE_ID)) {

                if($createNotes=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."enquiry_notes(enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value) VALUES('{$leadId}', '".USER_ID."', 'duplicate', '".PROFILE_DISPLAY_NAME."')")) {
                    $status='success';
                    $responseMsg="Lead mark duplicate successfully";
                } else {
                    $status='error';
                    $responseMsg="Something went wrong while assigning lead to user";
                }
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

    if($status=='success') {
        mysqli_query($connection, "COMMIT");
    } else {
        mysqli_query($connection, "ROLLBACK");
    }

    echo json_encode(array(
        'status' => $status,
        'msg' => $responseMsg
    ), TRUE);
    
    
    exit();