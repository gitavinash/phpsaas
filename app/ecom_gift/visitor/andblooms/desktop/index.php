<?php

if (SITE_VERSION == 'mobile') {
    header("Location: /category/flower/", true, 301);
    exit;
}

require_once 'header.php';

if ($checkSlider = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "slide WHERE slide_status=1 AND slider_device='" . SITE_VERSION . "' AND slide_site_id=" . SITE_ID . " ORDER BY slide_update DESC")) {

    if (mysqli_num_rows($checkSlider) != 0) {
?>
        <!-- Hero Section -->
        <section
            class="relative h-screen w-full overflow-hidden z-[50] max-h-[380px] md:max-h-[550px] float-left mt-[4.5em] mb-[2em]">
            <div class="slider-container relative w-full h-full transition-transform duration-500" id="heroSlider">
                <?php
                $slideIndex = 0;
                while ($slide = mysqli_fetch_assoc($checkSlider)) {
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
                    <a href="<?php echo $btnUrl ?>" title="<?php echo $title ?>">
                        <div class="slide absolute inset-0 bg-cover bg-center bg-no-repeat <?php echo $slideClass ?>"
                            style="background-image: url('<?php echo $bgImage ?>')">
                            <div class="absolute inset-0 <?php echo $overlay ?>"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="px-6 w-full max-w-7xl <?php echo $textAlignClass ?>">
                                    <?php if ($slide_subtitle): ?>
                                        <p class="text-white text-sm md:text-base font-semibold tracking-wider uppercase mb-3 opacity-90"><?php echo $slide_subtitle ?></p>
                                    <?php endif; ?>
                                    <h1 class="text-4xl md:text-6xl text-white font-medium mb-6 leading-tight">
                                        <?php echo nl2br($title) ?>
                                    </h1>
                                    <p class="text-white text-lg md:text-xl mb-8 max-w-2xl <?php echo $align === 'center' ? 'mx-auto' : '' ?>">
                                        <?php echo nl2br($subtitle) ?>
                                    </p>
                                    <?php if (!empty($btnText) && !empty($btnUrl)): ?>
                                        <div class="flex <?php echo $flexAlignClass ?> gap-4">
                                            <a href="<?php echo $btnUrl ?>"
                                                class="inline-block px-8 py-3 !rounded-button hover:opacity-90 transition-all duration-300 whitespace-nowrap"
                                                style="background-color: <?php echo $cta1_bg ?>; color: <?php echo $cta1_text ?>;">
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
                ?>
            </div>

            <!-- Navigation & Dots -->
            <div class="absolute bottom-8 left-0 right-0 flex justify-center items-center space-x-6">
                <button
                    class="slider-nav-btn prev w-10 h-10 flex items-center justify-center text-white hover:text-primary transition-colors">
                    <i class="ri-arrow-left-s-line text-2xl"></i>
                </button>
                <div class="flex space-x-2">
                    <?php for ($i = 0; $i < $slideIndex; $i++): ?>
                        <button
                            class="slider-dot w-3 h-3 rounded-full bg-white <?php $i === 0 ? 'bg-opacity-100' : 'bg-opacity-50' ?>"
                            data-index="<?php $i ?>"></button>
                    <?php endfor; ?>
                </div>
                <button
                    class="slider-nav-btn next w-10 h-10 flex items-center justify-center text-white hover:text-primary transition-colors">
                    <i class="ri-arrow-right-s-line text-2xl"></i>
                </button>
            </div>
        </section>
<?php
    }
}
?>

<section class="py-20 px-6">
    <div class="container mx-auto">
        <h2 class="serif text-3xl md:text-4xl text-center mb-12">Shop by Category</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="category-card relative overflow-hidden group cursor-pointer">
                <a href="/category/flower/" title="Fresh Flowers">
                    <div class="absolute inset-0 bg-black opacity-0 transition-opacity duration-300 overlay z-10">
                    </div>
                    <img src="/upload/andblooms.com/2024/08/1723026408_untitled_design___2024_08_07t155632.797.jpg"
                        alt="Birthday" class="w-full h-96 object-cover transition-transform duration-500 ease-out">
                    <div
                        class="absolute bottom-0 left-0 right-0 z-20 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-6">
                        <h3 class="serif text-2xl text-white drop-shadow-lg">Fresh Flowers</h3>
                    </div>
                </a>
            </div>
            <a href='/category/planters/' title="Planters">
                <div class="category-card relative overflow-hidden group cursor-pointer">
                    <div class="absolute inset-0 bg-black opacity-0 transition-opacity duration-300 overlay z-10">
                    </div>
                    <img src="/upload/andblooms.com/2024/09/1726295035_untitled_design_3.jpg" alt="Planters"
                        class="w-full h-96 object-cover transition-transform duration-500 ease-out">
                    <div
                        class="absolute bottom-0 left-0 right-0 z-20 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-6">
                        <h3 class="serif text-2xl text-white drop-shadow-lg">Planters</h3>
                    </div>
                </div>
            </a>
            <a href="/category/cake/" title="Cake & Hampers">
                <div class="category-card relative overflow-hidden group cursor-pointer">
                    <!-- Optional hover overlay -->
                    <div
                        class="absolute inset-0 bg-black opacity-0 group-hover:opacity-30 transition-opacity duration-300 z-10">
                    </div>

                    <!-- Image -->
                    <img src="upload/2023/10/1698668342_untitled_design_59.jpg" alt="Cake & Hampers"
                        class="w-full h-96 object-cover transition-transform duration-500 ease-out">

                    <!-- Black gradient behind text -->
                    <div
                        class="absolute bottom-0 left-0 right-0 z-20 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-6">
                        <h3 class="serif text-2xl text-white drop-shadow-lg">Cake & Hampers</h3>
                    </div>
                </div>
            </a>

        </div>
    </div>
</section>

<section class="py-20 px-6">
    <div class="container mx-auto">
        <h2 class="serif text-3xl md:text-4xl text-center mb-12">Shop by Occasion</h2>
        <!-- Option 1: Elegant Cards with Hover Effect -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            <a href="/tag/birthday/" title="Birthday" class="group">
                <div
                    class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100">
                    <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center bg-secondary/30 rounded-full">
                        <i class="ri-cake-3-line text-2xl text-primary"></i>
                    </div>
                    <p class="text-center serif text-gray-800">Birthday</p>
                    <!-- <p class="text-xs text-center text-gray-500 mt-1">From ₹3,999</p> -->
                </div>
            </a>
            <a href="/tag/anniversary/" title="Anniversary" class="group">
                <div
                    class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100">
                    <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center bg-secondary/30 rounded-full">
                        <i class="ri-heart-2-line text-2xl text-primary"></i>
                    </div>
                    <p class="text-center serif text-gray-800">Anniversary</p>
                    <!-- <p class="text-xs text-center text-gray-500 mt-1">From ₹4,999</p> -->
                </div>
            </a>
            <a href="/tag/congratulation/" title="Congratulations" class="group">
                <div
                    class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100">
                    <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center bg-secondary/30 rounded-full">
                        <i class="ri-gift-2-line text-2xl text-primary"></i>
                    </div>
                    <p class="text-center serif text-gray-800">Congratulations</p>
                    <!-- <p class="text-xs text-center text-gray-500 mt-1">From ₹3,499</p> -->
                </div>
            </a>
            <a href="/tag/love/" title="Love & Romance" class="group">
                <div
                    class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100">
                    <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center bg-secondary/30 rounded-full">
                        <i class="ri-hearts-line text-2xl text-primary"></i>
                    </div>
                    <p class="text-center serif text-gray-800">Love & Romance</p>
                    <!-- <p class="text-xs text-center text-gray-500 mt-1">From ₹5,999</p> -->
                </div>
            </a>
            <a href="/tag/get-well/" title="Get Well Soon" class="group">
                <div
                    class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100">
                    <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center bg-secondary/30 rounded-full">
                        <i class="ri-heart-pulse-line text-2xl text-primary"></i>
                    </div>
                    <p class="text-center serif text-gray-800">Get Well Soon</p>
                    <!-- <p class="text-xs text-center text-gray-500 mt-1">From ₹2,999</p> -->
                </div>
            </a>
            <a href="/tag/thank-you/" title="Thank You" class="group">
                <div
                    class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100">
                    <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center bg-secondary/30 rounded-full">
                        <i class="ri-emotion-happy-line text-2xl text-primary"></i>
                    </div>
                    <p class="text-center serif text-gray-800">Thank You</p>
                    <!-- <p class="text-xs text-center text-gray-500 mt-1">From ₹3,299</p> -->
                </div>
            </a>
        </div>
    </div>
</section>
<!-- Father's Day Collection -->
<!-- <section class="py-20 px-6 bg-white">
    <div class="container mx-auto">
        <div class="flex flex-col lg:flex-row items-center gap-12">
            <div class="lg:w-1/2">
                <img src="https://readdy.ai/api/search-image?query=elegant%2520father%2527s%2520day%2520gift%2520arrangement%2520with%2520luxury%2520flowers%2520and%2520premium%2520accessories%252C%2520sophisticated%2520composition%252C%2520warm%2520tones%252C%2520professional%2520photography%252C%2520light%2520background&width=800&height=600&seq=fathers-day&orientation=landscape"
                    alt="Father's Day Collection" class="w-full rounded shadow-lg">
            </div>
            <div class="lg:w-1/2">
                <h2 class="serif text-3xl md:text-4xl mb-6">Father's Day Collection</h2>
                <p class="text-gray-700 mb-6 leading-relaxed">Celebrate the special man
                    in your life with our
                    exclusive Father's Day collection. Featuring sophisticated
                    arrangements and thoughtful gift
                    combinations that perfectly express your gratitude.</p>
                <ul class="mb-8 space-y-4">
                    <li class="flex items-center">
                        <i class="ri-check-line text-primary mr-3"></i>
                        <span>Premium flower selections</span>
                    </li>
                    <li class="flex items-center">
                        <i class="ri-check-line text-primary mr-3"></i>
                        <span>Luxury gift packaging</span>
                    </li>
                    <li class="flex items-center">
                        <i class="ri-check-line text-primary mr-3"></i>
                        <span>Same-day delivery available</span>
                    </li>
                </ul>
                <a href="#"
                    class="inline-block bg-primary text-white px-8 py-3 !rounded-button hover:bg-opacity-90 transition-all duration-300 whitespace-nowrap">Shop
                    Collection</a>
            </div>
        </div>
    </div>
</section> -->
<!-- Best Sellers Section -->
<section class="py-20 px-6 bg-secondary">
    <div class="container mx-auto">
        <h2 class="serif text-3xl md:text-4xl text-center mb-12">Best Sellers</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
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
                }
            }
            ?>
        </div>

        <!-- <div class="text-center mt-12">
            <a href="#"
                class="inline-block border border-primary text-primary px-8 py-3 !rounded-button hover:bg-primary hover:text-white transition-all duration-300 whitespace-nowrap">View
                All</a>
        </div> -->
    </div>
</section>

<?php
// ─── SOCIAL PROOF CAROUSEL ───────────────────────────────────────────────────
if (!function_exists('getLocalVideoUrl')) {
    function getLocalVideoUrl($url)
    {
        if (empty($url)) return '';
        if (strpos($url, '.mp4') !== false || strpos($url, '/upload/') !== false) {
            return $url;
        }
        $id = '';
        if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
            if (preg_match('/(?:shorts\/|v=|be\/|embed\/)([^?&#]+)/', $url, $matches)) {
                $id = $matches[1];
            }
        } elseif (strpos($url, 'instagram.com') !== false) {
            if (preg_match('/(?:reel|reels|p)\/([^\/?#&]+)/', $url, $matches)) {
                $id = $matches[1];
            }
        }
        if (!empty($id)) {
            return '/upload/' . SITE_DOMAIN . '/social_proof/' . $id . '.mp4';
        }
        return $url;
    }
}

$socialProofs = getSocialProofsData($connection, 'social_proof', 8, true);
if (!empty($socialProofs)):
?>
    <!-- Social Proof Section -->
    <style>
    :root {
      --sp-font-serif: "Playfair Display", Georgia, serif;
      --sp-font-sans: "Montserrat", sans-serif;
      --sp-title-size: 1.25rem;
    }
    </style>
    <section class="social-proof-section py-20" id="social-proof">
        <div class="container mx-auto px-6 text-center mb-16">
            <span class="text-sm font-semibold uppercase tracking-widest text-[#D4946D] mb-4 block">Watch Us In Action</span>
            <h2 class="serif text-3xl md:text-5xl text-gray-800 mb-6 font-medium">Real Moments &amp; Fresh Inspirations</h2>
            <p class="max-w-2xl mx-auto text-gray-500 text-lg leading-relaxed">Step inside our studio — behind-the-scenes arrangement stories, customer unboxings, and bloom guides straight from the AndBlooms team.</p>
        </div>

        <div class="carousel-outer">
            <button class="carousel-btn btn-prev" aria-label="Slide left">
            </button>
            <button class="carousel-btn btn-next" aria-label="Slide right">
            </button>

            <div class="carousel-container">
                <div class="carousel-track">
                    <?php foreach ($socialProofs as $proof):
                        $proofTitle       = htmlspecialchars($proof['title'] ?? '');
                        $proofImage       = htmlspecialchars($proof['image'] ?? '');
                        $proofType        = strtolower($proof['sp_type'] ?? 'instagram');
                        $proofUrl         = htmlspecialchars($proof['link_url'] ?? '#');
                        $videoUrl         = htmlspecialchars($proof['sp_video_url'] ?? '');
                        $resolvedVideoUrl = getLocalVideoUrl($videoUrl);
                    ?>
                        <div class="social-card"
                            data-video-url="<?= $resolvedVideoUrl ?>"
                            data-video-type="<?= $proofType ?>"
                            data-title="<?= $proofTitle ?>"
                            data-description="<?= htmlspecialchars($proof['description'] ?? '') ?>"
                            data-ref-type="<?= htmlspecialchars($proof['sp_ref_type'] ?? '') ?>"
                            data-ref-url="<?= htmlspecialchars($proof['ref_url'] ?? '') ?>"
                            data-ref-title="<?= htmlspecialchars($proof['ref_title'] ?? '') ?>"
                            data-ref-image="<?= htmlspecialchars($proof['ref_image'] ?? '') ?>"
                            data-ref-description="<?= htmlspecialchars($proof['ref_description'] ?? '') ?>">

                            <div class="social-video-thumb">
                                <img src="<?= $proofImage ?>" alt="<?= $proofTitle ?>" class="social-video-poster" />
                                <video
                                    data-src="<?= $resolvedVideoUrl ?>"
                                    poster="<?= $proofImage ?>"
                                    class="social-video-img"
                                    loop muted playsinline
                                    style="position:absolute;left:0;top:0;width:100%;height:100%;object-fit:cover;border:none;cursor:pointer;">
                                </video>
                            </div>

                            <div class="social-card-content">
                                <h4 class="social-card-title">
                                    <?= $proofTitle ?>
                                </h4>
                                <p class="social-card-desc"><?= htmlspecialchars($proof['description'] ?? '') ?></p>
                                <?php if (!empty($proof['ref_url'])):
                                    $refName = !empty($proof['ref_title']) ? htmlspecialchars($proof['ref_title']) : 'View Product';
                                ?>
                                    <div class="social-card-ref">
                                        <a href="<?= htmlspecialchars($proof['ref_url']) ?>" class="ref-link">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px;vertical-align:middle;">
                                                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"></path>
                                            </svg>
                                            <span><?= $refName ?></span>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="social-cta-box">
            <a href="https://instagram.com/andblooms" class="inline-block bg-[#D4946D] text-white px-8 py-3 rounded-full hover:bg-[#b87b54] transition-all duration-300 whitespace-nowrap" target="_blank" rel="noopener">
                Follow on Instagram
            </a>
            <a href="https://youtube.com/@andblooms" class="inline-block border border-[#D4946D] text-[#D4946D] px-8 py-3 rounded-full hover:bg-[#D4946D] hover:text-white transition-all duration-300 whitespace-nowrap" target="_blank" rel="noopener">
                Subscribe on YouTube
            </a>
        </div>
    </section>
<?php endif; ?>

<!-- Recent Products -->
<section class="py-20 px-6">
    <div class="container mx-auto">
        <h2 class="serif text-3xl md:text-4xl text-center mb-12">Recently Added</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
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
                }
            }
            ?>
        </div>

    </div>
</section>
<!-- About Preview -->
<section class="py-20 px-6">
    <div class="container mx-auto">
        <div class="flex flex-col md:flex-row items-center">

            <!-- Responsive YouTube Video -->
            <div class="w-full md:w-1/2 mb-10 md:mb-0 md:pr-10">
                <div class="relative w-full" style="padding-top: 56.25%;">
                    <iframe class="absolute top-0 left-0 w-full h-full"
                        src="https://www.youtube.com/embed/9-gPXnnKUS8?si=KSTxplQhkHqjEUho" title="YouTube video player"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                    </iframe>
                </div>
            </div>

            <!-- Text Section -->
            <div class="w-full md:w-1/2 md:pl-10">
                <h2 class="serif text-3xl md:text-4xl mb-6">Our Story</h2>
                <p class="text-gray-700 mb-6 leading-relaxed">
                    Founded in 2018, AndBlooms is a Chattarpur, New Delhi-based floral studio driven by a deep passion
                    for creating unforgettable floral experiences. Every arrangement is thoughtfully designed and
                    handcrafted using the freshest blooms, responsibly sourced from sustainable farms around the world.
                </p>
                <p class="text-gray-700 mb-8 leading-relaxed">
                    Our team of skilled florists combines years of experience with artistic vision to craft elegant,
                    meaningful bouquets that tell a story and leave a lasting impression. At AndBlooms, we believe
                    flowers are more than gifts. They’re emotions, memories, and moments beautifully expressed.
                </p>
                <a href="#"
                    class="inline-block bg-primary text-white px-8 py-3 rounded hover:bg-opacity-90 transition-all duration-300 whitespace-nowrap">
                    Learn More
                </a>
            </div>

        </div>
    </div>
</section>


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