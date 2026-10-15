<?php

/**
 * V3 Send Vehicle Photos API
 * Path: /api/v3/leads/send-vehicle-photos/
 * Allows transporters to select fleet vehicles and send photo profiles & specifications
 * to customers via WhatsApp and Email.
 */

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
@ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');

$docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
if (!isset($connection)) {
    if (file_exists($docRoot . '/lib/configuration/index.php')) {
        require_once $docRoot . '/lib/configuration/index.php';
    }
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
}

if (!defined('LOGIN_CHECK')) {
    define('LOGIN_CHECK', $docRoot . '/lib/modules/isLogin.php');
}
if (file_exists(LOGIN_CHECK)) {
    require_once LOGIN_CHECK;
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : (int)($_SESSION['site_id'] ?? 0);
$userId    = defined('USER_ID')      ? (int) USER_ID : (int)($_SESSION['user_id'] ?? 0);

if ($siteId <= 0) {
    $siteId = 1;
}

if (file_exists($docRoot . '/api/v3/vehicles/schema.php')) {
    require_once $docRoot . '/api/v3/vehicles/schema.php';
    ensureTransporterVehiclesSchema($connection, $tblPrefix, $siteId);
}

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed. Use POST.']);
    exit;
}

// Read JSON Input or POST
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);
if (!is_array($data)) {
    $data = $_POST;
}

$rawLeadId = $data['lead_id'] ?? ($data['id'] ?? ($_POST['lead_id'] ?? ($_POST['id'] ?? ($_REQUEST['lead_id'] ?? ($_REQUEST['id'] ?? ($_GET['lead_id'] ?? ($_GET['id'] ?? '')))))));
$leadId = 0;
if (is_numeric($rawLeadId) && (int)$rawLeadId > 0) {
    $leadId = (int)$rawLeadId;
} elseif (is_string($rawLeadId) && !empty($rawLeadId)) {
    $rawLeadId = trim($rawLeadId);
    $decoded = base64_decode($rawLeadId, true);
    if ($decoded !== false && is_numeric($decoded) && (int)$decoded > 0) {
        $leadId = (int)$decoded;
    } else {
        $decodedUrl = base64_decode(urldecode($rawLeadId), true);
        if ($decodedUrl !== false && is_numeric($decodedUrl) && (int)$decodedUrl > 0) {
            $leadId = (int)$decodedUrl;
        }
    }
}

if ($leadId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Valid lead ID is required.']);
    exit;
}

// Selected vehicle IDs
$vehicleIds = [];
if (!empty($data['vehicle_ids']) && is_array($data['vehicle_ids'])) {
    foreach ($data['vehicle_ids'] as $vId) {
        if (is_numeric($vId) && (int)$vId > 0) {
            $vehicleIds[] = (int)$vId;
        }
    }
} elseif (!empty($data['vehicle_id']) && is_numeric($data['vehicle_id'])) {
    $vehicleIds[] = (int)$data['vehicle_id'];
}

if (empty($vehicleIds)) {
    echo json_encode(['status' => 'error', 'message' => 'Please select at least one vehicle to send.']);
    exit;
}

$sendWhatsApp = !empty($data['send_whatsapp']) && $data['send_whatsapp'] !== '0' && $data['send_whatsapp'] !== 'false';
$sendEmail    = !empty($data['send_email']) && $data['send_email'] !== '0' && $data['send_email'] !== 'false';
$customNote   = trim((string)($data['custom_message'] ?? ($data['note'] ?? '')));

if (!$sendWhatsApp && !$sendEmail) {
    echo json_encode(['status' => 'error', 'message' => 'Please select at least one delivery channel (WhatsApp or Email).']);
    exit;
}

// 1. Fetch Lead Details
$leadQuery = mysqli_query($connection, "SELECT * FROM {$tblPrefix}enquiry WHERE enquiry_id = {$leadId} LIMIT 1");
if (!$leadQuery || mysqli_num_rows($leadQuery) === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Lead not found.']);
    exit;
}
$lead = mysqli_fetch_assoc($leadQuery);
$leadSiteId = !empty($lead['enquiry_site_id']) ? (int)$lead['enquiry_site_id'] : $siteId;

$customerName = trim(implode(' ', array_filter([$lead['enquiry_first_name'] ?? '', $lead['enquiry_middle_name'] ?? '', $lead['enquiry_last_name'] ?? ''])));
if (empty($customerName)) {
    $customerName = trim($lead['enquiry_name'] ?? 'Customer');
}
$customerMobile = trim($lead['enquiry_mobile'] ?? ($lead['enquiry_phone'] ?? ''));
$customerEmail  = trim($lead['enquiry_email'] ?? '');
$destination    = trim($lead['enquiry_destination'] ?? '');
$pickupLocation = trim($lead['enquiry_pickup_location'] ?? '');
if (empty($destination) && !empty($lead['enquiry_query'])) {
    $parsedQuery = json_decode($lead['enquiry_query'], true);
    if (is_array($parsedQuery)) {
        $destination = $parsedQuery['destination'] ?? ($parsedQuery['to_city'] ?? '');
        if (empty($pickupLocation)) {
            $pickupLocation = $parsedQuery['pickup_location'] ?? ($parsedQuery['from_city'] ?? '');
        }
    }
}

// 2. Fetch Selected Vehicles with Photos
$vIdList = implode(',', $vehicleIds);
$vQuery = mysqli_query($connection, "SELECT 
    vehicle_id, 
    vehicle_title, 
    vehicle_type, 
    vehicle_seating, 
    vehicle_luggage, 
    vehicle_fuel_type, 
    vehicle_description, 
    vehicle_thumbnail, 
    vehicle_gallery 
    FROM {$tblPrefix}vehicles 
    WHERE vehicle_id IN ({$vIdList}) 
    ORDER BY FIELD(vehicle_id, {$vIdList})");

$vehicles = [];
$baseUrl = '';
if (defined('SITE_URL') && !empty(SITE_URL)) {
    $baseUrl = rtrim(SITE_URL, '/');
} elseif (defined('SITE_DOMAIN') && !empty(SITE_DOMAIN)) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $baseUrl = $protocol . rtrim(SITE_DOMAIN, '/');
} elseif (!empty($_SERVER['HTTP_HOST'])) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $baseUrl = $protocol . $_SERVER['HTTP_HOST'];
}

function normalizeVehiclePhotoUrl(string $url, string $baseUrl): string
{
    $url = trim($url);
    if (empty($url)) return '';
    if (preg_match('/^https?:\/\//i', $url)) return $url;
    return rtrim($baseUrl, '/') . '/' . ltrim($url, '/');
}

$typeLabels = [
    'mpv' => 'MPV',
    'suv' => 'SUV',
    'bus' => 'Bus / Coach',
    'traveller' => 'Tempo Traveller',
    'light_commercial_vehicle' => 'Van / Commercial',
    'sedan' => 'Sedan',
    'hatchback' => 'Hatchback',
    'micro' => 'Small Car / Micro'
];

if ($vQuery && mysqli_num_rows($vQuery) > 0) {
    while ($vRow = mysqli_fetch_assoc($vQuery)) {
        $rawThumb = trim($vRow['vehicle_thumbnail'] ?? '');
        $thumbUrl = !empty($rawThumb) ? normalizeVehiclePhotoUrl($rawThumb, $baseUrl) : '';

        $photos = [];
        if (!empty($thumbUrl)) {
            $photos[] = $thumbUrl;
        }

        $rawGallery = $vRow['vehicle_gallery'] ?? '';
        if (!empty($rawGallery)) {
            $decodedG = json_decode($rawGallery, true);
            if (is_array($decodedG)) {
                foreach ($decodedG as $gImg) {
                    $norm = normalizeVehiclePhotoUrl((string)$gImg, $baseUrl);
                    if (!empty($norm) && !in_array($norm, $photos)) {
                        $photos[] = $norm;
                    }
                }
            }
        }

        $vType = strtolower($vRow['vehicle_type'] ?? 'vehicle');
        $vehicles[] = [
            'id'           => (int)$vRow['vehicle_id'],
            'title'        => ucwords(strtolower(trim($vRow['vehicle_title'] ?? 'Fleet Vehicle'))),
            'type'         => $vType,
            'type_label'   => $typeLabels[$vType] ?? ucwords(str_replace('_', ' ', $vType)),
            'seating'      => (int)($vRow['vehicle_seating'] ?? 4),
            'luggage'      => (int)($vRow['vehicle_luggage'] ?? 2),
            'fuel_type'    => trim($vRow['vehicle_fuel_type'] ?? ''),
            'description'  => trim($vRow['vehicle_description'] ?? ''),
            'thumbnail'    => $thumbUrl,
            'photos'       => $photos,
            'photo_count'  => count($photos)
        ];
    }
}

if (empty($vehicles)) {
    echo json_encode(['status' => 'error', 'message' => 'No matching vehicle records found.']);
    exit;
}

// 3. Fetch Company & Site Profile
$compName = defined('SITE_NAME') ? SITE_NAME : 'Transport Services';
$compPhone = '';
$compEmail = '';
$siteCfgQ = mysqli_query($connection, "SELECT site_configuration_key, site_configuration_value FROM {$tblPrefix}site_configuration WHERE site_configuration_site_id = {$leadSiteId} AND site_configuration_status = 1 AND site_configuration_key IN ('company_name', 'company_phone_number', 'company_email_address')");
if ($siteCfgQ) {
    while ($scRow = mysqli_fetch_assoc($siteCfgQ)) {
        if ($scRow['site_configuration_key'] === 'company_name' && !empty($scRow['site_configuration_value'])) {
            $compName = trim($scRow['site_configuration_value']);
        }
        if ($scRow['site_configuration_key'] === 'company_phone_number' && !empty($scRow['site_configuration_value'])) {
            $compPhone = trim($scRow['site_configuration_value']);
        }
        if ($scRow['site_configuration_key'] === 'company_email_address' && !empty($scRow['site_configuration_value'])) {
            $compEmail = trim($scRow['site_configuration_value']);
        }
    }
}

$sentChannels = [];
$whatsappSent = false;
$whatsappError = null;
$emailSent = false;
$emailError = null;

// 4. Dispatch Email
if ($sendEmail) {
    if (!empty($customerEmail) && filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
        $toAddress = $customerEmail;
        $subject = "Vehicle Fleet Photos & Details for your trip | " . $compName;

        $safeCustName = htmlspecialchars($customerName ?: 'Customer', ENT_QUOTES, 'UTF-8');
        $safeCompName = htmlspecialchars($compName, ENT_QUOTES, 'UTF-8');

        $vehicleCardsHtml = '';
        foreach ($vehicles as $v) {
            $safeTitle = htmlspecialchars($v['title'], ENT_QUOTES, 'UTF-8');
            $safeType  = htmlspecialchars($v['type_label'], ENT_QUOTES, 'UTF-8');
            $safeFuel  = !empty($v['fuel_type']) ? ' • Fuel: ' . htmlspecialchars($v['fuel_type'], ENT_QUOTES, 'UTF-8') : '';

            $galleryHtml = '';
            if (!empty($v['photos'])) {
                $galleryHtml .= '<div style="display: flex; gap: 8px; flex-wrap: wrap; margin-top: 12px;">';
                foreach (array_slice($v['photos'], 0, 4) as $pUrl) {
                    $safePUrl = htmlspecialchars($pUrl, ENT_QUOTES, 'UTF-8');
                    $galleryHtml .= '<a href="' . $safePUrl . '" target="_blank" style="text-decoration: none;">
                        <img src="' . $safePUrl . '" alt="' . $safeTitle . '" style="width: 130px; height: 95px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0; display: block;" />
                    </a>';
                }
                $galleryHtml .= '</div>';
            }

            $vehicleCardsHtml .= "
                <div style='background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 18px; margin-bottom: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);'>
                    <div style='display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 6px;'>
                        <h3 style='margin: 0; color: #0f172a; font-size: 17px;'>{$safeTitle}</h3>
                        <span style='background: #eff6ff; color: #1d4ed8; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 4px; text-transform: uppercase;'>{$safeType}</span>
                    </div>
                    <p style='margin: 4px 0 10px 0; font-size: 13px; color: #64748b;'>
                        <strong>Seating:</strong> {$v['seating']} Passengers &bull; <strong>Luggage:</strong> {$v['luggage']} Bags{$safeFuel}
                    </p>
                    {$galleryHtml}
                </div>
            ";
        }

        $safeCustomNoteHtml = '';
        if (!empty($customNote)) {
            $safeNote = nl2br(htmlspecialchars($customNote, ENT_QUOTES, 'UTF-8'));
            $safeCustomNoteHtml = "
                <div style='background: #fffbeb; border-left: 4px solid #f59e0b; padding: 12px 16px; margin: 18px 0; border-radius: 4px; font-size: 13.5px; color: #92400e;'>
                    <strong>Note from your Travel Representative:</strong><br>{$safeNote}
                </div>
            ";
        }

        $email_body = "
            <div style='font-family: Arial, sans-serif; max-width: 620px; margin: 0 auto; padding: 24px; color: #1e293b; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;'>
                <div style='background: #ffffff; border-radius: 10px; border: 1px solid #e2e8f0; padding: 20px; margin-bottom: 20px;'>
                    <h2 style='color: #0f172a; margin: 0; font-size: 21px; font-weight: 800;'>{$safeCompName}</h2>
                    <p style='color: #64748b; margin: 4px 0 0 0; font-size: 13px;'>Vehicle Fleet Profiles &amp; Photos for your Travel Inquiry (#{$leadId})</p>
                </div>

                <p style='font-size: 14.5px; line-height: 22px; margin-bottom: 12px;'>Dear <strong>{$safeCustName}</strong>,</p>
                <p style='font-size: 14px; line-height: 22px; color: #475569;'>
                    Thank you for contacting <strong>{$safeCompName}</strong>. Below are the vehicle options, high-resolution photos, and seating specifications available for your journey:
                </p>

                {$safeCustomNoteHtml}
                {$vehicleCardsHtml}

                <div style='background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 16px; margin-top: 24px; text-align: center;'>
                    <p style='margin: 0; font-size: 13.5px; color: #334155;'>
                        Please reply to this email or message us on WhatsApp with your preferred vehicle choice to confirm your reservation.
                    </p>
                </div>

                <p style='font-size: 12px; line-height: 18px; color: #94a3b8; text-align: center; margin-top: 24px;'>
                    —<br>
                    <em>Powered by Aleya.in Transport CRM</em>
                </p>
            </div>
        ";

        if (defined('SEND_EMAIL') && file_exists(SEND_EMAIL)) {
            ob_start();
            try {
                require SEND_EMAIL;
                $emailSent = true;
                $sentChannels[] = "Email";
            } catch (\Throwable $e) {
                $emailError = $e->getMessage();
            }
            $emailOut = ob_get_clean();
            if (!empty($emailOut) && (stripos($emailOut, 'Mailer Error') !== false || stripos($emailOut, 'Exception') !== false)) {
                $emailError = trim(strip_tags($emailOut));
                $emailSent = false;
            }
        } else {
            $emailError = 'Mailer service not configured.';
        }
    } else {
        $emailError = 'Customer email address is invalid or not available.';
    }
} else {
    $emailError = 'Skipped by user toggle.';
}

// 5. Dispatch WhatsApp
if ($sendWhatsApp) {
    $cleanPhone = preg_replace('/[^0-9]/', '', (string)$customerMobile);
    if (!empty($cleanPhone)) {
        require_once dirname(__DIR__) . '/whatsapp/device_helper.php';

        $waLines = [];
        $waLines[] = "🚗 *{$compName}*";
        $waLines[] = "📸 *VEHICLE FLEET PHOTOS & DETAILS*";
        $waLines[] = "━━━━━━━━━━━━━━━━━━━━━━";
        $waLines[] = "Hello *{$customerName}*,";
        $waLines[] = "";
        $waLines[] = "Here are the vehicle specifications & photos for your upcoming travel inquiry:";
        $waLines[] = "";

        $primaryPhoto = '';
        foreach ($vehicles as $idx => $v) {
            $num = $idx + 1;
            $waLines[] = "*{$num}. {$v['title']}* ({$v['type_label']})";
            $waLines[] = "• Seating: {$v['seating']} Passengers + Driver";
            $waLines[] = "• Luggage: {$v['luggage']} Bags";
            if (!empty($v['fuel_type'])) {
                $waLines[] = "• Fuel: {$v['fuel_type']}";
            }
            if (!empty($v['photos'])) {
                $waLines[] = "• Photos: " . implode(" , ", array_slice($v['photos'], 0, 3));
                if (empty($primaryPhoto) && !empty($v['photos'][0])) {
                    $primaryPhoto = $v['photos'][0];
                }
            }
            $waLines[] = "";
        }

        if (!empty($customNote)) {
            $waLines[] = "📝 *Note:* " . $customNote;
            $waLines[] = "";
        }

        $waLines[] = "Please reply with your preferred vehicle choice so we can finalize your booking!";
        $waLines[] = "";
        $waLines[] = "—";
        $waLines[] = "_Powered by Aleya.in_";

        $waText = implode("\n", $waLines);

        $waOpts = [
            'lead_id'   => $leadId,
            'type'      => 'text'
        ];

        // If a direct photo is available, send as image message with caption
        if (!empty($primaryPhoto)) {
            $waOpts['type'] = 'image';
            $waOpts['media_url'] = $primaryPhoto;
        }

        $waRes = sendWhatsAppDirectMessage(
            $connection,
            $leadSiteId,
            $userId,
            $cleanPhone,
            $waText,
            $waOpts
        );

        if (isset($waRes['status']) && $waRes['status'] === 'success') {
            $whatsappSent = true;
            $sentChannels[] = "WhatsApp";
        } else {
            $whatsappError = $waRes['msg'] ?? 'WhatsApp device error.';
        }
    } else {
        $whatsappError = 'Customer mobile number is missing.';
    }
} else {
    $whatsappError = 'Skipped by user toggle.';
}

// 6. Record Timeline Activity Note
$vehicleTitles = array_map(function ($v) {
    return $v['title'];
}, $vehicles);
$vehSummary = implode(', ', $vehicleTitles);
$userName = defined('PROFILE_DISPLAY_NAME') ? PROFILE_DISPLAY_NAME : 'Representative';

$timelineNote = "Shared vehicle photos for [{$vehSummary}]";
if (!empty($sentChannels)) {
    $timelineNote .= " via " . implode(' & ', $sentChannels);
} else {
    $timelineNote .= " (Delivery skipped or failed)";
}

$notePayload = [
    'vehicle_ids'    => $vehicleIds,
    'vehicles'       => $vehicleTitles,
    'channels'       => $sentChannels,
    'custom_note'    => $customNote,
    'whatsapp_sent'  => $whatsappSent,
    'email_sent'     => $emailSent,
    'date'           => date('d-m-Y h:i A'),
    'sent_by'        => $userName
];

$notePayloadEsc = mysqli_real_escape_string($connection, json_encode($notePayload, JSON_UNESCAPED_UNICODE));
$escNoteText = mysqli_real_escape_string($connection, $timelineNote);

@mysqli_query($connection, "INSERT INTO {$tblPrefix}enquiry_notes (
    enquiry_notes_ref_id,
    enquiry_notes_user_id,
    enquiry_notes_meta_type,
    enquiry_notes_meta_value
) VALUES (
    {$leadId},
    {$userId},
    'vehicle_photos',
    '{$notePayloadEsc}'
)");

@mysqli_query($connection, "UPDATE {$tblPrefix}enquiry SET enquiry_update = NOW() WHERE enquiry_id = {$leadId}");

$success = (!empty($sentChannels));
$msg = $success
    ? ("Vehicle photos sent successfully via " . implode(' & ', $sentChannels) . "!")
    : ("Vehicle photos delivery attempted but could not be dispatched: " . implode('; ', array_filter([$whatsappError, $emailError])));

echo json_encode([
    'status'         => $success ? 'success' : 'error',
    'message'        => $msg,
    'sent_channels'  => $sentChannels,
    'whatsapp_sent'  => $whatsappSent,
    'whatsapp_error' => $whatsappError,
    'email_sent'     => $emailSent,
    'email_error'    => $emailError,
    'vehicles_count' => count($vehicles)
]);
exit;
