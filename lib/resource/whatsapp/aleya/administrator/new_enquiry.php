<?php

require_once 'header.php';

$templateName = 'aleya_bw_1';

// Sanitize and ensure all values are strings
$subject       = is_array($name) ? implode(' ', $name) : trim($name ?? 'N/A');
$subject       = is_array($subject) ? implode(' ', $subject) : trim($subject ?? 'N/A');
$travelDates   = isset($travel_date) ? is_array($travel_date) ? implode(' ', $travel_date) : trim($travelDates ?? 'N/A') : 'N/A';
$email         = isset($email) ? $email : '';
$requirements  = isset($requirements) ? is_array($requirements) ? implode(' ', $requirements) : trim($requirements ?? 'N/A') : 'N/A';
$link          = SITE_URL . '/dashboard/leads/view/?id=' . base64_encode($leadId);

// Build the message parameters string
$variablesValues = $subject . ',' . $travelDates . ',' . $name . ',' . $mobile . ',' . $email . ',' . $requirements . ',' . $link;

// Remove accidental trailing comma if any
$variablesValues = rtrim($variablesValues, ',');

// Send to WhatsApp
require_once WHATSAPP_TEMPLATE . 'footer.php';