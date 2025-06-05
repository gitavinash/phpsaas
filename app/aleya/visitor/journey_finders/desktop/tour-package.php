<?php

require_once 'header.php';

?>
<!-- Main Content -->
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumbs -->
    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-500">
            <a href="#" class="hover:text-primary">Home</a>
            <span class="mx-2">/</span>
            <a href="#" class="hover:text-primary">Holiday</a>
            <span class="mx-2">/</span>
            <a href="#" class="hover:text-primary">Bali</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700 font-medium">Bali Cultural Explorer</span>
        </div>
    </div>
    <!-- Hero Section -->
    <div class="relative rounded-xl overflow-hidden mb-8">
        <img src="<?php echo USER_IMAGES ?>city/bali.jpg" alt="Bali Cultural Explorer"
            class="w-full h-80 object-cover object-top" />
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
        <div class="absolute bottom-0 left-0 p-6 md:p-8">
            <div class="flex items-center mb-2">
                <span class="bg-primary text-white text-xs font-semibold px-2 py-1 rounded-full mr-2">Featured</span>
                <span class="bg-white/90 backdrop-blur-sm text-gray-800 text-xs font-semibold px-2 py-1 rounded-full">10
                    Days</span>
            </div>
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                Bali Cultural Explorer
            </h1>
            <div class="flex items-center text-white/90 mb-4">
                <div class="flex items-center mr-4">
                    <div class="w-5 h-5 flex items-center justify-center mr-1">
                        <i class="ri-map-pin-line"></i>
                    </div>
                    <span>Bali, Indonesia</span>
                </div>
                <div class="flex items-center mr-4">
                    <div class="w-5 h-5 flex items-center justify-center mr-1">
                        <i class="ri-group-line"></i>
                    </div>
                    <span>Min 2 people</span>
                </div>
                <div class="flex items-center">
                    <div class="w-5 h-5 flex items-center justify-center mr-1">
                        <i class="ri-star-fill"></i>
                    </div>
                    <span>4.9 (128 reviews)</span>
                </div>
            </div>
            <div class="flex items-center">
                <div class="text-white mr-4">
                    <span class="text-lg font-bold">₹ 15,899</span>
                    <span class="text-white/80 text-sm">per person</span>
                </div>
                <a href="#enquiry-form"
                    class="bg-primary text-white px-6 py-2 !rounded-button whitespace-nowrap font-medium hover:bg-primary/90 transition">Enquire
                    Now</a>
            </div>
        </div>
    </div>
    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Tour Details -->
        <div class="lg:col-span-2">
            <!-- Overview Section -->
            <section class="bg-white rounded-xl shadow-sm p-6 mb-8">
                <h2 class="text-2xl font-bold mb-4">Tour Overview</h2>
                <p class="text-gray-700 mb-6">
                    Experience the best of Bali's rich cultural heritage and natural
                    beauty on this comprehensive 10-day journey. From ancient temples
                    and traditional villages to stunning rice terraces and pristine
                    beaches, this tour offers an authentic insight into the island's
                    unique culture and breathtaking landscapes.
                </p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="w-10 h-10 flex items-center justify-center text-primary mb-2">
                            <i class="ri-calendar-line ri-xl"></i>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900">Duration</h3>
                        <p class="text-gray-600">10 Days / 9 Nights</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="w-10 h-10 flex items-center justify-center text-primary mb-2">
                            <i class="ri-group-line ri-xl"></i>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900">Group Size</h3>
                        <p class="text-gray-600">2-12 People</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="w-10 h-10 flex items-center justify-center text-primary mb-2">
                            <i class="ri-hotel-line ri-xl"></i>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900">Accommodation</h3>
                        <p class="text-gray-600">4-Star Hotels</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="w-10 h-10 flex items-center justify-center text-primary mb-2">
                            <i class="ri-translate-2 ri-xl"></i>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900">Languages</h3>
                        <p class="text-gray-600">English, Indonesian</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-1 rounded">Cultural</span>
                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-1 rounded">Adventure</span>
                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-1 rounded">Nature</span>
                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-1 rounded">Beach</span>
                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-1 rounded">Wellness</span>
                </div>
            </section>
            <!-- Highlights Section -->
            <section class="bg-white rounded-xl shadow-sm p-6 mb-8">
                <h2 class="text-2xl font-bold mb-4">Tour Highlights</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-start">
                        <div
                            class="w-8 h-8 flex items-center justify-center bg-primary/10 rounded-full text-primary mr-3 mt-0.5 flex-shrink-0">
                            <i class="ri-check-line ri-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">
                                Ubud Cultural Immersion
                            </h3>
                            <p class="text-gray-600 text-sm">
                                Explore Bali's cultural heart with visits to art galleries,
                                craft villages, and traditional performances.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div
                            class="w-8 h-8 flex items-center justify-center bg-primary/10 rounded-full text-primary mr-3 mt-0.5 flex-shrink-0">
                            <i class="ri-check-line ri-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">Sacred Temple Tour</h3>
                            <p class="text-gray-600 text-sm">
                                Visit Bali's most important temples including Besakih,
                                Uluwatu, and Tanah Lot.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div
                            class="w-8 h-8 flex items-center justify-center bg-primary/10 rounded-full text-primary mr-3 mt-0.5 flex-shrink-0">
                            <i class="ri-check-line ri-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">Rice Terrace Trek</h3>
                            <p class="text-gray-600 text-sm">
                                Walk through the stunning UNESCO-listed Tegalalang and
                                Jatiluwih rice terraces.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div
                            class="w-8 h-8 flex items-center justify-center bg-primary/10 rounded-full text-primary mr-3 mt-0.5 flex-shrink-0">
                            <i class="ri-check-line ri-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">
                                Balinese Cooking Class
                            </h3>
                            <p class="text-gray-600 text-sm">
                                Learn to prepare authentic Balinese dishes using traditional
                                techniques and fresh local ingredients.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div
                            class="w-8 h-8 flex items-center justify-center bg-primary/10 rounded-full text-primary mr-3 mt-0.5 flex-shrink-0">
                            <i class="ri-check-line ri-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">
                                Mount Batur Sunrise Trek
                            </h3>
                            <p class="text-gray-600 text-sm">
                                Early morning hike to witness a spectacular sunrise from an
                                active volcano.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div
                            class="w-8 h-8 flex items-center justify-center bg-primary/10 rounded-full text-primary mr-3 mt-0.5 flex-shrink-0">
                            <i class="ri-check-line ri-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">
                                Relaxation in Nusa Dua
                            </h3>
                            <p class="text-gray-600 text-sm">
                                Unwind on pristine beaches and enjoy luxury spa treatments
                                in Bali's premier resort area.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Itinerary Section -->
            <section class="bg-white rounded-xl shadow-sm p-6 mb-8">
                <h2 class="text-2xl font-bold mb-6">Day-by-Day Itinerary</h2>
                <div class="space-y-4">
                    <!-- Day 1 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div
                            class="itinerary-accordion flex items-center justify-between bg-gray-50 p-4 cursor-pointer">
                            <div class="flex items-center">
                                <div
                                    class="w-8 h-8 flex items-center justify-center bg-primary text-white rounded-full mr-3 flex-shrink-0">
                                    <span class="text-sm font-medium">1</span>
                                </div>
                                <h3 class="font-medium">
                                    Arrival in Denpasar & Transfer to Ubud
                                </h3>
                            </div>
                            <div class="w-6 h-6 flex items-center justify-center text-gray-500">
                                <i class="ri-arrow-down-s-line ri-lg accordion-icon"></i>
                            </div>
                        </div>
                        <div class="itinerary-accordion-content p-4 border-t border-gray-200">
                            <p class="text-gray-700 mb-4">
                                Welcome to Bali! Upon arrival at Ngurah Rai International
                                Airport in Denpasar, you'll be greeted by your tour guide
                                and transferred to your hotel in Ubud, Bali's cultural
                                heart. After check-in, enjoy a welcome dinner featuring
                                traditional Balinese cuisine while being entertained by a
                                classical dance performance.
                            </p>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <div class="w-5 h-5 flex items-center justify-center text-primary mr-1">
                                        <i class="ri-hotel-line"></i>
                                    </div>
                                    <span>Accommodation: Alila Ubud or similar</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-5 h-5 flex items-center justify-center text-primary mr-1">
                                        <i class="ri-restaurant-line"></i>
                                    </div>
                                    <span>Meals: Dinner</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Day 2 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div
                            class="itinerary-accordion flex items-center justify-between bg-gray-50 p-4 cursor-pointer">
                            <div class="flex items-center">
                                <div
                                    class="w-8 h-8 flex items-center justify-center bg-primary text-white rounded-full mr-3 flex-shrink-0">
                                    <span class="text-sm font-medium">2</span>
                                </div>
                                <h3 class="font-medium">Ubud Cultural Exploration</h3>
                            </div>
                            <div class="w-6 h-6 flex items-center justify-center text-gray-500">
                                <i class="ri-arrow-down-s-line ri-lg accordion-icon"></i>
                            </div>
                        </div>
                        <div class="itinerary-accordion-content p-4 border-t border-gray-200">
                            <p class="text-gray-700 mb-4">
                                Spend the day exploring Ubud's cultural highlights. Visit
                                the Sacred Monkey Forest Sanctuary, Ubud Palace, and the
                                Ubud Art Market. In the afternoon, tour the Agung Rai Museum
                                of Art (ARMA) to learn about Balinese painting traditions.
                                End the day with a visit to a traditional Balinese compound
                                to understand local architecture and family life.
                            </p>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <div class="w-5 h-5 flex items-center justify-center text-primary mr-1">
                                        <i class="ri-hotel-line"></i>
                                    </div>
                                    <span>Accommodation: Alila Ubud or similar</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-5 h-5 flex items-center justify-center text-primary mr-1">
                                        <i class="ri-restaurant-line"></i>
                                    </div>
                                    <span>Meals: Breakfast, Lunch</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Day 3 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div
                            class="itinerary-accordion flex items-center justify-between bg-gray-50 p-4 cursor-pointer">
                            <div class="flex items-center">
                                <div
                                    class="w-8 h-8 flex items-center justify-center bg-primary text-white rounded-full mr-3 flex-shrink-0">
                                    <span class="text-sm font-medium">3</span>
                                </div>
                                <h3 class="font-medium">
                                    Tegalalang Rice Terraces & Craft Villages
                                </h3>
                            </div>
                            <div class="w-6 h-6 flex items-center justify-center text-gray-500">
                                <i class="ri-arrow-down-s-line ri-lg accordion-icon"></i>
                            </div>
                        </div>
                        <div class="itinerary-accordion-content p-4 border-t border-gray-200">
                            <p class="text-gray-700 mb-4">
                                This morning, visit the stunning Tegalalang Rice Terraces
                                for a guided walk through the emerald-green fields. Learn
                                about the ancient Subak irrigation system that has sustained
                                these terraces for centuries. In the afternoon, tour the
                                surrounding craft villages, each specializing in different
                                traditional arts: Mas for wood carving, Celuk for silver
                                jewelry, and Batuan for traditional painting. Participate in
                                a hands-on craft workshop of your choice.
                            </p>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <div class="w-5 h-5 flex items-center justify-center text-primary mr-1">
                                        <i class="ri-hotel-line"></i>
                                    </div>
                                    <span>Accommodation: Alila Ubud or similar</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-5 h-5 flex items-center justify-center text-primary mr-1">
                                        <i class="ri-restaurant-line"></i>
                                    </div>
                                    <span>Meals: Breakfast, Lunch</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Day 4 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div
                            class="itinerary-accordion flex items-center justify-between bg-gray-50 p-4 cursor-pointer">
                            <div class="flex items-center">
                                <div
                                    class="w-8 h-8 flex items-center justify-center bg-primary text-white rounded-full mr-3 flex-shrink-0">
                                    <span class="text-sm font-medium">4</span>
                                </div>
                                <h3 class="font-medium">
                                    Balinese Cooking Class & Tirta Empul Temple
                                </h3>
                            </div>
                            <div class="w-6 h-6 flex items-center justify-center text-gray-500">
                                <i class="ri-arrow-down-s-line ri-lg accordion-icon"></i>
                            </div>
                        </div>
                        <div class="itinerary-accordion-content p-4 border-t border-gray-200">
                            <p class="text-gray-700 mb-4">
                                Begin your day with a visit to a local market to select
                                fresh ingredients for your cooking class. Learn to prepare
                                authentic Balinese dishes under the guidance of an expert
                                chef. After enjoying your culinary creations for lunch,
                                visit the sacred Tirta Empul Temple, known for its holy
                                spring water where Balinese Hindus perform purification
                                rituals. End the day with a relaxing spa treatment featuring
                                traditional Balinese techniques and natural ingredients.
                            </p>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <div class="w-5 h-5 flex items-center justify-center text-primary mr-1">
                                        <i class="ri-hotel-line"></i>
                                    </div>
                                    <span>Accommodation: Alila Ubud or similar</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-5 h-5 flex items-center justify-center text-primary mr-1">
                                        <i class="ri-restaurant-line"></i>
                                    </div>
                                    <span>Meals: Breakfast, Lunch</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Day 5 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div
                            class="itinerary-accordion flex items-center justify-between bg-gray-50 p-4 cursor-pointer">
                            <div class="flex items-center">
                                <div
                                    class="w-8 h-8 flex items-center justify-center bg-primary text-white rounded-full mr-3 flex-shrink-0">
                                    <span class="text-sm font-medium">5</span>
                                </div>
                                <h3 class="font-medium">
                                    Mount Batur Sunrise Trek & Hot Springs
                                </h3>
                            </div>
                            <div class="w-6 h-6 flex items-center justify-center text-gray-500">
                                <i class="ri-arrow-down-s-line ri-lg accordion-icon"></i>
                            </div>
                        </div>
                        <div class="itinerary-accordion-content p-4 border-t border-gray-200">
                            <p class="text-gray-700 mb-4">
                                Early morning departure (around 2:30 AM) for a guided trek
                                up Mount Batur, an active volcano. Reach the summit (1,717m)
                                in time to witness a spectacular sunrise over Mount Agung
                                and the surrounding landscape. Enjoy a light breakfast at
                                the top before descending. Afterward, soothe your muscles
                                with a visit to natural hot springs overlooking Lake Batur.
                                Return to your hotel for some rest before an evening visit
                                to a local village to witness a traditional fire dance
                                performance.
                            </p>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <div class="w-5 h-5 flex items-center justify-center text-primary mr-1">
                                        <i class="ri-hotel-line"></i>
                                    </div>
                                    <span>Accommodation: Alila Ubud or similar</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-5 h-5 flex items-center justify-center text-primary mr-1">
                                        <i class="ri-restaurant-line"></i>
                                    </div>
                                    <span>Meals: Breakfast, Lunch</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Show More Button -->
                    <button id="show-more-itinerary"
                        class="w-full py-3 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition flex items-center justify-center">
                        <span>Show Full Itinerary</span>
                        <i class="ri-arrow-down-s-line ml-1"></i>
                    </button>
                </div>
            </section>
            <!-- Photo Gallery -->
            <section class="bg-white rounded-xl shadow-sm p-6 mb-8">
                <h2 class="text-2xl font-bold mb-4">Photo Gallery</h2>
                <div class="overflow-x-auto gallery-scrollbar pb-4">
                    <div class="flex space-x-4 min-w-max">
                        <div class="w-80 h-60 rounded-lg overflow-hidden">
                            <img src="#api/search-image?query=beautiful%2520Bali%2520Ubud%2520rice%2520terraces%2520with%2520traditional%2520irrigation%2520system%252C%2520lush%2520green%2520landscape%252C%2520perfect%2520sunny%2520day%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=400&height=300&seq=1002&orientation=landscape"
                                alt="Tegalalang Rice Terraces"
                                class="w-full h-full object-cover object-top hover:scale-105 transition duration-500" />
                        </div>
                        <div class="w-80 h-60 rounded-lg overflow-hidden">
                            <img src="#api/search-image?query=beautiful%2520Bali%2520Uluwatu%2520temple%2520on%2520cliff%2520edge%2520with%2520ocean%2520view%2520at%2520sunset%252C%2520traditional%2520architecture%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=400&height=300&seq=1003&orientation=landscape"
                                alt="Uluwatu Temple"
                                class="w-full h-full object-cover object-top hover:scale-105 transition duration-500" />
                        </div>
                        <div class="w-80 h-60 rounded-lg overflow-hidden">
                            <img src="#api/search-image?query=beautiful%2520Bali%2520traditional%2520dance%2520performance%2520with%2520colorful%2520costumes%252C%2520ornate%2520headdresses%252C%2520expressive%2520movements%252C%2520professional%2520cultural%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=400&height=300&seq=1004&orientation=landscape"
                                alt="Traditional Dance"
                                class="w-full h-full object-cover object-top hover:scale-105 transition duration-500" />
                        </div>
                        <div class="w-80 h-60 rounded-lg overflow-hidden">
                            <img src="#api/search-image?query=beautiful%2520Bali%2520Mount%2520Batur%2520volcano%2520sunrise%2520trek%2520with%2520panoramic%2520views%252C%2520misty%2520mountains%252C%2520golden%2520sunlight%252C%2520professional%2520landscape%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=400&height=300&seq=1005&orientation=landscape"
                                alt="Mount Batur Sunrise"
                                class="w-full h-full object-cover object-top hover:scale-105 transition duration-500" />
                        </div>
                        <div class="w-80 h-60 rounded-lg overflow-hidden">
                            <img src="#api/search-image?query=beautiful%2520Bali%2520beach%2520in%2520Nusa%2520Dua%2520with%2520white%2520sand%252C%2520turquoise%2520water%252C%2520palm%2520trees%252C%2520luxury%2520beach%2520umbrellas%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=400&height=300&seq=1006&orientation=landscape"
                                alt="Nusa Dua Beach"
                                class="w-full h-full object-cover object-top hover:scale-105 transition duration-500" />
                        </div>
                    </div>
                </div>
            </section>
            <!-- Included/Not Included -->
            <section class="bg-white rounded-xl shadow-sm p-6 mb-8">
                <h2 class="text-2xl font-bold mb-4">
                    What's Included/Not Included
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold mb-3 flex items-center text-gray-900">
                            <div class="w-6 h-6 flex items-center justify-center text-primary mr-2">
                                <i class="ri-check-line"></i>
                            </div>
                            Included
                        </h3>
                        <ul class="space-y-2">
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-primary mr-2 mt-0.5">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span class="text-gray-700">Airport transfers in air-conditioned vehicles</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-primary mr-2 mt-0.5">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span class="text-gray-700">9 nights accommodation in 4-star hotels</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-primary mr-2 mt-0.5">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span class="text-gray-700">Daily breakfast, 7 lunches, and 3 dinners</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-primary mr-2 mt-0.5">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span class="text-gray-700">English-speaking professional guide</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-primary mr-2 mt-0.5">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span class="text-gray-700">All activities and entrance fees as per itinerary</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-primary mr-2 mt-0.5">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span class="text-gray-700">Cooking class with market visit</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-primary mr-2 mt-0.5">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span class="text-gray-700">Traditional spa treatment</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-primary mr-2 mt-0.5">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span class="text-gray-700">Bottled water during tours</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-primary mr-2 mt-0.5">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span class="text-gray-700">All applicable taxes and fees</span>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-3 flex items-center text-gray-900">
                            <div class="w-6 h-6 flex items-center justify-center text-red-500 mr-2">
                                <i class="ri-close-line"></i>
                            </div>
                            Not Included
                        </h3>
                        <ul class="space-y-2">
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-red-500 mr-2 mt-0.5">
                                    <i class="ri-close-line"></i>
                                </div>
                                <span class="text-gray-700">International airfare to/from Bali</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-red-500 mr-2 mt-0.5">
                                    <i class="ri-close-line"></i>
                                </div>
                                <span class="text-gray-700">Travel insurance (mandatory)</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-red-500 mr-2 mt-0.5">
                                    <i class="ri-close-line"></i>
                                </div>
                                <span class="text-gray-700">Visa fees (if applicable)</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-red-500 mr-2 mt-0.5">
                                    <i class="ri-close-line"></i>
                                </div>
                                <span class="text-gray-700">Meals not mentioned in the itinerary</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-red-500 mr-2 mt-0.5">
                                    <i class="ri-close-line"></i>
                                </div>
                                <span class="text-gray-700">Personal expenses (laundry, phone calls, etc.)</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-red-500 mr-2 mt-0.5">
                                    <i class="ri-close-line"></i>
                                </div>
                                <span class="text-gray-700">Alcoholic and specialty beverages</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-red-500 mr-2 mt-0.5">
                                    <i class="ri-close-line"></i>
                                </div>
                                <span class="text-gray-700">Optional activities not mentioned in the itinerary</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-red-500 mr-2 mt-0.5">
                                    <i class="ri-close-line"></i>
                                </div>
                                <span class="text-gray-700">Tips for guides, drivers, and hotel staff</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>
            <!-- Reviews Section -->
            <section class="bg-white rounded-xl shadow-sm p-6 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold">Customer Reviews</h2>
                    <div class="flex items-center">
                        <div class="star-rating flex mr-2">
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-half-fill"></i>
                        </div>
                        <span class="font-medium">4.9</span>
                        <span class="text-gray-500 ml-1">(128 reviews)</span>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Review 1 -->
                    <div class="review-card bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <div
                                class="w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center text-primary font-medium mr-3">
                                JD
                            </div>
                            <div>
                                <h4 class="font-medium">James Davidson</h4>
                                <div class="flex items-center">
                                    <div class="star-rating flex text-sm mr-2">
                                        <i class="ri-star-fill"></i>
                                        <i class="ri-star-fill"></i>
                                        <i class="ri-star-fill"></i>
                                        <i class="ri-star-fill"></i>
                                        <i class="ri-star-fill"></i>
                                    </div>
                                    <span class="text-gray-500 text-xs">April 2, 2025</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-700 text-sm">
                            This tour exceeded all my expectations! Our guide Wayan was
                            incredibly knowledgeable about Balinese culture and took us to
                            some hidden gems that weren't on the original itinerary. The
                            cooking class was a highlight - I still make the recipes at
                            home. The accommodations were luxurious and the pace was
                            perfect.
                        </p>
                    </div>
                    <!-- Review 2 -->
                    <div class="review-card bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <div
                                class="w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center text-primary font-medium mr-3">
                                SM
                            </div>
                            <div>
                                <h4 class="font-medium">Sarah Mitchell</h4>
                                <div class="flex items-center">
                                    <div class="star-rating flex text-sm mr-2">
                                        <i class="ri-star-fill"></i>
                                        <i class="ri-star-fill"></i>
                                        <i class="ri-star-fill"></i>
                                        <i class="ri-star-fill"></i>
                                        <i class="ri-star-fill"></i>
                                    </div>
                                    <span class="text-gray-500 text-xs">March 15, 2025</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-700 text-sm">
                            The Mount Batur sunrise trek was challenging but absolutely
                            worth it! The views were breathtaking and our guide made sure
                            everyone was comfortable with the pace. I appreciated the
                            small group size which allowed for a more personalized
                            experience. The hotels were all excellent and in perfect
                            locations.
                        </p>
                    </div>
                    <!-- Review 3 -->
                    <div class="review-card bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <div
                                class="w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center text-primary font-medium mr-3">
                                RK
                            </div>
                            <div>
                                <h4 class="font-medium">Robert Kim</h4>
                                <div class="flex items-center">
                                    <div class="star-rating flex text-sm mr-2">
                                        <i class="ri-star-fill"></i>
                                        <i class="ri-star-fill"></i>
                                        <i class="ri-star-fill"></i>
                                        <i class="ri-star-fill"></i>
                                        <i class="ri-star-line"></i>
                                    </div>
                                    <span class="text-gray-500 text-xs">February 28, 2025</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-700 text-sm">
                            A well-organized tour that really captures the essence of
                            Bali. The cultural experiences were authentic and not
                            touristy. I would have preferred more free time in Ubud to
                            explore on my own, but overall it was an excellent experience.
                            The spa treatment was divine and the food throughout was
                            delicious.
                        </p>
                    </div>
                    <!-- Review 4 -->
                    <div class="review-card bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <div
                                class="w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center text-primary font-medium mr-3">
                                AP
                            </div>
                            <div>
                                <h4 class="font-medium">Amelia Patel</h4>
                                <div class="flex items-center">
                                    <div class="star-rating flex text-sm mr-2">
                                        <i class="ri-star-fill"></i>
                                        <i class="ri-star-fill"></i>
                                        <i class="ri-star-fill"></i>
                                        <i class="ri-star-fill"></i>
                                        <i class="ri-star-half-fill"></i>
                                    </div>
                                    <span class="text-gray-500 text-xs">January 17, 2025</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-700 text-sm">
                            This tour offers the perfect balance of cultural immersion and
                            relaxation. I loved learning about Balinese traditions and
                            spiritual practices. The temples were stunning and our guide
                            provided fascinating insights. The final days in Nusa Dua were
                            the perfect way to unwind after all the activities.
                        </p>
                    </div>
                </div>
                <a href="#" class="text-primary font-medium hover:underline flex items-center justify-center">
                    <span>Read all 128 reviews</span>
                    <i class="ri-arrow-right-line ml-1"></i>
                </a>
            </section>
            <!-- FAQ Section -->
            <section class="bg-white rounded-xl shadow-sm p-6 mb-8">
                <h2 class="text-2xl font-bold mb-6">Frequently Asked Questions</h2>
                <div class="space-y-4">
                    <!-- FAQ Item 1 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div
                            class="itinerary-accordion flex items-center justify-between bg-gray-50 p-4 cursor-pointer">
                            <h3 class="font-medium">
                                What is the best time to visit Bali?
                            </h3>
                            <div class="w-6 h-6 flex items-center justify-center text-gray-500">
                                <i class="ri-arrow-down-s-line ri-lg accordion-icon"></i>
                            </div>
                        </div>
                        <div class="itinerary-accordion-content p-4 border-t border-gray-200">
                            <p class="text-gray-700">
                                The best time to visit Bali is during the dry season from
                                April to October. This period offers sunny days with lower
                                humidity, ideal for outdoor activities and exploring. The
                                peak tourist season is July and August. The rainy season
                                (November to March) brings afternoon showers but also fewer
                                crowds and lush green landscapes.
                            </p>
                        </div>
                    </div>
                    <!-- FAQ Item 2 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div
                            class="itinerary-accordion flex items-center justify-between bg-gray-50 p-4 cursor-pointer">
                            <h3 class="font-medium">
                                How physically demanding is this tour?
                            </h3>
                            <div class="w-6 h-6 flex items-center justify-center text-gray-500">
                                <i class="ri-arrow-down-s-line ri-lg accordion-icon"></i>
                            </div>
                        </div>
                        <div class="itinerary-accordion-content p-4 border-t border-gray-200">
                            <p class="text-gray-700">
                                This tour has a moderate activity level. The Mount Batur
                                trek is the most physically demanding activity, requiring a
                                moderate fitness level for a 2-hour uphill hike in the dark.
                                The rice terrace walks involve some uneven terrain and
                                steps. Most other activities are not physically strenuous.
                                Alternative activities can be arranged for those who prefer
                                to skip the more demanding excursions.
                            </p>
                        </div>
                    </div>
                    <!-- FAQ Item 3 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div
                            class="itinerary-accordion flex items-center justify-between bg-gray-50 p-4 cursor-pointer">
                            <h3 class="font-medium">What should I pack for this trip?</h3>
                            <div class="w-6 h-6 flex items-center justify-center text-gray-500">
                                <i class="ri-arrow-down-s-line ri-lg accordion-icon"></i>
                            </div>
                        </div>
                        <div class="itinerary-accordion-content p-4 border-t border-gray-200">
                            <p class="text-gray-700">
                                Essential items include lightweight, breathable clothing, a
                                light rain jacket, comfortable walking shoes, hiking shoes
                                for Mount Batur, swimwear, sun protection (hat, sunglasses,
                                sunscreen), insect repellent, a small daypack, and a
                                refillable water bottle. For temple visits, bring clothes
                                that cover shoulders and knees (sarongs are often provided
                                at temples). A flashlight is useful for the pre-dawn Mount
                                Batur trek.
                            </p>
                        </div>
                    </div>
                    <!-- FAQ Item 4 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div
                            class="itinerary-accordion flex items-center justify-between bg-gray-50 p-4 cursor-pointer">
                            <h3 class="font-medium">Is travel insurance required?</h3>
                            <div class="w-6 h-6 flex items-center justify-center text-gray-500">
                                <i class="ri-arrow-down-s-line ri-lg accordion-icon"></i>
                            </div>
                        </div>
                        <div class="itinerary-accordion-content p-4 border-t border-gray-200">
                            <p class="text-gray-700">
                                Yes, comprehensive travel insurance is mandatory for all
                                participants. Your policy must cover medical expenses,
                                emergency evacuation, trip cancellation, and activities
                                included in the tour such as hiking. We recommend purchasing
                                insurance that covers COVID-19 related issues. Proof of
                                insurance must be provided before the tour begins.
                            </p>
                        </div>
                    </div>
                    <!-- FAQ Item 5 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div
                            class="itinerary-accordion flex items-center justify-between bg-gray-50 p-4 cursor-pointer">
                            <h3 class="font-medium">
                                Can dietary restrictions be accommodated?
                            </h3>
                            <div class="w-6 h-6 flex items-center justify-center text-gray-500">
                                <i class="ri-arrow-down-s-line ri-lg accordion-icon"></i>
                            </div>
                        </div>
                        <div class="itinerary-accordion-content p-4 border-t border-gray-200">
                            <p class="text-gray-700">
                                Yes, we can accommodate most dietary restrictions including
                                vegetarian, vegan, gluten-free, and food allergies. Please
                                inform us of your requirements at the time of booking so we
                                can make appropriate arrangements with restaurants and for
                                the cooking class. Balinese cuisine naturally offers many
                                vegetarian options, and our guides will help you navigate
                                local food choices.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- Right Column - Enquiry Form and Additional Info -->
        <div>
            <!-- Enquiry Form -->
            <section id="enquiry-form" class="bg-white rounded-xl shadow-sm p-6 mb-8 sticky top-24">
                <h2 class="text-2xl font-bold mb-4">Enquire About This Tour</h2>
                <p class="text-gray-600 mb-6">
                    Fill out the form below and our travel experts will get back to
                    you within 24 hours.
                </p>
                <form class="space-y-4">
                    <div>
                        <label for="full-name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                        <input type="text" id="full-name"
                            class="form-input w-full px-4 py-2 border border-gray-300 rounded text-gray-700 text-sm"
                            placeholder="Enter your full name" required />
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                        <input type="email" id="email"
                            class="form-input w-full px-4 py-2 border border-gray-300 rounded text-gray-700 text-sm"
                            placeholder="Enter your email address" required />
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                        <input type="tel" id="phone"
                            class="form-input w-full px-4 py-2 border border-gray-300 rounded text-gray-700 text-sm"
                            placeholder="Enter your phone number" required />
                    </div>
                    <div>
                        <label for="travel-date" class="block text-sm font-medium text-gray-700 mb-1">Preferred Travel
                            Date *</label>
                        <input type="date" id="travel-date"
                            class="form-input custom-date-input w-full px-4 py-2 border border-gray-300 rounded text-gray-700 text-sm"
                            required />
                    </div>
                    <div>
                        <label for="travelers" class="block text-sm font-medium text-gray-700 mb-1">Number of Travelers
                            *</label>
                        <div class="flex">
                            <button type="button"
                                class="decrease-travelers px-3 py-2 bg-gray-100 border border-gray-300 rounded-l text-gray-700 hover:bg-gray-200 transition">
                                <i class="ri-subtract-line"></i>
                            </button>
                            <input type="number" id="travelers"
                                class="form-input custom-number-input w-full px-4 py-2 border-y border-gray-300 text-center text-gray-700 text-sm"
                                value="2" min="1" max="12" required />
                            <button type="button"
                                class="increase-travelers px-3 py-2 bg-gray-100 border border-gray-300 rounded-r text-gray-700 hover:bg-gray-200 transition">
                                <i class="ri-add-line"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Special
                            Requirements/Message</label>
                        <textarea id="message" rows="4"
                            class="form-textarea w-full px-4 py-2 border border-gray-300 rounded text-gray-700 text-sm"
                            placeholder="Tell us about any special requirements, questions, or preferences..."></textarea>
                    </div>
                    <div class="flex items-start">
                        <input type="checkbox" id="privacy-policy" class="custom-checkbox mt-1" required />
                        <label for="privacy-policy" class="ml-2 text-sm text-gray-600">
                            I agree to the
                            <a href="#" class="text-primary hover:underline">Privacy Policy</a>
                            and consent to being contacted regarding my enquiry.
                        </label>
                    </div>
                    <button type="submit" id="submit-enquiry"
                        class="w-full bg-primary text-white px-6 py-3 !rounded-button whitespace-nowrap font-medium hover:bg-primary/90 transition">
                        Submit Enquiry
                    </button>
                    <!-- Confirmation Dialog -->
                    <div id="confirmation-dialog"
                        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                        <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
                            <div class="text-center mb-6">
                                <div
                                    class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="ri-check-line ri-2x text-primary"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">
                                    Thank You for Your Enquiry!
                                </h3>
                                <p class="text-gray-600 mb-2">
                                    We have received your information and will get back to you
                                    within 24 hours.
                                </p>
                                <p class="text-gray-600 mb-4">
                                    Your reference number:
                                    <span id="reference-number" class="font-medium text-primary"></span>
                                </p>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <button id="view-similar-tours"
                                    class="flex-1 px-6 py-2 bg-gray-100 text-gray-700 font-medium !rounded-button hover:bg-gray-200 transition">
                                    View Similar Tours
                                </button>
                                <button id="close-dialog"
                                    class="flex-1 px-6 py-2 bg-primary text-white font-medium !rounded-button hover:bg-primary/90 transition">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-4">
                        Prefer to speak with our travel experts?
                    </p>
                    <a href="tel:+911234567890"
                        class="flex items-center text-gray-700 font-medium hover:text-primary mb-2">
                        <div class="w-5 h-5 flex items-center justify-center text-primary mr-2">
                            <i class="ri-phone-line"></i>
                        </div>
                        <span>+91 (123) 456-7890</span>
                    </a>
                    <a href="mailto:info@journeyfinders.in"
                        class="flex items-center text-gray-700 font-medium hover:text-primary">
                        <div class="w-5 h-5 flex items-center justify-center text-primary mr-2">
                            <i class="ri-mail-line"></i>
                        </div>
                        <span>info@journeyfinders.in</span>
                    </a>
                </div>
            </section>
            <!-- Weather Information -->
            <section class="bg-white rounded-xl shadow-sm p-6 mb-8">
                <h2 class="text-lg font-bold mb-4">Bali Weather Information</h2>
                <div class="flex mb-4">
                    <button
                        class="weather-tab flex-1 py-2 px-1 text-center text-sm font-medium border-b-2 border-primary text-primary"
                        data-tab="temperature">
                        Temperature
                    </button>
                    <button
                        class="weather-tab flex-1 py-2 px-1 text-center text-sm font-medium border-b-2 border-gray-200 text-gray-500"
                        data-tab="rainfall">
                        Rainfall
                    </button>
                </div>
                <div class="tab-content active" id="temperature-tab">
                    <div class="weather-chart" id="temperature-chart"></div>
                    <p class="text-sm text-gray-600 mt-2">
                        Bali enjoys a tropical climate with warm temperatures
                        year-round. The average temperature ranges from 26-28°C
                        (79-82°F). April to October is the dry season with lower
                        humidity and more sunshine.
                    </p>
                </div>
                <div class="tab-content" id="rainfall-tab">
                    <div class="weather-chart" id="rainfall-chart"></div>
                    <p class="text-sm text-gray-600 mt-2">
                        The rainy season runs from November to March, with December and
                        January seeing the highest rainfall. Even during this period,
                        rain typically falls in short, heavy bursts in the afternoon or
                        evening.
                    </p>
                </div>
            </section>
            <!-- Related Tours -->
            <section class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-bold mb-4">You Might Also Like</h2>
                <div class="space-y-4">
                    <!-- Related Tour 1 -->
                    <div class="flex border border-gray-200 rounded-lg overflow-hidden hover:border-primary transition">
                        <div class="w-24 h-24 flex-shrink-0">
                            <img src="#api/search-image?query=beautiful%2520Bali%2520beach%2520sunset%2520with%2520silhouette%2520of%2520palm%2520trees%252C%2520colorful%2520sky%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=120&height=120&seq=1007&orientation=squarish"
                                alt="Bali Beach Escape" class="w-full h-full object-cover object-top" />
                        </div>
                        <div class="p-3 flex-grow">
                            <h3 class="font-medium text-gray-900 mb-1">
                                Bali Beach Escape
                            </h3>
                            <div class="flex items-center text-sm text-gray-600 mb-1">
                                <div class="w-4 h-4 flex items-center justify-center text-primary mr-1">
                                    <i class="ri-calendar-line"></i>
                                </div>
                                <span>7 Days</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="text-primary font-medium text-sm">
                                    From $1,299
                                </div>
                                <div class="star-rating flex text-xs">
                                    <i class="ri-star-fill"></i>
                                    <i class="ri-star-fill"></i>
                                    <i class="ri-star-fill"></i>
                                    <i class="ri-star-fill"></i>
                                    <i class="ri-star-half-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Related Tour 2 -->
                    <div class="flex border border-gray-200 rounded-lg overflow-hidden hover:border-primary transition">
                        <div class="w-24 h-24 flex-shrink-0">
                            <img src="#api/search-image?query=beautiful%2520Lombok%2520Indonesia%2520with%2520pristine%2520beaches%252C%2520turquoise%2520water%252C%2520lush%2520mountains%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=120&height=120&seq=1008&orientation=squarish"
                                alt="Bali & Lombok Adventure" class="w-full h-full object-cover object-top" />
                        </div>
                        <div class="p-3 flex-grow">
                            <h3 class="font-medium text-gray-900 mb-1">
                                Bali & Lombok Adventure
                            </h3>
                            <div class="flex items-center text-sm text-gray-600 mb-1">
                                <div class="w-4 h-4 flex items-center justify-center text-primary mr-1">
                                    <i class="ri-calendar-line"></i>
                                </div>
                                <span>12 Days</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="text-primary font-medium text-sm">
                                    From $2,199
                                </div>
                                <div class="star-rating flex text-xs">
                                    <i class="ri-star-fill"></i>
                                    <i class="ri-star-fill"></i>
                                    <i class="ri-star-fill"></i>
                                    <i class="ri-star-fill"></i>
                                    <i class="ri-star-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Related Tour 3 -->
                    <div class="flex border border-gray-200 rounded-lg overflow-hidden hover:border-primary transition">
                        <div class="w-24 h-24 flex-shrink-0">
                            <img src="#api/search-image?query=beautiful%2520Java%2520Indonesia%2520with%2520Borobudur%2520temple%252C%2520volcanic%2520landscapes%252C%2520traditional%2520villages%252C%2520professional%2520travel%2520photography%2520with%2520vibrant%2520colors%2520and%2520excellent%2520composition&width=120&height=120&seq=1009&orientation=squarish"
                                alt="Java & Bali Explorer" class="w-full h-full object-cover object-top" />
                        </div>
                        <div class="p-3 flex-grow">
                            <h3 class="font-medium text-gray-900 mb-1">
                                Java & Bali Explorer
                            </h3>
                            <div class="flex items-center text-sm text-gray-600 mb-1">
                                <div class="w-4 h-4 flex items-center justify-center text-primary mr-1">
                                    <i class="ri-calendar-line"></i>
                                </div>
                                <span>14 Days</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="text-primary font-medium text-sm">
                                    From $2,499
                                </div>
                                <div class="star-rating flex text-xs">
                                    <i class="ri-star-fill"></i>
                                    <i class="ri-star-fill"></i>
                                    <i class="ri-star-fill"></i>
                                    <i class="ri-star-fill"></i>
                                    <i class="ri-star-line"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <!-- Travel Tips Section -->
    <section class="bg-white rounded-xl shadow-sm p-6 mb-8">
        <h2 class="text-2xl font-bold mb-6">Essential Travel Tips for Bali</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Tip 1 -->
            <div class="bg-gray-50 rounded-lg p-5">
                <div class="w-12 h-12 flex items-center justify-center bg-primary/10 rounded-full text-primary mb-4">
                    <i class="ri-passport-line ri-lg"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">
                    Visa & Documentation
                </h3>
                <p class="text-gray-700 text-sm">
                    Many nationalities receive a 30-day visa-free entry to Indonesia.
                    For longer stays, a visa on arrival (extendable for 30 more days)
                    or a pre-arranged visa is required. Ensure your passport is valid
                    for at least 6 months beyond your departure date.
                </p>
            </div>
            <!-- Tip 2 -->
            <div class="bg-gray-50 rounded-lg p-5">
                <div class="w-12 h-12 flex items-center justify-center bg-primary/10 rounded-full text-primary mb-4">
                    <i class="ri-hand-coin-line ri-lg"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Currency & Money</h3>
                <p class="text-gray-700 text-sm">
                    The Indonesian Rupiah (IDR) is the local currency. ATMs are widely
                    available in tourist areas. Inform your bank of your travel plans
                    to avoid card blocks. Many places accept credit cards, but smaller
                    vendors and rural areas may be cash-only.
                </p>
            </div>
            <!-- Tip 3 -->
            <div class="bg-gray-50 rounded-lg p-5">
                <div class="w-12 h-12 flex items-center justify-center bg-primary/10 rounded-full text-primary mb-4">
                    <i class="ri-health-book-line ri-lg"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Health & Safety</h3>
                <p class="text-gray-700 text-sm">
                    Drink only bottled water and be cautious with street food. Pack
                    insect repellent, sunscreen, and basic medications. Medical
                    facilities in tourist areas are adequate, but comprehensive travel
                    insurance is essential for any serious medical issues.
                </p>
            </div>
            <!-- Tip 4 -->
            <div class="bg-gray-50 rounded-lg p-5">
                <div class="w-12 h-12 flex items-center justify-center bg-primary/10 rounded-full text-primary mb-4">
                    <i class="ri-plug-line ri-lg"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">
                    Electricity & Connectivity
                </h3>
                <p class="text-gray-700 text-sm">
                    Indonesia uses 230V, 50Hz electricity with European-style round
                    pin plugs (types C and F). Wi-Fi is widely available in hotels,
                    cafes, and restaurants in tourist areas, but may be limited in
                    more remote locations.
                </p>
            </div>
            <!-- Tip 5 -->
            <div class="bg-gray-50 rounded-lg p-5">
                <div class="w-12 h-12 flex items-center justify-center bg-primary/10 rounded-full text-primary mb-4">
                    <i class="ri-building-line ri-lg"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Cultural Etiquette</h3>
                <p class="text-gray-700 text-sm">
                    Respect local customs by dressing modestly, especially at temples
                    (shoulders and knees covered). Remove shoes before entering homes
                    and some temples. Use your right hand for giving or receiving
                    items. Ask permission before photographing locals.
                </p>
            </div>
            <!-- Tip 6 -->
            <div class="bg-gray-50 rounded-lg p-5">
                <div class="w-12 h-12 flex items-center justify-center bg-primary/10 rounded-full text-primary mb-4">
                    <i class="ri-taxi-line ri-lg"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Getting Around</h3>
                <p class="text-gray-700 text-sm">
                    Taxis, ride-hailing apps (Grab, Gojek), and private drivers are
                    convenient ways to get around. Renting a scooter is popular but
                    requires an international driving permit and experience with local
                    road conditions. Traffic can be chaotic in busy areas.
                </p>
            </div>
        </div>
    </section>
</div>
<?php

require_once 'footer.php';

?>