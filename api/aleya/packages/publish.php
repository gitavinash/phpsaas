<?php

require_once LOGIN_CHECK;

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $requiredFields = [
        'packageName',
        'travelStyle',
        'duration',
        'overview',
        'dayTitle',
        'dayContent',
        'inclusion',
        'exclusion',
        'hotel',
        'packageType',
        'outstationVehicle',
        'localSighseeignVehicle',
        'location',
        'start',
    ];

    $allValid = true; // Flag to check if all fields are valid

    foreach ($requiredFields as $field) {
        if (!isset($payload[$field]) || (is_array($payload[$field]) && empty($payload[$field])) || (!is_array($payload[$field]) && trim($payload[$field]) === '')) {
            $validRequest = false;
            $msg = "Field '{$field}' is missing or empty.";
        }
    }
    if (isset($validRequest)) {
        // $packageCity = ((isset($location[0])) ? $location[0] : $location);

        $tourItinerary = array();

        for ($i = 0; $i < count($_REQUEST['dayTitle']); $i++) {
            $tourItinerary[] = [
                'day' => $_REQUEST['dayTitle'][$i],
                'overview' => $_REQUEST['dayContent'][$i]
            ];
        }

        $tourItinerary = json_encode($tourItinerary, TRUE);

        $msg = 'Valid request';
        $insertItinerary = "INSERT INTO " . TABLE_PREFIX . "tour_package(tour_package_site_id, tour_package_created_by, tour_package_title, tour_package_theme, tour_package_start_from, tour_package_city, tour_package_duration, tour_package_overview, tour_package_itinerary, tour_package_inclusions, tour_package_exclusions, tour_package_hotel, tour_package_type, tour_package_outstation_vehicle, tour_package_localsightseen_vehicle, tour_package_status, tour_package_created_at) VALUES(" . SITE_ID . ", " . USER_ID . ", '{$packageName}', '{$travelStyle}', '{$start}', '{$location}', '{$duration}', '{$overview}', '{$tourItinerary}', '{$inclusion}', '{$exclusion}', '{$hotel}', '{$packageType}', '" . strtolower($outstationVehicle) . "', '" . strtolower($localSighseeignVehicle) . "', 0, NOW())";

        if (mysqli_query($connection, $insertItinerary)) {
            $itineraryId = mysqli_insert_id($connection);
            $httpStatus = 201;
            $status = 'success';
            $msg = 'Itinerary created successfully';
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
    'id' => base64_encode(((isset($itineraryId)) ? $itineraryId : 0)),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
