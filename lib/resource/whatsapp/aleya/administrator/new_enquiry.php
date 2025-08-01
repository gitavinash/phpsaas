<?php

require_once 'header.php';

$templateName = 'aleya_bw_1';

// Sanitize and ensure all values are strings
// Combine first and last name, then check if it's blank
$name = (isset($first_name) ? trim($first_name) : '') . ' ' . (isset($last_name) ? trim($last_name) : '');
$name = trim($name) !== '' ? $name : 'N/A';

// Check if subject is array or string, then handle blank
$subject = is_array($subject) ? implode(' ', array_map('trim', $subject)) : trim($subject ?? '');
$subject = $subject !== '' ? $subject : 'N/A';

// Check travel date
$travelDates = isset($travel_date) ? trim($travel_date) : '';
$travelDates = $travelDates !== '' ? $travelDates : 'N/A';

// Check email
$email = isset($email) ? trim($email) : '';
$email = $email !== '' ? $email : 'N/A';

// Check requirements
$requirements = isset($requirements) ? trim($requirements) : '';
$requirements = $requirements !== '' ? $requirements : 'N/A';

// Generate link
$link = SITE_URL . '/dashboard/leads/view/?id=' . base64_encode($leadId);


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