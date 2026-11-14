<?php

require_once 'header.php';

?>

<!-- Fiore-Inspired Editorial Asymmetrical Hero -->
<section class="vl-hero">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-center">
            <!-- Left Editorial Text -->
            <div class="lg:col-span-6 space-y-6 text-center lg:text-left">
                <span class="vl-badge vl-badge-terracotta">
                    <i class="ri-sparkling-line" aria-hidden="true"></i>
                    Haute Botanical Studio • Delhi NCR
                </span>
                
                <h1 class="font-serif text-4xl sm:text-5xl lg:text-6xl text-[#242220] leading-[1.12] tracking-tight">
                    <em>Bespoke Blooms &amp;</em> Botanical Artistry
                </h1>
                
                <p class="text-base sm:text-lg text-[#635E59] max-w-xl mx-auto lg:mx-0 leading-relaxed font-sans font-light">
                    Hand-tied morning harvest stems, European garden florals, and curated luxury gift boxes designed by master florists for memorable celebrations.
                </p>

                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-3 sm:gap-4 pt-2">
                    <a href="/shop/" class="vl-btn-primary">
                        <span>Explore Creations</span>
                        <i class="ri-arrow-right-line" aria-hidden="true"></i>
                    </a>
                    <a href="/events/" class="vl-btn-outline">
                        <span>Floral Workshops</span>
                    </a>
                </div>

                <!-- Trust Micro-Points -->
                <div class="pt-6 border-t border-[#E8DDD5] flex flex-wrap items-center justify-center lg:justify-start gap-4 sm:gap-6 text-xs text-[#635E59]">
                    <div class="flex items-center gap-1.5">
                        <i class="ri-checkbox-circle-fill text-[#3D5447]" aria-hidden="true"></i>
                        <span>Same-Day Delhi NCR Delivery</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <i class="ri-checkbox-circle-fill text-[#3D5447]" aria-hidden="true"></i>
                        <span>100% Fresh Stem Guarantee</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <i class="ri-checkbox-circle-fill text-[#3D5447]" aria-hidden="true"></i>
                        <span>Complimentary Calligraphy</span>
                    </div>
                </div>
            </div>

            <!-- Right Visual Composition (Fiore Style) -->
            <div class="lg:col-span-6 relative">
                <div class="vl-hero-frame relative">
                    <img src="<?php echo USER_IMAGES; ?>about-us-banner.jpg"
                        alt="Veloura Haute Botanical Arrangement"
                        class="w-full h-full object-cover"
                        fetchpriority="high"
                        loading="eager"
                        decoding="async">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                    
                    <!-- Floating Badge -->
                    <div class="absolute bottom-5 left-5 right-5 sm:right-auto bg-white/95 backdrop-blur-md p-4 rounded-xl border border-[#E8DDD5] shadow-lg max-w-xs">
                        <p class="text-[10px] uppercase tracking-[0.2em] font-semibold text-[#C47C68] mb-0.5">Atelier Craftsmanship</p>
                        <p class="font-serif text-sm text-[#242220] font-medium leading-snug">
                            Over 75,000 artisanal arrangements delivered with white-glove precision.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Fiorello-Inspired Interactive Category Filter & Showcase -->
<section class="py-12 sm:py-16 md:py-20 px-4 sm:px-6 lg:px-8 bg-white">
    <div class="max-w-7xl mx-auto">
        <!-- Section Header -->
        <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-12">
            <span class="vl-eyebrow">The Atelier Catalog</span>
            <h2 class="font-serif text-3xl sm:text-4xl text-[#242220] mb-3">Curated Botanical Creations</h2>
            <p class="text-sm sm:text-base text-[#635E59]">
                Select an atelier discipline to discover hand-tied bouquets, botanical greenery, and celebration hampers.
            </p>
        </div>

        <!-- Fiorello Grid Home Interactive Tabs -->
        <nav class="vl-filter-nav" aria-label="Product categories">
            <a href="/shop/" class="vl-filter-tab is-active">All Creations</a>
            <a href="/category/flower/" class="vl-filter-tab">Fresh Bouquets</a>
            <a href="/category/cake/" class="vl-filter-tab">Luxe Hampers</a>
            <a href="/category/planters/" class="vl-filter-tab">Ceramic Planters</a>
            <a href="/events/" class="vl-filter-tab">Atelier Workshops</a>
        </nav>

        <!-- 4-Column Product Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
            <?php
            $catalogQuery = "
                SELECT 
                    l.link_url, 
                    p.product_id, 
                    p.product_title, 
                    p.product_image, 
                    i.inventory_price, 
                    i.inventory_mrp,
                    i.inventory_currency_symbol,
                    i.inventory_in_stock,
                    l.link_published
                FROM " . TABLE_PREFIX . "product p
                INNER JOIN " . TABLE_PREFIX . "link l 
                    ON l.link_relation_id = p.product_id 
                    AND l.link_status = 200 
                    AND l.link_type = 'product' 
                    AND l.link_site_id = " . SITE_ID . "
                INNER JOIN " . TABLE_PREFIX . "inventory i 
                    ON i.inventory_relation_id = p.product_id 
                    AND i.inventory_type = 'product' 
                    AND i.inventory_site_id = " . SITE_ID . " 
                    AND i.inventory_price > 0
                WHERE p.product_status = 1
                  AND p.product_site_id = " . SITE_ID . "
                GROUP BY p.product_id
                ORDER BY l.link_published DESC
                LIMIT 8";

            if ($catalogRes = mysqli_query($connection, $catalogQuery)) {
                if (mysqli_num_rows($catalogRes) > 0) {
                    $idx = 0;
                    while ($prod = mysqli_fetch_assoc($catalogRes)) {
                        veloura_render_product_card($prod, [
                            'badge_label' => ($idx < 2 ? 'New Arrival' : ($idx === 2 ? 'Bestseller' : '')),
                            'priority_high' => ($idx < 4)
                        ]);
                        $idx++;
                    }
                }
            }
            ?>
        </div>

        <div class="mt-10 sm:mt-14 text-center">
            <a href="/shop/" class="vl-btn-outline">
                <span>View Full Atelier Collection</span>
                <i class="ri-arrow-right-line" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</section>

<!-- Fiorello Promo Banner Triad (3-Column Showcase) -->
<section class="py-12 sm:py-16 md:py-20 px-4 sm:px-6 lg:px-8 bg-[#FAF7F5] border-y border-[#E8DDD5]">
    <div class="max-w-7xl mx-auto">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="vl-eyebrow">Signature Offerings</span>
            <h2 class="font-serif text-3xl sm:text-4xl text-[#242220]">Artisanal Floral Experiences</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
            <!-- Promo 1 -->
            <article class="vl-promo-card group">
                <div class="vl-promo-media">
                    <img src="<?php echo USER_IMAGES; ?>about-us-banner.jpg"
                        alt="Hand-Tied Fresh Flowers"
                        loading="lazy" decoding="async">
                </div>
                <div class="vl-promo-body">
                    <span class="vl-eyebrow">Daily Farm Cut</span>
                    <h3 class="font-serif text-xl sm:text-2xl text-[#242220] mb-2">Hand-Tied Bouquets</h3>
                    <p class="text-sm text-[#635E59] leading-relaxed mb-4 flex-grow">
                        Seasonal roses, fragrant lilies, and Dutch foliage styled with delicate parchment and satin ribbons.
                    </p>
                    <a href="/category/flower/" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#C47C68] hover:text-[#A8634E] uppercase tracking-wider group-hover:translate-x-1 transition-all">
                        <span>Discover Bouquets</span>
                        <i class="ri-arrow-right-line" aria-hidden="true"></i>
                    </a>
                </div>
            </article>

            <!-- Promo 2 -->
            <article class="vl-promo-card group">
                <div class="vl-promo-media">
                    <img src="<?php echo USER_IMAGES; ?>fathers-day-banner.jpg"
                        alt="Curated Luxury Gift Hampers"
                        loading="lazy" decoding="async">
                </div>
                <div class="vl-promo-body">
                    <span class="vl-eyebrow">Celebration Gifting</span>
                    <h3 class="font-serif text-xl sm:text-2xl text-[#242220] mb-2">Artisan Gift Hampers</h3>
                    <p class="text-sm text-[#635E59] leading-relaxed mb-4 flex-grow">
                        Bespoke wooden and velvet gift boxes paired with artisanal treats, soy candles, and preserved stems.
                    </p>
                    <a href="/category/cake/" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#C47C68] hover:text-[#A8634E] uppercase tracking-wider group-hover:translate-x-1 transition-all">
                        <span>Discover Hampers</span>
                        <i class="ri-arrow-right-line" aria-hidden="true"></i>
                    </a>
                </div>
            </article>

            <!-- Promo 3 -->
            <article class="vl-promo-card group">
                <div class="vl-promo-media">
                    <img src="<?php echo USER_IMAGES; ?>service-app.jpg"
                        alt="Living Ceramic Planters"
                        loading="lazy" decoding="async">
                </div>
                <div class="vl-promo-body">
                    <span class="vl-eyebrow">Indoor Living</span>
                    <h3 class="font-serif text-xl sm:text-2xl text-[#242220] mb-2">Botanical Planters</h3>
                    <p class="text-sm text-[#635E59] leading-relaxed mb-4 flex-grow">
                        Air-purifying tropical greenery potted in hand-thrown terracotta, glazed ceramic, and fluted brass.
                    </p>
                    <a href="/category/planters/" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#C47C68] hover:text-[#A8634E] uppercase tracking-wider group-hover:translate-x-1 transition-all">
                        <span>Discover Planters</span>
                        <i class="ri-arrow-right-line" aria-hidden="true"></i>
                    </a>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- Fiore Heritage & Atelier Philosophy Section -->
<section class="py-14 sm:py-20 md:py-24 px-4 sm:px-6 lg:px-8 bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center">
            <div class="lg:col-span-6 relative">
                <div class="rounded-2xl overflow-hidden border border-[#E8DDD5] shadow-lg aspect-[4/3]">
                    <img src="<?php echo USER_IMAGES; ?>about-us-banner.jpg"
                        alt="Inside the Veloura Floral Atelier"
                        class="w-full h-full object-cover"
                        loading="lazy" decoding="async">
                </div>
                <div class="hidden sm:block absolute -bottom-6 -right-6 bg-[#FAF7F5] border border-[#E8DDD5] p-5 rounded-xl shadow-md max-w-xs">
                    <p class="font-serif text-2xl text-[#C47C68] font-semibold mb-1">100%</p>
                    <p class="text-xs uppercase tracking-wider text-[#635E59] font-medium">Eco-friendly hydration wraps &amp; recyclable packaging</p>
                </div>
            </div>

            <div class="lg:col-span-6 space-y-6">
                <span class="vl-eyebrow">Our Botanical Philosophy</span>
                <h2 class="font-serif text-3xl sm:text-4xl text-[#242220] leading-tight">
                    Every Stem is Selected to Convey Love, Poetry, and Timeless Elegance
                </h2>
                <p class="text-sm sm:text-base text-[#635E59] leading-relaxed">
                    Founded with a passion for European garden aesthetics, Veloura blends the delicate romance of Paris ateliers with contemporary bespoke design. We harvest stems at early dawn, condition them in nutrient-enriched water, and compose each creation by hand.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="p-4 rounded-xl bg-[#FAF7F5] border border-[#E8DDD5]">
                        <i class="ri-plant-line text-2xl text-[#3D5447] mb-2 block" aria-hidden="true"></i>
                        <h4 class="font-serif text-base text-[#242220] font-medium mb-1">Cold-Chain Freshness</h4>
                        <p class="text-xs text-[#635E59]">Delivered in climate-controlled transit so petals arrive in pristine bloom.</p>
                    </div>
                    <div class="p-4 rounded-xl bg-[#FAF7F5] border border-[#E8DDD5]">
                        <i class="ri-quill-pen-line text-2xl text-[#C47C68] mb-2 block" aria-hidden="true"></i>
                        <h4 class="font-serif text-base text-[#242220] font-medium mb-1">Wax-Sealed Notes</h4>
                        <p class="text-xs text-[#635E59]">Your heartfelt sentiments handwritten on luxury deckled paper with wax seal.</p>
                    </div>
                </div>

                <div class="pt-4">
                    <a href="/about-us/" class="vl-btn-primary">
                        <span>Read Our Story</span>
                        <i class="ri-arrow-right-line" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Masterclass & Workshop Banner -->
<section class="py-12 sm:py-16 md:py-20 px-4 sm:px-6 lg:px-8 bg-[#242220] text-white">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-center">
            <div class="lg:col-span-7 space-y-5">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-[#3D5447] text-[#EDF3F0]">
                    <i class="ri-calendar-event-line" aria-hidden="true"></i>
                    Weekend Botanical Masterclass
                </span>
                
                <h2 class="font-serif text-3xl sm:text-4xl lg:text-5xl text-white font-medium leading-tight">
                    The Art of Floral Design &amp; Tablescape Styling
                </h2>
                
                <p class="text-sm sm:text-base text-[#D5C4B7] leading-relaxed max-w-2xl font-light">
                    Join our Senior Floral Designers at the Chhatarpur Atelier for an intimate 3-hour masterclass. Learn color theory, stem conditioning, and build your own luxury arrangement to take home.
                </p>

                <div class="flex flex-wrap items-center gap-6 pt-2 text-sm text-[#F7EFEA]">
                    <div class="flex items-center gap-2">
                        <i class="ri-map-pin-2-line text-[#C47C68]" aria-hidden="true"></i>
                        <span>Chhatarpur &amp; Gurugram Ateliers</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="ri-money-rupee-circle-line text-[#C47C68]" aria-hidden="true"></i>
                        <span class="font-semibold text-lg text-white">₹4,500</span>
                        <span class="text-xs text-[#D5C4B7]">(All materials &amp; brunch included)</span>
                    </div>
                </div>

                <div class="pt-3">
                    <a href="/workshop-events/floral-design-workshop-/" class="vl-btn-primary">
                        <span>Reserve Your Seat</span>
                        <i class="ri-arrow-right-line" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="rounded-xl overflow-hidden border border-white/20 shadow-2xl aspect-[4/3]">
                    <img src="<?php echo USER_IMAGES; ?>about-us-banner.jpg"
                        alt="Floral Workshop Experience"
                        class="w-full h-full object-cover"
                        loading="lazy" decoding="async">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Client Praise & Reviews (Fiorello Style) -->
<section class="py-12 sm:py-16 md:py-20 px-4 sm:px-6 lg:px-8 bg-[#FAF7F5]">
    <div class="max-w-7xl mx-auto">
        <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-14">
            <span class="vl-eyebrow">Client Praise</span>
            <h2 class="font-serif text-3xl sm:text-4xl text-[#242220]">Kind Words from Our Patrons</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
            <article class="p-6 sm:p-8 rounded-xl bg-white border border-[#E8DDD5] shadow-sm flex flex-col justify-between">
                <div>
                    <div class="text-[#C47C68] text-sm mb-3">
                        <i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-fill"></i>
                    </div>
                    <p class="font-serif italic text-base sm:text-lg text-[#242220] leading-relaxed mb-4">
                        "The garden roses stayed breathtakingly fresh for over a week. The calligraphy note and velvet box made my anniversary truly unforgettable."
                    </p>
                </div>
                <div class="pt-4 border-t border-[#E8DDD5] flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-sm text-[#242220]">Ananya Mehra</p>
                        <p class="text-xs text-[#635E59]">Vasant Vihar, New Delhi</p>
                    </div>
                    <span class="text-xs text-[#3D5447] bg-[#EDF3F0] px-2 py-0.5 rounded font-medium">Verified Buyer</span>
                </div>
            </article>

            <article class="p-6 sm:p-8 rounded-xl bg-white border border-[#E8DDD5] shadow-sm flex flex-col justify-between">
                <div>
                    <div class="text-[#C47C68] text-sm mb-3">
                        <i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-fill"></i>
                    </div>
                    <p class="font-serif italic text-base sm:text-lg text-[#242220] leading-relaxed mb-4">
                        "Veloura’s same-day delivery in Gurugram was punctual to the minute. The arrangement looked even more opulent than the photographs."
                    </p>
                </div>
                <div class="pt-4 border-t border-[#E8DDD5] flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-sm text-[#242220]">Rohit Singhal</p>
                        <p class="text-xs text-[#635E59]">Golf Course Road, Gurugram</p>
                    </div>
                    <span class="text-xs text-[#3D5447] bg-[#EDF3F0] px-2 py-0.5 rounded font-medium">Verified Buyer</span>
                </div>
            </article>

            <article class="p-6 sm:p-8 rounded-xl bg-white border border-[#E8DDD5] shadow-sm flex flex-col justify-between">
                <div>
                    <div class="text-[#C47C68] text-sm mb-3">
                        <i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-fill"></i>
                    </div>
                    <p class="font-serif italic text-base sm:text-lg text-[#242220] leading-relaxed mb-4">
                        "Attended the floral design workshop in Chhatarpur. Superb instruction, warm hospitality, and I went home with a masterpiece!"
                    </p>
                </div>
                <div class="pt-4 border-t border-[#E8DDD5] flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-sm text-[#242220]">Dr. Priya Kapoor</p>
                        <p class="text-xs text-[#635E59]">Greater Kailash, New Delhi</p>
                    </div>
                    <span class="text-xs text-[#3D5447] bg-[#EDF3F0] px-2 py-0.5 rounded font-medium">Masterclass Attendee</span>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- White-Glove Atelier Trust & Perfection Bar -->
<section class="py-10 sm:py-14 px-4 sm:px-6 lg:px-8 bg-white border-t border-[#E8DDD5]">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div class="p-4 space-y-2">
                <i class="ri-truck-line text-3xl text-[#C47C68]" aria-hidden="true"></i>
                <h4 class="font-serif text-base text-[#242220] font-medium">Same-Day Delivery</h4>
                <p class="text-xs text-[#635E59]">Orders placed before 4 PM hand-delivered across Delhi NCR.</p>
            </div>
            <div class="p-4 space-y-2">
                <i class="ri-drop-line text-3xl text-[#3D5447]" aria-hidden="true"></i>
                <h4 class="font-serif text-base text-[#242220] font-medium">Fresh Stem Guarantee</h4>
                <p class="text-xs text-[#635E59]">Sourced from certified growers with 5-day vase life assurance.</p>
            </div>
            <div class="p-4 space-y-2">
                <i class="ri-gift-line text-3xl text-[#C47C68]" aria-hidden="true"></i>
                <h4 class="font-serif text-base text-[#242220] font-medium">Artisanal Packaging</h4>
                <p class="text-xs text-[#635E59]">Handcrafted bespoke boxes, satin ribbons &amp; calligraphy cards.</p>
            </div>
            <div class="p-4 space-y-2">
                <i class="ri-shield-check-line text-3xl text-[#3D5447]" aria-hidden="true"></i>
                <h4 class="font-serif text-base text-[#242220] font-medium">Encrypted Checkout</h4>
                <p class="text-xs text-[#635E59]">100% secure payment gateway with UPI, NetBanking &amp; Cards.</p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'footer.php'; ?>
