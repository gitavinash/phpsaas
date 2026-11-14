<?php

include_once 'header.php';
require_once __DIR__ . '/partials/product-card.php';

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
        $productIds[] = (int) $row['relation_id'];
    } elseif ($itemType === 'tag' && ($row['tag_type'] ?? '') === 'product' && !empty($row['relation_id'])) {
        $productIds[] = (int) $row['relation_id'];
    }
}
$productIds = array_values(array_unique(array_filter($productIds)));

$searchHeading = !empty($searchKeyword) ? ucwords(str_replace('-', ' ', htmlspecialchars($searchKeyword, ENT_QUOTES, 'UTF-8'))) : 'Search Results';

if (empty($productIds)) {
    echo '<section class="py-20 md:py-28 bg-[#FAF7F5] min-h-[50vh] flex items-center justify-center">
            <div class="max-w-md mx-auto text-center px-4">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-white border border-[#E8DDD5] flex items-center justify-center text-[#C47C68]">
                    <i class="ri-search-line text-2xl" aria-hidden="true"></i>
                </div>
                <span class="vl-eyebrow">Search Results</span>
                <h1 class="font-serif text-2xl sm:text-3xl text-[#242220] mt-1 mb-3">No Creations Found</h1>
                <p class="text-sm text-[#6E675F] leading-relaxed mb-6">
                    We could not find any floral arrangements matching "' . $searchHeading . '". Try exploring our seasonal collections.
                </p>
                <a href="/shop/" class="vl-btn-primary">
                    <span>Explore All Creations</span>
                    <i class="ri-arrow-right-line" aria-hidden="true"></i>
                </a>
            </div>
          </section>';
    include_once 'footer.php';
    exit;
}

$idList = implode(',', array_map('intval', $productIds));

// Sorting
$sortBy = 'l.link_published DESC';
if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'price_low':
            $sortBy = 'i.inventory_price ASC';
            break;
        case 'price_high':
            $sortBy = 'i.inventory_price DESC';
            break;
        case 'oldest':
            $sortBy = 'l.link_published ASC';
            break;
        case 'newest':
            $sortBy = 'l.link_published DESC';
            break;
    }
}
?>

<!-- Search Banner with Breadcrumb -->
<section class="py-10 sm:py-16 md:py-20 bg-[#FAF7F5] border-b border-[#E8DDD5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <nav class="flex items-center justify-center text-xs sm:text-sm text-[#6E675F] mb-4 gap-2" aria-label="Breadcrumb">
            <a href="/" class="hover:text-[#C47C68] transition-colors">Home</a>
            <span aria-hidden="true">/</span>
            <span class="text-[#242220] font-medium" aria-current="page">Search</span>
        </nav>
        <span class="vl-eyebrow">Atelier Discovery</span>
        <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl text-[#242220] mb-3">
            Search: &ldquo;<?php echo $searchHeading; ?>&rdquo;
        </h1>
        <p class="text-sm sm:text-base text-[#6E675F] max-w-xl mx-auto font-light">
            Found <?php echo count($productIds); ?> curated creation<?php echo count($productIds) === 1 ? '' : 's'; ?> matching your inquiry.
        </p>
    </div>
</section>

<!-- Product Grid & Toolbar -->
<section class="py-10 sm:py-16 px-4 sm:px-6 lg:px-8 bg-white">
    <div class="max-w-7xl mx-auto">
        <!-- Toolbar -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pb-6 mb-8 border-b border-[#E8DDD5]">
            <p class="text-xs sm:text-sm text-[#6E675F]">
                Showing <?php echo count($productIds); ?> creations
            </p>

            <form method="GET" class="flex items-center gap-2">
                <?php if (!empty($searchKeyword)): ?>
                    <input type="hidden" name="q" value="<?php echo htmlspecialchars($searchKeyword, ENT_QUOTES, 'UTF-8'); ?>">
                <?php endif; ?>
                <label for="search-sort" class="text-xs uppercase tracking-wider font-semibold text-[#6E675F]">Sort By:</label>
                <select name="sort" id="search-sort" class="vl-input py-1.5 px-3 text-xs w-auto cursor-pointer" data-auto-submit>
                    <option value="" <?php echo empty($_GET['sort']) ? 'selected' : ''; ?>>Latest Arrivals</option>
                    <option value="price_low" <?php echo (@$_GET['sort'] === 'price_low') ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_high" <?php echo (@$_GET['sort'] === 'price_high') ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="oldest" <?php echo (@$_GET['sort'] === 'oldest') ? 'selected' : ''; ?>>Oldest</option>
                </select>
            </form>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
            <?php
            $query = "
                SELECT 
                    l.link_id, 
                    l.link_url, 
                    l.link_published,
                    p.product_id, 
                    p.product_title, 
                    p.product_image, 
                    i.inventory_currency, 
                    i.inventory_in_stock, 
                    i.inventory_currency_symbol, 
                    i.inventory_price, 
                    i.inventory_mrp 
                FROM " . TABLE_PREFIX . "product p
                INNER JOIN " . TABLE_PREFIX . "inventory i 
                    ON i.inventory_relation_id = p.product_id 
                    AND i.inventory_type = 'product' 
                    AND i.inventory_site_id = " . SITE_ID . "
                INNER JOIN " . TABLE_PREFIX . "link l 
                    ON l.link_relation_id = p.product_id 
                    AND l.link_type = 'product' 
                    AND l.link_status = 200 
                    AND l.link_site_id = " . SITE_ID . "
                WHERE p.product_status = 1 
                  AND p.product_site_id = " . SITE_ID . "
                  AND i.inventory_in_stock = 1 
                  AND p.product_id IN ({$idList})
                ORDER BY {$sortBy} 
                LIMIT 0, 48";

            if ($result = mysqli_query($connection, $query)) {
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo veloura_render_product_card($row);
                    }
                } else {
                    echo "<div class='col-span-full py-16 text-center text-[#6E675F]'>No products currently active in stock.</div>";
                }
            }
            ?>
        </div>
    </div>
</section>

<?php include_once 'footer.php'; ?>
