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
        // Prepare the data to send in the API request
        "model" => "gpt-4o-mini",
        "response_format" => [
            "type" => "json_schema",
            "json_schema" => [
                "name" => "itinerary",
                "strict" => true, // disallow keys not in schema
                "schema" => [
                    "type" => "object",
                    "additionalProperties" => false,
                    "properties" => [
                        "overview" => [
                            "type" => "object",
                            "additionalProperties" => false,
                            "properties" => [
                                "intro" => ["type" => "string"],
                                "inclusion" => ["type" => "string"],
                                "exclusion" => ["type" => "string"]
                            ],
                            "required" => ["intro", "inclusion", "exclusion"]
                        ],
                        "durations" => [
                            "type" => "array",
                            "minItems" => 1,
                            "items" => [
                                "type" => "object",
                                "additionalProperties" => false,
                                "properties" => [
                                    "day" => ["type" => "string"],
                                    "details" => ["type" => "string"]
                                ],
                                "required" => ["day", "details"]
                            ]
                        ]
                    ],
                    "required" => ["overview", "durations"]
                ]
            ]
        ],
        "messages" => [
            [
                "role" => "system",
                "content" => "You must respond with a SINGLE JSON object that strictly matches the provided JSON Schema. All values MUST be plain text strings: no HTML, no Markdown, no emojis, no code fences. Do not include any text before or after the JSON. You are an expert travel planner with 10+ years of experience working for {$travelCompanyName}."
            ],
            [
                "role" => "user",
                "content" => "Create a detailed \"{$packageName}\" package itinerary where travel style is \"{$travelStyle}\".\nDestinations and nights: {$destinationsAndDurations}\nDay 1 will start from \"{$startingCity}\" and the tour will end at \"{$endCity}\". {$localSighseeignVehicleUpdated}local sightseeing will be done by {$localSighseeignVehicle}. Start timings are {$startTimings}.\n\nPopulate the schema fields as follows:\n- overview.intro: A compelling plain-text paragraph introducing a {$destinationCity[0]} {$travelStyle} tour that spans all listed destinations.\n- overview.inclusion: A comma-separated plain-text list of inclusions written as a sentence/phrases (still just a single string value).\n- overview.exclusion: A comma-separated plain-text list of exclusions written as a sentence/phrases (single string value).\n- durations: One item per calendar day (sum of all nights + 1). For each item:\n  - day: \"Day N - <title>\"\n  - details: ~500 words of plain text describing activities for that day (informative, professional tone).\n\nReturn ONLY the JSON object that matches the schema; do not add extra keys."
            ]
        ],
        "max_tokens" => 3500,
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