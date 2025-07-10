<?php

include_once 'header.php';

?>

<!-- Banner Section -->
<section class="relative bg-cover bg-center h-64 flex items-center justify-center text-white mb-12"
    style="background-image: url('/upload/banner-bg.jpg');">
    <div class="absolute inset-0 bg-black bg-opacity-50"></div>
    <div class="relative z-10 text-center">
        <h1 class="text-4xl font-serif font-bold mb-2">
            <?php echo $category_title ?>
        </h1>
        <h3 class="text-lg opacity-80">
            <?php echo $category_description ?>
        </h3>
    </div>
</section>

<!-- Breadcrumb -->
<div class="container mx-auto px-4 mb-8">
    <nav class="text-sm text-gray-600" aria-label="breadcrumb">
        <ol class="flex space-x-2">
            <li><a href="/" class="hover:underline text-primary">Home</a></li>
            <li>/</li>
            <li><a href="<?php echo ACTIVE_URL ?>" class="hover:underline text-primary">Category</a></li>
            <li>/</li>
            <li class="text-gray-800 font-semibold"><?php echo $category_title ?></li>
        </ol>
    </nav>
</div>

<!-- Product List -->
<section class="container mx-auto px-4">
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php
        if ($productList = mysqli_query($connection, "SELECT link_url, product_title, product_image, product_highlight, inventory_price, inventory_currency_symbol, inventory_in_stock, inventory_mrp, link_published FROM " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "product ON link_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id INNER JOIN isw_category ON category_id=product_category WHERE link_site_id=" . SITE_ID . " AND link_type='product' AND link_status=200 AND product_type='subscription' AND inventory_type='product' AND product_category={$category_id} ORDER BY inventory_price+0 ASC LIMIT 0, 100")) {

            if (mysqli_num_rows($productList) != 0) {
                while ($productData = mysqli_fetch_assoc($productList)) {
                    extract($productData);

                    $isNew = ($link_published > date('Y-m-d', strtotime('-7 days')));
                    $price = ($inventory_price != 0) ? $inventory_price : $inventory_mrp;
                    $hasDiscount = ($inventory_mrp > $inventory_price);
                    $discountPercent = $hasDiscount ? round((($inventory_mrp - $inventory_price) / $inventory_mrp) * 100) : 0;

                    echo "<a href='{$link_url}' class='bg-white rounded shadow-sm overflow-hidden hover:shadow-md transition'>
                            <div class='relative'>
                                <img src='{$product_image}' class='w-full h-56 object-cover' alt='{$product_title}'>
                                " . ($isNew ? "<span class='absolute top-2 right-2 bg-primary text-white text-xs px-2 py-1 rounded'>New</span>" : '') . "
                            </div>
                            <div class='p-4'>
                                <h4 class='text-base font-semibold mb-1'>{$product_title}</h4>
                                <div class='text-primary font-bold text-lg'>₹ {$price}</div>
                                " . ($hasDiscount ? "<div class='text-sm text-gray-500 line-through'>₹ {$inventory_mrp}</div><div class='text-sm text-green-600'>{$discountPercent}% OFF</div>" : '') . "
                                " . (($product_highlight != 1) ? "<p class='text-sm text-gray-600 mt-1'>{$product_highlight}</p>" : '') . "
                            </div>
                        </a>";
                }
            } else {
                echo "<div class='col-span-full text-center text-gray-500'>No products found</div>";
            }
        }
        ?>
    </div>
</section>

<!-- Subscription Info -->
<section class="bg-gray-50 py-16 mt-20">
    <div class="container mx-auto px-4 max-w-4xl">
        <h2 class="text-3xl font-serif text-center mb-6">Subscription</h2>
        <div class="space-y-6 text-gray-700">
            <div>
                <h3 class="text-xl font-semibold">What's &amp; Blooms subscription?</h3>
                <p>&amp; Blooms subscription simplifies life when you want flowers delivered regularly to your loved
                    ones. Perfect for birthdays, anniversaries, and special occasions — always on time.</p>
            </div>
            <div>
                <h3 class="text-xl font-semibold">What's your happiness guarantee?</h3>
                <p>We want every subscriber to be delighted with their &amp; Blooms experience. If you’re not, we’ll
                    make it right — every time.</p>
            </div>
            <div>
                <h3 class="text-xl font-semibold">What if I want fewer flowers?</h3>
                <p>No problem. You can skip deliveries or reschedule with ease from your account dashboard.</p>
            </div>
            <div>
                <h3 class="text-xl font-semibold">Subscribe to Happiness</h3>
                <p>Whether you're gifting or treating yourself, get fresh flowers delivered on your schedule with 20%
                    off and free shipping — every time.</p>
            </div>
        </div>
    </div>
</section>

<?php

include_once 'footer.php';

?>