<?php

require_once 'header.php';

/**
 * Map normalized $searchData payload
 */
$searchKeyword = $searchData['q'] ?? ($searchKeyword ?? '');
$queryPost     = $searchData['results'] ?? [];
$source        = $searchData['source'] ?? 'post';

/**
 * Helper for escaping
 */
function h($s)
{
    $clean = strip_tags((string) $s);

    return htmlspecialchars($clean, ENT_QUOTES, 'UTF-8');
}

?>

<!-- Search Hero -->
<section class="category-hero-section">

    <div class="container reveal-on-load">

        <span class="editorial-tag">

            Search Results

        </span>

        <h1 class="hero-premium-headline">

            <?php echo h(ucwords($searchKeyword)); ?>

        </h1>

        <p class="category-hero-subtitle reveal-on-load">

            <?php echo count($queryPost); ?> result(s) found for your search query.

        </p>

    </div>

</section>

<!-- Main Content -->
<section class="category-main-content">

    <div class="container">

        <div class="category-feed">

            <?php

            if (is_array($queryPost) && count($queryPost) > 0) {

                /**
                 * ============================================================
                 * POST / BLOG LISTING UI
                 * ============================================================
                 */

                if (!isset($postListType)) {

            ?>

            <div class="related-posts-list" style="margin: 0;">

                <?php

                        foreach ($queryPost as $row) {

                            $link_url = $row['url'] ?? '#';

                            if ($link_url !== '' && $link_url[0] !== '/') {

                                $link_url = '/' . ltrim($link_url, '/');
                            }

                            $post_title       = $row['title'] ?? '';
                            $post_image       = $row['image'] ?? '';
                            $post_published   = $row['published_at'] ?? '';
                            $meta_description = $row['meta_description'] ?? '';
                            $post_description = $row['description'] ?? '';

                            /**
                             * Prefer meta description
                             */
                            $shortDescription = trim($meta_description) ?: trim($post_description);

                            /**
                             * Limit text
                             */
                            if (strlen($shortDescription) > 160) {

                                $shortDescription = substr($shortDescription, 0, 157) . '...';
                            }

                        ?>

                <!-- Blog Card -->
                <div class="blog-card related-blog-card">

                    <div class="featured-img-col">

                        <a href="<?php echo h($link_url); ?>" class="featured-img-link related-img-link"
                            target="_blank">

                            <img src="<?php echo h($post_image); ?>" alt="<?php echo h($post_title); ?>" />

                        </a>

                    </div>

                    <div class="featured-text-col related-text-col">

                        <a href="#" class="category-tag">

                            Search Result

                        </a>

                        <a href="<?php echo h($link_url); ?>" class="featured-title-link related-title-link"
                            target="_blank">

                            <h2 class="serif-normal">

                                <?php echo h($post_title); ?>

                                <?php

                                            if ($post_paid == 1) {

                                            ?>

                                <img src="<?php echo USER_IMAGES; ?>premium.png" alt="Premium"
                                    style="width:18px; margin-left:6px; display:inline-block;">

                                <?php

                                            }

                                            ?>

                            </h2>

                        </a>

                        <p class="blog-excerpt related-blog-excerpt">

                            <?php echo h($shortDescription); ?>

                        </p>

                        <div class="post-meta-info" style="margin-top:15px; font-size:14px; opacity:0.7;">

                            <?php

                                        if ($post_published) {

                                        ?>

                            <span>

                                <?php echo date('d M Y, h:i A', strtotime($post_published)); ?>

                            </span>

                            <?php

                                        }

                                        ?>

                        </div>

                        <a href="<?php echo h($link_url); ?>" class="read-more-link" target="_blank">

                            Read More <i class="fa-solid fa-arrow-right"></i>

                        </a>

                    </div>

                </div>

                <?php

                        }

                        ?>

            </div>

            <?php

                } else {

                    /**
                     * ============================================================
                     * PRODUCT GRID UI
                     * ============================================================
                     */

                ?>

            <div class="row product-grid-row">

                <?php

                        foreach ($queryPost as $row) {

                            $link_url = $row['url'] ?? '#';

                            if ($link_url !== '' && $link_url[0] !== '/') {

                                $link_url = '/' . ltrim($link_url, '/');
                            }

                            $post_title         = $row['title'] ?? '';
                            $post_image         = $row['image'] ?? '';
                            $link_published     = $row['published_at'] ?? '';
                            $inventory_price    = $row['inventory_price'] ?? 0;
                            $inventory_mrp      = $row['inventory_mrp'] ?? 0;
                            $inventory_in_stock = $row['inventory_in_stock'] ?? 1;

                            /**
                             * New Badge
                             */
                            $isNew = $link_published &&
                                (strtotime($link_published) > strtotime('-7 days'));

                        ?>

                <div class="col-lg-3 col-md-4 col-6">

                    <a href="<?php echo h($link_url); ?>" class="product-card-link" target="_blank">

                        <div class="modern-product-card">

                            <div class="product-image-wrap">

                                <img src="<?php echo h($post_image); ?>" alt="<?php echo h($post_title); ?>">

                                <?php

                                            if ($isNew) {

                                            ?>

                                <span class="product-badge">

                                    New

                                </span>

                                <?php

                                            }

                                            ?>

                            </div>

                            <div class="product-content">

                                <h4>

                                    <?php echo h($post_title); ?>

                                </h4>

                                <p class="product-price">

                                    Rs.
                                    <?php

                                                echo (($inventory_price != 0)
                                                    ? h($inventory_price)
                                                    : h($inventory_mrp));

                                                ?>

                                </p>

                                <?php

                                            if ($inventory_in_stock != 1) {

                                            ?>

                                <span class="out-of-stock-label">

                                    Out of Stock

                                </span>

                                <?php

                                            }

                                            ?>

                            </div>

                        </div>

                    </a>

                </div>

                <?php

                        }

                        ?>

            </div>

            <?php

                }
            } else {

                ?>

            <!-- No Result -->
            <div class="no-post-found">

                <h3>

                    No Results Found

                </h3>

                <p>

                    Try searching with different keywords.

                </p>

            </div>

            <?php

            }

            ?>

            <!-- Pagination -->
            <div class="number-pagination-container">

                <?php

                require_once 'pagination.php';

                ?>

            </div>

        </div>

    </div>

</section>

<?php require_once 'footer.php'; ?>