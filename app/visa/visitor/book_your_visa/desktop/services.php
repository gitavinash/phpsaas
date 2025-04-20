<?php

require_once 'header.php';

?>
<section class="pt-20 relative">
    <div class="absolute inset-0 bg-cover bg-center"
        style="background-image: url('https://readdy.ai/api/search-image?query=A%20professional%2C%20modern%20travel%20scene%20with%20passport%2C%20visa%20documents%2C%20and%20travel%20accessories%20arranged%20neatly%20on%20a%20clean%20desk.%20The%20image%20has%20a%20gradient%20blue%20background%20that%20transitions%20to%20white%20on%20the%20left%20side%2C%20creating%20a%20perfect%20space%20for%20text%20overlay.%20The%20scene%20includes%20subtle%20travel%20elements%20like%20a%20world%20map%2C%20airplane%20model%2C%20and%20elegant%20visa%20stamps&width=1600&height=600&seq=visa-hero&orientation=landscape');">
    </div>
    <div class="absolute inset-0 bg-gradient-to-r from-white via-white/90 to-transparent"></div>
    <div class="container mx-auto px-4 py-16 md:py-24 relative">
        <div class="max-w-2xl">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Global Visa Services Simplified</h1>
            <p class="text-lg text-gray-700 mb-8">Navigate the complex world of visas with our expert guidance. We offer
                comprehensive visa services for over 190 countries with guaranteed approval rates.</p>
            <div class="flex flex-wrap gap-4">
                <a href="/start-application/"
                    class="bg-primary text-white hover:bg-primary/90 font-medium !rounded-button px-8 py-3 cursor-pointer whitespace-nowrap">Apply
                    Now</a>
            </div>
        </div>
    </div>
</section>
<!-- Visa Categories Grid -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-2">Visa Categories</h2>
        <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto">Explore our comprehensive range of visa services
            tailored to meet your specific travel needs.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Tourist Visa -->
            <div class="visa-card bg-white rounded-lg shadow-md p-6 transition-all duration-300">
                <div class="w-14 h-14 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                    <i class="ri-plane-line text-primary ri-2x"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Tourist Visa</h3>
                <p class="text-gray-600 mb-4">Perfect for leisure travelers planning to visit foreign countries for
                    tourism, sightseeing, or visiting friends and family.</p>
                <div class="flex items-center text-sm text-gray-500 mb-4">
                    <div class="flex items-center mr-4">
                        <i class="ri-time-line mr-1"></i>
                        <span>7-30 days</span>
                    </div>
                    <div class="flex items-center">
                        <i class="ri-money-dollar-circle-line mr-1"></i>
                        <span>From $49</span>
                    </div>
                </div>
                <a href="category-service"
                    class="text-primary font-medium flex items-center hover:underline !rounded-button whitespace-nowrap">
                    Learn More
                    <i class="ri-arrow-right-line ml-1"></i>
                </a>
            </div>
            <!-- Business Visa -->
            <div class="visa-card bg-white rounded-lg shadow-md p-6 transition-all duration-300">
                <div class="w-14 h-14 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                    <i class="ri-briefcase-line text-primary ri-2x"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Business Visa</h3>
                <p class="text-gray-600 mb-4">Designed for professionals traveling for business meetings, conferences,
                    trade fairs, or establishing business connections.</p>
                <div class="flex items-center text-sm text-gray-500 mb-4">
                    <div class="flex items-center mr-4">
                        <i class="ri-time-line mr-1"></i>
                        <span>10-45 days</span>
                    </div>
                    <div class="flex items-center">
                        <i class="ri-money-dollar-circle-line mr-1"></i>
                        <span>From $99</span>
                    </div>
                </div>
                <button
                    class="text-primary font-medium flex items-center hover:underline !rounded-button whitespace-nowrap">
                    Learn More
                    <i class="ri-arrow-right-line ml-1"></i>
                </button>
            </div>
            <!-- Work Visa -->
            <div class="visa-card bg-white rounded-lg shadow-md p-6 transition-all duration-300">
                <div class="w-14 h-14 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                    <i class="ri-tools-line text-primary ri-2x"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Work Visa</h3>
                <p class="text-gray-600 mb-4">For individuals seeking employment opportunities abroad with approved job
                    offers from foreign employers.</p>
                <div class="flex items-center text-sm text-gray-500 mb-4">
                    <div class="flex items-center mr-4">
                        <i class="ri-time-line mr-1"></i>
                        <span>30-90 days</span>
                    </div>
                    <div class="flex items-center">
                        <i class="ri-money-dollar-circle-line mr-1"></i>
                        <span>From $199</span>
                    </div>
                </div>
                <button
                    class="text-primary font-medium flex items-center hover:underline !rounded-button whitespace-nowrap">
                    Learn More
                    <i class="ri-arrow-right-line ml-1"></i>
                </button>
            </div>
            <!-- Student Visa -->
            <div class="visa-card bg-white rounded-lg shadow-md p-6 transition-all duration-300">
                <div class="w-14 h-14 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                    <i class="ri-book-open-line text-primary ri-2x"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Student Visa</h3>
                <p class="text-gray-600 mb-4">Tailored for students pursuing education abroad with admission to
                    recognized educational institutions.</p>
                <div class="flex items-center text-sm text-gray-500 mb-4">
                    <div class="flex items-center mr-4">
                        <i class="ri-time-line mr-1"></i>
                        <span>30-60 days</span>
                    </div>
                    <div class="flex items-center">
                        <i class="ri-money-dollar-circle-line mr-1"></i>
                        <span>From $149</span>
                    </div>
                </div>
                <button
                    class="text-primary font-medium flex items-center hover:underline !rounded-button whitespace-nowrap">
                    Learn More
                    <i class="ri-arrow-right-line ml-1"></i>
                </button>
            </div>
            <!-- Family Visa -->
            <div class="visa-card bg-white rounded-lg shadow-md p-6 transition-all duration-300">
                <div class="w-14 h-14 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                    <i class="ri-group-line text-primary ri-2x"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Family Visa</h3>
                <p class="text-gray-600 mb-4">For individuals joining family members who are citizens or permanent
                    residents of a foreign country.</p>
                <div class="flex items-center text-sm text-gray-500 mb-4">
                    <div class="flex items-center mr-4">
                        <i class="ri-time-line mr-1"></i>
                        <span>60-120 days</span>
                    </div>
                    <div class="flex items-center">
                        <i class="ri-money-dollar-circle-line mr-1"></i>
                        <span>From $249</span>
                    </div>
                </div>
                <button
                    class="text-primary font-medium flex items-center hover:underline !rounded-button whitespace-nowrap">
                    Learn More
                    <i class="ri-arrow-right-line ml-1"></i>
                </button>
            </div>
            <!-- Transit Visa -->
            <div class="visa-card bg-white rounded-lg shadow-md p-6 transition-all duration-300">
                <div class="w-14 h-14 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                    <i class="ri-route-line text-primary ri-2x"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Transit Visa</h3>
                <p class="text-gray-600 mb-4">Required for travelers passing through a country en route to their final
                    destination, typically for short layovers.</p>
                <div class="flex items-center text-sm text-gray-500 mb-4">
                    <div class="flex items-center mr-4">
                        <i class="ri-time-line mr-1"></i>
                        <span>3-10 days</span>
                    </div>
                    <div class="flex items-center">
                        <i class="ri-money-dollar-circle-line mr-1"></i>
                        <span>From $29</span>
                    </div>
                </div>
                <button
                    class="text-primary font-medium flex items-center hover:underline !rounded-button whitespace-nowrap">
                    Learn More
                    <i class="ri-arrow-right-line ml-1"></i>
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Popular Visa Services -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Popular Visa Services</h2>
                <p class="text-gray-600">Most sought-after visa services with high success rates</p>
            </div>
            <div class="flex space-x-2">
                <button id="scroll-left"
                    class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200">
                    <i class="ri-arrow-left-s-line text-gray-700"></i>
                </button>
                <button id="scroll-right"
                    class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200">
                    <i class="ri-arrow-right-s-line text-gray-700"></i>
                </button>
            </div>
        </div>
        <div class="scroll-container overflow-x-auto pb-4" id="countryScroll">
            <div class="flex space-x-6 min-w-max">
                <?php

                if ($popularCountry = mysqli_query($connection, "SELECT visa_country_code, visa_country_image, visa_country_title, visa_country_services FROM {$tableNamePrefix}country WHERE visa_country_status=1 AND visa_country_site_id=" . SITE_ID)) {
                    while ($popularCountryData = mysqli_fetch_assoc($popularCountry)) {
                        echo "
                              <div class='visa-card bg-white rounded-lg shadow-md p-6 transition-all duration-300 w-72'>
                                    <div class='flex items-center mb-4'>
                                        <div class='w-10 h-10 flex items-center justify-center mr-3'>
                                            <img src='https://flagcdn.com/h80/" . strtolower($popularCountryData['visa_country_code']) . ".png' alt='{$popularCountryData['visa_country_title']} Flag'
                                                class='w-10 h-10 object-cover rounded-full'>
                                        </div>
                                        <div>
                                            <h3 class='font-semibold text-gray-900'>{$popularCountryData['visa_country_title']}</h3>
                                            <p class='text-sm text-gray-500'>B-2 Visa</p>
                                        </div>
                                    </div>
                                    <div class='flex justify-between text-sm text-gray-600 mb-4'>
                                        <div>
                                            <p class='font-medium'>Processing Time</p>
                                            <p>14-21 days</p>
                                        </div>
                                    </div>
                                    <div class='bg-gray-50 rounded p-3 mb-4'>
                                        <p class='text-gray-900 font-medium'>Starting from</p>
                                        <p class='text-2xl font-bold text-primary'>₹12,499</p>
                                    </div>
                                    <a href='/start-application/?country=" . strtolower(str_replace(' ', '_', $popularCountryData['visa_country_title'])) . "'
                                        class='w-full applyNowBtn bg-primary text-white py-2 !rounded-button whitespace-nowrap font-medium'>Apply
                                        Now</a>
                                </div>
                                ";
                    }
                }

                ?>
            </div>
        </div>
    </div>
</section>
<!-- Call-to-Action Section -->
<section class="py-16 bg-primary">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Need Help Choosing the Right Visa?</h2>
            <p class="text-white/90 mb-8 text-lg">Our visa experts are ready to analyze your specific situation and
                recommend the most suitable visa option with the highest chance of approval.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="/contact-us/"
                    class="bg-white text-primary px-6 py-3 !rounded-button whitespace-nowrap font-medium shadow-md hover:bg-gray-100">Contact
                    Our Experts</a>
            </div>
        </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>