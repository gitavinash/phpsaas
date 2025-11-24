<?php

if (defined('COMPANY_WHATSAPP_PAYMENT_REQUEST')) {

    $templateName = COMPANY_WHATSAPP_PAYMENT_REQUEST;

    if ($templateName != '') {
        // Helper to sanitize any input (convert array to string, trim strings)
        function sanitize_input($value, $default = 'N/A')
        {
            if (is_array($value)) {
                return implode(' ', array_map('trim', $value));
            }
            return trim($value ?? $default);
        }

        // Clean and sanitize values
        $name   = sanitize_input($profile_display_name ?? '');
        $tourPackage      = str_replace(['tour', 'package'], '', $tour_package_title);
        $paymentLink      = sanitize_input($paymentLink ?? '');

        // Build final param string for WhatsApp API
        $variablesValues =
            $name . "," .
            $tourPackage . "," .
            $paymentLink;

        $variablesValues = rtrim($variablesValues, ',');

        $sendMessageTo = $mobile;

        $whatsappAdmin = ((defined('COMPANY_WHATSAPP_USERNAME')) ? COMPANY_WHATSAPP_USERNAME : 'Aleya_BWA');
        $whatsappPassword = ((defined('COMPANY_WHATSAPP_PASSWORD')) ? COMPANY_WHATSAPP_PASSWORD : '123456');

        // Optional: Send the request or include tracking/confirmation template
        require WHATSAPP_TEMPLATE . 'footer.php';
    }
}