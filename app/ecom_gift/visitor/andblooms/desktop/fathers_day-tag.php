<?php include_once 'header.php'; ?>

<?php if (is_array($tagPost)):
    $tagIds = array_map(fn($t) => $t['tag_relation_id'], $tagPost);
    $resultTags = implode(',', $tagIds);

    $sortBy = 'link_published DESC';
    if (isset($_GET['sort'])) {
        switch ($_GET['sort']) {
            case 'price_low':
                $sortBy = 'inventory_price ASC';
                break;
            case 'price_high':
                $sortBy = 'inventory_price DESC';
                break;
        }
    }

    $tag_title = ucwords($tag_title);
?>

    <!-- Banner with Breadcrumb -->
    <!-- Father's Day Hero -->
    <section class="relative min-h-[125px] md:min-h-[600px] mt-16 overflow-hidden">
        <?php if (!empty($tag_image)) { ?>
            <div class="absolute inset-0 bg-cover bg-center"
                style="background-image:url('<?= $tag_image ?>')"></div>
        <?php } else { ?>
            <div class="absolute inset-0 bg-gradient-to-r from-[#1f2937] via-[#374151] to-[#111827]"></div>
        <?php } ?>

        <div class="absolute inset-0"></div>
    </section>

    <section class="bg-[#faf7f3] py-12 border-b">
        <div class="container mx-auto px-6">

            <div class="grid grid-cols-3 gap-4 text-center">

                <div>
                    <div class="text-4xl mb-3">🎁</div>
                    <h3 class="font-semibold text-lg">Curated Gifts</h3>
                    <p class="text-gray-600 text-sm">
                        Specially selected Father's Day favourites.
                    </p>
                </div>

                <div>
                    <div class="text-4xl mb-3">🚚</div>
                    <h3 class="font-semibold text-lg">Fast Delivery</h3>
                    <p class="text-gray-600 text-sm">
                        Get your gifts delivered right on time.
                    </p>
                </div>

                <div>
                    <div class="text-4xl mb-3">❤️</div>
                    <h3 class="font-semibold text-lg">Made With Love</h3>
                    <p class="text-gray-600 text-sm">
                        Celebrate the bond that matters most.
                    </p>
                </div>

            </div>

        </div>
    </section>

    <!-- Product Grid -->
    <div id="products" class="container mx-auto px-4 py-8">
        <div class="flex justify-end mb-6">
            <form method="GET">
                <select name="sort" onchange="this.form.submit()"
                    class="border border-gray-300 rounded px-4 py-2 shadow-sm">
                    <option value="#">Sort by</option>
                    <option value="latest" <?= @$_GET['sort'] === 'latest' ? 'selected' : '' ?>>Latest</option>
                    <option value="price_low" <?= @$_GET['sort'] === 'price_low' ? 'selected' : '' ?>>Price low to high
                    </option>
                    <option value="price_high" <?= @$_GET['sort'] === 'price_high' ? 'selected' : '' ?>>Price high to low
                    </option>
                </select>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            <?php
            $query = "SELECT link_url, product_title, product_image, inventory_currency_symbol, inventory_price, inventory_mrp, inventory_in_stock 
                  FROM " . TABLE_PREFIX . "link 
                  INNER JOIN " . TABLE_PREFIX . "product ON link_relation_id=product_id 
                  INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id 
                  WHERE link_relation_id IN ($resultTags) AND link_site_id=" . SITE_ID . " 
                  AND link_type='product' AND link_status=200 
                  GROUP BY link_url 
                  ORDER BY $sortBy 
                  LIMIT 0, 12";

            $result = mysqli_query($connection, $query);

            if (mysqli_num_rows($result) > 0):
                while ($row = mysqli_fetch_assoc($result)):
                    extract($row);
                    $price = ($inventory_price != 0) ? $inventory_price : $inventory_mrp;
                    $discount = ($inventory_price != 0 && $inventory_price < $inventory_mrp)
                        ? round((($inventory_mrp - $inventory_price) / $inventory_mrp) * 100)
                        : 0;
            ?>
                    <div class="product-card group">
                        <a href="<?= $link_url ?>">
                            <div class="relative overflow-hidden rounded-lg mb-4">
                                <img src="<?= $product_image ?>" alt="<?= $product_title ?>"
                                    class="w-full h-[350px] object-cover group-hover:scale-105 transition-transform duration-300">
                                <?php if ($inventory_in_stock == 0): ?>
                                    <div class="absolute top-3 left-3 bg-red-600 text-white text-xs px-3 py-1 rounded">Out of Stock
                                    </div>
                                <?php endif; ?>
                                <?php if ($discount > 0): ?>
                                    <div class="absolute top-3 right-3 bg-green-600 text-white text-xs px-3 py-1 rounded">
                                        <?= $discount ?>% OFF</div>
                                <?php endif; ?>
                            </div>
                        </a>
                        <h3 class="font-serif text-lg mb-2"><?= $product_title ?></h3>
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-primary font-semibold"><?= $inventory_currency_symbol . ' ' . $price ?></span>
                                <?php if ($discount > 0): ?>
                                    <div class="text-gray-400 text-sm line-through">
                                        <?= $inventory_currency_symbol . ' ' . $inventory_mrp ?></div>
                                <?php endif; ?>
                            </div>
                            <button data-info='<?php echo $product_id . '_' . $inventory_price . '_1'; ?>'
                                class="quickAddToCart bg-primary text-white px-4 py-2 rounded-button hover:bg-opacity-90 transition">Add
                                to
                                Cart</button>
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
        // require_once 'pagination.php';

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