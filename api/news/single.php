<?php

header('Content-Type: application/json');
$httpStatus = 400;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // Get URL parameter
    $newsUrl = isset($_GET['url']) ? mysqli_real_escape_string($connection, trim($_GET['url'])) : '';

    if (empty($newsUrl)) {
        $httpStatus = 400;
        $msg = 'URL parameter is required';
    } else {
        // Fetch single news article with complete information
        $newsQuery = "SELECT 
                        news_id,
                        news_site_id,
                        news_title,
                        news_description,
                        news_image,
                        news_thumbnail,
                        news_category,
                        news_price,
                        news_mrp,
                        news_paid,
                        news_owner_id,
                        news_status,
                        news_type,
                        news_is_public,
                        news_published,
                        category_title,
                        category_slug,
                        link_id,
                        link_url,
                        link_status,
                        link_is_featured,
                        link_creation,
                        link_published,
                        link_modified,
                        profile_display_name,
                        profile_email,
                        profile_image,
                        user_username,
                        meta_title,
                        meta_description,
                        meta_keyword,
                        meta_canonical,
                        meta_robots,
                        fb_img,
                        fb_title,
                        fb_desc,
                        tw_img,
                        tw_title,
                        tw_desc,
                        GROUP_CONCAT(DISTINCT tag_title SEPARATOR ', ') as tags
                      FROM " . TABLE_PREFIX . "news 
                      INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id = news_id 
                      INNER JOIN " . TABLE_PREFIX . "category ON news_category = category_id 
                      INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id = news_owner_id 
                      INNER JOIN " . TABLE_PREFIX . "user ON news_owner_id = user_id 
                      INNER JOIN " . TABLE_PREFIX . "meta ON meta_relation_id = link_id 
                      LEFT JOIN " . TABLE_PREFIX . "tag ON tag_relation_id = news_id AND tag_type = 'news' AND tag_site_id = " . SITE_ID . "
                      WHERE link_url = '{$newsUrl}' 
                      AND link_type = 'news' 
                      AND link_status = 200 
                      AND link_site_id = " . SITE_ID . "
                      GROUP BY news_id
                      LIMIT 1";

        if ($newsResult = mysqli_query($connection, $newsQuery)) {
            if (mysqli_num_rows($newsResult) > 0) {
                $newsData = mysqli_fetch_assoc($newsResult);

                $news = array(
                    'id' => (int)$newsData['news_id'],
                    'site_id' => (int)$newsData['news_site_id'],
                    'title' => $newsData['news_title'],
                    'description' => $newsData['news_description'],
                    'image' => $newsData['news_image'],
                    'thumbnail' => $newsData['news_thumbnail'],
                    'category' => array(
                        'id' => (int)$newsData['news_category'],
                        'title' => $newsData['category_title'],
                        'slug' => $newsData['category_slug']
                    ),
                    'price' => array(
                        'mrp' => $newsData['news_mrp'],
                        'selling_price' => $newsData['news_price'],
                        'is_paid' => (int)$newsData['news_paid'] === 1
                    ),
                    'url' => $newsData['link_url'],
                    'full_url' => 'https://' . SITE_DOMAIN . '/' . $newsData['link_url'],
                    'is_featured' => (int)$newsData['link_is_featured'] === 1,
                    'is_public' => (int)$newsData['news_is_public'] === 1,
                    'status' => (int)$newsData['news_status'],
                    'type' => $newsData['news_type'],
                    'author' => array(
                        'id' => (int)$newsData['news_owner_id'],
                        'name' => $newsData['profile_display_name'],
                        'username' => $newsData['user_username'],
                        'email' => $newsData['profile_email'],
                        'image' => $newsData['profile_image']
                    ),
                    'meta' => array(
                        'title' => $newsData['meta_title'],
                        'description' => $newsData['meta_description'],
                        'keywords' => $newsData['meta_keyword'],
                        'canonical' => $newsData['meta_canonical'],
                        'robots' => $newsData['meta_robots']
                    ),
                    'social' => array(
                        'facebook' => array(
                            'image' => $newsData['fb_img'],
                            'title' => $newsData['fb_title'],
                            'description' => $newsData['fb_desc']
                        ),
                        'twitter' => array(
                            'image' => $newsData['tw_img'],
                            'title' => $newsData['tw_title'],
                            'description' => $newsData['tw_desc']
                        )
                    ),
                    'tags' => !empty($newsData['tags']) ? explode(', ', $newsData['tags']) : [],
                    'published_date' => $newsData['news_published'],
                    'created_date' => $newsData['link_creation'],
                    'modified_date' => $newsData['link_modified']
                );

                $status = 'success';
                $msg = 'News article fetched successfully';
                $httpStatus = 200;
            } else {
                $status = 'error';
                $msg = 'News article not found';
                $httpStatus = 404;
            }
        } else {
            $status = 'error';
            $msg = 'Database query failed: ' . mysqli_error($connection);
            $httpStatus = 500;
        }
    }
} else {
    $msg = 'Invalid request method. Only GET is allowed';
}

$result = array(
    'status' => isset($status) ? $status : 'error',
    'msg' => isset($msg) ? $msg : 'Unknown error',
    'data' => isset($news) ? $news : null
);

http_response_code($httpStatus);
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);