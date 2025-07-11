<?php include_once 'header.php'; ?>

<?php if (is_array($queryPost)):
	$tagIds = array_filter(array_map(function ($t) {
		return isset($t['tag_relation_id']) ? $t['tag_relation_id'] : null;
	}, $queryPost));

	if (empty($tagIds)) {
		echo '<div class="text-center py-20"><h1 class="text-xl font-semibold text-gray-800 mb-4">No product found</h1><a href="/" class="inline-block px-6 py-3 bg-primary text-white rounded hover:bg-opacity-90 transition">Go to homepage</a></div>';
		include_once 'footer.php';
		exit;
	}

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

	$tag_title = ucwords(str_replace('-', ' ', $searchKeyword));
?>

<!-- Banner with Breadcrumb -->
<div class="relative h-[280px] w-full mb-8 mt-16">
    <div class="absolute inset-0 bg-gradient-to-r from-[#D4946D] via-[#e0b89c] to-[#d4946d]"></div>
    <div class="absolute inset-0 bg-black bg-opacity-40"></div>
    <div class="container mx-auto px-4 h-full flex flex-col justify-center items-center text-center relative z-10">
        <div class="flex items-center text-sm text-white/80 mb-6">
            <a href="/" class="hover:text-white">Home</a>
            <span class="mx-2">/</span>
            <span class="text-white"><?php echo $tag_title; ?></span>
        </div>
        <h1 class="font-serif text-4xl md:text-5xl text-white mb-4"><?php echo $tag_title; ?></h1>
        <p class="text-white text-lg max-w-2xl"><?php echo $tag_description; ?></p>
    </div>
</div>

<!-- Product Grid -->
<div class="container mx-auto px-4 py-8 pb-12">
    <div class="flex justify-end mb-6">
        <form method="GET">
            <select name="sort" onchange="this.form.submit()"
                class="border border-gray-300 rounded px-4 py-2 shadow-sm">
                <option value="#">Sort by</option>
                <option value="latest"
                    <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'latest') ? 'selected' : ''; ?>>Latest
                </option>
                <option value="price_low"
                    <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'price_low') ? 'selected' : ''; ?>>Price low
                    to high</option>
                <option value="price_high"
                    <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'price_high') ? 'selected' : ''; ?>>Price high
                    to low</option>
            </select>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
        <?php
			$query = "SELECT link_url, product_id, product_title, product_image, inventory_currency_symbol, inventory_price, inventory_mrp, inventory_in_stock, link_published " .
				"FROM " . TABLE_PREFIX . "link " .
				"INNER JOIN " . TABLE_PREFIX . "product ON link_relation_id=product_id " .
				"INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id " .
				"WHERE link_relation_id IN ($resultTags) AND link_site_id=" . SITE_ID . " " .
				"AND link_type='product' AND link_status=200 " .
				"GROUP BY link_url " .
				"ORDER BY $sortBy " .
				"LIMIT 0, 100";

			$result = mysqli_query($connection, $query);

			if ($result && mysqli_num_rows($result) > 0):
				while ($row = mysqli_fetch_assoc($result)):
					extract($row);
					$price = ($inventory_price != 0) ? $inventory_price : $inventory_mrp;
					$discount = ($inventory_price != 0 && $inventory_price < $inventory_mrp)
						? round((($inventory_mrp - $inventory_price) / $inventory_mrp) * 100)
						: 0;
			?>
        <div class="product-card group">
            <a href="<?php echo $link_url; ?>">
                <div class="relative overflow-hidden rounded-lg mb-4">
                    <img src="<?php echo $product_image; ?>" alt="<?php echo $product_title; ?>"
                        class="w-full h-[350px] object-cover group-hover:scale-105 transition-transform duration-300">
                    <?php if ($inventory_in_stock == 0): ?>
                    <div class="absolute top-3 left-3 bg-red-600 text-white text-xs px-3 py-1 rounded">Out of Stock
                    </div>
                    <?php endif; ?>
                    <?php if ($discount > 0): ?>
                    <div class="absolute top-3 right-3 bg-green-600 text-white text-xs px-3 py-1 rounded">
                        <?php echo $discount; ?>% OFF</div>
                    <?php endif; ?>
                    <?php if ($link_published > date('Y-m-d', strtotime('-7 days'))): ?>
                    <div class="absolute bottom-3 right-3 bg-[#D4946D] text-white text-xs px-3 py-1 rounded">New</div>
                    <?php endif; ?>
                </div>
            </a>
            <h3 class="font-serif text-lg mb-2"><?php echo $product_title; ?></h3>
            <div class="flex justify-between items-center">
                <div>
                    <span
                        class="text-primary font-semibold"><?php echo $inventory_currency_symbol . ' ' . $price; ?></span>
                    <?php if ($discount > 0): ?>
                    <div class="text-gray-400 text-sm line-through">
                        <?php echo $inventory_currency_symbol . ' ' . $inventory_mrp; ?></div>
                    <?php endif; ?>
                </div>
                <button data-info='<?php echo $product_id . "_" . $inventory_price . "_1"; ?>'
                    class="quickAddToCart bg-primary text-white px-4 py-2 rounded-button hover:bg-opacity-90 transition">Add
                    to Cart</button>
            </div>
        </div>
        <?php endwhile;
			else: ?>
        <div class="col-span-full text-center py-20">
            <h2 class="text-xl font-semibold text-gray-600">No products found for this tag.</h2>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php else: ?>
<!-- No Product Found -->
<div class="text-center py-20 mt-16">
    <h1 class="text-xl font-semibold text-gray-800 mb-4">No product found</h1>
    <a href="/" class="inline-block px-6 py-3 bg-primary text-white rounded hover:bg-opacity-90 transition">Go to
        homepage</a>
</div>
<?php endif; ?>

<?php include_once 'footer.php'; ?>