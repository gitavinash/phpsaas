<?php

require_once 'header.php';

$templateName = 'spa_new_membership';

$longUrl = SITE_URL . '/transaction/view/?id=' . base64_encode($transactionId);
$transactionLink = 'https://2dh.in/' . base64_encode($transactionId);

// Build the message parameters string
$variablesValues = SITE_ADMIN_NAME . ',' . $transactionId . ',' . $name . ',INR ' . $paymentAmount . ',' . date('jS M Y h:i A') . ',' . $paymentMode . ',' . $longUrl;

// Remove accidental trailing comma if any
$variablesValues = rtrim($variablesValues, ',');


if (defined('ADMIN_WHATSAPP_NUMBER')) {
    $sendMessageTo = ADMIN_WHATSAPP_NUMBER;
} else {
    $sendMessageTo = $site_mobile;
}

// Send to WhatsApp
require WHATSAPP_TEMPLATE . 'footer.php';