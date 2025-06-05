<?php

require_once 'header.php';

?>
<!-- Main Content -->
<div class="container mx-auto px-4 py-8">
    <!-- Featured Destinations -->
    <section class="mb-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold">Featured Destinations -
                <?php echo str_replace('-', ' ', ucwords(end($urlStructure))) ?></h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- Destination Card 1 -->
            <div class="destination-card bg-white rounded-lg overflow-hidden shadow-sm">
                <div class="relative h-56 overflow-hidden">
                    <img src="#api/search-image?query=beautiful%2520Bali%2520rice%2520terraces%2520with%2520traditional%2520temples%2520in%2520background%252C%2520lush%2520green%2520landscape%252C%2520perfect%2520sunny%2520day%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=600&height=400&seq=101&orientation=landscape"
                        alt="Bali" class="w-full h-full object-cover object-top" />
                    <div class="absolute top-4 left-4 bg-primary text-white text-xs font-medium px-2 py-1 rounded-full">
                        Featured
                    </div>
                    <div
                        class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white/80 backdrop-blur-sm rounded-full text-gray-700 hover:text-primary cursor-pointer transition">
                        <i class="ri-heart-line"></i>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-lg">Bali</h3>
                        <span class="text-sm text-gray-600">Indonesia</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                        Discover tropical paradise with lush rice terraces, ancient
                        temples, and pristine beaches.
                    </p>
                    <div class="mb-3">
                        <h4 class="text-sm font-medium mb-1">Popular Attractions:</h4>
                        <ul class="text-gray-600 text-sm space-y-1">
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Ubud Sacred Monkey Forest</span>
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Tegalalang Rice Terraces</span>
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Uluwatu Temple</span>
                            </li>
                        </ul>
                    </div>
                    <div class="flex items-center mb-4">
                        <div class="w-5 h-5 flex items-center justify-center text-primary mr-1">
                            <i class="ri-calendar-line"></i>
                        </div>
                        <span class="text-sm text-gray-600">Best time: April - October</span>
                    </div>
                    <a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/bf204a0d-8d2e-4112-bb67-ac835580264e"
                        data-readdy="true"
                        class="block w-full bg-primary text-white text-center px-4 py-2 !rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition">View
                        Tours</a>
                </div>
            </div>
            <!-- Destination Card 2 -->
            <div class="destination-card bg-white rounded-lg overflow-hidden shadow-sm">
                <div class="relative h-56 overflow-hidden">
                    <img src="#api/search-image?query=beautiful%2520Santorini%2520Greece%2520with%2520white%2520buildings%2520and%2520blue%2520domes%2520overlooking%2520the%2520Aegean%2520Sea%252C%2520stunning%2520sunset%2520colors%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=600&height=400&seq=102&orientation=landscape"
                        alt="Santorini" class="w-full h-full object-cover object-top" />
                    <div class="absolute top-4 left-4 bg-primary text-white text-xs font-medium px-2 py-1 rounded-full">
                        Featured
                    </div>
                    <div
                        class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white/80 backdrop-blur-sm rounded-full text-gray-700 hover:text-primary cursor-pointer transition">
                        <i class="ri-heart-line"></i>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-lg">Santorini</h3>
                        <span class="text-sm text-gray-600">Greece</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                        Iconic white-washed buildings with blue domes perched on cliffs
                        overlooking the Aegean Sea.
                    </p>
                    <div class="mb-3">
                        <h4 class="text-sm font-medium mb-1">Popular Attractions:</h4>
                        <ul class="text-gray-600 text-sm space-y-1">
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Oia Sunset Point</span>
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Fira Town</span>
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Red Beach</span>
                            </li>
                        </ul>
                    </div>
                    <div class="flex items-center mb-4">
                        <div class="w-5 h-5 flex items-center justify-center text-primary mr-1">
                            <i class="ri-calendar-line"></i>
                        </div>
                        <span class="text-sm text-gray-600">Best time: May - September</span>
                    </div>
                    <a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/b9b6daf9-df56-4396-a606-2678ca962433"
                        data-readdy="true"
                        class="block w-full bg-primary text-white text-center px-4 py-2 !rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition">View
                        Tours</a>
                </div>
            </div>
            <!-- Destination Card 3 -->
            <div class="destination-card bg-white rounded-lg overflow-hidden shadow-sm">
                <div class="relative h-56 overflow-hidden">
                    <img src="#api/search-image?query=beautiful%2520Kyoto%2520Japan%2520with%2520traditional%2520temples%252C%2520cherry%2520blossoms%252C%2520and%2520geisha%2520district%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition%252C%2520cultural%2520landmarks&width=600&height=400&seq=103&orientation=landscape"
                        alt="Kyoto" class="w-full h-full object-cover object-top" />
                    <div class="absolute top-4 left-4 bg-primary text-white text-xs font-medium px-2 py-1 rounded-full">
                        Featured
                    </div>
                    <div
                        class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white/80 backdrop-blur-sm rounded-full text-gray-700 hover:text-primary cursor-pointer transition">
                        <i class="ri-heart-line"></i>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-lg">Kyoto</h3>
                        <span class="text-sm text-gray-600">Japan</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                        Ancient capital with over 1,600 Buddhist temples, traditional
                        geisha districts, and imperial palaces.
                    </p>
                    <div class="mb-3">
                        <h4 class="text-sm font-medium mb-1">Popular Attractions:</h4>
                        <ul class="text-gray-600 text-sm space-y-1">
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Fushimi Inari Shrine</span>
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Arashiyama Bamboo Grove</span>
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Kinkaku-ji (Golden Pavilion)</span>
                            </li>
                        </ul>
                    </div>
                    <div class="flex items-center mb-4">
                        <div class="w-5 h-5 flex items-center justify-center text-primary mr-1">
                            <i class="ri-calendar-line"></i>
                        </div>
                        <span class="text-sm text-gray-600">Best time: March - May, Oct - Nov</span>
                    </div>
                    <a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/b9b6daf9-df56-4396-a606-2678ca962433"
                        data-readdy="true"
                        class="block w-full bg-primary text-white text-center px-4 py-2 !rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition">View
                        Tours</a>
                </div>
            </div>
            <!-- Destination Card 4 -->
            <div class="destination-card bg-white rounded-lg overflow-hidden shadow-sm">
                <div class="relative h-56 overflow-hidden">
                    <img src="#api/search-image?query=beautiful%2520Machu%2520Picchu%2520Peru%2520with%2520ancient%2520Incan%2520ruins%2520on%2520mountain%2520top%252C%2520dramatic%2520clouds%252C%2520lush%2520green%2520surroundings%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=600&height=400&seq=104&orientation=landscape"
                        alt="Machu Picchu" class="w-full h-full object-cover object-top" />
                    <div class="absolute top-4 left-4 bg-primary text-white text-xs font-medium px-2 py-1 rounded-full">
                        Featured
                    </div>
                    <div
                        class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white/80 backdrop-blur-sm rounded-full text-gray-700 hover:text-primary cursor-pointer transition">
                        <i class="ri-heart-line"></i>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-lg">Machu Picchu</h3>
                        <span class="text-sm text-gray-600">Peru</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                        Iconic 15th-century Inca citadel set high in the Andes
                        Mountains, surrounded by cloud forests.
                    </p>
                    <div class="mb-3">
                        <h4 class="text-sm font-medium mb-1">Popular Attractions:</h4>
                        <ul class="text-gray-600 text-sm space-y-1">
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>The Sacred Valley</span>
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Inca Trail</span>
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Huayna Picchu Mountain</span>
                            </li>
                        </ul>
                    </div>
                    <div class="flex items-center mb-4">
                        <div class="w-5 h-5 flex items-center justify-center text-primary mr-1">
                            <i class="ri-calendar-line"></i>
                        </div>
                        <span class="text-sm text-gray-600">Best time: May - September</span>
                    </div>
                    <a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/b9b6daf9-df56-4396-a606-2678ca962433"
                        data-readdy="true"
                        class="block w-full bg-primary text-white text-center px-4 py-2 !rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition">View
                        Tours</a>
                </div>
            </div>
        </div>
    </section>
    <!-- Quick Links Section -->
    <?php

    require_once 'quick_links.php';

    ?>
</div>
<!-- Newsletter -->
<section class="py-12 bg-primary/5">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-2xl font-bold mb-3">Get Travel Inspiration</h2>
            <p class="text-gray-600 mb-6">
                Subscribe to our newsletter for exclusive deals and travel tips
            </p>
            <div class="flex flex-col sm:flex-row gap-4 max-w-xl mx-auto">
                <input type="email" placeholder="Your email address"
                    class="flex-grow px-4 py-3 !rounded-button border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/20" />
                <button
                    class="bg-primary text-white px-6 py-3 !rounded-button whitespace-nowrap font-medium hover:bg-primary/90 transition">
                    Subscribe
                </button>
            </div>
        </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>