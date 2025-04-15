<?php

require_once 'header.php';

?>
<section>
    <div class="row">
        <div class="container">
            <?php

            if ($newsArticles = mysqli_query($connection, "SELECT link_is_featured, link_modified, news_title, news_image, news_description, news_paid, link_url, link_published, meta_description 
FROM " . TABLE_PREFIX . "news 
INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id = news_id 
INNER JOIN " . TABLE_PREFIX . "meta ON meta_relation_id = link_id 
WHERE link_type = 'news' 
AND link_status = 200 
AND news_category IN (201, 202)
AND link_site_id = " . SITE_ID . " 
ORDER BY link_is_featured DESC, link_published DESC
LIMIT 0, 100")) {

                if (mysqli_num_rows($newsArticles) != 0) {
                    $latestNewsData = [];
                    define('TOTAL_PAGE', round(mysqli_num_rows($newsArticles) / PER_PAGE_LIMIT));
                    define('TOTAL_RESULT', mysqli_num_rows($newsArticles));
                    while ($newsData = mysqli_fetch_assoc($newsArticles)) {
                        $latestNewsData[] = $newsData;
                    }
                } else {

                    echo "<p>No news found</p>";
                }
            } else {

                echo mysqli_error($connection);
                exit();
            }

            ?>
            <div class="featureArticle">
                <?php
                for ($i = 0; $i < 1; $i++) {
                    if (isset($latestNewsData[$i])) {
                        extract($latestNewsData[$i]);
                        echo "<div class='news_box'><a href='{$link_url}' target='_blank'>
                                        <div class='feature_image'>
                                            <img src='{$news_image}' alt='{$news_title}'>
                                            " . (($news_paid == 1) ? "<img src='" . USER_IMAGES . "premium.png' />" : "") . "
        </div>
        <div class='news_intro'>
            <h2>{$news_title}</h2>
            <p><span>
                    " . date('d M Y, h:i A', strtotime($link_published)) . "
            </span></p>
            <p>{$meta_description}</p>
        </div>
        </a></div>";
                    }
                }
                ?>
            </div>
            <div class="left-container">
                <?php

                for ($i = 1; $i < 5; $i++) {
                    if (isset($latestNewsData[$i])) {
                        extract($latestNewsData[$i]);
                        echo "<div class='news_box'><a href='{$link_url}' target='_blank'>
                                        <div class='feature_image'>
                                            <img src='{$news_image}' alt='{$news_title}'>
                                            " . (($news_paid == 1) ? "<img src='" . USER_IMAGES . "premium.png' />" : "") . "
        </div>
        <div class='news_intro'>
            <h2>{$news_title}</h2>
            <p><span>
                    " . date('d M Y, h:i A', strtotime($link_published)) . "
            </span></p>
            <p>{$meta_description}</p>
        </div>
        </a></div>";
                    }
                }

                // Youtube section


                // Rest 5-10 news articles

                for ($i = 5; $i < 20; $i++) {
                    if (isset($latestNewsData[$i])) {
                        extract($latestNewsData[$i]);
                        echo "<div class='news_box'><a href='{$link_url}' target='_blank'>
                                        <div class='feature_image'>
                                            <img src='{$news_image}' alt='{$news_title}'>
                                            " . (($news_paid == 1) ? "<img src='" . USER_IMAGES . "premium.png' />" : "") . "
        </div>
        <div class='news_intro'>
            <h2>{$news_title}</h2>
            <p><span>
                    " . date('d M Y, h:i A', strtotime($link_published)) . "
            </span></p>
            <p>{$meta_description}</p>
        </div>
        </a></div>";
                    }
                }
                ?>
                <!-- <?php

                        require_once 'pagination.php';

                        ?> -->
            </div>
            <?php

            require_once 'sidebar.php';

            ?>
        </div>
    </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>