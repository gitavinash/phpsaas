<?php include_once 'header.php'; ?>

<?php if (is_array($tagPost)):
    $tagIds = array_map(fn($t) => $t['tag_relation_id'], $tagPost);
    $resultTags = implode(',', $tagIds);

    $sortBy = 'link_published DESC';
    // $sortBy = 'RAND()';
    if (isset($_GET['sort'])) {
        switch ($_GET['sort']) {
            case 'price_low':
                $sortBy = 'inventory_price ASC';
                break;
            case 'price_high':
                $sortBy = 'inventory_price DESC';
                break;
            case 'newest':
                $sortBy = 'link_published DESC';
                break;
            case 'oldest':
                $sortBy = 'link_published ASC';
                break;
        }
    }

    // Execute query first to get products
    $query = "SELECT link_url, link_published, product_id, product_title, product_image, inventory_currency_symbol, inventory_price, inventory_mrp, inventory_in_stock 
          FROM " . TABLE_PREFIX . "link 
          INNER JOIN " . TABLE_PREFIX . "product ON link_relation_id=product_id 
          INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id 
          WHERE link_relation_id IN ($resultTags) AND link_site_id=" . SITE_ID . " 
          AND link_type='product' AND link_status=200 
          GROUP BY link_url 
          ORDER BY $sortBy";

    $result = mysqli_query($connection, $query);
?>

    <style>
        /* Custom Layout & Mobile-First styling rules */
        .custom-product-card {
            background: #ffffff;
            border: 1px solid #f3f4f6;
            border-radius: 12px;
            padding: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        @media (min-width: 768px) {
            .custom-product-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 12px 20px -8px rgba(212, 148, 109, 0.15);
                border-color: rgba(212, 148, 109, 0.3);
            }
        }

        .custom-product-title {
            font-family: 'Playfair Display', serif;
            font-weight: 500;
            color: #1f2937;
            margin-bottom: 8px !important;
            display: -webkit-box !important;
            -webkit-line-clamp: 2 !important;
            -webkit-box-orient: vertical !important;
            overflow: hidden !important;
            height: 2.75rem !important;
            line-height: 1.35 !important;
        }

        .custom-card-footer {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            margin-top: auto !important;
        }

        .custom-price-wrapper {
            display: flex !important;
            flex-direction: column !important;
        }

        .custom-price {
            color: #D4946D !important;
            font-weight: 600 !important;
            font-size: 1.05rem !important;
        }

        .custom-mrp {
            color: #9ca3af !important;
            font-size: 0.875rem !important;
            text-decoration: line-through !important;
        }

        .custom-add-to-cart {
            background-color: #D4946D !important;
            color: #ffffff !important;
            font-weight: 500 !important;
            border-radius: 6px !important;
            transition: background-color 0.2s ease !important;
        }

        @media (max-width: 767px) {
            .custom-banner {
                height: 220px !important;
            }

            .custom-banner-content {
                padding: 1rem 0.75rem !important;
            }

            .custom-banner-breadcrumb {
                display: none !important;
            }

            .custom-banner-title {
                font-size: 1.5rem !important;
                margin-bottom: 0.5rem !important;
                line-height: 1.2 !important;
                font-weight: 600 !important;
            }

            .custom-banner-desc {
                font-size: 0.825rem !important;
                line-height: 1.35 !important;
                max-width: 95% !important;
                display: -webkit-box !important;
                -webkit-line-clamp: 3 !important;
                -webkit-box-orient: vertical !important;
                overflow: hidden !important;
                opacity: 0.95 !important;
            }

            .custom-sort-container {
                margin-bottom: 1rem !important;
                justify-content: stretch !important;
            }

            .custom-sort-form {
                width: 100% !important;
            }

            .custom-sort-select {
                width: 100% !important;
                font-size: 0.875rem !important;
                padding: 8px 12px !important;
                border-radius: 8px !important;
                background-color: #fff !important;
                border: 1px solid #e5e7eb !important;
            }

            .custom-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 12px !important;
                padding-left: 8px !important;
                padding-right: 8px !important;
            }

            .custom-product-card {
                padding: 8px;
                border-radius: 8px;
            }

            .custom-img-wrapper {
                border-radius: 6px !important;
                margin-bottom: 6px !important;
            }

            .custom-product-img {
                height: 180px !important;
            }

            .custom-badge-left,
            .custom-badge-right {
                top: 8px !important;
                padding: 2px 6px !important;
                font-size: 10px !important;
                border-radius: 4px !important;
            }

            .custom-badge-left {
                left: 8px !important;
            }

            .custom-badge-right {
                right: 8px !important;
            }

            .custom-product-title {
                font-size: 0.875rem !important;
                margin-bottom: 4px !important;
                height: 2.25rem !important;
                line-height: 1.25 !important;
            }

            .custom-card-footer {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 8px !important;
                margin-top: 4px !important;
            }

            .custom-price-wrapper {
                flex-direction: row !important;
                align-items: baseline !important;
                gap: 6px !important;
            }

            .custom-price {
                font-size: 0.95rem !important;
            }

            .custom-mrp {
                font-size: 0.75rem !important;
            }

            .custom-add-to-cart {
                width: 100% !important;
                padding: 6px 12px !important;
                font-size: 0.8rem !important;
                text-align: center !important;
            }
        }

        .fd-trust-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            background: #fff;
            padding: 18px;
            border-radius: 16px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, .06);
            margin: 100px 0px 25px 0;
        }

        /* Father's Day Landing Page */

        .fd-trust-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }

        .fd-trust-item span {
            font-size: 24px;
        }

        .fd-trust-item small {
            font-size: 13px;
            color: #374151;
        }

        .fd-promo-banner {
            overflow: hidden;
            border-radius: 20px;
            margin-bottom: 40px;
        }

        .fd-promo-banner img {
            width: 100%;
            display: block;
            max-height: 380px;
            object-fit: cover;
        }

        .fd-collection-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 30px;
        }

        .fd-small-label {
            color: #D4946D;
            font-weight: 600;
            text-transform: uppercase;
        }

        .fd-collection-header h2 {
            font-size: 38px;
            margin: 10px 0;
        }

        .fd-collection-header p {
            color: #6b7280;
        }

        .fd-product-count {
            margin-top: 10px;
            font-weight: 600;
        }

        .fd-save-text {
            color: #16a34a;
            font-size: 13px;
            font-weight: 700;
            margin-top: 2px;
        }

        .fd-banner-cta {
            text-align: center;
            margin-top: -10px;
            margin-bottom: 35px;
        }

        .fd-banner-cta a {
            background: #3D6B33;
            color: #fff;
            padding: 14px 28px;
            border-radius: 999px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
        }

        .fd-banner-cta a:hover {
            color: #fff;
        }

        .fd-sale-note {
            color: #D4946D;
            font-weight: 600;
            margin-top: 8px;
        }

        @media(max-width:767px) {

            .fd-trust-strip {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
                padding: 12px;
                font-size: 13px;
            }

            .fd-collection-header h2 {
                font-size: 28px;
            }

            .fd-promo-banner {
                border-radius: 12px;
            }

            .fd-promo-banner img {
                max-height: 220px;
                object-fit: cover;
            }
        }
    </style>

    <div class="container mx-auto px-4">

        <!-- <div class="fd-trust-strip">
            <div class="fd-trust-item">
                <span>🚚</span>
                <small>Same Day Delivery</small>
            </div>
            <div class="fd-trust-item">
                <span>🎁</span>
                <small>Gift Ready Packaging</small>
            </div>
            <div class="fd-trust-item">
                <span>💚</span>
                <small>Fresh Blooms</small>
            </div>
            <div class="fd-trust-item">
                <span>⭐</span>
                <small>Father's Day Special</small>
            </div>
        </div> -->

        <div class="fd-promo-banner">

            <img
                src="<?php echo $tag_image ?>"
                alt="Father's Day Special">

        </div>
        <div class="fd-banner-cta">
            <a href="#giftCollection">
                Shop Father's Day Gifts
            </a>
        </div>

    </div>

    <!-- Product Grid -->
    <div class="container mx-auto px-4 py-8">

        <div id="giftCollection" class="fd-collection-header">

            <span class="fd-small-label">
                Father's Day Collection
            </span>
            <div class="fd-sale-note">
                🎁 Limited Time Father's Day Specials
            </div>

            <h2>
                Find The Perfect Gift For Dad
            </h2>

            <p>
                Celebrate the man who has always been there with thoughtful flowers and gifts.
            </p>

        </div>
        <div class="flex justify-end mb-6 custom-sort-container">
            <form method="GET" class="custom-sort-form">
                <select name="sort" onchange="this.form.submit()"
                    class="border border-gray-300 rounded px-4 py-2 shadow-sm custom-sort-select">
                    <option value="#">Sort by</option>
                    <option value="price_low" <?php echo  @$_GET['sort'] === 'price_low' ? 'selected' : '' ?>>Price low to
                        high
                    </option>
                    <option value="price_high" <?php echo  @$_GET['sort'] === 'price_high' ? 'selected' : '' ?>>Price high
                        to low
                    </option>
                    <option value="newest"
                        <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'newest') ? 'selected' : '' ?>>Newest</option>
                    <option value="oldest"
                        <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'oldest') ? 'selected' : '' ?>>Oldest</option>
                </select>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 custom-grid">
            <?php
            // Query already executed above, just check results
            if (mysqli_num_rows($result) > 0):
                mysqli_data_seek($result, 0); // Reset pointer to start
                while ($row = mysqli_fetch_assoc($result)):
                    extract($row);
                    $price = ($inventory_price != 0) ? $inventory_price : $inventory_mrp;
                    $discount = ($inventory_price != 0 && $inventory_price < $inventory_mrp)
                        ? round((($inventory_mrp - $inventory_price) / $inventory_mrp) * 100)
                        : 0;
                    $savedAmount = $inventory_mrp - $price;
            ?>
                    <div class="product-card group custom-product-card">
                        <a href="<?php echo  $link_url ?>">
                            <div class="relative overflow-hidden rounded-lg mb-4 custom-img-wrapper">
                                <img src="<?php echo  $product_image ?>" alt="<?php echo  $product_title ?>"
                                    class="w-full h-[280px] object-cover group-hover:scale-105 transition-transform duration-300 custom-product-img">
                                <?php if ($inventory_in_stock == 0): ?>
                                    <div class="absolute top-3 left-3 bg-red-600 text-white text-xs px-3 py-1 rounded custom-badge-left">Out of Stock
                                    </div>
                                <?php elseif (strtotime($link_published) > strtotime('-1 month')): ?>
                                    <div class="absolute top-3 left-3 bg-blue-600 text-white text-xs px-3 py-1 rounded custom-badge-left">Dad's Favorite</div>
                                <?php endif; ?>
                                <?php if ($discount > 0): ?>
                                    <div class="absolute top-3 right-3 bg-green-600 text-white text-xs px-3 py-1 rounded custom-badge-right">
                                        <?php echo  $discount ?>% OFF</div>
                                <?php endif; ?>
                            </div>
                        </a>
                        <h3 class="font-serif text-lg mb-2 custom-product-title"><?php echo  $product_title ?></h3>
                        <div class="flex justify-between items-center custom-card-footer">
                            <div class="custom-price-wrapper">
                                <span
                                    class="text-primary font-semibold custom-price"><?php echo  $inventory_currency_symbol . ' ' . $price ?></span>
                                <?php if ($discount > 0): ?>
                                    <div class="text-gray-400 text-sm line-through custom-mrp">
                                        <?php echo  $inventory_currency_symbol . ' ' . $inventory_mrp ?></div>
                                    <div class="fd-save-text">
                                        Save <?php echo $inventory_currency_symbol . ' ' . $savedAmount; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php
                            if ($inventory_in_stock > 0) { ?>
                                <button data-info='<?php echo $product_id . '_' . $inventory_price . '_1'; ?>'
                                    class="quickAddToCart bg-primary text-white px-4 py-2 rounded-button whitespace-nowrap hover:bg-opacity-90 transition-colors custom-add-to-cart">Add to Cart</button>
                            <?php
                            }
                            ?>

                        </div>
                    </div>
                <?php endwhile;
            else: ?>
                <div class="col-span-full text-center py-20">
                    <h2 class="text-xl font-semibold text-gray-600">No products found for this tag.</h2>
                </div>
            <?php endif; ?>
        </div>

        <?php

        // Pagination
        require_once 'pagination.php';

        ?>

    <?php else: ?>
        <!-- No Product Found -->
        <div class="text-center py-20">
            <h1 class="text-xl font-semibold text-gray-800 mb-4">No product found</h1>
            <a href="/" class="inline-block px-6 py-3 bg-primary text-white rounded hover:bg-opacity-90 transition">Go to
                homepage</a>
        </div>
    <?php endif; ?>
    </div>
    <?php include_once 'footer.php'; ?>