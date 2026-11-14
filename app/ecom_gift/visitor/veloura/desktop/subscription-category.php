<?php

include_once 'header.php';
require_once __DIR__ . '/partials/product-card.php';

$catTitle = !empty($category_title) ? htmlspecialchars($category_title, ENT_QUOTES, 'UTF-8') : 'Floral Subscriptions';
$catDesc = !empty($category_description) ? htmlspecialchars($category_description, ENT_QUOTES, 'UTF-8') : 'Fresh botanical arrangements delivered recurringly to your doorstep.';
?>

<!-- Editorial Banner -->
<section class="py-10 sm:py-16 md:py-20 bg-[#FAF7F5] border-b border-[#E8DDD5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <nav class="flex items-center justify-center text-xs sm:text-sm text-[#6E675F] mb-4 gap-2" aria-label="Breadcrumb">
            <a href="/" class="hover:text-[#C47C68] transition-colors">Home</a>
            <span aria-hidden="true">/</span>
            <span class="text-[#242220] font-medium" aria-current="page"><?php echo $catTitle; ?></span>
        </nav>
        <span class="vl-eyebrow">Recurring Living Art</span>
        <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl text-[#242220] mb-3"><?php echo $catTitle; ?></h1>
        <p class="text-sm sm:text-base text-[#6E675F] max-w-xl mx-auto font-light">
            <?php echo $catDesc; ?>
        </p>
    </div>
</section>

<!-- Subscription Offerings Grid -->
<section class="py-12 sm:py-16 px-4 sm:px-6 lg:px-8 bg-white border-b border-[#E8DDD5]">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
            <?php
            $catIdEsc = (int) ($category_id ?? 0);
            $query = "
                SELECT 
                    l.link_url, 
                    l.link_published,
                    p.product_id, 
                    p.product_title, 
                    p.product_image, 
                    p.product_highlight, 
                    i.inventory_price, 
                    i.inventory_currency_symbol, 
                    i.inventory_in_stock, 
                    i.inventory_mrp 
                FROM " . TABLE_PREFIX . "link l
                INNER JOIN " . TABLE_PREFIX . "product p ON l.link_relation_id = p.product_id 
                INNER JOIN " . TABLE_PREFIX . "inventory i ON i.inventory_relation_id = p.product_id 
                WHERE l.link_site_id = " . SITE_ID . " 
                  AND l.link_type = 'product' 
                  AND l.link_status = 200 
                  AND p.product_type = 'subscription' 
                  AND i.inventory_type = 'product' 
                  AND p.product_category = {$catIdEsc} 
                ORDER BY i.inventory_price ASC 
                LIMIT 0, 100";

            if ($productList = mysqli_query($connection, $query)) {
                if (mysqli_num_rows($productList) > 0) {
                    while ($row = mysqli_fetch_assoc($productList)) {
                        echo veloura_render_product_card($row);
                    }
                } else {
                    echo "<div class='col-span-full py-16 text-center text-[#6E675F]'>No subscription tiers currently active in this category.</div>";
                }
            }
            ?>
        </div>
    </div>
</section>

<!-- Subscription Benefits & Philosophy -->
<section class="py-16 md:py-24 bg-[#FAF7F5]">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="vl-eyebrow">The Subscription Experience</span>
            <h2 class="font-serif text-3xl sm:text-4xl text-[#242220] mt-1 mb-3 font-normal">
                Effortless Floral Luxury, Week After Week
            </h2>
            <p class="text-sm text-[#6E675F]">
                Designed for discerning homes, corporate executive suites, and perpetual gifting.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white border border-[#E8DDD5] rounded-2xl p-6 sm:p-8 shadow-sm">
                <div class="w-10 h-10 rounded-full bg-[#FAF7F5] text-[#C47C68] flex items-center justify-center text-lg mb-4">
                    <i class="ri-calendar-check-line" aria-hidden="true"></i>
                </div>
                <h3 class="font-serif text-xl text-[#242220] mb-2">Flexible Frequency</h3>
                <p class="text-sm text-[#6E675F] leading-relaxed">
                    Choose weekly, fortnightly, or monthly deliveries. Pause, skip dates, or redirect your delivery to another address anytime with our concierge.
                </p>
            </div>

            <div class="bg-white border border-[#E8DDD5] rounded-2xl p-6 sm:p-8 shadow-sm">
                <div class="w-10 h-10 rounded-full bg-[#FAF7F5] text-[#3D5447] flex items-center justify-center text-lg mb-4">
                    <i class="ri-sparkling-line" aria-hidden="true"></i>
                </div>
                <h3 class="font-serif text-xl text-[#242220] mb-2">Curated Seasonal Rotations</h3>
                <p class="text-sm text-[#6E675F] leading-relaxed">
                    Every dispatch features newly arrived seasonal blooms, exotic foliage, and complementary ceramic vessel options so your space remains perpetually fresh.
                </p>
            </div>

            <div class="bg-white border border-[#E8DDD5] rounded-2xl p-6 sm:p-8 shadow-sm">
                <div class="w-10 h-10 rounded-full bg-[#FAF7F5] text-[#C47C68] flex items-center justify-center text-lg mb-4">
                    <i class="ri-shield-star-line" aria-hidden="true"></i>
                </div>
                <h3 class="font-serif text-xl text-[#242220] mb-2">The Freshness Guarantee</h3>
                <p class="text-sm text-[#6E675F] leading-relaxed">
                    We stand unconditionally behind our stem quality. If any bloom falters within 48 hours, we dispatch a complimentary replacement arrangement immediately.
                </p>
            </div>

            <div class="bg-white border border-[#E8DDD5] rounded-2xl p-6 sm:p-8 shadow-sm">
                <div class="w-10 h-10 rounded-full bg-[#FAF7F5] text-[#3D5447] flex items-center justify-center text-lg mb-4">
                    <i class="ri-gift-line" aria-hidden="true"></i>
                </div>
                <h3 class="font-serif text-xl text-[#242220] mb-2">Exclusive Subscriber Privilege</h3>
                <p class="text-sm text-[#6E675F] leading-relaxed">
                    Enjoy 15% privilege savings on all ad-hoc bouquet orders, complimentary greeting cards, and priority booking for all Veloura workshops.
                </p>
            </div>
        </div>
    </div>
</section>

<?php
include_once 'footer.php';
?>
