<?php
include_once 'header.php';
?>

<!-- Editorial Collections Banner -->
<section class="tbe-banner py-8 sm:py-14 md:py-20">
    <img src="<?php echo USER_IMAGES; ?>about-us-banner.jpg"
        alt=""
        aria-hidden="true"
        fetchpriority="high"
        loading="eager"
        decoding="async"
        class="tbe-banner-media">
    <div class="tbe-banner-scrim" aria-hidden="true"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center relative z-10">
        <nav class="flex items-center justify-center text-xs sm:text-sm text-[#EFE8DC] mb-4 sm:mb-5 gap-2" aria-label="Breadcrumb">
            <a href="/" class="hover:text-white transition-colors">Home</a>
            <span aria-hidden="true">/</span>
            <span class="text-white font-medium" aria-current="page">Shop</span>
        </nav>
        <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl font-medium text-white mb-3">New Collections</h1>
        <p class="text-[#F4EFE6] text-sm sm:text-base md:text-lg max-w-2xl mx-auto">
            Freshly harvested seasonal arrangements, sculpted planters, and bespoke gift boxes composed at our New Delhi studios.
        </p>
    </div>
</section>

<!-- Product Grid -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 md:py-16">
    <?php
    if ($checkProducts = mysqli_query($connection, "SELECT product_id, link_id, link_url, link_published, product_title, product_image, inventory_currency, inventory_in_stock, inventory_currency_symbol, inventory_price, inventory_mrp FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id WHERE inventory_type='product' AND link_type='product' AND link_status=200 AND inventory_in_stock=1 AND link_site_id=" . SITE_ID . " ORDER BY link_published DESC LIMIT 0, 20")) {
        $totalListed = mysqli_num_rows($checkProducts);
    ?>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-5 sm:pb-6 mb-6 sm:mb-8 border-b border-[#E6DDD3]">
            <p class="text-xs uppercase tracking-widest text-[#5E5650] font-medium">
                Showing <strong class="text-[#1C1A18]"><?php echo $totalListed; ?></strong> Latest Creation<?php echo $totalListed === 1 ? '' : 's'; ?>
            </p>
            <div class="flex flex-wrap gap-3 text-xs font-semibold uppercase tracking-widest">
                <a href="/category/flower/" class="text-[#183028] hover:text-[#9B5637] transition-colors">Flowers</a>
                <span class="text-[#D4C8BA]" aria-hidden="true">/</span>
                <a href="/category/planters/" class="text-[#183028] hover:text-[#9B5637] transition-colors">Planters</a>
                <span class="text-[#D4C8BA]" aria-hidden="true">/</span>
                <a href="/category/cake/" class="text-[#183028] hover:text-[#9B5637] transition-colors">Hampers</a>
            </div>
        </div>

        <?php if ($totalListed > 0): ?>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-6 lg:gap-7">
                <?php
                $idx = 0;
                while ($productList = mysqli_fetch_assoc($checkProducts)) {
                    tbe_render_product_card($productList, ['priority_high' => ($idx < 4)]);
                    $idx++;
                }
                ?>
            </div>
        <?php else: ?>
            <div class="bg-white border border-[#E6DDD3] rounded-xl text-center py-20 px-6 max-w-2xl mx-auto">
                <h2 class="font-serif text-2xl font-medium text-[#1C1A18] mb-3">No Creations Available Right Now</h2>
                <a href="/" class="tbe-btn-forest">Go to Storefront</a>
            </div>
        <?php endif; ?>
    <?php
    }
    ?>
</div>

<?php
include_once 'footer.php';
?>
