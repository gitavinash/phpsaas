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
    "1 Star Hotel ( Tourist )",
    "2 Star Hotel ( Standard )",
    "3 Star Hotel ( Comfort )",
    "4 Star Hotel ( First Class )",
    "5 Star Hotel ( Luxury )",
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