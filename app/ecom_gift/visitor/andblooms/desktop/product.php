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

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: '#D4946D',
                    secondary: '#E5B499'
                },
                borderRadius: {
                    'none': '0px',
                    'sm': '4px',
                    DEFAULT: '8px',
                    'md': '12px',
                    'lg': '16px',
                    'xl': '20px',
                    '2xl': '24px',
                    '3xl': '32px',
                    'full': '9999px',
                    'button': '8px'
                }
            }
        }
    }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <style>
    :where([class^="ri-"])::before {
        content: "\f3c2";
    }

    body {
        font-family: 'Poppins', sans-serif;
    }

    .product-image-container {
        position: relative;
        overflow: hidden;
        width: 100%;
        height: 600px;
    }

    #mainProductImage {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    #zoomOverlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        /* important to avoid flicker */
        background-repeat: no-repeat;
        background-size: 200% 200%;
        /* zoom factor 2x */
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
        cursor: zoom-in;
    }

    #zoomOverlay.active {
        opacity: 1;
        cursor: zoom-out;
    }

    .quantity-input::-webkit-outer-spin-button,
    .quantity-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .quantity-input {
        -moz-appearance: textfield;
    }

    .tab-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
        font-family: 'Poppins', sans-serif;
        font-size: 16px;
        line-height: 24px;
        --tw-text-opacity: 1;
        color: rgb(75 85 99 / var(--tw-text-opacity, 1));
    }

    .tab-content p {
        margin-bottom: 20px;
        line-height: 24px;
    }

    .tab-content.active {
        max-height: 500px;
    }

    /* ul {
        list-style: bullet !important;
        margin-bottom: 20px;
    }

    ul li {
        margin-left: 30px;
    } */

    /* footer ul {
        list-style: none;
        margin: 0;
    } */

    @media (max-width: 768px) {
        .product-image-container {
            height: 400px;
        }

    }
    </style>
    <?php

    require_once DEFAULT_META;

    ?>
</head>

<body class="bg-white">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-8">
                <a href="/" class="flex items-center">
                    <img src="https://andblooms.com//upload/2020/10/1602753246_andblooms_logo_mobile_128x128.png"
                        alt="AndBlooms Logo" class="h-8">
                </a>
                <nav class="hidden md:flex space-x-6">
                    <a href="/" class="text-gray-700 hover:text-primary transition">Home</a>
                    <a href="/tag/luxury/" class="text-gray-700 hover:text-primary transition">Luxury Collections</a>
                    <a href="/category/planters/" class="text-gray-700 hover:text-primary transition">Planters</a>
                    <a href="/tag/blooms-of-<?php echo strtolower(date('F')) ?>/"
                        class="text-gray-700 hover:text-primary transition">Blooms of
                        <?php echo date('F') ?></a>
                    <a href="/tag/father/" class="hover:text-gray-700 text-primary transition"><strong>Father's
                            Day</strong></a>
                </nav>
            </div>
            <div class="flex items-center space-x-4">
                <div class="relative hidden md:block">
                    <form action="/search/" method="get">
                        <input type="text" name="q" placeholder="Search..."
                            class="pl-10 pr-4 py-2 rounded-full border border-gray-200 focus:outline-none focus:border-primary w-64 text-sm">
                        <div
                            class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 flex items-center justify-center text-gray-400">
                            <i class="ri-search-line"></i>
                        </div>
                    </form>
                </div>
                <?php

                if ($cartNumber = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "cart INNER JOIN " . TABLE_PREFIX . "product ON cart_relation_id=product_id WHERE cart_session_id='" . SESSION_ID . "' AND cart_status=0 AND product_status=1 AND cart_site_id=" . SITE_ID)) {

                    $cartItems = mysqli_num_rows($cartNumber);
                }

                ?>
                <div class="relative">
                    <a href="/checkout/"
                        class="w-10 h-10 flex items-center justify-center text-gray-700 hover:text-primary transition">
                        <i class="ri-shopping-bag-line text-xl"></i>
                    </a>
                    <span
                        class="absolute -top-1 -right-1 bg-primary text-white text-xs w-5 h-5 flex items-center justify-center rounded-full"><?php echo $cartItems ?></span>
                </div>
                <button class="md:hidden w-10 h-10 flex items-center justify-center text-gray-700"
                    id="mobile-menu-button">
                    <i class="ri-menu-line text-xl"></i>
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div class="md:hidden hidden bg-white w-full py-4 px-4 shadow-md" id="mobile-menu">
            <div class="mb-4">
                <div class="relative">
                    <input type="text" placeholder="Search..."
                        class="w-full pl-10 pr-4 py-2 rounded-full border border-gray-200 focus:outline-none focus:border-primary text-sm">
                    <div
                        class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 flex items-center justify-center text-gray-400">
                        <i class="ri-search-line"></i>
                    </div>
                </div>
            </div>
            <nav class="flex flex-col space-y-3">
                <a href="/" class="text-gray-700 hover:text-primary transition">Home</a>
                <a href="/tag/luxury/" class="text-gray-700 hover:text-primary transition">Luxury Collections</a>
                <a href="/category/planters/" class="text-gray-700 hover:text-primary transition">Planters</a>
                <a href="/tag/blooms-of-<?php echo strtolower(date('F')) ?>/"
                    class="text-gray-700 hover:text-primary transition">Blooms of
                    <?php echo date('F') ?></a>
                <a href="/tag/father/" class="hover:text-gray-700 text-primary transition"><strong>Father's
                        Day</strong></a>
            </nav>
        </div>
    </header>
    <main class="container mx-auto px-4 py-8">
        <!-- Breadcrumb -->
        <div class="flex items-center text-sm text-gray-500 mb-6">
            <a href="/" class="hover:text-primary">Home</a>
            <span class="mx-2">/</span>
            <a href="<?php echo $brandURL ?>" class="hover:text-primary"><?php echo $category_title ?></a>
            <span class="mx-2">/</span>
            <span class="text-gray-700"><?php echo $product_title ?></span>
        </div>
        <!-- Product Hero Section -->
        <div class="flex flex-col md:flex-row gap-8 mb-16">
            <!-- Product Images -->
            <div class="md:w-3/5">
                <div class="product-image-container mb-4 rounded-lg overflow-hidden position-relative">
                    <img src="<?php echo $product_image ?>" id="mainProductImage" alt="<?php echo $product_title ?>"
                        class="w-full h-full object-cover object-top">
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
            <div class="md:w-2/5">
                <h1 class="text-3xl font-semibold text-gray-800 mb-2"><?php echo $product_title ?></h1>
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
                    <?php
                    if (strtolower($category_title) != 'planters' && strtolower($category_title) != 'cake') {
                        echo "<div class='flex items-center mb-3'>
                            <div class='w-6 h-6 flex items-center justify-center text-green-500 mr-2'>
                                <i class='ri-refresh-line'></i>
                            </div>
                            <p class='text-sm text-gray-600'>Fresh flower guarantee - 7 days</p>
                        </div>";
                    }
                    ?>
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
                        <?php echo $product_description ?>
                    </div>
                </div>
                <div id="delivery-content" class="tab-content">
                    <div class="max-w-3xl">
                        <h3 class="text-xl font-semibold mb-4">Delivery Information</h3>
                        <div class="mb-6">
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
                            class='absolute bottom-0 left-0 right-0 bg-black bg-opacity-20 p-4 translate-y-full group-hover:translate-y-0 transition duration-300'>
                            <button data-info='{$product_id}_{$inventory_price}_1'
                                class='w-full quickAddToCart py-2 bg-white text-gray-800 rounded-button font-medium hover:bg-gray-100 transition flex items-center justify-center whitespace-nowrap'>
                                <i class='ri-shopping-cart-line mr-2'></i>
                                Quick Add
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
    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-16 pb-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <div>
                    <a href="/" class="inline-block mb-6">
                        <img src="https://andblooms.com//upload/2020/10/1602753246_andblooms_logo_mobile_128x128.png"
                            alt="AndBlooms Logo" class="h-8 brightness-0 invert">
                    </a>
                    <p class="text-gray-400 mb-6">Delivering fresh, beautiful flowers for all your special moments since
                        2010.</p>
                    <div class="flex space-x-4">
                        <a href="#"
                            class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 text-white hover:bg-primary transition">
                            <i class="ri-facebook-fill"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 text-white hover:bg-primary transition">
                            <i class="ri-instagram-line"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 text-white hover:bg-primary transition">
                            <i class="ri-pinterest-line"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 text-white hover:bg-primary transition">
                            <i class="ri-twitter-x-line"></i>
                        </a>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-6">Quick Links</h3>
                    <ul class="space-y-3">
                        <li><a href="/" class="text-gray-400 hover:text-white transition">Home</a></li>
                        <li><a href="/about-us/" class="text-gray-400 hover:text-white transition">About Us</a></li>
                        <li><a href="/workshop/" class="text-gray-400 hover:text-white transition">Workshop</a></li>
                        <li><a href="/franchise/" class="text-gray-400 hover:text-white transition">Franchise</a></li>
                        <li><a href="/contact-us/" class="text-gray-400 hover:text-white transition">Contact</a></li>
                        <li><a href="/subscription/" class="text-gray-400 hover:text-white transition">Subscription</a>
                        </li>

                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-6">Product categories</h3>
                    <ul class="space-y-3">
                        <li><a href="/tag/luxury/" class="text-gray-400 hover:text-white transition">Luxury
                                Collections</a>
                        <li><a href="/category/flower/" class="text-gray-400 hover:text-white transition">Fresh
                                Flowers</a>
                        <li><a href="/category/and-blooms-express/" class="text-gray-400 hover:text-white transition">&
                                Blooms Express</a>
                        <li><a href="/category/artistic-artificial/"
                                class="text-gray-400 hover:text-white transition">Artistic artificial</a>
                        <li><a href="/category/cake/" class="text-gray-400 hover:text-white transition">Cake</a>
                        <li><a href="/category/infinity-everlasting-flowers/"
                                class="text-gray-400 hover:text-white transition">Infinity Everlasting Flowers</a>
                        <li><a href="/category/planters/" class="text-gray-400 hover:text-white transition">Planters</a>
                        <li><a href="/category/vases-and-ceramics/"
                                class="text-gray-400 hover:text-white transition">Vases & Ceramics</a>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-6">Newsletter</h3>
                    <p class="text-gray-400 mb-4">Subscribe to receive updates, access to exclusive deals, and more.</p>
                    <div class="mb-4">
                        <div class="flex">
                            <input type="email" placeholder="Your email address" d
                                class="flex-1 py-3 px-4 bg-gray-800 border-none rounded-l-button text-white focus:outline-none text-sm">
                            <button
                                class="bg-primary hover:bg-opacity-90 text-white py-3 px-4 rounded-r-button font-medium transition whitespace-nowrap">Subscribe</button>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold mb-4 mt-6">Payment Methods</h3>
                    <div class="flex space-x-3">
                        <div class="w-10 h-6 flex items-center justify-center text-gray-400">
                            <i class="ri-visa-fill text-xl"></i>
                        </div>
                        <div class="w-10 h-6 flex items-center justify-center text-gray-400">
                            <i class="ri-mastercard-fill text-xl"></i>
                        </div>
                        <div class="w-10 h-6 flex items-center justify-center text-gray-400">
                            <i class="ri-paypal-fill text-xl"></i>
                        </div>
                        <div class="w-10 h-6 flex items-center justify-center text-gray-400">
                            <i class="ri-apple-fill text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-500 text-sm mb-4 md:mb-0">© <?php echo date('Y') . ' ' . SITE_NAME ?>. All
                        rights reserved. | Developed by <a href='https://blumox.com'
                            class="text-gray-500 hover:text-white text-sm transition">Blumox Technologies</a></p>
                    <div class="flex space-x-6">
                        <a href="/terms-and-conditions/" class="text-gray-500 hover:text-white text-sm transition">Terms
                            and
                            Conditions</a>
                        <a href="/privacy-policy/" class="text-gray-500 hover:text-white text-sm transition">Privacy
                            Policy</a>
                        <a href="/cancellation---refund/"
                            class="text-gray-500 hover:text-white text-sm transition">Cancellation & Refund</a>
                        <a href="/shipping-policy/" class="text-gray-500 hover:text-white text-sm transition">Shipping
                            policy</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script type="text/javascript" src="<?php echo PLUGIN_LIBRARY ?>datepicker/datepicker.min.js"></script>
    <script type="text/javascript" src="<?php echo USER_JS ?>app.min.js"></script>
    <script id="mobile-menu-script">
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    });
    </script>
    <script id="quantity-control-script">
    document.addEventListener('DOMContentLoaded', function() {
        const decreaseButton = document.getElementById('decrease-quantity');
        const increaseButton = document.getElementById('increase-quantity');
        const quantityInput = document.getElementById('quantity');
        decreaseButton.addEventListener('click', function() {
            const currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        });
        increaseButton.addEventListener('click', function() {
            const currentValue = parseInt(quantityInput.value);
            quantityInput.value = currentValue + 1;
        });
        quantityInput.addEventListener('change', function() {
            if (parseInt(this.value) < 1 || isNaN(parseInt(this.value))) {
                this.value = 1;
            }
        });
    });
    </script>
    <script id="tab-control-script">
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                // Update active tab button
                tabButtons.forEach(btn => {
                    btn.classList.remove('border-primary', 'text-primary');
                    btn.classList.add('border-transparent', 'text-gray-500');
                });
                this.classList.remove('border-transparent', 'text-gray-500');
                this.classList.add('border-primary', 'text-primary');
                // Show active tab content
                tabContents.forEach(content => {
                    content.classList.remove('active');
                });
                document.getElementById(`${tabName}-content`).classList.add('active');
            });
        });
    });
    </script>
    <script id="checkbox-script">
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxLabels = document.querySelectorAll(
            'label[for="chocolate"], label[for="card"], label[for="vase"]');
        checkboxLabels.forEach(label => {
            label.addEventListener('click', function(e) {
                const checkbox = document.getElementById(this.getAttribute('for'));
                const checkIcon = this.querySelector('.ri-check-line');
                const customCheckbox = this.querySelector('.checkbox-custom');
                checkbox.checked = !checkbox.checked;
                if (checkbox.checked) {
                    customCheckbox.classList.add('bg-primary', 'border-primary');
                    checkIcon.classList.remove('hidden');
                } else {
                    customCheckbox.classList.remove('bg-primary', 'border-primary');
                    checkIcon.classList.add('hidden');
                }
                e.preventDefault();
            });
        });
    });
    </script>
    <script>
    $(document).ready(function() {
        // Thumbnail click to update main image & border
        $('.thumbnail img').on('click', function() {
            var newSrc = $(this).attr('src');
            $('#mainProductImage').attr('src', newSrc);
            $('#zoomOverlay').css('background-image', 'url(' + newSrc + ')');

            $('.thumbnail img').parent().removeClass('border-primary');
            $(this).parent().addClass('border-primary');
        });

        // Setup initial zoom overlay image
        $('#zoomOverlay').css('background-image', 'url(' + $('#mainProductImage').attr('src') + ')');

        // Zoom on hover
        $('.product-image-container').on('mouseenter', function() {
            $('#zoomOverlay').addClass('active');
        }).on('mouseleave', function() {
            $('#zoomOverlay').removeClass('active');
        }).on('mousemove', function(e) {
            var $this = $(this);
            var offset = $this.offset();
            var x = e.pageX - offset.left;
            var y = e.pageY - offset.top;
            var width = $this.width();
            var height = $this.height();

            // Calculate background position % (reverse because background moves opposite direction)
            var xPercent = (x / width) * 100;
            var yPercent = (y / height) * 100;

            $('#zoomOverlay').css('background-position', xPercent + '% ' + yPercent + '%');
        });
    });
    </script>
</body>

</html>