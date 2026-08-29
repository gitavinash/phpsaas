<?php

/**
 * V3 API Endpoint: Process 5-Minute Follow-Up Reminders
 * Path: /api/v3/cron/followup-reminders.php
 */

header("Content-type: application/json; charset=utf-8");

$cronScript = dirname(__DIR__, 3) . '/cron/followupReminders.php';
if (!file_exists($cronScript)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Cron script not found.'
    ]);
    exit();
}

ob_start();
include $cronScript;
$output = ob_get_clean();

echo json_encode([
    'status' => 'success',
    'message' => 'Follow-up reminders processed successfully.',
    'output' => trim($output)
]);
exit();
