<?php

include_once 'header.php';

?>

<!-- Editorial Page Banner -->
<section class="tbe-banner">
    <img src="<?php echo safe_html(USER_IMAGES . 'about-us-banner.jpg'); ?>"
        alt="Crafted floral arrangements at the &amp;Blooms Chhatarpur atelier"
        fetchpriority="high" decoding="async" class="tbe-banner-media">
    <div class="tbe-banner-overlay"></div>
    <div class="tbe-banner-content">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <nav class="text-xs uppercase tracking-[0.16em] text-[#EFE8DC]/85 mb-3" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a href="/" class="hover:text-white transition-colors">Home</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-white" aria-current="page">Our Story</li>
                </ol>
            </nav>
            <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl text-white">Our Story</h1>
            <p class="text-[#EFE8DC] text-sm sm:text-base md:text-lg max-w-2xl mt-2 sm:mt-3">
                Crafting expressive floral compositions &amp; bespoke luxury gifting since 2018.
            </p>
        </div>
    </div>
</section>

<!-- Atelier Introduction -->
<section class="bg-[#FAF7F2] py-10 sm:py-16 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-10 lg:gap-14 items-center">
            <div class="lg:col-span-6">
                <div class="rounded-2xl overflow-hidden border border-[#E6DDD3] shadow-sm aspect-[4/3] bg-white">
                    <img src="<?php echo safe_html(USER_IMAGES . 'andblooms-store.jpg'); ?>"
                        alt="Inside the &amp;Blooms floral boutique in Delhi NCR"
                        loading="lazy" decoding="async"
                        class="w-full h-full object-cover object-center">
                </div>
            </div>
            <div class="lg:col-span-6 space-y-4 sm:space-y-5">
                <span class="tbe-eyebrow">Since 2018 &middot; Delhi NCR</span>
                <h2 class="font-serif text-2xl sm:text-3xl md:text-4xl text-[#1C1A18] leading-tight">
                    Bringing Nature&rsquo;s Quiet Elegance to Life&rsquo;s Cherished Moments
                </h2>
                <p class="text-[#5E5650] leading-relaxed">
                    Founded in 2018 in the heart of Chhatarpur, New Delhi, &amp;Blooms was born from a deep-rooted passion for curating meaningful floral experiences. What began as an intimate design studio quickly blossomed into a sought-after destination for celebrating love, milestones, and thoughtful gestures.
                </p>
                <p class="text-[#5E5650] leading-relaxed">
                    Our floral artisans blend traditional Indian sensibilities with contemporary botanical aesthetics to sculpt arrangements that are both refined and emotionally resonant. Every stem is sourced at dawn from sustainable growers so your bouquet arrives vibrant and enduring.
                </p>
                <div class="pt-2 flex flex-wrap gap-3">
                    <a href="/shop/" class="tbe-btn-forest">Explore Creations</a>
                    <a href="/contact-us/" class="tbe-btn-outline">Visit Our Ateliers</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Atelier Values -->
<section class="bg-[#EFE8DC]/55 border-y border-[#E6DDD3] py-10 sm:py-16 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-12">
            <span class="tbe-eyebrow">Philosophy</span>
            <h2 class="font-serif text-2xl sm:text-3xl md:text-4xl text-[#1C1A18] mt-1 mb-3">Our Guiding Pillars</h2>
            <p class="text-sm sm:text-base text-[#5E5650]">The principles behind every signature stem and gift curated at &amp;Blooms.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
            <article class="bg-white border border-[#E6DDD3] rounded-xl p-5 sm:p-8 shadow-sm">
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-[#EFE8DC] text-[#183028] mb-4 sm:mb-5">
                    <i class="ri-leaf-line text-xl" aria-hidden="true"></i>
                </div>
                <h3 class="font-serif text-xl text-[#1C1A18] mb-2 sm:mb-3">Mindful Sourcing</h3>
                <p class="text-sm text-[#5E5650] leading-relaxed">
                    We partner directly with trusted local and ethical farms, embracing seasonal varieties and recyclable, plastic-minimal presentation.
                </p>
            </article>

            <article class="bg-white border border-[#E6DDD3] rounded-xl p-5 sm:p-8 shadow-sm">
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-[#F4E9E4] text-[#9B5637] mb-4 sm:mb-5">
                    <i class="ri-heart-3-line text-xl" aria-hidden="true"></i>
                </div>
                <h3 class="font-serif text-xl text-[#1C1A18] mb-2 sm:mb-3">Master Craftsmanship</h3>
                <p class="text-sm text-[#5E5650] leading-relaxed">
                    Each composition is conditioned and arranged by hand in small batches, balanced for silhouette, fragrance, and longevity.
                </p>
            </article>

            <article class="bg-white border border-[#E6DDD3] rounded-xl p-5 sm:p-8 shadow-sm">
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-[#EFE8DC] text-[#183028] mb-4 sm:mb-5">
                    <i class="ri-customer-service-2-line text-xl" aria-hidden="true"></i>
                </div>
                <h3 class="font-serif text-xl text-[#1C1A18] mb-2 sm:mb-3">White-Glove Delivery</h3>
                <p class="text-sm text-[#5E5650] leading-relaxed">
                    Dedicated same-day courier dispatch across Chhatarpur, Gurugram, and Delhi NCR ensures every gift arrives effortlessly on cue.
                </p>
            </article>
        </div>
    </div>
</section>

<!-- Key Milestones -->
<section class="bg-[#FAF7F2] py-10 sm:py-16 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-6">
            <div class="bg-white border border-[#E6DDD3] rounded-xl p-4 sm:p-7 text-center shadow-sm">
                <div class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center bg-[#EFE8DC] text-[#183028] rounded-full mx-auto mb-3 sm:mb-4">
                    <i class="ri-calendar-line text-lg sm:text-xl" aria-hidden="true"></i>
                </div>
                <p class="font-serif text-2xl sm:text-3xl md:text-4xl text-[#9B5637] mb-1">2018</p>
                <p class="text-[11px] sm:text-xs uppercase tracking-[0.12em] sm:tracking-[0.14em] text-[#5E5650] font-medium">Atelier Established</p>
            </div>
            <div class="bg-white border border-[#E6DDD3] rounded-xl p-4 sm:p-7 text-center shadow-sm">
                <div class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center bg-[#EFE8DC] text-[#183028] rounded-full mx-auto mb-3 sm:mb-4">
                    <i class="ri-flower-line text-lg sm:text-xl" aria-hidden="true"></i>
                </div>
                <p class="font-serif text-2xl sm:text-3xl md:text-4xl text-[#9B5637] mb-1">75,000+</p>
                <p class="text-[11px] sm:text-xs uppercase tracking-[0.12em] sm:tracking-[0.14em] text-[#5E5650] font-medium">Orders Delivered</p>
            </div>
            <div class="bg-white border border-[#E6DDD3] rounded-xl p-4 sm:p-7 text-center shadow-sm">
                <div class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center bg-[#EFE8DC] text-[#183028] rounded-full mx-auto mb-3 sm:mb-4">
                    <i class="ri-user-smile-line text-lg sm:text-xl" aria-hidden="true"></i>
                </div>
                <p class="font-serif text-2xl sm:text-3xl md:text-4xl text-[#9B5637] mb-1">28,000+</p>
                <p class="text-[11px] sm:text-xs uppercase tracking-[0.12em] sm:tracking-[0.14em] text-[#5E5650] font-medium">Happy Patrons</p>
            </div>
            <div class="bg-white border border-[#E6DDD3] rounded-xl p-4 sm:p-7 text-center shadow-sm">
                <div class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center bg-[#EFE8DC] text-[#183028] rounded-full mx-auto mb-3 sm:mb-4">
                    <i class="ri-plant-line text-lg sm:text-xl" aria-hidden="true"></i>
                </div>
                <p class="font-serif text-2xl sm:text-3xl md:text-4xl text-[#9B5637] mb-1">120+</p>
                <p class="text-[11px] sm:text-xs uppercase tracking-[0.12em] sm:tracking-[0.14em] text-[#5E5650] font-medium">Flower Varieties</p>
            </div>
        </div>
    </div>
</section>

<!-- Leadership -->
<section class="bg-white border-t border-[#E6DDD3] py-10 sm:py-16 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-12">
            <span class="tbe-eyebrow">Leadership</span>
            <h2 class="font-serif text-2xl sm:text-3xl md:text-4xl text-[#1C1A18] mt-1 mb-3">Meet the Minds Behind &amp;Blooms</h2>
            <p class="text-sm sm:text-base text-[#5E5650]">Guiding our botanical artistry and atelier standards across New Delhi and Gurugram.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-8 max-w-4xl mx-auto">
            <article class="bg-[#FAF7F2] border border-[#E6DDD3] rounded-xl overflow-hidden shadow-sm">
                <div class="aspect-[4/5] w-full overflow-hidden bg-[#EFE8DC]">
                    <img src="<?php echo safe_html(USER_IMAGES . 'shalini_dugar.jpg'); ?>"
                        alt="Shalini Dugar, Founder of &amp;Blooms"
                        loading="lazy" decoding="async"
                        class="w-full h-full object-cover object-top">
                </div>
                <div class="p-4 sm:p-6">
                    <h3 class="font-serif text-xl sm:text-2xl text-[#1C1A18]">Shalini Dugar</h3>
                    <p class="text-xs uppercase tracking-[0.16em] text-[#9B5637] font-semibold mt-1">Founder &amp; Creative Lead</p>
                </div>
            </article>

            <article class="bg-[#FAF7F2] border border-[#E6DDD3] rounded-xl overflow-hidden shadow-sm">
                <div class="aspect-[4/5] w-full overflow-hidden bg-[#EFE8DC]">
                    <img src="<?php echo safe_html(USER_IMAGES . 'ayush_dugar.jpeg'); ?>"
                        alt="Ayush Dugar, Director at &amp;Blooms"
                        loading="lazy" decoding="async"
                        class="w-full h-full object-cover object-top">
                </div>
                <div class="p-4 sm:p-6">
                    <h3 class="font-serif text-xl sm:text-2xl text-[#1C1A18]">Ayush Dugar</h3>
                    <p class="text-xs uppercase tracking-[0.16em] text-[#9B5637] font-semibold mt-1">Director</p>
                </div>
            </article>
        </div>
    </div>
</section>

<?php

include_once 'footer.php';

?>
