<?php

require_once 'header.php';

$templateName = 'aleya_bw_1';
$bypassDevice = true; // Exclusively send via BhashSMS WhatsApp Business API (bypass WhatsApp Web)

// Sanitize and ensure all values are strings
$name = (isset($first_name) ? trim($first_name) : '') . ' ' . (isset($last_name) ? trim($last_name) : '');
$name = trim($name) !== '' ? $name : 'N/A';

// Format lead alert title for admin
$rawSubject = is_array($subject) ? implode(' ', $subject) : trim((string)($subject ?? ''));
if ($rawSubject === '' || strcasecmp($rawSubject, 'Facebook Lead') === 0 || strcasecmp($rawSubject, 'enquiry') === 0 || strcasecmp($rawSubject, 'N/A') === 0) {
    $subject = 'New Lead Alert (Facebook)';
} else {
    $subject = 'New Lead Alert: ' . $rawSubject;
}

$travelDates = isset($travel_date) && trim($travel_date) !== '' ? trim($travel_date) : 'N/A';

$email = isset($email) && trim($email) !== '' ? trim($email) : 'N/A';

$requirements = isset($requirements) && trim($requirements) !== '' ? trim($requirements) : 'N/A';

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

$siteUrl = (isset($site_domain) && !empty($site_domain)) ? $site_domain : (defined('SITE_URL') ? SITE_URL : '');
$leadIdEncoded = isset($leadId) ? base64_encode($leadId) : '';
$link = $siteUrl . '/dashboard/leads/view/?id=' . $leadIdEncoded;

$paramSubject      = cleanAleyaBw1Param($subject, 'New Lead Alert (Facebook)');
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
} elseif (defined('NOTIFICATION_ADMIN_WHATSAPP_NUMBER') && !empty(NOTIFICATION_ADMIN_WHATSAPP_NUMBER)) {
    $targetNumber = NOTIFICATION_ADMIN_WHATSAPP_NUMBER;
} elseif (defined('COMPANY_WHATSAPP') && !empty(COMPANY_WHATSAPP)) {
    $targetNumber = COMPANY_WHATSAPP;
}

if (empty($targetNumber) && isset($connection)) {
    $targetSiteId = defined('SITE_ID') && (int)SITE_ID > 0 ? (int)SITE_ID : (isset($site_id) ? (int)$site_id : 0);
    $ownerId = isset($site_owner_id) ? (int)$site_owner_id : 0;
    $parentSiteId = defined('APP_SITE_ID') ? (int)APP_SITE_ID : 0;
    $siteIds = array_unique(array_filter([$targetSiteId, $ownerId, $parentSiteId]));
    if (!empty($siteIds)) {
        $siteIdList = implode(',', $siteIds);
        $tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
        $qNum = mysqli_query($connection, "SELECT site_configuration_key, site_configuration_value FROM {$tblPrefix}site_configuration WHERE site_configuration_site_id IN ({$siteIdList}) AND site_configuration_key IN ('notification_admin_whatsapp_number', 'admin_whatsapp_number', 'notification_company_whatsapp_number', 'company_whatsapp_number', 'notification_company_whatsapp', 'company_whatsapp', 'company_mobile') AND site_configuration_status = 1 AND site_configuration_value != '' ORDER BY CASE site_configuration_key WHEN 'notification_admin_whatsapp_number' THEN 1 WHEN 'admin_whatsapp_number' THEN 2 WHEN 'notification_company_whatsapp_number' THEN 3 WHEN 'company_whatsapp_number' THEN 4 ELSE 5 END ASC LIMIT 1");
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