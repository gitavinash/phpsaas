<?php

require_once 'header.php';

?>

<main>
    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 overflow-hidden">
        <div
            style="background-image: url('<?php echo USER_IMAGES ?>tourist-us-background.jpg'); background-size: cover; background-position: center; position: absolute; top: 0; right: 0; bottom: 0; left: 0; opacity: 0.15;">
        </div>
        <div class="container mx-auto px-4 relative">
            <div class="max-w-3xl">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">Tourist Visa Services</h1>
                <p class="text-xl text-gray-700 mb-8">Explore the world with our hassle-free tourist visa services. We
                    provide expert assistance to ensure your travel dreams become reality with minimal paperwork and
                    maximum efficiency.</p>
                <div class="flex flex-wrap gap-4">
                    <a href="/start-application/"
                        class="bg-primary text-white hover:bg-primary/90 font-medium !rounded-button px-8 py-3 cursor-pointer whitespace-nowrap">Apply
                        Now</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Destinations Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Popular Destinations</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Explore our most requested tourist visa destinations with
                    competitive processing times and fees</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    class="bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:shadow-lg hover:-translate-y-1">
                    <div class="h-48 overflow-hidden">
                        <img src="https://readdy.ai/api/search-image?query=United%2520States%2520landmarks%2520skyline%2520with%2520Statue%2520of%2520Liberty%2520and%2520skyscrapers%252C%2520professional%2520travel%2520photography%252C%2520bright%2520daylight%252C%2520clear%2520blue%2520sky%252C%2520high%2520quality&width=600&height=400&seq=usa-visa&orientation=landscape"
                            alt="USA Tourist Visa" class="w-full h-full object-cover object-top">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <i class="ri-flag-2-fill text-primary mr-2 w-6 h-6 flex items-center justify-center"></i>
                            <h3 class="text-xl font-semibold">United States</h3>
                        </div>
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Processing Time:</span>
                                <span class="font-medium">7-14 days</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Starting Fee:</span>
                                <span class="font-medium">₹ 160</span>
                            </div>
                        </div>
                        <a href="/start-application/?country=united_states&purpose=tourist"
                            class="block text-center bg-white text-primary border border-primary hover:bg-gray-50 font-medium !rounded-button py-2 cursor-pointer whitespace-nowrap">View
                            Details</a>
                    </div>
                </div>

                <div
                    class="bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:shadow-lg hover:-translate-y-1">
                    <div class="h-48 overflow-hidden">
                        <img src="https://readdy.ai/api/search-image?query=UK%2520landmarks%2520with%2520London%2520Eye%2520and%2520Big%2520Ben%252C%2520professional%2520travel%2520photography%252C%2520bright%2520daylight%252C%2520clear%2520blue%2520sky%252C%2520high%2520quality&width=600&height=400&seq=uk-visa&orientation=landscape"
                            alt="UK Tourist Visa" class="w-full h-full object-cover object-top">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <i class="ri-flag-2-fill text-primary mr-2 w-6 h-6 flex items-center justify-center"></i>
                            <h3 class="text-xl font-semibold">United Kingdom</h3>
                        </div>
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Processing Time:</span>
                                <span class="font-medium">15-20 days</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Starting Fee:</span>
                                <span class="font-medium">₹ 140</span>
                            </div>
                        </div>
                        <a href="/start-application/?country=united_kingdom&purpose=tourist"
                            class="block text-center bg-white text-primary border border-primary hover:bg-gray-50 font-medium !rounded-button py-2 cursor-pointer whitespace-nowrap">View
                            Details</a>
                    </div>
                </div>

                <div
                    class="bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:shadow-lg hover:-translate-y-1">
                    <div class="h-48 overflow-hidden">
                        <img src="https://readdy.ai/api/search-image?query=Schengen%2520Europe%2520landmarks%2520with%2520Eiffel%2520Tower%2520and%2520Colosseum%252C%2520professional%2520travel%2520photography%252C%2520bright%2520daylight%252C%2520clear%2520blue%2520sky%252C%2520high%2520quality&width=600&height=400&seq=schengen-visa&orientation=landscape"
                            alt="Schengen Tourist Visa" class="w-full h-full object-cover object-top">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <i class="ri-flag-2-fill text-primary mr-2 w-6 h-6 flex items-center justify-center"></i>
                            <h3 class="text-xl font-semibold">Schengen Area</h3>
                        </div>
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Processing Time:</span>
                                <span class="font-medium">10-15 days</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Starting Fee:</span>
                                <span class="font-medium">₹ 80</span>
                            </div>
                        </div>
                        <a href="/start-application/?country=schengen&purpose=tourist"
                            class="block text-center bg-white text-primary border border-primary hover:bg-gray-50 font-medium !rounded-button py-2 cursor-pointer whitespace-nowrap">View
                            Details</a>
                    </div>
                </div>

                <div
                    class="bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:shadow-lg hover:-translate-y-1">
                    <div class="h-48 overflow-hidden">
                        <img src="https://readdy.ai/api/search-image?query=Australia%2520landmarks%2520with%2520Sydney%2520Opera%2520House%2520and%2520natural%2520landscapes%252C%2520professional%2520travel%2520photography%252C%2520bright%2520daylight%252C%2520clear%2520blue%2520sky%252C%2520high%2520quality&width=600&height=400&seq=australia-visa&orientation=landscape"
                            alt="Australia Tourist Visa" class="w-full h-full object-cover object-top">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <i class="ri-flag-2-fill text-primary mr-2 w-6 h-6 flex items-center justify-center"></i>
                            <h3 class="text-xl font-semibold">Australia</h3>
                        </div>
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Processing Time:</span>
                                <span class="font-medium">5-10 days</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Starting Fee:</span>
                                <span class="font-medium">₹ 120</span>
                            </div>
                        </div>
                        <a href="/start-application/?country=australia&purpose=tourist"
                            class="block text-center bg-white text-primary border border-primary hover:bg-gray-50 font-medium !rounded-button py-2 cursor-pointer whitespace-nowrap">View
                            Details</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Application Process Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Application Process</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Follow these simple steps to apply for your tourist visa</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow-md p-6 relative">
                    <div
                        class="w-12 h-12 bg-primary text-white rounded-full flex items-center justify-center font-bold text-xl mb-4">
                        1</div>
                    <h3 class="text-xl font-semibold mb-3">Complete Application</h3>
                    <p class="text-gray-600">Fill out our simple online application form with your personal and travel
                        details.</p>
                    <i class="ri-file-list-3-line text-gray-200 absolute bottom-4 right-4 text-5xl"></i>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 relative">
                    <div
                        class="w-12 h-12 bg-primary text-white rounded-full flex items-center justify-center font-bold text-xl mb-4">
                        2</div>
                    <h3 class="text-xl font-semibold mb-3">Upload Documents</h3>
                    <p class="text-gray-600">Submit all required documents according to your destination country's
                        requirements.</p>
                    <i class="ri-upload-cloud-line text-gray-200 absolute bottom-4 right-4 text-5xl"></i>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 relative">
                    <div
                        class="w-12 h-12 bg-primary text-white rounded-full flex items-center justify-center font-bold text-xl mb-4">
                        3</div>
                    <h3 class="text-xl font-semibold mb-3">Pay Fees</h3>
                    <p class="text-gray-600">Make a secure payment for your visa processing and service fees.</p>
                    <i class="ri-secure-payment-line text-gray-200 absolute bottom-4 right-4 text-5xl"></i>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 relative">
                    <div
                        class="w-12 h-12 bg-primary text-white rounded-full flex items-center justify-center font-bold text-xl mb-4">
                        4</div>
                    <h3 class="text-xl font-semibold mb-3">Receive Visa</h3>
                    <p class="text-gray-600">Get your approved visa delivered to your email or physical address.</p>
                    <i class="ri-mail-check-line text-gray-200 absolute bottom-4 right-4 text-5xl"></i>
                </div>
            </div>
        </div>
    </section>


    <!-- Application CTA Section -->
    <section id="apply" class="py-16 bg-primary/5">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Ready to Start Your Visa Application?</h2>
                <p class="text-xl text-gray-700 mb-8">Begin your journey today with our simple, secure application
                    process</p>
                <a href="/start-application/"
                    class="inline-block bg-primary text-white hover:bg-primary/90 font-medium !rounded-button px-8 py-3 cursor-pointer whitespace-nowrap text-lg">Start
                    Your Application</a>
                <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                            <i
                                class="ri-customer-service-2-line text-primary text-2xl w-8 h-8 flex items-center justify-center"></i>
                        </div>
                        <h3 class="text-lg font-medium mb-2">24/7 Support</h3>
                        <p class="text-gray-600 text-center">Our team is available around the clock to assist with your
                            application</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                            <i
                                class="ri-shield-check-line text-primary text-2xl w-8 h-8 flex items-center justify-center"></i>
                        </div>
                        <h3 class="text-lg font-medium mb-2">Secure Process</h3>
                        <p class="text-gray-600 text-center">Your information is protected with advanced encryption
                            technology</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                            <i class="ri-award-line text-primary text-2xl w-8 h-8 flex items-center justify-center"></i>
                        </div>
                        <h3 class="text-lg font-medium mb-2">High Success Rate</h3>
                        <p class="text-gray-600 text-center">Over 95% of applications processed through our service are
                            approved</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php

require_once 'footer.php';

?>