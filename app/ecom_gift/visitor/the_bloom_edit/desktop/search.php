<?php include_once 'header.php'; ?>

<?php
$rawResults = [];
if (isset($searchData['results']) && is_array($searchData['results'])) {
    $rawResults = $searchData['results'];
} elseif (isset($queryPost) && is_array($queryPost)) {
    $rawResults = $queryPost;
}

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

$keywordRaw = (string) ($searchKeyword ?? ($_GET['q'] ?? ''));
$tag_title = ucwords(str_replace('-', ' ', safe_html($keywordRaw)));

if (empty($productIds)):
?>
    <section class="tbe-banner py-8 sm:py-14 md:py-16">
        <div class="tbe-banner-scrim" aria-hidden="true"></div>
        <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
            <span class="tbe-eyebrow text-[#EFE8DC] mb-2">Atelier Search</span>
            <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl font-medium text-white mb-2 sm:mb-3">
                <?php echo $tag_title !== '' ? 'Results for &ldquo;' . $tag_title . '&rdquo;' : 'Search Our Creations'; ?>
            </h1>
        </div>
    </section>

    <div class="max-w-2xl mx-auto px-4 py-10 sm:py-16">
        <div class="bg-white border border-[#E6DDD3] rounded-xl text-center p-6 sm:p-10 space-y-5">
            <i class="ri-search-eye-line text-4xl text-[#9B5637]" aria-hidden="true"></i>
            <h2 class="font-serif text-2xl font-medium text-[#1C1A18]">No Matching Arrangements Found</h2>
            <p class="text-sm text-[#5E5650]">
                Try refining your search or explore our signature bouquets, ceramic planters, and gift hampers.
            </p>
            <div class="flex flex-wrap justify-center gap-3 pt-2">
                <a href="/category/flower/" class="tbe-btn-forest">Fresh Flowers</a>
                <a href="/category/planters/" class="tbe-btn-outline">Planters</a>
                <a href="/shop/" class="tbe-btn-outline">View All Creations</a>
            </div>
        </div>
    </div>
<?php
    include_once 'footer.php';
    exit;
endif;

$idList = implode(',', array_map('intval', $productIds));

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
        case 'latest':
        case 'newest':
            $sortBy = 'l.link_published DESC';
            break;
    }
}

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
   AND i.inventory_in_stock = 1
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
$resultCount = ($result) ? mysqli_num_rows($result) : 0;
?>

<!-- Editorial Search Banner -->
<section class="tbe-banner py-8 sm:py-14 md:py-16">
    <img src="<?php echo USER_IMAGES; ?>about-us-banner.jpg" alt="" aria-hidden="true" fetchpriority="high" loading="eager" decoding="async" class="tbe-banner-media">
    <div class="tbe-banner-scrim" aria-hidden="true"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center relative z-10">
        <nav class="flex items-center justify-center text-xs sm:text-sm text-[#EFE8DC] mb-3 sm:mb-4 gap-2" aria-label="Breadcrumb">
            <a href="/" class="hover:text-white transition-colors">Home</a>
            <span aria-hidden="true">/</span>
            <span class="text-white font-medium" aria-current="page">Search</span>
        </nav>
        <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl font-medium text-white mb-2">
            <?php echo $tag_title !== '' ? '&ldquo;' . $tag_title . '&rdquo;' : 'Search Results'; ?>
        </h1>
        <p class="text-[#F4EFE6] text-xs sm:text-sm uppercase tracking-widest">
            <?php echo $resultCount; ?> Curated Result<?php echo $resultCount === 1 ? '' : 's'; ?> Found
        </p>
    </div>
</section>

<!-- Product Grid -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 md:py-14">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 pb-5 sm:pb-6 mb-6 sm:mb-8 border-b border-[#E6DDD3]">
        <p class="text-xs uppercase tracking-widest text-[#5E5650] font-medium">
            Showing <strong class="text-[#1C1A18]"><?php echo $resultCount; ?></strong> Result<?php echo $resultCount === 1 ? '' : 's'; ?>
        </p>

        <form method="GET" class="flex items-center justify-between sm:justify-end gap-3 w-full sm:w-auto">
            <?php if (!empty($_GET['q'])): ?>
                <input type="hidden" name="q" value="<?php echo safe_html((string) $_GET['q']); ?>">
            <?php endif; ?>
            <label for="sort-search" class="text-xs uppercase tracking-widest text-[#5E5650] font-semibold whitespace-nowrap">Sort by</label>
            <select id="sort-search" name="sort" aria-label="Sort search results" class="tbe-input py-2 px-3.5 text-base sm:text-sm flex-1 sm:flex-none sm:w-auto sm:min-w-[190px]">
                <option value="#">Relevance</option>
                <option value="price_low" <?php echo (($_GET['sort'] ?? '') === 'price_low') ? 'selected' : ''; ?>>Price: Low to High</option>
                <option value="price_high" <?php echo (($_GET['sort'] ?? '') === 'price_high') ? 'selected' : ''; ?>>Price: High to Low</option>
                <option value="newest" <?php echo (($_GET['sort'] ?? '') === 'newest') ? 'selected' : ''; ?>>Newest</option>
                <option value="oldest" <?php echo (($_GET['sort'] ?? '') === 'oldest') ? 'selected' : ''; ?>>Oldest</option>
            </select>
        </form>
    </div>

    <?php if ($resultCount > 0): ?>
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-6 lg:gap-7">
            <?php
            $idx = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                tbe_render_product_card($row, ['priority_high' => ($idx < 3)]);
                $idx++;
            }
            ?>
        </div>
    <?php else: ?>
        <div class="bg-white border border-[#E6DDD3] rounded-xl text-center py-20 px-6 max-w-2xl mx-auto">
            <h2 class="font-serif text-2xl font-medium text-[#1C1A18] mb-3">No In-Stock Products Matched Your Search</h2>
            <p class="text-sm text-[#5E5650] mb-6">Discover freshly added bouquets and studio favorites in our catalog.</p>
            <a href="/shop/" class="tbe-btn-forest">Browse Shop</a>
        </div>
    <?php endif; ?>
</div>

<?php include_once 'footer.php'; ?>
