<?php

$logFile = __DIR__ . '/cron.log';
$logTimezone = new DateTimeZone('Asia/Kolkata');
$logRunId = (new DateTime('now', $logTimezone))->format('Ymd_His') . '_' . substr(md5(uniqid('', true)), 0, 8);

function formatSchedulerContext(array $context)
{
    if (empty($context)) {
        return '';
    }

    $parts = [];
    foreach ($context as $key => $value) {
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        } elseif (is_null($value)) {
            $value = 'null';
        } elseif (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }
        $parts[] = $key . '=' . $value;
    }

    return implode(' | ', $parts);
}

function writeSchedulerLog($level, $event, $context = [])
{
    global $logFile, $logRunId, $logTimezone;
    $filename = basename(__FILE__);
    $timestamp = (new DateTime('now', $logTimezone))->format('Y-m-d H:i:s') . ' IST';
    $prefix = '[' . $timestamp . "] [" . strtoupper($level) . "] [file={$filename}] [run={$logRunId}] " . $event;
    $contextText = formatSchedulerContext((array) $context);
    $line = $prefix . (($contextText !== '') ? ' | ' . $contextText : '') . PHP_EOL;
    @file_put_contents($logFile, $line, FILE_APPEND);
}

writeSchedulerLog('info', 'Started: checking for users with expired suspension');
writeSchedulerLog('info', 'Started: destroySession.php - checking for users with expired suspension');


$dbConnectionFile = dirname(__DIR__) . '/dbConfig.php';

if (!file_exists($dbConnectionFile)) {
    writeSchedulerLog('error', 'Aborted: configuration file not found', ['path' => $dbConnectionFile]);
    exit("Configuration file not found\n");
} else {
    require_once $dbConnectionFile;
    if ($connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME)) {
        writeSchedulerLog('info', 'Database connection established');
        writeSchedulerLog('info', 'Database connection established in destroySession.php');
        writeSchedulerLog('error', 'Aborted: configuration file not found in destroySession.php', ['path' => $dbConnectionFile]);
    } else {
        writeSchedulerLog('error', 'Aborted: database connection failed', [
            'mysql_error' => mysqli_connect_error(),
        ]);
                writeSchedulerLog('error', 'Aborted: database connection failed in destroySession.php', [
                    'mysql_error' => mysqli_connect_error(),
                ]);
        exit("Database connection failed\n");
    }
}


if (empty($connection) || mysqli_connect_errno()) {
    writeSchedulerLog('error', 'Aborted: database connection could not be established', [
        'mysql_error' => mysqli_connect_error() ?? 'no connection object',
        writeSchedulerLog('error', 'Aborted: database connection could not be established in destroySession.php', [
            'mysql_error' => mysqli_connect_error() ?? 'no connection object',
        ]);
    ]);
    exit("Database connection failed\n");
}

if (!defined('TABLE_PREFIX')) {
    writeSchedulerLog('error', 'Aborted: TABLE_PREFIX is not defined in config');
        writeSchedulerLog('error', 'Aborted: TABLE_PREFIX is not defined in config (destroySession.php)');
    exit("TABLE_PREFIX not defined\n");
}

writeSchedulerLog('info', 'Configuration loaded, querying users with expired suspension');
writeSchedulerLog('info', 'Configuration loaded in destroySession.php, querying users with expired suspension');

$query = "SELECT 
    us_user_id, 
    COUNT(*) AS totalSession,
    MAX(us_active_time) AS last_active_time
FROM 
    " . TABLE_PREFIX . "user_sessions
GROUP BY 
    us_user_id
HAVING 
    COUNT(*) > 1
    AND MAX(us_active_time) < NOW() - INTERVAL 120 HOUR;";
$scheduledUsers = mysqli_query($connection, $query);

if (!$scheduledUsers) {
    writeSchedulerLog('error', 'Aborted: failed to fetch users from database', [
        'error' => mysqli_error($connection),
        writeSchedulerLog('error', 'Aborted: failed to fetch users from database in destroySession.php', [
            'error' => mysqli_error($connection),
        ]);
    ]);
    exit("Scheduler query failed\n");
}

$processedCount = 0;
$successCount = 0;
$failedCount = 0;

while ($item = mysqli_fetch_assoc($scheduledUsers)) {
    $userId = (int) $item['us_user_id'];
    $processedCount++;
    mysqli_query($connection, "START TRANSACTION");

    $destroySession = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "user_sessions WHERE us_user_id = $userId");

    if ($destroySession) {
        mysqli_query($connection, "COMMIT");
        $successCount++;
        writeSchedulerLog('info', 'User sessions destroyed, user unblocked', [
            'user_id' => $userId,
                writeSchedulerLog('info', 'User sessions destroyed for user (unblocked)', [
                    'user_id' => $userId,
                ]);
        ]);
    } else {
        mysqli_query($connection, "ROLLBACK");
        $failedCount++;
        writeSchedulerLog('error', 'Failed to destroy user sessions, transaction rolled back', [
                    writeSchedulerLog('error', 'Failed to destroy user sessions, transaction rolled back in destroySession.php', [
                        'user_id' => $userId,
                        'error' => mysqli_error($connection),
                    ]);
            'user_id' => $userId,
            'error' => mysqli_error($connection),
        ]);
    }
}


if ($processedCount === 0) {
    writeSchedulerLog('info', 'No users with expired suspension found, no actions taken');
    writeSchedulerLog('info', 'No users with expired suspension found, no sessions destroyed (destroySession.php)');
} else {
    writeSchedulerLog('info', 'Finished: run summary', [
        'processed' => $processedCount,
        'success' => $successCount,
        'failed' => $failedCount,
        writeSchedulerLog('info', 'Finished: destroySession.php run summary', [
            'processed_users' => $processedCount,
            'sessions_destroyed' => $successCount,
            'failures' => $failedCount,
        ]);
    ]);
}

mysqli_close($connection);
