<?php

require_once 'header.php';

$templateName = 'aleya_bw_1';
$bypassDevice = true; // Exclusively send via BhashSMS WhatsApp Business API (bypass WhatsApp Web)

// Sanitize and ensure all values are strings
// Combine first and last name, then check if it's blank
$name = (isset($first_name) ? trim($first_name) : '') . ' ' . (isset($last_name) ? trim($last_name) : '');
$name = trim($name) !== '' ? $name : 'N/A';

// Format lead alert title for admin (use "New Lead Alert" instead of generic "Website Enquiry")
$rawSubject = is_array($subject) ? implode(' ', array_map('trim', $subject)) : trim((string)($subject ?? ''));
if ($rawSubject === '' || strcasecmp($rawSubject, 'Website Enquiry') === 0 || strcasecmp($rawSubject, 'enquiry') === 0 || strcasecmp($rawSubject, 'N/A') === 0) {
    $subject = 'New Lead Alert';
} else {
    $subject = 'New Lead Alert: ' . $rawSubject;
}

// Check travel date
$travelDates = isset($travel_date) ? trim($travel_date) : '';
$travelDates = $travelDates !== '' ? $travelDates : 'N/A';

// Check email
$email = isset($email) ? trim($email) : '';
$email = $email !== '' ? $email : 'N/A';

// Check requirements
$requirements = isset($requirements) ? trim($requirements) : '';
$requirements = $requirements !== '' ? $requirements : 'N/A';

// Sanitize each variable and remove commas to ensure exactly 7 parameters for aleya_bw_1
if (!function_exists('cleanAleyaBw1Param')) {
    function cleanAleyaBw1Param($val, $default = 'N/A')
    {
        if (is_array($val)) {
            $val = implode(' ', array_map('trim', $val));
        }
        $val = trim((string)($val ?? ''));
        if ($val === '') {
            $val = $default;
        }
        // Commas and newlines break the comma-separated Params query in BhashSMS
        return str_replace([',', "\r", "\n"], [' ', ' ', ' '], $val);
    }
}

// Generate link
$siteUrl = defined('SITE_URL') ? SITE_URL : '';
$leadIdEncoded = isset($leadId) ? base64_encode($leadId) : '';
$link = $siteUrl . '/dashboard/leads/view/?id=' . $leadIdEncoded;

// Clean each parameter
$paramSubject      = cleanAleyaBw1Param($subject, 'New Lead Alert');
$paramTravelDates  = cleanAleyaBw1Param($travelDates, 'N/A');
$paramName         = cleanAleyaBw1Param($name, 'N/A');
$paramMobile       = cleanAleyaBw1Param($mobile, 'N/A');
$paramEmail        = cleanAleyaBw1Param($email, 'N/A');
$paramRequirements = cleanAleyaBw1Param($requirements, 'N/A');
$paramLink         = cleanAleyaBw1Param($link, $siteUrl);

// Build the message parameters string (strictly 7 comma-separated parameters)
$variablesValues = $paramSubject . ',' . $paramTravelDates . ',' . $paramName . ',' . $paramMobile . ',' . $paramEmail . ',' . $paramRequirements . ',' . $paramLink;

// Resolve Admin WhatsApp number from constant or site_configuration
$targetNumber = '';
if (defined('ADMIN_WHATSAPP_NUMBER') && !empty(ADMIN_WHATSAPP_NUMBER)) {
    $targetNumber = ADMIN_WHATSAPP_NUMBER;
} elseif (isset($connection)) {
    $targetSiteId = defined('SITE_ID') && (int)SITE_ID > 0 ? (int)SITE_ID : (isset($site_id) ? (int)$site_id : 0);
    $tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
    if ($targetSiteId > 0) {
        $qNum = mysqli_query($connection, "SELECT site_configuration_value FROM {$tblPrefix}site_configuration WHERE site_configuration_site_id = {$targetSiteId} AND site_configuration_key = 'admin_whatsapp_number' AND site_configuration_status = 1 LIMIT 1");
        if ($qNum && $rNum = mysqli_fetch_assoc($qNum)) {
            $targetNumber = trim($rNum['site_configuration_value']);
        }
    }
}
if (empty($targetNumber) && isset($site_mobile)) {
    $targetNumber = $site_mobile;
}

$cleanNumber = preg_replace('/[^0-9]/', '', (string)$targetNumber);
if (strlen($cleanNumber) === 11 && substr($cleanNumber, 0, 1) === '0') {
    $cleanNumber = substr($cleanNumber, 1);
}
$sendMessageTo = $cleanNumber;

if (defined('COMPANY_WHATSAPP_USERNAME') && !empty(COMPANY_WHATSAPP_USERNAME)) {
    $whatsappAdmin = COMPANY_WHATSAPP_USERNAME;
}
if (defined('COMPANY_WHATSAPP_PASSWORD') && !empty(COMPANY_WHATSAPP_PASSWORD)) {
    $whatsappPassword = COMPANY_WHATSAPP_PASSWORD;
}

// Send to WhatsApp
require WHATSAPP_TEMPLATE . 'footer.php';