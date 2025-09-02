<?php

require_once LOGIN_CHECK;

$httpStatus = 500;
$status = 'warning';
$msg = 'Invalid request';
$quotationId = '';

// Only handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get raw POST data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true); // associative array

    // Extract and sanitize input
    $id = isset($data['id']) ? intval($data['id']) : null;
    $content = isset($data['content']) ? $data['content'] : [];

    // Extract price using regex
    preg_match('/\d+/', isset($content['price']) ? $content['price'] : '', $matches);
    $itineraryPrice = isset($matches[0]) ? intval($matches[0]) : null;

    // Required fields check
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
        // Escape user input
        $refId = mysqli_real_escape_string($connection, $data['refId']);
        $refType = mysqli_real_escape_string($connection, $data['refType']);
        $itineraryDescription = mysqli_real_escape_string($connection, json_encode($content));
        $mrp = isset($data['mrp']) ? floatval($data['mrp']) : 0;

        // Check previous itinerary
        $query = "
            SELECT quotation_id AS itineraryParentId 
            FROM " . TABLE_PREFIX . "quotation 
            WHERE quotation_ref_id = '{$refId}' 
              AND quotation_ref_type = '{$refType}' 
              AND quotation_type_id = {$id} 
              AND quotation_type = 'tour_package' 
              AND quotation_site_id = " . SITE_ID . " 
            ORDER BY quotation_id ASC 
            LIMIT 1
        ";

        $itineraryParentId = 0;
        if ($checkItinerary = mysqli_query($connection, $query)) {
            if (mysqli_num_rows($checkItinerary) > 0) {
                extract(mysqli_fetch_assoc($checkItinerary));
            }
        }

        // Insert new itinerary
        $insertQuery = "
            INSERT INTO " . TABLE_PREFIX . "quotation (
                quotation_parent_id, quotation_ref_id, quotation_ref_type,
                quotation_type_id, quotation_type, quotation_site_id,
                quotation_sent_by, quotation_mrp, quotation_price, 
                quotation_description, quotation_status, quotation_time
            ) VALUES (
                {$itineraryParentId}, '{$refId}', '{$refType}', {$id},
                'tour_package', " . SITE_ID . ", " . USER_ID . ",
                {$mrp}, {$itineraryPrice}, '{$itineraryDescription}', '0', NOW()
            )
        ";

        if ($saveQuotation = mysqli_query($connection, $insertQuery)) {
            $quotationId = mysqli_insert_id($connection);

            // Deactivate old itineraries
            $updateQuery = "
                UPDATE " . TABLE_PREFIX . "quotation 
                SET quotation_status = '2' 
                WHERE quotation_id != '{$quotationId}' 
                  AND quotation_ref_id = '{$refId}' 
                  AND quotation_ref_type = '{$refType}' 
                  AND quotation_type_id = {$id} 
                  AND quotation_type = 'tour_package' 
                  AND quotation_site_id = " . SITE_ID;

            if ($updateOldQuotation = mysqli_query($connection, $updateQuery)) {
                $httpStatus = 201;
                $msg = "Itinerary published successfully";
                $status = "success";

                if ($checkCustomerProfile = mysqli_query($connection, "SELECT profile_email, profile_first_name, profile_mobile FROM " . TABLE_PREFIX . "profile INNER JOIN " . TABLE_PREFIX . "enquiry ON enquiry_user_id = profile_user_id AND enquiry_site_id=" . SITE_ID . " WHERE enquiry_id={$refId} LIMIT 0,1")) {

                    if (mysqli_num_rows($checkCustomerProfile) != 0) {
                        extract(mysqli_fetch_assoc($checkCustomerProfile));
                        if ($data['isDownload'] == false) {
                            // Customer profile

                            $toAddress = $profile_email;
                            $mobile = $profile_mobile;

                            // Send Email
                            require_once USER_EMAIL_TEMPLATE . 'send-itinerary.php';

                            // Send WhatsApp
                            // require_once WHATSAPP_TEMPLATE_VISITOR . 'send-itinerary.php';
                        }
                    }
                } else {
                    echo mysqli_error($connection);
                    exit();
                }
            } else {
                $httpStatus = 500;
                $msg = "Failed to update old itinerary";
                $status = $msg;
            }
        } else {
            $httpStatus = 500;
            $msg = "Failed to save itinerary";
            $status = $msg;
        }
    }
}

// Response
$result = array(
    'status' => $status,
    'msg' => $msg,
    'itineraryId' => $quotationId,
);

http_response_code($httpStatus);
echo json_encode($result, true);