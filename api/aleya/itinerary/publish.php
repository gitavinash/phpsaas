<?php

require_once LOGIN_CHECK;

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read raw POST data (JSON)
    $json = file_get_contents('php://input');

    // Decode JSON into PHP array
    $data = json_decode($json, true); // true = associative array

    // Access the values
    $id = isset($data['id']) ? $data['id'] : null;
    $content = isset($data['content']) ? $data['content'] : [];

    // Example: Validate and Save
    if ($id && !empty($content)) {
    } else {
        $httpStatus = 400;
        $msg = "Invalid data received";
        $status = "Invalid data received";
    }
}

$result = array(
    'status' => $status,
    'msg' => $msg,
    'itineraryList' => (isset($itineraryList) ? $itineraryList : '')
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);