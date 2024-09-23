<?php

$chatGPTKey = (defined('CHAT_GPT_API') ? CHAT_GPT_API : '');

// API endpoint URL
$url = 'https://api.openai.com/v1/chat/completions'; // Replace with your actual API endpoint

// Prepare the data for the API request
$data = [
    "model" => "gpt-4",
    "messages" => [
        [
            "role" => "system",
            "content" => "You are an assistant that only responds in JSON format. Any answer provided should be a valid JSON object or array."
        ],
        [
            "role" => "user",
            "content" => "Create a tour itinerary for a 5-day trip to Shimla and Manali. Include day titles, overviews with at least 300 characters each, and an itinerary overview field with inclusions and exclusions."
        ]
    ],
    "temperature" => 0
];

// Initialize cURL
$ch = curl_init($url);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $chatGPTKey,
]);

// Execute the cURL request and get the response
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    // Decode the JSON response
    $responseData = json_decode($response, true);

    // Print the formatted response
    $itineraryData = json_encode($responseData['choices'][0]['message']['content'], JSON_PRETTY_PRINT);
    // json_decode($result, true);
}

// Close cURL session
curl_close($ch);
