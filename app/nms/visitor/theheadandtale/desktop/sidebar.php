<div class="right-container">
    <div class="hightlightBox">
        <h2>Fintech Highlights</h2>
        <?php

        if ($checkFinTechArticles = mysqli_query($connection, "SELECT link_url, news_image, news_title FROM " . TABLE_PREFIX . "news INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id WHERE news_category=281 AND link_type='news' AND link_status=200 AND link_site_id=" . SITE_ID . " ORDER BY news_published DESC LIMIT 0, 5")) {
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
                                        <a href='/fintech-news/' class='readMore'>
                                            Read more news...
                                        </a>
                                </li>";
            } else {
                echo "<li><a title=''>No article found</a></li>";
            }
            echo "</ol>";
        } else {
            echo ((DEVELOPMENT_MODE == true) ? mysqli_error($connection) :  "");
        }
        ?>
    </div>
    <div class="hightlightBox">
        <h2>AI/Emerging Tech</h2>
        <?php

        if ($checkFinTechArticles = mysqli_query($connection, "SELECT link_url, news_image, news_title FROM " . TABLE_PREFIX . "news INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id WHERE news_category=282 AND link_type='news' AND link_status=200 AND link_site_id=" . SITE_ID . " ORDER BY news_published DESC LIMIT 0, 5")) {
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
                                        <a href='/ai-emerging-tech/' class='readMore'>
                                            Read more news...
                                        </a>
                                </li>";
            } else {
                echo "<li><a title=''>No article found</a></li>";
            }
            echo "</ol>";
        } else {
            echo ((DEVELOPMENT_MODE == true) ? mysqli_error($connection) :  "");
        }
        ?>
    </div>
    <div class="hightlightBox">
        <h2>Editor's Pick</h2>
        <?php

        if ($checkFinTechArticles = mysqli_query($connection, "SELECT news_title, news_image, link_url FROM " . TABLE_PREFIX . "tag INNER JOIN " . TABLE_PREFIX . "news ON news_id=tag_relation_id INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id WHERE link_site_id=" . SITE_ID . " AND link_type='news' AND link_status=200 AND tag_title LIKE '%editor%' AND tag_site_id=" . SITE_ID . " ORDER BY news_published DESC LIMIT 0,3")) {
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