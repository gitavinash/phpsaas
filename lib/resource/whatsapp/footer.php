<?php

if ($sendMessageTo != '') {

    $sentViaDevice = false;
    $shouldBypassDevice = (!empty($bypassDevice) || (isset($templateName) && $templateName === 'aleya_bw_1'));

    $siteIdVal = defined('SITE_ID') ? (int)SITE_ID : 0;
    if ($siteIdVal <= 0 && isset($siteId)) {
        $siteIdVal = (int)$siteId;
    }
    if (!$shouldBypassDevice && isset($connection) && $siteIdVal > 0) {
        $tblPfx = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
        $devChk = mysqli_query($connection, "SELECT device_id FROM {$tblPfx}whatsapp_device WHERE device_site_id = {$siteIdVal} AND device_status = 'connected' LIMIT 1");
        if ($devChk && mysqli_num_rows($devChk) > 0) {
            $helperPath = (defined('ROOT') ? ROOT : dirname(__DIR__, 2)) . '/api/v3/whatsapp/device_helper.php';
            if (file_exists($helperPath)) {
                require_once $helperPath;
                if (function_exists('sendWhatsAppDirectMessage')) {
                    $rawVars = !empty($variablesValues) ? explode(',', $variablesValues) : [];
                    $siteNameVal = defined('SITE_NAME') ? SITE_NAME : 'Aleya';
                    $msgBody = '';

                    if (stripos($templateName, 'enquiry') !== false || stripos($templateName, 'thank') !== false) {
                        // Automatic customer enquiry greetings are disabled by default; strictly handled via CRM Workflow Automation
                        $sentViaDevice = true;
                        $response = 'Automatic enquiry message skipped (handled via Workflow)';
                    } elseif (stripos($templateName, 'payment') !== false) {
                        $cName = trim($rawVars[0] ?? 'Valued Customer');
                        $cPkg = trim($rawVars[1] ?? 'Tour Package');
                        $cAmt = trim($rawVars[2] ?? '');
                        $cLink = trim($rawVars[3] ?? '');
                        $msgBody = "Hello {$cName},\n\nGreetings from *{$siteNameVal}*!\nPlease find your payment details for *{$cPkg}*" . (!empty($cAmt) ? " - Amount: {$cAmt}" : "") . ".\n" . (!empty($cLink) ? "\n💳 *Payment Link:* {$cLink}\n" : "") . "\nWarm regards,\n*{$siteNameVal}*";
                    } elseif (stripos($templateName, 'itinerary') !== false) {
                        $cName = trim($rawVars[0] ?? 'Valued Customer');
                        $cTour = trim($rawVars[1] ?? 'Tour Proposal');
                        $cDur  = trim($rawVars[2] ?? '');
                        $cPrice = trim($rawVars[3] ?? '');
                        $cView = trim($rawVars[4] ?? '');
                        $msgBody = "🌟 *Tour Proposal & Itinerary*\n\nDear {$cName},\n\nGreetings from *{$siteNameVal}*!\nWe are pleased to share your customized tour proposal for *{$cTour}*." . (!empty($cDur) ? "\n📅 *Duration:* {$cDur}" : "") . (!empty($cPrice) ? "\n💰 *Package Price:* {$cPrice}" : "") . (!empty($cView) ? "\n\n🌐 *View Proposal:* {$cView}" : "") . "\n\nFeel free to reply here for any modifications.\n\nWarm regards,\n*{$siteNameVal}*";
                    } else {
                        $msgBody = "Hello,\n\nGreetings from *{$siteNameVal}*!\n" . implode("\n", array_filter(array_map('trim', $rawVars))) . "\n\nWarm regards,\n*{$siteNameVal}*";
                    }

                    if (!empty($msgBody)) {
                        $senderUserVal = defined('USER_ID') ? (int)USER_ID : 0;
                        if ($senderUserVal <= 0 && isset($userId)) {
                            $senderUserVal = (int)$userId;
                        }
                        $leadIdVal = isset($leadId) ? (int)$leadId : (isset($enquiry_id) ? (int)$enquiry_id : 0);
                        $sendOpts = [
                            'lead_id' => $leadIdVal,
                            'recipient_phone' => $sendMessageTo
                        ];
                        $directRes = sendWhatsAppDirectMessage($connection, $siteIdVal, $senderUserVal, $sendMessageTo, $msgBody, $sendOpts);
                        if ($directRes && isset($directRes['status']) && $directRes['status'] === 'success') {
                            $sentViaDevice = true;
                            $response = 'success (via connected WhatsApp Web device)';
                        }
                    }
                }
            }
        }
    }

    if (!$sentViaDevice) {
        $url = "https://bhashsms.com/api/sendmsgutil.php";

        $cleanPhone = preg_replace('/[^0-9]/', '', (string)$sendMessageTo);
        if (strlen($cleanPhone) === 11 && substr($cleanPhone, 0, 1) === '0') {
            $cleanPhone = substr($cleanPhone, 1);
        }
        $bhashUser = !empty($whatsappAdmin) ? $whatsappAdmin : (defined('COMPANY_WHATSAPP_USERNAME') ? COMPANY_WHATSAPP_USERNAME : "Aleya_BWA");
        $bhashPass = !empty($whatsappPassword) ? $whatsappPassword : (defined('COMPANY_WHATSAPP_PASSWORD') ? COMPANY_WHATSAPP_PASSWORD : "123456");

        // Encode all other values normally
        $query = http_build_query([
            "user"     => $bhashUser,
            "pass"     => $bhashPass,
            "sender"   => "BUZWAP",
            "phone"    => $cleanPhone,
            "text"     => $templateName,
            "priority" => "wa",
            "stype"    => "normal"
        ]);

        // But Params: allow commas, encode only URL-sensitive characters (e.g., space → %20, & → %26)
        $safeParams = str_replace(
            [' ', '+', '&'],
            ['%20', '%20', '%26'],
            $variablesValues
        );

        // Combine manually
        $fullUrl = $url . '?' . $query . '&Params=' . $safeParams;

        // Perform GET request
        $response = @file_get_contents($fullUrl);
        if ($response === false || $response === null) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $fullUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);
        }
    }

    // Prepare log data
    $logStatus = (strpos(strtolower((string)$response), 'success') !== false) ? 'success' : 'failed';
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'phone' => $sendMessageTo,
        'template' => $templateName,
        'status' => $logStatus,
        'response' => $response
    ];

    // Create logs directory if not exists
    $baseRootDir = defined('ROOT') ? rtrim(ROOT, '/') : dirname(__DIR__, 2);
    $logDir = $baseRootDir . '/logs/whatsapp-logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }

    // Format as table row and append to log file
    $logFile = $logDir . '/whatsapp-logs.txt';
    $logLine = sprintf(
        "| %-19s | %-15s | %-30s | %-8s | %-50s |\n",
        $logEntry['timestamp'],
        $logEntry['phone'],
        $logEntry['template'],
        $logEntry['status'],
        substr($logEntry['response'], 0, 50)
    );

    // Add header if file doesn't exist
    if (!file_exists($logFile)) {
        $header = sprintf(
            "| %-19s | %-15s | %-30s | %-8s | %-50s |\n",
            'Timestamp',
            'Phone',
            'Template',
            'Status',
            'Response'
        );
        file_put_contents($logFile, $header, FILE_APPEND);
    }

    file_put_contents($logFile, $logLine, FILE_APPEND);
}
