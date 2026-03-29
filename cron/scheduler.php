<?php

if (!$connection) {
    exit("Database connection failed\n");
}

$query = "SELECT news_id FROM " . TABLE_PREFIX . "news INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id AND link_type='news' WHERE news_status=2 AND link_status=206 AND link_published <= NOW()";
$scheduledNews = mysqli_query($connection, $query);

if (!$scheduledNews) {
    exit("Scheduler query failed\n");
}

while ($item = mysqli_fetch_assoc($scheduledNews)) {
    $newsId = (int) $item['news_id'];
    mysqli_query($connection, "START TRANSACTION");

    $updateNews = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "news SET news_status=1, news_published=NOW() WHERE news_id='{$newsId}'");
    $updateLink = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "link SET link_status=200, link_modified=NOW() WHERE link_relation_id='{$newsId}' AND link_type='news' AND link_status=206");

    if ($updateNews && $updateLink) {
        mysqli_query($connection, "COMMIT");
    } else {
        mysqli_query($connection, "ROLLBACK");
    }
}

mysqli_close($connection);