<?php

    require_once LOGIN_CHECK;

    $leadId = base64_decode($leadId);

    print_r($_REQUEST);
    print_r($_FILES);
    exit();

    mysqli_query($connection, 'START TRANSACTION');

    if($checkEnquiryAccess=mysqli_query($connection, "SELECT enquiry_id, enquiry_status FROM ".TABLE_PREFIX."enquiry WHERE enquiry_id='{$leadId}' AND enquiry_agent='".USER_ID."' AND enquiry_site_id=".SITE_ID)) {
        if(mysqli_num_rows($checkEnquiryAccess)!=0) {
            extract((mysqli_fetch_assoc($checkEnquiryAccess)));

            if($enquiry_status!=4 || $enquiry_status!=5) {

                if($msg=='') {
                    $msg=$logginUserName;
                }

                if($updateNotes=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."enquiry_notes(enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value) VALUES('{$leadId}', '".USER_ID."', '{$title}', '{$msg}')")) {

                    $addFollowUp='';
                    if(isset($followupTime)) {
                        $enquiryStatus=3;
                        $addFollowUp=", enquiry_followup='".date('Y-m-d H:i:s', strtotime($followupTime))."' ";
                    } else {
                        $enquiryStatus=4;
                    }

                    if($updateEnquiry=mysqli_query($connection, "UPDATE ".TABLE_PREFIX."enquiry SET enquiry_status={$enquiryStatus} {$addFollowUp} WHERE enquiry_id='{$leadId}' AND enquiry_site_id=".SITE_ID)) {
                        $status='success';
                        if(isset($followupTime)) {
                            $msg='Follow up added successfully';
                        } else {
                            $msg='Notes update successfully';
                        }
                        mysqli_query($connection, "COMMIT");
                    } else {
                        echo mysqli_error($connection);
                        $status='error';
                        $msg='Something went wrong while updating the notes';
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