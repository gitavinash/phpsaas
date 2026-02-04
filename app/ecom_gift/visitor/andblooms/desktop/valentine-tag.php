<?php include_once 'header.php'; ?>

<?php if (is_array($tagPost)):
    $tagIds = array_map(fn($t) => $t['tag_relation_id'], $tagPost);
    $resultTags = implode(',', $tagIds);

    $tag_title = ucwords($tag_title);

    $sortBy = 'RAND()';
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
          AND link_type='product' AND link_status=200 ORDER BY $sortBy";

    $result = mysqli_query($connection, $query);
?>

<!-- Banner with Breadcrumb -->
<div class="relative h-[280px] w-full mb-8 mt-16 overflow-hidden">
    <?php
        $bannerImage = $tag_image;
        $bannerFromProduct = false;
        if (empty($tag_image) && $result && mysqli_num_rows($result) > 0) {
            mysqli_data_seek($result, 0);
            $firstProduct = mysqli_fetch_assoc($result);
            $bannerImage = $firstProduct['product_image'];
            $bannerFromProduct = true;
            mysqli_data_seek($result, 0);
        }
        ?>
    <?php if (!empty($bannerImage)) { ?>
    <?php
            $bannerStyle = "background-image: url('{$bannerImage}');";
            if ($bannerFromProduct) {
                $bannerStyle .= ' filter: blur(05px); transform: scale(1.05);';
            }
            ?>
    <div style="<?= $bannerStyle ?>" class="absolute inset-0 bg-cover bg-center"></div>
    <?php } else { ?>
    <div class="absolute inset-0 bg-gradient-to-r from-[#D4946D] via-[#e0b89c] to-[#d4946d]"></div>
    <?php } ?>
    <!-- <div class="absolute inset-0 bg-black bg-opacity-40"></div> -->
    <div class="container mx-auto px-4 h-full flex flex-col justify-center items-center text-center relative z-10">
        <!-- <div class="flex items-center text-sm text-white/80 mb-6">
            <a href="/" class="hover:text-white">Home</a>
            <span class="mx-2">/</span>
            <span class="text-white"><?php echo  $tag_title ?></span>
        </div>
        <h1 class="font-serif text-4xl md:text-5xl text-white mb-4"><?php echo  $tag_title ?></h1>
        <p class="text-white text-lg max-w-2xl"><?php echo  $tag_description ?></p> -->
    </div>
</div>

<!-- Product Grid -->
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-end mb-6">
        <form method="GET">
            <select name="sort" onchange="this.form.submit()"
                class="border border-gray-300 rounded px-4 py-2 shadow-sm">
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

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
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
            ?>
        <div class="product-card group">
            <a href="<?php echo  $link_url ?>">
                <div class="relative overflow-hidden rounded-lg mb-4">
                    <img src="<?php echo  $product_image ?>" alt="<?php echo  $product_title ?>"
                        class="w-full h-[350px] object-cover group-hover:scale-105 transition-transform duration-300">
                    <?php if ($inventory_in_stock == 0): ?>
                    <div class="absolute top-3 left-3 bg-red-600 text-white text-xs px-3 py-1 rounded">Out of Stock
                    </div>
                    <?php elseif (strtotime($link_published) > strtotime('-1 month')): ?>
                    <div class="absolute top-3 left-3 bg-blue-600 text-white text-xs px-3 py-1 rounded">New</div>
                    <?php endif; ?>
                    <?php if ($discount > 0): ?>
                    <div class="absolute top-3 right-3 bg-green-600 text-white text-xs px-3 py-1 rounded">
                        <?php echo  $discount ?>% OFF</div>
                    <?php endif; ?>
                </div>
            </a>
            <h3 class="font-serif text-lg mb-2"><?php echo  $product_title ?></h3>
            <div class="flex justify-between items-center">
                <div>
                    <span
                        class="text-primary font-semibold"><?php echo  $inventory_currency_symbol . ' ' . $price ?></span>
                    <?php if ($discount > 0): ?>
                    <div class="text-gray-400 text-sm line-through">
                        <?php echo  $inventory_currency_symbol . ' ' . $inventory_mrp ?></div>
                    <?php endif; ?>
                </div>
                <?php
                            if ($inventory_in_stock > 0) { ?>
                <button data-info='<?php echo $product_id . '_' . $inventory_price . '_1'; ?>'
                    class="quickAddToCart bg-primary text-white px-4 py-2 rounded-button whitespace-nowrap hover:bg-opacity-90 transition-colors">Add
                    to Cart</button>
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