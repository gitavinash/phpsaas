<?php

require_once DATA_FUNCTIONS;
require_once 'header.php';

$sliderList = getSlider($connection);

if (count($sliderList) != 0) {
    echo "<section
    class='relative h-screen w-full overflow-hidden z-[50] max-h-[380px] md:max-h-[550px] float-left mt-[4.5em] mb-[2em]'>
    <div class='slider-container relative w-full h-full transition-transform duration-500' id='heroSlider'>";
    $slideIndex = 0;
    foreach ($sliderList as $slide) {
        $bgImage = htmlspecialchars($slide['slide_image']);
        $title = htmlspecialchars($slide['slide_title']);
        $subtitle = htmlspecialchars($slide['slide_description']);
        $btnText = htmlspecialchars($slide['slider_cta']);
        $btnUrl = htmlspecialchars($slide['slide_link']);

        // New properties
        $align = isset($slide['slide_alignment']) ? $slide['slide_alignment'] : 'center';
        $overlay = isset($slide['slide_overlay']) && $slide['slide_overlay'] == 1 ? 'bg-black/50' : '';
        $cta1_bg = !empty($slide['cta1_bg_color']) ? $slide['cta1_bg_color'] : '#F5D154';
        $cta1_text = !empty($slide['cta1_text_color']) ? $slide['cta1_text_color'] : '#1E293B';
        $cta2_style = isset($slide['cta2_style']) ? $slide['cta2_style'] : 'outline';
        $slide_subtitle = !empty($slide['slide_subtitle']) ? htmlspecialchars($slide['slide_subtitle']) : '';
        
        $cta2_bg = !empty($slide['cta2_bg_color']) ? $slide['cta2_bg_color'] : '#FFFFFF';
        $cta2_text = !empty($slide['cta2_text_color']) ? $slide['cta2_text_color'] : '#000000';
        $cta2_inline_style = $cta2_style === 'outline' 
            ? "border: 2px solid {$cta2_text}; color: {$cta2_text}; background-color: transparent;" 
            : "background-color: {$cta2_bg}; color: {$cta2_text}; border: 2px solid {$cta2_bg};";

        $textAlignClass = $align === 'center' ? 'text-center mx-auto' : ($align === 'right' ? 'text-right ml-auto' : 'text-left mr-auto');
        $flexAlignClass = $align === 'center' ? 'justify-center' : ($align === 'right' ? 'justify-end' : 'justify-start');

        // Assign slide class
        $slideClass = $slideIndex === 0 ? '' : 'translate-x-full';
?>
<a href='<?php echo $btnUrl ?>' title='<?php echo $title ?>'>
    <div class='slide absolute inset-0 bg-cover bg-center bg-no-repeat <?php echo $slideClass ?>'
        style='background-image: url(<?php echo $bgImage ?>)'>
        <div class='absolute inset-0 <?php echo $overlay ?>'></div>
        <div class='absolute inset-0 flex items-center justify-center'>
            <div class='px-6 w-full max-w-7xl <?php echo $textAlignClass ?>'>
                <?php if ($slide_subtitle): ?>
                    <p class="text-white text-sm md:text-base font-semibold tracking-wider uppercase mb-3 opacity-90"><?php echo $slide_subtitle ?></p>
                <?php endif; ?>
                <h1 class='text-4xl md:text-6xl text-white font-medium mb-6 leading-tight'>
                    <?php echo nl2br($title) ?>
                </h1>
                <p class='text-white text-lg md:text-xl mb-8 max-w-2xl <?php echo $align === 'center' ? 'mx-auto' : '' ?>'>
                    <?php echo nl2br($subtitle) ?>
                </p>
                <?php if (!empty($btnText) && !empty($btnUrl)): ?>
                <div class="flex <?php echo $flexAlignClass ?> gap-4">
                    <a href='<?php echo $btnUrl ?>'
                        class='inline-block px-8 py-3 !rounded-button hover:opacity-90 transition-all duration-300 whitespace-nowrap'
                        style='background-color: <?php echo $cta1_bg ?>; color: <?php echo $cta1_text ?>;'>
                        <?php echo $btnText ?>
                    </a>
                    <?php if (!empty($slide['slider_cta2']) && !empty($slide['slide_link2'])): ?>
                    <a href="<?php echo htmlspecialchars($slide['slide_link2']) ?>"
                        class="inline-block px-8 py-3 !rounded-button hover:opacity-90 transition-all duration-300 whitespace-nowrap"
                        style="<?php echo $cta2_inline_style ?>">
                        <?php echo htmlspecialchars($slide['slider_cta2']) ?>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</a>
<?php

        $slideIndex++;
    }
    echo "<div class='absolute bottom-8 left-0 right-0 flex justify-center items-center space-x-6'>
        <button
            class='slider-nav-btn prev w-10 h-10 flex items-center justify-center text-white hover:text-primary transition-colors'>
            <i class='ri-arrow-left-s-line text-2xl'></i>
        </button>
        <div class='flex space-x-2'>";
    for ($i = 0; $i < $slideIndex; $i++): ?>
<button class='slider-dot w-3 h-3 rounded-full bg-white <?php $i === 0 ? 'bg-opacity-100' : 'bg-opacity-50' ?>'
    data-index='<?php $i ?>'></button>
<?php endfor;
    echo "</div>
<button
    class='slider-nav-btn next w-10 h-10 flex items-center justify-center text-white hover:text-primary transition-colors'>
    <i class='ri-arrow-right-s-line text-2xl'></i>
</button>
</div>
</section>";
}

if ($featureCategory = mysqli_query($connection, "SELECT link_url, category_title, category_image FROM " . TABLE_PREFIX
    . "link INNER JOIN " . TABLE_PREFIX . "category ON link_relation_id=category_id AND category_type='product' AND
category_site_id=" . SITE_ID . " WHERE link_status=200 AND link_type='category' AND link_is_featured=1 AND
link_site_id=" . SITE_ID . " ORDER BY RAND() LIMIT 0, 10")) {
    if (mysqli_num_rows($featureCategory) != 0) {
        echo '<section class="py-20 px-6">
    <div class="container mx-auto">
        <h2 class="serif text-3xl md:text-4xl text-center mb-12">Shop by Category</h2>';
        echo "<div class='grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6'>";
        while ($categoryData = mysqli_fetch_assoc($featureCategory)) {
            extract($categoryData);
            echo "
            <a href='{$link_url}' title='{$category_title}' class='group block'>
                <div
                    class='relative h-40 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100'>

                    <!-- Background Image with Zoom -->
                    <div class='absolute inset-0 bg-cover bg-center transform transition-transform duration-700 group-hover:scale-110'
                        style='background-image: url({$category_image});'></div>

                    <!-- Black Overlay -->
                    <div class='absolute inset-0 bg-black/40 group-hover:bg-black/50 transition-all duration-300'></div>

                    <!-- Text -->
                    <div class='relative flex items-center justify-center h-full p-4'>
                        <p class='text-center text-white font-medium text-lg'>{$category_title}</p>
                    </div>
                </div>
            </a>
            ";
        }
        echo "
        </div>
</section>
</div>";
    }
}

?>

<?php
if ($bestSellerProducts = mysqli_query($connection, "
    SELECT 
        link_url, 
        product_id, 
        product_title, 
        product_image, 
        inventory_price, 
        inventory_mrp,
        order_product_id,
        total_orders
    FROM (
        SELECT order_product_id, COUNT(*) AS total_orders
        FROM " . TABLE_PREFIX . "order
        INNER JOIN " . TABLE_PREFIX . "inventory 
            ON inventory_relation_id = order_product_id 
            AND inventory_site_id = " . SITE_ID . "
            AND inventory_price > 0
        WHERE order_site_id = " . SITE_ID . "
          AND order_product_id != ''
        GROUP BY order_product_id
        ORDER BY total_orders DESC
        LIMIT 4
    ) AS o
    INNER JOIN " . TABLE_PREFIX . "product ON o.order_product_id = product_id
    INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id = product_id
    INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id = product_id 
        AND inventory_site_id = " . SITE_ID . "
    WHERE product_status = 1
      AND link_status = 200
      AND link_type = 'product'
      AND link_site_id = " . SITE_ID . "
      AND inventory_price > 0
")) {
    if (mysqli_num_rows($bestSellerProducts) != 0) {
        echo '<section class="py-20 px-6 bg-secondary">
    <div class="container mx-auto">
        <h2 class="serif text-3xl md:text-4xl text-center mb-12">Best Sellers</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">';
        while ($bestSeller = mysqli_fetch_assoc($bestSellerProducts)) {
            extract($bestSeller);

            // Discount calculation
            $showDiscount = ($inventory_mrp > $inventory_price && $inventory_mrp != 0);
            $discount = $showDiscount ? round((($inventory_mrp - $inventory_price) / $inventory_mrp) * 100) : 0;

            echo "<div class='product-card bg-white rounded shadow-sm overflow-hidden group'>
                <a href='{$link_url}' title='{$product_title}'>
                        <div class='relative overflow-hidden'>
                    <img src='{$product_image}' alt='{$product_title}' class='w-full h-72 object-cover transition-transform duration-500 ease-out group-hover:scale-105'>

                    <div class='add-to-cart absolute bottom-0 left-0 right-0 bg-primary bg-opacity-20 p-4 translate-y-full group-hover:translate-y-0 transition duration-300'>
                        <button data-info='{$product_id}_{$inventory_price}_1'
                            class='w-full quickAddToCart py-2 bg-white text-primary rounded-button font-medium hover:bg-primary hover:text-white transition flex items-center justify-center whitespace-nowrap'>
                            <i class='ri-shopping-cart-line mr-2'></i>
                            Add to cart
                        </button>
                    </div>
                </div>
                <div class='p-4'>
                    <h3 class='serif text-lg font-medium mb-2 line-clamp-2'>{$product_title}</h3>
                    <div class='text-primary font-semibold text-base flex items-center gap-2'>";
            echo "₹" . number_format($inventory_price);
            if ($showDiscount) {
                echo "<span class='text-sm text-gray-400 line-through'>₹" . number_format($inventory_mrp) . "</span>";
                echo "<span class='text-green-600 text-sm font-medium'>({$discount}% OFF)</span>";
            }
            echo "</div>
                </div>
                </a>
            </div>";
        }
        echo '
</div>

<!-- <div class="text-center mt-12">
            <a href="#"
                class="inline-block border border-primary text-primary px-8 py-3 !rounded-button hover:bg-primary hover:text-white transition-all duration-300 whitespace-nowrap">View
                All</a>
        </div> -->
</div>
</section>';
    }
}
?>


<?php
if ($recentProducts = mysqli_query($connection, "
    SELECT 
        link_url, 
        product_id, 
        product_title, 
        product_image, 
        inventory_price, 
        inventory_mrp
    FROM " . TABLE_PREFIX . "product
    INNER JOIN " . TABLE_PREFIX . "link 
        ON link_relation_id = product_id
        AND link_status = 200
        AND link_type = 'product'
        AND link_site_id = " . SITE_ID . "
    INNER JOIN " . TABLE_PREFIX . "inventory 
        ON inventory_relation_id = product_id 
        AND inventory_site_id = " . SITE_ID . "
        AND inventory_price > 0
    WHERE product_status = 1
    GROUP BY product_id
    ORDER BY product_time DESC
    LIMIT 4")) {
    if (mysqli_num_rows($recentProducts) != 0) {
        echo '<!-- Recent Products -->
<section class="py-20 px-6">
    <div class="container mx-auto">
        <h2 class="serif text-3xl md:text-4xl text-center mb-12">Recently Added</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">';
        while ($recent = mysqli_fetch_assoc($recentProducts)) {
            extract($recent);

            // Discount logic
            $showDiscount = ($inventory_mrp > $inventory_price && $inventory_mrp != 0);
            $discount = $showDiscount ? round((($inventory_mrp - $inventory_price) / $inventory_mrp) * 100) : 0;

            echo "
            <div class='product-card bg-white rounded shadow-sm overflow-hidden group'>
                <div class='relative overflow-hidden'>
                    <a href='{$link_url}' target='_blank'>
                        <img src='{$product_image}' alt='{$product_title}' class='w-full h-72 object-cover transition-transform duration-500 ease-out group-hover:scale-105'>
                    </a>
                    <div class='absolute bottom-0 left-0 right-0 bg-primary bg-opacity-20 p-4 translate-y-full group-hover:translate-y-0 transition duration-300'>
                        <button data-info='{$product_id}_{$inventory_price}_1'
                            class='w-full quickAddToCart py-2 bg-white text-primary rounded-button font-medium hover:bg-primary hover:text-white transition flex items-center justify-center whitespace-nowrap'>
                            <i class='ri-shopping-cart-line mr-2'></i> Add to cart
                        </button>
                    </div>
                </div>
                <div class='p-4'>
                    <h3 class='serif text-lg font-medium mb-2 line-clamp-2'>{$product_title}</h3>
                    <div class='text-primary font-semibold text-base flex items-center gap-2'>
                        ₹" . number_format($inventory_price);
            if ($showDiscount) {
                echo "<span class='text-sm text-gray-400 line-through'>₹" . number_format($inventory_mrp) . "</span>";
                echo "<span class='text-green-600 text-sm font-medium'>({$discount}% OFF)</span>";
            }
            echo "</div>
                </div>
            </div>";
        }
        echo '
</div>

</div>
</section>';
    }
}
?>
<!-- About Preview -->
<section class="py-20 px-6">
    <div class="container mx-auto">
        <?php
        $homepageContent = getHomepageContent($connection);
        if ($homepageContent) {
            echo $homepageContent['homepageContent'];
        }
        ?>
    </div>
</section>


<?php
$testimonialsList = testimonialsList($connection, 10);
if (!empty($testimonialsList)) {
    echo "<section class='py-20 px-6'>
    <div class='container mx-auto'>";
    echo '<h2 class="serif text-3xl md:text-4xl mb-10 text-center">What Our Customers Say</h2>';
    echo "<div class='relative'>
  <!-- Viewport -->
  <div class='overflow-hidden'>
    <!-- Track -->
    <div id='tTrack' class='flex transition-transform duration-500 ease-out'>";
    foreach ($testimonialsList as $testimonials) {
        echo "<div class='t-slide px-3'>
  <div class='h-full bg-white border border-gray-100 rounded-xl shadow-sm p-6 flex flex-col'>
    <div class='flex items-center gap-4 mb-4'>
      <img src='" . (($testimonials['category_image'] != '') ? $testimonials['category_image'] : IMG_LIBRARY . 'default-profile.png') . "'
alt='{$testimonials['category_title']} photo'
class='w-12 h-12 rounded-full object-cover border'>
<div>
    <div class='font-semibold text-gray-900'>{$testimonials['category_title']}</div>
<div class='text-xs text-gray-400'>Verified Customer</div>
</div>
</div>
<div style='text-align: justify' >" . $testimonials['category_description'] . "</div>
</div>
</div>";
    }
    echo "</div>
</div>
</div>";
    echo "
<div id='tDots' class='flex justify-center gap-2 mt-6'></div>
<button id='tPrev'
    class='absolute -left-3 md:-left-6 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center rounded-full bg-white border hover:border-primary hover:text-primary shadow'>
    <i class='ri-arrow-left-s-line text-xl'></i>
</button>
<button id='tNext'
    class='absolute -right-3 md:-right-6 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center rounded-full bg-white border hover:border-primary hover:text-primary shadow'>
    <i class='ri-arrow-right-s-line text-xl'></i>
</button>";
    echo "</div>
</section>";
}
?>


<!-- Newsletter Section -->
<!-- <section class="py-20 newsletter-container">
    <div class="container mx-auto px-6">
        <div class="max-w-xl mx-auto text-center">
            <h2 class="serif text-3xl md:text-4xl mb-4">Join Our Mailing List</h2>
            <p class="text-gray-700 mb-8">Subscribe to receive updates on new
                collections, special offers, and
                floral inspiration.</p>
            <div class="flex flex-col sm:flex-row gap-4">
                <input type="email" placeholder="Your email address"
                    class="flex-grow px-4 py-3 border border-gray-300 !rounded-button focus:border-primary">
                <button
                    class="bg-primary text-white px-6 py-3 !rounded-button hover:bg-opacity-90 transition-all duration-300 whitespace-nowrap">Subscribe</button>
            </div>
            <label class="custom-checkbox flex items-center justify-center mt-6 text-sm text-gray-600">
                <input type="checkbox">
                <span class="checkmark mr-2"></span>
                I agree to receive marketing emails from AndBlooms
            </label>
        </div>
    </div>
</section> -->
<?php

require_once 'footer.php';

?>