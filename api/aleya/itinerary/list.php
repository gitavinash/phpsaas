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
            $itineraryQuery = "SELECT * FROM " . TABLE_PREFIX . "tour_package WHERE tour_package_country LIKE '%{$destination}%' {$themeQuery} AND tour_package_status=1 AND  tour_package_site_id=" . SITE_ID;
            $cityName = $destination;
            break;
        case 2:
            $stateName = $placeInfo[0];
            $countryName = $placeInfo[1];
            $cityName = $stateName;

            $itineraryQuery = "SELECT * FROM " . TABLE_PREFIX . "tour_package WHERE tour_package_state LIKE '%{$stateName}%' {$themeQuery} AND tour_package_status=1 AND  tour_package_site_id=" . SITE_ID;
            break;
        default:
            $cityName = $placeInfo[0];
            $stateName = $placeInfo[count($placeInfo) - 2];
            $countryName = end($placeInfo);

            $itineraryQuery = "SELECT * FROM " . TABLE_PREFIX . "tour_package WHERE tour_package_city LIKE '%{$cityName}%' AND tour_package_state LIKE '%{$stateName}%' {$themeQuery} AND tour_package_status=1 AND  tour_package_site_id=" . SITE_ID;
            break;
    }

    if ($checkItinerary = mysqli_query($connection, $itineraryQuery)) {
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
    }
}

$result = array(
    'status' => $status,
    'msg' => $msg,
    'itineraryList' => (isset($itineraryList) ? $itineraryList : '')
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
