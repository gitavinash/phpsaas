<?php
if (empty($product_id)) {
    http_response_code(404);
    if (defined('ERROR_404') && file_exists(ERROR_404)) {
        include_once ERROR_404;
    } else {
        include_once __DIR__ . '/404.php';
    }
    return;
}

$whatsappUrl = 'https://wa.me/919910744119?text=' . ((isset($link_type)) ? (($link_type == 'product') ? urlencode('Hello Veloura Atelier, I would like to enquire about ' . $product_title . ' ' . SITE_URL . ACTIVE_URL) : 'Hello') : 'Hello');

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
                      INNER JOIN " . TABLE_PREFIX . "inventory i ON p.product_id = i.inventory_relation_id AND i.inventory_type = 'product' AND i.inventory_site_id = " . SITE_ID . "
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
    <nav class="flex items-center text-[#635E59] mb-4 sm:mb-8 overflow-x-auto scrollbar-none" aria-label="Breadcrumb">
        <ol class="flex items-center text-xs sm:text-sm whitespace-nowrap gap-1.5 sm:gap-2.5">
            <li>
                <a href="/" class="hover:text-[#C47C68] transition-colors">Home</a>
            </li>
            <li class="text-[#D5C4B7]" aria-hidden="true">/</li>
            <li>
                <a href="<?php echo safe_html((string) ($brandURL ?? '/shop/')); ?>" class="hover:text-[#C47C68] transition-colors truncate">
                    <?php echo safe_html(ucwords((string) ($category_title ?? 'Collections'))); ?>
                </a>
            </li>
            <li class="text-[#D5C4B7]" aria-hidden="true">/</li>
            <li class="text-[#242220] font-medium truncate" aria-current="page">
                <?php echo safe_html(ucwords((string) $product_title)); ?>
            </li>
        </ol>
    </nav>

    <!-- Product Hero Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 mb-12 sm:mb-16 items-start">
        <!-- Product Gallery -->
        <div class="lg:col-span-6 md:sticky md:top-28 md:self-start">
            <div class="product-gallery-wrapper">
                <div class="product-main-view relative mb-3">
                    <div class="product-image-container relative aspect-square rounded-2xl overflow-hidden bg-white border border-[#E8DDD5] shadow-sm select-none"
                        id="productMainContainer"
                        data-gallery-images="<?php echo $galleryJson; ?>">
                        <img src="<?php echo safe_html($product_image); ?>"
                            alt="<?php echo safe_html($product_title); ?>"
                            id="mainProductImage"
                            class="w-full h-full object-cover"
                            fetchpriority="high"
                            loading="eager"
                            decoding="async">
                    </div>
                </div>

                <!-- Thumbnails -->
                <?php if (count($thumbnails) > 1): ?>
                    <div class="flex gap-2.5 overflow-x-auto pb-2 scrollbar-none" id="productThumbnails">
                        <?php foreach ($thumbnails as $idx => $t): ?>
                            <button type="button"
                                class="w-16 h-16 rounded-lg overflow-hidden border-2 transition-all flex-shrink-0 <?php echo $idx === 0 ? 'border-[#C47C68]' : 'border-[#E8DDD5] opacity-70 hover:opacity-100'; ?>"
                                data-thumb-src="<?php echo safe_html($t['path']); ?>"
                                aria-label="<?php echo safe_html('View image ' . ($idx + 1)); ?>">
                                <img src="<?php echo safe_html($t['path']); ?>" alt="" class="w-full h-full object-cover">
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Purchase Panel -->
        <div class="lg:col-span-6 space-y-6">
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="vl-eyebrow"><?php echo safe_html(strtoupper((string) ($category_title ?? 'Haute Floral'))); ?></span>
                    <?php if ($inventory_in_stock > 0): ?>
                        <span class="vl-badge vl-badge-sage text-[10px]">In Stock • Handcrafted Daily</span>
                    <?php else: ?>
                        <span class="vl-badge bg-[#FEF2F2] text-[#991B1B] text-[10px]">Temporarily Sold Out</span>
                    <?php endif; ?>
                </div>

                <h1 class="font-serif text-3xl sm:text-4xl text-[#242220] leading-tight font-medium">
                    <?php echo safe_html($product_title); ?>
                </h1>

                <!-- Price Presentation -->
                <div class="flex items-baseline gap-3 pt-1">
                    <span class="text-2xl sm:text-3xl font-serif font-bold text-[#C47C68]">
                        <?php echo $currencySymbol . number_format($effectivePrice, 0); ?>
                    </span>
                    <?php if ($inventory_mrp > $effectivePrice && $inventory_mrp > 0): ?>
                        <span class="text-base text-[#8E8780] line-through">
                            <?php echo $currencySymbol . number_format($inventory_mrp, 0); ?>
                        </span>
                        <span class="vl-badge vl-badge-gold">
                            Save <?php echo round((($inventory_mrp - $effectivePrice) / $inventory_mrp) * 100); ?>%
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Short Excerpt / Atelier Note -->
            <?php if (!empty($product_description)): ?>
                <div class="text-sm text-[#635E59] leading-relaxed border-t border-[#E8DDD5] pt-4 font-light">
                    <?php echo nl2br(safe_html((string) $product_description)); ?>
                </div>
            <?php endif; ?>

            <!-- Variations / Attributes -->
            <?php if (!empty($productAttributes)): ?>
                <div class="space-y-3 pt-2">
                    <label class="block text-xs uppercase tracking-wider font-semibold text-[#635E59]">Select Size / Arrangement Style</label>
                    <div class="flex flex-wrap gap-2.5">
                        <?php foreach ($productAttributes as $idx => $attr): ?>
                            <button type="button" class="vl-filter-tab text-xs py-2 px-4 <?php echo $idx === 0 ? 'is-active' : ''; ?>">
                                <?php echo safe_html($attr[0] ?? 'Standard'); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Add-ons Selection -->
            <?php if (!empty($addonProducts)): ?>
                <div class="space-y-3 pt-4 border-t border-[#E8DDD5]">
                    <span class="vl-eyebrow">Complete Your Floral Gift</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <?php foreach ($addonProducts as $addon): ?>
                            <div class="flex items-center gap-3 p-3 rounded-xl bg-white border border-[#E8DDD5] hover:border-[#C47C68] transition-colors">
                                <img src="<?php echo safe_html($addon['product_image']); ?>" alt="" class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold text-[#242220] truncate"><?php echo safe_html($addon['product_title']); ?></p>
                                    <p class="text-xs text-[#C47C68] font-bold">₹<?php echo number_format($addon['inventory_price'], 0); ?></p>
                                </div>
                                <button type="button"
                                    data-info="<?php echo safe_html($addon['product_id'] . '_' . $addon['inventory_price'] . '_1'); ?>"
                                    class="quickAddToCart text-xs px-2.5 py-1.5 rounded-full bg-[#FAF7F5] border border-[#E8DDD5] text-[#242220] hover:bg-[#C47C68] hover:text-white transition-colors"
                                    aria-label="<?php echo safe_html('Add ' . $addon['product_title'] . ' add-on'); ?>">
                                    + Add
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Action Area: Quantity & Add to Cart -->
            <div class="space-y-4 pt-4 border-t border-[#E8DDD5]">
                <div class="flex items-center gap-4">
                    <div class="vl-qty-control">
                        <button type="button" id="decrease-quantity" class="vl-qty-btn" aria-label="Decrease quantity">-</button>
                        <input type="number" id="quantity" value="1" min="1" max="99" class="vl-qty-input" aria-label="Quantity">
                        <button type="button" id="increase-quantity" class="vl-qty-btn" aria-label="Increase quantity">+</button>
                    </div>

                    <!-- Hidden Product Meta -->
                    <div id="productInfo"
                        value="<?php echo (int) $product_id . '_' . (float) $effectivePrice . '_1'; ?>"
                        data-product-id="<?php echo (int) $product_id; ?>"
                        data-unit-price="<?php echo (float) $effectivePrice; ?>"
                        data-currency="<?php echo safe_html($currencySymbol); ?>"
                        data-item-type="product"
                        class="hidden"></div>

                    <!-- Primary Add to Bag Button -->
                    <button type="button" id="addToCart"
                        class="vl-btn-primary flex-1 py-3.5 text-sm font-semibold justify-center">
                        <i class="ri-shopping-bag-3-line text-lg" aria-hidden="true"></i>
                        <span id="addToCartText">Add to Floral Bag</span>
                    </button>
                </div>

                <!-- WhatsApp Concierge Support -->
                <a href="<?php echo safe_html($whatsappUrl); ?>" target="_blank" rel="noopener noreferrer"
                    class="w-full vl-btn-outline justify-center py-3 text-xs gap-2">
                    <i class="ri-whatsapp-line text-base text-[#25D366]" aria-hidden="true"></i>
                    <span>Order via Atelier Concierge on WhatsApp</span>
                </a>
            </div>

            <!-- Delivery & Promise Accordion / Tabs -->
            <div class="pt-6 border-t border-[#E8DDD5] space-y-3">
                <details class="group rounded-xl bg-[#FAF7F5] border border-[#E8DDD5] p-4 cursor-pointer" open>
                    <summary class="font-serif font-medium text-base text-[#242220] flex items-center justify-between list-none">
                        <span>Delivery &amp; Packaging Notes</span>
                        <i class="ri-arrow-down-s-line text-lg group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <div class="pt-3 text-xs text-[#635E59] leading-relaxed space-y-1.5">
                        <p>✦ <strong>Same-Day Dispatch:</strong> Orders confirmed before 4:00 PM are delivered same-day across Delhi NCR.</p>
                        <p>✦ <strong>Hydration Pack:</strong> Stems travel in eco-friendly hydration pods to preserve freshness in transit.</p>
                        <p>✦ <strong>Complimentary Card:</strong> Every creation includes a handwritten envelope with wax seal.</p>
                    </div>
                </details>

                <details class="group rounded-xl bg-[#FAF7F5] border border-[#E8DDD5] p-4 cursor-pointer">
                    <summary class="font-serif font-medium text-base text-[#242220] flex items-center justify-between list-none">
                        <span>Botanical Care &amp; Longevity</span>
                        <i class="ri-arrow-down-s-line text-lg group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <div class="pt-3 text-xs text-[#635E59] leading-relaxed space-y-1.5">
                        <p>1. Trim stems at a 45-degree angle under cool water before placing in a vase.</p>
                        <p>2. Fill clean vase with room-temperature water and change every 48 hours.</p>
                        <p>3. Keep away from direct midday sunlight, draughts, and fresh fruit.</p>
                    </div>
                </details>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($tagList)): ?>
        <section class="mt-16 sm:mt-24 pt-12 border-t border-[#E8DDD5]">
            <div class="text-center max-w-xl mx-auto mb-10">
                <span class="vl-eyebrow">Curated Complements</span>
                <h2 class="font-serif text-3xl text-[#242220]">You May Also Adore</h2>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
                <?php
                $relQ = "SELECT DISTINCT 
                    p.product_id, 
                    p.product_title, 
                    p.product_image, 
                    i.inventory_price, 
                    i.inventory_mrp, 
                    i.inventory_currency_symbol, 
                    l.link_url
                FROM " . TABLE_PREFIX . "product p
                INNER JOIN " . TABLE_PREFIX . "inventory i ON p.product_id = i.inventory_relation_id AND i.inventory_type = 'product' AND i.inventory_site_id = " . SITE_ID . "
                INNER JOIN " . TABLE_PREFIX . "link l ON l.link_relation_id = p.product_id 
                    AND l.link_status = 200 
                    AND l.link_type = 'product'
                WHERE p.product_status = 1 
                  AND p.product_site_id = " . SITE_ID . " 
                  AND l.link_site_id = " . SITE_ID . " 
                  AND p.product_id IN ({$tagList})
                LIMIT 4";
                if ($getRel = mysqli_query($connection, $relQ)) {
                    while ($relProd = mysqli_fetch_assoc($getRel)) {
                        veloura_render_product_card($relProd);
                    }
                }
                ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<?php include_once 'footer.php'; ?>
