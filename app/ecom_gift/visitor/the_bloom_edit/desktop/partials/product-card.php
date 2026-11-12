<?php
/**
 * Shared editorial product card component.
 *
 * Expects `$cardProduct` (associative array) or `$product` / local extracted fields.
 * Uses stretched link (`a.tbe-card-link::after`) so interactive Quick Add buttons remain
 * valid sibling controls rather than interactive children nested inside an anchor.
 */

if (!function_exists('tbe_render_product_card')) {
    function tbe_render_product_card(array $item, array $options = []): void
    {
        $title = trim((string)($item['product_title'] ?? ''));
        $url = trim((string)($item['link_url'] ?? '#'));
        $image = trim((string)($item['product_image'] ?? ''));
        if ($image === '') {
            $image = USER_IMAGES . 'about-us-banner.jpg';
        }

        $productId = (int)($item['product_id'] ?? 0);
        $currency = trim((string)($item['inventory_currency_symbol'] ?? '₹'));
        if ($currency === '') {
            $currency = '₹';
        }

        $rawPrice = (float)($item['inventory_price'] ?? 0);
        $rawMrp = (float)($item['inventory_mrp'] ?? 0);
        $displayPrice = $rawPrice > 0 ? $rawPrice : $rawMrp;
        $hasDiscount = ($rawPrice > 0 && $rawMrp > $rawPrice);
        $discountPercent = $hasDiscount ? (int)round((($rawMrp - $rawPrice) / $rawMrp) * 100) : 0;

        $inStock = array_key_exists('inventory_in_stock', $item) ? (int)$item['inventory_in_stock'] : 1;
        $publishedAt = (string)($item['published_at'] ?? ($item['link_published'] ?? ($item['publishedTime'] ?? '')));
        $isNew = $publishedAt !== '' && strtotime($publishedAt) !== false && strtotime($publishedAt) > strtotime('-1 month');

        $badgeLabel = isset($options['badge_label']) ? (string)$options['badge_label'] : ($isNew ? 'New' : '');
        $subtitle = isset($options['subtitle']) ? trim((string)$options['subtitle']) : '';
        $showQuickAdd = !isset($options['show_quick_add']) || (bool)$options['show_quick_add'];
        $aspectClass = !empty($options['aspect_class']) ? (string)$options['aspect_class'] : 'aspect-[4/5]';
        $priorityHigh = !empty($options['priority_high']);

        $safeTitle = safe_html($title);
        $safeUrl = safe_html($url);
        $safeImage = safe_html($image);
        $safeCurrency = safe_html($currency);
        $formattedPrice = $safeCurrency . number_format($displayPrice, 0);
        $formattedMrp = $safeCurrency . number_format($rawMrp, 0);
        $dataInfo = safe_html($productId . '_' . ($rawPrice > 0 ? $rawPrice : $displayPrice) . '_1');
        ?>
        <article class="tbe-product-card product-card group">
            <div class="tbe-product-media <?php echo safe_html($aspectClass); ?>">
                <img src="<?php echo $safeImage; ?>"
                    alt="<?php echo $safeTitle; ?>"
                    class="tbe-product-img"
                    loading="<?php echo $priorityHigh ? 'eager' : 'lazy'; ?>"
                    decoding="async">

                <?php if ($inStock <= 0 || $badgeLabel !== '' || $discountPercent > 0): ?>
                    <div class="tbe-card-badges">
                        <?php if ($inStock <= 0): ?>
                            <span class="tbe-badge tbe-badge-soldout">Sold Out</span>
                        <?php elseif ($badgeLabel !== ''): ?>
                            <span class="tbe-badge tbe-badge-editorial"><?php echo safe_html($badgeLabel); ?></span>
                        <?php endif; ?>

                        <?php if ($discountPercent > 0): ?>
                            <span class="tbe-badge tbe-badge-savings">Save <?php echo $discountPercent; ?>%</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="tbe-product-body">
                <h3 class="tbe-product-title">
                    <a href="<?php echo $safeUrl; ?>" class="tbe-card-link">
                        <?php echo $safeTitle; ?>
                    </a>
                </h3>

                <?php if ($subtitle !== ''): ?>
                    <p class="tbe-product-subtitle"><?php echo safe_html($subtitle); ?></p>
                <?php endif; ?>

                <div class="tbe-product-footer">
                    <div class="tbe-product-pricing">
                        <span class="tbe-price-current"><?php echo $formattedPrice; ?></span>
                        <?php if ($hasDiscount): ?>
                            <span class="tbe-price-mrp"><?php echo $formattedMrp; ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if ($showQuickAdd && $productId > 0 && $inStock > 0): ?>
                        <button type="button"
                            data-info="<?php echo $dataInfo; ?>"
                            class="quickAddToCart tbe-card-add-btn"
                            aria-label="<?php echo safe_html('Add ' . $title . ' to bag'); ?>">
                            <i class="ri-shopping-bag-3-line" aria-hidden="true"></i>
                            <span>Add to Bag</span>
                        </button>
                    <?php elseif ($inStock <= 0): ?>
                        <span class="tbe-card-status-note">Unavailable</span>
                    <?php endif; ?>
                </div>
            </div>
        </article>
        <?php
    }
}
