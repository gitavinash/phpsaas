<?php
// Path to the log file
$logFile = __DIR__ . '/../logs/error/cron.log';

// Check if file exists
if (file_exists($logFile)) {
    $fileSize = filesize($logFile); // in bytes
    $maxSize = 10 * 1024 * 1024; // 10 MB in bytes

    // Count lines efficiently
    $lineCount = 0;
    $handle = fopen($logFile, 'r');
    if ($handle) {
        while (!feof($handle)) {
            $line = fgets($handle);
            $lineCount++;
            if ($lineCount > 10000) {
                break;
            }
        }
        fclose($handle);
    }

    // Clear file if size >= 10MB or lines > 10000
    if ($fileSize >= $maxSize || $lineCount > 10000) {
        file_put_contents($logFile, '');
        error_log(date('[Y-m-d H:i:s]') . " [INFO] cron.log cleared: size={$fileSize}B, lines={$lineCount}\n", 3, __DIR__ . '/../logs/error/clearLog-action.log');
    }
} else {
    echo "Log file does not exist: $logFile\n";
}
