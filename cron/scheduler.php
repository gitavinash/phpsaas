<?php

$logFile = __DIR__ . '/scheduler.log';
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

    $timestamp = (new DateTime('now', $logTimezone))->format('Y-m-d H:i:s') . ' IST';
    $prefix = '[' . $timestamp . '] [' . strtoupper($level) . '] [run=' . $logRunId . '] ' . $event;
    $contextText = formatSchedulerContext((array) $context);
    $line = $prefix . (($contextText !== '') ? ' | ' . $contextText : '') . PHP_EOL;

    @file_put_contents($logFile, $line, FILE_APPEND);
}

writeSchedulerLog('info', 'Scheduler started — checking for news due to publish');


if (file_exists('lib/configuration/index.php')) {

    /** Including configuration file file  */

    require_once 'lib/configuration/index.php';
} else {
    writeSchedulerLog('error', 'Aborted — configuration file not found at lib/configuration/index.php');
    exit("Configuration file not found\n");
}

$query = "SELECT news_id FROM " . TABLE_PREFIX . "news INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id AND link_type='news' WHERE news_status=2 AND link_status=206 AND link_published <= NOW()";
$scheduledNews = mysqli_query($connection, $query);

if (!$scheduledNews) {
    writeSchedulerLog('error', 'Aborted — failed to fetch scheduled news from database', [
        'error' => mysqli_error($connection),
    ]);
    exit("Scheduler query failed\n");
}

$processedCount = 0;
$successCount = 0;
$failedCount = 0;

while ($item = mysqli_fetch_assoc($scheduledNews)) {
    $newsId = (int) $item['news_id'];
    $processedCount++;
    mysqli_query($connection, "START TRANSACTION");

    $updateNews = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "news SET news_status=1, news_published=NOW() WHERE news_id='{$newsId}'");
    $updateLink = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "link SET link_status=200, link_modified=NOW() WHERE link_relation_id='{$newsId}' AND link_type='news' AND link_status=206");

    if ($updateNews && $updateLink) {
        mysqli_query($connection, "COMMIT");
        $successCount++;
        writeSchedulerLog('info', 'News published successfully — status updated to live', [
            'news_id' => $newsId,
        ]);
    } else {
        mysqli_query($connection, "ROLLBACK");
        $failedCount++;
        writeSchedulerLog('error', 'Failed to publish news — transaction rolled back', [
            'news_id' => $newsId,
            'error' => mysqli_error($connection),
        ]);
    }
}

writeSchedulerLog('info', 'Scheduler finished — run summary', [
    'processed' => $processedCount,
    'success' => $successCount,
    'failed' => $failedCount,
]);

mysqli_close($connection);