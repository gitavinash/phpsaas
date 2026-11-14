<?php 
include_once 'header.php'; 

$bannerImage = !empty($category_image) ? $category_image : USER_IMAGES . 'about-us-banner.jpg';
$catTitleSafe = safe_html(ucwords((string) ($category_title ?? 'Creations')));
$catDescSafe = safe_html((string) ($category_description ?? 'Handcrafted fresh stems, botanical planters, and curated gift hampers.'));

$sortBy = 'link_published DESC';
if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'price_low':
            $sortBy = 'inventory_price ASC';
            break;
        case 'price_high':
            $sortBy = 'inventory_price DESC';
            break;
        case 'newest':
            $sortBy = 'link_published DESC';
            break;
        case 'oldest':
            $sortBy = 'link_published ASC';
            break;
    }
}
?>

<!-- Category Editorial Banner -->
<section class="py-10 sm:py-16 md:py-20 bg-[#FAF7F5] border-b border-[#E8DDD5] relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <nav class="flex items-center justify-center text-xs sm:text-sm text-[#635E59] mb-4 gap-2" aria-label="Breadcrumb">
            <a href="/" class="hover:text-[#C47C68] transition-colors">Home</a>
            <span aria-hidden="true">/</span>
            <span class="text-[#8E8780]">Collection</span>
            <span aria-hidden="true">/</span>
            <span class="text-[#242220] font-medium" aria-current="page"><?php echo $catTitleSafe; ?></span>
        </nav>
        
        <span class="vl-eyebrow">Atelier Category</span>
        <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl text-[#242220] mb-3"><?php echo $catTitleSafe; ?></h1>
        <p class="text-sm sm:text-base text-[#635E59] max-w-xl mx-auto font-light leading-relaxed">
            <?php echo $catDescSafe; ?>
        </p>

        <!-- Category Nav Pills -->
        <div class="flex flex-wrap items-center justify-center gap-2 pt-6">
            <a href="/shop/" class="vl-filter-tab">All</a>
            <a href="/category/flower/" class="vl-filter-tab <?php echo (strpos(ACTIVE_URL, 'flower') !== false ? 'is-active' : ''); ?>">Fresh Flowers</a>
            <a href="/category/cake/" class="vl-filter-tab <?php echo (strpos(ACTIVE_URL, 'cake') !== false ? 'is-active' : ''); ?>">Gift Hampers</a>
            <a href="/category/planters/" class="vl-filter-tab <?php echo (strpos(ACTIVE_URL, 'planter') !== false ? 'is-active' : ''); ?>">Ceramic Planters</a>
            <a href="/events/" class="vl-filter-tab">Masterclasses</a>
        </div>
    </div>
</section>

<!-- Product Grid Section -->
<section class="py-10 sm:py-16 px-4 sm:px-6 lg:px-8 bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pb-6 mb-8 border-b border-[#E8DDD5]">
            <p class="text-xs sm:text-sm text-[#635E59]">
                <?php echo isset($categorySinglePost) ? count($categorySinglePost) : 0; ?> creations in this collection
            </p>

            <form method="GET" class="flex items-center gap-2">
                <label for="cat-sort" class="text-xs uppercase tracking-wider font-semibold text-[#635E59]">Sort By:</label>
                <select name="sort" id="cat-sort" class="vl-input py-1.5 px-3 text-xs w-auto cursor-pointer" data-auto-submit>
                    <option value="" <?php echo empty($_GET['sort']) ? 'selected' : ''; ?>>Featured</option>
                    <option value="price_low" <?php echo (@$_GET['sort'] === 'price_low') ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_high" <?php echo (@$_GET['sort'] === 'price_high') ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="newest" <?php echo (@$_GET['sort'] === 'newest') ? 'selected' : ''; ?>>Newest</option>
                    <option value="oldest" <?php echo (@$_GET['sort'] === 'oldest') ? 'selected' : ''; ?>>Oldest</option>
                </select>
            </form>
        </div>

        <?php if (!empty($categorySinglePost) && is_array($categorySinglePost)): ?>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
                <?php 
                $idx = 0;
                foreach ($categorySinglePost as $product): 
                    veloura_render_product_card($product, ['priority_high' => ($idx < 4)]);
                    $idx++;
                endforeach; 
                ?>
            </div>
        <?php else: ?>
            <div class="text-center py-16 text-[#635E59]">
                <p class="font-serif text-2xl mb-2 text-[#242220]">No Creations Found in This Category</p>
                <p class="text-sm mb-6">Explore our other collections or check back for the new morning harvest.</p>
                <a href="/shop/" class="vl-btn-primary">Browse All Creations</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include_once 'footer.php'; ?>
