<?php include_once 'header.php'; ?>

<?php
// Use normalized controller data if present; else fall back to legacy $queryPost
$rawResults = [];
if (isset($searchData['results']) && is_array($searchData['results'])) {
    $rawResults = $searchData['results'];
} elseif (isset($queryPost) && is_array($queryPost)) {
    $rawResults = $queryPost;
}

/** Collect product IDs from:
 * - item_type === 'product'  -> relation_id (product_id)
 * - item_type === 'tag' AND tag_type === 'product' -> relation_id (product_id)
 */
$productIds = [];
foreach ($rawResults as $row) {
    $itemType = $row['item_type'] ?? '';
    if ($itemType === 'product' && !empty($row['relation_id'])) {
        $productIds[] = (int)$row['relation_id'];
    } elseif ($itemType === 'tag' && ($row['tag_type'] ?? '') === 'product' && !empty($row['relation_id'])) {
        $productIds[] = (int)$row['relation_id'];
    }
}
$productIds = array_values(array_unique(array_filter($productIds)));

if (empty($productIds)) {
    echo '<div class="text-center py-20 mt-16">
            <h1 class="text-xl font-semibold text-gray-800 mb-4">No product found</h1>
            <a href="/" class="inline-block px-6 py-3 bg-primary text-white rounded hover:bg-opacity-90 transition">Go to homepage</a>
          </div>';
    include_once 'footer.php';
    exit;
}

$idList = implode(',', array_map('intval', $productIds));

// Sorting (avoid ONLY_FULL_GROUP_BY issues; we won’t use GROUP BY)
$sortBy = 'l.link_published DESC';
if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'price_low':
            $sortBy = 'i.inventory_price ASC';
            break;
        case 'price_high':
            $sortBy = 'i.inventory_price DESC';
            break;
        case 'latest':
            $sortBy = 'l.link_published DESC';
            break;
    }
}

$tag_title = ucwords(str_replace('-', ' ', htmlspecialchars($searchKeyword ?? '', ENT_QUOTES, 'UTF-8')));
$tag_description = ''; // optional
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
        <?php if ($tag_description): ?>
        <p class="text-white text-lg max-w-2xl"><?php echo $tag_description; ?></p>
        <?php endif; ?>
    </div>
</div>

<!-- Product Grid -->
<div class="container mx-auto px-4 py-8 pb-12">
    <div class="flex justify-end mb-6">
        <form method="GET">
            <select name="sort" onchange="updateSort(this)" class="border border-gray-300 rounded px-4 py-2 shadow-sm">
                <script>
                function updateSort(select) {
                    const urlParams = new URLSearchParams(window.location.search);
                    urlParams.set('sort', select.value);
                    window.location.search = urlParams.toString();
                }
                </script>
                <option value="#">Sort by</option>
                <option value="price_low" <?php echo (($_GET['sort'] ?? '') === 'price_low') ? 'selected' : ''; ?>>Price
                    low to high</option>
                <option value="price_high" <?php echo (($_GET['sort'] ?? '') === 'price_high') ? 'selected' : ''; ?>>
                    Price high to low</option>
                <option value="newest"
                    <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'newest') ? 'selected' : '' ?>>Newest</option>
                <option value="oldest"
                    <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'oldest') ? 'selected' : '' ?>>Oldest</option>
            </select>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
        <?php
        // Return one row per product (avoid GROUP BY by selecting the latest link per product)
        $query = "
      SELECT
        l.link_url, p.product_id, p.product_title, p.product_image,
        i.inventory_currency_symbol, i.inventory_price, i.inventory_mrp,
        i.inventory_in_stock, l.link_published
      FROM " . TABLE_PREFIX . "product p
      JOIN " . TABLE_PREFIX . "link l
        ON l.link_relation_id = p.product_id
       AND l.link_type = 'product'
       AND l.link_status = 200
       AND l.link_site_id = " . SITE_ID . "
      JOIN " . TABLE_PREFIX . "inventory i
        ON i.inventory_relation_id = p.product_id
       AND i.inventory_type = 'product'
       AND i.inventory_site_id = " . SITE_ID . "
      WHERE p.product_id IN ($idList)
        AND NOT EXISTS (
          SELECT 1 FROM " . TABLE_PREFIX . "link l2
           WHERE l2.link_relation_id = p.product_id
             AND l2.link_type = 'product'
             AND l2.link_status = 200
             AND l2.link_site_id = " . SITE_ID . "
             AND l2.link_published > l.link_published
        )
      ORDER BY $sortBy
      LIMIT 100";

        $result = mysqli_query($connection, $query);

        if ($result && mysqli_num_rows($result) > 0):
            while ($row = mysqli_fetch_assoc($result)):
                $product_id = (int)$row['product_id'];
                $product_title = $row['product_title'];
                $product_image = $row['product_image'];
                $inventory_currency_symbol = $row['inventory_currency_symbol'];
                $inventory_price = (float)$row['inventory_price'];
                $inventory_mrp = (float)$row['inventory_mrp'];
                $inventory_in_stock = (int)$row['inventory_in_stock'];
                $link_published = $row['link_published'];
                $link_url = $row['link_url'];

                $price = ($inventory_price > 0) ? $inventory_price : $inventory_mrp;
                $discount = ($inventory_price > 0 && $inventory_price < $inventory_mrp)
                    ? (int)round((($inventory_mrp - $inventory_price) / max($inventory_mrp, 1)) * 100)
                    : 0;
        ?>
        <div class="product-card group">
            <a href="<?php echo htmlspecialchars($link_url, ENT_QUOTES, 'UTF-8'); ?>">
                <div class="relative overflow-hidden rounded-lg mb-4">
                    <img src="<?php echo htmlspecialchars($product_image, ENT_QUOTES, 'UTF-8'); ?>"
                        alt="<?php echo htmlspecialchars($product_title, ENT_QUOTES, 'UTF-8'); ?>"
                        class="w-full h-[350px] object-cover group-hover:scale-105 transition-transform duration-300">
                    <?php if ($inventory_in_stock === 0): ?>
                    <div class="absolute top-3 left-3 bg-red-600 text-white text-xs px-3 py-1 rounded">Out of Stock
                    </div>
                    <?php elseif (strtotime($link_published) > strtotime('-1 month')): ?>
                    <div class="absolute top-3 left-3 bg-blue-600 text-white text-xs px-3 py-1 rounded">New</div>
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
            <h3 class="font-serif text-lg mb-2"><?php echo htmlspecialchars($product_title, ENT_QUOTES, 'UTF-8'); ?>
            </h3>
            <div class="flex justify-between items-center">
                <div>
                    <span class="text-primary font-semibold">
                        <?php echo htmlspecialchars($inventory_currency_symbol, ENT_QUOTES, 'UTF-8') . ' ' . number_format($price, 2); ?>
                    </span>
                    <?php if ($discount > 0): ?>
                    <div class="text-gray-400 text-sm line-through">
                        <?php echo htmlspecialchars($inventory_currency_symbol, ENT_QUOTES, 'UTF-8') . ' ' . number_format($inventory_mrp, 2); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php

                        if ($inventory_in_stock > 0) { ?>
                <button data-info="<?php echo $product_id . '_' . (float)$inventory_price . '_1'; ?>"
                    class="quickAddToCart bg-primary text-white px-4 py-2 rounded-button hover:bg-opacity-90 transition">
                    Add to Cart
                </button>
                <?php
                        }

                        ?>
            </div>
        </div>
        <?php endwhile;
        else: ?>
        <div class="col-span-full text-center py-20">
            <h2 class="text-xl font-semibold text-gray-600">No products found for this search.</h2>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once 'footer.php'; ?>