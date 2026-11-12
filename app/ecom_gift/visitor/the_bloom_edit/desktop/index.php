<?php

if (SITE_VERSION == 'mobile') {
    header("Location: /category/flower/", true, 301);
    exit;
}

require_once 'header.php';

$hasRenderedH1 = false;

if ($checkSlider = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "slide WHERE slide_status=1 AND slider_device='" . SITE_VERSION . "' AND slide_site_id=" . SITE_ID . " ORDER BY slide_update DESC")) {
    $totalSlides = mysqli_num_rows($checkSlider);
    if ($totalSlides > 0) {
?>
        <!-- Editorial Hero Carousel -->
        <section class="tbe-hero-section" aria-roledescription="carousel" aria-label="Featured seasonal collections">
            <div class="slider-container relative w-full h-full" id="heroSlider">
                <?php
                $slideIndex = 0;
                while ($slide = mysqli_fetch_assoc($checkSlider)) {
                    $rawImage = (string) ($slide['slide_image'] ?? '');
                    if ($rawImage !== '' && strpos($rawImage, 'http') !== 0 && strpos($rawImage, '/') !== 0) {
                        $rawImage = '/' . $rawImage;
                    }
                    $bgImage = safe_html($rawImage);
                    $title = safe_html((string) ($slide['slide_title'] ?? ''));
                    $subtitle = safe_html((string) ($slide['slide_description'] ?? ''));
                    $btnText = safe_html((string) ($slide['slider_cta'] ?? ''));
                    $btnUrl = safe_html((string) ($slide['slide_link'] ?? ''));
                    $align = isset($slide['slide_alignment']) ? $slide['slide_alignment'] : 'left';
                    $slide_subtitle = !empty($slide['slide_subtitle']) ? safe_html((string) $slide['slide_subtitle']) : '';
                    $cta2Text = !empty($slide['slider_cta2']) ? safe_html((string) $slide['slider_cta2']) : '';
                    $cta2Url = !empty($slide['slide_link2']) ? safe_html((string) $slide['slide_link2']) : '';

                    $textAlignClass = $align === 'center' ? 'text-center mx-auto' : ($align === 'right' ? 'text-right ml-auto' : 'text-left mr-auto');
                    $flexAlignClass = $align === 'center' ? 'justify-center' : ($align === 'right' ? 'justify-end' : 'justify-start');
                    $slideClass = $slideIndex === 0 ? '' : 'translate-x-full';
                    $fetchPriority = $slideIndex === 0 ? 'fetchpriority="high" loading="eager"' : 'loading="lazy"';
                ?>
                    <div class="tbe-hero-slide <?php echo $slideClass; ?>"
                        role="group"
                        aria-roledescription="slide"
                        aria-label="Slide <?php echo $slideIndex + 1; ?> of <?php echo $totalSlides; ?>">
                        <?php if ($bgImage !== ''): ?>
                            <img src="<?php echo $bgImage; ?>"
                                alt="<?php echo $title !== '' ? $title : 'The Bloom Edit Floral Arrangement'; ?>"
                                class="tbe-hero-img"
                                <?php echo $fetchPriority; ?>
                                decoding="async">
                        <?php endif; ?>
                        <div class="tbe-hero-scrim" aria-hidden="true"></div>

                        <div class="relative z-10 h-full max-w-7xl mx-auto px-5 sm:px-10 flex items-center pb-14 sm:pb-0">
                            <div class="w-full max-w-2xl <?php echo $textAlignClass; ?>">
                                <?php if ($slide_subtitle): ?>
                                    <p class="text-xs sm:text-sm font-semibold tracking-[0.22em] uppercase text-[#EFE8DC] mb-3 sm:mb-4">
                                        <?php echo $slide_subtitle; ?>
                                    </p>
                                <?php endif; ?>

                                <?php if ($slideIndex === 0): $hasRenderedH1 = true; ?>
                                    <h1 class="font-serif text-3xl sm:text-5xl md:text-6xl text-white font-medium mb-3 sm:mb-5 leading-[1.12] tracking-tight">
                                        <?php echo nl2br($title); ?>
                                    </h1>
                                <?php else: ?>
                                    <p class="font-serif text-3xl sm:text-5xl md:text-6xl text-white font-medium mb-3 sm:mb-5 leading-[1.12] tracking-tight">
                                        <?php echo nl2br($title); ?>
                                    </p>
                                <?php endif; ?>

                                <?php if ($subtitle !== ''): ?>
                                    <p class="text-[#F4EFE6] text-sm sm:text-lg md:text-xl mb-5 sm:mb-8 max-w-xl leading-relaxed <?php echo $align === 'center' ? 'mx-auto' : ''; ?>">
                                        <?php echo nl2br($subtitle); ?>
                                    </p>
                                <?php endif; ?>

                                <?php if ($btnText !== '' && $btnUrl !== ''): ?>
                                    <div class="flex flex-wrap <?php echo $flexAlignClass; ?> gap-3 sm:gap-4">
                                        <a href="<?php echo $btnUrl; ?>" class="tbe-btn-ivory">
                                            <span><?php echo $btnText; ?></span>
                                            <i class="ri-arrow-right-line" aria-hidden="true"></i>
                                        </a>
                                        <?php if ($cta2Text !== '' && $cta2Url !== ''): ?>
                                            <a href="<?php echo $cta2Url; ?>"
                                                class="tbe-btn-outline border-white/75 text-white hover:bg-white hover:text-[#183028]">
                                                <?php echo $cta2Text; ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php
                    $slideIndex++;
                }
                ?>
            </div>

            <?php if ($slideIndex > 1): ?>
                <!-- Accessible Hero Navigation Controls -->
                <div class="tbe-hero-controls">
                    <button type="button" class="slider-nav-btn prev tbe-hero-ctrl-btn" aria-label="Previous slide">
                        <i class="ri-arrow-left-s-line text-xl" aria-hidden="true"></i>
                    </button>
                    <div class="flex items-center gap-2" role="group" aria-label="Choose slide">
                        <?php for ($i = 0; $i < $slideIndex; $i++): ?>
                            <button type="button"
                                class="tbe-hero-dot <?php echo $i === 0 ? 'is-active' : ''; ?>"
                                data-index="<?php echo $i; ?>"
                                aria-label="Go to slide <?php echo $i + 1; ?>"
                                aria-pressed="<?php echo $i === 0 ? 'true' : 'false'; ?>"></button>
                        <?php endfor; ?>
                    </div>
                    <button type="button" class="slider-nav-btn next tbe-hero-ctrl-btn" aria-label="Next slide">
                        <i class="ri-arrow-right-s-line text-xl" aria-hidden="true"></i>
                    </button>
                    <button type="button" id="heroPauseBtn" class="tbe-hero-ctrl-btn border-l border-white/20 pl-1"
                        aria-label="Pause hero slideshow" aria-pressed="false">
                        <i class="ri-pause-fill" aria-hidden="true"></i>
                    </button>
                </div>
            <?php endif; ?>
        </section>
<?php
    }
}
?>

<?php if (!$hasRenderedH1): ?>
    <section class="tbe-banner py-16">
        <div class="max-w-5xl mx-auto px-6 text-center relative z-10">
            <span class="tbe-eyebrow text-[#EFE8DC] mb-3">Luxury Floral Studio</span>
            <h1 class="font-serif text-4xl md:text-6xl font-medium text-white mb-4">
                Handcrafted Blooms for Moments Worth Remembering
            </h1>
            <p class="text-[#F4EFE6] text-lg max-w-2xl mx-auto mb-8">
                Seasonal floral compositions, artisan hampers, and ceramic planters hand-delivered from our Chhatarpur and Gurugram studios.
            </p>
            <a href="/shop/" class="tbe-btn-ivory">Explore Collection</a>
        </div>
    </section>
<?php endif; ?>

<!-- Atelier Signature Pillars -->
<section class="bg-white border-b border-[#E6DDD3]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-6 grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="flex items-start gap-2.5 sm:gap-3.5">
            <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#EFE8DC] text-[#183028] flex items-center justify-center flex-shrink-0">
                <i class="ri-flower-line text-lg" aria-hidden="true"></i>
            </span>
            <div>
                <p class="font-serif text-sm sm:text-base font-medium text-[#1C1A18]">Farm-Fresh Harvests</p>
                <p class="text-xs text-[#5E5650]">Curated daily from premier growers</p>
            </div>
        </div>
        <div class="flex items-start gap-2.5 sm:gap-3.5">
            <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#EFE8DC] text-[#183028] flex items-center justify-center flex-shrink-0">
                <i class="ri-truck-line text-lg" aria-hidden="true"></i>
            </span>
            <div>
                <p class="font-serif text-sm sm:text-base font-medium text-[#1C1A18]">Same-Day Concierge</p>
                <p class="text-xs text-[#5E5650]">Hand-delivered across Delhi NCR</p>
            </div>
        </div>
        <div class="flex items-start gap-2.5 sm:gap-3.5">
            <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#EFE8DC] text-[#183028] flex items-center justify-center flex-shrink-0">
                <i class="ri-quill-pen-line text-lg" aria-hidden="true"></i>
            </span>
            <div>
                <p class="font-serif text-sm sm:text-base font-medium text-[#1C1A18]">Bespoke Gift Notes</p>
                <p class="text-xs text-[#5E5650]">Stationery printed &amp; sealed by hand</p>
            </div>
        </div>
        <div class="flex items-start gap-2.5 sm:gap-3.5">
            <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#EFE8DC] text-[#183028] flex items-center justify-center flex-shrink-0">
                <i class="ri-award-line text-lg" aria-hidden="true"></i>
            </span>
            <div>
                <p class="font-serif text-sm sm:text-base font-medium text-[#1C1A18]">Established 2018</p>
                <p class="text-xs text-[#5E5650]">Trusted by India&rsquo;s leading luxury houses</p>
            </div>
        </div>
    </div>
</section>

<!-- Shop by Category -->
<section class="py-10 sm:py-16 md:py-24 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-12">
            <span class="tbe-eyebrow mb-2">Curated Departments</span>
            <h2 class="font-serif text-2xl sm:text-3xl md:text-4xl font-medium text-[#1C1A18] mb-3">Shop by Category</h2>
            <p class="text-[#5E5650] text-sm md:text-base">Sculptural arrangements, enduring foliage planters, and indulgent gifting hampers.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-7">
            <article class="group relative overflow-hidden rounded-xl border border-[#E6DDD3] bg-[#EFE8DC] aspect-[4/3] sm:aspect-[4/5] shadow-sm hover:shadow-xl transition-all duration-300">
                <img src="/upload/andblooms.com/2024/08/1723026408_untitled_design___2024_08_07t155632.797.jpg"
                    alt="Fresh Flowers handcrafted bouquets"
                    loading="lazy"
                    decoding="async"
                    class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-t from-[#183028]/85 via-[#183028]/20 to-transparent"></div>
                <div class="absolute inset-x-0 bottom-0 p-5 sm:p-7 text-white">
                    <p class="text-[11px] uppercase tracking-[0.2em] text-[#EFE8DC] mb-1.5">Signature Stem Bar</p>
                    <h3 class="font-serif text-xl sm:text-2xl md:text-3xl font-medium mb-2">
                        <a href="/category/flower/" class="tbe-card-link">Fresh Flowers</a>
                    </h3>
                    <span class="inline-flex items-center gap-2 text-xs uppercase tracking-widest font-semibold text-[#F4EFE6]">
                        Explore Collection <i class="ri-arrow-right-line transition-transform group-hover:translate-x-1" aria-hidden="true"></i>
                    </span>
                </div>
            </article>

            <article class="group relative overflow-hidden rounded-xl border border-[#E6DDD3] bg-[#EFE8DC] aspect-[4/3] sm:aspect-[4/5] shadow-sm hover:shadow-xl transition-all duration-300">
                <img src="/upload/andblooms.com/2024/09/1726295035_untitled_design_3.jpg"
                    alt="Designer Ceramic Planters"
                    loading="lazy"
                    decoding="async"
                    class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-t from-[#183028]/85 via-[#183028]/20 to-transparent"></div>
                <div class="absolute inset-x-0 bottom-0 p-5 sm:p-7 text-white">
                    <p class="text-[11px] uppercase tracking-[0.2em] text-[#EFE8DC] mb-1.5">Living Sculptures</p>
                    <h3 class="font-serif text-xl sm:text-2xl md:text-3xl font-medium mb-2">
                        <a href="/category/planters/" class="tbe-card-link">Planters</a>
                    </h3>
                    <span class="inline-flex items-center gap-2 text-xs uppercase tracking-widest font-semibold text-[#F4EFE6]">
                        Explore Collection <i class="ri-arrow-right-line transition-transform group-hover:translate-x-1" aria-hidden="true"></i>
                    </span>
                </div>
            </article>

            <article class="group relative overflow-hidden rounded-xl border border-[#E6DDD3] bg-[#EFE8DC] aspect-[4/3] sm:aspect-[4/5] shadow-sm hover:shadow-xl transition-all duration-300">
                <img src="/upload/2023/10/1698668342_untitled_design_59.jpg"
                    alt="Luxury Cakes and Gift Hampers"
                    loading="lazy"
                    decoding="async"
                    class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-t from-[#183028]/85 via-[#183028]/20 to-transparent"></div>
                <div class="absolute inset-x-0 bottom-0 p-5 sm:p-7 text-white">
                    <p class="text-[11px] uppercase tracking-[0.2em] text-[#EFE8DC] mb-1.5">Artisanal Treats</p>
                    <h3 class="font-serif text-xl sm:text-2xl md:text-3xl font-medium mb-2">
                        <a href="/category/cake/" class="tbe-card-link">Cake &amp; Hampers</a>
                    </h3>
                    <span class="inline-flex items-center gap-2 text-xs uppercase tracking-widest font-semibold text-[#F4EFE6]">
                        Explore Collection <i class="ri-arrow-right-line transition-transform group-hover:translate-x-1" aria-hidden="true"></i>
                    </span>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- Shop by Occasion -->
<section class="py-10 sm:py-16 md:py-20 px-4 sm:px-6 lg:px-8 bg-[#EFE8DC]/55 border-y border-[#E6DDD3]">
    <div class="max-w-7xl mx-auto">
        <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-12">
            <span class="tbe-eyebrow mb-2">Thoughtful Gifting</span>
            <h2 class="font-serif text-2xl sm:text-3xl md:text-4xl font-medium text-[#1C1A18] mb-3">Shop by Occasion</h2>
            <p class="text-[#5E5650] text-sm md:text-base">Select a sentiment and discover arrangements crafted to speak without words.</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5 sm:gap-5">
            <a href="/tag/birthday/"
                class="group bg-white rounded-xl p-4 sm:p-6 border border-[#E6DDD3] hover:border-[#183028] hover:-translate-y-1 transition-all duration-300 text-center">
                <div class="w-12 h-12 sm:w-14 sm:h-14 mx-auto mb-3 sm:mb-4 flex items-center justify-center bg-[#F4E9E4] group-hover:bg-[#183028] rounded-full transition-colors">
                    <i class="ri-cake-3-line text-xl sm:text-2xl text-[#9B5637] group-hover:text-white transition-colors" aria-hidden="true"></i>
                </div>
                <p class="font-serif text-sm sm:text-base font-medium text-[#1C1A18]">Birthday</p>
                <span class="text-xs text-[#5E5650] mt-1 block">Celebratory Edit</span>
            </a>

            <a href="/tag/anniversary/"
                class="group bg-white rounded-xl p-4 sm:p-6 border border-[#E6DDD3] hover:border-[#183028] hover:-translate-y-1 transition-all duration-300 text-center">
                <div class="w-12 h-12 sm:w-14 sm:h-14 mx-auto mb-3 sm:mb-4 flex items-center justify-center bg-[#F4E9E4] group-hover:bg-[#183028] rounded-full transition-colors">
                    <i class="ri-heart-2-line text-xl sm:text-2xl text-[#9B5637] group-hover:text-white transition-colors" aria-hidden="true"></i>
                </div>
                <p class="font-serif text-sm sm:text-base font-medium text-[#1C1A18]">Anniversary</p>
                <span class="text-xs text-[#5E5650] mt-1 block">Timeless Gestures</span>
            </a>

            <a href="/tag/congratulation/"
                class="group bg-white rounded-xl p-4 sm:p-6 border border-[#E6DDD3] hover:border-[#183028] hover:-translate-y-1 transition-all duration-300 text-center">
                <div class="w-12 h-12 sm:w-14 sm:h-14 mx-auto mb-3 sm:mb-4 flex items-center justify-center bg-[#F4E9E4] group-hover:bg-[#183028] rounded-full transition-colors">
                    <i class="ri-gift-2-line text-xl sm:text-2xl text-[#9B5637] group-hover:text-white transition-colors" aria-hidden="true"></i>
                </div>
                <p class="font-serif text-sm sm:text-base font-medium text-[#1C1A18]">Congratulations</p>
                <span class="text-xs text-[#5E5650] mt-1 block">Milestone Flowers</span>
            </a>

            <a href="/tag/love/"
                class="group bg-white rounded-xl p-4 sm:p-6 border border-[#E6DDD3] hover:border-[#183028] hover:-translate-y-1 transition-all duration-300 text-center">
                <div class="w-12 h-12 sm:w-14 sm:h-14 mx-auto mb-3 sm:mb-4 flex items-center justify-center bg-[#F4E9E4] group-hover:bg-[#183028] rounded-full transition-colors">
                    <i class="ri-hearts-line text-xl sm:text-2xl text-[#9B5637] group-hover:text-white transition-colors" aria-hidden="true"></i>
                </div>
                <p class="font-serif text-sm sm:text-base font-medium text-[#1C1A18]">Love &amp; Romance</p>
                <span class="text-xs text-[#5E5650] mt-1 block">Passionate Stems</span>
            </a>

            <a href="/tag/get-well/"
                class="group bg-white rounded-xl p-4 sm:p-6 border border-[#E6DDD3] hover:border-[#183028] hover:-translate-y-1 transition-all duration-300 text-center">
                <div class="w-12 h-12 sm:w-14 sm:h-14 mx-auto mb-3 sm:mb-4 flex items-center justify-center bg-[#F4E9E4] group-hover:bg-[#183028] rounded-full transition-colors">
                    <i class="ri-heart-pulse-line text-xl sm:text-2xl text-[#9B5637] group-hover:text-white transition-colors" aria-hidden="true"></i>
                </div>
                <p class="font-serif text-sm sm:text-base font-medium text-[#1C1A18]">Get Well Soon</p>
                <span class="text-xs text-[#5E5650] mt-1 block">Soothing Botanicals</span>
            </a>

            <a href="/tag/thank-you/"
                class="group bg-white rounded-xl p-4 sm:p-6 border border-[#E6DDD3] hover:border-[#183028] hover:-translate-y-1 transition-all duration-300 text-center">
                <div class="w-12 h-12 sm:w-14 sm:h-14 mx-auto mb-3 sm:mb-4 flex items-center justify-center bg-[#F4E9E4] group-hover:bg-[#183028] rounded-full transition-colors">
                    <i class="ri-emotion-happy-line text-xl sm:text-2xl text-[#9B5637] group-hover:text-white transition-colors" aria-hidden="true"></i>
                </div>
                <p class="font-serif text-sm sm:text-base font-medium text-[#1C1A18]">Thank You</p>
                <span class="text-xs text-[#5E5650] mt-1 block">Graceful Gratitude</span>
            </a>
        </div>
    </div>
</section>

<!-- Best Sellers Section -->
<section class="py-10 sm:py-16 md:py-24 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 sm:mb-12">
            <div>
                <span class="tbe-eyebrow mb-2">Most Coveted</span>
                <h2 class="font-serif text-2xl sm:text-3xl md:text-4xl font-medium text-[#1C1A18]">Best Sellers</h2>
            </div>
            <a href="/shop/" class="inline-flex items-center gap-2 text-xs uppercase tracking-widest font-semibold text-[#183028] hover:text-[#9B5637] transition-colors">
                <span>View Full Collection</span>
                <i class="ri-arrow-right-line" aria-hidden="true"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-6 lg:gap-7">
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
                        tbe_render_product_card($bestSeller, [
                            'badge_label' => 'Best Seller',
                        ]);
                    }
                }
            }
            ?>
        </div>
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
    <section class="social-proof-section py-12 sm:py-20 border-y border-[#E6DDD3]" id="social-proof">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center mb-8 sm:mb-14">
            <span class="tbe-eyebrow mb-3">Watch Us In Action</span>
            <h2 class="font-serif text-3xl md:text-5xl text-[#1C1A18] mb-5 font-medium">Real Moments &amp; Fresh Inspirations</h2>
            <p class="max-w-2xl mx-auto text-[#5E5650] text-base md:text-lg leading-relaxed">
                Step inside our Chhatarpur and Gurugram studios — behind-the-scenes composition stories, patron unboxings, and botanical notes from the AndBlooms team.
            </p>
        </div>

        <div class="carousel-outer">
            <button type="button" class="carousel-btn btn-prev" aria-label="Slide left"></button>
            <button type="button" class="carousel-btn btn-next" aria-label="Slide right"></button>

            <div class="carousel-container">
                <div class="carousel-track">
                    <?php foreach ($socialProofs as $proof):
                        $proofTitle       = safe_html((string) ($proof['title'] ?? ''));
                        $proofImage       = safe_html((string) ($proof['image'] ?? ''));
                        $proofType        = strtolower((string) ($proof['sp_type'] ?? 'instagram'));
                        $videoUrl         = (string) ($proof['sp_video_url'] ?? '');
                        $resolvedVideoUrl = safe_html(getLocalVideoUrl($videoUrl));
                    ?>
                        <div class="social-card"
                            data-video-url="<?php echo $resolvedVideoUrl; ?>"
                            data-video-type="<?php echo safe_html($proofType); ?>"
                            data-title="<?php echo $proofTitle; ?>"
                            data-description="<?php echo safe_html((string) ($proof['description'] ?? '')); ?>"
                            data-ref-type="<?php echo safe_html((string) ($proof['sp_ref_type'] ?? '')); ?>"
                            data-ref-url="<?php echo safe_html((string) ($proof['ref_url'] ?? '')); ?>"
                            data-ref-title="<?php echo safe_html((string) ($proof['ref_title'] ?? '')); ?>"
                            data-ref-image="<?php echo safe_html((string) ($proof['ref_image'] ?? '')); ?>"
                            data-ref-description="<?php echo safe_html((string) ($proof['ref_description'] ?? '')); ?>">

                            <div class="social-video-thumb">
                                <img src="<?php echo $proofImage; ?>" alt="<?php echo $proofTitle; ?>" class="social-video-poster" loading="lazy" />
                                <video
                                    data-src="<?php echo $resolvedVideoUrl; ?>"
                                    poster="<?php echo $proofImage; ?>"
                                    class="social-video-img absolute inset-0 w-full h-full object-cover border-0 cursor-pointer"
                                    loop muted playsinline>
                                </video>
                            </div>

                            <div class="social-card-content">
                                <h3 class="social-card-title">
                                    <?php echo $proofTitle; ?>
                                </h3>
                                <p class="social-card-desc"><?php echo safe_html((string) ($proof['description'] ?? '')); ?></p>
                                <?php if (!empty($proof['ref_url'])):
                                    $refName = !empty($proof['ref_title']) ? safe_html((string) $proof['ref_title']) : 'View Product';
                                ?>
                                    <div class="social-card-ref">
                                        <a href="<?php echo safe_html((string) $proof['ref_url']); ?>" class="ref-link">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5 inline align-middle" aria-hidden="true">
                                                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"></path>
                                            </svg>
                                            <span><?php echo $refName; ?></span>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="social-cta-box mt-8">
            <a href="https://instagram.com/andblooms" class="tbe-btn-forest" target="_blank" rel="noopener noreferrer">
                <i class="ri-instagram-line" aria-hidden="true"></i>
                <span>Follow on Instagram</span>
            </a>
            <a href="https://youtube.com/@andblooms" class="tbe-btn-outline" target="_blank" rel="noopener noreferrer">
                <i class="ri-youtube-line" aria-hidden="true"></i>
                <span>Subscribe on YouTube</span>
            </a>
        </div>
    </section>
<?php endif; ?>

<!-- Recent Products -->
<section class="py-10 sm:py-16 md:py-24 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 sm:mb-12">
            <div>
                <span class="tbe-eyebrow mb-2">Freshly Composed</span>
                <h2 class="font-serif text-2xl sm:text-3xl md:text-4xl font-medium text-[#1C1A18]">Recently Added</h2>
            </div>
            <a href="/shop/" class="inline-flex items-center gap-2 text-xs uppercase tracking-widest font-semibold text-[#183028] hover:text-[#9B5637] transition-colors">
                <span>Browse All Arrivals</span>
                <i class="ri-arrow-right-line" aria-hidden="true"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-6 lg:gap-7">
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
                        tbe_render_product_card($recent, [
                            'badge_label' => 'New Arrival',
                        ]);
                    }
                }
            }
            ?>
        </div>
    </div>
</section>

<!-- Atelier Story & Film Facade -->
<section class="py-10 sm:py-16 md:py-24 px-4 sm:px-6 lg:px-8 bg-white border-t border-[#E6DDD3]">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-7 sm:gap-10 lg:gap-14 items-center">
            <!-- Click-to-Play Video Facade -->
            <div class="lg:col-span-6">
                <button type="button"
                    class="tbe-yt-facade group"
                    data-yt-facade
                    data-yt-id="9-gPXnnKUS8"
                    data-yt-title="Inside the AndBlooms Floral Studio"
                    aria-label="Play AndBlooms Atelier Film">
                    <img src="<?php echo USER_IMAGES; ?>andblooms-store.jpg"
                        alt="Inside the AndBlooms floral design atelier"
                        loading="lazy"
                        decoding="async"
                        class="tbe-yt-poster">
                    <span class="tbe-yt-play-btn" aria-hidden="true">
                        <i class="ri-play-fill"></i>
                    </span>
                </button>
                <p class="text-xs text-[#5E5650] mt-3">
                    Atelier Film: Inside our Chhatarpur flagship studio &amp; floral craftsmanship.
                </p>
            </div>

            <!-- Story Narrative -->
            <div class="lg:col-span-6 space-y-6">
                <span class="tbe-eyebrow">Heritage &amp; Philosophy</span>
                <h2 class="font-serif text-3xl md:text-4xl font-medium text-[#1C1A18] leading-tight">
                    Designed as Botanical Poetry, Handcrafted in New Delhi
                </h2>
                <p class="text-[#5E5650] leading-relaxed">
                    Founded in 2018 by Shalini and Ayush Dugar, &amp;Blooms (The Bloom Edit) is a Chhatarpur, New Delhi-based floral studio driven by a quiet reverence for seasonal blooms, sculptural proportions, and tactile presentation.
                </p>
                <p class="text-[#5E5650] leading-relaxed">
                    Every arrangement is composed to order using sustainably cultivated stems sourced from premier farms worldwide. To us, flowers are never mere gestures — they are living expressions of celebration, gratitude, and presence.
                </p>
                <div class="pt-2 flex flex-wrap items-center gap-4">
                    <a href="/about-us/" class="tbe-btn-forest">
                        <span>Discover Our Story</span>
                        <i class="ri-arrow-right-line" aria-hidden="true"></i>
                    </a>
                    <a href="/events/" class="tbe-btn-outline">
                        Explore Workshops
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Client Logos Slider Section -->
<section class="client-logo-section py-10 sm:py-16 px-4 sm:px-6 lg:px-8 border-t border-[#E6DDD3]">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6 sm:mb-10 text-center md:text-left">
            <div>
                <span class="tbe-eyebrow mb-2">Corporate &amp; Event Partners</span>
                <h2 class="font-serif text-2xl md:text-4xl text-[#1C1A18] font-medium mb-2">Trusted by Leading Luxury Houses</h2>
                <p class="text-[#5E5650] text-sm md:text-base max-w-2xl">
                    Honored to craft bespoke floral installations and gifting programs for premier fashion maisons, hospitality leaders, and cultural hosts across India.
                </p>
            </div>
            <div class="flex justify-center md:justify-end">
                <button type="button" id="marqueePauseBtn"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-[#D4C8BA] bg-white text-xs font-semibold uppercase tracking-widest text-[#1C1A18] hover:border-[#183028] transition-colors"
                    aria-pressed="false"
                    aria-label="Pause brand partners ticker">
                    <i class="ri-pause-line" aria-hidden="true"></i>
                    <span>Pause</span>
                </button>
            </div>
        </div>
    </div>

    <div class="client-marquee-container">
        <div class="client-marquee-track">
            <?php
            $clientLogos = [
                ['name' => 'Versace', 'img' => USER_IMAGES . 'versace-logo.jpg'],
                ['name' => 'COS', 'img' => USER_IMAGES . 'cos-logo.jpg'],
                ['name' => 'Ethos Watches', 'img' => USER_IMAGES . 'ethos-logo.svg'],
                ['name' => 'Salvatore Ferragamo', 'img' => USER_IMAGES . 'salvatore-ferragamo-logo.png'],
                ['name' => 'Jimmy Choo', 'img' => USER_IMAGES . 'jimmy-choo-logo.png'],
            ];

            $loopLogos = array_merge($clientLogos, $clientLogos, $clientLogos);

            foreach ($loopLogos as $logo):
            ?>
                <div class="client-logo-item" title="<?php echo safe_html($logo['name']); ?>">
                    <img src="<?php echo safe_html($logo['img']); ?>" alt="<?php echo safe_html($logo['name']); ?> Logo" loading="lazy" />
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
require_once 'footer.php';
?>
