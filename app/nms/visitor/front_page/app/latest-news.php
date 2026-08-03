<?php

require_once 'header.php';

?>

<div class='page-header'>
    <h3 class='page-title'>Latest Articles</h3>
</div>
<section class='section-block'>
    <div class='vertical-feed'>

        <?php

        $featureNewsQuery = "SELECT 
    news_title,
    news_image,news_description,
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
    LIMIT 0, 50";

        if ($latestNewsResult = mysqli_query($connection, $featureNewsQuery)) {
            if (mysqli_num_rows($latestNewsResult) > 0) {
                while ($latestNews = mysqli_fetch_assoc($latestNewsResult)) {
                    echo "<a href='{$latestNews['link_url']}' class='list-card native-list-item'>
                        <div class='list-content'>
                            <div class='list-meta'>
                                <span class='tag-text'>{$latestNews['category_title']}</span> &bull; " . publishedTimeAgo($latestNews['link_published']) . "
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

<?php
require_once 'footer.php';
?>