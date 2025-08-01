<?php

require_once 'header.php';

$templateName = 'aleya_bw_1';

// Sanitize and ensure all values are strings
$name = (isset($first_name) ? trim($first_name) : '') . ' ' . (isset($last_name) ? trim($last_name) : '');
$name = trim($name) !== '' ? $name : 'N/A';

$subject = is_array($subject) ? implode(' ', $subject) : trim($subject ?? '');
$subject = $subject !== '' ? $subject : 'N/A';

$travelDates = isset($travel_date) && trim($travel_date) !== '' ? trim($travel_date) : 'N/A';

$email = isset($email) && trim($email) !== '' ? trim($email) : 'N/A';

$requirements = isset($requirements) && trim($requirements) !== '' ? trim($requirements) : 'N/A';

$link = ((isset($site_domain)) ? $site_domain  : SITE_URL) . '/dashboard/leads/view/?id=' . base64_encode($leadId);


// Build the message parameters string
$variablesValues = $subject . ',' . $travelDates . ',' . $name . ',' . $mobile . ',' . $email . ',' . $requirements . ',' . $link;

// Remove accidental trailing comma if any
$variablesValues = rtrim($variablesValues, ',');


if (defined('SITE_MOBILE')) {
    $sendMessageTo = SITE_MOBILE;
} else {
    $sendMessageTo = $site_mobile;
}


// Send to WhatsApp
require WHATSAPP_TEMPLATE . 'footer.php';