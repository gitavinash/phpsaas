<?php

require_once 'header.php';

?>
<section>
    <div class="row">
        <div class="container">
            <div class="featureArticle">
            </div>
            <div class="left-container">
                <div class="mediaFile">
                    <h1><?php echo $media_title ?></h1>
                    <img style="float: left; width: 100%; height: auto" src="<?php echo SITE_URL . $media_url; ?>" alt="">
                </div>
            </div>
            <div class="right-container">
                <div class="hightlightBox">
                    <h2>Fintech Hightlights</h2>
                    <?php

                    if ($checkFinTechArticles = mysqli_query($connection, "SELECT link_url, news_title FROM " . TABLE_PREFIX . "news INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id WHERE news_category=202 AND link_type='news' AND link_status=200 AND link_site_id=" . SITE_ID . " ORDER BY link_published DESC LIMIT 0, 5")) {
                        echo "<ol>";
                        if (mysqli_num_rows($checkFinTechArticles) != 0) {
                            while ($finTechArticlesInfo = mysqli_fetch_assoc($checkFinTechArticles)) {
                                extract($finTechArticlesInfo);
                                echo "<li><a href='{$link_url}' title='{$news_title}'>{$news_title}</a></li>";
                            }
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

                    if ($checkFinTechArticles = mysqli_query($connection, "SELECT link_url, news_title FROM " . TABLE_PREFIX . "news INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id WHERE news_category=280 AND link_type='news' AND link_status=200 AND link_site_id=" . SITE_ID . " ORDER BY link_published DESC LIMIT 0, 5")) {
                        echo "<ol>";
                        if (mysqli_num_rows($checkFinTechArticles) != 0) {
                            while ($finTechArticlesInfo = mysqli_fetch_assoc($checkFinTechArticles)) {
                                extract($finTechArticlesInfo);
                                echo "<li><a href='{$link_url}' title='{$news_title}'>{$news_title}</a></li>";
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
</section>
<?php

require_once 'footer.php';

?>