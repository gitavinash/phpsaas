<?php
header('Content-Type: application/json');

// Property Types
$propertyTypes = [
    "Apartments",
    "Capsule hotel",
    "Guest house",
    "Inn",
    "Hostel",
    "House",
    "Home stay",
    "1 Star Hotel",
    "2 Star Hotel",
    "3 Star Hotel",
    "4 Star Hotel",
    "5 Star Hotel",
    "Motel",
    "Lodge",
    "Tent",
    "Farm stay",
    "Resorts"
];

// Build API response
$response = [
    "status" => "success",
    "propertyTypes" => $propertyTypes,
];

// Output JSON
echo json_encode($response, JSON_PRETTY_PRINT);