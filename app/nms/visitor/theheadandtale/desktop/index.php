<?php require_once 'header.php'; ?>

<section>
    <div class="row">
        <div class="container">

            <?php
            $latestNewsData = [];

            // Optimized query
            $query = "
                SELECT 
                    link_is_featured, 
                    link_modified, 
                    news_title, 
                    news_image, 
                    news_description, 
                    news_paid, 
                    link_url, 
                    link_published,
                    news_published,
                    meta_description 
                FROM " . TABLE_PREFIX . "news 
                INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id = news_id 
                INNER JOIN " . TABLE_PREFIX . "meta ON meta_relation_id = link_id 
                WHERE 
                    link_type = 'news' 
                    AND link_status = 200 
                    AND news_category IN (201, 202, 285, 386, 399) 
                    AND link_site_id = " . SITE_ID . "
                ORDER BY 
                    link_is_featured DESC, 
                    news_published DESC
                LIMIT 100";

            $result = mysqli_query($connection, $query);

            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $latestNewsData[] = $row;
                }

                define('TOTAL_RESULT', count($latestNewsData));
                define('TOTAL_PAGE', ceil(TOTAL_RESULT / PER_PAGE_LIMIT));
            } else {
                echo "<p>Error loading news: " . mysqli_error($connection) . "</p>";
            }

            // Helper function to render news card
            function renderNewsCard($data)
            {
                $premiumBadge = $data['news_paid'] == 1
                    ? "<img src='" . USER_IMAGES . "premium.png' class='premium-badge' alt='Premium news' />"
                    : '';

                $formattedDate = date('d M Y, h:i A', strtotime($data['news_published']));
                $newsImage = htmlspecialchars($data['news_image']);
                $newsTitle = htmlspecialchars($data['news_title']);
                $newsUrl = htmlspecialchars($data['link_url']);
                $metaDescription = htmlspecialchars($data['meta_description']);

                echo "
                <div class='news_box'>
                    <a href='{$newsUrl}' target='_blank'>
                        <div class='feature_image'>
                            <img src='{$newsImage}' alt='{$newsTitle}'>
                            {$premiumBadge}
                        </div>
                        <div class='news_intro'>
                            <h2>{$newsTitle}</h2>
                            <p><span>{$formattedDate}</span></p>
                            <p>{$metaDescription}</p>
                        </div>
                    </a>
                </div>";
            }

            // Helper function to render Fintech Highlights
            function renderFintechHighlights($connection)
            {
                echo '<div class="hightlightBox">';
                echo '<h2>Fintech Highlights</h2>';

                if ($checkFinTechArticles = mysqli_query($connection, "SELECT link_url, news_image, news_title FROM " . TABLE_PREFIX . "news INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id WHERE news_category=281 AND link_type='news' AND link_status=200 AND link_site_id=" . SITE_ID . " ORDER BY news_published DESC LIMIT 0, 5")) {
                    echo "<ol>";
                    if (mysqli_num_rows($checkFinTechArticles) != 0) {
                        while ($finTechArticlesInfo = mysqli_fetch_assoc($checkFinTechArticles)) {
                            extract($finTechArticlesInfo);
                            echo "<li>
                                <a href='{$link_url}' title='{$news_title}' target='_blank'>
                                    <div class='img'><img src='{$news_image}'></div>
                                    <div class='text'><h3>{$news_title}</h3></div>
                                </a>
                            </li>";
                        }
                        echo "<li>
                                <a href='/fintech-news/' class='readMore'>
                                    Read more news...
                                </a>
                            </li>";
                    } else {
                        echo "<li><a title=''>No article found</a></li>";
                    }
                    echo "</ol>";
                }
                echo '</div>';
            }

            // Helper function to render PE/VC Tales
            function renderPEVCTales($connection)
            {
                echo '<div class="hightlightBox">';
                echo '<h2>PE/VC Tales</h2>';

                if ($checkFinTechArticles = mysqli_query($connection, "SELECT link_url, news_image, news_title FROM " . TABLE_PREFIX . "news INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id WHERE news_category=399 AND link_type='news' AND link_status=200 AND link_site_id=" . SITE_ID . " ORDER BY news_published DESC LIMIT 0, 5")) {
                    echo "<ol>";
                    if (mysqli_num_rows($checkFinTechArticles) != 0) {
                        while ($finTechArticlesInfo = mysqli_fetch_assoc($checkFinTechArticles)) {
                            extract($finTechArticlesInfo);
                            echo "<li>
                                <a href='{$link_url}' title='{$news_title}' target='_blank'>
                                    <div class='img'><img src='{$news_image}'></div>
                                    <div class='text'><h3>{$news_title}</h3></div>
                                </a>
                            </li>";
                        }
                        echo "<li>
                                <a href='/pe-vc-tales/' class='readMore'>
                                    Read more news...
                                </a>
                            </li>";
                    } else {
                        echo "<li><a title=''>No article found</a></li>";
                    }
                    echo "</ol>";
                }
                echo '</div>';
            }

            // Helper function to render AI/Emerging Tech
            function renderAIEmergingTech($connection)
            {
                echo '<div class="hightlightBox">';
                echo '<h2>AI/Emerging Tech</h2>';

                if ($checkFinTechArticles = mysqli_query($connection, "SELECT link_url, news_image, news_title FROM " . TABLE_PREFIX . "news INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id WHERE news_category=282 AND link_type='news' AND link_status=200 AND link_site_id=" . SITE_ID . " ORDER BY news_published DESC LIMIT 0, 5")) {
                    echo "<ol>";
                    if (mysqli_num_rows($checkFinTechArticles) != 0) {
                        while ($finTechArticlesInfo = mysqli_fetch_assoc($checkFinTechArticles)) {
                            extract($finTechArticlesInfo);
                            echo "<li>
                                <a href='{$link_url}' title='{$news_title}' target='_blank'>
                                    <div class='img'><img src='{$news_image}'></div>
                                    <div class='text'><h3>{$news_title}</h3></div>
                                </a>
                            </li>";
                        }
                        echo "<li>
                                <a href='/ai-emerging-tech/' class='readMore'>
                                    Read more news...
                                </a>
                            </li>";
                    } else {
                        echo "<li><a title=''>No article found</a></li>";
                    }
                    echo "</ol>";
                }
                echo '</div>';
            }

            // Helper function to render IPO Tales
            function renderIPOTales($connection)
            {
                echo '<div class="hightlightBox">';
                echo '<h2>IPO Tales</h2>';

                if ($checkFinTechArticles = mysqli_query($connection, "SELECT link_url, news_image, news_title FROM " . TABLE_PREFIX . "news INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id WHERE news_category=386 AND link_type='news' AND link_status=200 AND link_site_id=" . SITE_ID . " ORDER BY news_published DESC LIMIT 0, 4")) {
                    echo "<ol>";
                    if (mysqli_num_rows($checkFinTechArticles) != 0) {
                        while ($finTechArticlesInfo = mysqli_fetch_assoc($checkFinTechArticles)) {
                            extract($finTechArticlesInfo);
                            echo "<li>
                                <a href='{$link_url}' title='{$news_title}' target='_blank'>
                                    <div class='img'><img src='{$news_image}'></div>
                                    <div class='text'><h3>{$news_title}</h3></div>
                                </a>
                            </li>";
                        }
                        echo "<li>
                                <a href='/ipo-tales/' class='readMore'>
                                    Read more news...
                                </a>
                            </li>";
                    } else {
                        echo "<li><a title=''>No article found</a></li>";
                    }
                    echo "</ol>";
                }
                echo '</div>';
            }

            // Helper function to render Money & Machines
            function renderMoneyMachines($connection)
            {
                echo '<div class="hightlightBox">';
                echo '<h2>Money & Machines</h2>';

                if ($checkFinTechArticles = mysqli_query($connection, "SELECT link_url, news_image, news_title FROM " . TABLE_PREFIX . "news INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id WHERE news_category=329 AND link_type='news' AND link_status=200 AND link_site_id=" . SITE_ID . " ORDER BY news_published DESC LIMIT 0, 4")) {
                    echo "<ol>";
                    if (mysqli_num_rows($checkFinTechArticles) != 0) {
                        while ($finTechArticlesInfo = mysqli_fetch_assoc($checkFinTechArticles)) {
                            extract($finTechArticlesInfo);
                            echo "<li>
                                <a href='{$link_url}' title='{$news_title}' target='_blank'>
                                    <div class='img'><img src='{$news_image}'></div>
                                    <div class='text'><h3>{$news_title}</h3></div>
                                </a>
                            </li>";
                        }
                        echo "<li>
                                <a href='/money-and-machines/' class='readMore'>
                                    Previous editions...
                                </a>
                            </li>";
                    } else {
                        echo "<li><a title=''>No article found</a></li>";
                    }
                    echo "</ol>";
                }
                echo '</div>';
            }

            // Helper function to render Editor's Pick
            function renderEditorsPick($connection)
            {
                echo '<div class="hightlightBox">';
                echo '<h2>Editor\'s Pick</h2>';

                if ($checkFinTechArticles = mysqli_query($connection, "SELECT news_title, news_image, link_url FROM " . TABLE_PREFIX . "tag INNER JOIN " . TABLE_PREFIX . "news ON news_id=tag_relation_id INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=news_id WHERE link_site_id=" . SITE_ID . " AND link_type='news' AND link_status=200 AND tag_title LIKE '%editor%' AND tag_site_id=" . SITE_ID . " ORDER BY news_published DESC LIMIT 0,3")) {
                    echo "<ol>";
                    if (mysqli_num_rows($checkFinTechArticles) != 0) {
                        while ($finTechArticlesInfo = mysqli_fetch_assoc($checkFinTechArticles)) {
                            extract($finTechArticlesInfo);
                            echo "<li>
                                <a href='{$link_url}' title='{$news_title}' target='_blank'>
                                    <div class='img'><img src='{$news_image}'></div>
                                    <div class='text'><h3>{$news_title}</h3></div>
                                </a>
                            </li>";
                        }
                    } else {
                        echo "<li><a title=''>No article found</a></li>";
                    }
                    echo "</ol>";
                }
                echo '</div>';
            }
            ?>

            <!-- Left News Container -->
            <div class="left-container" <?php echo ((SITE_ID != '49') ? 'style="width: 100%;"' : '') ?>>
                <!-- Featured Article -->
                <div class="featureArticle">
                    <?php if (!empty($latestNewsData[0])) renderNewsCard($latestNewsData[0]); ?>
                </div>

                <?php
                // On mobile: intersperse sidebar sections with news
                if (SITE_VERSION == 'mobile' && SITE_ID == '49') {
                    $maxArticles = min(20, TOTAL_RESULT); // Same limit as desktop


                    // Show articles 2-5
                    for ($i = 1; $i <= 1 && $i < $maxArticles; $i++) {
                        renderNewsCard($latestNewsData[$i]);
                    }

                    // After 1st article: Fintech Highlights
                    renderFintechHighlights($connection);

                    // Show articles 2-5
                    for ($i = 2; $i <= 6 && $i < $maxArticles; $i++) {
                        renderNewsCard($latestNewsData[$i]);
                    }

                    // After 5th article: AI/Emerging Tech
                    if ($maxArticles > 6) {
                        renderAIEmergingTech($connection);
                    }

                    // Show articles 6-10
                    for ($i = 7; $i <= 11 && $i < $maxArticles; $i++) {
                        renderNewsCard($latestNewsData[$i]);
                    }

                    // After 10th article: IPO Tales
                    if ($maxArticles > 10) {
                        renderIPOTales($connection);
                    }

                    // Show articles 11-15
                    for ($i = 11; $i <= 15 && $i < $maxArticles; $i++) {
                        renderNewsCard($latestNewsData[$i]);
                    }

                    // After 15th article: PE/VC Tales
                    if ($maxArticles > 15) {
                        renderPEVCTales($connection);
                    }


                    // Show articles 16-20
                    for ($i = 16; $i <= 20 && $i < $maxArticles; $i++) {
                        renderNewsCard($latestNewsData[$i]);
                    }

                    // After 20th article: Money & Machines
                    if ($maxArticles > 15) {
                        renderMoneyMachines($connection);
                    }

                    // At the end: Editor's Pick
                    if ($maxArticles > 1) {
                        renderEditorsPick($connection);
                    }
                } else {
                    // Desktop: show all articles in sequence
                    for ($i = 1; $i < min(20, TOTAL_RESULT); $i++) {
                        renderNewsCard($latestNewsData[$i]);
                    }
                }
                ?>
            </div>

            <!-- Sidebar (Desktop only) -->
            <?php
            if (SITE_VERSION == 'desktop' && SITE_ID == '49') {
                require_once 'sidebar.php';
            }
            ?>
        </div>
    </div>
</section>

<?php

if (SITE_VERSION == 'mobile') { ?>
<!-- Floating trigger (mobile only) -->
<a href='/who-reads-us/' class="reviews-fab" id="openReviews" aria-controls="reviewsSheet" aria-expanded="false">
    Who Reads Us
</a>
<?php
}

?>

<?php require_once 'footer.php'; ?>