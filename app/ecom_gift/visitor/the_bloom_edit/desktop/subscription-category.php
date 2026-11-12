<?php
include_once 'header.php';
?>

<!-- Editorial Banner Section -->
<section class="tbe-banner py-8 sm:py-14 md:py-20">
    <img src="<?php echo USER_IMAGES; ?>about-us-banner.jpg"
        alt=""
        aria-hidden="true"
        fetchpriority="high"
        loading="eager"
        decoding="async"
        class="tbe-banner-media">
    <div class="tbe-banner-scrim" aria-hidden="true"></div>

    <div class="relative z-10 max-w-4xl mx-auto px-4 text-center">
        <nav class="flex items-center justify-center text-xs sm:text-sm text-[#EFE8DC] mb-4 sm:mb-5 gap-2" aria-label="Breadcrumb">
            <a href="/" class="hover:text-white transition-colors">Home</a>
            <span aria-hidden="true">/</span>
            <a href="<?php echo safe_html((string) ACTIVE_URL); ?>" class="hover:text-white transition-colors">Subscriptions</a>
            <span aria-hidden="true">/</span>
            <span class="text-white font-medium" aria-current="page"><?php echo safe_html(ucwords((string) $category_title)); ?></span>
        </nav>
        <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl font-medium text-white mb-3">
            <?php echo safe_html(ucwords((string) $category_title)); ?>
        </h1>
        <?php if (!empty($category_description)): ?>
            <p class="text-[#F4EFE6] text-sm sm:text-base md:text-lg max-w-2xl mx-auto leading-relaxed">
                <?php echo safe_html((string) $category_description); ?>
            </p>
        <?php endif; ?>
    </div>
</section>

<!-- Subscription Products Grid -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-6 lg:gap-7">
        <?php
        if ($productList = mysqli_query($connection, "SELECT product_id, link_url, product_title, product_image, product_highlight, inventory_price, inventory_currency_symbol, inventory_in_stock, inventory_mrp, link_published FROM " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "product ON link_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id INNER JOIN isw_category ON category_id=product_category WHERE link_site_id=" . SITE_ID . " AND link_type='product' AND link_status=200 AND product_type='subscription' AND inventory_type='product' AND product_category={$category_id} ORDER BY inventory_price+0 ASC LIMIT 0, 100")) {
            if (mysqli_num_rows($productList) != 0) {
                $idx = 0;
                while ($productData = mysqli_fetch_assoc($productList)) {
                    $subNote = ((string) ($productData['product_highlight'] ?? '') !== '1') ? (string) ($productData['product_highlight'] ?? '') : '';
                    tbe_render_product_card($productData, [
                        'subtitle'      => $subNote,
                        'aspect_class'  => 'aspect-[4/3]',
                        'priority_high' => ($idx < 4),
                    ]);
                    $idx++;
                }
            } else {
                echo '<div class="col-span-full bg-white border border-[#E6DDD3] rounded-xl py-16 px-6 text-center text-[#5E5650]">No active subscription editions found in this category.</div>';
            }
        }
        ?>
    </div>
</section>

<!-- Subscription Editorial FAQ -->
<section class="bg-white border-t border-[#E6DDD3] py-10 sm:py-16 md:py-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-12">
            <span class="tbe-eyebrow mb-2">Membership Guide</span>
            <h2 class="font-serif text-2xl sm:text-3xl md:text-4xl font-medium text-[#1C1A18]">Bespoke Floral Subscriptions</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
            <div class="bg-[#FAF7F2] border border-[#E6DDD3] rounded-xl p-4 sm:p-6">
                <h3 class="font-serif text-xl font-medium text-[#1C1A18] mb-2">What is an &amp;Blooms subscription?</h3>
                <p class="text-sm text-[#5E5650] leading-relaxed">
                    Thoughtfully curated seasonal blooms delivered on your preferred cadence — ideal for private residences, ateliers, and meaningful year-round gifting.
                </p>
            </div>
            <div class="bg-[#FAF7F2] border border-[#E6DDD3] rounded-xl p-6">
                <h3 class="font-serif text-xl font-medium text-[#1C1A18] mb-2">Our Studio Happiness Pledge</h3>
                <p class="text-sm text-[#5E5650] leading-relaxed">
                    Every stem is conditioned fresh prior to dispatch. Should any delivery fall short of your expectations, our concierge team makes it right promptly.
                </p>
            </div>
            <div class="bg-[#FAF7F2] border border-[#E6DDD3] rounded-xl p-6">
                <h3 class="font-serif text-xl font-medium text-[#1C1A18] mb-2">Flexible Pauses &amp; Rescheduling</h3>
                <p class="text-sm text-[#5E5650] leading-relaxed">
                    Traveling or away from home? Pause, skip, or redirect an upcoming floral arrival effortlessly with our concierge support on +91-9910744119.
                </p>
            </div>
            <div class="bg-[#FAF7F2] border border-[#E6DDD3] rounded-xl p-6">
                <h3 class="font-serif text-xl font-medium text-[#1C1A18] mb-2">Recurring Member Savings</h3>
                <p class="text-sm text-[#5E5650] leading-relaxed">
                    Enjoy preferential pricing and complimentary hand-tied delivery across Delhi NCR on recurring seasonal subscriptions.
                </p>
            </div>
        </div>
    </div>
</section>

<?php
include_once 'footer.php';
?>
