<?php
include_once 'header.php';
?>

<section class="min-h-[65vh] flex items-center justify-center py-20 px-4 bg-[#FAF7F5]">
    <div class="max-w-lg w-full mx-auto text-center bg-white border border-[#E8DDD5] rounded-2xl p-8 sm:p-12 shadow-sm">
        <div class="w-16 h-16 mx-auto mb-5 rounded-full bg-[#FAF7F5] border border-[#E8DDD5] flex items-center justify-center text-[#C47C68]">
            <i class="ri-compass-3-line text-2xl" aria-hidden="true"></i>
        </div>
        <span class="vl-eyebrow">404 &bull; Page Not Found</span>
        <h1 class="font-serif text-3xl sm:text-4xl text-[#242220] mt-1 mb-3">A Stem Has Wilted Away</h1>
        <p class="text-sm text-[#6E675F] leading-relaxed mb-8">
            The page you are searching for may have been moved or is currently in seasonal rest. Explore our latest fresh floral arrivals.
        </p>

        <!-- Search Form -->
        <form action="/search/" method="GET" class="mb-8">
            <div class="flex gap-2">
                <input type="text" name="q" placeholder="Search creations, flowers, hampers..." required class="vl-input py-2.5 text-sm">
                <button type="submit" class="vl-btn-primary px-5 text-xs">
                    <i class="ri-search-line" aria-hidden="true"></i>
                    <span>Search</span>
                </button>
            </div>
        </form>

        <div class="pt-6 border-t border-[#E8DDD5]">
            <span class="block text-xs uppercase tracking-wider text-[#6E675F] font-semibold mb-3">Popular Atelier Destinations</span>
            <div class="flex flex-wrap justify-center gap-2">
                <a href="/shop/" class="vl-filter-tab">All Creations</a>
                <a href="/category/flower/" class="vl-filter-tab">Fresh Flowers</a>
                <a href="/events/" class="vl-filter-tab">Workshops</a>
                <a href="/contact-us/" class="vl-filter-tab">Concierge</a>
            </div>
        </div>
    </div>
</section>

<?php
include_once 'footer.php';
?>
