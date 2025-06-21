<?php

require_once 'header.php';

$templateName = 'aleya_bw_1';

// Sanitize and ensure all values are strings
$name = ((isset($first_name)) ? $first_name : '') . ' ' . ((isset($last_name)) ? $last_name : '');
$subject = is_array($subject) ? implode(' ', $subject) : trim($subject ?? 'N/A');
$travelDates = isset($travel_date) ? $travel_date : 'N/A';
$email = isset($email) ? $email : '';
$requirements = isset($requirements) ? $requirements : 'N/A';
$link = SITE_URL . '/dashboard/leads/view/?id=' . base64_encode($leadId);

// Build the message parameters string
$variablesValues = $subject . ',' . $travelDates . ',' . $name . ',' . $mobile . ',' . $email . ',' . $requirements . ',' . $link;

// Remove accidental trailing comma if any
$variablesValues = rtrim($variablesValues, ',');

// Send to WhatsApp
require WHATSAPP_TEMPLATE . 'footer.php';