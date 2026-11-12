<?php

include_once 'header.php';

?>

<section class="bg-[#FAF7F2] py-10 sm:py-16 md:py-24 px-4">
    <div class="max-w-2xl mx-auto bg-white border border-[#E6DDD3] rounded-2xl shadow-sm p-6 sm:p-12 text-center">
        <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-[#F4E9E4] text-[#9B5637] flex items-center justify-center">
            <i class="ri-tools-line text-3xl" aria-hidden="true"></i>
        </div>

        <span class="tbe-eyebrow">500 &middot; Temporary Interruption</span>
        <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl text-[#1C1A18] mt-1 mb-4">We&rsquo;re Tending the Atelier</h1>
        <p class="text-[#5E5650] text-sm sm:text-base leading-relaxed max-w-lg mx-auto mb-8">
            Our storefront experienced a momentary technical interruption. Please refresh in a few moments, search below, or call our concierge directly.
        </p>

        <search>
            <form action="/search/" method="GET" class="flex flex-col sm:flex-row gap-3 max-w-lg mx-auto">
                <label for="error500Search" class="sr-only">Search floral collections</label>
                <input type="search" id="error500Search" name="q" placeholder="Search roses, hampers, cakes..."
                    autocorrect="off" autocapitalize="none" spellcheck="false"
                    class="tbe-input flex-1" required>
                <button type="submit" class="tbe-btn-forest justify-center whitespace-nowrap px-6">
                    <i class="ri-search-line" aria-hidden="true"></i>
                    <span>Search</span>
                </button>
            </form>
        </search>

        <div class="mt-6">
            <a href="/" class="tbe-btn-outline">Return to Homepage</a>
        </div>

        <div class="mt-10 pt-8 border-t border-[#E6DDD3]">
            <p class="text-xs uppercase tracking-[0.15em] text-[#5E5650] font-semibold mb-4">Explore Categories</p>
            <div class="flex flex-wrap justify-center gap-2.5 sm:gap-3 text-sm">
                <a href="/category/flower/" class="inline-flex items-center min-h-[40px] px-3.5 py-2 rounded-full bg-[#FAF7F2] border border-[#E6DDD3] text-[#183028] hover:border-[#183028] transition-colors">Fresh Flowers</a>
                <a href="/tag/luxury/" class="inline-flex items-center min-h-[40px] px-3.5 py-2 rounded-full bg-[#FAF7F2] border border-[#E6DDD3] text-[#183028] hover:border-[#183028] transition-colors">Luxury Collections</a>
                <a href="/category/planters/" class="inline-flex items-center min-h-[40px] px-3.5 py-2 rounded-full bg-[#FAF7F2] border border-[#E6DDD3] text-[#183028] hover:border-[#183028] transition-colors">Planters</a>
                <a href="/category/cake/" class="inline-flex items-center min-h-[40px] px-3.5 py-2 rounded-full bg-[#FAF7F2] border border-[#E6DDD3] text-[#183028] hover:border-[#183028] transition-colors">Cake &amp; Hampers</a>
                <a href="/tag/blooms-of-<?php echo strtolower(date('F')); ?>/" class="inline-flex items-center min-h-[40px] px-3.5 py-2 rounded-full bg-[#FAF7F2] border border-[#E6DDD3] text-[#183028] hover:border-[#183028] transition-colors">
                    Blooms of <?php echo date('F'); ?>
                </a>
            </div>
        </div>

        <p class="mt-8 text-xs text-[#5E5650]">
            Urgent Order Assistance: <a href="tel:+919910744119" class="text-[#9B5637] font-medium hover:underline">+91-9910744119</a>
        </p>
    </div>
</section>

<?php

include_once 'footer.php';

?>
