<?php
include_once 'header.php';
$resultCount = (isset($productList) && is_array($productList)) ? count($productList) : 0;
$safeFilterType = safe_html(ucwords((string) ($filterType ?? 'Collection')));
?>

<!-- Editorial Filter Banner -->
<section class="tbe-banner py-8 sm:py-14 md:py-20">
    <img src="<?php echo USER_IMAGES; ?>about-us-banner.jpg" alt="" aria-hidden="true" fetchpriority="high" loading="eager" decoding="async" class="tbe-banner-media">
    <div class="tbe-banner-scrim" aria-hidden="true"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center relative z-10">
        <nav class="flex items-center justify-center text-xs sm:text-sm text-[#EFE8DC] mb-3 sm:mb-5 gap-2" aria-label="Breadcrumb">
            <a href="/" class="hover:text-white transition-colors">Home</a>
            <span aria-hidden="true">/</span>
            <a href="<?php echo safe_html((string) ACTIVE_URL); ?>" class="hover:text-white transition-colors">Filter</a>
            <span aria-hidden="true">/</span>
            <span class="text-white font-medium" aria-current="page"><?php echo $safeFilterType; ?></span>
        </nav>
        <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl font-medium text-white mb-2 sm:mb-3"><?php echo $safeFilterType; ?></h1>
        <p class="text-[#F4EFE6] text-sm sm:text-base md:text-lg">
            Creations priced between ₹<?php echo safe_html((string) ($min ?? '0')); ?> and ₹<?php echo safe_html((string) ($max ?? '')); ?>
        </p>
    </div>
</section>

<!-- Filtered Product Grid -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 md:py-14">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 pb-5 sm:pb-6 mb-6 sm:mb-8 border-b border-[#E6DDD3]">
        <p class="text-xs uppercase tracking-widest text-[#5E5650] font-medium">
            Showing <strong class="text-[#1C1A18]"><?php echo $resultCount; ?></strong> Filtered Creation<?php echo $resultCount === 1 ? '' : 's'; ?>
        </p>

        <div class="flex items-center justify-between sm:justify-end gap-3 w-full sm:w-auto">
            <label for="sort-filter" class="text-xs uppercase tracking-widest text-[#5E5650] font-semibold whitespace-nowrap">Sort by</label>
            <select id="sort-filter" name="sort" aria-label="Sort filtered products" class="sorting tbe-input py-2 px-3.5 text-base sm:text-sm flex-1 sm:flex-none sm:w-auto sm:min-w-[190px]">
                <option value="#">Featured</option>
                <option value="latest" <?php echo ((@$_GET['sort'] == 'latest') ? 'selected' : ''); ?>>Latest Arrivals</option>
                <option value="price_low" <?php echo ((@$_GET['sort'] == 'price_low') ? 'selected' : ''); ?>>Price: Low to High</option>
                <option value="price_high" <?php echo ((@$_GET['sort'] == 'price_high') ? 'selected' : ''); ?>>Price: High to Low</option>
            </select>
        </div>
    </div>

    <?php if ($resultCount > 0): ?>
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-6 lg:gap-7">
            <?php
            foreach ($productList as $idx => $item) {
                tbe_render_product_card($item, ['priority_high' => ($idx < 3)]);
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
            <h2 class="font-serif text-2xl font-medium text-[#1C1A18] mb-3">No Creations Matched This Price Edit</h2>
            <p class="text-sm text-[#5E5650] mb-6">Explore our full storefront collection for seasonal bouquets and gifts.</p>
            <a href="/shop/" class="tbe-btn-forest">Return to Shop</a>
        </div>
    <?php endif; ?>
</div>

<?php
include_once 'footer.php';
?>
