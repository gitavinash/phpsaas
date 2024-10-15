<?php

require_once LOGIN_CHECK;

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quoteType = ucwords($priceType);
    $requiredFields = [
        "sessionStart{$quoteType}Sharing",
        "sessionEnd{$quoteType}Sharing",
        "netCostAdult{$quoteType}Sharing",
        "mrpAdult{$quoteType}Sharing",
        "sellingPriceAdult{$quoteType}Sharing",
        "agentPriceAdult{$quoteType}Sharing",
        "netCostChild{$quoteType}Sharing",
        "mrpChild{$quoteType}Sharing",
        "sellingPriceChild{$quoteType}Sharing",
        "agentPriceChild{$quoteType}Sharing",
        "tourPackageId",
    ];

    $allValid = true; // Flag to check if all fields are valid

    foreach ($requiredFields as $field) {
        if (!isset($payload[$field]) || (is_array($payload[$field]) && empty($payload[$field])) || (!is_array($payload[$field]) && trim($payload[$field]) === '')) {
            $validRequest = false;
            $msg = "Field '{$field}' is missing or empty.";
        }
    }
    if (isset($validRequest)) {
        print_r($netCostAdultSingleSharing);
        print_r(${"netCostAdult" . $quoteType . "Sharing"});
    } else {
        $msg = 'Not valid request';
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
    'id' => base64_encode(((isset($itineraryId)) ? $itineraryId : 0)),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
