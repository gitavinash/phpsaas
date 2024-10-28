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
        'startCity',
        'startCity',
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

        $placeInfo = explode(',', $location);

        $cityName = $placeInfo[0];
        $stateName = $placeInfo[count($placeInfo) - 2];
        $countryName = end($placeInfo);

        mysqli_query($connection, "START TRANSATION");

        $insertItinerary = "INSERT INTO " . TABLE_PREFIX . "tour_package(tour_package_site_id, tour_package_created_by, tour_package_title, tour_package_theme, tour_package_start_from, tour_package_end_to, tour_package_city, tour_package_duration, tour_package_overview, tour_package_itinerary, tour_package_inclusions, tour_package_exclusions, tour_package_hotel, tour_package_state, tour_package_country,  tour_package_type, tour_package_outstation_vehicle, tour_package_localsightseen_vehicle, tour_package_status, tour_package_created_at) VALUES(" . SITE_ID . ", " . USER_ID . ", '{$packageName}', '{$travelStyle}', '{$startCity}', '{$endCity}', '{$cityName}', '{$duration}', '{$overview}', '{$tourItinerary}', '{$inclusion}', '{$exclusion}', '{$hotel}', '{$stateName}', '{$countryName}', '{$packageType}', '" . strtolower($outstationVehicle) . "', '" . strtolower($localSighseeignVehicle) . "', 2, NOW())";

        $createNewItinerary = mysqli_query($connection, $insertItinerary);
        $itineraryId = mysqli_insert_id($connection);

        function convertToUnderscore($string)
        {
            // Remove all special characters except alphanumeric and spaces, replace them with '_'
            $string = preg_replace('/[^A-Za-z0-9\s]/', '_', $string);

            // Replace any whitespace (space, tab, newline, etc.) with '_'
            $string = preg_replace('/\s+/', '_', $string);

            return $string;
        }

        $url = convertToUnderscore($packageName);
        $url = strtolower('/' . $url . '-' . time() . '/');

        $insertLink = "INSERT INTO " . TABLE_PREFIX . "link(link_url, link_relation_id, link_status, link_type, link_owner_id, link_site_id, link_creation, link_published) VALUES('{$url}', '{$itineraryId}', 200, 'tour_package', '" . USER_ID . "', '" . SITE_ID . "', NOW(), NOW())";
        $createPublicLink = mysqli_query($connection, $insertLink);
        $linkId = mysqli_insert_id($connection);

        $insertMeta = "INSERT INTO " . TABLE_PREFIX . "meta(meta_relation_id, meta_site_id, meta_type, meta_robots) VALUES('{$linkId}', '" . SITE_ID . "', 'tour_package', 'index, follow')";
        $createMeta = mysqli_query($connection, $insertMeta);

        if ($createNewItinerary && $createPublicLink && $createMeta) {
            $httpStatus = 201;
            $status = 'success';
            $msg = 'Itinerary created successfully';
            mysqli_query($connection, "COMMIT");
        } else {
            $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something went wrong');
            mysqli_query($connection, "ROLLBACK");
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
