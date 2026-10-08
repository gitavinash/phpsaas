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

require_once __DIR__ . '/itinerary_engine.php';

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

// 6. Included Meals (Optional)
$rawMeals = $payload['meals'] ?? ($payload['includedMeals'] ?? ($payload['meal'] ?? ($payload['mealPlan'] ?? [])));
$includedMeals = [];
if (is_array($rawMeals)) {
    foreach ($rawMeals as $m) {
        $mStr = trim((string)$m);
        if (!empty($mStr) && strtolower($mStr) !== 'none') {
            $includedMeals[] = $mStr;
        }
    }
} elseif (is_string($rawMeals) && !empty(trim($rawMeals)) && strtolower(trim($rawMeals)) !== 'none') {
    $parts = preg_split('/[,&+]+/', trim($rawMeals));
    foreach ($parts as $p) {
        $p = trim($p);
        if (!empty($p)) $includedMeals[] = $p;
    }
}
$includedMeals = array_values(array_unique($includedMeals));

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

// 7. Day-Wise Inputs (Night Stays & User Notes from Day Planner)
$rawDays = $payload['days'] ?? ($payload['daySchedule'] ?? []);
$userDaysInput = [];

if (is_array($rawDays)) {
    foreach ($rawDays as $idx => $d) {
        if (is_array($d)) {
            $stayType = strtolower(trim((string)($d['stayType'] ?? '')));
            $stayVal = trim((string)($d['stayCity'] ?? ($d['stay'] ?? ($d['city'] ?? ''))));
            if ($stayType === 'transit' || strtolower($stayVal) === 'transit' || strtolower($stayVal) === 'tour departure') {
                $stayVal = 'Transit';
            }
            $userDaysInput[] = [
                'day' => intval($d['day'] ?? ($idx + 1)),
                'stay' => $stayVal,
                'stayType' => ($stayVal === 'Transit' ? 'transit' : 'in_hotel'),
                'notes' => trim((string)($d['notes'] ?? ($d['details'] ?? ($d['highlights'] ?? '')))),
                'title' => trim((string)($d['title'] ?? ''))
            ];
        }
    }
} elseif (!empty($payload['dayStay']) && is_array($payload['dayStay'])) {
    foreach ($payload['dayStay'] as $idx => $st) {
        $cleanSt = trim((string)$st);
        if (strtolower($cleanSt) === 'transit' || strtolower($cleanSt) === 'tour departure') {
            $cleanSt = 'Transit';
        }
        $userDaysInput[] = [
            'day' => $idx + 1,
            'stay' => $cleanSt,
            'stayType' => ($cleanSt === 'Transit' ? 'transit' : 'in_hotel'),
            'notes' => trim((string)($payload['dayNotes'][$idx] ?? '')),
            'title' => trim((string)($payload['dayTitle'][$idx] ?? ''))
        ];
    }
}

// Compute default night stays sequence across total days
$defaultNightStays = [];
for ($d = 0; $d < count($destinationCity); $d++) {
    $c = $destinationCity[$d];
    $n = $tripDuration[$d] ?? 1;
    for ($k = 0; $k < $n; $k++) {
        $defaultNightStays[] = $c;
    }
}

// Build normalized day plan for prompt and fallback
$normalizedDayPlan = [];
for ($i = 0; $i < $totalDays; $i++) {
    $dayNum = $i + 1;
    $userInput = $userDaysInput[$i] ?? [];
    $stayCity = !empty($userInput['stay']) ? $userInput['stay'] : ($defaultNightStays[$i] ?? ($i === $totalDays - 1 ? 'Transit' : ($destinationCity[0] ?? $startingCity)));
    $userNotes = !empty($userInput['notes']) ? $userInput['notes'] : '';
    $userTitle = !empty($userInput['title']) ? $userInput['title'] : '';

    $normalizedDayPlan[] = [
        'day' => $dayNum,
        'stay' => $stayCity,
        'notes' => $userNotes,
        'title' => $userTitle
    ];
}

$chatGPTKey = defined('CHAT_GPT_API') ? CHAT_GPT_API : '';
$tripData = null;

if (!empty($chatGPTKey)) {
    $endpoint = "https://api.openai.com/v1/chat/completions";
    $destinationsAndDurations = "";
    for ($i = 0; $i < sizeof($destinationCity); $i++) {
        $nightCount = isset($tripDuration[$i]) ? $tripDuration[$i] : 1;
        $destinationsAndDurations .= "{$destinationCity[$i]} ({$nightCount} nights), ";
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
                            "stay" => ["type" => "string"],
                            "details" => ["type" => "string"]
                        ],
                        "required" => ["day", "stay", "details"]
                    ]
                ]
            ],
            "required" => ["overview", "durations"]
        ]
    ];

    $dayWisePromptLines = [];
    foreach ($normalizedDayPlan as $dp) {
        $isTransit = ($dp['stay'] === 'Transit' || strtolower($dp['stay']) === 'transit');
        $stayDesc = $isTransit ? "In Transit (No overnight hotel stay)" : "Overnight Hotel Stay in \"{$dp['stay']}\"";
        $line = "- Day {$dp['day']}: {$stayDesc}";
        if (!empty($dp['title'])) {
            $line .= ", Target Title: \"{$dp['title']}\"";
        }
        if (!empty($dp['notes'])) {
            $line .= ", User Highlights & Focus: \"{$dp['notes']}\"";
        }
        $dayWisePromptLines[] = $line;
    }
    $dayWisePromptText = implode("\n", $dayWisePromptLines);

    $promptContent = "Create a bespoke, vivid, and highly realistic \"{$packageName}\" tour itinerary.\n";
    $promptContent .= "Travel Style: \"{$travelStyle}\". Total Duration: {$totalNights} Nights / {$totalDays} Days.\n";
    $promptContent .= "Route: Starting from \"{$startingCity}\", journeying through {$destinationsAndDurations}, and concluding at \"{$endCity}\".\n";
    $promptContent .= "Fleet: {$localSightseeingVehicleUpdated}local sightseeing in private {$localSightseeingVehicle}. Departure: {$startTimings}.\n";
    if (!empty($includedMeals)) {
        $mealsListStr = implode(', ', $includedMeals);
        $promptContent .= "Meal Plan: Included Daily Meals - {$mealsListStr}.\n";
    } else {
        $promptContent .= "Meal Plan: Room Only (No complimentary meals included).\n";
    }
    $promptContent .= "\n### Planned Day-by-Day Structure & Night Stays:\n{$dayWisePromptText}\n\n";
    $promptContent .= "### STRICT CONTENT & WRITING GUIDELINES:\n";
    $promptContent .= "1. NATURAL & ORGANIC PROSE: Write like a seasoned travel journalist. Completely avoid repetitive, robotic sentence structures across days. STRICTLY DO NOT start each day with phrases like \"Wake up to a crisp morning...\", \"Wake up early and savor a hearty breakfast...\", or \"After a delicious breakfast...\". Vary sentence openings, pace, and descriptive rhythm dynamically.\n";
    $promptContent .= "2. REALISTIC PACING: Day 1 should focus on arrival excitement, greeting, and scenic transit. Middle days should dive into authentic local culture, architecture, viewpoints, local food, and leisure. Excursion days highlight landscapes and activities. The final day focuses on gentle farewell, souvenir hunting, and seamless departure.\n";
    $promptContent .= "3. ACCURACY TO USER INPUTS: If specific user highlights or focus notes are provided for a day, explicitly feature and expand those exact attractions and experiences naturally in that day's narrative and title.\n";
    $promptContent .= "4. NIGHT STAY HARMONY: Each day's \"stay\" field must contain either the assigned hotel city name (e.g. \"Srinagar\") or \"Transit\" if marked as Transit. If stay is \"Transit\", conclude the day naturally mentioning onward journey / overnight transit without hotel check-in. If stay is in a hotel, conclude mentioning evening relaxation, dinner, and comfortable overnight stay in that city.\n";
    $promptContent .= "5. DURATION SCHEMA: Exactly {$totalDays} items in `durations` array. Each item must contain:\n";
    $promptContent .= "   - `day`: \"Day N - <Evocative Landmark / Experience Title>\"\n";
    $promptContent .= "   - `stay`: City name where traveler stays the night, or \"Transit\"\n";
    $promptContent .= "   - `details`: Engaging 150-250 word authentic narrative covering the day's progression from morning to night.\n";
    if (!empty($includedMeals)) {
        $mealsListStr = implode(' & ', $includedMeals);
        $promptContent .= "6. OVERVIEW: `overview.intro` must be an inspiring 2-paragraph journey introduction; `overview.inclusion` must clearly list \"Daily {$mealsListStr}\" alongside hotel stays and transport; `overview.exclusion` must list standard non-covered items.\n";
    } else {
        $promptContent .= "6. OVERVIEW: `overview.intro` must be an inspiring 2-paragraph journey introduction; `overview.inclusion` must cover accommodation and transport; `overview.exclusion` must state \"Lunch, dinner and personal food expenses (Room Only plan)\".\n";
    }
    $promptContent .= "7. ZERO DUPLICATION GUARANTEE: Every day title MUST be unique and specifically describe the planned activity and landmarks (NEVER repeat generic titles like 'Highlights & Local Experiences' across multiple days). Every day overview narrative MUST be completely unique with authentic day-by-day itineraries. NEVER repeat boilerplate phrases or closing sentences across days.\n";

    $data = [
        "model" => "gpt-4o-mini",
        "response_format" => [
            "type" => "json_schema",
            "json_schema" => $schema
        ],
        "messages" => [
            [
                "role" => "system",
                "content" => "You are an elite master travel curator for {$travelCompanyName}. You craft distinctive, authentic, and evocative day-by-day itineraries tailored to specific destinations without repetitive templates. Output strictly valid JSON matching the schema."
            ],
            [
                "role" => "user",
                "content" => $promptContent
            ]
        ],
        "max_tokens" => 3800,
        "temperature" => 0.75
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
    $citiesFormatted = array_map('v3_format_city_display', $destinationCity);
    $citiesText = implode(' & ', $citiesFormatted);
    $startCityFormatted = v3_format_city_display($startingCity);
    $endCityFormatted = v3_format_city_display($endCity);

    $intro = "Welcome to your {$travelStyle} journey through {$citiesText}! This {$totalNights} Nights / {$totalDays} Days holiday package has been meticulously planned to deliver an authentic, immersive, and unforgettable travel experience. Beginning from {$startCityFormatted} and concluding at {$endCityFormatted}, enjoy comfortable travel aboard your dedicated {$outstationVehicle} and private local sightseeing in a {$localSightseeingVehicle}. Discover majestic landscapes, iconic monuments, vibrant markets, and flavorful local culinary heritage.";

    $mealInclusionText = !empty($includedMeals) ? "Daily " . implode(' & ', $includedMeals) . ", " : "";
    $mealExclusionText = !empty($includedMeals) ? "Meals other than specified (" . implode(', ', $includedMeals) . "), " : "All meals (Breakfast, Lunch & Dinner) unless explicitly arranged, ";

    $inclusions = "Accommodation in selected category hotel, {$mealInclusionText}Dedicated private vehicle ({$outstationVehicle} / {$localSightseeingVehicle}) for all transfers and sightseeing, Fuel charges, Toll tax, State permits, Parking fees and Driver allowance, 24x7 On-tour assistance from our operations team.";

    $exclusions = "Airfare / Train fare to {$startCityFormatted} and from {$endCityFormatted}, {$mealExclusionText}Monument entrance tickets, Camera fees and Activity charges (Ropeway, Rafting, Trekking), Personal expenses such as laundry, telephone calls, room heater and alcoholic beverages, Optional excursions, 5% GST and Travel Insurance.";

    $durations = [];
    $assignedTracker = [];

    foreach ($normalizedDayPlan as $idx => $dayItem) {
        $dayNum = $dayItem['day'];
        $stayCity = $dayItem['stay'];
        $notes = $dayItem['notes'];
        $customTitle = $dayItem['title'];

        // Determine destination context for this day
        $isTransit = ($stayCity === 'Transit' || strtolower($stayCity) === 'transit');
        if (!$isTransit && !empty($stayCity)) {
            $destCity = $stayCity;
        } else {
            $destIdx = min(count($destinationCity) - 1, intval($idx / max(1, ceil($totalDays / max(1, count($destinationCity))))));
            $destCity = $destinationCity[$destIdx] ?? $startingCity;
        }

        $activeVehicle = ($dayNum === 1 || $dayNum === $totalDays) ? $outstationVehicle : $localSightseeingVehicle;
        $activeMealPlan = !empty($includedMeals) ? implode(' & ', $includedMeals) : 'Room Only';

        $durations[] = v3_build_smart_itinerary_day(
            $dayNum,
            $totalDays,
            $stayCity,
            $destCity,
            $notes,
            $customTitle,
            $activeVehicle,
            $activeMealPlan,
            $idx,
            $assignedTracker
        );
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

// Ensure each duration item in tripData contains 'stay'
if (isset($tripData['durations']) && is_array($tripData['durations'])) {
    foreach ($tripData['durations'] as $idx => &$dItem) {
        if (empty($dItem['stay'])) {
            $dItem['stay'] = $normalizedDayPlan[$idx]['stay'] ?? ($defaultNightStays[$idx] ?? 'Tour Stay');
        }
    }
    unset($dItem);
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
    'includedMeals' => $includedMeals,
    'mealPlan' => !empty($includedMeals) ? implode(' & ', $includedMeals) : 'Room Only',
    'overview' => $tripData['overview'],
    'durations' => $tripData['durations'],
    'hotel' => '3_star_hotel'
];

echo json_encode([
    'status' => 'success',
    'msg' => 'Itinerary generated successfully.',
    'data' => $responseData
]);

