<?php

require_once LOGIN_CHECK;

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $requiredFields = [
        'startDate',
        'endDate',
        'adultNet',
        'adultSelling',
        'childNet',
        'childSelling',
        'id'
    ];

    $allValid = true; // Flag to check if all fields are valid

    foreach ($requiredFields as $field) {
        if (!isset($payload[$field]) || (is_array($payload[$field]) && empty($payload[$field])) || (!is_array($payload[$field]) && trim($payload[$field]) === '')) {
            $validRequest = false;
            $msg = "Field '{$field}' is missing or empty.";
        }
    }
    if (isset($validRequest)) {

        $itineraryId = base64_decode($id);
        $startDate = date('1997-m-d', strtotime($startDate));
        $endDate = date('1997-m-d', strtotime($endDate));

        $updateItineraryPriceQuery = "UPDATE " . TABLE_PREFIX . "tour_package_price 
                SET 
                    tour_package_price_date_start='{$startDate}',
                    tour_package_price_date_end='{$endDate}',
                    tour_package_price_net_adult='{$adultNet}',
                    tour_package_price_net_child='{$childNet}',
                    tour_package_price_mrp_adult='{$adultMRP}',
                    tour_package_price_mrp_child='{$childMRP}',
                    tour_package_price_selling_adult='{$adultSelling}',
                    tour_package_price_selling_child='{$childSelling}',
                    tour_package_price_agent_adult='{$adultAgent}',
                    tour_package_price_agent_child='{$childAgent}'
                WHERE 
                    tour_package_price_id = {$itineraryId}
                    AND tour_package_price_site_id = " . SITE_ID;

        // echo $updateItineraryPriceQuery;
        // exit();

        if (mysqli_query($connection, $updateItineraryPriceQuery)) {
            $httpStatus = 201;
            $status = 'success';
            $msg = 'Itinerary price update successfully';
        } else {
            $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something went wrong');
        }
    } else {
        $msg = 'Not valid request';
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
