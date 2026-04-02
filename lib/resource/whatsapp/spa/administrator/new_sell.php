<?php

require_once 'header.php';

$templateName = 'spa_new_sell';

$link = SITE_URL . '/dashboard/sell/view/?id=' . base64_encode($membershipId);

// Build the message parameters string
$variablesValues = SITE_ADMIN_NAME . ',' . ucwords($name) . ',' . ucwords($category_title) . ',' . 'INR ' . $price . ',' . 'INR ' . $link;

// Remove accidental trailing comma if any
$variablesValues = rtrim($variablesValues, ',');


if (defined('ADMIN_WHATSAPP_NUMBER')) {
    $sendMessageTo = ADMIN_WHATSAPP_NUMBER;
} else {
    $sendMessageTo = $site_mobile;
}

// Send to WhatsApp
require WHATSAPP_TEMPLATE . 'footer.php';