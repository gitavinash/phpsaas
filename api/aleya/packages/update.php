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

        $updateItineraryQuery = "UPDATE " . TABLE_PREFIX . "tour_package 
                SET 
                    tour_package_title = '{$packageName}', 
                    tour_package_theme = '{$travelStyle}', 
                    tour_package_overview = '{$overview}', 
                    tour_package_itinerary = '{$tourItinerary}', 
                    tour_package_inclusions = '{$inclusion}', 
                    tour_package_exclusions = '{$exclusion}', 
                    tour_package_hotel = '{$hotel}', 
                    tour_package_type = '{$packageType}', 
                    tour_package_outstation_vehicle = '" . strtolower($outstationVehicle) . "', 
                    tour_package_localsightseen_vehicle = '" . strtolower($localSighseeignVehicle) . "', 
                    tour_package_status = 0, 
                    tour_package_updated_at = NOW()
                WHERE 
                    tour_package_created_by = " . USER_ID . "
                    AND tour_package_id = '" . base64_decode($tourPackageId) . "'
                    AND tour_package_site_id = " . SITE_ID;

        if (mysqli_query($connection, $updateItineraryQuery)) {
            $httpStatus = 201;
            $status = 'success';
            $msg = 'Itinerary update successfully';
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
