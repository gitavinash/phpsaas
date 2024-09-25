<?php

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($validRequest)) {
        $msg = 'Valid request';
    } else {
        $msg = 'Not valid request';
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
    'id' => ((isset($productId)) ? $productId : 0),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
