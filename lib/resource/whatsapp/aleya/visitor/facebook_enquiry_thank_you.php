<?php

require_once 'header.php';

$templateName = 'aleya_customer_enquiry_confirmation';

// Helper to sanitize any input (convert array to string, trim strings)
function sanitize_input($value, $default = 'N/A')
{
    if (is_array($value)) {
        return implode(' ', array_map('trim', $value));
    }
    return trim($value ?? $default);
}

// Clean and sanitize values
$subjectRaw   = sanitize_input($subject ?? '');
$cleaned      = str_replace(['tour', 'package'], '', $subjectRaw);
$subject      = preg_replace('/\s+/', ' ', $cleaned);
$name         = sanitize_input($name ?? 'User');
$travelDate   = sanitize_input($travel_date ?? 'N/A');
$mobile       = sanitize_input($mobile ?? 'N/A');
$email        = sanitize_input($email ?? 'N/A');
$requirements = sanitize_input($requirements ?? '');

if (defined('SITE_MOBILE')) {
    $site_mobile = SITE_MOBILE;
}

if (isset($site_mobile)) {

    // Build final param string for WhatsApp API
    $variablesValues = implode(',', [
        $name,
        $subject,
        $travelDate,
        $mobile,
        $email,
        $requirements,
        SITE_NAME,
        SITE_URL,
        $site_mobile,
        ((defined('SITE_EMAIL')) ? SITE_EMAIL : $site_email)
    ]);

    // Optional safety trim (not needed with implode but kept just in case)
    $variablesValues = rtrim($variablesValues, ',');

    // Build full WhatsApp API request URL
    $fullUrl = "https://bhashsms.com/api/sendmsgutil.php?" . http_build_query([
        'user'     => 'Aleya_BWA',
        'pass'     => '123456',
        'sender'   => 'BUZWAP',
        'phone'    => $mobile,
        'text'     => 'aleya_bw_1',
        'priority' => 'wa',
        'stype'    => 'normal',
        'Params'   => $variablesValues
    ]);

    $sendMessageTo = $mobile;

    // Optional: Send the request or include tracking/confirmation template
    require WHATSAPP_TEMPLATE . 'footer.php';
}