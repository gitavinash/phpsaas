<?php

if ($checkProductMeta = mysqli_query($connection, "SELECT product_meta_key, product_meta_value FROM " . TABLE_PREFIX . "product_meta WHERE product_meta_relation_id='{$product_id}' AND product_meta_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkProductMeta) != 0) {
        while ($checkProductMetaData = mysqli_fetch_assoc($checkProductMeta)) {
            ${$checkProductMetaData['product_meta_key']} = $checkProductMetaData['product_meta_value'];
        }
    }
}

$effectivePrice = ($inventory_price == 0) ? $inventory_mrp : $inventory_price;
$currencySymbol = !empty($inventory_currency_symbol) ? $inventory_currency_symbol : '₹';
$hasDiscount = ($inventory_mrp > $inventory_price && $inventory_mrp > 0 && $inventory_price > 0);
$discountPercent = $hasDiscount ? round((($inventory_mrp - $inventory_price) / $inventory_mrp) * 100) : 0;

$thumbnails = [];
if (!empty($product_gallery)) {
    $galleryImages = json_decode($product_gallery, true);
    if (!empty($galleryImages) && is_array($galleryImages)) {
        $thumbnails[] = ['path' => $product_image, 'title' => $product_title];
        foreach ($galleryImages as $gallery) {
            if (!empty($gallery['path'])) {
                $thumbnails[] = ['path' => $gallery['path'], 'title' => $product_title];
            }
        }
    }
}
if (empty($thumbnails)) {
    $thumbnails[] = ['path' => $product_image, 'title' => $product_title];
}

include_once 'header.php';
require_once __DIR__ . '/partials/product-card.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-12 pb-28 md:pb-24">
    <!-- Breadcrumb -->
    <nav class="flex items-center text-[#6E675F] mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center text-xs sm:text-sm gap-2">
            <li><a href="/" class="hover:text-[#C47C68] transition-colors">Home</a></li>
            <li aria-hidden="true">/</li>
            <li><a href="/category/subscriptions/" class="hover:text-[#C47C68] transition-colors">Subscriptions</a></li>
            <li aria-hidden="true">/</li>
            <li class="text-[#242220] font-medium" aria-current="page"><?php echo htmlspecialchars($product_title, ENT_QUOTES, 'UTF-8'); ?></li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-start">
        <!-- Gallery Column -->
        <div class="lg:col-span-7">
            <div class="lg:sticky lg:top-28 space-y-4">
                <div class="relative bg-[#FAF7F5] border border-[#E8DDD5] rounded-2xl overflow-hidden aspect-[4/5] shadow-sm">
                    <img id="mainProductImage"
                        src="<?php echo htmlspecialchars($thumbnails[0]['path'], ENT_QUOTES, 'UTF-8'); ?>"
                        alt="<?php echo htmlspecialchars($product_title, ENT_QUOTES, 'UTF-8'); ?>"
                        class="w-full h-full object-cover">
                </div>

                <?php if (count($thumbnails) > 1): ?>
                    <div class="flex items-center gap-3 overflow-x-auto py-2">
                        <?php foreach ($thumbnails as $idx => $thumb): ?>
                            <button type="button"
                                class="thumb-btn flex-shrink-0 w-20 h-20 rounded-xl overflow-hidden border border-[#E8DDD5] hover:border-[#C47C68] transition-all <?php echo $idx === 0 ? 'ring-2 ring-[#C47C68]' : ''; ?>"
                                data-img-src="<?php echo htmlspecialchars($thumb['path'], ENT_QUOTES, 'UTF-8'); ?>">
                                <img src="<?php echo htmlspecialchars($thumb['path'], ENT_QUOTES, 'UTF-8'); ?>"
                                    alt="Thumbnail" class="w-full h-full object-cover">
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Details Column -->
        <div class="lg:col-span-5 space-y-6">
            <div>
                <span class="vl-eyebrow">Living Subscription</span>
                <h1 class="font-serif text-3xl sm:text-4xl text-[#242220] mt-1 mb-3 font-normal">
                    <?php echo htmlspecialchars($product_title, ENT_QUOTES, 'UTF-8'); ?>
                </h1>

                <div class="flex items-baseline gap-3 mb-4">
                    <span class="font-serif text-2xl sm:text-3xl text-[#242220] font-normal">
                        <?php echo $currencySymbol; ?> <?php echo number_format((float) $effectivePrice, 0); ?>
                    </span>
                    <?php if ($hasDiscount): ?>
                        <span class="text-sm sm:text-base text-[#6E675F] line-through">
                            <?php echo $currencySymbol; ?> <?php echo number_format((float) $inventory_mrp, 0); ?>
                        </span>
                        <span class="vl-badge vl-badge-sage">
                            <?php echo $discountPercent; ?>% OFF
                        </span>
                    <?php endif; ?>
                </div>

                <div class="flex items-center gap-2 text-xs">
                    <?php if ($inventory_in_stock > 0): ?>
                        <span class="vl-badge vl-badge-sage">In Stock &bull; Accepting Subscribers</span>
                    <?php else: ?>
                        <span class="vl-badge vl-badge-terracotta">Sold Out</span>
                    <?php endif; ?>
                    <?php if (!empty($inventory_sku)): ?>
                        <span class="text-[#6E675F]">SKU: <?php echo htmlspecialchars($inventory_sku, ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($product_highlight)): ?>
                <div class="p-4 bg-[#FAF7F5] border border-[#E8DDD5] rounded-xl text-xs sm:text-sm text-[#242220] leading-relaxed">
                    <strong class="block text-xs uppercase tracking-wider text-[#C47C68] mb-1">Edition Highlights:</strong>
                    <?php echo htmlspecialchars($product_highlight, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <!-- Frequency Option Selector -->
            <div class="space-y-2">
                <span class="block text-xs uppercase tracking-wider font-semibold text-[#6E675F]">Select Delivery Cadence</span>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" class="vl-filter-tab is-active justify-center text-center">Weekly</button>
                    <button type="button" class="vl-filter-tab justify-center text-center">Fortnightly</button>
                    <button type="button" class="vl-filter-tab justify-center text-center">Monthly</button>
                </div>
            </div>

            <!-- Actions -->
            <div class="pt-4 border-t border-[#E8DDD5] space-y-4">
                <div class="flex items-center gap-4">
                    <div class="vl-qty-control">
                        <button type="button" class="vl-qty-btn" id="qtyMinus" aria-label="Decrease quantity">&minus;</button>
                        <input type="text" id="qtyInput" value="1" readonly class="vl-qty-input" aria-label="Subscription plan quantity">
                        <button type="button" class="vl-qty-btn" id="qtyPlus" aria-label="Increase quantity">&plus;</button>
                    </div>

                    <input type="hidden" id="qty" value="<?php echo (int) $product_id; ?>_1_<?php echo (float) $effectivePrice; ?>">

                    <?php if ($inventory_in_stock > 0): ?>
                        <button type="button" id="addToCart" data-item-type="product"
                            class="flex-1 vl-btn-primary py-3.5 px-6 justify-center">
                            <span>Subscribe to Plan</span>
                            <i class="ri-arrow-right-line" aria-hidden="true"></i>
                        </button>
                    <?php else: ?>
                        <button type="button" disabled class="flex-1 vl-btn-outline py-3.5 px-6 justify-center opacity-60 cursor-not-allowed">
                            Temporarily Unavailable
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Description -->
            <?php if (!empty($product_description)): ?>
                <div class="pt-6 border-t border-[#E8DDD5] space-y-3">
                    <h2 class="font-serif text-xl text-[#242220]">Subscription Overview</h2>
                    <div class="text-sm text-[#6E675F] leading-relaxed space-y-3">
                        <?php echo nl2br(htmlspecialchars($product_description, ENT_QUOTES, 'UTF-8')); ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Trust Highlights -->
            <div class="pt-6 border-t border-[#E8DDD5] space-y-3 text-xs text-[#6E675F]">
                <div class="flex items-center gap-2.5">
                    <i class="ri-calendar-check-line text-base text-[#C47C68]" aria-hidden="true"></i>
                    <span>Pause, reschedule, or cancel deliveries anytime via concierge</span>
                </div>
                <div class="flex items-center gap-2.5">
                    <i class="ri-truck-line text-base text-[#3D5447]" aria-hidden="true"></i>
                    <span>Complimentary temperature-controlled hand delivery across Delhi NCR</span>
                </div>
                <div class="flex items-center gap-2.5">
                    <i class="ri-sparkling-line text-base text-[#C47C68]" aria-hidden="true"></i>
                    <span>Fresh vase arrangement conditioned by master florists with every delivery</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once 'footer.php';
?>
