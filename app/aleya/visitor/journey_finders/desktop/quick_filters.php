<!-- Search Parameters Bar -->
<div class="bg-white border-b border-gray-200 shadow-sm">
    <div class="container mx-auto px-4 py-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <!-- Search Info -->
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 flex items-center justify-center text-primary bg-primary/10 rounded-full">
                    <i class="ri-search-line"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Search for</p>
                    <p class="font-medium">
                        <?php
                        $destinationPage = ucwords(str_replace('-', ' ', $q ?? ''));
                        echo $destinationPage;
                        ?>
                    </p>
                </div>
            </div>
            <!-- Back Button -->
            <div>
                <a href="/holiday/" data-readdy="true"
                    class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-button whitespace-nowrap text-sm hover:bg-gray-50 transition">
                    <i class="ri-arrow-left-line mr-1"></i>
                    Back
                </a>
            </div>
        </div>
    </div>
</div>
<!-- Quick Filters -->
<div class="bg-white border-b border-gray-200">
    <div class="container mx-auto px-4 py-3 flex items-center gap-2">
        <!-- Non-scrollable label -->
        <span class="text-gray-700 font-medium whitespace-nowrap flex-shrink-0">Quick Filters:</span>
        <div class="flex overflow-x-auto custom-scrollbar pb-1 gap-2">

            <button type="button"
                class="quick-filter px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm whitespace-nowrap hover:bg-gray-200 transition"
                data-filter="duration" data-value="5-7">
                5-7 Days
            </button>
            <button type="button"
                class="quick-filter px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm whitespace-nowrap hover:bg-gray-200 transition"
                data-filter="duration" data-value="8-30">
                More than 1 week
            </button>
            <button type="button"
                class="quick-filter px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm whitespace-nowrap hover:bg-gray-200 transition"
                data-filter="type" data-value="adventure">
                Adventure
            </button>
            <button type="button"
                class="quick-filter px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm whitespace-nowrap hover:bg-gray-200 transition"
                data-filter="type" data-value="cultural">
                Cultural
            </button>
            <button type="button"
                class="quick-filter px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm whitespace-nowrap hover:bg-gray-200 transition"
                data-filter="type" data-value="beach">
                Beach
            </button>
            <button type="button"
                class="quick-filter px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm whitespace-nowrap hover:bg-gray-200 transition"
                data-filter="price" data-value="0-20000">
                Under ₹ 20,000
            </button>
            <button type="button"
                class="quick-filter px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm whitespace-nowrap hover:bg-gray-200 transition"
                data-filter="price" data-value="20001-50000">
                ₹ 20,000 - ₹ 50,000
            </button>
            <button type="button"
                class="quick-filter px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm whitespace-nowrap hover:bg-gray-200 transition"
                data-filter="price" data-value="50001-1000000">
                More than ₹ 50,000
            </button>
        </div>
    </div>
</div>