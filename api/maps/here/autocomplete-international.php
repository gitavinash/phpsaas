<?php

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';
$locationList = [];

$searchQuery = isset($_GET['searchQuery']) ? $_GET['searchQuery'] : null;

if ($_SERVER['REQUEST_METHOD'] == 'GET' && $searchQuery) {

    $curl = curl_init();
    $searchQuery = urlencode(strtolower($searchQuery));

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://autocomplete.search.hereapi.com/v1/autocomplete?q={$searchQuery}&lang=en-IN&apiKey=" . HERE_MAPS_KEY,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));


    $responseData = curl_exec($curl);
    $responseData = json_decode($responseData, true);
    curl_close($curl);

    // Uncomment for debugging:
    // print_r($responseData);

    if (isset($responseData['items']) && is_array($responseData['items'])) {
        foreach ($responseData['items'] as $item) {
            $locationList[] = [
                'location' => $item['address']['label'] ?? '',
                'city' => $item['address']['city'] ?? '',
                'state' => $item['address']['state'] ?? '',
                'country' => $item['address']['countryName'] ?? '',
                'address' => implode(', ', array_filter([
                    $item['address']['city'] ?? '',
                    $item['address']['state'] ?? '',
                    $item['address']['countryName'] ?? '',
                ]))
            ];
        }
        $httpStatus = 200;
        $status = 'success';
    } else {
        $httpStatus = 400;
        $status = 'warning';
        $locationList = $responseData;
    }
}

$result = [
    'status' => $status,
    'locationList' => $locationList
];

http_response_code($httpStatus);
echo json_encode($result, true);