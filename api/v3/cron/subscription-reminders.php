<?php

/**
 * V3 API Endpoint: Process Site Subscription Expiration Reminders
 * Path: /api/v3/cron/subscription-reminders.php
 *
 * Query Parameters:
 *   - dry_run=1         : Simulate candidate selection without sending emails
 *   - force_window=1    : Bypass the 10:00 AM - 11:50 AM morning window check
 *   - force=1           : Bypass both window and deduplication checks
 *   - site_id=N         : Target a specific site ID
 *   - stage=X           : Target a specific stage ('7_days_before', '3_days_before', etc.)
 *   - date=YYYY-MM-DD   : Simulate execution on a specific date
 *   - test_email=xyz    : Override recipient for test deliveries (e.g. avinash@blumox.com)
 */

header("Content-Type: application/json; charset=utf-8");

$cronScript = dirname(__DIR__, 3) . '/cron/subscriptionReminders.php';
if (!file_exists($cronScript)) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Cron script not found: ' . $cronScript
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit();
}

ob_start();
include $cronScript;
$consoleOutput = ob_get_clean();

$results = $GLOBALS['subscriptionReminderOutput'] ?? null;

echo json_encode([
    'status'    => 'success',
    'message'   => 'Site subscription reminders executed.',
    'summary'   => $results['summary'] ?? [],
    'output'    => trim($consoleOutput),
    'processed' => $results['processed'] ?? []
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
exit();

