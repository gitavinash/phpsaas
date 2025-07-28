<?php

require_once 'header.php';

?>


<main class="pt-28 pb-16">
    <div class="container mx-auto px-4">
        <div class="mb-6 px-4">
            <h1 class="text-2xl font-semibold text-gray-800">
                Search results for: <span class="text-primary"><?php echo htmlspecialchars($q); ?></span>
            </h1>
        </div>

        <div class="flex flex-col lg:flex-row">

            <div class="lg:w-3/4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <article class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
                        <img src="https://readdy.ai/api/search-image?query=modern%20airport%20terminal%20with%20diverse%20travelers%2C%20business%20people%20and%20families%20walking%20through%2C%20natural%20lighting%2C%20professional%20photography%20style%2C%20high-end%20travel%20atmosphere&width=600&height=400&seq=1&orientation=landscape"
                            alt="Travel Tips" class="w-full h-56 object-cover">
                        <div class="p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="px-3 py-1 text-sm bg-blue-50 text-blue-600 rounded-full">Travel Tips</span>
                                <span class="text-sm text-gray-500">5 min read</span>
                            </div>
                            <h2 class="text-xl font-semibold mb-2 line-clamp-2">Essential Travel Documents: A Complete
                                Checklist for International Travel</h2>
                            <p class="text-gray-600 mb-4 line-clamp-3">Planning an international trip? Make sure you
                                have all the necessary documents ready. From passports to visas, here's your
                                comprehensive guide to travel documentation.</p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-200"></div>
                                    <div>
                                        <div class="text-sm font-medium">Emily Richardson</div>
                                        <div class="text-xs text-gray-500">July 19, 2025</div>
                                    </div>
                                </div>
                                <button class="text-primary hover:text-primary/80">Read More</button>
                            </div>
                        </div>
                    </article>
                    <article class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
                        <img src="https://readdy.ai/api/search-image?query=professional%20visa%20application%20process%2C%20documents%20and%20passport%20on%20modern%20desk%2C%20official%20stamps%20and%20papers%2C%20clean%20business%20environment&width=600&height=400&seq=2&orientation=landscape"
                            alt="Visa Guide" class="w-full h-56 object-cover">
                        <div class="p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="px-3 py-1 text-sm bg-green-50 text-green-600 rounded-full">Visa
                                    Guide</span>
                                <span class="text-sm text-gray-500">7 min read</span>
                            </div>
                            <h2 class="text-xl font-semibold mb-2 line-clamp-2">Understanding Different Types of Visas:
                                Which One is Right for You?</h2>
                            <p class="text-gray-600 mb-4 line-clamp-3">Navigate through the complexities of visa types.
                                From tourist to business visas, learn about the requirements and application processes
                                for each category.</p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-200"></div>
                                    <div>
                                        <div class="text-sm font-medium">Marcus Chen</div>
                                        <div class="text-xs text-gray-500">July 18, 2025</div>
                                    </div>
                                </div>
                                <button class="text-primary hover:text-primary/80">Read More</button>
                            </div>
                        </div>
                    </article>
                    <article class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
                        <img src="https://readdy.ai/api/search-image?query=luxury%20hotel%20lobby%20with%20modern%20design%2C%20elegant%20reception%20area%2C%20sophisticated%20travelers%20checking%20in%2C%20professional%20hospitality%20photography&width=600&height=400&seq=3&orientation=landscape"
                            alt="Accommodation" class="w-full h-56 object-cover">
                        <div class="p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <span
                                    class="px-3 py-1 text-sm bg-purple-50 text-purple-600 rounded-full">Accommodation</span>
                                <span class="text-sm text-gray-500">6 min read</span>
                            </div>
                            <h2 class="text-xl font-semibold mb-2 line-clamp-2">Top 10 Luxury Hotels in Europe: Where
                                Comfort Meets Culture</h2>
                            <p class="text-gray-600 mb-4 line-clamp-3">Discover the most exclusive hotels across Europe.
                                From historic palaces to modern luxury resorts, find your perfect stay for an
                                unforgettable experience.</p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-200"></div>
                                    <div>
                                        <div class="text-sm font-medium">Sophie Anderson</div>
                                        <div class="text-xs text-gray-500">July 17, 2025</div>
                                    </div>
                                </div>
                                <button class="text-primary hover:text-primary/80">Read More</button>
                            </div>
                        </div>
                    </article>
                    <article class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
                        <img src="https://readdy.ai/api/search-image?query=modern%20travel%20planning%20setup%20with%20digital%20devices%2C%20maps%20and%20travel%20accessories%20on%20wooden%20desk%2C%20professional%20flat%20lay%20photography&width=600&height=400&seq=4&orientation=landscape"
                            alt="Planning" class="w-full h-56 object-cover">
                        <div class="p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="px-3 py-1 text-sm bg-red-50 text-red-600 rounded-full">Planning</span>
                                <span class="text-sm text-gray-500">8 min read</span>
                            </div>
                            <h2 class="text-xl font-semibold mb-2 line-clamp-2">Digital Nomad's Guide: Working While
                                Traveling the World</h2>
                            <p class="text-gray-600 mb-4 line-clamp-3">Learn how to balance work and travel effectively.
                                Tips on finding reliable internet, coworking spaces, and maintaining productivity while
                                exploring new destinations.</p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-200"></div>
                                    <div>
                                        <div class="text-sm font-medium">Alexander Wright</div>
                                        <div class="text-xs text-gray-500">July 16, 2025</div>
                                    </div>
                                </div>
                                <button class="text-primary hover:text-primary/80">Read More</button>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="mt-12 flex items-center justify-center">
                    <nav class="flex items-center gap-1">
                        <button
                            class="w-10 h-10 flex items-center justify-center rounded-full text-gray-500 hover:bg-gray-100">
                            <i class="ri-arrow-left-s-line"></i>
                        </button>
                        <button
                            class="w-10 h-10 flex items-center justify-center rounded-full bg-primary text-white">1</button>
                        <button
                            class="w-10 h-10 flex items-center justify-center rounded-full text-gray-700 hover:bg-gray-100">2</button>
                        <button
                            class="w-10 h-10 flex items-center justify-center rounded-full text-gray-700 hover:bg-gray-100">3</button>
                        <button
                            class="w-10 h-10 flex items-center justify-center rounded-full text-gray-500 hover:bg-gray-100">
                            <i class="ri-arrow-right-s-line"></i>
                        </button>
                    </nav>
                </div>
            </div>
            <?php
            require_once 'sidebar.php';
            ?>
        </div>
    </div>
</main>

<?php

require_once 'footer.php';

?>