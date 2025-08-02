<?php

require_once LOGIN_CHECK;
header('Content-Type: application/json');

$responseCode = 400; // Default to bad request
$status = 'error';
$message = 'Query parameter is required.';
$locations = [];

if (isset($_GET['q'])) {
    $q = trim($_GET['q']);

    if ($q !== '') {
        $escaped_q = mysqli_real_escape_string($connection, $q);

        $query = "SELECT location_title FROM " . TABLE_PREFIX . "locations WHERE location_title LIKE '%{$escaped_q}%'";
        $checkLocation = mysqli_query($connection, $query);

        if ($checkLocation && mysqli_num_rows($checkLocation) > 0) {
            while ($location = mysqli_fetch_assoc($checkLocation)) {
                $locations[] = $location['location_title']; // Only title as string
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