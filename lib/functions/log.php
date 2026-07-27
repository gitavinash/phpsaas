<?php

$logFile = __DIR__ . '/cron.log';
$logTimezone = new DateTimeZone('Asia/Kolkata');
$logRunId = (new DateTime('now', $logTimezone))->format('Ymd_His') . '_' . substr(md5(uniqid('', true)), 0, 8);

function logMessage($connection,)
{
    $timestamp = date('Y-m-d H:i:s');
    $query = "INSERT INTO logs (timestamp, message) VALUES (?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'ss', $timestamp, $message);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}


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
