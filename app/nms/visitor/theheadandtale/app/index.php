<?php

require_once 'header.php';

?>

<div id="view-home" class="page-view active">
    <div class="greeting-section">
        <p class="date-text"><?php echo date('l, M d') ?></p>
        <h1 class="greeting-title">Top Stories</h1>
    </div>

    <!-- Hero Grid -->
    <section class="hero-grid-section">
        <?php

        $featureNewsQuery = "
SELECT 
    news_title,
    news_image,
    news_description,
    news_paid,
    category_title,
    link_url,
    link_published
FROM " . TABLE_PREFIX . "link
INNER JOIN " . TABLE_PREFIX . "news
    ON link_relation_id = news_id
INNER JOIN " . TABLE_PREFIX . "category
    ON news_category = category_id
    AND category_site_id = " . SITE_ID . "
WHERE link_is_featured=1 AND link_status = 200
    AND link_type='news'
    AND link_site_id = " . SITE_ID . "
    ORDER BY link_published DESC
    LIMIT 0, 5";

        $featuredNews = [];

        if ($featureNewsResult = mysqli_query($connection, $featureNewsQuery)) {
            if (mysqli_num_rows($featureNewsResult) > 0) {
                while ($data = mysqli_fetch_assoc($featureNewsResult)) {
                    $featuredNews[] = $data;
                }
            }
        }

        if (!empty($featuredNews)) {
            $heroPrimaryBadge = ((isset($featuredNews[0]['news_paid']) && $featuredNews[0]['news_paid'] == 1) ? "<span class='premium-badge'><i class='ph-fill ph-crown'></i> Premium</span>" : "");

            echo "<a href='{$featuredNews[0]['link_url']}' class='hero-primary native-card'>
                <img
                    src='{$featuredNews[0]['news_image']}'
                    alt='{$featuredNews[0]['news_title']}'
                    class='hero-primary-img' />
                <div class='hero-primary-content'>
                    <div class='category-tag-wrapper'>
                        <div class='category-tag'>{$featuredNews[0]['category_title']}</div>
                        {$heroPrimaryBadge}
                    </div>
                    <h2 class='hero-title'>
                        {$featuredNews[0]['news_title']}
                    </h2>
                    <p class='hero-excerpt'>
                        " . trimParagraph($featuredNews[0]['news_description']) . "
                    </p>
                    <div class='article-meta'>
                        <span class='meta-time'>" . timeToRead($featuredNews[0]['news_description']) . "</span>
                    </div>
                </div>
            </a>";

            echo "<div class='hero-secondary-list'>";
            $showDiver = 'yes';
            $loopCount = 0;
            $totalNews = count($featuredNews);
            foreach ($featuredNews as $news) {
                if ($loopCount === 0) {
                    $loopCount++;
                    continue; // Skip the first news item as it's already displayed as primary
                }
                $secondaryBadge = ((isset($news['news_paid']) && $news['news_paid'] == 1) ? " &bull; <span class='premium-badge'><i class='ph-fill ph-crown'></i> Premium</span>" : "");
                echo "<a href='{$news['link_url']}' class='hero-secondary native-list-item'>
                    <h3 class='secondary-title'>
                        {$news['news_title']}
                    </h3>
                    <div class='article-meta'>
                        <span class='meta-time'>" . publishedTimeAgo($news['link_published']) . "</span>{$secondaryBadge}
                    </div>
                </a>";
                if ($showDiver == 'yes') {
                    echo "<div class='divider'></div>";
                }
                if ($loopCount < $totalNews - 1) {
                    $showDiver = 'no';
                }
                $loopCount++;
            }
            echo "</div>";
        } else {
            echo '<p>No featured news available.</p>';
        }


        ?>
    </section>

    <!-- Latest News Compact List -->
    <section class="section-block">
        <div class="section-header">
            <h3 class="section-title">Latest News</h3>
            <a href="/latest-news/" class="view-all-btn">View All</a>
        </div>
        <div class="vertical-feed">

            <?php

            $featureNewsQuery = "SELECT 
    news_title,
    news_image,
    news_description,
    news_paid,
    category_title,
    link_url,
    link_published
FROM " . TABLE_PREFIX . "link
INNER JOIN " . TABLE_PREFIX . "news
    ON link_relation_id = news_id
INNER JOIN " . TABLE_PREFIX . "category
    ON news_category = category_id
    AND category_site_id = " . SITE_ID . "
WHERE
    link_status = 200
    AND link_type='news'
    AND link_site_id = " . SITE_ID . "
    ORDER BY link_published DESC
    LIMIT 0, 10";

            if ($latestNewsResult = mysqli_query($connection, $featureNewsQuery)) {
                if (mysqli_num_rows($latestNewsResult) > 0) {
                    while ($latestNews = mysqli_fetch_assoc($latestNewsResult)) {
                        $latestBadge = ((isset($latestNews['news_paid']) && $latestNews['news_paid'] == 1) ? " &bull; <span class='premium-badge'><i class='ph-fill ph-crown'></i> Premium</span>" : "");
                        echo "<a href='{$latestNews['link_url']}' class='list-card native-list-item'>
                            <div class='list-content'>
                                <div class='list-meta'>
                                    <span class='tag-text'>{$latestNews['category_title']}</span> &bull; " . publishedTimeAgo($latestNews['link_published']) . "{$latestBadge}
                                </div>
                                <h4 class='list-title'>
                                    {$latestNews['news_title']}
                                </h4>
                            </div>
                            <img
                                src='{$latestNews['news_image']}'
                                alt='{$latestNews['news_title']}'
                                class='list-img' />
                        </a>";
                    }
                }
            }

            ?>
        </div>
    </section>
</div>

<?php
require_once 'footer.php';
?>