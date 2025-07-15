<?php include_once 'header.php'; ?>

<!-- Banner with Breadcrumb -->
<div class="relative h-[280px] w-full mb-8 mt-16">
    <?php if (!empty($category_image)) { ?>
    <div style="background-image: url('<?= $category_image ?>');" class="absolute inset-0 bg-cover bg-center"></div>
    <?php } else { ?>
    <div class="absolute inset-0 bg-gradient-to-r from-[#D4946D] via-[#e0b89c] to-[#d4946d]"></div>
    <?php } ?>

    <div class="absolute inset-0 bg-black bg-opacity-40"></div>

    <div class="container mx-auto px-4 h-full flex flex-col justify-center items-center text-center relative z-10">
        <div class="flex items-center text-sm text-white/80 mb-6">
            <a href="/" class="hover:text-white transition-colors">Home</a>
            <span class="mx-2">/</span>
            <a href="<?= ACTIVE_URL ?>" class="hover:text-white transition-colors">Categories</a>
            <span class="mx-2">/</span>
            <span class="text-white"><?= $category_title ?></span>
        </div>
        <h1 class="font-serif text-4xl md:text-5xl text-white mb-4"><?= $category_title ?></h1>
        <p class="text-white text-lg max-w-2xl"><?= $category_description ?></p>
    </div>
</div>


<!-- Product Grid Section -->
<div class="container mx-auto px-4 py-8">
    <?php if (isset($categorySinglePost) && count($categorySinglePost) > 0) { ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
        <?php foreach ($categorySinglePost as $product) {
                extract($product);
                $price = ($inventory_price != 0 ? $inventory_price : $inventory_mrp);
                $discount = ($inventory_price != 0 && $inventory_price < $inventory_mrp)
                    ? round((($inventory_mrp - $inventory_price) / $inventory_mrp) * 100)
                    : 0;
            ?>
        <div class="product-card group">
            <a href="<?php echo $link_url ?>" title="<?php echo $product_title ?>">
                <div class="relative overflow-hidden rounded-lg mb-4">
                    <img src="<?php echo $product_image ?>" alt="<?php echo $product_title ?>"
                        class="w-full h-[350px] object-cover object-top transition-transform duration-500 group-hover:scale-105">
                    <?php if ($inventory_in_stock == 0): ?>
                    <div class="absolute top-3 left-3 bg-red-600 text-white text-xs px-3 py-1 rounded">Out of Stock
                    </div>
                    <?php endif; ?>
                    <?php if ($discount > 0): ?>
                    <div class="absolute top-3 right-3 bg-green-600 text-white text-xs px-3 py-1 rounded">
                        <?php echo $discount ?>%
                        OFF</div>
                    <?php endif; ?>
                </div>
            </a>
            <h3 class="font-serif text-lg mb-2"><?php echo $product_title ?></h3>
            <div class="flex justify-between items-center">
                <div class="flex flex-col">
                    <span class="text-primary font-semibold text-base">
                        <?php echo $inventory_currency_symbol . ' ' . $price ?>
                    </span>
                    <?php if ($discount > 0): ?>
                    <span class="text-sm text-gray-400 line-through">
                        <?php echo $inventory_currency_symbol . ' ' . $inventory_mrp ?>
                    </span>
                    <?php endif; ?>
                </div>
                <?php
                        if ($inventory_in_stock > 0) { ?>
                <button data-info='<?php echo $product_id . '_' . $inventory_price . '_1'; ?>'
                    class="quickAddToCart bg-primary text-white px-4 py-2 rounded-button whitespace-nowrap hover:bg-opacity-90 transition-colors">Add
                    to Cart</button>
                <?php
                        }
                        ?>
            </div>
        </div>
        <?php } ?>
    </div>

    <!-- Pagination -->
    <div class="container mx-auto px-4 py-8 flex justify-center">
        <div class="flex items-center space-x-2">
            <?php
                $sort = (isset($_GET['sort'])) ? '&sort=' . $_GET['sort'] : '';
                if (PAGE_NO != 0) {
                    echo '<a href="' . BASE_URL . '?pg=' . PREVIOUS_PAGE . $sort . '" class="w-10 h-10 flex items-center justify-center rounded border border-gray-200 text-gray-500 hover:border-primary hover:text-primary transition-colors">
                            <i class="ri-arrow-left-s-line"></i>
                          </a>';
                }
                for ($i = 1; $i <= TOTAL_PAGE; $i++) {
                    $active = ($i == PAGE_NO + 1) ? 'bg-primary text-white' : 'border border-gray-200 hover:border-primary hover:text-primary';
                    echo '<a href="' . BASE_URL . '?pg=' . ($i - 1) . $sort . '" class="w-10 h-10 flex items-center justify-center rounded ' . $active . '">' . $i . '</a>';
                }
                if (NEXT_PAGE <= TOTAL_PAGE) {
                    echo '<a href="' . BASE_URL . '?pg=' . NEXT_PAGE . $sort . '" class="w-10 h-10 flex items-center justify-center rounded border border-gray-200 text-gray-500 hover:border-primary hover:text-primary transition-colors">
                            <i class="ri-arrow-right-s-line"></i>
                          </a>';
                }
                ?>
        </div>
    </div>
    <?php } else { ?>
    <!-- No Product Found Message -->
    <div class="text-center py-20">
        <h1 class="text-xl font-semibold text-gray-800 mb-4">No product found</h1>
        <a href="/" class="inline-block px-6 py-3 bg-primary text-white rounded hover:bg-opacity-90 transition">Go to
            homepage</a>
    </div>
    <?php } ?>
</div>

<?php include_once 'footer.php'; ?>