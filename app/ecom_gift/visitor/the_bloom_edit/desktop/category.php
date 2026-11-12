<?php include_once 'header.php'; ?>

<?php
$bannerImage = $category_image ?? '';
$bannerFromProduct = false;
if (empty($bannerImage) && !empty($categorySinglePost) && is_array($categorySinglePost)) {
    $firstProduct = reset($categorySinglePost);
    if (is_array($firstProduct) && !empty($firstProduct['product_image'])) {
        $bannerImage = $firstProduct['product_image'];
        $bannerFromProduct = true;
    }
}
$productCount = (isset($categorySinglePost) && is_array($categorySinglePost)) ? count($categorySinglePost) : 0;
?>

<!-- Editorial Collection Banner -->
<section class="tbe-banner py-8 sm:py-14 md:py-20">
    <?php if (!empty($bannerImage)): ?>
        <img src="<?php echo safe_html((string) $bannerImage); ?>"
            alt=""
            aria-hidden="true"
            fetchpriority="high"
            loading="eager"
            decoding="async"
            class="tbe-banner-media<?php echo $bannerFromProduct ? ' is-soft-blur' : ''; ?>">
    <?php endif; ?>
    <div class="tbe-banner-scrim" aria-hidden="true"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center relative z-10">
        <nav class="flex items-center justify-center text-xs sm:text-sm text-[#EFE8DC] mb-4 sm:mb-5 gap-2" aria-label="Breadcrumb">
            <a href="/" class="hover:text-white transition-colors">Home</a>
            <span aria-hidden="true">/</span>
            <a href="<?php echo safe_html((string) ACTIVE_URL); ?>" class="hover:text-white transition-colors">Collections</a>
            <span aria-hidden="true">/</span>
            <span class="text-white font-medium" aria-current="page"><?php echo safe_html(ucwords((string) $category_title)); ?></span>
        </nav>
        <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl font-medium text-white mb-3 sm:mb-4">
            <?php echo safe_html(ucwords((string) $category_title)); ?>
        </h1>
        <?php if (!empty($category_description)): ?>
            <p class="text-[#F4EFE6] text-sm sm:text-base md:text-lg max-w-2xl mx-auto leading-relaxed">
                <?php echo safe_html((string) $category_description); ?>
            </p>
        <?php endif; ?>
    </div>
</section>

<!-- Product Grid Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 md:py-14">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 pb-5 sm:pb-6 mb-6 sm:mb-8 border-b border-[#E6DDD3]">
        <p class="text-xs uppercase tracking-widest text-[#5E5650] font-medium">
            Showing <strong class="text-[#1C1A18]"><?php echo $productCount; ?></strong> Curated Creation<?php echo $productCount === 1 ? '' : 's'; ?>
        </p>

        <form method="GET" class="flex items-center gap-3 w-full sm:w-auto">
            <label for="sort-category" class="text-xs uppercase tracking-widest text-[#5E5650] font-semibold whitespace-nowrap">Sort by</label>
            <select id="sort-category" name="sort" aria-label="Sort products"
                class="tbe-input py-2 px-3.5 text-base sm:text-sm flex-1 sm:flex-none sm:w-auto sm:min-w-[190px]">
                <option value="#">Featured</option>
                <option value="price_low" <?php echo (($_GET['sort'] ?? '') === 'price_low') ? 'selected' : ''; ?>>Price: Low to High</option>
                <option value="price_high" <?php echo (($_GET['sort'] ?? '') === 'price_high') ? 'selected' : ''; ?>>Price: High to Low</option>
                <option value="newest" <?php echo (($_GET['sort'] ?? '') === 'newest') ? 'selected' : ''; ?>>Newest Arrivals</option>
                <option value="oldest" <?php echo (($_GET['sort'] ?? '') === 'oldest') ? 'selected' : ''; ?>>Classic Edit</option>
            </select>
        </form>
    </div>

    <?php if ($productCount > 0): ?>
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-6 lg:gap-7">
            <?php foreach ($categorySinglePost as $idx => $product):
                tbe_render_product_card($product, ['priority_high' => ($idx < 3)]);
            endforeach; ?>
        </div>

        <?php
        if (defined('PAGE_NO') && defined('TOTAL_PAGE')) {
            require_once 'pagination.php';
        }
        ?>
    <?php else: ?>
        <div class="bg-white border border-[#E6DDD3] rounded-xl text-center py-20 px-6 max-w-2xl mx-auto">
            <span class="tbe-eyebrow mb-2">Atelier Collection</span>
            <h2 class="font-serif text-2xl font-medium text-[#1C1A18] mb-3">No Creations Currently Listed</h2>
            <p class="text-sm text-[#5E5650] mb-6">Explore our signature bouquets, luxury ceramic planters, or seasonal hampers.</p>
            <a href="/shop/" class="tbe-btn-forest">Explore All Creations</a>
        </div>
    <?php endif; ?>
</div>

<?php include_once 'footer.php'; ?>
