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

                if ($updateLog =  mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "enquiry_notes(enquiry_notes_ref_id, enquiry_notes_user_id, enquiry_notes_meta_type, enquiry_notes_meta_value) VALUES({$refId}, " . USER_ID . ", 'itinerary', '')")) {

                    $httpStatus = 201;
                    $msg = "Itinerary published successfully";
                    $status = "success";

                    if ($checkCustomerProfile = mysqli_query($connection, "SELECT enquiry_email, enquiry_mobile, enquiry_first_name FROM " . TABLE_PREFIX . "enquiry WHERE enquiry_id={$refId} LIMIT 0,1")) {

                        if (mysqli_num_rows($checkCustomerProfile) != 0) {
                            extract(mysqli_fetch_assoc($checkCustomerProfile));
                            if ($data['isDownload'] == false) {
                                // Customer profile

                                $toAddress = $enquiry_email;
                                $mobile = $enquiry_mobile;

                                $itinerary  = isset($content['itinerary']) && is_array($content['itinerary']) ? $content['itinerary'] : [];
                                $totalDays  = count($itinerary);

                                // Extract quick highlights (up to 3) from the 'day' field like "Day 1 - Delhi to Shimla"
                                $highlights = [];
                                foreach ($itinerary as $d) {
                                    $raw = trim((string)($d['day'] ?? ''));
                                    if ($raw === '') continue;
                                    // Try to split by " - " to get the title part
                                    $parts = explode(' - ', $raw, 2);
                                    $title = isset($parts[1]) ? $parts[1] : $raw;
                                    $title = trim($title);
                                    if ($title !== '') $highlights[] = $title;
                                    if (count($highlights) >= 3) break;
                                }

                                // First/last “titles” (for the mini range display)
                                $firstTitle = $highlights[0] ?? null;
                                $lastTitle  = null;
                                if ($totalDays > 1) {
                                    // Try to derive last title from the last “day” row
                                    $lastRaw = trim((string)($itinerary[$totalDays - 1]['day'] ?? ''));
                                    if ($lastRaw !== '') {
                                        $parts = explode(' - ', $lastRaw, 2);
                                        $lastTitle = isset($parts[1]) ? trim($parts[1]) : $lastRaw;
                                    }
                                }

                                // Build URLs (correct mapping)
                                $encodedId = base64_encode($quotationId);
                                $viewUrl   = 'https://' . SITE_DOMAIN . "/view-itinerary/{$encodedId}/";
                                $pdfUrl    = 'https://' . SITE_DOMAIN . "/download-itinerary/{$encodedId}/";

                                // Safety
                                $safeName     = htmlspecialchars($enquiry_first_name ?? 'Traveler', ENT_QUOTES, 'UTF-8');
                                $safeTour     = htmlspecialchars((string)($content['title'] ?? 'Your Trip'), ENT_QUOTES, 'UTF-8');
                                $safeSiteName = htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8');

                                // Send Email
                                if ($toAddress != '') {
                                    require_once USER_EMAIL_TEMPLATE . 'send-itinerary.php';
                                }

                                // Send whatsapp
                                if (defined('ENABLE_WHATSAPP_BUSINESS_API')) {
                                    if (ENABLE_WHATSAPP_BUSINESS_API == 'yes') {
                                        require_once WHATSAPP_TEMPLATE_USER . 'send-itinerary.php';
                                    }
                                }
                            }
                        }
                    }
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