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
                    AND news_category IN (201, 202, 285) 
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
                    ? "<img src='" . USER_IMAGES . "premium.png' alt='Premium news' />"
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
            ?>

            <!-- Left News Container -->
            <div class="left-container"
                <?php echo (($site_domain != 'theheadandtale.com') ? 'style="width: 100%;"' : '') ?>>
                <!-- Featured Article -->
                <div class="featureArticle">
                    <?php if (!empty($latestNewsData[0])) renderNewsCard($latestNewsData[0]); ?>
                </div>
                <?php
                // Show next 4 articles
                for ($i = 1; $i < min(5, TOTAL_RESULT); $i++) {
                    renderNewsCard($latestNewsData[$i]);
                }

                // Show remaining articles up to 20
                for ($i = 5; $i < min(20, TOTAL_RESULT); $i++) {
                    renderNewsCard($latestNewsData[$i]);
                }
                ?>
            </div>

            <!-- Sidebar -->
            <?php

            if (SITE_DOMAIN == 'theheadandtale.com') {
                require_once 'sidebar.php';
            }

            ?>
        </div>
    </div>
</section>

<?php require_once 'footer.php'; ?>