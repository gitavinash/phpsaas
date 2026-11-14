<?php

include_once 'header.php';
require_once __DIR__ . '/partials/product-card.php';

$filterHeading = isset($filterType) ? ucwords(str_replace('-', ' ', htmlspecialchars($filterType, ENT_QUOTES, 'UTF-8'))) : 'Filtered Creations';
$subHeading = (isset($min) && isset($max)) ? "Creations between ₹" . number_format((float) $min) . " – ₹" . number_format((float) $max) : "Curated selections based on your filter criteria";
?>

<!-- Filter Editorial Banner -->
<section class="py-10 sm:py-16 md:py-20 bg-[#FAF7F5] border-b border-[#E8DDD5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <nav class="flex items-center justify-center text-xs sm:text-sm text-[#6E675F] mb-4 gap-2" aria-label="Breadcrumb">
            <a href="/" class="hover:text-[#C47C68] transition-colors">Home</a>
            <span aria-hidden="true">/</span>
            <span class="text-[#242220] font-medium" aria-current="page"><?php echo $filterHeading; ?></span>
        </nav>
        <span class="vl-eyebrow">Price &amp; Occasion Filter</span>
        <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl text-[#242220] mb-3"><?php echo $filterHeading; ?></h1>
        <p class="text-sm sm:text-base text-[#6E675F] max-w-xl mx-auto font-light">
            <?php echo $subHeading; ?>
        </p>
    </div>
</section>

<!-- Filter Results Section -->
<section class="py-10 sm:py-16 px-4 sm:px-6 lg:px-8 bg-white">
    <div class="max-w-7xl mx-auto">
        <!-- Toolbar -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pb-6 mb-8 border-b border-[#E8DDD5]">
            <p class="text-xs sm:text-sm text-[#6E675F]">
                Showing filtered boutique creations
            </p>

            <form method="GET" class="flex items-center gap-2">
                <label for="filter-sort" class="text-xs uppercase tracking-wider font-semibold text-[#6E675F]">Sort By:</label>
                <select name="sort" id="filter-sort" class="vl-input py-1.5 px-3 text-xs w-auto cursor-pointer" data-auto-submit>
                    <option value="" <?php echo empty($_GET['sort']) ? 'selected' : ''; ?>>Latest Arrivals</option>
                    <option value="price_low" <?php echo (@$_GET['sort'] === 'price_low') ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_high" <?php echo (@$_GET['sort'] === 'price_high') ? 'selected' : ''; ?>>Price: High to Low</option>
                </select>
            </form>
        </div>

        <?php if (isset($productList) && !empty($productList)): ?>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
                <?php
                foreach ($productList as $productItem) {
                    echo veloura_render_product_card($productItem);
                }
                ?>
            </div>

            <!-- Pagination -->
            <?php if (defined('TOTAL_PAGE') && TOTAL_PAGE > 1): ?>
                <nav class="flex items-center justify-center gap-2 pt-12" aria-label="Pagination">
                    <?php
                    $sortParam = !empty($_GET['sort']) ? '&sort=' . urlencode($_GET['sort']) : '';
                    if (defined('PAGE_NO') && PAGE_NO > 1) {
                        echo '<a href="' . htmlspecialchars(BASE_URL . '?page=' . PREVIOUS_PAGE . $sortParam, ENT_QUOTES, 'UTF-8') . '" class="vl-btn-outline py-2 px-4 text-xs">&larr; Previous</a>';
                    }
                    if (defined('NEXT_PAGE') && defined('TOTAL_PAGE') && NEXT_PAGE <= TOTAL_PAGE) {
                        echo '<a href="' . htmlspecialchars(BASE_URL . '?page=' . NEXT_PAGE . $sortParam, ENT_QUOTES, 'UTF-8') . '" class="vl-btn-outline py-2 px-4 text-xs">Next &rarr;</a>';
                    }
                    ?>
                </nav>
            <?php endif; ?>

        <?php else: ?>
            <div class="py-20 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-[#FAF7F5] border border-[#E8DDD5] flex items-center justify-center text-[#C47C68]">
                    <i class="ri-filter-off-line text-2xl" aria-hidden="true"></i>
                </div>
                <h2 class="font-serif text-2xl text-[#242220] mb-2">No Products Match This Filter</h2>
                <p class="text-sm text-[#6E675F] max-w-md mx-auto mb-6">We could not find creations matching these specific price constraints. Please adjust your filters or browse all creations.</p>
                <a href="/shop/" class="vl-btn-primary">Browse All Creations</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
include_once 'footer.php';
?>
