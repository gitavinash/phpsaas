<?php

header("Content-Type: application/json; charset=utf-8");

$query = $_GET['q'] ?? ($_GET['term'] ?? ($_GET['query'] ?? ($_GET['tag'] ?? '')));
$query = trim((string)$query);

$suggestions = [];
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
    'msg' => array_values(array_unique($suggestions))
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;

?>
