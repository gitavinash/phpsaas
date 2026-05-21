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

<!-- Blog Style Article Content -->
<article class="service-blog-post">
    <div class="container reveal">
        <div class="blog-post-header">
            <h2 class="serif-normal">
                Mastering the Science of Spectral Harmony
            </h2>
            <p class="section-desc">
                Discover how professional color guidance transforms more than just
                your walls—it transforms your experience of space and self.
            </p>
        </div>

        <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?q=80&w=2070&auto=format&fit=crop"
            alt="Color Harmony" class="blog-post-hero-image" />

        <div class="blog-post-body">
            <p>
                Color is not merely a visual adornment; it is a fundamental pillar
                of environmental psychology. At Hue Culture, we approach the "Color
                Clinic" as an laboratory where science meets soul. Every room, every
                outfit, and every brand palette is an opportunity to tune the
                frequency of your daily life.
            </p>

            <h3>The Anatomy of a Perfect Space</h3>
            <p>
                When we evaluate a room, we start with the light. Natural light is
                dynamic, shifting from the cool blues of early morning to the warm
                ambers of sunset. A color that looks impeccable in a north-facing
                studio might feel stark and sterile in an east-facing kitchen. Our
                process involves mapping these shifts to ensure your finishes—tiles,
                fabrics, and paints—remain harmonious throughout the day.
            </p>

            <img src="https://images.unsplash.com/photo-1513161455079-7dc1de15ef3e?q=80&w=1976&auto=format&fit=crop"
                alt="Interior Details" class="blog-post-inline-image" />

            <h3>Identity Through Palette</h3>
            <p>
                In personal styling, your palette is your signature. It's the silent
                communicator that tells the world who you are before you speak. Our
                Personal Color Analysis goes beyond seasonal labels. We look at the
                undertones of your skin, the clarity of your eyes, and the natural
                contrast levels of your features to build a "core" wardrobe palette
                that empowers you with confidence.
            </p>

            <p>
                By integrating these scientific principles with your personal
                aesthetic goals, we create a roadmap for effortless style. No more
                second-guessing; only clarity and confidence in every choice.
            </p>
        </div>

        <!-- Featured Products Section at bottom of Article -->
        <div class="blog-post-footer">
            <h2 class="featured-products-title serif-normal">
                Featured Products
            </h2>
            <div class="related-blog-grid">
                <a href="product-details.html" class="product-card">
                    <img src="https://images.unsplash.com/photo-1544457070-4cd773b4d71e?q=80&w=2130&auto=format&fit=crop"
                        class="product-card-img" alt="Linen Throw" />
                    <div class="product-card-info">
                        <span class="product-card-tag">Textiles</span>
                        <h3 class="product-card-title serif-normal">Raw Linen Throw</h3>
                        <div class="product-card-price">₹120.00</div>
                    </div>
                </a>

                <a href="product-details.html" class="product-card">
                    <img src="https://images.unsplash.com/photo-1610701596007-11502861dcfa?q=80&w=2070&auto=format&fit=crop"
                        class="product-card-img" alt="Ambient Lamp" />
                    <div class="product-card-info">
                        <span class="product-card-tag">Lighting</span>
                        <h3 class="product-card-title serif-normal">
                            Ambient Hue Lamp
                        </h3>
                        <div class="product-card-price">₹210.00</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</article>
<?php

require_once 'footer.php';

?>