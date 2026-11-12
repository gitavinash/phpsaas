<?php include_once 'header.php'; ?>

<?php if (is_array($tagPost)):
    $tagIds = array_map(fn($t) => (int)$t['tag_relation_id'], $tagPost);
    $tagIds = array_filter($tagIds, fn($id) => $id > 0);
    $resultTags = implode(',', $tagIds);

    $tagTitleSafe = safe_html(ucwords((string) $tag_title));

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

    $result = null;
    if (!empty($resultTags)) {
        $query = "SELECT link_url, link_published, product_id, product_title, product_image, inventory_currency_symbol, inventory_price, inventory_mrp, inventory_in_stock 
                  FROM " . TABLE_PREFIX . "link 
                  INNER JOIN " . TABLE_PREFIX . "product ON link_relation_id=product_id 
                  INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id 
                  WHERE link_relation_id IN ($resultTags) AND link_site_id=" . SITE_ID . " 
                  AND link_type='product' AND link_status=200 GROUP BY link_url ORDER BY $sortBy";
        $result = mysqli_query($connection, $query);
    }
    $resultCount = ($result) ? mysqli_num_rows($result) : 0;

    $bannerImage = $tag_image ?? '';
    $bannerFromProduct = false;
    if (empty($bannerImage) && $resultCount > 0) {
        mysqli_data_seek($result, 0);
        $firstProduct = mysqli_fetch_assoc($result);
        $bannerImage = $firstProduct['product_image'] ?? '';
        $bannerFromProduct = !empty($bannerImage);
        mysqli_data_seek($result, 0);
    }
?>

<!-- Valentine Editorial Banner -->
<section class="tbe-banner py-10 sm:py-16 md:py-24">
    <?php if (!empty($bannerImage)): ?>
        <img src="<?php echo safe_html((string) $bannerImage); ?>" alt="" aria-hidden="true" fetchpriority="high" loading="eager" decoding="async"
            class="tbe-banner-media<?php echo $bannerFromProduct ? ' is-soft-blur' : ''; ?>">
    <?php endif; ?>
    <div class="tbe-banner-scrim" aria-hidden="true"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center relative z-10">
        <nav class="flex items-center justify-center text-xs sm:text-sm text-[#EFE8DC] mb-3 sm:mb-5 gap-2" aria-label="Breadcrumb">
            <a href="/" class="hover:text-white transition-colors">Home</a>
            <span aria-hidden="true">/</span>
            <span class="text-white font-medium" aria-current="page"><?php echo $tagTitleSafe; ?></span>
        </nav>
        <span class="inline-block text-xs uppercase tracking-[0.22em] font-semibold text-[#F4E9E4] mb-2 sm:mb-3">
            Love, Unfiltered &mdash; Limited Seasonal Edit
        </span>
        <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl font-medium text-white mb-2 sm:mb-4"><?php echo $tagTitleSafe; ?></h1>
        <?php if (!empty($tag_description)): ?>
            <p class="text-[#F4EFE6] text-sm sm:text-base md:text-lg max-w-2xl mx-auto leading-relaxed">
                <?php echo safe_html((string) $tag_description); ?>
            </p>
        <?php endif; ?>
    </div>
</section>

<!-- Product Grid -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 md:py-14">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 pb-5 sm:pb-6 mb-6 sm:mb-8 border-b border-[#E6DDD3]">
        <p class="text-xs uppercase tracking-widest text-[#5E5650] font-medium">
            Showing <strong class="text-[#1C1A18]"><?php echo $resultCount; ?></strong> Romantic Arrangement<?php echo $resultCount === 1 ? '' : 's'; ?>
        </p>

        <form method="GET" class="flex items-center justify-between sm:justify-end gap-3 w-full sm:w-auto">
            <label for="sort-valentine" class="text-xs uppercase tracking-widest text-[#5E5650] font-semibold whitespace-nowrap">Sort by</label>
            <select id="sort-valentine" name="sort" aria-label="Sort Valentine products" class="tbe-input py-2 px-3.5 text-base sm:text-sm flex-1 sm:flex-none sm:w-auto sm:min-w-[190px]">
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
                    'badge_label'   => 'Valentine Edit',
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
            <a href="/shop/" class="tbe-btn-forest">Explore All Flowers</a>
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
