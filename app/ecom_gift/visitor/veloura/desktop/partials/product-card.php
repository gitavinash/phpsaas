<?php
/**
 * Veloura Shared Editorial Product Card Component.
 *
 * Inspired by Fiore & Fiorello design language:
 * - Semantic <article> root with 4:5 editorial aspect ratio
 * - Hover image zoom & smooth elevation
 * - Subtle floating status badges (New, Bestseller, Discount)
 * - Stretched card link for accessibility with independent Quick Add button
 * - Centered luxury serif typography with elegant price hierarchy
 */

if (!function_exists('safe_html')) {
    function safe_html(?string $str): string
    {
        return htmlspecialchars((string) $str, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('veloura_render_product_card')) {
    function veloura_render_product_card(array $item, array $options = []): string
    {
        ob_start();

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

        $categoryRaw = (string)($item['category_title'] ?? ($item['category_slug'] ?? ''));
        $categoryAttr = strtolower(trim($categoryRaw));

        $safeTitle = safe_html($title);
        $safeUrl = safe_html($url);
        $safeImage = safe_html($image);
        $safeCurrency = safe_html($currency);
        $formattedPrice = $safeCurrency . number_format($displayPrice, 0);
        $formattedMrp = $safeCurrency . number_format($rawMrp, 0);
        $dataInfo = safe_html($productId . '_' . ($rawPrice > 0 ? $rawPrice : $displayPrice) . '_1');
        ?>
        <article class="vl-product-card product-card group" data-category="<?php echo safe_html($categoryAttr); ?>">
            <div class="vl-product-media <?php echo safe_html($aspectClass); ?>">
                <img src="<?php echo $safeImage; ?>"
                    alt="<?php echo $safeTitle; ?>"
                    class="vl-product-img"
                    loading="<?php echo $priorityHigh ? 'eager' : 'lazy'; ?>"
                    decoding="async">

                <?php if ($inStock <= 0 || $badgeLabel !== '' || $discountPercent > 0): ?>
                    <div class="vl-card-badges">
                        <?php if ($inStock <= 0): ?>
                            <span class="vl-badge vl-badge-soldout bg-[#FEF2F2] text-[#991B1B] border border-[#FCA5A5]">Sold Out</span>
                        <?php elseif ($badgeLabel !== ''): ?>
                            <span class="vl-badge vl-badge-terracotta"><?php echo safe_html($badgeLabel); ?></span>
                        <?php endif; ?>

                        <?php if ($discountPercent > 0): ?>
                            <span class="vl-badge vl-badge-gold">Save <?php echo $discountPercent; ?>%</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($showQuickAdd && $productId > 0 && $inStock > 0): ?>
                    <button type="button"
                        data-info="<?php echo $dataInfo; ?>"
                        class="quickAddToCart vl-card-add-btn"
                        aria-label="<?php echo safe_html('Add ' . $title . ' to bag'); ?>">
                        <i class="ri-shopping-bag-3-line" aria-hidden="true"></i>
                        <span>Add to Bag</span>
                    </button>
                <?php endif; ?>
            </div>

            <div class="vl-product-body">
                <h3 class="vl-product-title">
                    <a href="<?php echo $safeUrl; ?>" class="vl-card-link">
                        <?php echo $safeTitle; ?>
                    </a>
                </h3>

                <?php if ($subtitle !== ''): ?>
                    <p class="text-xs text-[#8E8780] mb-2 truncate"><?php echo safe_html($subtitle); ?></p>
                <?php endif; ?>

                <div class="vl-product-pricing">
                    <span class="vl-price-current"><?php echo $formattedPrice; ?></span>
                    <?php if ($hasDiscount): ?>
                        <span class="vl-price-mrp"><?php echo $formattedMrp; ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </article>
        <?php
        $output = ob_get_clean();
        if (!empty($options['return_only'])) {
            return $output;
        }
        echo $output;
        return '';
    }
}
