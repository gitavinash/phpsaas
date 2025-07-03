<?php

require_once LOGIN_CHECK;

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read raw POST data (JSON)
    $json = file_get_contents('php://input');

    // Decode JSON into PHP array
    $data = json_decode($json, true); // true = associative array

    // Access the values
    $id = isset($data['id']) ? $data['id'] : null;
    $content = isset($data['content']) ? $data['content'] : [];

    preg_match('/\d+/', $content['price'], $matches);

    $itineraryPrice = isset($matches[0]) ? $matches[0] : null;

    // Check if the required fields are present

    $requiredFields = ['id', 'title', 'itinerary', 'itineraryPrice'];
    $missingFields = [];

    foreach ($requiredFields as $field) {
        if (!isset($$field) && !isset($content[$field])) {
            $missingFields[] = $field;
        }
    }

    if (!empty($missingFields)) {
        $httpStatus = 400;
        $msg = "Missing required fields: " . implode(', ', $missingFields);
        $status = $msg;
    } else {
        // check privious itinerary

        if ($checkItinerary = mysqli_query($connection, "SELECT quotation_id as itineraryParentId FROM " . TABLE_PREFIX . "quotation WHERE quotation_ref_id='{$data['refId']}' AND quotation_ref_type='{$data['refType']}' AND quotation_type_id={$id} AND quotation_type='tour_package' AND quotation_site_id=" . SITE_ID . " ORDER BY quotation_id ASC LIMIT 1")) {
            if (mysqli_num_rows($checkItinerary) > 0) {
                extract(mysqli_fetch_assoc($checkItinerary));
            } else {
                $itineraryParentId = 0;
            }
        }

        $itineraryDescription = json_encode($content);

        if ($saveQuotation = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "quotation(quotation_parent_id, quotation_ref_id, quotation_ref_type, quotation_type_id, quotation_type, quotation_site_id, quotation_by, quotation_mrp, quotation_price, quotation_description, quotation_status, quotation_time) VALUES({$itineraryParentId}, '{$data['refId']}', '{$data['refType']}', {$id}, 'tour_package', " . SITE_ID . ", " . USER_ID . ", {$data['mrp']}, {$itineraryPrice}, '{$itineraryDescription}', '0', NOW())")) {
            $quotationId = mysqli_insert_id($connection);

            if ($updateOldQuotation = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "quotation SET quotation_status='2' WHERE quotation_id!='{$quotationId}' AND quotation_ref_id='{$data['refId']}' AND quotation_ref_type='{$data['refType']}' AND quotation_type_id={$id} AND quotation_type='tour_package' AND quotation_site_id=" . SITE_ID)) {
                $httpStatus = 201;
                $msg = "Itinerary published successfully";
                $status = "success";
            } else {
                $httpStatus = 500;
                $msg = "Failed to update old itinerary";
                $status = "Failed to update old itinerary";
            }
        } else {
            $httpStatus = 500;
            $msg = "Failed to save itinerary";
            $status = "Failed to save itinerary";
        }
    }

    // Example: Validate and Save
    if ($id && !empty($content)) {
    } else {
        $httpStatus = 400;
        $msg = "Invalid data received";
        $status = "Invalid data received";
    }
}

$result = array(
    'status' => $status,
    'msg' => $msg,
    'itineraryId' => isset($quotationId) ? $quotationId : '',
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);