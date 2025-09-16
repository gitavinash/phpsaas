<?php

if (defined('COMPANY_WHATSAPP_ITINERARY_SENT')) {

    $templateName = COMPANY_WHATSAPP_ITINERARY_SENT;

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
        $tourDuration = $totalDays . ' Nights / ' . $totalDays . ' Days';
        $paymentLink      = 'https://d2h.in/56drt';

        // Build final param string for WhatsApp API
        $variablesValues =
            $safeName . "," .
            $safeTour . "," .
            $tourDuration . "," .
            'INR ' . $itineraryPrice . "," .
            $viewUrl . "," .
            $pdfUrl;

        $variablesValues = rtrim($variablesValues, ',');

        $sendMessageTo = $mobile;

        $whatsappAdmin = ((defined('COMPANY_WHATSAPP_USERNAME')) ? COMPANY_WHATSAPP_USERNAME : 'Aleya_BWA');
        $whatsappPassword = ((defined('COMPANY_WHATSAPP_PASSWORD')) ? COMPANY_WHATSAPP_PASSWORD : '123456');

        // Optional: Send the request or include tracking/confirmation template
        require WHATSAPP_TEMPLATE . 'footer.php';
    }
}