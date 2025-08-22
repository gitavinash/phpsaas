<?php
header('Content-Type: application/json');

// Room Types
$roomTypes = [
    "Deluxe",
    "Super Deluxe",
    "Luxury",
    "Family Room",
    "Executive Suite",
    "Junior Suite",
    "Super Deluxe Room", // ✅ fixed typo
    "King Size"
];

// Build API response
$response = [
    "status" => "success",
    "roomTypes" => $roomTypes
];

// Output JSON
echo json_encode($response, JSON_PRETTY_PRINT);