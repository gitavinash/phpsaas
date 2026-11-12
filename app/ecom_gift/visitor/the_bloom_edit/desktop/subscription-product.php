<?php

if ($checkProductMeta = mysqli_query($connection, "SELECT product_meta_key, product_meta_value FROM " . TABLE_PREFIX . "product_meta WHERE product_meta_relation_id='{$product_id}' AND product_meta_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkProductMeta) != 0) {
        $productMeta = array();
        while ($checkProductMetaData = mysqli_fetch_assoc($checkProductMeta)) {
            ${$checkProductMetaData['product_meta_key']} = $checkProductMetaData['product_meta_value'];
        }
    }
}

$whatsappUrl = 'https://wa.me/919910744119?text=' . urlencode('I want to inquire about ' . ($product_title ?? 'subscription') . ' ' . SITE_URL . ACTIVE_URL);
$effectivePrice = ($inventory_price == 0) ? $inventory_mrp : $inventory_price;
$currencySymbol = !empty($inventory_currency_symbol) ? $inventory_currency_symbol : '₹';

$thumbnails = array();
if (!empty($product_gallery)) {
    $galleryImages = json_decode($product_gallery, true);
    if (!empty($galleryImages) && is_array($galleryImages)) {
        $thumbnails[] = array('path' => $product_image, 'title' => $product_title);
        foreach ($galleryImages as $gallery) {
            if (!empty($gallery['path'])) {
                $thumbnails[] = array('path' => $gallery['path'], 'title' => $product_title);
            }
        }
    }
}
if (empty($thumbnails)) {
    $thumbnails[] = array('path' => $product_image, 'title' => $product_title);
}
include_once 'header.php';
$galleryJson = safe_html(json_encode($thumbnails, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8 md:py-12 pb-28 md:pb-20">
    <!-- Breadcrumb -->
    <nav class="flex items-center text-[#5E5650] mb-4 sm:mb-8 overflow-x-auto scrollbar-none" aria-label="Breadcrumb">
        <ol class="flex items-center text-xs sm:text-sm whitespace-nowrap gap-1.5 sm:gap-2.5">
            <li>
                <a href="/" class="hover:text-[#183028] transition-colors">Home</a>
            </li>
            <li class="text-[#D4C8BA]" aria-hidden="true">/</li>
            <li>
                <a href="/shop/" class="hover:text-[#183028] transition-colors">Collections</a>
            </li>
            <li class="text-[#D4C8BA]" aria-hidden="true">/</li>
            <li class="text-[#1C1A18] font-medium truncate" aria-current="page">
                <?php echo safe_html(ucwords((string) $product_title)); ?>
            </li>
        </ol>
    </nav>

    <!-- Subscription Hero Split -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-14 mb-10 sm:mb-16 items-start">
        <div class="lg:col-span-6 md:sticky md:top-28 md:self-start">
            <div class="product-gallery-wrapper">
                <div class="product-main-view relative mb-3">
                    <div class="product-image-container relative aspect-square rounded-xl overflow-hidden bg-white border border-[#E6DDD3] shadow-sm cursor-crosshair group select-none"
                        id="productMainContainer"
                        data-gallery-images="<?php echo $galleryJson; ?>">
                        <div id="productAmbientBg" class="product-ambient-bg"></div>
                        <img src="<?php echo safe_html((string) $product_image); ?>"
                            id="mainProductImage"
                            alt="<?php echo safe_html(ucwords((string) $product_title)); ?>"
                            fetchpriority="high"
                            loading="eager"
                            decoding="async"
                            class="w-full h-full object-cover object-center select-none pointer-events-none relative z-10">
                        <div id="zoomLens" class="zoom-lens hidden"></div>
                        <div class="absolute top-3 left-3 bg-[#FAF7F2]/92 backdrop-blur-sm text-[#1C1A18] border border-[#E6DDD3] text-xs px-3 py-1.5 rounded-full shadow-sm flex items-center gap-1.5 pointer-events-none transition-opacity duration-200 group-hover:opacity-0 z-10">
                            <i class="ri-zoom-in-line text-[#9B5637] text-sm" aria-hidden="true"></i>
                            <span class="hidden md:inline font-medium">Hover to inspect</span>
                            <span class="md:hidden font-medium">Tap to expand</span>
                        </div>
                        <span id="galleryCounter" class="absolute bottom-3 right-3 bg-[#183028]/80 backdrop-blur-sm text-white text-xs px-2.5 py-1 rounded-full font-medium z-10 pointer-events-none md:hidden">
                            1 / <?php echo count($thumbnails); ?>
                        </span>
                        <?php if (count($thumbnails) > 1): ?>
                            <button type="button" id="galleryPrevBtn"
                                class="gallery-nav-btn prev absolute left-3 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/95 border border-[#E6DDD3] shadow-md text-[#1C1A18] flex items-center justify-center transition z-10 opacity-90 md:opacity-0 md:group-hover:opacity-100"
                                aria-label="Previous image">
                                <i class="ri-arrow-left-s-line text-xl" aria-hidden="true"></i>
                            </button>
                            <button type="button" id="galleryNextBtn"
                                class="gallery-nav-btn next absolute right-3 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/95 border border-[#E6DDD3] shadow-md text-[#1C1A18] flex items-center justify-center transition z-10 opacity-90 md:opacity-0 md:group-hover:opacity-100"
                                aria-label="Next image">
                                <i class="ri-arrow-right-s-line text-xl" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                    <div id="zoomFlyout" class="zoom-flyout hidden">
                        <img src="<?php echo safe_html((string) $product_image); ?>" id="zoomFlyoutImg" alt="" class="zoom-flyout-img">
                        <div class="zoom-flyout-badge"><i class="ri-zoom-in-line" aria-hidden="true"></i> Click to expand</div>
                    </div>
                </div>

                <?php if (count($thumbnails) > 1): ?>
                    <div class="thumbnails-container flex md:grid md:grid-cols-5 gap-2.5 overflow-x-auto pb-2 scrollbar-none" id="thumbnailsStrip">
                        <?php foreach ($thumbnails as $index => $thumb):
                            $thumbPath = safe_html((string) $thumb['path']);
                            $isActive = $index === 0 ? 'border-[#183028] ring-2 ring-[#183028]/25' : 'border-[#E6DDD3] hover:border-[#B48E54]';
                        ?>
                            <button type="button"
                                class="thumbnail-item flex-shrink-0 w-16 h-16 md:w-auto md:h-auto aspect-square rounded-lg overflow-hidden cursor-pointer border-2 <?php echo $isActive; ?> transition-all bg-white p-0"
                                data-index="<?php echo $index; ?>"
                                data-src="<?php echo $thumbPath; ?>"
                                data-title="<?php echo safe_html((string) $thumb['title']); ?>"
                                aria-label="View image <?php echo $index + 1; ?>"
                                aria-pressed="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                                <img src="<?php echo $thumbPath; ?>" alt="" class="w-full h-full object-cover pointer-events-none">
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="lg:col-span-6 space-y-6" id="productDetails">
            <div>
                <div class="flex flex-wrap items-center gap-3 mb-3">
                    <span class="tbe-eyebrow">Floral Subscription Edit</span>
                    <?php if ($inventory_in_stock == 1): ?>
                        <span class="tbe-badge bg-[#ECFDF5] text-[#166534] border border-[#BBF7D0]">Available</span>
                    <?php else: ?>
                        <span class="tbe-badge tbe-badge-soldout">Out of Stock</span>
                    <?php endif; ?>
                </div>

                <h1 class="font-serif text-2xl sm:text-4xl font-medium text-[#1C1A18] leading-tight mb-4">
                    <?php echo safe_html(ucwords((string) $product_title)); ?>
                </h1>

                <input type="hidden" id="productInfo"
                    value="<?php echo safe_html($product_id . '_' . $effectivePrice . '_1'); ?>"
                    data-unit-price="<?php echo (float) $effectivePrice; ?>"
                    data-currency="<?php echo safe_html($currencySymbol); ?>">
                <input type="hidden" id="qty"
                    value="<?php echo safe_html($product_id . '_1_' . $effectivePrice); ?>">

                <div class="flex items-baseline flex-wrap gap-3">
                    <span class="text-3xl font-serif font-semibold text-[#1C1A18]">
                        <?php echo safe_html($currencySymbol); ?> <?php echo number_format((float) $effectivePrice); ?>
                    </span>
                    <?php if (!empty($inventory_mrp) && $inventory_price != 0 && $inventory_mrp > $inventory_price):
                        $discountPercentage = round((1 - $inventory_price / $inventory_mrp) * 100, 0);
                    ?>
                        <span class="text-base text-[#5E5650] line-through">
                            <?php echo safe_html($currencySymbol); ?> <?php echo number_format((float) $inventory_mrp); ?>
                        </span>
                        <span class="tbe-badge tbe-badge-savings">
                            <?php echo $discountPercentage; ?>% OFF
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($product_highlight)): ?>
                <div class="bg-white border border-[#E6DDD3] rounded-xl p-5">
                    <p class="text-xs uppercase tracking-widest font-semibold text-[#5E5650] mb-1.5">What&rsquo;s Included</p>
                    <p class="text-sm text-[#1C1A18] leading-relaxed">
                        <?php echo nl2br(safe_html((string) $product_highlight)); ?>
                    </p>
                </div>
            <?php endif; ?>

            <div class="flex items-center gap-2.5 bg-[#EFE8DC]/70 border border-[#E6DDD3] rounded-xl px-4 py-3 text-sm text-[#183028] font-medium">
                <i class="ri-truck-line text-lg" aria-hidden="true"></i>
                <span>Same-day complimentary studio dispatch across Delhi NCR</span>
            </div>

            <div class="flex flex-col sm:flex-row gap-3.5 pt-1">
                <?php if ($inventory_in_stock == 1): ?>
                    <button type="button" id="addToCart" class="flex-1 tbe-btn-forest py-3.5">
                        <i class="ri-shopping-bag-line" aria-hidden="true"></i>
                        <span>Buy Subscription</span>
                    </button>
                <?php else: ?>
                    <button type="button" disabled class="flex-1 tbe-btn-forest py-3.5 bg-[#5E5650] border-[#5E5650] opacity-75 cursor-not-allowed">
                        <span>Out of Stock</span>
                    </button>
                <?php endif; ?>
                <a href="<?php echo safe_html($whatsappUrl); ?>" target="_blank" rel="noopener noreferrer" class="tbe-btn-outline">
                    <i class="ri-whatsapp-line text-lg" aria-hidden="true"></i>
                    <span>Consult Concierge</span>
                </a>
            </div>

            <div id="pdpPostAddBar" class="tbe-post-add-bar hidden" role="status" aria-live="polite">
                <div class="flex flex-wrap items-center justify-between gap-2.5">
                    <p id="pdpPostAddMsg" class="text-xs sm:text-sm font-medium text-[#183028] flex items-center gap-1.5">
                        <i class="ri-checkbox-circle-fill text-base" aria-hidden="true"></i>
                        <span>Added to your floral bag.</span>
                    </p>
                    <a href="/checkout/" class="inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-wider text-[#9B5637] hover:text-[#183028] underline underline-offset-4 py-1">
                        <span>View Bag &amp; Checkout</span>
                        <i class="ri-arrow-right-line" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <div class="bg-white border border-[#E6DDD3] rounded-xl p-6 space-y-3">
                <h2 class="font-serif text-xl font-medium text-[#1C1A18]">Arrangement Overview</h2>
                <div class="sameFont text-sm sm:text-base text-[#5E5650] leading-relaxed">
                    <?php echo $product_description; ?>
                </div>
                <?php if (!empty($inventory_sku)): ?>
                    <p class="text-xs uppercase tracking-widest text-[#5E5650] pt-2 border-t border-[#E6DDD3]">
                        Reference SKU: <strong class="text-[#1C1A18]"><?php echo safe_html((string) $inventory_sku); ?></strong>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Related Creations -->
    <section class="mb-16">
        <div class="mb-8">
            <span class="tbe-eyebrow mb-1">Further Discovery</span>
            <h2 class="font-serif text-2xl sm:text-3xl font-medium text-[#1C1A18]">You May Also Like</h2>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-6 lg:gap-7">
            <?php
            if ($tagListData = mysqli_query($connection, "SELECT tag_title as relatedTags FROM isw_tag WHERE tag_relation_id='{$product_id}' LIMIT 0, 1")) {
                if (mysqli_num_rows($tagListData) != 0) {
                    extract(mysqli_fetch_assoc($tagListData));
                    $relatedProductQuery = "SELECT product_id, link_url, product_title, product_image, inventory_currency, inventory_currency_symbol, inventory_price, inventory_mrp, inventory_in_stock FROM isw_tag INNER JOIN isw_link ON link_relation_id=tag_relation_id INNER JOIN isw_product ON product_id=link_relation_id INNER JOIN isw_inventory ON inventory_relation_id=product_id WHERE tag_title LIKE '%{$relatedTags}%' AND link_status=200 AND link_type='product' AND link_site_id='" . SITE_ID . "' AND inventory_in_stock=1 AND product_id!={$product_id} GROUP BY tag_relation_id ORDER BY RAND() LIMIT 0, 8";
                } else {
                    if ($product_type == 'subscription') {
                        $relatedProductQuery = "SELECT product_id, link_url, product_title, product_image, inventory_currency, inventory_currency_symbol, inventory_price, inventory_mrp, inventory_in_stock FROM isw_link INNER JOIN isw_product ON product_id=link_relation_id INNER JOIN isw_inventory ON inventory_relation_id=product_id WHERE product_type='subscription' AND link_status=200 AND link_type='product' AND link_site_id='" . SITE_ID . "' AND inventory_in_stock=1 AND product_id!={$product_id} ORDER BY RAND() LIMIT 0, 8";
                    } else {
                        $productRelated = explode(' ', $product_title);
                        $relatedProductQuery = "SELECT product_id, link_url, product_title, product_image, inventory_currency, inventory_currency_symbol, inventory_price, inventory_mrp, inventory_in_stock FROM isw_tag INNER JOIN isw_link ON link_relation_id=tag_relation_id INNER JOIN isw_product ON product_id=link_relation_id INNER JOIN isw_inventory ON inventory_relation_id=product_id WHERE tag_title LIKE '%{$productRelated[0]}%' AND link_status=200 AND link_type='product' AND link_site_id='" . SITE_ID . "' AND inventory_in_stock=1 AND product_id!={$product_id} GROUP BY tag_relation_id ORDER BY RAND() LIMIT 0, 8";
                    }
                }
            }

            if (isset($relatedProductQuery) && $relatedProductsData = mysqli_query($connection, $relatedProductQuery)) {
                if (mysqli_num_rows($relatedProductsData) != 0) {
                    while ($relatedProducts = mysqli_fetch_assoc($relatedProductsData)) {
                        tbe_render_product_card($relatedProducts);
                    }
                } else {
                    echo '<p class="col-span-full text-sm text-[#5E5650]">Explore our main collections for seasonal compositions.</p>';
                }
            }
            ?>
        </div>
    </section>

    <!-- Studio Signature Pillars -->
    <section class="pt-12 border-t border-[#E6DDD3]">
        <div class="text-center max-w-xl mx-auto mb-10">
            <span class="tbe-eyebrow mb-2">The &amp;Blooms Standard</span>
            <h2 class="font-serif text-2xl sm:text-3xl font-medium text-[#1C1A18]">Curated Atelier Craft</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-7">
            <article class="group relative overflow-hidden rounded-xl border border-[#E6DDD3] bg-[#EFE8DC] aspect-[4/3]">
                <img src="<?php echo USER_IMAGES; ?>artistic-flowers.jpg" alt="Fresh Flower selection" loading="lazy"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-[#183028]/80 via-transparent to-transparent"></div>
                <h3 class="absolute bottom-5 left-6 font-serif text-xl font-medium text-white">Fresh Flowers</h3>
            </article>

            <article class="group relative overflow-hidden rounded-xl border border-[#E6DDD3] bg-[#EFE8DC] aspect-[4/3]">
                <img src="<?php echo USER_IMAGES; ?>freshness-flower.jpg" alt="Artistic floral arranging" loading="lazy"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-[#183028]/80 via-transparent to-transparent"></div>
                <h3 class="absolute bottom-5 left-6 font-serif text-xl font-medium text-white">Artistic Compositions</h3>
            </article>

            <article class="group relative overflow-hidden rounded-xl border border-[#E6DDD3] bg-[#EFE8DC] aspect-[4/3]">
                <img src="<?php echo USER_IMAGES; ?>vases-an-accessories.jpg" alt="Vases and studio accessories" loading="lazy"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-[#183028]/80 via-transparent to-transparent"></div>
                <h3 class="absolute bottom-5 left-6 font-serif text-xl font-medium text-white">Vases &amp; Accessories</h3>
            </article>
        </div>
    </section>
</div>

<!-- Mobile Fixed Bottom Subscription Bar -->
<div class="tbe-mobile-sticky-bar md:hidden">
    <div class="flex items-center gap-3">
        <div class="min-w-0 shrink-0">
            <p class="text-[10px] uppercase tracking-widest text-[#5E5650] font-semibold">Plan Price</p>
            <p class="text-base font-serif font-semibold text-[#183028] leading-tight">
                <?php echo safe_html($currencySymbol . ' ' . number_format((float) $effectivePrice)); ?>
            </p>
        </div>
        <?php if ($inventory_in_stock == 1): ?>
            <button type="button" data-trigger-add-to-cart class="flex-1 tbe-btn-forest py-3 min-h-[44px]">
                <i class="ri-shopping-bag-line" aria-hidden="true"></i>
                <span>Buy Subscription</span>
            </button>
        <?php else: ?>
            <button type="button" disabled class="flex-1 tbe-btn-forest py-3 min-h-[44px] bg-[#5E5650] border-[#5E5650] opacity-75 cursor-not-allowed">
                <span>Out of Stock</span>
            </button>
        <?php endif; ?>
        <a href="<?php echo safe_html($whatsappUrl); ?>" target="_blank" rel="noopener noreferrer"
            aria-label="Inquire on WhatsApp"
            class="w-11 h-11 shrink-0 inline-flex items-center justify-center rounded-md bg-[#EFE8DC] text-[#183028] border border-[#D4C8BA]">
            <i class="ri-whatsapp-line text-xl" aria-hidden="true"></i>
        </a>
    </div>
</div>

<!-- Lightbox Modal -->
<div id="productLightboxModal" class="lightbox-modal fixed inset-0 z-[100] hidden bg-[#1C1A18]/95 backdrop-blur-md flex-col select-none" role="dialog" aria-modal="true" aria-label="Product Image Gallery">
    <div class="tbe-lightbox-topbar flex items-center justify-between px-4 sm:px-6 pb-3 bg-black/50 border-b border-white/10 z-30 shrink-0">
        <div class="flex items-center gap-3 min-w-0 flex-1 mr-2">
            <span class="font-serif text-sm font-medium text-white truncate max-w-[150px] sm:max-w-md"><?php echo safe_html(ucwords((string) $product_title)); ?></span>
            <span id="lightboxCounter" class="text-xs text-[#EFE8DC] font-mono bg-white/10 px-2.5 py-0.5 rounded whitespace-nowrap shrink-0">1 of <?php echo count($thumbnails); ?></span>
        </div>
        <div class="flex items-center gap-1.5 shrink-0">
            <button type="button" id="lightboxZoomOut" class="w-11 h-11 sm:w-9 sm:h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition" aria-label="Zoom out"><i class="ri-zoom-out-line text-lg" aria-hidden="true"></i></button>
            <button type="button" id="lightboxZoomIn" class="w-11 h-11 sm:w-9 sm:h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition" aria-label="Zoom in"><i class="ri-zoom-in-line text-lg" aria-hidden="true"></i></button>
            <button type="button" id="lightboxZoomReset" class="w-11 h-11 sm:w-9 sm:h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition" aria-label="Reset zoom"><i class="ri-refresh-line text-lg" aria-hidden="true"></i></button>
            <button type="button" id="lightboxCloseBtn" class="w-11 h-11 sm:w-9 sm:h-9 rounded-full bg-white/20 hover:bg-[#9B5637] text-white flex items-center justify-center transition ml-1" aria-label="Close gallery"><i class="ri-close-line text-xl" aria-hidden="true"></i></button>
        </div>
    </div>
    <div class="flex-1 relative overflow-hidden flex items-center justify-center p-2 sm:p-6" id="lightboxStage">
        <button type="button" id="lightboxPrevBtn" class="absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-black/60 hover:bg-[#183028] border border-white/20 text-white flex items-center justify-center transition z-20" aria-label="Previous image"><i class="ri-arrow-left-s-line text-2xl" aria-hidden="true"></i></button>
        <div class="w-full h-full flex items-center justify-center overflow-hidden" id="lightboxViewport">
            <img src="<?php echo safe_html((string) $product_image); ?>" id="lightboxImage" alt="<?php echo safe_html(ucwords((string) $product_title)); ?>" class="max-w-full max-h-full object-contain select-none transition-transform duration-150">
        </div>
        <button type="button" id="lightboxNextBtn" class="absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-black/60 hover:bg-[#183028] border border-white/20 text-white flex items-center justify-center transition z-20" aria-label="Next image"><i class="ri-arrow-right-s-line text-2xl" aria-hidden="true"></i></button>
    </div>
    <?php if (count($thumbnails) > 1): ?>
        <div class="tbe-lightbox-thumbbar px-4 pt-3 bg-black/60 border-t border-white/10 overflow-x-auto flex items-center justify-center gap-2.5 z-20 scrollbar-none" id="lightboxThumbnailsStrip">
            <?php foreach ($thumbnails as $index => $thumb):
                $thumbPath = safe_html((string) $thumb['path']);
                $isActive = $index === 0 ? 'border-[#B48E54] ring-2 ring-[#B48E54]' : 'border-white/20 opacity-60 hover:opacity-100';
            ?>
                <button type="button"
                    class="lightbox-thumb-item w-14 h-14 sm:w-16 sm:h-16 flex-shrink-0 rounded-lg overflow-hidden cursor-pointer border-2 <?php echo $isActive; ?> transition-all bg-white/5 p-0"
                    data-index="<?php echo $index; ?>"
                    data-src="<?php echo $thumbPath; ?>"
                    aria-label="Select image <?php echo $index + 1; ?>">
                    <img src="<?php echo $thumbPath; ?>" alt="" class="w-full h-full object-cover select-none pointer-events-none">
                </button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="<?php echo USER_JS ?>product-gallery.min.js?v=<?php echo assetVersion('js/product-gallery.min.js'); ?>"></script>
<?php
include_once 'footer.php';
?>
