<?php

header('Content-Type: application/json');
$httpStatus = 405;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Pagination parameters
    $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $perPageLimit = isset($_GET['limit']) ? (int)$_GET['limit'] : (defined('PER_PAGE_LIMIT') ? PER_PAGE_LIMIT : 20);
    $offset = ($currentPage - 1) * $perPageLimit;

    // Category filter (optional)
    $categoryFilter = '';
    if (isset($_GET['categories'])) {
        $categories = mysqli_real_escape_string($connection, $_GET['categories']);
        $categoryFilter = " AND news_category IN ({$categories})";
    }

    // Count total records for pagination
    $countQuery = "SELECT COUNT(*) as total 
                   FROM " . TABLE_PREFIX . "news 
                   INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id = news_id 
                   WHERE link_type = 'news' 
                   AND link_status = 200 
                   AND news_status = 1 
                   AND link_site_id = " . SITE_ID . "
                   {$categoryFilter}";

    $countResult = mysqli_query($connection, $countQuery);
    $totalRecords = 0;
    if ($countResult) {
        $countData = mysqli_fetch_assoc($countResult);
        $totalRecords = (int)$countData['total'];
    }

    $totalPages = ceil($totalRecords / $perPageLimit);

    // Fetch news data with meta information
    $newsListQuery = "SELECT 
                        news_id,
                        news_title,
                        news_image,
                        news_paid,
                        news_published,
                        link_url,
                        link_published,
                        link_modified,
                        link_is_featured,
                        meta_description
                      FROM " . TABLE_PREFIX . "news 
                      INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id = news_id 
                      INNER JOIN " . TABLE_PREFIX . "meta ON meta_relation_id = link_id 
                      WHERE link_type = 'news' 
                      AND link_status = 200 
                      AND news_status = 1 
                      AND link_site_id = " . SITE_ID . "
                      {$categoryFilter}
                      ORDER BY link_is_featured DESC, news_published DESC 
                      LIMIT {$offset}, {$perPageLimit}";

    if ($newsListResult = mysqli_query($connection, $newsListQuery)) {
        $blockList = array();
        while ($newsListData = mysqli_fetch_assoc($newsListResult)) {
            $newsData = array(
                'title' => $newsListData['news_title'],
                'description' => $newsListData['meta_description'],
                'image' => $newsListData['news_image'],
                'is_paid' => (int)$newsListData['news_paid'],
                'is_featured' => (int)$newsListData['link_is_featured'],
                'published' => $newsListData['news_published'],
                'url' => $newsListData['link_url'],
            );
            array_push($blockList, $newsData);
        }
        $status = 'success';
        $msg = 'News fetched successfully';
        $httpStatus = 200;
    } else {
        $httpStatus = 500;
        $msg = 'Database query failed: ' . mysqli_error($connection);
    }
} else {
    $httpStatus = 400;
    $msg = 'Invalid request method';
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid request method -> ' . $_SERVER['REQUEST_METHOD']),
    'items' => ((isset($blockList)) ? $blockList : []),
    'pagination' => array(
        'current_page' => isset($currentPage) ? $currentPage : 1,
        'per_page' => isset($perPageLimit) ? $perPageLimit : 20,
        'total_pages' => isset($totalPages) ? $totalPages : 0,
        'total_records' => isset($totalRecords) ? $totalRecords : 0,
        'has_next' => isset($currentPage, $totalPages) ? ($currentPage < $totalPages) : false,
        'has_previous' => isset($currentPage) ? ($currentPage > 1) : false
    )
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);