<?php

header("Content-Type: application/json; charset=utf-8");

$query = $_GET['q'] ?? ($_GET['term'] ?? ($_GET['query'] ?? ($_GET['tag'] ?? '')));
$query = trim((string)$query);

$suggestions = [];
$news = [];
$status = 200;

if (isset($connection) && $connection) {
    $site_id = defined('SITE_ID') ? (int)SITE_ID : 1;
    $table_prefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';

    if (!empty($query)) {
        $safeQuery = mysqli_real_escape_string($connection, $query);
        $sql = "SELECT DISTINCT tag_title FROM {$table_prefix}tag 
                WHERE tag_title LIKE '%{$safeQuery}%' 
                AND tag_site_id = {$site_id} 
                GROUP BY tag_title 
                ORDER BY CASE WHEN tag_title LIKE '{$safeQuery}%' THEN 1 ELSE 2 END, tag_title ASC 
                LIMIT 0, 10";

        $newsSql = "SELECT DISTINCT n.news_title, n.news_image, l.link_url FROM {$table_prefix}news n INNER JOIN {$table_prefix}link l ON l.link_relation_id = n.news_id AND l.link_type = 'news' AND l.link_status = 200 AND l.link_site_id = {$site_id} LEFT JOIN {$table_prefix}tag t ON (t.tag_relation_id = n.news_id OR t.tag_relation_id = l.link_id) AND t.tag_type = 'news' AND t.tag_site_id = {$site_id} WHERE n.news_site_id = {$site_id} AND n.news_status = 1 AND n.news_is_public = 1 AND (n.news_title LIKE '%{$safeQuery}%' OR t.tag_title LIKE '%{$safeQuery}%') ORDER BY n.news_id DESC LIMIT 0, 4";
        $newsResult = mysqli_query($connection, $newsSql);
        if ($newsResult && mysqli_num_rows($newsResult) > 0) {
            while ($row = mysqli_fetch_assoc($newsResult)) {
                $news[] = [
                    'title' => trim($row['news_title']),
                    'image' => trim($row['news_image'] ?? ''),
                    'url' => trim($row['link_url'] ?? '')
                ];
            }
        }
    } else {
        $sql = "SELECT DISTINCT tag_title FROM {$table_prefix}tag 
                WHERE tag_site_id = {$site_id} 
                GROUP BY tag_title 
                ORDER BY tag_id DESC 
                LIMIT 0, 10";
    }

    $result = mysqli_query($connection, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            if (!empty($row['tag_title'])) {
                $suggestions[] = trim($row['tag_title']);
            }
        }
    }
}

$response = [
    'status' => $status,
    'query' => $query,
    'count' => count($suggestions),
    'suggestions' => array_values(array_unique($suggestions)),
    'news' => $news,
    'msg' => array_values(array_unique($suggestions))
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;

?>
