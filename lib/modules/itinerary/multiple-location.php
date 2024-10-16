<?php

$chatGPTKey = (defined('CHAT_GPT_API') ? CHAT_GPT_API : '');

if ($chatGPTKey != '') {
    $endpoint = "https://api.openai.com/v1/chat/completions";

    $destinationsAndDurations = "";

    for ($i = 0; $i < sizeof($destinationCity); $i++) {
        $destinationsAndDurations .= "{$destinationCity[$i]} with {$tripDuration[$i]} nights, ";
    }

    $startTimings = (($outstationVehicle == 'volvo') ? 'in evening' : 'in morning');

    $localSighseeignVehicleUpdated = (($startingCity != $destinationCity[0]) ?  "{$startingCity} to {$destinationCity[0]} will be transferred by {$outstationVehicle} and " : '');

    // Prepare the data to send in the API request
    $data = [
        "model" => "gpt-3.5-turbo", // GPT-3.5-turbo model
        "messages" => [
            [
                "role" => "system",
                "content" => "You are an assistant that only responds in JSON format. Any answer provided should be a valid JSON object or array.  You are an expert travel planner with more than 10 years of experience in Tour Package Planning. You work for {$travelCompanyName}"
            ],
            [
                "role" => "user",
                "content" => "Create a detailed {$packageName} package itinerary where travel style is {$travelStyle}. Prepare itinerary for {$destinationsAndDurations}. Day 1 tour will be starting from {$startingCity} and tour will be end at {$endCity}. {$localSighseeignVehicleUpdated}local sightseeing will be done by {$localSighseeignVehicle}.
                - Format the output in JSON format with:
                {
                    'overview: [
                        'intro': {{return_paragraph_overview_of_{$destinationCity[0]}_{$travelStyle}_tour_package_intinenary}}
                        'inclusion': {{inclusion_of_{$destinationCity[0]}_{$travelStyle}_tour_package_intinenary}}
                        'exclusion': {{exclusion_of_{$destinationCity[0]}_{$travelStyle}_tour_package_intinenary}}
                    ]
                    'durations':[
                        {
                            'day': {{day_number}} - {{day_title}},
                            'details': {{detailed_paragraph_for_the_day}}
                        }
                    ]
                }
Ensure each day has at least a 500 word paragraph describing the day's activities. Make sure the JSON is valid and well-formed."
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
}
