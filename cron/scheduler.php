<?php

$logFile = __DIR__ . '/scheduler.log';
$logDomain = defined('SITE_DOMAIN') ? SITE_DOMAIN : 'unknown-domain';
$logRunId = date('Ymd_His') . '_' . substr(md5(uniqid('', true)), 0, 8);

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
    global $logFile, $logDomain, $logRunId;

    $timestamp = date('Y-m-d H:i:s');
    $prefix = '[' . $timestamp . '] [' . strtoupper($level) . '] [domain=' . $logDomain . '] [run=' . $logRunId . '] ' . $event;
    $contextText = formatSchedulerContext((array) $context);
    $line = $prefix . (($contextText !== '') ? ' | ' . $contextText : '') . PHP_EOL;

    @file_put_contents($logFile, $line, FILE_APPEND);
}

writeSchedulerLog('info', 'Scheduler execution started');

if (!$connection) {
    writeSchedulerLog('error', 'Database connection failed');
    exit("Database connection failed\n");
}

$query = "SELECT news_id FROM " . TABLE_PREFIX . "news INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id AND link_type='news' WHERE news_status=2 AND link_status=206 AND link_published <= NOW()";
$scheduledNews = mysqli_query($connection, $query);

if (!$scheduledNews) {
    writeSchedulerLog('error', 'Scheduler query failed', [
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
        writeSchedulerLog('info', 'Published scheduled news', [
            'news_id' => $newsId,
        ]);
    } else {
        mysqli_query($connection, "ROLLBACK");
        $failedCount++;
        writeSchedulerLog('error', 'Failed to publish scheduled news', [
            'news_id' => $newsId,
            'error' => mysqli_error($connection),
        ]);
    }
}

writeSchedulerLog('info', 'Scheduler execution completed', [
    'processed' => $processedCount,
    'success' => $successCount,
    'failed' => $failedCount,
]);

mysqli_close($connection);