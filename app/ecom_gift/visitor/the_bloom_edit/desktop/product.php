<?php
$whatsappUrl = 'https://wa.me/919910744119?text=' . ((isset($link_type)) ? (($link_type == 'product') ? urlencode('i want to know about ' . $product_title . ' ' . SITE_URL . ACTIVE_URL) : 'Hello') : 'Hello');

if ($checkProductMeta = mysqli_query($connection, "SELECT product_meta_key, product_meta_value FROM " . TABLE_PREFIX . "product_meta WHERE product_meta_relation_id='{$product_id}' AND product_meta_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkProductMeta) != 0) {
        $productMeta = array();
        while ($checkProductMetaData = mysqli_fetch_assoc($checkProductMeta)) {
            ${$checkProductMetaData['product_meta_key']} = $checkProductMetaData['product_meta_value'];
        }
    }
}

/** Decode and fetch attributes/variations */
$productAttributes = [];
if (isset($inventory_variation) && !empty($inventory_variation)) {
    $decodedVariation = base64_decode($inventory_variation);
    $productAttributes = json_decode($decodedVariation, true) ?: [];
}

/** Decode and fetch add-ons */
$productAddons = [];
$addonProducts = [];
if (isset($inventory_addons) && !empty($inventory_addons)) {
    $decodedAddons = base64_decode($inventory_addons);
    $productAddons = json_decode($decodedAddons, true) ?: [];

    $addonIds = array_column($productAddons, 0);
    $addonIds = array_filter($addonIds, function ($id) use ($product_id) {
        return $id != $product_id;
    });

    if (!empty($addonIds)) {
        $addonIdsStr = implode(',', array_map('intval', $addonIds));
        $addOnQuery = "SELECT p.product_id, p.product_title, p.product_image, 
                             i.inventory_price, i.inventory_mrp, i.inventory_currency_symbol, l.link_url, c.category_title
                      FROM " . TABLE_PREFIX . "product p
                      INNER JOIN " . TABLE_PREFIX . "inventory i ON p.product_id = i.inventory_relation_id AND i.inventory_site_id = " . SITE_ID . "
                      INNER JOIN " . TABLE_PREFIX . "link l ON l.link_relation_id = p.product_id 
                        AND l.link_status = 200 AND l.link_type = 'product'
                      INNER JOIN " . TABLE_PREFIX . "category c ON c.category_id = p.product_category
                      WHERE p.product_status = 1 
                        AND p.product_site_id = " . SITE_ID . " 
                        AND l.link_site_id = " . SITE_ID . " 
                        AND p.product_id IN ({$addonIdsStr})";

        if ($addonResult = mysqli_query($connection, $addOnQuery)) {
            while ($addon = mysqli_fetch_assoc($addonResult)) {
                $addonProducts[] = $addon;
            }
        }
    }
}

if ($getTagIds = mysqli_query($connection, "SELECT * 
FROM " . TABLE_PREFIX . "tag 
WHERE tag_title LIKE CONCAT('%', (
    SELECT tag_title 
    FROM " . TABLE_PREFIX . "tag 
    WHERE tag_relation_id = {$product_id} AND tag_site_id = " . SITE_ID . " AND tag_type = 'product'
    LIMIT 1
), '%')
AND tag_relation_id != {$product_id}
AND tag_site_id = " . SITE_ID . "
AND tag_type = 'product'
AND tag_time >= NOW() - INTERVAL 1 YEAR
GROUP BY tag_relation_id
ORDER BY RAND()
LIMIT 0, 4")) {
    if (mysqli_num_rows($getTagIds) > 0) {
        $tagIds = array();
        while ($tagData = mysqli_fetch_assoc($getTagIds)) {
            $tagIds[] = $tagData['tag_relation_id'];
        }
        $tagList = implode(',', $tagIds);
    }
}

// Fetch all tags for this product
$productTags = array();
if ($getProductTags = mysqli_query($connection, "SELECT DISTINCT tag_title FROM " . TABLE_PREFIX . "tag WHERE
tag_relation_id = {$product_id} AND tag_site_id = " . SITE_ID . " AND tag_type = 'product' ORDER BY tag_title")) {
    while ($tagRow = mysqli_fetch_assoc($getProductTags)) {
        if (isset($tagRow['tag_title']) && $tagRow['tag_title'] !== '') {
            $productTags[] = $tagRow['tag_title'];
        }
    }
}

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

require_once 'header.php';
$galleryJson = safe_html(json_encode($thumbnails, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8 md:py-12 pb-28 md:pb-24">
    <!-- Breadcrumb -->
    <nav class="flex items-center text-[#5E5650] mb-4 sm:mb-8 overflow-x-auto scrollbar-none" aria-label="Breadcrumb">
        <ol class="flex items-center text-xs sm:text-sm whitespace-nowrap gap-1.5 sm:gap-2.5">
            <li>
                <a href="/" class="hover:text-[#183028] transition-colors">Home</a>
            </li>
            <li class="text-[#D4C8BA]" aria-hidden="true">/</li>
            <li>
                <a href="<?php echo safe_html((string) ($brandURL ?? '/shop/')); ?>" class="hover:text-[#183028] transition-colors truncate">
                    <?php echo safe_html(ucwords((string) ($category_title ?? 'Collections'))); ?>
                </a>
            </li>
            <li class="text-[#D4C8BA]" aria-hidden="true">/</li>
            <li class="text-[#1C1A18] font-medium truncate" aria-current="page">
                <?php echo safe_html(ucwords((string) $product_title)); ?>
            </li>
        </ol>
    </nav>

    <!-- Product Hero Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-14 mb-10 sm:mb-16 items-start">
        <!-- Product Gallery -->
        <div class="lg:col-span-6 product-media-sticky md:sticky md:top-28 md:self-start">
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
                                class="gallery-nav-btn prev absolute left-3 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/95 border border-[#E6DDD3] shadow-md text-[#1C1A18] hover:text-[#183028] hover:bg-white flex items-center justify-center transition-all z-10 opacity-90 md:opacity-0 md:group-hover:opacity-100"
                                aria-label="Previous image">
                                <i class="ri-arrow-left-s-line text-xl" aria-hidden="true"></i>
                            </button>
                            <button type="button" id="galleryNextBtn"
                                class="gallery-nav-btn next absolute right-3 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/95 border border-[#E6DDD3] shadow-md text-[#1C1A18] hover:text-[#183028] hover:bg-white flex items-center justify-center transition-all z-10 opacity-90 md:opacity-0 md:group-hover:opacity-100"
                                aria-label="Next image">
                                <i class="ri-arrow-right-s-line text-xl" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- Desktop Zoom Flyout Window -->
                    <div id="zoomFlyout" class="zoom-flyout hidden">
                        <img src="<?php echo safe_html((string) $product_image); ?>"
                            id="zoomFlyoutImg"
                            alt="<?php echo safe_html(ucwords((string) $product_title)); ?> magnified preview"
                            class="zoom-flyout-img">
                        <div class="zoom-flyout-badge">
                            <i class="ri-zoom-in-line" aria-hidden="true"></i>
                            <span>Click image for fullscreen gallery</span>
                        </div>
                    </div>
                </div>

                <?php if (count($thumbnails) > 1): ?>
                    <div class="thumbnails-container flex md:grid md:grid-cols-5 gap-2.5 overflow-x-auto md:overflow-visible pb-2 md:pb-0 scrollbar-none" id="thumbnailsStrip">
                        <?php foreach ($thumbnails as $index => $thumb):
                            $thumbPath = safe_html((string) $thumb['path']);
                            $thumbTitle = safe_html((string) $thumb['title']);
                            $isActive = $index === 0 ? 'border-[#183028] ring-2 ring-[#183028]/25' : 'border-[#E6DDD3] hover:border-[#B48E54]';
                        ?>
                            <button type="button"
                                class="thumbnail-item flex-shrink-0 w-16 h-16 md:w-auto md:h-auto aspect-square rounded-lg overflow-hidden cursor-pointer border-2 <?php echo $isActive; ?> transition-all duration-200 bg-white p-0"
                                data-index="<?php echo $index; ?>"
                                data-src="<?php echo $thumbPath; ?>"
                                data-title="<?php echo $thumbTitle; ?>"
                                aria-label="View image <?php echo $index + 1; ?> of <?php echo count($thumbnails); ?>"
                                aria-pressed="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                                <img src="<?php echo $thumbPath; ?>"
                                    alt="<?php echo $thumbTitle; ?> Thumbnail <?php echo $index + 1; ?>"
                                    class="w-full h-full object-cover select-none pointer-events-none">
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Details -->
        <div class="lg:col-span-6 product-details space-y-6">
            <div>
                <div class="flex flex-wrap items-center gap-3 mb-3">
                    <span class="tbe-eyebrow"><?php echo safe_html(ucwords((string) ($category_title ?? 'Bespoke Arrangement'))); ?></span>
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-[#5E5650]">
                        <i class="ri-star-fill text-[#B48E54]" aria-hidden="true"></i>
                        <span>4.8 Rated by Patrons</span>
                    </span>
                </div>

                <h1 class="font-serif text-2xl sm:text-4xl font-medium text-[#1C1A18] leading-tight mb-3">
                    <?php echo safe_html(ucwords((string) $product_title)); ?>
                </h1>

                <?php
                $filterTags = isset($productTags) ? array_map('trim', array_map('strtolower', $productTags)) : [];
                $hasValentineTag = array_reduce($filterTags, function ($carry, $tag) {
                    return $carry || (bool) preg_match('/valentine/i', $tag);
                }, false);
                $subHeadingText = $hasValentineTag ? 'Love, Unfiltered — Valentine Edition | Limited Creation' : '';
                $resolvedSub = !empty($sub_heading) ? $sub_heading : $subHeadingText;
                if ($resolvedSub !== ''):
                ?>
                    <p class="text-sm text-[#9B5637] font-medium mb-3">
                        <?php echo safe_html((string) $resolvedSub); ?>
                    </p>
                <?php endif; ?>

                <!-- Pricing Presentation -->
                <div class="flex items-baseline flex-wrap gap-3 pt-1">
                    <span class="text-3xl font-serif font-semibold text-[#1C1A18]">
                        <?php echo safe_html($currencySymbol); ?> <?php echo number_format((float) $effectivePrice); ?>
                    </span>
                    <?php if ($inventory_mrp > $inventory_price && $inventory_mrp > 0 && $inventory_price > 0):
                        $discountPercentage = round((1 - $inventory_price / $inventory_mrp) * 100, 0);
                    ?>
                        <span class="text-base text-[#5E5650] line-through">
                            <?php echo safe_html($currencySymbol); ?> <?php echo number_format((float) $inventory_mrp); ?>
                        </span>
                        <span class="tbe-badge tbe-badge-savings">
                            Save <?php echo $discountPercentage; ?>%
                        </span>
                    <?php endif; ?>
                </div>
                <p class="text-xs text-[#5E5650] mt-1.5">Inclusive of hand-tied atelier wrapping &amp; personal gift card</p>
            </div>

            <!-- Concierge Availability -->
            <div class="bg-[#EFE8DC]/70 border border-[#E6DDD3] rounded-xl p-4 space-y-2">
                <div class="flex items-center gap-2.5 text-sm font-medium text-[#183028]">
                    <i class="ri-checkbox-circle-fill text-lg flex-shrink-0" aria-hidden="true"></i>
                    <span>Hand-delivered across Delhi, Gurugram, Noida, Ghaziabad &amp; Faridabad</span>
                </div>
                <p class="text-xs text-[#5E5650] pl-7">
                    Same-day studio dispatch: order <strong id="urgency-timer" class="text-[#9B5637] font-semibold">before 11:00 AM</strong>
                </p>
            </div>

            <?php if (!empty($product_highlight)): ?>
                <div class="bg-white border border-[#E6DDD3] rounded-xl p-4 sm:p-5">
                    <h2 class="font-serif text-lg font-medium text-[#1C1A18] mb-2">Editorial Highlight</h2>
                    <p class="text-sm text-[#5E5650] leading-relaxed">
                        <?php echo nl2br(safe_html((string) $product_highlight)); ?>
                    </p>
                </div>
            <?php endif; ?>

            <?php if (!empty($inventory_weight) || !empty($inventory_lenght) || !empty($inventory_height) || !empty($inventory_width)): ?>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs bg-white border border-[#E6DDD3] rounded-xl p-4">
                    <?php if (!empty($inventory_weight)): ?>
                        <div>
                            <span class="text-[#5E5650] uppercase tracking-wider block">Weight</span>
                            <span class="font-semibold text-[#1C1A18] mt-0.5 block"><?php echo safe_html((string) $inventory_weight); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($inventory_lenght)): ?>
                        <div>
                            <span class="text-[#5E5650] uppercase tracking-wider block">Length</span>
                            <span class="font-semibold text-[#1C1A18] mt-0.5 block"><?php echo safe_html((string) $inventory_lenght); ?> cm</span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($inventory_height)): ?>
                        <div>
                            <span class="text-[#5E5650] uppercase tracking-wider block">Height</span>
                            <span class="font-semibold text-[#1C1A18] mt-0.5 block"><?php echo safe_html((string) $inventory_height); ?> cm</span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($inventory_width)): ?>
                        <div>
                            <span class="text-[#5E5650] uppercase tracking-wider block">Width</span>
                            <span class="font-semibold text-[#1C1A18] mt-0.5 block"><?php echo safe_html((string) $inventory_width); ?> cm</span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php
            // Available Variations
            if (!empty($productAttributes)) {
                $productIds = array();
                foreach ($productAttributes as $attribute) {
                    if (!empty($attribute[0])) {
                        $productIds[] = (int) $attribute[0];
                    }
                }
                if (!empty($productIds)) {
                    $productVarientIds = implode(',', $productIds);
                    if ($productVarient = mysqli_query($connection, "SELECT product_title, product_image, link_url
                        FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id
                        WHERE product_id IN ({$productVarientIds}) AND product_site_id=" . SITE_ID . " AND link_site_id=" . SITE_ID . "
                        AND link_type='product' AND link_status=200")) {
                        if (mysqli_num_rows($productVarient) > 0) {
            ?>
                            <div>
                                <h2 class="font-serif text-lg font-medium text-[#1C1A18] mb-3">Available Variations</h2>
                                <div class="flex overflow-x-auto gap-3 pb-2 scrollbar-none">
                                    <?php while ($productVarientData = mysqli_fetch_assoc($productVarient)): ?>
                                        <a href="<?php echo safe_html((string) $productVarientData['link_url']); ?>"
                                            class="flex-shrink-0 group w-28 sm:w-32 rounded-xl overflow-hidden border border-[#E6DDD3] hover:border-[#183028] transition-all bg-white">
                                            <div class="relative aspect-[4/5] overflow-hidden bg-[#EFE8DC]">
                                                <img src="<?php echo safe_html((string) $productVarientData['product_image']); ?>"
                                                    alt="<?php echo safe_html((string) $productVarientData['product_title']); ?>"
                                                    loading="lazy"
                                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                <div class="absolute inset-0 bg-gradient-to-t from-[#183028]/80 via-transparent to-transparent"></div>
                                                <p class="absolute bottom-0 inset-x-0 p-2 text-white text-xs font-medium line-clamp-2">
                                                    <?php echo safe_html(ucwords((string) $productVarientData['product_title'])); ?>
                                                </p>
                                            </div>
                                        </a>
                                    <?php endwhile; ?>
                                </div>
                            </div>
            <?php
                        }
                    }
                }
            }
            ?>

            <!-- Purchase Action Area -->
            <input type="hidden" id="productInfo"
                value="<?php echo safe_html($product_id . '_' . $effectivePrice . '_1'); ?>"
                data-unit-price="<?php echo (float) $effectivePrice; ?>"
                data-currency="<?php echo safe_html($currencySymbol); ?>">

            <?php if ($inventory_in_stock == 1): ?>
                <div class="desktop-default-cta space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3.5">
                            <label for="quantity" class="text-xs uppercase tracking-widest font-semibold text-[#5E5650]">Quantity</label>
                            <div class="inline-flex items-center border border-[#D4C8BA] rounded-md bg-white overflow-hidden">
                                <button type="button" id="decrease-quantity"
                                    class="w-11 h-11 flex items-center justify-center text-[#1C1A18] hover:bg-[#EFE8DC] transition-colors"
                                    aria-label="Decrease quantity">
                                    <i class="ri-subtract-line" aria-hidden="true"></i>
                                </button>
                                <input type="number" id="quantity" value="1" min="1" max="99" inputmode="numeric" aria-label="Product quantity"
                                    class="w-12 h-11 text-center text-base sm:text-sm font-semibold text-[#1C1A18] border-0 bg-transparent focus:outline-none">
                                <button type="button" id="increase-quantity"
                                    class="w-11 h-11 flex items-center justify-center text-[#1C1A18] hover:bg-[#EFE8DC] transition-colors"
                                    aria-label="Increase quantity">
                                    <i class="ri-add-line" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                        <p class="text-sm text-[#5E5650]">
                            Subtotal:
                            <strong id="pdpQtyTotal" class="font-serif text-base font-semibold text-[#183028]">
                                <?php echo safe_html($currencySymbol . ' ' . number_format((float) $effectivePrice)); ?>
                            </strong>
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="button" id="addToCart" class="flex-1 tbe-btn-forest py-3.5">
                            <i class="ri-shopping-bag-line text-base" aria-hidden="true"></i>
                            <span>Add to Bag</span>
                        </button>
                        <a href="<?php echo safe_html($whatsappUrl); ?>" target="_blank" rel="noopener noreferrer"
                            class="tbe-btn-outline border-[#183028] text-[#183028] hover:bg-[#183028] hover:text-white whitespace-nowrap">
                            <i class="ri-whatsapp-line text-lg" aria-hidden="true"></i>
                            <span>Order via Concierge</span>
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
                </div>
            <?php else: ?>
                <div class="desktop-default-cta flex flex-col sm:flex-row gap-3">
                    <button type="button" id="outOfStock" disabled
                        class="flex-1 tbe-btn-forest bg-[#5E5650] border-[#5E5650] opacity-75 cursor-not-allowed">
                        <i class="ri-close-circle-line" aria-hidden="true"></i>
                        <span>Currently Out of Stock</span>
                    </button>
                    <a href="<?php echo safe_html($whatsappUrl); ?>" target="_blank" rel="noopener noreferrer"
                        class="tbe-btn-outline whitespace-nowrap">
                        <i class="ri-whatsapp-line text-lg" aria-hidden="true"></i>
                        <span>Inquire Custom Restock</span>
                    </a>
                </div>
            <?php endif; ?>

            <!-- Mobile Fixed Bottom CTA -->
            <div class="tbe-mobile-sticky-bar md:hidden">
                <div class="flex items-center gap-3">
                    <div class="min-w-0 shrink-0">
                        <p class="text-[10px] uppercase tracking-widest text-[#5E5650] font-semibold">Total</p>
                        <p id="pdpMobilePrice" class="text-base font-serif font-semibold text-[#183028] leading-tight">
                            <?php echo safe_html($currencySymbol . ' ' . number_format((float) $effectivePrice)); ?>
                        </p>
                    </div>
                    <?php if ($inventory_in_stock == 1): ?>
                        <button type="button" data-trigger-add-to-cart
                            class="flex-1 tbe-btn-forest py-3 min-h-[44px]">
                            <i class="ri-shopping-bag-line" aria-hidden="true"></i>
                            <span>Add to Bag</span>
                        </button>
                    <?php else: ?>
                        <button type="button" disabled
                            class="flex-1 tbe-btn-forest py-3 min-h-[44px] bg-[#5E5650] border-[#5E5650] opacity-75 cursor-not-allowed">
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

            <!-- Add-ons Recommendations -->
            <?php if (!empty($addonProducts)):
                $addonCategories = array();
                foreach ($addonProducts as $addon) {
                    if (!empty($addon['category_title']) && !in_array($addon['category_title'], $addonCategories, true)) {
                        $addonCategories[] = $addon['category_title'];
                    }
                }
            ?>
                <section class="bg-[#F4E9E4]/65 border border-[#E6DDD3] rounded-xl p-5 space-y-4">
                    <div>
                        <span class="tbe-eyebrow mb-1">Artisan Pairings</span>
                        <h2 class="font-serif text-xl font-medium text-[#1C1A18] flex items-center gap-2">
                            <i class="ri-gift-line text-[#9B5637]" aria-hidden="true"></i>
                            <span>Complete Your Gesture</span>
                        </h2>
                        <p class="text-xs text-[#5E5650] mt-0.5">Pair your bouquet with hand-selected chocolates, vases, or keepsakes.</p>
                    </div>

                    <?php if (!empty($addonCategories)): ?>
                        <div class="flex flex-nowrap overflow-x-auto scrollbar-none sm:flex-wrap gap-2 pb-1" role="group" aria-label="Filter add-on categories">
                            <button type="button"
                                class="addon-filter-btn active is-active whitespace-nowrap shrink-0 min-h-[38px] text-xs px-3.5 py-1.5 rounded-full border border-[#183028] bg-[#183028] text-white font-semibold transition-colors"
                                data-category="all" aria-pressed="true">
                                All
                            </button>
                            <?php foreach ($addonCategories as $cat): ?>
                                <button type="button"
                                    class="addon-filter-btn whitespace-nowrap shrink-0 min-h-[38px] text-xs px-3.5 py-1.5 rounded-full border border-[#D4C8BA] text-[#5E5650] hover:border-[#183028] hover:text-[#183028] font-semibold transition-colors bg-white"
                                    data-category="<?php echo safe_html(strtolower(str_replace(' ', '-', $cat))); ?>"
                                    aria-pressed="false">
                                    <?php echo safe_html(ucwords($cat)); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3.5">
                        <?php foreach ($addonProducts as $addon):
                            $addonId = (int) $addon['product_id'];
                            $addonTitle = safe_html(ucwords((string) $addon['product_title']));
                            $addonImage = safe_html((string) $addon['product_image']);
                            $addonUrl = safe_html((string) $addon['link_url']);
                            $addonPrice = (float) $addon['inventory_price'];
                            $addonMrp = (float) $addon['inventory_mrp'];
                            $addonCurr = safe_html((string) ($addon['inventory_currency_symbol'] ?: '₹'));
                            $addonCatSlug = safe_html(strtolower(str_replace(' ', '-', (string) $addon['category_title'])));
                            $discountPercent = ($addonMrp > 0 && $addonPrice < $addonMrp) ? round((1 - $addonPrice / $addonMrp) * 100, 0) : 0;
                        ?>
                            <article class="addon-product-card relative bg-white rounded-xl overflow-hidden border border-[#E6DDD3] hover:border-[#183028] hover:shadow-md transition-all duration-300 group flex flex-col"
                                data-category="<?php echo $addonCatSlug; ?>">
                                <div class="relative w-full aspect-square overflow-hidden bg-[#EFE8DC]">
                                    <img src="<?php echo $addonImage; ?>" alt="<?php echo $addonTitle; ?>" loading="lazy"
                                        class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
                                    <?php if ($discountPercent > 0): ?>
                                        <span class="tbe-badge tbe-badge-savings absolute top-2 right-2">
                                            -<?php echo $discountPercent; ?>%
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="p-3 flex flex-col flex-1 gap-2">
                                    <h3 class="font-serif text-sm font-medium text-[#1C1A18] leading-snug line-clamp-2 min-h-[2.4rem]">
                                        <a href="<?php echo $addonUrl; ?>" class="tbe-card-link">
                                            <?php echo $addonTitle; ?>
                                        </a>
                                    </h3>
                                    <div class="flex items-baseline gap-1.5 flex-wrap">
                                        <span class="text-sm font-semibold text-[#1C1A18]">
                                            <?php echo $addonCurr . number_format($addonPrice); ?>
                                        </span>
                                        <?php if ($addonMrp > 0 && $addonPrice < $addonMrp): ?>
                                            <span class="text-xs text-[#5E5650] line-through">
                                                <?php echo $addonCurr . number_format($addonMrp); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <button type="button"
                                        data-info="<?php echo $addonId . '_' . $addonPrice . '_1'; ?>"
                                        class="addAddOnProductToCart relative z-10 mt-auto w-full py-2 text-xs bg-[#183028] hover:bg-[#9B5637] text-white rounded-md font-semibold uppercase tracking-wider transition-colors flex items-center justify-center gap-1">
                                        <i class="ri-add-circle-line" aria-hidden="true"></i>
                                        <span>Add Pairing</span>
                                    </button>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Atelier Assurance Badges -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                <div class="bg-white border border-[#E6DDD3] rounded-xl p-3 sm:p-4 text-center">
                    <i class="ri-truck-line text-xl text-[#183028] mb-1.5 block" aria-hidden="true"></i>
                    <p class="text-xs font-semibold text-[#1C1A18]">Same-Day Dispatch</p>
                    <p class="text-[11px] text-[#5E5650] mt-0.5">Delhi NCR Hand Delivery</p>
                </div>
                <div class="bg-white border border-[#E6DDD3] rounded-xl p-3 sm:p-4 text-center">
                    <i class="ri-plant-line text-xl text-[#183028] mb-1.5 block" aria-hidden="true"></i>
                    <p class="text-xs font-semibold text-[#1C1A18]">Freshness Pledge</p>
                    <p class="text-[11px] text-[#5E5650] mt-0.5">Studio Sourced Daily</p>
                </div>
                <div class="bg-white border border-[#E6DDD3] rounded-xl p-3 sm:p-4 text-center">
                    <i class="ri-award-line text-xl text-[#183028] mb-1.5 block" aria-hidden="true"></i>
                    <p class="text-xs font-semibold text-[#1C1A18]">Artisanal Craft</p>
                    <p class="text-[11px] text-[#5E5650] mt-0.5">Hand-Tied to Order</p>
                </div>
                <div class="bg-white border border-[#E6DDD3] rounded-xl p-3 sm:p-4 text-center">
                    <i class="ri-shield-check-line text-xl text-[#183028] mb-1.5 block" aria-hidden="true"></i>
                    <p class="text-xs font-semibold text-[#1C1A18]">Encrypted Payment</p>
                    <p class="text-[11px] text-[#5E5650] mt-0.5">Razorpay Protected</p>
                </div>
            </div>
        </div>
    </div>

    <!-- WAI-ARIA Product Information Tabs -->
    <section class="mb-16 bg-white border border-[#E6DDD3] rounded-xl overflow-hidden">
        <div class="border-b border-[#E6DDD3] bg-[#FAF7F2] px-4 sm:px-6 overflow-x-auto">
            <div class="flex gap-2 sm:gap-6 -mb-px" role="tablist" aria-label="Product details">
                <button type="button"
                    id="tab-btn-description"
                    role="tab"
                    aria-selected="true"
                    aria-controls="description-content"
                    tabindex="0"
                    class="tab-button py-4 px-2 sm:px-4 border-b-2 border-[#183028] text-[#183028] font-medium text-sm whitespace-nowrap transition-colors"
                    data-tab="description">
                    Composition &amp; Story
                </button>
                <button type="button"
                    id="tab-btn-delivery"
                    role="tab"
                    aria-selected="false"
                    aria-controls="delivery-content"
                    tabindex="-1"
                    class="tab-button py-4 px-2 sm:px-4 border-b-2 border-transparent text-[#5E5650] hover:text-[#183028] font-medium text-sm whitespace-nowrap transition-colors"
                    data-tab="delivery">
                    Delivery Information
                </button>
                <button type="button"
                    id="tab-btn-care"
                    role="tab"
                    aria-selected="false"
                    aria-controls="care-content"
                    tabindex="-1"
                    class="tab-button py-4 px-2 sm:px-4 border-b-2 border-transparent text-[#5E5650] hover:text-[#183028] font-medium text-sm whitespace-nowrap transition-colors"
                    data-tab="care">
                    Care Guide
                </button>
            </div>
        </div>

        <div class="p-6 sm:p-8">
            <div id="description-content"
                role="tabpanel"
                aria-labelledby="tab-btn-description"
                class="tab-content active">
                <h2 class="font-serif text-xl font-medium text-[#1C1A18] mb-3">Arrangement Overview</h2>
                <div class="sameFont text-[#5E5650] text-sm sm:text-base leading-relaxed">
                    <?php echo $product_description; ?>
                </div>
            </div>

            <div id="delivery-content"
                role="tabpanel"
                aria-labelledby="tab-btn-delivery"
                class="tab-content"
                hidden>
                <h2 class="font-serif text-xl font-medium text-[#1C1A18] mb-3">White-Glove Delivery Information</h2>
                <div class="sameFont text-[#5E5650] text-sm sm:text-base leading-relaxed">
                    <?php
                    if (isset($delivery_information) && $delivery_information != '') {
                        echo '<ul><li>' . str_replace('<br />', '</li><li>', $delivery_information) . '</li></ul>';
                    } else {
                        if (strtolower((string) $category_title) != 'planters' && strtolower((string) $category_title) != 'cake') {
                    ?>
                            <ul>
                                <li>Photographs depict studio compositions; natural stem seasonal variations may occur while preserving palette and volume.</li>
                                <li>Blooms may arrive partially in bud to extend vase longevity across upcoming days.</li>
                                <li>Selected delivery time windows are studio target windows across Delhi NCR.</li>
                                <li>Due to the perishable nature of fresh botanicals, one coordinated courier attempt is made per schedule.</li>
                            </ul>
                    <?php
                        } else {
                            echo '<p>Carefully packaged and couriered from our Delhi NCR atelier in protective luxury presentation.</p>';
                        }
                    }
                    ?>
                </div>
            </div>

            <div id="care-content"
                role="tabpanel"
                aria-labelledby="tab-btn-care"
                class="tab-content"
                hidden>
                <h2 class="font-serif text-xl font-medium text-[#1C1A18] mb-3">Botanical Care &amp; Longevity</h2>
                <div class="sameFont text-[#5E5650] text-sm sm:text-base leading-relaxed">
                    <?php
                    if (isset($care_n_tips) && $care_n_tips != '') {
                        echo '<ul><li>' . str_replace('<br />', '</li><li>', $care_n_tips) . '</li></ul>';
                    } else {
                        if (strtolower((string) $category_title) != 'planters' && strtolower((string) $category_title) != 'cake') {
                    ?>
                            <ul>
                                <li>Unfasten decorative wrapping gently before placing stems in fresh room-temperature water.</li>
                                <li>Trim stems diagonally by 1–2 cm beneath cool running water prior to arranging.</li>
                                <li>Remove any foliage that sits beneath the waterline and refresh water every 48 hours.</li>
                                <li>Display away from direct midday sun, radiators, and ripening fruit bowls.</li>
                            </ul>
                    <?php
                        } else {
                            echo '<p>Keep away from direct sunlight and follow care card instructions enclosed with your delivery.</p>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Products -->
    <?php if (isset($tagList)): ?>
        <section class="mb-14">
            <div class="mb-8">
                <span class="tbe-eyebrow mb-1">Curated Companions</span>
                <h2 class="font-serif text-2xl sm:text-3xl font-medium text-[#1C1A18]">You May Also Like</h2>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-6 lg:gap-7">
                <?php
                if ($getProductList = mysqli_query($connection, "SELECT DISTINCT 
                    p.product_id, 
                    p.product_title, 
                    p.product_image, 
                    i.inventory_price, 
                    i.inventory_mrp, 
                    i.inventory_currency_symbol, 
                    l.link_url
                FROM " . TABLE_PREFIX . "product p
                INNER JOIN " . TABLE_PREFIX . "inventory i ON p.product_id = i.inventory_relation_id
                INNER JOIN " . TABLE_PREFIX . "link l ON l.link_relation_id = p.product_id 
                    AND l.link_status = 200 
                    AND l.link_type = 'product'
                WHERE p.product_status = 1 
                  AND p.product_site_id = " . SITE_ID . " 
                  AND l.link_site_id = " . SITE_ID . " 
                  AND p.product_id IN ({$tagList})")) {
                    while ($productData = mysqli_fetch_assoc($getProductList)) {
                        tbe_render_product_card($productData);
                    }
                }
                ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- Product Occasion Tags -->
    <?php if (!empty($productTags)): ?>
        <div class="mb-6 pt-6 border-t border-[#E6DDD3]">
            <p class="text-xs uppercase tracking-widest text-[#5E5650] font-semibold mb-3">Occasions &amp; Themes</p>
            <div class="flex flex-wrap gap-2">
                <?php foreach ($productTags as $tagTitle):
                    $tagName = safe_html((string) $tagTitle);
                    $tagSlug = rawurlencode(str_replace(' ', '-', strtolower((string) $tagTitle)));
                ?>
                    <a href="/tag/<?php echo $tagSlug; ?>/"
                        class="px-3.5 py-1.5 rounded-full border border-[#E6DDD3] bg-white text-xs font-medium text-[#5E5650] hover:border-[#183028] hover:text-[#183028] transition-colors">
                        <?php echo ucwords($tagName); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if ($inventory_in_stock == 1): ?>
    <!-- Desktop Sticky Quick-Action Bar -->
    <div class="tbe-pdp-sticky desktop-sticky-cta hidden md:block">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 flex items-center justify-between gap-6">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="<?php echo safe_html((string) $product_image); ?>"
                    alt=""
                    class="w-12 h-12 rounded-lg object-cover border border-[#E6DDD3] flex-shrink-0">
                <div class="min-w-0">
                    <p class="font-serif text-base font-medium text-[#1C1A18] truncate">
                        <?php echo safe_html(ucwords((string) $product_title)); ?>
                    </p>
                    <p class="text-sm font-semibold text-[#183028]">
                        <?php echo safe_html($currencySymbol); ?> <?php echo number_format((float) $effectivePrice); ?>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3 flex-shrink-0">
                <a href="<?php echo safe_html($whatsappUrl); ?>" target="_blank" rel="noopener noreferrer"
                    class="tbe-btn-outline text-xs py-2.5 px-4">
                    <i class="ri-whatsapp-line text-base" aria-hidden="true"></i>
                    <span>WhatsApp Concierge</span>
                </a>
                <button type="button" data-trigger-add-to-cart class="tbe-btn-forest text-xs py-2.5 px-6">
                    <i class="ri-shopping-bag-line" aria-hidden="true"></i>
                    <span>Add to Bag</span>
                </button>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Product Fullscreen Lightbox Modal -->
<div id="productLightboxModal" class="lightbox-modal fixed inset-0 z-[100] hidden bg-[#1C1A18]/95 backdrop-blur-md flex-col select-none" role="dialog" aria-modal="true" aria-label="Product Image Gallery">
    <div class="tbe-lightbox-topbar flex items-center justify-between px-4 sm:px-6 pb-3 bg-black/50 border-b border-white/10 z-30 shrink-0">
        <div class="flex items-center gap-3 min-w-0 flex-1 mr-2">
            <span class="font-serif text-sm font-medium text-white truncate max-w-[150px] sm:max-w-md"><?php echo safe_html(ucwords((string) $product_title)); ?></span>
            <span id="lightboxCounter" class="text-xs text-[#EFE8DC] font-mono bg-white/10 px-2.5 py-0.5 rounded whitespace-nowrap shrink-0">1 of <?php echo count($thumbnails); ?></span>
        </div>

        <div class="flex items-center gap-1.5 shrink-0">
            <button type="button" id="lightboxZoomOut" class="w-11 h-11 sm:w-9 sm:h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition" aria-label="Zoom out" title="Zoom Out (-)">
                <i class="ri-zoom-out-line text-lg" aria-hidden="true"></i>
            </button>
            <button type="button" id="lightboxZoomIn" class="w-11 h-11 sm:w-9 sm:h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition" aria-label="Zoom in" title="Zoom In (+)">
                <i class="ri-zoom-in-line text-lg" aria-hidden="true"></i>
            </button>
            <button type="button" id="lightboxZoomReset" class="w-11 h-11 sm:w-9 sm:h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition" aria-label="Reset zoom" title="Reset Zoom">
                <i class="ri-refresh-line text-lg" aria-hidden="true"></i>
            </button>
            <button type="button" id="lightboxCloseBtn" class="w-11 h-11 sm:w-9 sm:h-9 rounded-full bg-white/20 hover:bg-[#9B5637] text-white flex items-center justify-center transition ml-1" aria-label="Close gallery" title="Close (Esc)">
                <i class="ri-close-line text-xl" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <div class="flex-1 relative overflow-hidden flex items-center justify-center p-2 sm:p-6" id="lightboxStage">
        <button type="button" id="lightboxPrevBtn" class="absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-black/60 hover:bg-[#183028] border border-white/20 text-white flex items-center justify-center transition z-20" aria-label="Previous image">
            <i class="ri-arrow-left-s-line text-2xl" aria-hidden="true"></i>
        </button>

        <div class="w-full h-full flex items-center justify-center cursor-grab active:cursor-grabbing overflow-hidden" id="lightboxViewport">
            <img src="<?php echo safe_html((string) $product_image); ?>" id="lightboxImage"
                alt="<?php echo safe_html(ucwords((string) $product_title)); ?>"
                class="max-w-full max-h-full object-contain select-none transition-transform duration-150">
        </div>

        <button type="button" id="lightboxNextBtn" class="absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-black/60 hover:bg-[#183028] border border-white/20 text-white flex items-center justify-center transition z-20" aria-label="Next image">
            <i class="ri-arrow-right-s-line text-2xl" aria-hidden="true"></i>
        </button>
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
require_once 'footer.php';
?>
