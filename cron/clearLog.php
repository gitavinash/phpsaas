<?php
// Path to the log file
$logFile = __DIR__ . '/../logs/error/cron.log';

// Check if file exists
if (file_exists($logFile)) {
    $fileSize = filesize($logFile); // in bytes
    $maxSize = 10 * 1024 * 1024; // 10 MB in bytes

    // Remove lines older than 1 week
    $oneWeekAgo = strtotime('-7 days');
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $filtered = [];
    foreach ($lines as $line) {
        if (preg_match('/^\\[(.*?)\\]/', $line, $matches)) {
            $dateStr = $matches[1];
            $date = DateTime::createFromFormat('Y-m-d H:i:s T', $dateStr);
            if (!$date) {
                $date = DateTime::createFromFormat('Y-m-d H:i:s', $dateStr);
            }
            if ($date && $date->getTimestamp() >= $oneWeekAgo) {
                $filtered[] = $line;
            }
        } else {
            $filtered[] = $line; // keep lines without a date
        }
    }
    // If file is too large or lines were removed, rewrite file
    if ($fileSize >= $maxSize || count($filtered) < count($lines)) {
        file_put_contents($logFile, implode("\n", $filtered) . "\n");
        error_log(date('[Y-m-d H:i:s]') . " [INFO] cron.log cleaned: size={$fileSize}B, lines_removed=" . (count($lines) - count($filtered)) . "\n", 3, __DIR__ . '/../logs/error/clearLog-action.log');
    }
} else {
    echo "Log file does not exist: $logFile\n";
}