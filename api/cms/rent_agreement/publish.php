<?php

function publish($connection, $data)
{

    extract($data);

    $httpStatus = 500;
    $status = 'warning';
    $msg = '';

    $requiredFields = [
        'landlord_name',
        'landlord_mobile',
        'landlord_address',
        'landlord_father_name',
        'landlord_email',
        'tenate_name',
        'tenate_mobile',
        'tenate_address',
        'tenate_father_name',
        'tenate_email',
        'property_floor',
        'property_bhk',
        'property_address',
        'agreement_start_date',
        'rent_amount',
        'state',
        'payment_date',
        'security_amount',
        'notice_period'
    ];

    foreach ($requiredFields as $field) {
        if (empty($$field)) {
            $msg = ucfirst(str_replace('_', ' ', $field)) . ' is missing';
            echo json_encode(['status' => $status, 'msg' => $msg, 'enquiryId' => $request_id], TRUE);
            return;
        }
    }

    if (!filter_var($landlord_email, FILTER_VALIDATE_EMAIL)) {
        $msg = "Invalid landlord email";
        echo json_encode(['status' => $status, 'msg' => $msg, 'enquiryId' => $request_id], TRUE);
        return;
    }

    if (!filter_var($tenate_email, FILTER_VALIDATE_EMAIL)) {
        $msg = "Invalid tenate email";
        echo json_encode(['status' => $status, 'msg' => $msg, 'enquiryId' => $request_id], TRUE);
        return;
    }

    if ($checkRequest = mysqli_query($connection, "SELECT enquiry_id FROM " . TABLE_PREFIX . "enquiry WHERE enquiry_id='" . base64_decode($request_id) . "' AND enquiry_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkRequest) != 0) {
            $requestIsValid = true;
        }
    }

    if ($requestIsValid) {

        unset($_REQUEST['type']);
        unset($_REQUEST['request_id']);
        unset($_REQUEST['action']);
        if (sizeof($_REQUEST) != 0) {
            foreach ($_REQUEST as $key => $value) {
                $enquiryMeta[$key] = $value;
            }
        }
        $messageDB = json_encode($enquiryMeta, TRUE);

        if (mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "enquiry SET enquiry_message='{$messageDB}' WHERE enquiry_id='" . base64_decode($request_id) . "' AND enquiry_status=0 AND enquiry_site_id=" . SITE_ID)) {
            $status = 'success';
            $httpStatus = 201;
            $msg = 'Procces to next step';
        } else {
            $status = 'error';
            $msg = 'Something went wrong while updating agreement';
        }
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
        'enquiryId' => ((isset($request_id)) ? $request_id : ''),
    );

    echo json_encode($result, TRUE);
    http_response_code($httpStatus);
}