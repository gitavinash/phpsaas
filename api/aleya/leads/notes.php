<?php

    require_once LOGIN_CHECK;

    $leadId = base64_decode($leadId);

    mysqli_query($connection, 'START TRANSACTION');

    if($checkEnquiryAccess=mysqli_query($connection, "SELECT enquiry_id, enquiry_status FROM ".TABLE_PREFIX."enquiry WHERE enquiry_id='{$leadId}' AND enquiry_agent='".USER_ID."' AND enquiry_site_id=".SITE_ID)) {
        if(mysqli_num_rows($checkEnquiryAccess)!=0) {
            extract((mysqli_fetch_assoc($checkEnquiryAccess)));

            if($enquiry_status!=4) {

                if($updateNotes=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."enquiry_notes(enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value) VALUES('{$leadId}', '".USER_ID."', '{$title}', '{$msg}')")) {

                    if(isset($finalNotes)) {
                        $enquiryStatus=4;
                    } else {
                        $enquiryStatus=3;
                    }

                    if($updateEnquiry=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."enquiry SET enquiry_status={$enquiryStatus} WHERE enquiry_id='{$leadId}' AND enquiry_site_id=".SITE_ID)) {
                        $status='success';
                        $msg='Notes update successfully';
                        mysqli_query($connection, "COMMIT");
                    } else {
                        $status='error';
                        $msg='Something went wrong while updating the enquiry status';
                        mysqli_query($connection, "ROLLBACK");
                    }

                } else {
                    echo mysqli_error($connection);
                    $status='error';
                    $msg='Something went wrong while updating the notes';
                    mysqli_query($connection, "ROLLBACK");
                }

            } else {
                $status='warning';
                $msg='Final interaction note is already updated';
            }

        } else {
            $status='warning';
            $msg="You're not authorized to update this lead.";
        }
    } else {
        $status='error';
        $msg='Invalid request found';
    }

    $result = array(
        'status' => $status,
        'msg'=> $msg
    );

    echo json_encode($result, TRUE);
    exit();