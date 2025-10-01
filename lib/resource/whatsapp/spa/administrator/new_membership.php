<?php

require_once 'header.php';

$templateName = 'spa_new_membership';

$link = SITE_URL . '/dashboard/membership/update/?id=' . base64_encode($membershipId);

// Build the message parameters string
$variablesValues = ((isset($_COOKIE['user_name'])) ? base64_decode($_COOKIE['user_name']) : 'User') . ',' . $name . ',' . $category_title . ',' . 'INR ' . $price . ',' . 'INR ' . $dueAmount . ',' . $visit . ',' . $link;

// Remove accidental trailing comma if any
$variablesValues = rtrim($variablesValues, ',');


if (defined('ADMIN_WHATSAPP_NUMBER')) {
    $sendMessageTo = ADMIN_WHATSAPP_NUMBER;
} else {
    $sendMessageTo = $site_mobile;
}


// Send to WhatsApp
require WHATSAPP_TEMPLATE . 'footer.php';