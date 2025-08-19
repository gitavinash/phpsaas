<?php
$whatsappUrl = 'https://wa.me/919910744119?text=' . ((isset($link_type)) ? (($link_type == 'product') ? urlencode('i want to know about ' . $product_title . ' ' . SITE_URL . ACTIVE_URL) : 'Hello') : 'Hello');

if ($checkProductMeta = mysqli_query($connection, "SELECT product_meta_key, product_meta_value FROM " . TABLE_PREFIX . "product_meta WHERE product_meta_relation_id='{$product_id}' AND product_meta_site_id=" . SITE_ID)) {

    if (mysqli_num_rows($checkProductMeta) != 0) {

        $productMeta = array();

        while ($checkProductMetaData = mysqli_fetch_assoc($checkProductMeta)) {


            // Create dynamic variable in the name of product_meta_key value

            ${$checkProductMetaData['product_meta_key']} = $checkProductMetaData['product_meta_value'];
        }
    }
}

require_once 'header.php';

?>

<main class="container mx-auto px-4 py-8 pt-24">
    <!-- Breadcrumb -->
    <div class="flex items-center text-sm text-gray-500 mb-6">
        <a href="/" class="hover:text-primary">Home</a>
        <span class="mx-2">/</span>
        <a href="<?php echo $brandURL ?>" class="hover:text-primary"><?php echo ucwords($category_title) ?></a>
        <span class="mx-2">/</span>
        <span class="text-gray-700"><?php echo ucwords($product_title) ?></span>
    </div>
    <!-- Product Hero Section -->
    <div class="flex flex-col md:flex-row gap-8 mb-16">
        <!-- Product Images -->
        <div class="md:w-1/2">
            <div class="product-image-container mb-4 rounded-lg overflow-hidden position-relative">
                <img src="<?php echo $product_image ?>" id="mainProductImage"
                    alt="<?php echo ucwords($product_title) ?>" class="w-full h-full object-cover object-top">
                <div id="zoomOverlay"></div>
            </div>
            <div class="grid thumbnail grid-cols-4 gap-2">

                <?php

                if ($product_gallery != '') {
                    $galleryImages = json_decode($product_gallery, TRUE);

                    if (!empty($galleryImages)) {

                        echo "<div class='h-24 rounded-md overflow-hidden cursor-pointer border-2 border-primary'>
                <img src='{$product_image}'
                     alt='{$product_title} Thumbnail 1'
                     data-src='{$product_image}'
                     class='w-full h-full object-cover object-center'>
              </div>";

                        for ($i = 0; $i < sizeof($galleryImages); $i++) {

                            extract($galleryImages[$i]);
                            echo "<div class='h-24 rounded-md overflow-hidden cursor-pointer border-2'>
                    <img src='{$path}'
                         alt='{$product_title} Thumbnail " . ($i + 1) . "'
                         data-src='{$path}'
                         class='w-full h-full object-cover object-center'>
                  </div>";
                        }
                    }
                }
                ?>

            </div>
        </div>
        <!-- Product Details -->
        <div class="md:w-1/2">
            <h1 class="text-3xl font-semibold text-gray-800 mb-2"><?php echo ucwords($product_title) ?></h1>
            <div class="flex items-center mb-4">
                <div class="flex text-yellow-400">
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-half-fill"></i>
                </div>
                <span class="text-gray-500 text-sm ml-2">4.5</span>
            </div>
            <div class="mb-6">
                <?php
                if ($inventory_price == $inventory_mrp) {
                    echo "<span class='text-2xl font-bold text-gray-900'>{$inventory_currency_symbol} {$inventory_price}</span>";
                } else { ?>
                <span
                    class="text-2xl font-bold text-gray-900"><?php echo $inventory_currency_symbol . ' ' . $inventory_price ?></span>
                <span
                    class="text-gray-500 line-through ml-2"><?php echo $inventory_currency_symbol . ' ' . $inventory_mrp ?></span>
                <span class="bg-red-100 text-red-600 text-xs font-medium px-2 py-1 rounded ml-2">SAVE
                    <?php echo $discountPersantage = round((1 - $inventory_price / $inventory_mrp) * 100, 0); ?>%</span>
                <?php
                }

                ?>
            </div>
            <h3 class="text-xl font-semibold mb-4">Product Highlight</h3>
            <p class="text-gray-600 mb-6"><?php echo $product_highlight ?></p>
            <div class="flex flex-col sm:flex-row gap-4 mb-8">
                <p style="display:none" id="productInfo"
                    value="<?php echo $product_id . '_' . (($inventory_price == 0) ? $inventory_mrp : $inventory_price) . '_1' ?>">
                </p>
                <button id="addToCart"
                    class="flex-1 bg-primary text-white py-3 px-6 rounded-button font-medium hover:bg-opacity-90 transition flex items-center justify-center whitespace-nowrap">
                    <i class="ri-shopping-cart-line mr-2"></i>
                    Add to Cart
                </button>
                <a href="<?php echo $whatsappUrl ?>" target="_blank" rel="noopener noreferrer"
                    class="flex items-center justify-center px-6 py-3 rounded-button font-medium text-green-800 bg-green-100 border border-green-300 hover:bg-green-200 hover:text-green-900 focus:outline-none focus:ring-2 focus:ring-green-400 transition whitespace-nowrap">
                    <i class="ri-whatsapp-line mr-2 text-xl"></i>
                    Order on WhatsApp
                </a>

            </div>
            <div class="border-t border-gray-200 pt-6">
                <div class="flex items-center mb-3">
                    <div class="w-6 h-6 flex items-center justify-center text-green-500 mr-2">
                        <i class="ri-truck-line"></i>
                    </div>
                    <p class="text-sm text-gray-600">Same day delivery</p>
                </div>
                <div class="flex items-center">
                    <div class="w-6 h-6 flex items-center justify-center text-green-500 mr-2">
                        <i class="ri-shield-check-line"></i>
                    </div>
                    <p class="text-sm text-gray-600">Secure payment</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Product Information Tabs -->
    <div class="mb-16">
        <div class="border-b border-gray-200">
            <div class="flex flex-wrap -mb-px">
                <button
                    class="tab-button inline-block py-4 px-6 text-center border-b-2 border-primary text-primary font-medium"
                    data-tab="description">
                    Description
                </button>
                <button
                    class="tab-button inline-block py-4 px-6 text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium"
                    data-tab="delivery">
                    Delivery Information
                </button>
                <button
                    class="tab-button inline-block py-4 px-6 text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium"
                    data-tab="care">
                    Care Instructions
                </button>
            </div>
        </div>
        <div class="py-6">
            <div id="description-content" class="tab-content active">
                <div class="max-w-3xl">
                    <h3 class="text-xl font-semibold mb-4">Product Description</h3>
                    <div class="sameFont">
                        <?php echo $product_description ?>
                    </div>
                </div>
            </div>
            <div id="delivery-content" class="tab-content">
                <div class="max-w-3xl">
                    <h3 class="text-xl font-semibold mb-4">Delivery Information</h3>
                    <div class="mb-6 sameFont">
                        <?php

                        if (isset($delivery_information)) {

                            if ($delivery_information != '') {

                                echo '<ul><li>' . str_replace('<br />', '</li><li>', $delivery_information) . '</li></ul>';
                            } else {

                                if (strtolower($category_title) != 'planters' && strtolower($category_title) != 'cake') {
                        ?>
                        <ul>
                            <li>The image displayed is indicative. Actual product may vary in design & shape
                            </li>
                            <li>Flowers may deliver in Bud, Semi Bloomed or Fully Bloomed stage.</li>
                            <li>The chosen delivery time is an estimate.</li>
                            <li>Since flowers are perishable in nature, we will be able to attempt delivery of
                                your
                                order once.</li>
                            <li>Occasionally, substitution of flowers is necessary due unavailability issues.
                            </li>
                        </ul>

                        <?php

                                }
                            }
                        }

                        ?>
                    </div>
                </div>
            </div>
            <div id="care-content" class="tab-content">
                <div class="max-w-3xl">
                    <h3 class="text-xl font-semibold mb-4">Care Instructions</h3>
                    <div class="sameFont">
                        <?php
                        if (isset($care_n_tips)) {

                            if ($care_n_tips != '') {

                                echo '<ul><li>' . str_replace('<br />', '</li><li>', $care_n_tips) . '</li></ul>';
                            } else {
                                if (strtolower($category_title) != 'planters' && strtolower($category_title) != 'cake') {
                        ?>
                        <ul>
                            <li>Start by removing the bouquet wrapper.</li>
                            <li>Cut the stems by one or two inches from the bottom to ensure better water
                                intake.
                            </li>
                            <li>Choose a vase and fill it with water.</li>
                            <li>Remove the leaves below the water level to prevent bacterial growth.</li>
                            <li>Add two tablespoon of Lemon Juice and sugar into the water.</li>
                            <li>Make sure to place the vase in indirect sunlight.</li>
                        </ul>

                        <?php
                                }
                            }
                        }

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php


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

    ?>
    <!-- Related Products -->

    <?php

    if (isset($tagList)) { ?>

    <div class="mb-16">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">You May Also Like</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

            <?php

                if ($getProductList = mysqli_query($connection, "SELECT DISTINCT 
    p.product_id, 
    p.product_title, 
    p.product_image, 
    i.inventory_price, 
    i.inventory_mrp, 
    i.inventory_currency_symbol, 
    l.link_url
FROM " . TABLE_PREFIX . "product p
INNER JOIN " . TABLE_PREFIX . "inventory i ON p.product_id = i.inventory_relation_id
INNER JOIN " . TABLE_PREFIX . "link l ON l.link_relation_id = p.product_id 
    AND l.link_status = 200 
    AND l.link_type = 'product'
WHERE p.product_status = 1 
  AND p.product_site_id = " . SITE_ID . " 
  AND l.link_site_id = " . SITE_ID . " 
  AND p.product_id IN ({$tagList})")) {

                    while ($productData = mysqli_fetch_assoc($getProductList)) {
                        extract($productData);
                        echo "
                        <a href='{$link_url}' title='{$product_title}' class='group'>
                    <div class='relative mb-3 rounded-lg overflow-hidden'>
                        <img src='{$product_image}'
                            alt='{$product_title}'
                            class='w-full h-64 object-cover object-top group-hover:scale-105 transition duration-300'>
                        <div
                            class='absolute bottom-0 left-0 right-0 bg-primary bg-opacity-20 p-4 translate-y-full group-hover:translate-y-0 transition duration-300'>
                            <button data-info='{$product_id}_{$inventory_price}_1'
                                class='w-full quickAddToCart py-2 bg-white text-primary rounded-button font-medium hover:bg-primary hover:text-white transition flex items-center justify-center whitespace-nowrap'>
                                <i class='ri-shopping-cart-line mr-2'></i>
                                Add to cart
                            </button>
                        </div>
                    </div>
                    <h3 class='font-medium text-gray-800 mb-1'>{$product_title}</h3>
                    <p class='text-gray-900 font-medium'>{$inventory_currency_symbol} {$inventory_price}</p>
                        </a>
                        ";
                    }
                }

                ?>

        </div>
    </div>

    <?php
    }
    ?>

    <!-- Trust Indicators -->
    <div class="bg-gray-50 rounded-lg p-8 mb-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 flex items-center justify-center text-primary text-3xl mb-4">
                    <i class="ri-truck-line"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Fast Delivery</h3>
                <p class="text-gray-600 text-sm">Same-day delivery available for orders placed before 11am</p>
            </div>
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 flex items-center justify-center text-primary text-3xl mb-4">
                    <i class="ri-plant-line"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Fresh Guarantee</h3>
                <p class="text-gray-600 text-sm">7-day freshness guarantee or your money back</p>
            </div>
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 flex items-center justify-center text-primary text-3xl mb-4">
                    <i class="ri-shield-check-line"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Secure Payment</h3>
                <p class="text-gray-600 text-sm">Multiple secure payment options for your convenience</p>
            </div>
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 flex items-center justify-center text-primary text-3xl mb-4">
                    <i class="ri-customer-service-2-line"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Customer Support</h3>
                <p class="text-gray-600 text-sm">Dedicated support team available 7 days a week</p>
            </div>
        </div>
    </div>
</main>
<?php

require_once 'footer.php';

?>