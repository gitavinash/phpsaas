<?php

include_once 'header.php';

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
    }
}
?>

<!-- Shop Editorial Banner -->
<section class="py-10 sm:py-16 md:py-20 bg-[#FAF7F5] border-b border-[#E8DDD5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <nav class="flex items-center justify-center text-xs sm:text-sm text-[#635E59] mb-4 gap-2" aria-label="Breadcrumb">
            <a href="/" class="hover:text-[#C47C68] transition-colors">Home</a>
            <span aria-hidden="true">/</span>
            <span class="text-[#242220] font-medium" aria-current="page">Atelier Shop</span>
        </nav>
        <span class="vl-eyebrow">The Complete Collection</span>
        <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl text-[#242220] mb-3">All Botanical Creations</h1>
        <p class="text-sm sm:text-base text-[#635E59] max-w-xl mx-auto font-light">
            Explore daily farm-cut garden stems, curated celebration hampers, and luxury indoor planters.
        </p>

        <!-- Category Nav Pills -->
        <div class="flex flex-wrap items-center justify-center gap-2 pt-6">
            <a href="/shop/" class="vl-filter-tab is-active">All</a>
            <a href="/category/flower/" class="vl-filter-tab">Fresh Flowers</a>
            <a href="/category/cake/" class="vl-filter-tab">Gift Hampers</a>
            <a href="/category/planters/" class="vl-filter-tab">Ceramic Planters</a>
            <a href="/events/" class="vl-filter-tab">Masterclasses</a>
        </div>
    </div>
</section>

<!-- Product Grid & Toolbar -->
<section class="py-10 sm:py-16 px-4 sm:px-6 lg:px-8 bg-white">
    <div class="max-w-7xl mx-auto">
        <!-- Toolbar -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pb-6 mb-8 border-b border-[#E8DDD5]">
            <p class="text-xs sm:text-sm text-[#635E59]">
                Showing bespoke creations available for same-day delivery
            </p>

            <form method="GET" class="flex items-center gap-2">
                <label for="shop-sort" class="text-xs uppercase tracking-wider font-semibold text-[#635E59]">Sort By:</label>
                <select name="sort" id="shop-sort" class="vl-input py-1.5 px-3 text-xs w-auto cursor-pointer" data-auto-submit>
                    <option value="" <?php echo empty($_GET['sort']) ? 'selected' : ''; ?>>Latest Arrivals</option>
                    <option value="price_low" <?php echo (@$_GET['sort'] === 'price_low') ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_high" <?php echo (@$_GET['sort'] === 'price_high') ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="oldest" <?php echo (@$_GET['sort'] === 'oldest') ? 'selected' : ''; ?>>Oldest</option>
                </select>
            </form>
        </div>

        <!-- Grid -->
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
                ORDER BY {$sortBy} 
                LIMIT 0, 40";

            if ($checkProducts = mysqli_query($connection, $query)) {
                if (mysqli_num_rows($checkProducts) == 0) {
                    echo "<div class='col-span-full py-16 text-center text-[#635E59]'>
                            <p class='font-serif text-2xl mb-2 text-[#242220]'>No Creations Currently Listed</p>
                            <p class='text-sm mb-6'>Please check back soon or browse our other floral categories.</p>
                            <a href='/' class='vl-btn-primary'>Return Home</a>
                          </div>";
                } else {
                    $idx = 0;
                    while ($productList = mysqli_fetch_assoc($checkProducts)) {
                        veloura_render_product_card($productList, ['priority_high' => ($idx < 4)]);
                        $idx++;
                    }
                }
            }
            ?>
        </div>
    </div>
</section>

<?php include_once 'footer.php'; ?>
