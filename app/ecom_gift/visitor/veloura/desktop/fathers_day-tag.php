<?php

include_once 'header.php';
require_once __DIR__ . '/partials/product-card.php';

if (is_array($tagPost)) {
    $tagIds = array_map(fn($t) => (int) $t['tag_relation_id'], $tagPost);
    $resultTags = implode(',', $tagIds);

    $sortBy = 'l.link_published DESC';
    if (isset($_GET['sort'])) {
        switch ($_GET['sort']) {
            case 'price_low':
                $sortBy = 'i.inventory_price ASC';
                break;
            case 'price_high':
                $sortBy = 'i.inventory_price DESC';
                break;
            case 'oldest':
                $sortBy = 'l.link_published ASC';
                break;
            case 'newest':
                $sortBy = 'l.link_published DESC';
                break;
        }
    }

    $tagHeading = ucwords(str_replace('-', ' ', htmlspecialchars($tag_title ?? "Father's Day Collection", ENT_QUOTES, 'UTF-8')));
?>

<!-- Editorial Banner -->
<section class="py-10 sm:py-16 md:py-20 bg-[#FAF7F5] border-b border-[#E8DDD5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <nav class="flex items-center justify-center text-xs sm:text-sm text-[#6E675F] mb-4 gap-2" aria-label="Breadcrumb">
            <a href="/" class="hover:text-[#C47C68] transition-colors">Home</a>
            <span aria-hidden="true">/</span>
            <span class="text-[#242220] font-medium" aria-current="page"><?php echo $tagHeading; ?></span>
        </nav>
        <span class="vl-eyebrow">Celebration Edition</span>
        <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl text-[#242220] mb-3"><?php echo $tagHeading; ?></h1>
        <?php if (!empty($tag_description)): ?>
            <p class="text-sm sm:text-base text-[#6E675F] max-w-xl mx-auto font-light">
                <?php echo htmlspecialchars((string) $tag_description, ENT_QUOTES, 'UTF-8'); ?>
            </p>
        <?php endif; ?>
    </div>
</section>

<!-- Product Grid & Toolbar -->
<section class="py-10 sm:py-16 px-4 sm:px-6 lg:px-8 bg-white">
    <div class="max-w-7xl mx-auto">
        <!-- Toolbar -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pb-6 mb-8 border-b border-[#E8DDD5]">
            <p class="text-xs sm:text-sm text-[#6E675F]">
                Showing curated creations for <?php echo $tagHeading; ?>
            </p>

            <form method="GET" class="flex items-center gap-2">
                <label for="fday-sort" class="text-xs uppercase tracking-wider font-semibold text-[#6E675F]">Sort By:</label>
                <select name="sort" id="fday-sort" class="vl-input py-1.5 px-3 text-xs w-auto cursor-pointer" data-auto-submit>
                    <option value="" <?php echo empty($_GET['sort']) ? 'selected' : ''; ?>>Latest Arrivals</option>
                    <option value="price_low" <?php echo (@$_GET['sort'] === 'price_low') ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_high" <?php echo (@$_GET['sort'] === 'price_high') ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="oldest" <?php echo (@$_GET['sort'] === 'oldest') ? 'selected' : ''; ?>>Oldest</option>
                </select>
            </form>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
            <?php
            $query = "
                SELECT 
                    l.link_id, 
                    l.link_url, 
                    l.link_published,
                    p.product_id, 
                    p.product_title, 
                    p.product_image, 
                    i.inventory_currency, 
                    i.inventory_in_stock, 
                    i.inventory_currency_symbol, 
                    i.inventory_price, 
                    i.inventory_mrp 
                FROM " . TABLE_PREFIX . "product p
                INNER JOIN " . TABLE_PREFIX . "inventory i 
                    ON i.inventory_relation_id = p.product_id 
                    AND i.inventory_type = 'product' 
                    AND i.inventory_site_id = " . SITE_ID . "
                INNER JOIN " . TABLE_PREFIX . "link l 
                    ON l.link_relation_id = p.product_id 
                    AND l.link_type = 'product' 
                    AND l.link_status = 200 
                    AND l.link_site_id = " . SITE_ID . "
                WHERE p.product_status = 1 
                  AND p.product_site_id = " . SITE_ID . "
                  AND i.inventory_in_stock = 1 
                  AND p.product_id IN ({$resultTags})
                ORDER BY {$sortBy} 
                LIMIT 0, 48";

            if ($result = mysqli_query($connection, $query)) {
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo veloura_render_product_card($row);
                    }
                } else {
                    echo "<div class='col-span-full py-16 text-center text-[#6E675F]'>No creations currently active for this collection.</div>";
                }
            }
            ?>
        </div>
    </div>
</section>

<?php
} else {
    echo '<section class="py-20 text-center bg-[#FAF7F5] min-h-[50vh] flex items-center justify-center">
            <div>
                <h1 class="font-serif text-2xl text-[#242220] mb-3">Collection Not Found</h1>
                <a href="/shop/" class="vl-btn-primary">Browse All Creations</a>
            </div>
          </section>';
}

include_once 'footer.php';
?>
