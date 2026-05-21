<?php

require_once 'header.php';

?>

<!-- 2-Column Hero Section -->
<section class="service-hero">
    <div class="container hero-split-container">
        <!-- Left Column: Text Content -->
        <div class="service-hero-content">
            <h1 class="hero-premium-headline">
                <?php echo $category_title ?>
            </h1>
            <p class="service-hero-desc">
                <?php echo $category_description ?>
            </p>
        </div>


        <?php

        if ($checkProductCategory = mysqli_query($connection, "SELECT link_url, category_title, category_image FROM " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "category ON link_relation_id = category_id AND link_site_id=" . SITE_ID . " AND link_status=200 AND link_type='category' WHERE category_type='product' AND category_status=1 ORDER BY link_published ASC")) {
            if (mysqli_num_rows($checkProductCategory) != 0) {
                echo "<div class='service-hero-right'>
            <div class='service-feature-grid'>";

                while ($productCategoryDetails = mysqli_fetch_assoc($checkProductCategory)) {
                    extract($productCategoryDetails);
                    echo "<div class='service-feature-card" . (($link_url == BASE_URL) ? ' primary' : '') . "'>
                        <a href='{$link_url}' title='{$category_title}' style='text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center; gap: 12px;'>
                            <div class='service-feature-icon'>
                                <img src='{$category_image}' alt='{$category_title}'>
                            </div>
                            <h3 class='serif-normal'>{$category_title}</h3>
                        </a>
                    </div>";
                }

                echo "</div>
        </div>";
            }
        }

        ?>

    </div>
</section>
<?php

require_once 'footer.php';

?>