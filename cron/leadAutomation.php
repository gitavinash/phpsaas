<?php
/**
 * Cron Job: CRM Lead Follow-Up, No-Response Escalation & Automation Processor
 * Path: /cron/leadAutomation.php
 * 
 * Description:
 * 1. Synchronizes initial contact tasks for newly created uncontacted enquiries.
 * 2. Recalculates customer response status across active leads (24h, 48h, 7d no-response).
 * 3. Escalates 48h no-response leads with urgent follow-up tasks (idempotent).
 * 4. Flags 7d unresponsive leads for nurture/manager review.
 * 5. Links converted bookings to leads and terminates sales alerts.
 */

$logFile = __DIR__ . '/cron.log';
$logTimezone = new DateTimeZone('Asia/Kolkata');
$logRunId = (new DateTime('now', $logTimezone))->format('Ymd_His') . '_' . substr(md5(uniqid('', true)), 0, 8);

function formatLogContext(array $context) {
    if (empty($context)) return '';
    $parts = [];
    foreach ($context as $k => $v) {
        if (is_bool($v)) $v = $v ? 'true' : 'false';
        elseif (is_null($v)) $v = 'null';
        elseif (is_array($v) || is_object($v)) $v = json_encode($v);
        $parts[] = "{$k}={$v}";
    }
    return implode(' | ', $parts);
}

function writeCronLog($level, $event, $context = []) {
    global $logFile, $logRunId, $logTimezone;
    $filename = basename(__FILE__);
    $timestamp = (new DateTime('now', $logTimezone))->format('Y-m-d H:i:s') . ' IST';
    $prefix = "[{$timestamp}] [" . strtoupper($level) . "] [file={$filename}] [run={$logRunId}] {$event}";
    $ctx = formatLogContext((array)$context);
    $line = $prefix . ($ctx !== '' ? " | {$ctx}" : '') . PHP_EOL;
    @file_put_contents($logFile, $line, FILE_APPEND);
}

writeCronLog('info', 'Started: leadAutomation.php CRM workflow processor');

$dbFile = dirname(__DIR__) . '/dbConfig.php';
if (!file_exists($dbFile)) {
    writeCronLog('error', 'dbConfig.php not found', ['path' => $dbFile]);
    exit("dbConfig not found\n");
}

require_once $dbFile;
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$connection) {
    writeCronLog('error', 'Database connection failed', ['error' => mysqli_connect_error()]);
    exit("DB connection failed\n");
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
require_once dirname(__DIR__) . '/api/v3/leads/status-config.php';

// 1. Initial Contact Task for New Enquiries without tasks (within last 3 days)
$newLeadsSql = "SELECT e.enquiry_id, e.enquiry_site_id, e.enquiry_agent, e.enquiry_first_name, e.enquiry_last_name 
                FROM {$tblPrefix}enquiry e
                LEFT JOIN {$tblPrefix}enquiry_task t ON e.enquiry_id = t.task_enquiry_id
                WHERE e.enquiry_status IN (0, 2)
                  AND t.task_id IS NULL
                  AND e.enquiry_time >= DATE_SUB(NOW(), INTERVAL 3 DAY)
                LIMIT 50";

$initialTasksCreated = 0;
if ($nRes = mysqli_query($connection, $newLeadsSql)) {
    while ($nRow = mysqli_fetch_assoc($nRes)) {
        $lId = (int)$nRow['enquiry_id'];
        $sId = (int)($nRow['enquiry_site_id'] ?? 0);
        $aId = (int)($nRow['enquiry_agent'] ?? 0);
        $dueAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        $insSql = "INSERT INTO {$tblPrefix}enquiry_task 
                   (task_site_id, task_enquiry_id, task_agent_id, task_type, task_due_at, task_notes, task_next_action, task_status) 
                   VALUES 
                   ({$sId}, {$lId}, {$aId}, 'Call', '{$dueAt}', 'Initial contact for new inquiry', 'Call client to collect requirements', 'pending')";
        if (mysqli_query($connection, $insSql)) {
            $initialTasksCreated++;
        }
    }
}
if ($initialTasksCreated > 0) {
    writeCronLog('info', "Created {$initialTasksCreated} initial contact tasks for new enquiries");
}

// 2. Link converted bookings to leads
$linkBookingsSql = "UPDATE {$tblPrefix}enquiry e
                    INNER JOIN {$tblPrefix}booking b ON e.enquiry_id = b.booking_enquiry_id
                    SET e.enquiry_booking_id = b.booking_id,
                        e.enquiry_response_status = 'No Activity'
                    WHERE (e.enquiry_booking_id IS NULL OR e.enquiry_booking_id = 0)
                      AND b.booking_id > 0";
mysqli_query($connection, $linkBookingsSql);

// 3. Process No-Response status transitions on active leads
// Exclude closed statuses: 1, 4, 5, 8, 11, 12, 13, 14, 15, 16, 17, 18, 28, 29
$closedList = "1, 4, 5, 8, 11, 12, 13, 14, 15, 16, 17, 18, 28, 29";

// 3a. 24 Hours No-Response (Contacted between 24h and 48h ago, no reply)
$update24hSql = "UPDATE {$tblPrefix}enquiry
                 SET enquiry_response_status = 'No Response – 24 Hours'
                 WHERE enquiry_status NOT IN ({$closedList})
                   AND enquiry_last_contacted_at IS NOT NULL
                   AND enquiry_last_contacted_at <= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                   AND enquiry_last_contacted_at > DATE_SUB(NOW(), INTERVAL 48 HOUR)
                   AND (enquiry_last_customer_response_at IS NULL OR enquiry_last_customer_response_at < enquiry_last_contacted_at)
                   AND enquiry_response_status != 'No Response – 24 Hours'";
mysqli_query($connection, $update24hSql);
$updated24h = mysqli_affected_rows($connection);

// 3b. 48 Hours No-Response (Contacted between 48h and 7d ago, no reply)
$update48hSql = "UPDATE {$tblPrefix}enquiry
                 SET enquiry_response_status = 'No Response – 48 Hours'
                 WHERE enquiry_status NOT IN ({$closedList})
                   AND enquiry_last_contacted_at IS NOT NULL
                   AND enquiry_last_contacted_at <= DATE_SUB(NOW(), INTERVAL 48 HOUR)
                   AND enquiry_last_contacted_at > DATE_SUB(NOW(), INTERVAL 7 DAY)
                   AND (enquiry_last_customer_response_at IS NULL OR enquiry_last_customer_response_at < enquiry_last_contacted_at)
                   AND enquiry_response_status != 'No Response – 48 Hours'";
mysqli_query($connection, $update48hSql);
$updated48h = mysqli_affected_rows($connection);

// 3c. 7 Days No-Response (Contacted > 7 days ago, no reply)
$update7dSql = "UPDATE {$tblPrefix}enquiry
                SET enquiry_response_status = 'No Response – 7 Days'
                WHERE enquiry_status NOT IN ({$closedList})
                  AND enquiry_last_contacted_at IS NOT NULL
                  AND enquiry_last_contacted_at <= DATE_SUB(NOW(), INTERVAL 7 DAY)
                  AND (enquiry_last_customer_response_at IS NULL OR enquiry_last_customer_response_at < enquiry_last_contacted_at)
                  AND enquiry_response_status != 'No Response – 7 Days'";
mysqli_query($connection, $update7dSql);
$updated7d = mysqli_affected_rows($connection);

// 4. Create Escalated Follow-Up Reminder for 48h unresponsive leads if no pending task exists (idempotent)
$escalateSql = "SELECT e.enquiry_id, e.enquiry_site_id, e.enquiry_agent 
                FROM {$tblPrefix}enquiry e
                LEFT JOIN {$tblPrefix}enquiry_task t ON e.enquiry_id = t.task_enquiry_id AND t.task_status = 'pending'
                WHERE e.enquiry_response_status = 'No Response – 48 Hours'
                  AND e.enquiry_status NOT IN ({$closedList})
                  AND t.task_id IS NULL
                LIMIT 30";
$escalatedCreated = 0;
if ($eRes = mysqli_query($connection, $escalateSql)) {
    while ($eRow = mysqli_fetch_assoc($eRes)) {
        $lId = (int)$eRow['enquiry_id'];
        $sId = (int)($eRow['enquiry_site_id'] ?? 0);
        $aId = (int)($eRow['enquiry_agent'] ?? 0);
        $dueAt = date('Y-m-d H:i:s', strtotime('+2 hours'));

        $insEscSql = "INSERT INTO {$tblPrefix}enquiry_task 
                      (task_site_id, task_enquiry_id, task_agent_id, task_type, task_due_at, task_notes, task_next_action, task_status) 
                      VALUES 
                      ({$sId}, {$lId}, {$aId}, 'Call', '{$dueAt}', 'Escalated: No customer response for 48 hours', 'Urgent callback / alternative channel reachout', 'pending')";
        if (mysqli_query($connection, $insEscSql)) {
            $escalatedCreated++;
        }
    }
}

writeCronLog('info', 'Completed: leadAutomation.php processor', [
    'updated_24h'       => max(0, $updated24h),
    'updated_48h'       => max(0, $updated48h),
    'updated_7d'        => max(0, $updated7d),
    'escalated_created' => $escalatedCreated,
]);

echo "Completed CRM lead automation processor.\n";
