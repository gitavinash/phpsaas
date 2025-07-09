<?php include_once 'header.php'; ?>

<?php if (is_array($tagPost)):

    $tagIds = array_map(fn($t) => $t['tag_relation_id'], $tagPost);
    $resultTags = implode(',', $tagIds);

    $query = "
        SELECT link_url, product_title, product_image, inventory_currency_symbol, inventory_price, inventory_mrp, inventory_in_stock
        FROM " . TABLE_PREFIX . "link
        INNER JOIN " . TABLE_PREFIX . "product ON link_relation_id=product_id
        INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id
        WHERE link_relation_id IN ({$resultTags})
        AND link_site_id=" . SITE_ID . "
        AND link_type='product' AND link_status=200
        GROUP BY link_url
        ORDER BY link_is_featured DESC, link_modified DESC
        LIMIT 0, 30
    ";

    $result = mysqli_query($connection, $query);
?>

<!-- Valentine’s Banner -->
<section class="relative h-[400px] w-full bg-cover bg-center mb-8 mt-32"
    style="background-image: url('<?php echo USER_IMAGES ?>valentine-day-banner.jpg');">
    <div class="absolute inset-0 bg-black bg-opacity-40"></div>
    <div class="container mx-auto px-4 h-full flex flex-col justify-center items-center text-center relative z-10">
        <h2 class="text-white text-4xl md:text-5xl font-serif mb-3">Valentine's Day Special</h2>
        <p class="text-white text-lg">Special gift for your partner</p>
    </div>
</section>

<!-- Product Grid -->
<div class="container mx-auto px-4 py-8">
    <?php if (mysqli_num_rows($result) > 0): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
        <?php while ($row = mysqli_fetch_assoc($result)):
                    extract($row);
                    $price = ($inventory_price != 0) ? $inventory_price : $inventory_mrp;
                    $discount = ($inventory_price != 0 && $inventory_price < $inventory_mrp)
                        ? round((($inventory_mrp - $inventory_price) / $inventory_mrp) * 100)
                        : 0;
                ?>
        <div class="product-card group">
            <a href="<?php echo $link_url ?>">
                <div class="relative overflow-hidden rounded-lg mb-4">
                    <img src="<?php echo $product_image ?>" alt="<?php echo $product_title ?>"
                        class="w-full h-[350px] object-cover transition-transform duration-300 group-hover:scale-105">
                    <?php if ($inventory_in_stock == 0): ?>
                    <div class="absolute top-3 left-3 bg-red-600 text-white text-xs px-3 py-1 rounded">Out of Stock
                    </div>
                    <?php endif; ?>
                    <?php if ($discount > 0): ?>
                    <div class="absolute top-3 right-3 bg-green-600 text-white text-xs px-3 py-1 rounded">
                        <?php echo $discount ?>% OFF</div>
                    <?php endif; ?>
                </div>
            </a>
            <h3 class="font-serif text-lg mb-2"><?php echo $product_title ?></h3>
            <div class="flex justify-between items-center">
                <div>
                    <span
                        class="text-primary font-semibold"><?php echo $inventory_currency_symbol . ' ' . $price ?></span>
                    <?php if ($discount > 0): ?>
                    <div class="text-gray-400 text-sm line-through">
                        <?php echo $inventory_currency_symbol . ' ' . $inventory_mrp ?></div>
                    <?php endif; ?>
                </div>
                <button
                    class="bg-primary text-white px-4 py-2 rounded-button whitespace-nowrap hover:bg-opacity-90 transition">Add
                    to Cart</button>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="text-center py-20">
        <h2 class="text-xl font-semibold text-gray-700">No products found</h2>
        <a href="/" class="inline-block px-6 py-3 bg-primary text-white rounded hover:bg-opacity-90 transition">Go to
            homepage</a>
    </div>
    <?php endif; ?>
</div>

<?php else: ?>
<div class="text-center py-20">
    <h1 class="text-xl font-semibold text-gray-800 mb-4">No product found</h1>
    <a href="/" class="inline-block px-6 py-3 bg-primary text-white rounded hover:bg-opacity-90 transition">Go to
        homepage</a>
</div>
<?php endif; ?>

<?php include_once 'footer.php'; ?>