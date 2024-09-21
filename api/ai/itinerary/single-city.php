<?php

$chatGPTKey = (defined('CHAT_GPT_API') ? CHAT_GPT_API : '');

if ($chatGPTKey != '') {
    $endpoint = "https://api.openai.com/v1/chat/completions";

    // Prepare the data to send in the API request
    $data = [
        "model" => "gpt-3.5-turbo", // GPT-3.5-turbo model
        "messages" => [
            [
                "role" => "system",
                "content" => "You are an expert travel planner with more than 10 years of experience in Tour Package Planning. You work for {$travelCompanyName}, a New Delhi-based company that's 5 years old."
            ],
            [
                "role" => "user",
                "content" => "Create a detailed itinerary of {$destinationCity} {$travelStyle} tour package for {$tripDuration} nights, " . ($tripDuration + 1) . " days {$travelStyle} trip plan for a tour from {$startingCity} to {$destinationCity} by {$travelCompanyName}. {$startingCity} to {$destinationCity} will be transfer by {$outstationVehicle} and local sightseeing will be done by {$localSighseeignVehicle}.
                - Format the output in JSON format with:
                {
                    'overview: {overview_of_{$destinationCity}_{$travelStyle}_tour_package_intinenary}
                    'durations':[
                        {
                            'day': {{day_number}} - {{day_title}},
                            'details': {{detailed_paragraph_for_the_day}}
                        }
                    ]
                    'inclusion: {inclusion_of_{$destinationCity}_{$travelStyle}_tour_package_intinenary}
                    'exclusion: {exclusion_of_{$destinationCity}_{$travelStyle}_tour_package_intinenary}
                }
Ensure each day has at least a 500 word paragraph describing the day's activities."
            ]
        ],
        "max_tokens" => 1024,
        "n" => 1,
        "temperature" => 0.7
    ];

    // Set headers for the API request
    $headers = [
        "Content-Type: application/json",
        "Authorization: " . 'Bearer ' . $chatGPTKey
    ];

    // Initialize a cURL session to make the API request
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Execute the API request and capture the response
    $response = curl_exec($ch);
    curl_close($ch);

    // Decode the JSON response from the API
    $result = json_decode($response, true);

    // Check if the response contains the trip content
    if (isset($result['choices'][0]['message']['content'])) {
        $tripData = json_decode($result['choices'][0]['message']['content'], true);
        if ($tripData != '') {
            $status = 'success';
        }
    } else {
        return json_encode([
            'error' => 'Failed to generate itinerary',
        ]);
    }

    echo json_encode(array(
        'status' => ((isset($status)) ? $status : 'error'),
        'itinerary' => ((isset($tripData)) ? $tripData : '')
    ), TRUE);
}
