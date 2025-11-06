<?php

require_once COMMUNICATION_FUNCTIONS;
require_once LOGIN_CHECK;

$httpStatus = 500;
$status = 'warning';
$msg = 'Something went wrong';
$data = array();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($membershipId)) {
        $membershipId = base64_decode($membershipId);
        if ($checkVisits = mysqli_query($connection, "SELECT visit_duration as duration, visit_room_number as roomNumber, visit_time as visitTime, visit_staff_one_id as staffOne, visit_staff_two_id as staffTwo FROM " . TABLE_PREFIX . "membership_visits WHERE visit_site_id=" . SITE_ID . " AND visit_membership_id='{$membershipId}' AND visit_status='1' ORDER BY visit_time ASC")) {
            $visits = array();
            if (mysqli_num_rows($checkVisits) != 0) {
                while ($visit = mysqli_fetch_assoc($checkVisits)) {
                    // Convert visit_time to a DateTime object
                    $date = new DateTime($visit['visitTime']);

                    // Format the visit_time as "10th January 2025 10:55 PM"
                    $formattedVisitTime = $date->format('jS F Y h:i A');

                    // Store the formatted time in the visit array
                    $visit['visitTime'] = $formattedVisitTime;
                    $visits[] = $visit;
                }

                $status = 'success';
                $httpStatus = 200;
                $msg = "Visits found";
            } else {
                $status = 'warning';
                $httpStatus = 200;
                $msg = 'No History found';
            }
        } else {

            $msg = 'Technical error';
        }
    } else {
        $msg = 'Something is wrong while sending request to server';
    }
} else {
    $msg = 'Invalid request method';
}


$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Something went wrong'),
    'visits' => ((isset($visits)) ? $visits : '')
);


http_response_code($httpStatus);
echo json_encode($result, TRUE);