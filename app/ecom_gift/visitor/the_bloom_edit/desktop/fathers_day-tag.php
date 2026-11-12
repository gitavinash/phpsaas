<?php include_once 'header.php'; ?>

<?php if (is_array($tagPost)):
    $tagIds = array_map(fn($t) => (int)$t['tag_relation_id'], $tagPost);
    $tagIds = array_filter($tagIds, fn($id) => $id > 0);
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
            case 'newest':
                $sortBy = 'link_published DESC';
                break;
            case 'oldest':
                $sortBy = 'link_published ASC';
                break;
        }
    }

    $result = null;
    if (!empty($resultTags)) {
        $query = "SELECT link_url, link_published, product_id, product_title, product_image, inventory_currency_symbol, inventory_price, inventory_mrp, inventory_in_stock 
                  FROM " . TABLE_PREFIX . "link 
                  INNER JOIN " . TABLE_PREFIX . "product ON link_relation_id=product_id 
                  INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id AND inventory_type='product' AND inventory_site_id=" . SITE_ID . " 
                  WHERE link_relation_id IN ($resultTags) AND link_site_id=" . SITE_ID . " 
                  AND link_type='product' AND link_status=200 
                  GROUP BY link_url 
                  ORDER BY $sortBy";
        $result = mysqli_query($connection, $query);
    }
    $resultCount = ($result) ? mysqli_num_rows($result) : 0;
    $heroImage = !empty($tag_image) ? $tag_image : (USER_IMAGES . 'fathers-day-banner.jpg');
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-8 md:pt-12">
    <div class="tbe-fd-banner relative shadow-sm mb-6 sm:mb-8">
        <img src="<?php echo safe_html((string) $heroImage); ?>"
            alt="Father's Day Collection"
            fetchpriority="high"
            loading="eager"
            decoding="async">
    </div>

    <div class="text-center mb-8 sm:mb-10">
        <a href="#giftCollection" class="tbe-btn-forest">
            <span>Shop Father&rsquo;s Day Gifts</span>
            <i class="ri-arrow-down-line" aria-hidden="true"></i>
        </a>
    </div>
</div>

<!-- Product Grid -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12 sm:pb-16">
    <div id="giftCollection" class="text-center max-w-2xl mx-auto mb-8 sm:mb-10">
        <span class="tbe-eyebrow mb-2">Father&rsquo;s Day Collection</span>
        <p class="text-xs font-semibold uppercase tracking-widest text-[#9B5637] mb-3">Limited Time Studio Specials</p>
        <h1 class="font-serif text-2xl sm:text-3xl md:text-5xl font-medium text-[#1C1A18] mb-3">
            Find The Perfect Gift For Dad
        </h1>
        <p class="text-[#5E5650] text-sm md:text-base leading-relaxed">
            Celebrate the man who has always stood beside you with architectural planters, botanical sets, and bespoke hampers.
        </p>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 pb-5 sm:pb-6 mb-6 sm:mb-8 border-b border-[#E6DDD3]">
        <p class="text-xs uppercase tracking-widest text-[#5E5650] font-medium">
            Showing <strong class="text-[#1C1A18]"><?php echo $resultCount; ?></strong> Signature Gift<?php echo $resultCount === 1 ? '' : 's'; ?>
        </p>

        <form method="GET" class="flex items-center justify-between sm:justify-end gap-3 w-full sm:w-auto">
            <label for="sort-fathers-day" class="text-xs uppercase tracking-widest text-[#5E5650] font-semibold whitespace-nowrap">Sort by</label>
            <select id="sort-fathers-day" name="sort" aria-label="Sort Father's Day products" class="tbe-input py-2 px-3.5 text-base sm:text-sm flex-1 sm:flex-none sm:w-auto sm:min-w-[190px]">
                <option value="#">Featured</option>
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
            mysqli_data_seek($result, 0);
            $idx = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                tbe_render_product_card($row, [
                    'badge_label'   => "Dad's Edit",
                    'priority_high' => ($idx < 3),
                ]);
                $idx++;
            }
            ?>
        </div>
        <?php
        if (defined('PAGE_NO') && defined('TOTAL_PAGE')) {
            require_once 'pagination.php';
        }
        ?>
    <?php else: ?>
        <div class="bg-white border border-[#E6DDD3] rounded-xl text-center py-20 px-6 max-w-2xl mx-auto">
            <h2 class="font-serif text-2xl font-medium text-[#1C1A18] mb-3">No Creations Found for This Edit</h2>
            <a href="/shop/" class="tbe-btn-forest">Explore All Gifts</a>
        </div>
    <?php endif; ?>
</div>

<?php else: ?>
    <div class="max-w-2xl mx-auto px-4 py-20">
        <div class="bg-white border border-[#E6DDD3] rounded-xl text-center py-16 px-6">
            <h1 class="font-serif text-2xl font-medium text-[#1C1A18] mb-4">Collection Not Found</h1>
            <a href="/" class="tbe-btn-forest">Return to Storefront</a>
        </div>
    </div>
<?php endif; ?>

<?php include_once 'footer.php'; ?>
