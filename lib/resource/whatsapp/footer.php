<?php

if ($sendMessageTo != '') {

    $url = "https://bhashsms.com/api/sendmsgutil.php";

    // Encode all other values normally
    $query = http_build_query([
        "user"     => ((isset($whatsappAdmin)) ? $whatsappAdmin : "Aleya_BWA"),
        "pass"     => ((isset($whatsappPassword)) ? $whatsappPassword : "123456"),
        "sender"   => "BUZWAP",
        "phone"    => $sendMessageTo,
        "text"     => $templateName,
        "priority" => "wa",
        "stype"    => "normal"
    ]);

    // But Params: allow commas, encode only URL-sensitive characters (e.g., space → %20)
    $safeParams = str_replace(
        [' ', '+'],
        ['%20', '%20'], // ensure space stays as %20, not "+"
        $variablesValues
    );

    // Combine manually
    $fullUrl = $url . '?' . $query . '&Params=' . $safeParams;

    // Perform GET request
    $response = file_get_contents($fullUrl);

    // Prepare log data
    $logStatus = (strpos($response, 'success') !== false) ? 'success' : 'failed';
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'phone' => $sendMessageTo,
        'template' => $templateName,
        'status' => $logStatus,
        'response' => $response
    ];

    // Create logs directory if not exists
    $logDir = ROOT . 'logs/whatsapp-logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
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
