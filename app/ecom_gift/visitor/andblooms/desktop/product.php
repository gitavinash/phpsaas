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

/** Decode and fetch attributes/variations */
$productAttributes = [];
if (isset($inventory_variation) && !empty($inventory_variation)) {
    $decodedVariation = base64_decode($inventory_variation);
    $productAttributes = json_decode($decodedVariation, true) ?: [];
}

/** Decode and fetch add-ons */
$productAddons = [];
$addonProducts = [];
if (isset($inventory_addons) && !empty($inventory_addons)) {
    $decodedAddons = base64_decode($inventory_addons);
    $productAddons = json_decode($decodedAddons, true) ?: [];

    // Extract addon product IDs
    $addonIds = array_column($productAddons, 0);

    // remove current product id from add ons

    $addonIds = array_filter($addonIds, function ($id) use ($product_id) {
        return $id != $product_id;
    });

    if (!empty($addonIds)) {
        $addonIdsStr = implode(',', array_map('intval', $addonIds));
        $addOnQuery = "SELECT p.product_id, p.product_title, p.product_image, 
                             i.inventory_price, i.inventory_mrp, i.inventory_currency_symbol, l.link_url, c.category_title
                      FROM " . TABLE_PREFIX . "product p
                      INNER JOIN " . TABLE_PREFIX . "inventory i ON p.product_id = i.inventory_relation_id AND i.inventory_site_id = " . SITE_ID . "
                      INNER JOIN " . TABLE_PREFIX . "link l ON l.link_relation_id = p.product_id 
                        AND l.link_status = 200 AND l.link_type = 'product'
                    INNER JOIN " . TABLE_PREFIX . "category c ON c.category_id = p.product_category
                      WHERE p.product_status = 1 
                        AND p.product_site_id = " . SITE_ID . " 
                        AND l.link_site_id = " . SITE_ID . " 
                        AND p.product_id IN ({$addonIdsStr})";

        if ($addonResult = mysqli_query($connection, $addOnQuery)) {
            while ($addon = mysqli_fetch_assoc($addonResult)) {
                $addonProducts[] = $addon;
            }
        }
    }
}

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

// Fetch all tags for this product
$productTags = array();
if ($getProductTags = mysqli_query($connection, "SELECT DISTINCT tag_title FROM " . TABLE_PREFIX . "tag WHERE
tag_relation_id = {$product_id} AND tag_site_id = " . SITE_ID . " AND tag_type = 'product' ORDER BY tag_title")) {
    while ($tagRow = mysqli_fetch_assoc($getProductTags)) {
        if (isset($tagRow['tag_title']) && $tagRow['tag_title'] !== '') {
            $productTags[] = $tagRow['tag_title'];
        }
    }
}

require_once 'header.php';

?>

<style>
    @media (min-width: 768px) {
        .product-media-sticky {
            position: sticky;
            top: 6.5rem;
            align-self: flex-start;
        }

        .desktop-sticky-cta {
            position: fixed;
            bottom: 1.5rem;
            z-index: 60;
            opacity: 0;
            pointer-events: none;
            transform: translateY(0.5rem);
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .desktop-sticky-cta.is-active {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        .desktop-default-cta.is-hidden {
            display: none;
        }
    }
</style>

<main class="container mx-auto px-4 py-8 pt-24 md:pb-32">
    <!-- Breadcrumb -->
    <nav class="flex items-center text-gray-500 mb-6 overflow-x-auto" aria-label="Breadcrumb">
        <ol class="flex items-center text-xs sm:text-sm whitespace-nowrap gap-1 sm:gap-2">
            <li>
                <a href="/" class="hover:text-primary transition-colors">Home</a>
            </li>
            <li class="text-gray-300">/</li>
            <li>
                <a href="<?php echo $brandURL ?>" class="hover:text-primary transition-colors truncate">
                    <?php echo htmlspecialchars($category_title, ENT_QUOTES, 'UTF-8') ?>
                </a>
            </li>
            <li class="text-gray-300">/</li>
            <li class="text-gray-700 truncate" aria-current="page">
                <?php echo htmlspecialchars(ucwords($product_title), ENT_QUOTES, 'UTF-8') ?>
            </li>
        </ol>
    </nav>
    <!-- Product Hero Section -->
    <div class="flex flex-col md:flex-row gap-8 mb-16">
        <!-- Product Images -->
        <div class="md:w-1/2 product-media-sticky">
            <?php
            $thumbnails = array();

            if ($product_gallery != '') {
                $galleryImages = json_decode($product_gallery, true);
                if (!empty($galleryImages)) {
                    $thumbnails[] = array('path' => $product_image, 'title' => $product_title);
                    foreach ($galleryImages as $gallery) {
                        $thumbnails[] = array('path' => $gallery['path'], 'title' => $product_title);
                    }
                }
            } else {
                $thumbnails[] = array('path' => $product_image, 'title' => $product_title);
            }
            ?>
            <div class="product-gallery-wrapper">
                <!-- Main Image Area with Zoom Flyout Anchor -->
                <div class="product-main-view relative mb-3">
                    <!-- Main Product Image Container (Fixed square aspect ratio, never increases height on hover) -->
                    <div class="product-image-container relative rounded-xl overflow-hidden bg-gray-50 aspect-square border border-gray-100 shadow-sm cursor-crosshair group select-none" id="productMainContainer">
                        <!-- Dynamic Ambient Color Glow Background -->
                        <div id="productAmbientBg" class="product-ambient-bg"></div>

                        <img src="<?php echo $product_image ?>" id="mainProductImage"
                            alt="<?php echo ucwords($product_title) ?>" class="w-full h-full object-cover object-center select-none pointer-events-none relative z-10">

                        <!-- Amazon Zoom Lens -->
                        <div id="zoomLens" class="zoom-lens hidden"></div>

                        <!-- Visual Zoom Hint Badges -->
                        <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-gray-700 text-xs px-2.5 py-1 rounded-full shadow-sm flex items-center gap-1.5 pointer-events-none transition-opacity duration-200 group-hover:opacity-0">
                            <i class="ri-zoom-in-line text-primary text-sm"></i>
                            <span class="hidden md:inline font-medium">Roll over image to zoom in</span>
                            <span class="md:hidden font-medium">Double tap to zoom image</span>
                        </div>

                        <!-- Mobile Counter Badge -->
                        <span id="galleryCounter" class="absolute bottom-3 right-3 bg-black/60 backdrop-blur-sm text-white text-xs px-2.5 py-1 rounded-full font-medium z-10 pointer-events-none md:hidden">
                            1 / <?php echo count($thumbnails); ?>
                        </span>

                        <?php if (count($thumbnails) > 1) { ?>
                        <!-- Quick Navigation Arrows on Main Image -->
                        <button type="button" id="galleryPrevBtn" class="gallery-nav-btn prev absolute left-2.5 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/95 shadow-md text-gray-700 hover:text-primary hover:bg-white flex items-center justify-center transition-all z-10 opacity-90 md:opacity-0 md:group-hover:opacity-100 focus:outline-none" aria-label="Previous image">
                            <i class="ri-arrow-left-s-line text-xl"></i>
                        </button>
                        <button type="button" id="galleryNextBtn" class="gallery-nav-btn next absolute right-2.5 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/95 shadow-md text-gray-700 hover:text-primary hover:bg-white flex items-center justify-center transition-all z-10 opacity-90 md:opacity-0 md:group-hover:opacity-100 focus:outline-none" aria-label="Next image">
                            <i class="ri-arrow-right-s-line text-xl"></i>
                        </button>
                        <?php } ?>
                    </div>

                    <!-- Amazon Zoom Flyout Window (Desktop) - Anchored strictly to product-main-view so height matches main image -->
                    <div id="zoomFlyout" class="zoom-flyout hidden">
                        <img src="<?php echo $product_image ?>" id="zoomFlyoutImg" alt="<?php echo ucwords($product_title) ?> Zoom" class="zoom-flyout-img">
                        <div class="zoom-flyout-badge"><i class="ri-zoom-in-line text-primary"></i> Click image for expanded view</div>
                    </div>
                </div>

                <?php if (count($thumbnails) > 1) { ?>
                <!-- Thumbnails Strip (Always visible directly below main product image) -->
                <div class="thumbnails-container flex md:grid md:grid-cols-5 gap-2.5 overflow-x-auto md:overflow-visible pb-2 md:pb-0 scrollbar-none" id="thumbnailsStrip">
                    <?php
                    foreach ($thumbnails as $index => $thumb) {
                        $isActive = $index === 0 ? 'border-primary ring-2 ring-primary/40' : 'border-gray-200 hover:border-gray-300';
                        echo "<div class='thumbnail-item flex-shrink-0 w-16 h-16 md:w-auto md:h-auto aspect-square rounded-lg overflow-hidden cursor-pointer border-2 {$isActive} transition-all duration-200 bg-white' data-index='{$index}' data-src='{$thumb['path']}' data-title='" . htmlspecialchars($thumb['title'], ENT_QUOTES, 'UTF-8') . "'>";
                        echo "<img src='{$thumb['path']}' alt='" . htmlspecialchars($thumb['title'], ENT_QUOTES, 'UTF-8') . " Thumbnail " . ($index + 1) . "' class='w-full h-full object-cover select-none pointer-events-none'>";
                        echo "</div>";
                    }
                    ?>
                </div>
                <?php } ?>
            </div>
        </div>
        <!-- Product Details -->
        <div class="md:w-1/2 product-details">
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
                if ($inventory_price == $inventory_mrp || $inventory_mrp == 0) {
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
                <div class="text-sm text-gray-500 mb-4">
                    <?php
                    $filterTags = isset($productTags) ? array_map('strtolower', $productTags) : [];

                    $filterTags = array_map('trim', $filterTags);
                    $hasValentineTag = array_reduce($filterTags, function ($carry, $tag) {
                        return $carry || (bool) preg_match('/valentine/i', $tag);
                    }, false);

                    $subHeadingText = $hasValentineTag ? "Love, Unfiltered — Valentine Edition | New Launch" : '';

                    echo (!empty($sub_heading)) ? $sub_heading : $subHeadingText;
                    ?>
                </div>
            </div>

            <!-- Stock & Urgency Indicators -->
            <!-- <div class="flex flex-col sm:flex-row gap-3 mb-4">
                <div class="flex items-center gap-2 bg-green-50 rounded-lg px-4 py-2">
                    <i class="ri-checkbox-circle-fill text-green-600 text-lg"></i>
                    <span
                        class="text-sm font-semibold text-green-700"><?php echo $inventory_in_stock == 1 ? 'In Stock' : 'Out of Stock'; ?></span>
                </div>
            </div> -->

            <div class="flex flex-col sm:flex-row gap-3 mb-4">
                <div class="flex items-center gap-2 bg-green-50 rounded-lg px-4 py-2">
                    <i class="ri-checkbox-circle-fill text-green-600 text-lg"></i>
                    <span class="text-sm font-semibold text-green-700">
                        Availability in: Delhi, Gurgaon, Noida, Ghaziabad, Faridabad
                    </span>
                </div>
            </div>

            <?php

            if ($product_highlight != '') { ?>
                <h3 class="text-xl font-semibold mb-4">Product Highlight</h3>
                <p class="text-gray-600 mb-6"><?php echo $product_highlight ?></p>
            <?php
            }

            if ($inventory_weight != '' || $inventory_lenght != '' || $inventory_height != '' || $inventory_width != '') { ?>
                <p class="font-semibold mb-4"><strong>Product Details</strong></p>
                <?php if ($inventory_weight != '') { ?>
                    <p class="text-gray-600 mb-6"><strong>Weight:</strong> <?php echo $inventory_weight ?></p>
                <?php } ?>
                <?php
                $dimensions = array();
                if ($inventory_lenght != '') {
                    $dimensions[] = 'Length: ' . $inventory_lenght . ' CM';
                }
                if ($inventory_height != '') {
                    $dimensions[] = 'Height: ' . $inventory_height . ' CM';
                }
                if ($inventory_width != '') {
                    $dimensions[] = 'Width: ' . $inventory_width . ' CM';
                }
                if (!empty($dimensions)) { ?>
                    <p class="mb-4"><strong>Dimensions:</strong> <?php echo implode(' | ', $dimensions) ?>
                    </p>
                <?php } ?>
            <?php }

            // Display attributes/variations as image cards
            if (!empty($productAttributes)) {
                $productIds = array();
                foreach ($productAttributes as $attribute) {
                    $productIds[] = $attribute[0];
                }
                $productVarient = implode(',', $productIds);

                if ($productVarient = mysqli_query($connection, "SELECT product_title, product_image, link_url
            FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id
            WHERE product_id IN ({$productVarient}) AND product_site_id=" . SITE_ID . " AND link_site_id=" . SITE_ID
                    . "
            AND link_type='product' AND link_status=200")) {
                    if (mysqli_num_rows($productVarient) > 0) {
                        echo '<div class="mb-6">';
                        echo '<h3 class="text-lg font-semibold text-gray-800 mb-3">Available Variations</h3>';
                        echo '<div
                    class="flex overflow-x-auto gap-4 pb-2 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                    ';

                        while ($productVarientData = mysqli_fetch_assoc($productVarient)) {
                            echo "<a href='{$productVarientData['link_url']}' class='flex-shrink-0 group'>";
                            echo "<div
                            class='relative w-32 h-40 rounded-lg overflow-hidden border-2 border-gray-200 hover:border-primary transition-all duration-300 hover:shadow-lg'>
                            ";
                            echo "<div
                                class='absolute inset-0 bg-cover bg-center group-hover:scale-110 transition-transform duration-300'
                                style='background-image: url(\"{$productVarientData['product_image']}\");'></div>";
                            echo "<div class='absolute inset-0 bg-gradient-to-t from-black/70 to-transparent'></div>
                            ";
                            echo "<div class='absolute bottom-0 left-0 right-0 p-2.5'>";
                            echo "<p class='text-white font-medium text-xs line-clamp-2 drop-shadow-lg'
                                    style='text-shadow: 0 2px 8px rgba(0,0,0,0.9);'>
                                    {$productVarientData['product_title']}</p>";
                            echo "</div>";
                            echo "<div
                                class='absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300'>
                            </div>";
                            echo "</div>";
                            echo "</a>";
                        }

                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    if (DEVELOPMENT_MODE == true) {
                        echo mysqli_error($connection);
                    } else {
                        echo "Error in fetching product variations.";
                    }
                }
            }

            ?>

            <?php

            if ($inventory_in_stock == 1) { ?>
                <div class="hidden sm:flex flex-col sm:flex-row gap-4 mb-8 desktop-default-cta">
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
            <?php
            } else { ?>
                <div class="hidden sm:flex flex-col sm:flex-row gap-4 mb-8 desktop-default-cta">
                    <button id="outOfStock"
                        class="flex-1 bg-gray-400 text-white py-3 px-6 rounded-button font-medium cursor-not-allowed flex items-center justify-center whitespace-nowrap">
                        <i class="ri-shopping-cart-line mr-2"></i>
                        Out of Stock
                    </button>
                </div>
            <?php
            }

            ?>

            <!-- Mobile Fixed CTA Buttons -->
            <div
                class="sm:hidden fixed bottom-0 left-0 right-0 bg-white border-t-2 border-gray-200 shadow-2xl z-50 px-4 py-3">
                <div class="flex gap-3">
                    <button onclick="document.getElementById('addToCart').click()"
                        class="flex-1 bg-primary text-white py-3.5 px-6 rounded-button font-bold hover:bg-opacity-90 transition flex items-center justify-center shadow-lg">
                        <i class="ri-shopping-cart-line mr-2 text-lg"></i>
                        Add to Cart
                    </button>
                    <a href="<?php echo $whatsappUrl ?>" target="_blank" rel="noopener noreferrer"
                        class="flex items-center justify-center px-5 py-3.5 rounded-button font-bold text-white bg-green-600 hover:bg-green-700 transition shadow-lg">
                        <i class="ri-whatsapp-line text-2xl"></i>
                    </a>
                </div>
            </div>

            <!-- Mobile Padding to prevent content being hidden by fixed buttons -->
            <div class="sm:hidden h-20"></div>

            <!-- Add-ons Recommendations -->
            <?php if (!empty($addonProducts)) {
                // Get unique categories from addon products
                $addonCategories = array();
                foreach ($addonProducts as $addon) {
                    if (!empty($addon['category_title']) && !in_array($addon['category_title'], $addonCategories)) {
                        $addonCategories[] = $addon['category_title'];
                    }
                }
            ?>
                <div
                    class="bg-gradient-to-br from-pink-50 to-purple-50 rounded-lg sm:rounded-xl p-3 sm:p-5 md:p-6 mb-4 sm:mb-6 border border-pink-100">
                    <div class="mb-3 sm:mb-4">
                        <h3
                            class="text-sm sm:text-base md:text-lg font-bold text-gray-800 mb-0.5 sm:mb-1 flex items-center">
                            <i class="ri-gift-line text-primary text-base sm:text-lg md:text-xl mr-1.5 sm:mr-2"></i>
                            Complete Your Gift
                        </h3>
                        <p class="text-[10px] sm:text-xs md:text-sm text-gray-600">Make it extra special with these
                            add-ons
                        </p>
                    </div>
                    <?php if (!empty($addonCategories)) { ?>
                        <div class="mb-3 sm:mb-4 flex flex-wrap gap-2">
                            <button
                                class="addon-filter-btn active text-[10px] sm:text-xs md:text-sm px-2.5 sm:px-3 md:px-4 py-1.5 sm:py-2 rounded-full border-2 border-primary text-primary font-semibold transition-all"
                                data-category="all">All</button>
                            <?php foreach ($addonCategories as $cat) { ?>
                                <button
                                    class="addon-filter-btn text-[10px] sm:text-xs md:text-sm px-2.5 sm:px-3 md:px-4 py-1.5 sm:py-2 rounded-full border-2 border-gray-300 text-gray-700 hover:border-primary hover:text-primary font-semibold transition-all"
                                    data-category="<?php echo strtolower(str_replace(' ', '-', $cat)); ?>"><?php echo ucwords($cat); ?></button>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-2.5 sm:gap-3 md:gap-4">
                        <?php foreach ($addonProducts as $addon) {
                            extract($addon);
                            $discountPercent = ($inventory_mrp > 0 && $inventory_price < $inventory_mrp)
                                ? round((1 - $inventory_price / $inventory_mrp) * 100, 0) : 0;
                        ?>
                            <div class="addon-product-card bg-white rounded-md sm:rounded-lg overflow-hidden border border-gray-200 hover:border-primary hover:shadow-lg transition-all duration-300 group flex flex-col"
                                data-category="<?php echo strtolower(str_replace(' ', '-', $addon['category_title'])); ?>">
                                <a href="<?php echo $link_url ?>" title="<?php echo ucwords($product_title) ?>"
                                    class="block flex flex-col h-full">
                                    <div class="relative w-full aspect-square overflow-hidden">
                                        <img src="<?php echo $product_image ?>" alt="<?php echo ucwords($product_title) ?>"
                                            class="absolute inset-0 w-full h-full object-cover object-center group-hover:scale-110 transition-transform duration-500">
                                        <?php if ($discountPercent > 0) { ?>
                                            <span
                                                class="absolute top-1.5 right-1.5 sm:top-2 sm:right-2 bg-red-500 text-white text-[9px] sm:text-[10px] md:text-xs font-bold px-1.5 sm:px-2 py-0.5 sm:py-1 rounded shadow-md z-10">
                                                -<?php echo $discountPercent ?>%
                                            </span>
                                        <?php } ?>
                                    </div>
                                    <div class="p-2 sm:p-2.5 md:p-3 flex flex-col flex-1">
                                        <h4
                                            class="font-medium text-gray-800 text-[10px] sm:text-[11px] md:text-xs leading-snug mb-1.5 sm:mb-2 line-clamp-2 min-h-[2.5rem] sm:min-h-[2.75rem]">
                                            <?php echo ucwords($product_title) ?></h4>
                                        <div class="flex items-baseline flex-wrap gap-1 sm:gap-1.5 mb-2 sm:mb-2.5">
                                            <span
                                                class="text-xs sm:text-sm md:text-base font-bold text-gray-900"><?php echo $inventory_currency_symbol . $inventory_price ?></span>
                                            <?php if ($inventory_mrp > 0 && $inventory_price < $inventory_mrp) { ?>
                                                <span
                                                    class="text-[9px] sm:text-[10px] md:text-xs text-gray-500 line-through"><?php echo $inventory_currency_symbol . $inventory_mrp ?></span>
                                            <?php } ?>
                                        </div>
                                        <button data-info="<?php echo $product_id . '_' . $inventory_price . '_1' ?>"
                                            class="w-full addAddOnProductToCart py-1.5 sm:py-2 md:py-2.5 text-[10px] sm:text-[11px] md:text-xs bg-primary text-white rounded-md sm:rounded-lg font-semibold hover:bg-opacity-90 hover:shadow-md active:scale-95 transition-all duration-300 flex items-center justify-center gap-1 mt-auto">
                                            <i class="ri-add-circle-line text-xs sm:text-sm md:text-base"></i>
                                            <span>Add</span>
                                        </button>
                                    </div>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>

            <!-- Premium Trust Badges - Horizontal Layout -->
            <div class="bg-white rounded-2xl border-2 border-gray-100 shadow-md overflow-hidden mb-6">
                <div
                    class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-gray-100">
                    <!-- Same Day Delivery -->
                    <div
                        class="p-6 text-center hover:bg-gradient-to-br hover:from-green-50 hover:to-transparent transition-all duration-300 group">
                        <div
                            class="w-16 h-16 mx-auto mb-3 flex items-center justify-center rounded-full bg-gradient-to-br from-green-400 to-green-600 shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="ri-truck-line text-2xl text-white"></i>
                        </div>
                        <h5 class="text-sm font-bold text-gray-800 mb-1">Same Day Delivery</h5>
                        <p class="text-xs text-gray-600">Fast & Reliable</p>
                    </div>

                    <!-- Fresh Flower Guarantee -->
                    <div
                        class="p-6 text-center hover:bg-gradient-to-br hover:from-blue-50 hover:to-transparent transition-all duration-300 group">
                        <div
                            class="w-16 h-16 mx-auto mb-3 flex items-center justify-center rounded-full bg-gradient-to-br from-blue-400 to-blue-600 shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="ri-refresh-line text-2xl text-white"></i>
                        </div>
                        <h5 class="text-sm font-bold text-gray-800 mb-1">Fresh Flower Guarantee</h5>
                        <p class="text-xs text-gray-600">7 Days Fresh</p>
                    </div>

                    <!-- Quality Guarantee -->
                    <div
                        class="p-6 text-center hover:bg-gradient-to-br hover:from-orange-50 hover:to-transparent transition-all duration-300 group">
                        <div
                            class="w-16 h-16 mx-auto mb-3 flex items-center justify-center rounded-full bg-gradient-to-br from-orange-400 to-orange-600 shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="ri-award-line text-2xl text-white"></i>
                        </div>
                        <h5 class="text-sm font-bold text-gray-800 mb-1">Quality Guarantee</h5>
                        <p class="text-xs text-gray-600">Premium Quality</p>
                    </div>

                    <!-- Secure Payment -->
                    <div
                        class="p-6 text-center hover:bg-gradient-to-br hover:from-purple-50 hover:to-transparent transition-all duration-300 group">
                        <div
                            class="w-16 h-16 mx-auto mb-3 flex items-center justify-center rounded-full bg-gradient-to-br from-purple-400 to-purple-600 shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="ri-shield-check-line text-2xl text-white"></i>
                        </div>
                        <h5 class="text-sm font-bold text-gray-800 mb-1">Secure Payment</h5>
                        <p class="text-xs text-gray-600">100% Protected</p>
                    </div>
                </div>
            </div>
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

                <h3 class="text-xl font-semibold mb-4">Product Description</h3>
                <div class="sameFont">
                    <?php echo $product_description ?>
                </div>

            </div>
            <div id="delivery-content" class="tab-content">
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
            <div id="care-content" class="tab-content">
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


    <?php if (!empty($productTags)) { ?>
        <div class="mt-5 text-sm text-gray-600">
            <div class="mb-2 font-medium text-gray-700">Tags</div>
            <div class="flex flex-wrap gap-2">
                <?php foreach ($productTags as $tagTitle) { ?>
                    <span
                        class="px-3 py-1 rounded-full border border-gray-100 bg-gray-50 text-gray-700 hover:border-primary hover:text-primary transition">
                        <?php
                        $tagName = htmlspecialchars($tagTitle, ENT_QUOTES, 'UTF-8');
                        echo "<a href='/tag/" . str_replace(' ', '-', strtolower($tagName))     . "/' target='_blank'>" . ucwords($tagName) . "</a>" ?>
                    </span>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
    </div>

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

<div class="hidden md:flex desktop-sticky-cta flex-col sm:flex-row gap-4">
    <button onclick="document.getElementById('addToCart').click()"
        class="flex-1 bg-primary text-white py-3 px-6 rounded-button font-semibold hover:bg-opacity-90 transition flex items-center justify-center whitespace-nowrap">
        <i class="ri-shopping-cart-line mr-2"></i>
        Add to Cart
    </button>
    <a href="<?php echo $whatsappUrl ?>" target="_blank" rel="noopener noreferrer"
        class="flex items-center justify-center px-6 py-3 rounded-button font-semibold text-green-800 bg-green-100 border border-green-300 hover:bg-green-200 hover:text-green-900 focus:outline-none focus:ring-2 focus:ring-green-400 transition whitespace-nowrap">
        <i class="ri-whatsapp-line mr-2 text-xl"></i>
        Order on WhatsApp
    </a>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterBtns = document.querySelectorAll('.addon-filter-btn');
        const productCards = document.querySelectorAll('.addon-product-card');
        const mediaSticky = document.querySelector('.product-media-sticky');
        const desktopStickyCta = document.querySelector('.desktop-sticky-cta');
        const desktopDefaultCta = document.querySelector('.desktop-default-cta');
        const productDetails = document.querySelector('.product-details');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const selectedCategory = this.getAttribute('data-category');

                // Update active button
                filterBtns.forEach(b => b.classList.remove('active', 'border-primary',
                    'text-primary'));
                filterBtns.forEach(b => b.classList.add('border-gray-300', 'text-gray-700'));
                this.classList.add('active', 'border-primary', 'text-primary');
                this.classList.remove('border-gray-300', 'text-gray-700');

                // Filter products
                productCards.forEach(card => {
                    if (selectedCategory === 'all') {
                        card.style.display = '';
                    } else {
                        const cardCategory = card.getAttribute('data-category');
                        card.style.display = cardCategory === selectedCategory ? '' :
                            'none';
                    }
                });
            });
        });

        if (mediaSticky && desktopStickyCta && productDetails) {
            const updateStickyCta = () => {
                if (window.innerWidth < 768) {
                    desktopStickyCta.classList.remove('is-active');
                    desktopStickyCta.style.left = '';
                    desktopStickyCta.style.width = '';
                    if (desktopDefaultCta) {
                        desktopDefaultCta.classList.remove('is-hidden');
                    }
                    return;
                }

                const stickyTop = parseFloat(getComputedStyle(mediaSticky).top) || 0;
                const mediaRect = mediaSticky.getBoundingClientRect();
                const mediaHeight = mediaSticky.offsetHeight;
                const isStuck = mediaRect.top <= stickyTop + 1 &&
                    mediaRect.bottom >= stickyTop + mediaHeight - 1;

                if (isStuck) {
                    const detailsRect = productDetails.getBoundingClientRect();
                    desktopStickyCta.style.left = `${detailsRect.left}px`;
                    desktopStickyCta.style.width = `${detailsRect.width}px`;
                    desktopStickyCta.classList.add('is-active');
                    if (desktopDefaultCta) {
                        desktopDefaultCta.classList.add('is-hidden');
                    }
                } else {
                    desktopStickyCta.classList.remove('is-active');
                    if (desktopDefaultCta) {
                        desktopDefaultCta.classList.remove('is-hidden');
                    }
                }
            };

            updateStickyCta();
            window.addEventListener('scroll', updateStickyCta, {
                passive: true
            });
            window.addEventListener('resize', updateStickyCta);
        }
    });
</script>

<!-- Amazon-Style Product Fullscreen Lightbox Modal -->
<div id="productLightboxModal" class="lightbox-modal fixed inset-0 z-[100] hidden bg-black/95 backdrop-blur-md flex-col select-none" role="dialog" aria-modal="true" aria-label="Product Image Gallery">
    <!-- Top Bar -->
    <div class="flex items-center justify-between px-3 sm:px-6 py-2.5 sm:py-3 bg-black/80 backdrop-blur-md border-b border-white/10 z-30 shrink-0">
        <!-- Title & Counter -->
        <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1 mr-2">
            <span class="text-xs sm:text-sm font-medium text-white truncate max-w-[130px] sm:max-w-md"><?php echo htmlspecialchars(ucwords($product_title), ENT_QUOTES, 'UTF-8'); ?></span>
            <span class="text-white/30 hidden sm:inline">|</span>
            <span id="lightboxCounter" class="text-xs sm:text-sm text-gray-300 font-mono bg-white/10 px-2 py-0.5 rounded whitespace-nowrap shrink-0">1 of <?php echo count($thumbnails); ?></span>
        </div>

        <!-- Hint badge on mobile and desktop -->
        <div class="flex items-center gap-1.5 text-[11px] sm:text-xs text-amber-200/95 bg-amber-500/15 px-2.5 sm:px-3 py-1 rounded-full border border-amber-400/25 mr-1 sm:mr-2 whitespace-nowrap shrink-0">
            <i class="ri-fingerprint-line text-primary text-xs sm:text-sm"></i>
            <span>Double tap to zoom image</span>
        </div>

        <!-- Controls -->
        <div class="flex items-center gap-1 sm:gap-2 shrink-0">
            <button type="button" id="lightboxZoomOut" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white/10 hover:bg-white/20 active:bg-white/30 text-white flex items-center justify-center transition focus:outline-none" title="Zoom Out (-)">
                <i class="ri-zoom-out-line text-base sm:text-lg"></i>
            </button>
            <button type="button" id="lightboxZoomIn" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white/10 hover:bg-white/20 active:bg-white/30 text-white flex items-center justify-center transition focus:outline-none" title="Zoom In (+)">
                <i class="ri-zoom-in-line text-base sm:text-lg"></i>
            </button>
            <button type="button" id="lightboxZoomReset" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white/10 hover:bg-white/20 active:bg-white/30 text-white flex items-center justify-center transition focus:outline-none" title="Reset Zoom">
                <i class="ri-refresh-line text-base sm:text-lg"></i>
            </button>
            <button type="button" id="lightboxCloseBtn" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white/20 hover:bg-red-600/90 active:bg-red-700 text-white flex items-center justify-center transition focus:outline-none ml-1 sm:ml-2" title="Close (Esc)">
                <i class="ri-close-line text-xl sm:text-2xl"></i>
            </button>
        </div>
    </div>

    <!-- Main Lightbox Stage -->
    <div class="flex-1 relative overflow-hidden flex items-center justify-center p-2 sm:p-6" id="lightboxStage">
        <!-- Prev Button -->
        <button type="button" id="lightboxPrevBtn" class="absolute left-2 sm:left-6 top-1/2 -translate-y-1/2 w-11 h-11 sm:w-13 sm:h-13 rounded-full bg-black/60 hover:bg-black/90 border border-white/20 text-white flex items-center justify-center transition z-20 focus:outline-none" aria-label="Previous image">
            <i class="ri-arrow-left-s-line text-2xl sm:text-3xl"></i>
        </button>

        <!-- Zoomable Image Viewport -->
        <div class="w-full h-full flex items-center justify-center cursor-grab active:cursor-grabbing overflow-hidden" id="lightboxViewport">
            <img src="<?php echo $product_image ?>" id="lightboxImage" alt="<?php echo htmlspecialchars(ucwords($product_title), ENT_QUOTES, 'UTF-8'); ?>" class="max-w-full max-h-full object-contain select-none transition-transform duration-150">
        </div>

        <!-- Next Button -->
        <button type="button" id="lightboxNextBtn" class="absolute right-2 sm:right-6 top-1/2 -translate-y-1/2 w-11 h-11 sm:w-13 sm:h-13 rounded-full bg-black/60 hover:bg-black/90 border border-white/20 text-white flex items-center justify-center transition z-20 focus:outline-none" aria-label="Next image">
            <i class="ri-arrow-right-s-line text-2xl sm:text-3xl"></i>
        </button>
    </div>

    <?php if (count($thumbnails) > 1) { ?>
    <!-- Bottom Thumbnail Strip -->
    <div class="px-4 py-3 bg-black/60 border-t border-white/10 overflow-x-auto flex items-center justify-center gap-2.5 z-20 scrollbar-none" id="lightboxThumbnailsStrip">
        <?php
        foreach ($thumbnails as $index => $thumb) {
            $isActive = $index === 0 ? 'border-primary ring-2 ring-primary' : 'border-white/20 opacity-60 hover:opacity-100';
            echo "<div class='lightbox-thumb-item w-14 h-14 sm:w-16 sm:h-16 flex-shrink-0 rounded-lg overflow-hidden cursor-pointer border-2 {$isActive} transition-all duration-200 bg-white/5' data-index='{$index}' data-src='{$thumb['path']}'>";
            echo "<img src='{$thumb['path']}' alt='Thumbnail " . ($index + 1) . "' class='w-full h-full object-cover select-none pointer-events-none'>";
            echo "</div>";
        }
        ?>
    </div>
    <?php } ?>
</div>

<script>
    window.PRODUCT_GALLERY_IMAGES = <?php echo json_encode($thumbnails, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
</script>
<script src="<?php echo USER_JS ?>product-gallery.min.js?v=<?php echo time(); ?>"></script>
<?php

require_once 'footer.php';

?>