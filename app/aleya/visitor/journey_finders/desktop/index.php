<?php

require_once 'header.php';

?>
<!-- Hero Section -->
<section class="relative h-[600px] overflow-hidden">
    <div style="
          background-image: url('https://public.readdy.ai/ai/img_res/6fd34f201eda9995d1478d5c357d55bb.jpg');
        " class="absolute inset-0 bg-cover bg-center"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-black/50 to-transparent"></div>
    <div class="relative container mx-auto px-4 h-full flex flex-col justify-center">
        <div class="max-w-2xl">
            <h1 class="text-4xl md:text-5xl font-bold text-white leading-tight mb-4">
                Discover Your Next Adventure
            </h1>
            <p class="text-xl text-white mb-8">
                Explore handpicked tours and create unforgettable memories with
                JourneyFinders
            </p>
            <div class="bg-white p-4 rounded-lg hero-search-bar">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search Input - 2/3 width -->
                    <div class="relative col-span-1 md:col-span-2">
                        <div
                            class="w-10 h-10 flex items-center justify-center text-gray-500 absolute left-2 top-1/2 transform -translate-y-1/2">
                            <i class="ri-map-pin-line ri-lg"></i>
                        </div>
                        <input type="text" placeholder="Destination"
                            class="pl-12 pr-4 py-3 w-full border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-primary/20 text-sm" />
                    </div>

                    <!-- Search Button - 1/3 width -->
                    <div class="col-span-1">
                        <a href="#home/5d980096-f6d6-4a18-b632-f4eaee7979c7/bf204a0d-8d2e-4112-bb67-ac835580264e"
                            data-readdy="true"
                            class="bg-primary text-white px-6 py-3 rounded-button whitespace-nowrap font-medium hover:bg-primary/90 transition flex items-center justify-center w-full h-full">
                            <i class="ri-search-line mr-2"></i>
                            Search Tours
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
<!-- Featured Tour Packages -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-3">Featured Tour Packages</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Explore our handpicked selection of popular tour packages for your
                next adventure
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="package-card bg-white rounded-lg overflow-hidden shadow-md transition duration-300">
                <div class="relative h-56 overflow-hidden">
                    <img src="https://public.readdy.ai/ai/img_res/6fd34f201eda9995d1478d5c357d55bb.jpg"
                        alt="Bali Adventure" class="w-full h-full object-cover object-top" />
                    <div class="absolute top-4 left-4 bg-primary text-white text-xs font-medium px-2 py-1 rounded-full">
                        Best Seller
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                        <div class="flex items-center text-white mb-1">
                            <i class="ri-map-pin-line mr-1"></i>
                            <span class="text-sm">Bali, Indonesia</span>
                        </div>
                        <h3 class="text-white font-semibold">
                            Bali Cultural Adventure
                        </h3>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center mb-3">
                        <div class="flex text-yellow-400 mr-2">
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-half-fill"></i>
                        </div>
                        <span class="text-gray-600 text-sm">4.8 (256 reviews)</span>
                    </div>
                    <div class="flex items-center text-gray-600 text-sm mb-3">
                        <div class="flex items-center mr-4">
                            <i class="ri-time-line mr-1"></i>
                            <span>5 Days</span>
                        </div>
                        <div class="flex items-center">
                            <i class="ri-group-line mr-1"></i>
                            <span>Max 12 people</span>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">
                        Explore Bali's rich culture with visits to ancient temples, rice
                        terraces, and traditional villages.
                    </p>
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-gray-500 text-sm line-through">$899</span>
                            <span class="text-primary font-semibold ml-1">$749</span>
                            <span class="text-gray-600 text-xs">/ person</span>
                        </div>
                        <button
                            class="bg-primary text-white px-4 py-2 rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition">
                            Enquire Now
                        </button>
                    </div>
                </div>
            </div>
            <div class="package-card bg-white rounded-lg overflow-hidden shadow-md transition duration-300">
                <div class="relative h-56 overflow-hidden">
                    <img src="https://public.readdy.ai/ai/img_res/6fd34f201eda9995d1478d5c357d55bb.jpg"
                        alt="Santorini Escape" class="w-full h-full object-cover object-top" />
                    <div
                        class="absolute top-4 left-4 bg-green-500 text-white text-xs font-medium px-2 py-1 rounded-full">
                        20% Off
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                        <div class="flex items-center text-white mb-1">
                            <i class="ri-map-pin-line mr-1"></i>
                            <span class="text-sm">Santorini, Greece</span>
                        </div>
                        <h3 class="text-white font-semibold">
                            Santorini Island Escape
                        </h3>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center mb-3">
                        <div class="flex text-yellow-400 mr-2">
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                        </div>
                        <span class="text-gray-600 text-sm">4.9 (318 reviews)</span>
                    </div>
                    <div class="flex items-center text-gray-600 text-sm mb-3">
                        <div class="flex items-center mr-4">
                            <i class="ri-time-line mr-1"></i>
                            <span>7 Days</span>
                        </div>
                        <div class="flex items-center">
                            <i class="ri-group-line mr-1"></i>
                            <span>Max 10 people</span>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">
                        Experience the magic of Santorini with stunning sunsets,
                        blue-domed churches, and Mediterranean cuisine.
                    </p>
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-gray-500 text-sm line-through">$1,299</span>
                            <span class="text-primary font-semibold ml-1">$999</span>
                            <span class="text-gray-600 text-xs">/ person</span>
                        </div>
                        <button
                            class="bg-primary text-white px-4 py-2 rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition">
                            Enquire Now
                        </button>
                    </div>
                </div>
            </div>
            <div class="package-card bg-white rounded-lg overflow-hidden shadow-md transition duration-300">
                <div class="relative h-56 overflow-hidden">
                    <img src="https://public.readdy.ai/ai/img_res/6fd34f201eda9995d1478d5c357d55bb.jpg"
                        alt="Peru Adventure" class="w-full h-full object-cover object-top" />
                    <div
                        class="absolute top-4 left-4 bg-blue-500 text-white text-xs font-medium px-2 py-1 rounded-full">
                        New Tour
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                        <div class="flex items-center text-white mb-1">
                            <i class="ri-map-pin-line mr-1"></i>
                            <span class="text-sm">Peru</span>
                        </div>
                        <h3 class="text-white font-semibold">
                            Peru & Machu Picchu Explorer
                        </h3>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center mb-3">
                        <div class="flex text-yellow-400 mr-2">
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-line"></i>
                        </div>
                        <span class="text-gray-600 text-sm">4.7 (189 reviews)</span>
                    </div>
                    <div class="flex items-center text-gray-600 text-sm mb-3">
                        <div class="flex items-center mr-4">
                            <i class="ri-time-line mr-1"></i>
                            <span>10 Days</span>
                        </div>
                        <div class="flex items-center">
                            <i class="ri-group-line mr-1"></i>
                            <span>Max 15 people</span>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">
                        Discover the ancient wonders of Peru including Machu Picchu,
                        Sacred Valley, and Cusco.
                    </p>
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-gray-500 text-sm line-through">$1,899</span>
                            <span class="text-primary font-semibold ml-1">$1,649</span>
                            <span class="text-gray-600 text-xs">/ person</span>
                        </div>
                        <button
                            class="bg-primary text-white px-4 py-2 rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition">
                            Enquire Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-10">
            <a href="#" class="inline-flex items-center text-primary font-medium hover:underline">
                View all tour packages
                <i class="ri-arrow-right-line ml-1"></i>
            </a>
        </div>
    </div>
</section>
<!-- Popular Destinations -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-3">Popular Destinations</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Explore our most sought-after destinations loved by travelers
                worldwide
            </p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <div class="destination-card bg-white rounded-lg overflow-hidden shadow-md transition duration-300">
                <div class="relative h-48 overflow-hidden">
                    <img src="https://public.readdy.ai/ai/img_res/6fd34f201eda9995d1478d5c357d55bb.jpg" alt="Bali"
                        class="w-full h-full object-cover object-top" />
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                        <h3 class="text-white font-semibold">Bali, Indonesia</h3>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 text-sm">125 packages</span>
                        <span class="text-primary font-medium">From $499</span>
                    </div>
                </div>
            </div>
            <div class="destination-card bg-white rounded-lg overflow-hidden shadow-md transition duration-300">
                <div class="relative h-48 overflow-hidden">
                    <img src="https://public.readdy.ai/ai/img_res/6fd34f201eda9995d1478d5c357d55bb.jpg" alt="Santorini"
                        class="w-full h-full object-cover object-top" />
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                        <h3 class="text-white font-semibold">Santorini, Greece</h3>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 text-sm">87 packages</span>
                        <span class="text-primary font-medium">From $799</span>
                    </div>
                </div>
            </div>
            <div class="destination-card bg-white rounded-lg overflow-hidden shadow-md transition duration-300">
                <div class="relative h-48 overflow-hidden">
                    <img src="https://public.readdy.ai/ai/img_res/6fd34f201eda9995d1478d5c357d55bb.jpg" alt="Maldives"
                        class="w-full h-full object-cover object-top" />
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                        <h3 class="text-white font-semibold">Maldives</h3>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 text-sm">64 packages</span>
                        <span class="text-primary font-medium">From $1,299</span>
                    </div>
                </div>
            </div>
            <div class="destination-card bg-white rounded-lg overflow-hidden shadow-md transition duration-300">
                <div class="relative h-48 overflow-hidden">
                    <img src="https://public.readdy.ai/ai/img_res/6fd34f201eda9995d1478d5c357d55bb.jpg" alt="Peru"
                        class="w-full h-full object-cover object-top" />
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                        <h3 class="text-white font-semibold">Peru</h3>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 text-sm">42 packages</span>
                        <span class="text-primary font-medium">From $1,499</span>
                    </div>
                </div>
            </div>
            <div class="destination-card bg-white rounded-lg overflow-hidden shadow-md transition duration-300">
                <div class="relative h-48 overflow-hidden">
                    <img src="https://public.readdy.ai/ai/img_res/6fd34f201eda9995d1478d5c357d55bb.jpg" alt="Japan"
                        class="w-full h-full object-cover object-top" />
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                        <h3 class="text-white font-semibold">Japan</h3>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 text-sm">93 packages</span>
                        <span class="text-primary font-medium">From $1,199</span>
                    </div>
                </div>
            </div>
            <div class="destination-card bg-white rounded-lg overflow-hidden shadow-md transition duration-300">
                <div class="relative h-48 overflow-hidden">
                    <img src="https://public.readdy.ai/ai/img_res/6fd34f201eda9995d1478d5c357d55bb.jpg" alt="Paris"
                        class="w-full h-full object-cover object-top" />
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                        <h3 class="text-white font-semibold">Paris, France</h3>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 text-sm">78 packages</span>
                        <span class="text-primary font-medium">From $899</span>
                    </div>
                </div>
            </div>
            <div class="destination-card bg-white rounded-lg overflow-hidden shadow-md transition duration-300">
                <div class="relative h-48 overflow-hidden">
                    <img src="https://public.readdy.ai/ai/img_res/6fd34f201eda9995d1478d5c357d55bb.jpg"
                        alt="South Africa" class="w-full h-full object-cover object-top" />
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                        <h3 class="text-white font-semibold">South Africa</h3>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 text-sm">56 packages</span>
                        <span class="text-primary font-medium">From $1,099</span>
                    </div>
                </div>
            </div>
            <div class="destination-card bg-white rounded-lg overflow-hidden shadow-md transition duration-300">
                <div class="relative h-48 overflow-hidden">
                    <img src="https://public.readdy.ai/ai/img_res/6fd34f201eda9995d1478d5c357d55bb.jpg" alt="New York"
                        class="w-full h-full object-cover object-top" />
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                        <h3 class="text-white font-semibold">New York, USA</h3>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 text-sm">82 packages</span>
                        <span class="text-primary font-medium">From $999</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Why Choose Us -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-3">Why Choose Us</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                We're committed to making your travel experiences seamless and
                memorable
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 flex items-center justify-center mx-auto bg-primary/10 rounded-full mb-4">
                    <i class="ri-award-line ri-xl text-primary"></i>
                </div>
                <h3 class="text-lg font-semibold mb-2">15+ Years Experience</h3>
                <p class="text-gray-600 text-sm">
                    With over 15 years in the travel industry, we've built a
                    reputation for excellence and reliability.
                </p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 flex items-center justify-center mx-auto bg-primary/10 rounded-full mb-4">
                    <i class="ri-price-tag-3-line ri-xl text-primary"></i>
                </div>
                <h3 class="text-lg font-semibold mb-2">Best Price Guarantee</h3>
                <p class="text-gray-600 text-sm">
                    We promise the most competitive prices for all our tour packages
                    and activities.
                </p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 flex items-center justify-center mx-auto bg-primary/10 rounded-full mb-4">
                    <i class="ri-customer-service-2-line ri-xl text-primary"></i>
                </div>
                <h3 class="text-lg font-semibold mb-2">24/7 Customer Support</h3>
                <p class="text-gray-600 text-sm">
                    Our dedicated team is available around the clock to assist with
                    any queries or concerns.
                </p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 flex items-center justify-center mx-auto bg-primary/10 rounded-full mb-4">
                    <i class="ri-shield-check-line ri-xl text-primary"></i>
                </div>
                <h3 class="text-lg font-semibold mb-2">100% Secure Booking</h3>
                <p class="text-gray-600 text-sm">
                    Your payments and personal information are always protected with
                    our secure booking system.
                </p>
            </div>
        </div>
        <div class="mt-16 bg-gray-50 rounded-lg p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary mb-2">50,000+</div>
                    <p class="text-gray-600">Happy Travelers</p>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary mb-2">1,200+</div>
                    <p class="text-gray-600">Tour Packages</p>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary mb-2">100+</div>
                    <p class="text-gray-600">Destinations</p>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary mb-2">4.8/5</div>
                    <p class="text-gray-600">Customer Rating</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Enquiry Form -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2">
                <div class="p-8">
                    <h2 class="text-2xl font-bold mb-6">Send Us an Enquiry</h2>
                    <p class="text-gray-600 mb-6">
                        Fill out the form below and our travel experts will get back to
                        you within 24 hours.
                    </p>
                    <form>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full
                                    Name*</label>
                                <input type="text" id="name"
                                    class="w-full px-4 py-2 border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-primary/20 text-sm"
                                    required />
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email
                                    Address*</label>
                                <input type="email" id="email"
                                    class="w-full px-4 py-2 border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-primary/20 text-sm"
                                    required />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone
                                    Number*</label>
                                <input type="tel" id="phone"
                                    class="w-full px-4 py-2 border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-primary/20 text-sm"
                                    required />
                            </div>
                            <div>
                                <label for="destination" class="block text-sm font-medium text-gray-700 mb-1">Preferred
                                    Destination</label>
                                <select id="destination"
                                    class="w-full px-4 py-2 border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-primary/20 text-sm appearance-none bg-white pr-8">
                                    <option value="">Select Destination</option>
                                    <option value="bali">Bali, Indonesia</option>
                                    <option value="santorini">Santorini, Greece</option>
                                    <option value="maldives">Maldives</option>
                                    <option value="peru">Peru</option>
                                    <option value="japan">Japan</option>
                                    <option value="paris">Paris, France</option>
                                    <option value="south-africa">South Africa</option>
                                    <option value="new-york">New York, USA</option>
                                    <option value="other">Other</option>
                                </select>
                                <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                    <i class="ri-arrow-down-s-line text-gray-500"></i>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="travel-date" class="block text-sm font-medium text-gray-700 mb-1">Travel
                                    Date</label>
                                <input type="text" id="travel-date"
                                    class="w-full px-4 py-2 border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-primary/20 text-sm date-input"
                                    placeholder="Select date" onfocus="(this.type='date')"
                                    onblur="if(!this.value)this.type='text'" />
                            </div>
                            <div>
                                <label for="travelers" class="block text-sm font-medium text-gray-700 mb-1">Number
                                    of Travelers</label>
                                <select id="travelers"
                                    class="w-full px-4 py-2 border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-primary/20 text-sm appearance-none bg-white pr-8">
                                    <option value="">Select</option>
                                    <option value="1">1 Traveler</option>
                                    <option value="2">2 Travelers</option>
                                    <option value="3">3 Travelers</option>
                                    <option value="4">4 Travelers</option>
                                    <option value="5+">5+ Travelers</option>
                                </select>
                                <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                    <i class="ri-arrow-down-s-line text-gray-500"></i>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Special
                                Requirements</label>
                            <textarea id="message" rows="4"
                                class="w-full px-4 py-2 border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-primary/20 text-sm"></textarea>
                        </div>
                        <div class="flex items-center mb-6">
                            <input type="checkbox" id="consent" class="custom-checkbox" required />
                            <label for="consent" class="text-sm text-gray-600 ml-2">I agree to receive travel
                                updates and promotional offers
                                from JourneyFinders</label>
                        </div>
                        <button type="submit"
                            class="bg-primary text-white px-6 py-3 rounded-button whitespace-nowrap font-medium hover:bg-primary/90 transition w-full">
                            Submit Enquiry
                        </button>
                    </form>
                </div>
                <div class="bg-gray-100 p-8 flex flex-col justify-center">
                    <h3 class="text-xl font-bold mb-4">Contact Information</h3>
                    <div class="space-y-4 mb-8">
                        <div class="flex items-start">
                            <div
                                class="w-10 h-10 flex items-center justify-center bg-primary/10 rounded-full mr-3 text-primary">
                                <i class="ri-map-pin-line"></i>
                            </div>
                            <div>
                                <h4 class="font-medium">Office Address</h4>
                                <p class="text-gray-600 text-sm">
                                    123 Travel Street, Adventure City, AC 10001, India
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div
                                class="w-10 h-10 flex items-center justify-center bg-primary/10 rounded-full mr-3 text-primary">
                                <i class="ri-phone-line"></i>
                            </div>
                            <div>
                                <h4 class="font-medium">Phone Number</h4>
                                <p class="text-gray-600 text-sm">+91 (123) 456-7890</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div
                                class="w-10 h-10 flex items-center justify-center bg-primary/10 rounded-full mr-3 text-primary">
                                <i class="ri-mail-line"></i>
                            </div>
                            <div>
                                <h4 class="font-medium">Email Address</h4>
                                <p class="text-gray-600 text-sm">info@journeyfinders.in</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div
                                class="w-10 h-10 flex items-center justify-center bg-primary/10 rounded-full mr-3 text-primary">
                                <i class="ri-time-line"></i>
                            </div>
                            <div>
                                <h4 class="font-medium">Working Hours</h4>
                                <p class="text-gray-600 text-sm">
                                    Monday - Saturday: 9:00 AM - 6:00 PM
                                </p>
                            </div>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Follow Us</h3>
                    <div class="flex space-x-3">
                        <a href="#"
                            class="w-10 h-10 flex items-center justify-center rounded-full bg-primary/10 hover:bg-primary hover:text-white text-primary transition">
                            <i class="ri-facebook-fill"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 flex items-center justify-center rounded-full bg-primary/10 hover:bg-primary hover:text-white text-primary transition">
                            <i class="ri-twitter-x-fill"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 flex items-center justify-center rounded-full bg-primary/10 hover:bg-primary hover:text-white text-primary transition">
                            <i class="ri-instagram-fill"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 flex items-center justify-center rounded-full bg-primary/10 hover:bg-primary hover:text-white text-primary transition">
                            <i class="ri-youtube-fill"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Travel Styles -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-3">Choose Your Travel Style</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Discover experiences that match your unique travel preferences
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition">
                <div class="relative h-48">
                    <img src="#api/search-image?query=luxury%20resort%20infinity%20pool%20overlooking%20ocean%20at%20sunset%2C%20with%20comfortable%20loungers%20and%20butler%20service%2C%20high-end%20luxury%20travel%20experience%2C%20professional%20photography&width=400&height=300&seq=14&orientation=landscape"
                        alt="Luxury Travel" class="w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-black/20"></div>
                    <div class="absolute bottom-4 left-4">
                        <h3 class="text-white text-lg font-semibold">Luxury Travel</h3>
                        <p class="text-white/90 text-sm">
                            Premium experiences & comfort
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition">
                <div class="relative h-48">
                    <img src="#api/search-image?query=adventurous%20mountain%20hiking%20scene%20with%20backpackers%20on%20scenic%20trail%2C%20dramatic%20landscape%20with%20snow-capped%20peaks%2C%20adventure%20travel%20photography&width=400&height=300&seq=15&orientation=landscape"
                        alt="Adventure Travel" class="w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-black/20"></div>
                    <div class="absolute bottom-4 left-4">
                        <h3 class="text-white text-lg font-semibold">
                            Adventure Travel
                        </h3>
                        <p class="text-white/90 text-sm">
                            Thrilling outdoor activities
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition">
                <div class="relative h-48">
                    <img src="#api/search-image?query=cultural%20immersion%20scene%20with%20traditional%20festival%20celebration%2C%20colorful%20costumes%20and%20local%20customs%2C%20authentic%20cultural%20travel%20photography&width=400&height=300&seq=16&orientation=landscape"
                        alt="Cultural Travel" class="w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-black/20"></div>
                    <div class="absolute bottom-4 left-4">
                        <h3 class="text-white text-lg font-semibold">
                            Cultural Travel
                        </h3>
                        <p class="text-white/90 text-sm">Immersive local experiences</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition">
                <div class="relative h-48">
                    <img src="#api/search-image?query=romantic%20couple%20enjoying%20private%20beach%20dinner%20at%20sunset%2C%20candlelit%20table%20with%20champagne%2C%20honeymoon%20travel%20photography&width=400&height=300&seq=17&orientation=landscape"
                        alt="Romantic Getaways" class="w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-black/20"></div>
                    <div class="absolute bottom-4 left-4">
                        <h3 class="text-white text-lg font-semibold">
                            Romantic Getaways
                        </h3>
                        <p class="text-white/90 text-sm">Perfect for couples</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Explore by Terrain -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-3">Explore by Terrain</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Discover destinations based on your preferred natural landscapes
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition group">
                <div class="relative h-64">
                    <img src="#api/search-image?query=pristine%20tropical%20beach%20with%20crystal%20clear%20turquoise%20water%2C%20white%20sand%2C%20and%20palm%20trees%2C%20perfect%20beach%20destination%2C%20professional%20travel%20photography&width=600&height=400&seq=18&orientation=landscape"
                        alt="Beaches"
                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-6 left-6">
                        <h3 class="text-white text-2xl font-semibold mb-2">Beaches</h3>
                        <div class="flex items-center text-white/90">
                            <span class="text-sm">42 destinations</span>
                            <i class="ri-arrow-right-line ml-2"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition group">
                <div class="relative h-64">
                    <img src="#api/search-image?query=majestic%20mountain%20range%20with%20snow-capped%20peaks%2C%20alpine%20meadows%2C%20and%20clear%20mountain%20lake%2C%20dramatic%20mountain%20landscape%2C%20professional%20travel%20photography&width=600&height=400&seq=19&orientation=landscape"
                        alt="Mountains"
                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-6 left-6">
                        <h3 class="text-white text-2xl font-semibold mb-2">
                            Mountains
                        </h3>
                        <div class="flex items-center text-white/90">
                            <span class="text-sm">35 destinations</span>
                            <i class="ri-arrow-right-line ml-2"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition group">
                <div class="relative h-64">
                    <img src="#api/search-image?query=vast%20desert%20landscape%20with%20golden%20sand%20dunes%20at%20sunset%2C%20camel%20caravan%20silhouette%2C%20dramatic%20desert%20scenery%2C%20professional%20travel%20photography&width=600&height=400&seq=20&orientation=landscape"
                        alt="Deserts"
                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-6 left-6">
                        <h3 class="text-white text-2xl font-semibold mb-2">Deserts</h3>
                        <div class="flex items-center text-white/90">
                            <span class="text-sm">28 destinations</span>
                            <i class="ri-arrow-right-line ml-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Newsletter -->
<section class="py-16 bg-primary/5">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-3xl font-bold mb-3">Get Travel Inspiration</h2>
            <p class="text-gray-600 mb-8">
                Subscribe to our newsletter for exclusive deals and travel tips
            </p>
            <div class="flex flex-col sm:flex-row gap-4 max-w-xl mx-auto">
                <input type="email" placeholder="Your email address"
                    class="flex-grow px-4 py-3 rounded-button border border-gray-200 focus:outline-none focus:ring-2 focus:ring-primary/20" />
                <button
                    class="bg-primary text-white px-6 py-3 rounded-button whitespace-nowrap font-medium hover:bg-primary/90 transition">
                    Subscribe
                </button>
            </div>
            <div class="mt-4 flex items-center justify-center gap-2">
                <input type="checkbox" class="custom-checkbox" id="newsletter-consent" />
                <label for="newsletter-consent" class="text-sm text-gray-600">I agree to receive promotional emails
                    and travel updates</label>
            </div>
        </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>