<?php

if (isset($company_whatsapp_enquiry_thank_you)) {

    $templateName = (($company_whatsapp_enquiry_thank_you != '') ? $company_whatsapp_enquiry_thank_you : 'aleya_customer_enquiry_confirmation');

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

    // Build final param string for WhatsApp API
    $variablesValues =
        $name . "," .
        $subject . "," .
        $travelDate . "," .
        $mobile . "," .
        $email . "," .
        $requirements . "," .
        SITE_NAME . "," .
        ((defined('company_site_domain')) ? COMPANY_SITE_DOMAIN : SITE_URL) . "," .
        ((defined('admin_whatsapp_number')) ? ADMIN_WHATSAPP_NUMBER : SITE_URL) . "," .
        ((defined('SITE_EMAIL')) ? SITE_EMAIL : $site_email);

    $variablesValues = rtrim($variablesValues, ',');

    $sendMessageTo = $mobile;

    $whatsappAdmin = ((defined('COMPANY_WHATSAPP_USERNAME')) ? COMPANY_WHATSAPP_USERNAME : 'Aleya_BWA');
    $whatsappPassword = ((defined('COMPANY_WHATSAPP_PASSWORD')) ? COMPANY_WHATSAPP_PASSWORD : '123456');

    // Optional: Send the request or include tracking/confirmation template
    require WHATSAPP_TEMPLATE . 'footer.php';
    exit();
}