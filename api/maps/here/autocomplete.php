<?php

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($searchQuery)) {

        $curl = curl_init();

        $countryId = ((isset($countryId)) ? $companyId : 'IND');

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://autocomplete.search.hereapi.com/v1/autocomplete?q={$searchQuery}&in=countryCode%3A{$countryId}&apiKey=" . HERE_MAPS_KEY,
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
        $locationList = array();
        curl_close($curl);

        // Check if the items array exists
        if (isset($responseData['items']) && is_array($responseData['items'])) {
            $i = 0;
            foreach ($responseData['items'] as $item) {
                // Check if address and label exist
                if (isset($item['address']['label'])) {
                    $locationList[] = ((isset($item['address']['label'])) ? $item['address']['label'] : '');
                    // $locationList[$i]['location'] = ((isset($item['address']['label'])) ? $item['address']['label'] : '');
                    // $locationList[$i]['city'] = ((isset($item['address']['city'])) ? $item['address']['city'] : '');
                    // $locationList[$i]['district'] = ((isset($item['address']['county'])) ? $item['address']['county'] : '');
                    // $locationList[$i]['state'] = ((isset($item['address']['state'])) ? $item['address']['state'] : '');
                    // $locationList[$i]['country'] = ((isset($item['address']['countryName'])) ? $item['address']['countryName'] : '');
                    // $locationList[$i]['pincode'] = ((isset($item['address']['postalCode'])) ? $item['address']['postalCode'] : '');
                }
                $i++;
            }
            $httpStatus = 200;
            $status = 'success';
        } else {
            $httpStatus = 204;
            $status = 'warning';
            $locationList[] = $responseData;
        }

        // $locationList = json_decode($locationList, TRUE);
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'locationList' => ((isset($locationList)) ? $locationList : []),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
