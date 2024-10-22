<?php

require_once 'header.php';

?>
<section>
    <div class="row">
        <div class="container">
            <div class="left-container">
                <?php

                for ($i = 0; $i < 5; $i++) {
                    if (isset($categorySinglePost[$i])) {
                        extract($categorySinglePost[$i]);
                        echo "<div class='news_box'><a href='{$link_url}'>
                                        <div class='feature_image'>
                                            <img src='{$news_image}' alt='{$news_title}'>
                                            " . (($news_paid == 1) ? "<img src='" . USER_IMAGES . "premium.png' />" : "") . "
        </div>
        <div class='news_intro'>
            <h2>{$news_title}</h2>
            <p><span>
                    " . date('M d, Y', strtotime($link_published)) . "
            </span></p>
            <p>{$meta_description}</p>
        </div>
        </a></div>";
                    }
                }

                // Youtube section


                // Rest 5-10 news articles

                for ($i = 5; $i < 10; $i++) {
                    if (isset($categorySinglePost[$i])) {
                        extract($categorySinglePost[$i]);
                        echo "<div class='news_box'><a href='{$link_url}'>
                                        <div class='feature_image'>
                                            <img src='{$news_image}' alt='{$news_title}'>
                                            " . (($news_paid == 1) ? "<img src='" . USER_IMAGES . "premium.png' />" : "") . "
        </div>
        <div class='news_intro'>
            <h2>{$news_title}</h2>
            <p><span>
                    " . date('M d, Y', strtotime($link_published)) . "
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
                    <h2>Fintech Hightlights of the day</h2>
                    <?php

                    if ($checkFinTechArticles = mysqli_query($connection, "SELECT link_url, news_image, news_title FROM " . TABLE_PREFIX . "news INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id WHERE news_category=281 AND link_type='news' AND link_status=200 AND link_site_id=" . SITE_ID . " ORDER BY link_published DESC LIMIT 0, 5")) {
                        echo "<ol>";
                        if (mysqli_num_rows($checkFinTechArticles) != 0) {
                            while ($finTechArticlesInfo = mysqli_fetch_assoc($checkFinTechArticles)) {
                                extract($finTechArticlesInfo);
                                echo "<li>
                                        <a href='{$link_url}' title='{$news_title}'>
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
    </div>
</section>
<?php

require_once 'footer.php';

?>