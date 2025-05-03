<?php

require_once 'header.php';

?>
<!-- Filter Bar -->
<div class="bg-white border-b border-gray-200 shadow-sm">
    <div class="container mx-auto px-4 py-4">
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Explore Destinations</h1>
                <div class="relative hidden md:block">
                    <div
                        class="w-10 h-10 flex items-center justify-center text-gray-500 absolute left-2 top-1/2 transform -translate-y-1/2">
                        <i class="ri-search-line ri-lg"></i>
                    </div>
                    <input type="text" placeholder="Search destinations..."
                        class="pl-12 pr-4 py-2 w-64 border border-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-primary/20 text-sm" />
                </div>
            </div>
            <div class="overflow-x-auto custom-scrollbar">
                <div class="flex space-x-3 py-2 min-w-max">
                    <div class="font-medium text-gray-700 flex items-center mr-2">
                        Filter by:
                    </div>
                    <!-- Continent Filters -->
                    <div class="relative group">
                        <button
                            class="px-4 py-2 bg-white border border-gray-200 rounded-full text-sm flex items-center gap-2 hover:border-gray-300 transition">
                            <i class="ri-earth-line"></i>
                            <span>Continents</span>
                            <i class="ri-arrow-down-s-line"></i>
                        </button>
                        <div
                            class="absolute left-0 top-full mt-2 bg-white shadow-lg rounded-lg p-3 w-48 hidden group-hover:block z-10">
                            <div class="space-y-2">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox" />
                                    <span class="ml-2 text-sm">Asia</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox" />
                                    <span class="ml-2 text-sm">Europe</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox" />
                                    <span class="ml-2 text-sm">North America</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox" />
                                    <span class="ml-2 text-sm">South America</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox" />
                                    <span class="ml-2 text-sm">Africa</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox" />
                                    <span class="ml-2 text-sm">Oceania</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <!-- Travel Themes -->
                    <div class="relative group">
                        <button
                            class="px-4 py-2 bg-white border border-gray-200 rounded-full text-sm flex items-center gap-2 hover:border-gray-300 transition">
                            <i class="ri-compass-line"></i>
                            <span>Travel Themes</span>
                            <i class="ri-arrow-down-s-line"></i>
                        </button>
                        <div
                            class="absolute left-0 top-full mt-2 bg-white shadow-lg rounded-lg p-3 w-48 hidden group-hover:block z-10">
                            <div class="space-y-2">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox" />
                                    <span class="ml-2 text-sm">Adventure</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox" />
                                    <span class="ml-2 text-sm">Beach</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox" />
                                    <span class="ml-2 text-sm">Cultural</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox" />
                                    <span class="ml-2 text-sm">Food & Culinary</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox" />
                                    <span class="ml-2 text-sm">Wildlife</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox" />
                                    <span class="ml-2 text-sm">Wellness</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <!-- Seasons -->
                    <div class="relative group">
                        <button
                            class="px-4 py-2 bg-white border border-gray-200 rounded-full text-sm flex items-center gap-2 hover:border-gray-300 transition">
                            <i class="ri-sun-line"></i>
                            <span>Best Season</span>
                            <i class="ri-arrow-down-s-line"></i>
                        </button>
                        <div
                            class="absolute left-0 top-full mt-2 bg-white shadow-lg rounded-lg p-3 w-48 hidden group-hover:block z-10">
                            <div class="space-y-2">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox" />
                                    <span class="ml-2 text-sm">Spring</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox" />
                                    <span class="ml-2 text-sm">Summer</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox" />
                                    <span class="ml-2 text-sm">Fall</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox" />
                                    <span class="ml-2 text-sm">Winter</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" class="custom-checkbox" />
                                    <span class="ml-2 text-sm">Year-round</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <!-- Quick Filter Pills -->
                    <button
                        class="filter-pill px-4 py-2 bg-white border border-gray-200 rounded-full text-sm hover:bg-gray-50 transition">
                        Popular
                    </button>
                    <button
                        class="filter-pill px-4 py-2 bg-white border border-gray-200 rounded-full text-sm hover:bg-gray-50 transition">
                        Trending
                    </button>
                    <button class="filter-pill active px-4 py-2 rounded-full text-sm">
                        Featured
                    </button>
                    <button
                        class="filter-pill px-4 py-2 bg-white border border-gray-200 rounded-full text-sm hover:bg-gray-50 transition">
                        Off the Beaten Path
                    </button>
                    <!-- Sort -->
                    <div class="relative ml-auto">
                        <select
                            class="pl-4 pr-8 py-2 border border-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-primary/20 text-sm appearance-none bg-white">
                            <option value="popular">Sort: Popular</option>
                            <option value="a-z">Sort: A-Z</option>
                            <option value="z-a">Sort: Z-A</option>
                            <option value="price-low">Sort: Price Low-High</option>
                            <option value="price-high">Sort: Price High-Low</option>
                        </select>
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                            <i class="ri-arrow-down-s-line text-gray-500"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Main Content -->
<div class="container mx-auto px-4 py-8">
    <!-- Featured Destinations -->
    <section class="mb-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold">Featured Destinations</h2>
            <a href="#" class="text-primary font-medium flex items-center hover:underline">
                <span>View all</span>
                <i class="ri-arrow-right-line ml-1"></i>
            </a>
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
    <!-- Asia Destinations -->
    <section class="mb-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold">Asia</h2>
            <a href="#" class="text-primary font-medium flex items-center hover:underline">
                <span>View all</span>
                <i class="ri-arrow-right-line ml-1"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- Asia Destination Card 1 -->
            <div class="destination-card bg-white rounded-lg overflow-hidden shadow-sm">
                <div class="relative h-56 overflow-hidden">
                    <img src="#api/search-image?query=beautiful%2520Tokyo%2520Japan%2520cityscape%2520with%2520modern%2520skyscrapers%252C%2520traditional%2520temples%252C%2520and%2520Mount%2520Fuji%2520in%2520background%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=600&height=400&seq=105&orientation=landscape"
                        alt="Tokyo" class="w-full h-full object-cover object-top" />
                    <div
                        class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white/80 backdrop-blur-sm rounded-full text-gray-700 hover:text-primary cursor-pointer transition">
                        <i class="ri-heart-line"></i>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-lg">Tokyo</h3>
                        <span class="text-sm text-gray-600">Japan</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                        Ultramodern metropolis with ancient temples, vibrant street
                        culture, and world-class cuisine.
                    </p>
                    <div class="mb-3">
                        <h4 class="text-sm font-medium mb-1">Popular Attractions:</h4>
                        <ul class="text-gray-600 text-sm space-y-1">
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Shibuya Crossing</span>
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Tokyo Skytree</span>
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Meiji Shrine</span>
                            </li>
                        </ul>
                    </div>
                    <div class="flex items-center mb-4">
                        <div class="w-5 h-5 flex items-center justify-center text-primary mr-1">
                            <i class="ri-calendar-line"></i>
                        </div>
                        <span class="text-sm text-gray-600">Best time: March - May, Sep - Nov</span>
                    </div>
                    <a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/b9b6daf9-df56-4396-a606-2678ca962433"
                        data-readdy="true"
                        class="block w-full bg-primary text-white text-center px-4 py-2 !rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition">View
                        Tours</a>
                </div>
            </div>
            <!-- Asia Destination Card 2 -->
            <div class="destination-card bg-white rounded-lg overflow-hidden shadow-sm">
                <div class="relative h-56 overflow-hidden">
                    <img src="#api/search-image?query=beautiful%2520Bangkok%2520Thailand%2520with%2520ornate%2520temples%252C%2520bustling%2520street%2520markets%252C%2520and%2520river%2520life%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition%252C%2520cultural%2520landmarks&width=600&height=400&seq=106&orientation=landscape"
                        alt="Bangkok" class="w-full h-full object-cover object-top" />
                    <div
                        class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white/80 backdrop-blur-sm rounded-full text-gray-700 hover:text-primary cursor-pointer transition">
                        <i class="ri-heart-line"></i>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-lg">Bangkok</h3>
                        <span class="text-sm text-gray-600">Thailand</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                        Vibrant capital with ornate shrines, bustling street markets,
                        and a blend of traditional and modern culture.
                    </p>
                    <div class="mb-3">
                        <h4 class="text-sm font-medium mb-1">Popular Attractions:</h4>
                        <ul class="text-gray-600 text-sm space-y-1">
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Grand Palace</span>
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Wat Arun</span>
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Chatuchak Weekend Market</span>
                            </li>
                        </ul>
                    </div>
                    <div class="flex items-center mb-4">
                        <div class="w-5 h-5 flex items-center justify-center text-primary mr-1">
                            <i class="ri-calendar-line"></i>
                        </div>
                        <span class="text-sm text-gray-600">Best time: November - March</span>
                    </div>
                    <a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/b9b6daf9-df56-4396-a606-2678ca962433"
                        data-readdy="true"
                        class="block w-full bg-primary text-white text-center px-4 py-2 !rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition">View
                        Tours</a>
                </div>
            </div>
            <!-- Asia Destination Card 3 -->
            <div class="destination-card bg-white rounded-lg overflow-hidden shadow-sm">
                <div class="relative h-56 overflow-hidden">
                    <img src="#api/search-image?query=beautiful%2520Maldives%2520with%2520overwater%2520bungalows%252C%2520crystal%2520clear%2520turquoise%2520water%252C%2520white%2520sand%2520beaches%252C%2520coral%2520reefs%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=600&height=400&seq=107&orientation=landscape"
                        alt="Maldives" class="w-full h-full object-cover object-top" />
                    <div
                        class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white/80 backdrop-blur-sm rounded-full text-gray-700 hover:text-primary cursor-pointer transition">
                        <i class="ri-heart-line"></i>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-lg">Maldives</h3>
                        <span class="text-sm text-gray-600">Maldives</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                        Tropical paradise with overwater bungalows, crystal-clear
                        lagoons, and vibrant coral reefs.
                    </p>
                    <div class="mb-3">
                        <h4 class="text-sm font-medium mb-1">Popular Attractions:</h4>
                        <ul class="text-gray-600 text-sm space-y-1">
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Underwater Diving</span>
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Bioluminescent Beaches</span>
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Malé City Tour</span>
                            </li>
                        </ul>
                    </div>
                    <div class="flex items-center mb-4">
                        <div class="w-5 h-5 flex items-center justify-center text-primary mr-1">
                            <i class="ri-calendar-line"></i>
                        </div>
                        <span class="text-sm text-gray-600">Best time: November - April</span>
                    </div>
                    <a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/b9b6daf9-df56-4396-a606-2678ca962433"
                        data-readdy="true"
                        class="block w-full bg-primary text-white text-center px-4 py-2 !rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition">View
                        Tours</a>
                </div>
            </div>
            <!-- Asia Destination Card 4 -->
            <div class="destination-card bg-white rounded-lg overflow-hidden shadow-sm">
                <div class="relative h-56 overflow-hidden">
                    <img src="#api/search-image?query=beautiful%2520Singapore%2520skyline%2520with%2520Marina%2520Bay%2520Sands%252C%2520Gardens%2520by%2520the%2520Bay%252C%2520and%2520modern%2520architecture%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=600&height=400&seq=108&orientation=landscape"
                        alt="Singapore" class="w-full h-full object-cover object-top" />
                    <div
                        class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white/80 backdrop-blur-sm rounded-full text-gray-700 hover:text-primary cursor-pointer transition">
                        <i class="ri-heart-line"></i>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-lg">Singapore</h3>
                        <span class="text-sm text-gray-600">Singapore</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                        Modern city-state with futuristic architecture, lush gardens,
                        and a melting pot of cultures.
                    </p>
                    <div class="mb-3">
                        <h4 class="text-sm font-medium mb-1">Popular Attractions:</h4>
                        <ul class="text-gray-600 text-sm space-y-1">
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Gardens by the Bay</span>
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Marina Bay Sands</span>
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Sentosa Island</span>
                            </li>
                        </ul>
                    </div>
                    <div class="flex items-center mb-4">
                        <div class="w-5 h-5 flex items-center justify-center text-primary mr-1">
                            <i class="ri-calendar-line"></i>
                        </div>
                        <span class="text-sm text-gray-600">Best time: Year-round</span>
                    </div>
                    <a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/b9b6daf9-df56-4396-a606-2678ca962433"
                        data-readdy="true"
                        class="block w-full bg-primary text-white text-center px-4 py-2 !rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition">View
                        Tours</a>
                </div>
            </div>
        </div>
    </section>
    <!-- Europe Destinations -->
    <section class="mb-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold">Europe</h2>
            <a href="#" class="text-primary font-medium flex items-center hover:underline">
                <span>View all</span>
                <i class="ri-arrow-right-line ml-1"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- Europe Destination Card 1 -->
            <div class="destination-card bg-white rounded-lg overflow-hidden shadow-sm">
                <div class="relative h-56 overflow-hidden">
                    <img src="#api/search-image?query=beautiful%2520Paris%2520France%2520with%2520Eiffel%2520Tower%252C%2520Seine%2520River%252C%2520and%2520historic%2520architecture%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition%252C%2520romantic%2520atmosphere&width=600&height=400&seq=109&orientation=landscape"
                        alt="Paris" class="w-full h-full object-cover object-top" />
                    <div
                        class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white/80 backdrop-blur-sm rounded-full text-gray-700 hover:text-primary cursor-pointer transition">
                        <i class="ri-heart-line"></i>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-lg">Paris</h3>
                        <span class="text-sm text-gray-600">France</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                        Romantic capital with iconic landmarks, world-class museums, and
                        charming boulevards.
                    </p>
                    <div class="mb-3">
                        <h4 class="text-sm font-medium mb-1">Popular Attractions:</h4>
                        <ul class="text-gray-600 text-sm space-y-1">
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Eiffel Tower</span>
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Louvre Museum</span>
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Notre-Dame Cathedral</span>
                            </li>
                        </ul>
                    </div>
                    <div class="flex items-center mb-4">
                        <div class="w-5 h-5 flex items-center justify-center text-primary mr-1">
                            <i class="ri-calendar-line"></i>
                        </div>
                        <span class="text-sm text-gray-600">Best time: April - June, Sep - Oct</span>
                    </div>
                    <a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/b9b6daf9-df56-4396-a606-2678ca962433"
                        data-readdy="true"
                        class="block w-full bg-primary text-white text-center px-4 py-2 !rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition">View
                        Tours</a>
                </div>
            </div>
            <!-- Europe Destination Card 2 -->
            <div class="destination-card bg-white rounded-lg overflow-hidden shadow-sm">
                <div class="relative h-56 overflow-hidden">
                    <img src="#api/search-image?query=beautiful%2520Venice%2520Italy%2520with%2520canals%252C%2520gondolas%252C%2520historic%2520bridges%252C%2520and%2520Renaissance%2520architecture%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=600&height=400&seq=110&orientation=landscape"
                        alt="Venice" class="w-full h-full object-cover object-top" />
                    <div
                        class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white/80 backdrop-blur-sm rounded-full text-gray-700 hover:text-primary cursor-pointer transition">
                        <i class="ri-heart-line"></i>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-lg">Venice</h3>
                        <span class="text-sm text-gray-600">Italy</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                        Romantic city built on a lagoon with a labyrinth of canals,
                        historic bridges, and Renaissance architecture.
                    </p>
                    <div class="mb-3">
                        <h4 class="text-sm font-medium mb-1">Popular Attractions:</h4>
                        <ul class="text-gray-600 text-sm space-y-1">
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>St. Mark's Square</span>
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Grand Canal</span>
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Rialto Bridge</span>
                            </li>
                        </ul>
                    </div>
                    <div class="flex items-center mb-4">
                        <div class="w-5 h-5 flex items-center justify-center text-primary mr-1">
                            <i class="ri-calendar-line"></i>
                        </div>
                        <span class="text-sm text-gray-600">Best time: April - June, Sep - Oct</span>
                    </div>
                    <a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/b9b6daf9-df56-4396-a606-2678ca962433"
                        data-readdy="true"
                        class="block w-full bg-primary text-white text-center px-4 py-2 !rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition">View
                        Tours</a>
                </div>
            </div>
            <!-- Europe Destination Card 3 -->
            <div class="destination-card bg-white rounded-lg overflow-hidden shadow-sm">
                <div class="relative h-56 overflow-hidden">
                    <img src="#api/search-image?query=beautiful%2520Barcelona%2520Spain%2520with%2520Sagrada%2520Familia%252C%2520Park%2520Guell%252C%2520Mediterranean%2520beaches%252C%2520and%2520Gothic%2520Quarter%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=600&height=400&seq=111&orientation=landscape"
                        alt="Barcelona" class="w-full h-full object-cover object-top" />
                    <div
                        class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white/80 backdrop-blur-sm rounded-full text-gray-700 hover:text-primary cursor-pointer transition">
                        <i class="ri-heart-line"></i>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-lg">Barcelona</h3>
                        <span class="text-sm text-gray-600">Spain</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                        Vibrant coastal city with unique architecture, Mediterranean
                        beaches, and lively cultural scene.
                    </p>
                    <div class="mb-3">
                        <h4 class="text-sm font-medium mb-1">Popular Attractions:</h4>
                        <ul class="text-gray-600 text-sm space-y-1">
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Sagrada Familia</span>
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Park Güell</span>
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>La Rambla</span>
                            </li>
                        </ul>
                    </div>
                    <div class="flex items-center mb-4">
                        <div class="w-5 h-5 flex items-center justify-center text-primary mr-1">
                            <i class="ri-calendar-line"></i>
                        </div>
                        <span class="text-sm text-gray-600">Best time: May - June, Sep - Oct</span>
                    </div>
                    <a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/b9b6daf9-df56-4396-a606-2678ca962433"
                        data-readdy="true"
                        class="block w-full bg-primary text-white text-center px-4 py-2 !rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition">View
                        Tours</a>
                </div>
            </div>
            <!-- Europe Destination Card 4 -->
            <div class="destination-card bg-white rounded-lg overflow-hidden shadow-sm">
                <div class="relative h-56 overflow-hidden">
                    <img src="#api/search-image?query=beautiful%2520Swiss%2520Alps%2520with%2520snow-capped%2520mountains%252C%2520alpine%2520lakes%252C%2520green%2520valleys%252C%2520and%2520traditional%2520chalets%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=600&height=400&seq=112&orientation=landscape"
                        alt="Swiss Alps" class="w-full h-full object-cover object-top" />
                    <div
                        class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white/80 backdrop-blur-sm rounded-full text-gray-700 hover:text-primary cursor-pointer transition">
                        <i class="ri-heart-line"></i>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-lg">Swiss Alps</h3>
                        <span class="text-sm text-gray-600">Switzerland</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                        Majestic mountain range with snow-capped peaks, alpine lakes,
                        and world-class ski resorts.
                    </p>
                    <div class="mb-3">
                        <h4 class="text-sm font-medium mb-1">Popular Attractions:</h4>
                        <ul class="text-gray-600 text-sm space-y-1">
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Matterhorn</span>
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Jungfrau Region</span>
                            </li>
                            <li class="flex items-start">
                                <i class="ri-check-line text-primary mr-1 mt-0.5"></i>
                                <span>Lake Geneva</span>
                            </li>
                        </ul>
                    </div>
                    <div class="flex items-center mb-4">
                        <div class="w-5 h-5 flex items-center justify-center text-primary mr-1">
                            <i class="ri-calendar-line"></i>
                        </div>
                        <span class="text-sm text-gray-600">Best time: Dec - Mar (winter), Jun - Sep (summer)</span>
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
    <section class="mb-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold">Explore by Travel Theme</h2>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <!-- Theme Card 1 -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden group hover:shadow-md transition">
                <div class="h-32 overflow-hidden relative">
                    <img src="#api/search-image?query=adventure%2520travel%2520theme%2520with%2520hiking%252C%2520mountain%2520climbing%252C%2520and%2520outdoor%2520activities%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=300&height=200&seq=113&orientation=landscape"
                        alt="Adventure"
                        class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-500" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <h3 class="absolute bottom-3 left-3 text-white font-semibold">
                        Adventure
                    </h3>
                </div>
            </div>
            <!-- Theme Card 2 -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden group hover:shadow-md transition">
                <div class="h-32 overflow-hidden relative">
                    <img src="#api/search-image?query=beach%2520vacation%2520theme%2520with%2520tropical%2520beaches%252C%2520palm%2520trees%252C%2520and%2520clear%2520blue%2520water%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=300&height=200&seq=114&orientation=landscape"
                        alt="Beach"
                        class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-500" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <h3 class="absolute bottom-3 left-3 text-white font-semibold">
                        Beach
                    </h3>
                </div>
            </div>
            <!-- Theme Card 3 -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden group hover:shadow-md transition">
                <div class="h-32 overflow-hidden relative">
                    <img src="#api/search-image?query=cultural%2520travel%2520theme%2520with%2520historic%2520sites%252C%2520temples%252C%2520museums%252C%2520and%2520local%2520traditions%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=300&height=200&seq=115&orientation=landscape"
                        alt="Cultural"
                        class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-500" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <h3 class="absolute bottom-3 left-3 text-white font-semibold">
                        Cultural
                    </h3>
                </div>
            </div>
            <!-- Theme Card 4 -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden group hover:shadow-md transition">
                <div class="h-32 overflow-hidden relative">
                    <img src="#api/search-image?query=wildlife%2520safari%2520theme%2520with%2520animals%2520in%2520natural%2520habitat%252C%2520savanna%2520landscapes%252C%2520and%2520nature%2520reserves%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=300&height=200&seq=116&orientation=landscape"
                        alt="Wildlife"
                        class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-500" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <h3 class="absolute bottom-3 left-3 text-white font-semibold">
                        Wildlife
                    </h3>
                </div>
            </div>
            <!-- Theme Card 5 -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden group hover:shadow-md transition">
                <div class="h-32 overflow-hidden relative">
                    <img src="#api/search-image?query=food%2520and%2520culinary%2520travel%2520theme%2520with%2520local%2520cuisine%252C%2520markets%252C%2520cooking%2520classes%252C%2520and%2520food%2520tours%252C%2520professional%2520food%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=300&height=200&seq=117&orientation=landscape"
                        alt="Food & Culinary"
                        class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-500" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <h3 class="absolute bottom-3 left-3 text-white font-semibold">
                        Food & Culinary
                    </h3>
                </div>
            </div>
            <!-- Theme Card 6 -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden group hover:shadow-md transition">
                <div class="h-32 overflow-hidden relative">
                    <img src="#api/search-image?query=wellness%2520and%2520spa%2520travel%2520theme%2520with%2520yoga%2520retreats%252C%2520meditation%2520spaces%252C%2520hot%2520springs%252C%2520and%2520relaxation%252C%2520professional%2520travel%2520photography%2520with%2520serene%2520atmosphere&width=300&height=200&seq=118&orientation=landscape"
                        alt="Wellness"
                        class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-500" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <h3 class="absolute bottom-3 left-3 text-white font-semibold">
                        Wellness
                    </h3>
                </div>
            </div>
        </div>
    </section>
    <!-- Trending Destinations -->
    <section class="mb-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold">Trending Destinations</h2>
            <a href="#" class="text-primary font-medium flex items-center hover:underline">
                <span>View all</span>
                <i class="ri-arrow-right-line ml-1"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Trending Destination 1 -->
            <div class="destination-card bg-white rounded-lg overflow-hidden shadow-sm">
                <div class="relative h-64 overflow-hidden">
                    <img src="#api/search-image?query=beautiful%2520Cappadocia%2520Turkey%2520with%2520hot%2520air%2520balloons%2520over%2520unique%2520rock%2520formations%252C%2520cave%2520dwellings%252C%2520and%2520fairy%2520chimneys%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=600&height=500&seq=119&orientation=portrait"
                        alt="Cappadocia" class="w-full h-full object-cover object-top" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 text-white">
                        <h3 class="font-bold text-xl mb-1">Cappadocia</h3>
                        <p class="text-white/90">Turkey</p>
                    </div>
                    <div
                        class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm text-primary text-xs font-medium px-2 py-1 rounded-full">
                        Trending
                    </div>
                </div>
            </div>
            <!-- Trending Destination 2 -->
            <div class="destination-card bg-white rounded-lg overflow-hidden shadow-sm">
                <div class="relative h-64 overflow-hidden">
                    <img src="#api/search-image?query=beautiful%2520Amalfi%2520Coast%2520Italy%2520with%2520colorful%2520cliffside%2520villages%252C%2520Mediterranean%2520Sea%252C%2520and%2520winding%2520coastal%2520roads%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=600&height=500&seq=120&orientation=portrait"
                        alt="Amalfi Coast" class="w-full h-full object-cover object-top" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 text-white">
                        <h3 class="font-bold text-xl mb-1">Amalfi Coast</h3>
                        <p class="text-white/90">Italy</p>
                    </div>
                    <div
                        class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm text-primary text-xs font-medium px-2 py-1 rounded-full">
                        Trending
                    </div>
                </div>
            </div>
            <!-- Trending Destination 3 -->
            <div class="destination-card bg-white rounded-lg overflow-hidden shadow-sm">
                <div class="relative h-64 overflow-hidden">
                    <img src="#api/search-image?query=beautiful%2520Marrakech%2520Morocco%2520with%2520colorful%2520markets%252C%2520historic%2520medina%252C%2520ornate%2520palaces%252C%2520and%2520traditional%2520riads%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=600&height=500&seq=121&orientation=portrait"
                        alt="Marrakech" class="w-full h-full object-cover object-top" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 text-white">
                        <h3 class="font-bold text-xl mb-1">Marrakech</h3>
                        <p class="text-white/90">Morocco</p>
                    </div>
                    <div
                        class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm text-primary text-xs font-medium px-2 py-1 rounded-full">
                        Trending
                    </div>
                </div>
            </div>
        </div>
    </section>
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