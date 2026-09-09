<?php
/**
 * V3 Itinerary Publish API
 * Endpoint: /api/v3/itinerary/publish.php
 * Method: POST
 */

header("Content-type: application/json; charset=utf-8");

$docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
if (!isset($connection)) {
    $configPath = $docRoot . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

if (!defined('LOGIN_CHECK')) {
    define('LOGIN_CHECK', $docRoot . '/lib/modules/isLogin.php');
}
if (file_exists(LOGIN_CHECK)) {
    require_once LOGIN_CHECK;
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : 0;
$userId    = defined('USER_ID')      ? (int) USER_ID : 0;

// Get raw POST JSON or form / query data
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);
if (!is_array($data) || empty($data)) {
    $data = !empty($_POST) ? $_POST : $_GET;
}

$rawPkgId = isset($data['id']) ? trim((string)$data['id']) : (isset($data['package_id']) ? trim((string)$data['package_id']) : '0');
$packageId = is_numeric($rawPkgId) ? (int)$rawPkgId : 0;

$rawRefId = isset($data['refId']) ? trim((string)$data['refId']) : (isset($data['ref_id']) ? trim((string)$data['ref_id']) : (isset($data['lead_id']) ? trim((string)$data['lead_id']) : ''));
if (empty($rawRefId) && isset($_GET['refId'])) {
    $rawRefId = trim((string)$_GET['refId']);
}
$decodedRefId = base64_decode($rawRefId, true);
$refId = ($decodedRefId !== false && is_numeric($decodedRefId)) ? (int)$decodedRefId : (int)$rawRefId;

$refType   = isset($data['refType']) ? mysqli_real_escape_string($connection, trim($data['refType'])) : 'enquiry';
$mrp       = isset($data['mrp']) ? (float)$data['mrp'] : 0;
$content   = isset($data['content']) && is_array($data['content']) ? $data['content'] : [];
$isDownload = !empty($data['isDownload']);

if ($refId <= 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Missing or invalid Lead reference ID.'
    ]);
    exit;
}

// Extract and sanitize pricing
preg_match('/\d+/', (string)($content['adult_per_person'] ?? ''), $mAdult);
$adultPrice = isset($mAdult[0]) ? (int)$mAdult[0] : 0;

preg_match('/\d+/', (string)($content['child_per_person'] ?? ''), $mChild);
$childPrice = isset($mChild[0]) ? (int)$mChild[0] : 0;

preg_match('/\d+/', (string)($content['infant_per_person'] ?? ''), $mInfant);
$infantPrice = isset($mInfant[0]) ? (int)$mInfant[0] : 0;

preg_match('/\d+/', (string)($content['total_price'] ?? ''), $mTotal);
$totalPrice = isset($mTotal[0]) ? (int)$mTotal[0] : ($adultPrice > 0 ? $adultPrice : 0);

$itineraryJson = mysqli_real_escape_string($connection, json_encode($content, JSON_UNESCAPED_UNICODE));

// Check if previous quotation exists to establish parent relationship
$parentCheckQuery = "
    SELECT quotation_id AS itineraryParentId 
    FROM {$tblPrefix}quotation 
    WHERE quotation_ref_id = '{$refId}' 
      AND quotation_ref_type = '{$refType}' 
      AND quotation_type_id = {$packageId} 
      AND quotation_type = 'tour_package' 
      " . ($siteId > 0 ? "AND quotation_site_id = {$siteId}" : "") . "
    ORDER BY quotation_id ASC 
    LIMIT 1
";

$itineraryParentId = 0;
$checkParentRes = mysqli_query($connection, $parentCheckQuery);
if ($checkParentRes && mysqli_num_rows($checkParentRes) > 0) {
    $pRow = mysqli_fetch_assoc($checkParentRes);
    $itineraryParentId = (int)($pRow['itineraryParentId'] ?? 0);
}

// Insert new quotation record
$insertQuery = "
    INSERT INTO {$tblPrefix}quotation (
        quotation_parent_id,
        quotation_ref_id,
        quotation_ref_type,
        quotation_type_id,
        quotation_type,
        quotation_site_id,
        quotation_sent_by,
        quotation_mrp,
        quotation_per_adult,
        quotation_per_child,
        quotation_per_infant,
        quotation_price,
        quotation_description,
        quotation_status,
        quotation_time
    ) VALUES (
        {$itineraryParentId},
        '{$refId}',
        '{$refType}',
        {$packageId},
        'tour_package',
        {$siteId},
        {$userId},
        {$mrp},
        {$adultPrice},
        {$childPrice},
        {$infantPrice},
        {$totalPrice},
        '{$itineraryJson}',
        0,
        NOW()
    )
";

if (mysqli_query($connection, $insertQuery)) {
    $quotationId = mysqli_insert_id($connection);
    $encodedId = base64_encode((string)$quotationId);

    // Deactivate previous quotations for this lead/package
    mysqli_query($connection, "
        UPDATE {$tblPrefix}quotation 
        SET quotation_status = 2 
        WHERE quotation_id != {$quotationId} 
          AND quotation_ref_id = '{$refId}' 
          AND quotation_ref_type = '{$refType}' 
          AND quotation_type = 'tour_package' 
          " . ($siteId > 0 ? "AND quotation_site_id = {$siteId}" : "") . "
    ");

    // Log in enquiry_notes
    mysqli_query($connection, "
        INSERT INTO {$tblPrefix}enquiry_notes (
            enquiry_notes_ref_id, 
            enquiry_notes_user_id, 
            enquiry_notes_meta_type, 
            enquiry_notes_meta_value
        ) VALUES (
            {$refId}, 
            {$userId}, 
            'itinerary', 
            '{$quotationId}'
        )
    ");

    // Fetch customer details from enquiry
    $customerQuery = mysqli_query($connection, "
        SELECT * FROM {$tblPrefix}enquiry 
        WHERE enquiry_id = {$refId} 
          " . ($siteId > 0 ? "AND enquiry_site_id = {$siteId}" : "") . "
        LIMIT 1
    ");

    if (!$customerQuery || mysqli_num_rows($customerQuery) === 0) {
        // Fallback query without site filter
        $customerQuery = mysqli_query($connection, "
            SELECT * FROM {$tblPrefix}enquiry 
            WHERE enquiry_id = {$refId} 
            LIMIT 1
        ");
    }

    $customerData = ($customerQuery && mysqli_num_rows($customerQuery) > 0) ? mysqli_fetch_assoc($customerQuery) : [];

    // Extract enquiry_email and enquiry_mobile
    $enquiryEmail  = trim($customerData['enquiry_email'] ?? '');
    $enquiryMobile = trim($customerData['enquiry_mobile'] ?? '');

    // Fallbacks from input data
    if (empty($enquiryEmail) && !empty($data['email'])) {
        $enquiryEmail = trim((string)$data['email']);
    }
    if (empty($enquiryEmail) && !empty($data['enquiry_email'])) {
        $enquiryEmail = trim((string)$data['enquiry_email']);
    }

    if (empty($enquiryMobile) && !empty($data['mobile'])) {
        $enquiryMobile = trim((string)$data['mobile']);
    }
    if (empty($enquiryMobile) && !empty($data['enquiry_mobile'])) {
        $enquiryMobile = trim((string)$data['enquiry_mobile']);
    }

    // Customer name extraction
    $firstName  = trim($customerData['enquiry_first_name'] ?? '');
    $middleName = trim($customerData['enquiry_middle_name'] ?? '');
    $lastName   = trim($customerData['enquiry_last_name'] ?? '');
    $customerName = trim(implode(' ', array_filter([$firstName, $middleName, $lastName])));

    // Fallbacks from enquiry_message if stored as JSON
    if (empty($enquiryEmail) || empty($enquiryMobile) || empty($customerName)) {
        $rawMsg = $customerData['enquiry_message'] ?? '';
        if (!empty($rawMsg) && ($rawMsg[0] === '{' || $rawMsg[0] === '[')) {
            $msgJson = json_decode($rawMsg, true);
            if (is_array($msgJson)) {
                if (empty($enquiryEmail)) {
                    $enquiryEmail = trim($msgJson['email'] ?? ($msgJson['enquiry_email'] ?? ''));
                }
                if (empty($enquiryMobile)) {
                    $enquiryMobile = trim($msgJson['mobile'] ?? ($msgJson['phone'] ?? ($msgJson['enquiry_mobile'] ?? '')));
                }
                if (empty($customerName)) {
                    $customerName = trim($msgJson['name'] ?? ($msgJson['customer_name'] ?? ''));
                }
            }
        }
    }

    if (empty($customerName)) {
        $customerName = 'Valued Customer';
    }

    // Persist email/mobile back to enquiry record if it was previously empty
    if (!empty($customerData)) {
        $updateFields = [];
        if (empty($customerData['enquiry_email']) && !empty($enquiryEmail)) {
            $escEmail = mysqli_real_escape_string($connection, $enquiryEmail);
            $updateFields[] = "enquiry_email = '{$escEmail}'";
        }
        if (empty($customerData['enquiry_mobile']) && !empty($enquiryMobile)) {
            $escMobile = mysqli_real_escape_string($connection, $enquiryMobile);
            $updateFields[] = "enquiry_mobile = '{$escMobile}'";
        }
        if (!empty($updateFields)) {
            mysqli_query($connection, "
                UPDATE {$tblPrefix}enquiry 
                SET " . implode(', ', $updateFields) . " 
                WHERE enquiry_id = {$refId}
            ");
        }
    }

    $siteDomain = defined('SITE_DOMAIN') && !empty(SITE_DOMAIN) ? SITE_DOMAIN : ($_SERVER['HTTP_HOST'] ?? '');
    $viewUrl = 'https://' . $siteDomain . "/view-itinerary/{$encodedId}/";
    $pdfUrl  = 'https://' . $siteDomain . "/download-itinerary/{$encodedId}/";

    $emailSent = false;
    $emailError = null;
    $whatsappSent = false;
    $whatsappError = null;

    if (!$isDownload) {
        $itineraryArr = isset($content['itinerary']) && is_array($content['itinerary']) ? $content['itinerary'] : [];
        $totalDays    = count($itineraryArr);

        // Extract highlights for email proposal summary
        $highlights = [];
        foreach ($itineraryArr as $d) {
            $raw = trim((string)($d['day'] ?? ''));
            if ($raw === '') continue;
            $parts = explode(' - ', $raw, 2);
            $title = isset($parts[1]) ? $parts[1] : $raw;
            $title = trim($title);
            if ($title !== '') $highlights[] = $title;
            if (count($highlights) >= 3) break;
        }

        $firstTitle = $highlights[0] ?? null;
        $lastTitle  = null;
        if ($totalDays > 1) {
            $lastRaw = trim((string)($itineraryArr[$totalDays - 1]['day'] ?? ''));
            if ($lastRaw !== '') {
                $parts = explode(' - ', $lastRaw, 2);
                $lastTitle = isset($parts[1]) ? trim($parts[1]) : $lastRaw;
            }
        }

        $safeName     = htmlspecialchars($customerName, ENT_QUOTES, 'UTF-8');
        $safeTour     = htmlspecialchars((string)($content['title'] ?? 'Your Trip'), ENT_QUOTES, 'UTF-8');
        $safeSiteName = htmlspecialchars(defined('SITE_NAME') ? SITE_NAME : 'Aleya', ENT_QUOTES, 'UTF-8');
        $itineraryPrice = $totalPrice;
        $tourDuration = ($totalDays > 1 ? ($totalDays - 1) . ' Nights / ' : '') . $totalDays . ' Days';

        // 1. Dispatch Itinerary via Email using enquiry_email
        $toAddress = $enquiryEmail;
        if (!empty($toAddress)) {
            $emailTemplateFile = '';
            if (defined('USER_EMAIL_TEMPLATE') && file_exists(USER_EMAIL_TEMPLATE . 'send-itinerary.php')) {
                $emailTemplateFile = USER_EMAIL_TEMPLATE . 'send-itinerary.php';
            } elseif (defined('EMAIL_TEMPLATE')) {
                $siteApp = defined('SITE_APP') ? SITE_APP : 'aleya';
                $emailCands = [
                    EMAIL_TEMPLATE . $siteApp . '/agent/send-itinerary.php',
                    EMAIL_TEMPLATE . $siteApp . '/administrator/send-itinerary.php',
                    EMAIL_TEMPLATE . $siteApp . '/operations/send-itinerary.php',
                ];
                foreach ($emailCands as $cand) {
                    if (file_exists($cand)) {
                        $emailTemplateFile = $cand;
                        break;
                    }
                }
            }

            if (!empty($emailTemplateFile)) {
                // Buffer output to catch any PHPMailer / echo outputs and prevent corrupting JSON
                ob_start();
                try {
                    require_once $emailTemplateFile;
                    $emailSent = true;
                } catch (\Throwable $e) {
                    $emailError = $e->getMessage();
                }
                $emailOut = ob_get_clean();
                if (!empty($emailOut) && (stripos($emailOut, 'Mailer Error') !== false || stripos($emailOut, 'Exception') !== false)) {
                    $emailError = trim(strip_tags($emailOut));
                    $emailSent = false;
                }
            } else {
                $emailError = 'Email template not found on server.';
            }
        } else {
            $emailError = 'Customer email address (enquiry_email) is missing.';
        }

        // 2. Dispatch Itinerary via WhatsApp using enquiry_mobile
        $cleanMobile = preg_replace('/[^0-9]/', '', $enquiryMobile);
        $mobile = !empty($cleanMobile) ? $cleanMobile : $enquiryMobile;

        if (!empty($mobile)) {
            $waEnabled = (
                (defined('ENABLE_WHATSAPP_BUSINESS_API') && (ENABLE_WHATSAPP_BUSINESS_API === 'yes' || ENABLE_WHATSAPP_BUSINESS_API === '1' || ENABLE_WHATSAPP_BUSINESS_API === 1))
                || !defined('ENABLE_WHATSAPP_BUSINESS_API')
            );

            if ($waEnabled) {
                $whatsappTemplateFile = '';
                if (defined('WHATSAPP_TEMPLATE_USER') && file_exists(WHATSAPP_TEMPLATE_USER . 'send-itinerary.php')) {
                    $whatsappTemplateFile = WHATSAPP_TEMPLATE_USER . 'send-itinerary.php';
                } elseif (defined('WHATSAPP_TEMPLATE')) {
                    $siteApp = defined('SITE_APP') ? SITE_APP : 'aleya';
                    $waCands = [
                        WHATSAPP_TEMPLATE . $siteApp . '/agent/send-itinerary.php',
                        WHATSAPP_TEMPLATE . $siteApp . '/administrator/send-itinerary.php',
                    ];
                    foreach ($waCands as $cand) {
                        if (file_exists($cand)) {
                            $whatsappTemplateFile = $cand;
                            break;
                        }
                    }
                }

                if (!empty($whatsappTemplateFile)) {
                    ob_start();
                    try {
                        require_once $whatsappTemplateFile;
                        $whatsappSent = true;
                    } catch (\Throwable $e) {
                        $whatsappError = $e->getMessage();
                    }
                    $waOut = ob_get_clean();
                    if (!empty($waOut) && stripos($waOut, 'failed') !== false) {
                        $whatsappError = trim(strip_tags($waOut));
                    }
                }
            }
        } else {
            $whatsappError = 'Customer mobile number (enquiry_mobile) is missing.';
        }
    }

    $msg = 'Itinerary published successfully.';
    if (!$isDownload) {
        if ($emailSent && $whatsappSent) {
            $msg = 'Itinerary published and sent to customer via Email and WhatsApp.';
        } elseif ($emailSent) {
            $msg = 'Itinerary published and sent to customer via Email.';
        } elseif ($whatsappSent) {
            $msg = 'Itinerary published and sent to customer via WhatsApp.';
        } elseif (!empty($emailError)) {
            $msg = 'Itinerary published, but could not send email: ' . $emailError;
        }
    }

    http_response_code(200);
    echo json_encode([
        'status'         => 'success',
        'httpStatus'     => 200,
        'msg'            => $msg,
        'itineraryId'    => $quotationId,
        'encodedId'      => $encodedId,
        'viewUrl'        => $viewUrl ?? '',
        'pdfUrl'         => $pdfUrl ?? '',
        'emailSent'      => $emailSent,
        'whatsappSent'   => $whatsappSent,
        'enquiry_email'  => $enquiryEmail,
        'enquiry_mobile' => $enquiryMobile
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'status'     => 'error',
        'httpStatus' => 500,
        'msg'        => (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE) ? mysqli_error($connection) : 'Failed to publish itinerary.'
    ]);
}
