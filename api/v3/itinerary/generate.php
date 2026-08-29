<?php
/**
 * V3 Smart AI Itinerary Generator API
 * Endpoint: /api/v3/itinerary/generate.php
 * Method: POST / GET
 */

header("Content-Type: application/json; charset=utf-8");

if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__, 3);
}
if (!isset($connection)) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/config.php')) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
    }
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/lib/modules/connection.php')) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/modules/connection.php';
    }
}

if (!defined('LOGIN_CHECK')) {
    define('LOGIN_CHECK', $_SERVER['DOCUMENT_ROOT'] . '/lib/session/index.php');
}
if (file_exists(LOGIN_CHECK)) {
    @require_once LOGIN_CHECK;
}

$travelCompanyName = defined('SITE_NAME') ? SITE_NAME : 'Tour Planner';

// Extract raw POST payload (supports JSON body, form-data, query parameters, and _json_payload)
$rawInput = file_get_contents('php://input');
$jsonPayload = json_decode($rawInput, true);

$payload = [];
if (!empty($_POST)) {
    $payload = array_merge($payload, $_POST);
}
if (!empty($_GET)) {
    $payload = array_merge($payload, $_GET);
}
if (!empty($_REQUEST)) {
    $payload = array_merge($payload, $_REQUEST);
}
if (is_array($jsonPayload) && !empty($jsonPayload)) {
    $payload = array_merge($payload, $jsonPayload);
}
if (!empty($payload['_json_payload'])) {
    $decodedSub = json_decode($payload['_json_payload'], true);
    if (is_array($decodedSub)) {
        $payload = array_merge($payload, $decodedSub);
    }
}

// 1. Package Name
$packageName = trim((string)($payload['packageName'] ?? ($payload['name'] ?? ($payload['package_name'] ?? ''))));

// 2. Travel Style
$travelStyle = trim((string)($payload['travelStyle'] ?? ($payload['style'] ?? ($payload['travel_style'] ?? 'Cultural & Heritage'))));

// 3. Starting City & End City
$startingCity = trim((string)($payload['startingCity'] ?? ($payload['startCity'] ?? ($payload['start_city'] ?? ($payload['pickupCity'] ?? '')))));
$endCity = trim((string)($payload['endCity'] ?? ($payload['end_city'] ?? ($payload['dropCity'] ?? $startingCity))));

// 4. Vehicles
$outstationVehicle = trim((string)($payload['outstationVehicle'] ?? ($payload['outstation_vehicle'] ?? 'Sedan')));
$localSightseeingVehicle = trim((string)($payload['localSighseeignVehicle'] ?? ($payload['localSightseeingVehicle'] ?? ($payload['local_sightseeing_vehicle'] ?? 'Sedan'))));

// 5. Destination Stops
$rawDests = $payload['destination'] ?? ($payload['destinationCity'] ?? ($payload['destinations'] ?? ($payload['destination_city'] ?? ($payload['location'] ?? []))));
$destinationCity = [];

if (is_array($rawDests)) {
    foreach ($rawDests as $item) {
        if (is_array($item)) {
            $c = trim((string)($item['city'] ?? ($item['name'] ?? ($item['text'] ?? ''))));
            if (!empty($c)) $destinationCity[] = $c;
        } elseif (is_string($item) || is_numeric($item)) {
            $itemStr = trim((string)$item);
            if (!empty($itemStr)) {
                $parts = preg_split('/[,_]+/', $itemStr);
                foreach ($parts as $p) {
                    $p = trim($p);
                    if (!empty($p)) $destinationCity[] = $p;
                }
            }
        }
    }
} elseif (is_string($rawDests) && !empty(trim($rawDests))) {
    $parts = preg_split('/[,_]+/', trim($rawDests));
    foreach ($parts as $p) {
        $p = trim($p);
        if (!empty($p)) $destinationCity[] = $p;
    }
}

// 6. Stay Durations
$rawDurs = $payload['duration'] ?? ($payload['tripDuration'] ?? ($payload['durations'] ?? ($payload['trip_duration'] ?? [])));
$tripDuration = [];

if (is_array($rawDurs)) {
    foreach ($rawDurs as $item) {
        if (is_array($item)) {
            $n = intval($item['nights'] ?? ($item['duration'] ?? 0));
            if ($n > 0) $tripDuration[] = $n;
        } else {
            $n = intval($item);
            if ($n > 0) $tripDuration[] = $n;
        }
    }
} elseif (is_string($rawDurs) && !empty(trim($rawDurs))) {
    $parts = preg_split('/[,_]+/', trim($rawDurs));
    foreach ($parts as $p) {
        $n = intval(trim($p));
        if ($n > 0) $tripDuration[] = $n;
    }
} elseif (is_numeric($rawDurs)) {
    $n = intval($rawDurs);
    if ($n > 0) $tripDuration[] = $n;
}

// Fallback validations and auto-completions
if (empty($destinationCity)) {
    if (!empty($startingCity)) {
        $destinationCity[] = $startingCity;
    } else {
        $destinationCity[] = "Scenic Destination";
    }
}

if (empty($tripDuration)) {
    $tripDuration = array_fill(0, count($destinationCity), 2);
}

// Ensure duration matches destination count
while (count($tripDuration) < count($destinationCity)) {
    $tripDuration[] = 2;
}

if (empty($packageName)) {
    $firstDest = $destinationCity[0] ?? 'Holiday';
    $packageName = "{$firstDest} {$travelStyle} Holiday Package";
}

if (empty($startingCity)) {
    $startingCity = $destinationCity[0] ?? "Delhi";
}
if (empty($endCity)) {
    $endCity = $startingCity;
}

$totalNights = array_sum($tripDuration);
if ($totalNights < 1) {
    $totalNights = 1;
}
$totalDays = $totalNights + 1;

$chatGPTKey = defined('CHAT_GPT_API') ? CHAT_GPT_API : '';
$tripData = null;

if (!empty($chatGPTKey)) {
    $endpoint = "https://api.openai.com/v1/chat/completions";
    $destinationsAndDurations = "";
    for ($i = 0; $i < sizeof($destinationCity); $i++) {
        $nightCount = isset($tripDuration[$i]) ? $tripDuration[$i] : 1;
        $destinationsAndDurations .= "{$destinationCity[$i]} with {$nightCount} nights, ";
    }
    $destinationsAndDurations = rtrim($destinationsAndDurations, ", ");

    $startTimings = ($outstationVehicle == 'volvo') ? 'in evening' : 'in morning';
    $localSightseeingVehicleUpdated = ($startingCity != $destinationCity[0])
        ? "{$startingCity} to {$destinationCity[0]} will be transferred by {$outstationVehicle} and "
        : '';

    $schema = [
        "name" => "itinerary",
        "strict" => true,
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
                    "minItems" => $totalDays,
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
    ];

    $promptContent = "Create a detailed \"{$packageName}\" tour itinerary where travel style is \"{$travelStyle}\".\n";
    $promptContent .= "Destinations and stay duration: {$destinationsAndDurations}.\n";
    $promptContent .= "Total duration: {$totalNights} Nights / {$totalDays} Days.\n";
    $promptContent .= "Tour starts from \"{$startingCity}\" and concludes at \"{$endCity}\". {$localSightseeingVehicleUpdated}local sightseeing will be arranged with {$localSightseeingVehicle}. Departure timings: {$startTimings}.\n\n";
    $promptContent .= "Populate the JSON fields as follows:\n";
    $promptContent .= "- overview.intro: A compelling, informative 2-3 paragraph overview describing the highlights, scenery, and experience of this {$travelStyle} journey across " . implode(', ', $destinationCity) . ".\n";
    $promptContent .= "- overview.inclusion: A clear list of inclusions (e.g. Hotel accommodation on twin sharing, Daily breakfast & dinner, Dedicated transfers by {$outstationVehicle} & {$localSightseeingVehicle}, All toll tax, state permits, parking fees, and driver allowances).\n";
    $promptContent .= "- overview.exclusion: A clear list of exclusions (e.g. Flight / train tickets, Monument entrance & camera fees, Adventure activity tickets, Personal expenses & laundry, Travel insurance).\n";
    $promptContent .= "- durations: Exactly {$totalDays} daily itinerary objects. For each item:\n";
    $promptContent .= "  - day: \"Day N - <Day Title & Location>\"\n";
    $promptContent .= "  - details: An engaging 200-350 word paragraph describing the daily route, sightseeing stops, meals, and evening leisure.";

    $data = [
        "model" => "gpt-4o-mini",
        "response_format" => [
            "type" => "json_schema",
            "json_schema" => $schema
        ],
        "messages" => [
            [
                "role" => "system",
                "content" => "You are an elite travel planner with over 15 years of itinerary creation experience working for {$travelCompanyName}. Respond ONLY with a valid JSON object matching the schema."
            ],
            [
                "role" => "user",
                "content" => $promptContent
            ]
        ],
        "max_tokens" => 3500,
        "temperature" => 0.7
    ];

    $headers = [
        "Content-Type: application/json",
        "Authorization: Bearer " . $chatGPTKey
    ];

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_TIMEOUT, 40);

    $response = curl_exec($ch);
    $curlErr = curl_error($ch);
    curl_close($ch);

    if (!$curlErr && $response) {
        $result = json_decode($response, true);
        if (isset($result['choices'][0]['message']['content'])) {
            $parsed = json_decode($result['choices'][0]['message']['content'], true);
            if (is_array($parsed) && !empty($parsed['durations'])) {
                $tripData = $parsed;
            }
        }
    }
}

// Fallback intelligent generator if OpenAI was unavailable or failed
if (empty($tripData)) {
    $citiesText = implode(' & ', $destinationCity);
    $intro = "Welcome to your {$travelStyle} journey through {$citiesText}! This {$totalNights} Nights / {$totalDays} Days tour package is carefully crafted to offer an unforgettable travel experience. Beginning from {$startingCity} and concluding at {$endCity}, enjoy comfortable transport by {$outstationVehicle} and dedicated local sightseeing in a private {$localSightseeingVehicle}. Explore iconic landmarks, breathtaking natural vistas, rich cultural heritage, and authentic local cuisine.";

    $inclusions = "Accommodation in selected category hotel, Daily Breakfast & Dinner, Dedicated private vehicle ({$outstationVehicle} / {$localSightseeingVehicle}) for all transfers and sightseeing, Fuel charges, Toll tax, State permits, Parking fees and Driver allowance, 24x7 On-tour assistance from our operations team.";

    $exclusions = "Airfare / Train fare to {$startingCity} and from {$endCity}, Monument entrance tickets, Camera fees and Activity charges (Ropeway, Rafting, Trekking), Personal expenses such as laundry, telephone calls, room heater and alcoholic beverages, Optional excursions and any meals not specified, 5% GST and Travel Insurance.";

    $durations = [];
    $currentDay = 1;

    // Day 1: Arrival & Transfer
    $firstDest = $destinationCity[0] ?? $startingCity;
    $durations[] = [
        'day' => "Day 1 - Arrival at {$startingCity} & Transfer to {$firstDest}",
        'details' => "Arrive at {$startingCity} where our representative will warmly welcome you. Board your comfortable {$outstationVehicle} as you begin your scenic road journey towards {$firstDest}. Marvel at the changing landscapes and lush vistas along the route. Upon arrival in {$firstDest}, check into your hotel and take time to freshen up. In the evening, enjoy a relaxing stroll through the local market or surrounding sights. Relish a delicious dinner and enjoy an overnight stay in {$firstDest}."
    ];
    $currentDay++;

    // Subsequent Days for destinations
    for ($d = 0; $d < count($destinationCity); $d++) {
        $dest = $destinationCity[$d];
        $nights = $tripDuration[$d] ?? 1;

        for ($n = 0; $n < $nights; $n++) {
            if ($currentDay > $totalDays - 1) break;
            $durations[] = [
                'day' => "Day {$currentDay} - {$dest} Exploration & Local Sightseeing",
                'details' => "Wake up to a crisp morning and savor a hearty breakfast at the hotel. Today, set out in your private {$localSightseeingVehicle} to explore the key tourist attractions, heritage sites, and panoramic viewpoints in and around {$dest}. Experience the vibrant local culture, capture memorable photographs, and indulge in shopping for handicrafts. Return to the hotel in the late afternoon. Unwind with dinner and a comfortable overnight stay."
            ];
            $currentDay++;
        }
    }

    // Final Departure Day
    while ($currentDay <= $totalDays) {
        $lastDest = end($destinationCity) ?: $startingCity;
        $durations[] = [
            'day' => "Day {$currentDay} - {$lastDest} to {$endCity} & Tour Departure",
            'details' => "Enjoy a delightful breakfast at your hotel before completing check-out formalities. Board your vehicle for your return transfer to {$endCity}. Reflect on the wonderful memories, scenic wonders, and new experiences collected throughout your trip. You will be dropped off at {$endCity} airport or railway station for your onward journey. Tour concludes with happy memories."
        ];
        $currentDay++;
    }

    $tripData = [
        'overview' => [
            'intro' => $intro,
            'inclusion' => $inclusions,
            'exclusion' => $exclusions
        ],
        'durations' => $durations
    ];
}

// Structure final payload
$responseData = [
    'packageName' => $packageName,
    'travelStyle' => $travelStyle,
    'startingCity' => $startingCity,
    'endCity' => $endCity,
    'destinationCity' => $destinationCity,
    'tripDuration' => $tripDuration,
    'totalNights' => $totalNights,
    'totalDays' => $totalDays,
    'outstationVehicle' => $outstationVehicle,
    'localSightseeingVehicle' => $localSightseeingVehicle,
    'overview' => $tripData['overview'],
    'durations' => $tripData['durations'],
    'hotel' => '3_star_hotel'
];

echo json_encode([
    'status' => 'success',
    'msg' => 'Itinerary generated successfully.',
    'data' => $responseData
]);
