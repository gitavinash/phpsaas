<?php

require_once LOGIN_CHECK;

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['priceType'])) {
        $priceType = $_POST['priceType'];
        $quoteType = ucwords($priceType);
    } else {
        $quoteType = '';
        $msg = "Price type is missing.";
    }

    $requiredFields = [
        "sessionStart{$quoteType}Sharing",
        "sessionEnd{$quoteType}Sharing",
        "netCostAdult{$quoteType}Sharing",
        "sellingPriceAdult{$quoteType}Sharing",
        "netCostChild{$quoteType}Sharing",
        "sellingPriceChild{$quoteType}Sharing",
        "tourPackageId",
    ];

    $payload = $_POST;

    $validRequest = true;

    foreach ($requiredFields as $field) {
        if (!isset($payload[$field]) || (is_array($payload[$field]) && empty($payload[$field])) || (!is_array($payload[$field]) && trim($payload[$field]) === '')) {
            $validRequest = false;
            $msg = "Field '{$field}' is missing or empty.";
            break;
        }
    }

    if ($validRequest) {

        mysqli_query($connection, "START TRANSACTION");

        // Delete previous records

        $packageId = base64_decode($tourPackageId);

        $deleteQuery = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "tour_package_price WHERE tour_package_price_parent_id='{$packageId}' AND tour_package_price_type='{$priceType}' AND tour_package_price_site_id=" . SITE_ID);

        $sessionStartKey = "sessionStart{$quoteType}Sharing";
        $sessionEndKey = "sessionEnd{$quoteType}Sharing";
        $netAdultPrice = "netCostAdult{$quoteType}Sharing";
        $mrpAdultPrice = "mrpAdult{$quoteType}Sharing";
        $sellingAdultPrice = "sellingPriceAdult{$quoteType}Sharing";
        $agentAdultPrice = "agentPriceAdult{$quoteType}Sharing";
        $netChildPrice = "netCostChild{$quoteType}Sharing";
        $mrpChildPrice = "mrpChild{$quoteType}Sharing";
        $sellingChildPrice = "sellingPriceChild{$quoteType}Sharing";
        $agentChildPrice = "agentPriceChild{$quoteType}Sharing";

        $queryValue = '';

        if (isset($payload[$sessionStartKey])) {
            for ($i = 0; $i < sizeof($payload[$sessionStartKey]); $i++) {
                $startDate = date('1997-m-d', strtotime($payload[$sessionStartKey][$i]));
                $endDate = date('1997-m-d', strtotime($payload[$sessionEndKey][$i]));

                $netAdultPriceValue = ((isset($payload[$netAdultPrice][$i])) ? $payload[$netAdultPrice][$i] : 0);
                $mrpAdultPriceValue = ((isset($payload[$mrpAdultPrice][$i])) ? $payload[$mrpAdultPrice][$i] : 0);
                $sellingAdultPriceValue = ((isset($payload[$sellingAdultPrice][$i])) ? $payload[$sellingAdultPrice][$i] : 0);
                $agentAdultPriceValue = ((isset($payload[$agentAdultPrice][$i])) ? $payload[$agentAdultPrice][$i] : 0);
                $netChildPriceValue = ((isset($payload[$netChildPrice][$i])) ? $payload[$netChildPrice][$i] : 0);
                $mrpChildPriceValue = ((isset($payload[$mrpChildPrice][$i])) ? $payload[$mrpChildPrice][$i] : 0);
                $sellingChildPriceValue = ((isset($payload[$sellingChildPrice][$i])) ? $payload[$sellingChildPrice][$i] : 0);
                $agentChildPriceValue = ((isset($payload[$agentChildPrice][$i])) ? $payload[$agentChildPrice][$i] : 0);

                $queryValue .= "('{$packageId}', " . SITE_ID . ", '" . USER_ID . "', '{$startDate}', '{$endDate}', '{$netAdultPriceValue}', '{$netChildPriceValue}', '{$mrpAdultPriceValue}', '{$mrpChildPriceValue}',  '{$sellingAdultPriceValue}', '{$sellingChildPriceValue}',  '{$agentAdultPriceValue}', '{$agentChildPriceValue}', '{$priceType}', 1, NOW()),";
            }
        }

        $insertQuery = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "tour_package_price(tour_package_price_parent_id, tour_package_price_site_id, tour_package_price_user_id, tour_package_price_date_start, tour_package_price_date_end, tour_package_price_net_adult, tour_package_price_net_child, tour_package_price_mrp_adult, tour_package_price_mrp_child, tour_package_price_selling_adult, tour_package_price_selling_child, tour_package_price_agent_adult, tour_package_price_agent_child, tour_package_price_type, tour_package_price_status, tour_package_price_created_at) VALUES" . rtrim($queryValue, ','));

        if ($deleteQuery && $insertQuery) {
            $httpStatus = 201;
            $status = 'success';
            $msg = 'Price updated successfully';
            mysqli_query($connection, "COMMIT");
        } else {
            $msg = ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something went wrong while updating the price');
            mysqli_query($connection, "ROLLBACK");
        }
    }
}

$result = array(
    'status' => $status,
    'msg' => $msg,
    'id' => base64_encode((isset($itineraryId) ? $itineraryId : 0)),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
