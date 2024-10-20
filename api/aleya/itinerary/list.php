<?php

require_once LOGIN_CHECK;

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    // City information

    $placeInfo = explode(', ', $destination);

    $theme = (($theme != 'select') ? $theme : '');
    $themeQuery = (($theme != '') ? " AND tour_package_theme='{$theme}'" : '');

    switch (count($placeInfo)) {
        case 1:
            $itineraryQuery = "TRIM(tour_package_country)='{$destination}' {$themeQuery} ";
            $cityName = $destination;
            break;
        case 2:
            $stateName = $placeInfo[0];
            $countryName = $placeInfo[1];
            $cityName = $stateName;

            $itineraryQuery = "TRIM(tour_package_state)='{$stateName}' {$themeQuery} ";
            break;
        default:
            $cityName = $placeInfo[0];
            $stateName = $placeInfo[count($placeInfo) - 2];
            $countryName = end($placeInfo);

            $itineraryQuery = "TRIM(tour_package_city)='{$cityName}' AND tour_package_state='{$stateName}' {$themeQuery}";
            break;
    }

    $finalInsertQuery = "SELECT * FROM " . TABLE_PREFIX . "tour_package INNER JOIN " . TABLE_PREFIX .
        "tour_package_price ON tour_package_price_parent_id=tour_package_id WHERE {$itineraryQuery} AND TRIM(tour_package_price_type) IN ('double', 'single') AND tour_package_status=1 AND  tour_package_site_id=" . SITE_ID;

    if ($checkItinerary = mysqli_query($connection, $finalInsertQuery)) {
        if (mysqli_num_rows($checkItinerary) != 0) {
            $itineraryList = array();
            while ($checkItineraryInfo = mysqli_fetch_assoc($checkItinerary)) {
                $itineraryList[] = $checkItineraryInfo;
            }
            $status = 'success';
            $httpStatus = 200;
            $msg = "Intenary for {$cityName} found successfully";
        } else {
            $status = 'warning';
            $httpStatus = 404;
            $msg = "No intenary found for {$cityName} {$theme}";
        }
    } else {
        $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something went wrong while executing query');
    }
}

$result = array(
    'status' => $status,
    'msg' => $msg,
    'itineraryList' => (isset($itineraryList) ? $itineraryList : '')
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
