<?php

require_once LOGIN_CHECK;
header('Content-Type: application/json');

$responseCode = 400; // Default bad request
$status = 'error';
$message = 'Query parameter is required.';
$locations = [];

if (isset($_GET['q'])) {
    $q = trim($_GET['q']);

    if ($q !== '') {
        $escaped_q = mysqli_real_escape_string($connection, $q);

        $query = "
            SELECT location_title, state_name, country_name
            FROM " . TABLE_PREFIX . "locations
            LEFT JOIN " . TABLE_PREFIX . "state ON location_state = state_id
            LEFT JOIN " . TABLE_PREFIX . "country ON location_country = country_id
            WHERE location_title LIKE '%{$escaped_q}%'
               OR state_name LIKE '%{$escaped_q}%'
               OR country_name LIKE '%{$escaped_q}%'
            LIMIT 10;
        ";

        $checkLocation = mysqli_query($connection, $query);

        if ($checkLocation && mysqli_num_rows($checkLocation) > 0) {
            while ($row = mysqli_fetch_assoc($checkLocation)) {
                // Combine into single string
                $fullLocation = $row['location_title'] . (($row['state_name'] != '') ? ', ' . $row['state_name'] : '') . ', ' . $row['country_name'];
                $locations[] = $fullLocation;
            }
            $status = 'success';
            $message = 'Locations found.';
            $responseCode = 200;
        } else {
            $message = 'No locations found matching the query.';
            $responseCode = 404;
        }
    } else {
        $message = 'Query parameter cannot be empty.';
        $responseCode = 422;
    }
}

http_response_code($responseCode);
echo json_encode([
    'status' => $status,
    'message' => $message,
    'locations' => $locations
]);