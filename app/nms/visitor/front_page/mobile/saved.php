<?php

if (defined('DATA_FUNCTIONS')) {
    require_once DATA_FUNCTIONS;
}

if (!function_exists('h')) {
    function h($s) {
        return htmlspecialchars(strip_tags((string)$s), ENT_QUOTES, 'UTF-8');
    }
}

// Determine logged in user
$loggedInUserId = 0;
if (defined('USER_ID') && USER_ID > 0) {
    $loggedInUserId = (int)USER_ID;
} else if (!empty($_COOKIE['user_data']) && isset($connection) && $connection) {
    $user_salt_key = mysqli_real_escape_string($connection, base64_decode($_COOKIE['user_data']));
    $userRes = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user WHERE user_salt_key='{$user_salt_key}' LIMIT 1");
    if ($userRes && mysqli_num_rows($userRes) > 0) {
        $userData = mysqli_fetch_assoc($userRes);
        $loggedInUserId = (int)$userData['user_id'];
    }
}

// Auto-create & migrate bookmark table
if (isset($connection) && $connection) {
    mysqli_query($connection, "CREATE TABLE IF NOT EXISTS " . TABLE_PREFIX . "bookmark (
        bookmark_id INT AUTO_INCREMENT PRIMARY KEY,
        bookmark_user_id INT NOT NULL,
        bookmark_ref_id INT NOT NULL,
        bookmark_ref_type VARCHAR(50) NOT NULL DEFAULT 'post',
        bookmark_site_id INT NOT NULL,
        bookmark_created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY user_ref_site (bookmark_user_id, bookmark_ref_id, bookmark_ref_type, bookmark_site_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $checkOldCol = mysqli_query($connection, "SHOW COLUMNS FROM " . TABLE_PREFIX . "bookmark LIKE 'bookmark_news_id'");
    if ($checkOldCol && mysqli_num_rows($checkOldCol) > 0) {
        mysqli_query($connection, "ALTER TABLE " . TABLE_PREFIX . "bookmark CHANGE bookmark_news_id bookmark_ref_id INT NOT NULL");
        mysqli_query($connection, "ALTER TABLE " . TABLE_PREFIX . "bookmark ADD COLUMN bookmark_ref_type VARCHAR(50) NOT NULL DEFAULT 'post' AFTER bookmark_ref_id");
    }
}

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$totalSavedItems = 0;
$totalPages = 1;

$savedArticles = [];
if ($loggedInUserId > 0 && isset($connection) && $connection) {
    $countRes = mysqli_query($connection, "SELECT COUNT(DISTINCT b.bookmark_ref_id) as total FROM " . TABLE_PREFIX . "bookmark b INNER JOIN " . TABLE_PREFIX . "news n ON b.bookmark_ref_id = n.news_id AND n.news_site_id = " . SITE_ID . " WHERE b.bookmark_user_id = {$loggedInUserId} AND b.bookmark_site_id = " . SITE_ID);
    if ($countRes && $countRow = mysqli_fetch_assoc($countRes)) {
        $totalSavedItems = (int)($countRow['total'] ?? 0);
        $totalPages = max(1, (int)ceil($totalSavedItems / $limit));
    }

    $savedSql = "SELECT n.news_id, n.news_title, n.news_image, n.news_description, n.news_paid, c.category_title, l.link_url, l.link_published, b.bookmark_ref_type
                 FROM " . TABLE_PREFIX . "bookmark b
                 INNER JOIN " . TABLE_PREFIX . "news n ON b.bookmark_ref_id = n.news_id AND n.news_site_id = " . SITE_ID . "
                 LEFT JOIN " . TABLE_PREFIX . "link l ON l.link_relation_id = n.news_id AND l.link_site_id = " . SITE_ID . " AND l.link_type = 'news'
                 LEFT JOIN " . TABLE_PREFIX . "category c ON c.category_id = n.news_category AND c.category_site_id = " . SITE_ID . "
                 WHERE b.bookmark_user_id = {$loggedInUserId} AND b.bookmark_site_id = " . SITE_ID . "
                 GROUP BY n.news_id
                 ORDER BY MAX(b.bookmark_created_at) DESC
                 LIMIT {$offset}, {$limit}";
    $savedRes = mysqli_query($connection, $savedSql);
    if ($savedRes && mysqli_num_rows($savedRes) > 0) {
        while ($row = mysqli_fetch_assoc($savedRes)) {
            $savedArticles[] = $row;
        }
    }
}

if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    header("Content-Type: application/json; charset=utf-8");
    $items = [];
    foreach ($savedArticles as $article) {
        $items[] = [
            'news_id' => $article['news_id'],
            'link_url' => !empty($article['link_url']) ? $article['link_url'] : '/news/' . $article['news_id'],
            'news_title' => $article['news_title'],
            'news_image' => $article['news_image'] ?? '',
            'published_date' => !empty($article['link_published']) ? date('M d, Y', strtotime($article['link_published'])) : '',
            'category_title' => $article['category_title'] ?? 'News',
            'news_paid' => (int)($article['news_paid'] ?? 0),
            'ref_type' => $article['bookmark_ref_type'] ?? 'post'
        ];
    }
    echo json_encode([
        'status' => 200,
        'page' => $page,
        'totalPages' => $totalPages,
        'items' => $items
    ]);
    exit();
}

require_once 'header.php';

?>

<div id='view-saved' class='page-view active'>
    <div class='page-header'>
        <h1 class='greeting-title'>Saved Articles</h1>
    </div>
    <div class='saved-page-container' style='padding: 0 16px 24px;'>
        <?php if ($loggedInUserId == 0) { ?>
            <div class="no-results-container" style="text-align: center; padding: 48px 16px;">
                <div class="no-results-icon" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 16px;">
                    <i class="ph ph-lock-key"></i>
                </div>
                <h3 class="no-results-title" style="font-size: 1.25rem; margin-bottom: 8px;">Login Required</h3>
                <p class="no-results-desc" style="color: var(--text-muted); margin-bottom: 24px;">
                    Please log in to your account to view and manage your saved articles.
                </p>
                <a href="/login/?redirect_url=/saved/" class="auth-btn primary-btn" style="display: inline-block; padding: 12px 24px; background: var(--accent-primary, #0066cc); color: #fff; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    Log In / Sign Up
                </a>
            </div>
        <?php } else if (count($savedArticles) == 0) { ?>
            <div class="no-results-container" style="text-align: center; padding: 48px 16px;">
                <div class="no-results-icon" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 16px;">
                    <i class="ph ph-bookmark-simple"></i>
                </div>
                <h3 class="no-results-title" style="font-size: 1.25rem; margin-bottom: 8px;">No Saved Articles</h3>
                <p class="no-results-desc" style="color: var(--text-muted); margin-bottom: 24px;">
                    You haven't saved any stories yet. Bookmark articles while reading to save them for later!
                </p>
                <a href="/" class="auth-btn primary-btn" style="display: inline-block; padding: 12px 24px; background: var(--accent-primary, #0066cc); color: #fff; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    Explore News
                </a>
            </div>
        <?php } else { ?>
            <div class="vertical-feed" style="padding: 0;">
                <?php foreach ($savedArticles as $article) { 
                    $articleUrl = !empty($article['link_url']) ? $article['link_url'] : '/news/' . $article['news_id'];
                    $publishedDate = !empty($article['link_published']) ? date('M d, Y', strtotime($article['link_published'])) : '';
                    $paidBadge = ((isset($article['news_paid']) && $article['news_paid'] == 1) ? " &bull; <span class='premium-badge'><i class='ph-fill ph-crown'></i> Premium</span>" : "");
                ?>
                    <div class="saved-card" data-news-id="<?php echo $article['news_id']; ?>">
                        <a href="<?php echo h($articleUrl); ?>" class="list-card native-list-item">
                            <div class="list-content">
                                <div class="list-meta">
                                    <span class="tag-text"><?php echo h($article['category_title'] ?? 'News'); ?></span>
                                    <?php if ($publishedDate) { echo ' &bull; ' . $publishedDate; } ?>
                                    <?php echo $paidBadge; ?>
                                </div>
                                <h4 class="list-title">
                                    <?php echo h($article['news_title']); ?>
                                </h4>
                            </div>
                            <?php if (!empty($article['news_image'])) { ?>
                                <img src="<?php echo h($article['news_image']); ?>" alt="<?php echo h($article['news_title']); ?>" class="list-img" />
                            <?php } ?>
                        </a>
                        <button type="button" class="remove-btn remove-bookmark-btn" data-news-id="<?php echo $article['news_id']; ?>" data-ref-type="<?php echo h($article['bookmark_ref_type'] ?? 'post'); ?>" aria-label="Remove saved article">
                            <i class="ph ph-trash"></i>
                        </button>
                    </div>
                <?php } ?>
            </div>
            <div id="infinite-scroll-sentinel" data-feed-type="saved" data-page="1" data-total-pages="<?php echo $totalPages; ?>"></div>
        <?php } ?>
    </div>
</div>

<?php

require_once 'footer.php';

?>