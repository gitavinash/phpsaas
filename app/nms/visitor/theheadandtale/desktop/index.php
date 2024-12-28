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
            </div>
            <div class="right-container">
                <div class="hightlightBox">
                    <h2>Fintech Hightlights</h2>
                    <?php

                    if ($checkFinTechArticles = mysqli_query($connection, "SELECT link_url, news_image, news_title FROM " . TABLE_PREFIX . "news INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id WHERE news_category=281 AND link_type='news' AND link_status=200 AND link_site_id=" . SITE_ID . " ORDER BY link_published DESC LIMIT 0, 5")) {
                        echo "<ol>";
                        if (mysqli_num_rows($checkFinTechArticles) != 0) {
                            while ($finTechArticlesInfo = mysqli_fetch_assoc($checkFinTechArticles)) {
                                extract($finTechArticlesInfo);
                                echo "<li>
                                        <a href='{$link_url}' title='{$news_title}' target='_blank'>
                                            <div class='img' ><img src='{$news_image}' ></div>
                                            <div class='text'><h3>{$news_title}</h3></div>
                                        </a>
                                </li>";
                            }
                            echo
                            "<li>
                                        <a href='/category/news/' class='readMore'>
                                            Read more highlights...
                                        </a>
                                </li>";
                        } else {
                            echo "<li><a title=''>No article found</a></li>";
                        }
                        echo "</ol>";
                    }
                    ?>
                </div>
                <div class="hightlightBox">
                    <h2>Editor's Pick</h2>
                    <?php

                    if ($checkFinTechArticles = mysqli_query($connection, "SELECT news_title, news_image, link_url FROM " . TABLE_PREFIX . "tag INNER JOIN " . TABLE_PREFIX . "news ON news_id=tag_relation_id INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id WHERE link_site_id=" . SITE_ID . " AND link_type='news' AND link_status=200 AND tag_title LIKE '%editor%' AND tag_site_id=" . SITE_ID . " ORDER BY link_published DESC LIMIT 0,3")) {
                        echo "<ol>";
                        if (mysqli_num_rows($checkFinTechArticles) != 0) {
                            while ($finTechArticlesInfo = mysqli_fetch_assoc($checkFinTechArticles)) {
                                extract($finTechArticlesInfo);
                                echo "<li>
                                        <a href='{$link_url}' title='{$news_title} target='_blank''>
                                            <div class='img' ><img src='{$news_image}' ></div>
                                            <div class='text'><h3>{$news_title}</h3></div>
                                        </a>
                                </li>";
                            }
                        } else {
                            echo "<li><a title=''>No article found</a></li>";
                        }
                        echo "</ol>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>