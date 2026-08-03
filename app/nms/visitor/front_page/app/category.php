<?php

if (defined('DATA_FUNCTIONS')) {
    require_once DATA_FUNCTIONS;
}

if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    header("Content-Type: application/json; charset=utf-8");
    $items = [];
    if (isset($categorySinglePost) && is_array($categorySinglePost)) {
        foreach ($categorySinglePost as $post) {
            $publishedTime = function_exists('publishedTimeAgo') ? publishedTimeAgo($post['news_published'] ?? $post['publishedTime'] ?? '') : '';
            $items[] = [
                'link_url' => $post['link_url'] ?? '',
                'news_title' => $post['news_title'] ?? '',
                'news_image' => $post['news_image'] ?? '',
                'news_published' => $publishedTime,
                'news_paid' => (int)($post['news_paid'] ?? 0),
                'category_title' => $category_title ?? 'News'
            ];
        }
    }
    echo json_encode([
        'status' => 200,
        'page' => (int)($currentPage ?? 1),
        'totalPages' => (int)(defined('TOTAL_PAGE') ? TOTAL_PAGE : 1),
        'items' => $items
    ]);
    exit();
}

require_once 'header.php';
$totalPages = defined('TOTAL_PAGE') ? TOTAL_PAGE : 1;

?>

<section class='section-block'>
    <div class='section-header'>
        <h3 class='section-title'><?php echo ucwords($category_title) ?></h3>
        <span class='page-subtitle'>Latest Articles</span>
    </div>
    <div class='vertical-feed'>

        <?php

        for ($i = 0; $i < 100; $i++) {
            if (isset($categorySinglePost[$i])) {
                $paidBadge = ((isset($categorySinglePost[$i]['news_paid']) && $categorySinglePost[$i]['news_paid'] == 1) ? " &bull; <span class='premium-badge'><i class='ph-fill ph-crown'></i> Premium</span>" : "");
                echo "<a href='{$categorySinglePost[$i]['link_url']}' class='list-card native-list-item'>
            <div class='list-content'>
                <div class='list-meta'>
                    <span class='tag-text'>{$category_title}</span> &bull; " . publishedTimeAgo($categorySinglePost[$i]['news_published']) . "{$paidBadge}
                </div>
                <h4 class='list-title'>
                    " . $categorySinglePost[$i]['news_title'] . "
                </h4>
            </div>
            <img
                src='" . $categorySinglePost[$i]['news_image'] . "'
                alt='" . str_replace("'", "", str_replace('"', '', $categorySinglePost[$i]['news_title'])) . "'
                class='list-img' />
        </a>";
            }
        }

        ?>
    </div>
    <div id="infinite-scroll-sentinel" data-feed-type="category" data-page="1" data-total-pages="<?php echo $totalPages; ?>"></div>
</section>

<?php
require_once 'footer.php';
?>