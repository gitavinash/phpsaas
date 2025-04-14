<?php

    require_once 'header.php';

?>
<main class="pt-20">
    <section class="relative min-h-[600px] flex items-center bg-cover bg-center"
        style="background-image: url('<?php echo USER_IMAGES ?>background.jpg')">
        <div class="absolute inset-0 bg-gradient-to-r from-white/90 to-white/30"></div>
        <div class="container mx-auto px-4 relative">
            <div class="max-w-2xl">
                <h1 class="text-3xl md:text-5xl font-bold text-gray-900 mb-6">Simple Visa Application Process</h1>
                <p class="text-lg md:text-xl text-gray-700 mb-8">Get your visa hassle-free with our streamlined
                    online process. Expert support at every step.</p>

                <div class="bg-white p-6 rounded-lg shadow-lg max-w-xl">
                    <div class="flex space-x-4 mb-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Country</label>
                            <div class="relative">
                                <select
                                    class="selectJS selectDestination w-full border border-gray-300 rounded-button px-4 py-2.5 appearance-none bg-white pr-10">
                                    <option></option>
                                    <option value="US" data-flag="https://flagcdn.com/w80/us.png">United States
                                    </option>
                                    <option value="CA" data-flag="https://flagcdn.com/w80/ca.png">Canada</option>
                                    <option value="GB" data-flag="https://flagcdn.com/w80/gb.png">United Kingdom
                                    </option>
                                    <option value="AU" data-flag="https://flagcdn.com/w80/au.png">Australia</option>
                                    <option value="AE" data-flag="https://flagcdn.com/w80/ae.png">United Arab
                                        Emirates
                                    </option>
                                    <option value="DE" data-flag="https://flagcdn.com/w80/de.png">Germany</option>
                                    <option value="NZ" data-flag="https://flagcdn.com/w80/nz.png">New Zealand
                                    </option>
                                    <option value="SG" data-flag="https://flagcdn.com/w80/sg.png">Singapore</option>
                                    <option value="SA" data-flag="https://flagcdn.com/w80/sa.png">Saudi Arabia
                                    </option>
                                    <option value="QA" data-flag="https://flagcdn.com/w80/qa.png">Qatar</option>
                                    <option value="MY" data-flag="https://flagcdn.com/w80/my.png">Malaysia</option>
                                    <option value="TH" data-flag="https://flagcdn.com/w80/th.png">Thailand</option>
                                    <option value="FR" data-flag="https://flagcdn.com/w80/fr.png">France</option>
                                    <option value="IT" data-flag="https://flagcdn.com/w80/it.png">Italy</option>
                                    <option value="NL" data-flag="https://flagcdn.com/w80/nl.png">Netherlands
                                    </option>
                                    <option value="JP" data-flag="https://flagcdn.com/w80/jp.png">Japan</option>
                                    <option value="KR" data-flag="https://flagcdn.com/w80/kr.png">South Korea
                                    </option>
                                    <option value="PT" data-flag="https://flagcdn.com/w80/pt.png">Portugal</option>
                                    <option value="ES" data-flag="https://flagcdn.com/w80/es.png">Spain</option>
                                    <option value="ZA" data-flag="https://flagcdn.com/w80/za.png">South Africa
                                    </option>
                                </select>
                                <i
                                    class="ri-arrow-down-s-line absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 flex items-center justify-center"></i>
                            </div>
                        </div>
                        <!-- <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Visa Type</label>
                            <div class="relative">
                                <select
                                    class="w-full border border-gray-300 rounded-button px-4 py-2.5 appearance-none bg-white pr-10">
                                    <option value="">Select type</option>
                                    <option value="tourist">Tourist</option>
                                    <option value="business">Business</option>
                                    <option value="student">Student</option>
                                </select>
                                <i
                                    class="ri-arrow-down-s-line absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 flex items-center justify-center"></i>
                            </div>
                        </div> -->
                    </div>
                    <a href="/start-application/" data-readdy="true"
                        class="w-full bg-primary text-white font-medium py-3 !rounded-button hover:bg-primary/90 cursor-pointer block text-center">
                        Apply for Visa Now
                    </a>
                </div>
            </div>
        </div>
    </section>
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">How It Works</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto">
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-earth-line text-primary ri-2x"></i>
                    </div>
                    <h3 class="font-medium mb-2">Select Country</h3>
                    <p class="text-sm text-gray-600">Choose your destination and visa type</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-file-list-3-line text-primary ri-2x"></i>
                    </div>
                    <h3 class="font-medium mb-2">Fill Details</h3>
                    <p class="text-sm text-gray-600">Complete your application form</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-upload-cloud-line text-primary ri-2x"></i>
                    </div>
                    <h3 class="font-medium mb-2">Upload Documents</h3>
                    <p class="text-sm text-gray-600">Submit required documents</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-radar-line text-primary ri-2x"></i>
                    </div>
                    <h3 class="font-medium mb-2">Track Status</h3>
                    <p class="text-sm text-gray-600">Monitor your application</p>
                </div>
            </div>
        </div>
    </section>
    <section class="py-20">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">Popular Destinations</h2>
            <div class="relative">
                <div class="overflow-hidden">
                    <div id="destinationSlider"
                        class="flex transition-transform duration-300 ease-in-out margin-bottom-50"
                        style="transform: translateX(0px);">
                        <div
                            class="country-card relative bg-white rounded-lg shadow-lg overflow-hidden group min-w-[calc(100%-1.2rem)] md:min-w-[calc(25%-1.2rem)] mx-3">
                            <img src="https://public.readdy.ai/ai/img_res/2adbb78ae9b4adaf452e0e695697df0f.jpg"
                                alt="USA" class="w-full h-48 object-cover">
                            <div class="p-4">
                                <div class="flex items-center mb-3">
                                    <i
                                        class="ri-flag-line text-primary mr-2 w-5 h-5 flex items-center justify-center"></i>
                                    <h3 class="font-medium">United States</h3>
                                </div>
                                <div class="flex justify-between text-sm text-gray-600 mb-4">
                                    <span>Processing: 5-7 days</span>
                                    <span>From $160</span>
                                </div>
                                <a href="/start-application/"
                                    class="w-full float-left text-center bg-primary text-white font-medium py-2 !rounded-button hover:bg-primary/90 cursor-pointer">Apply
                                    Now</a>
                            </div>
                        </div>
                        <div
                            class="country-card relative bg-white rounded-lg shadow-lg overflow-hidden group min-w-[calc(100%-1.2rem)] md:min-w-[calc(25%-1.2rem)] mx-3">
                            <img src="https://public.readdy.ai/ai/img_res/15e5b1cb313701eb6769351b7d409fd1.jpg" alt="UK"
                                class="w-full h-48 object-cover">
                            <div class="p-4">
                                <div class="flex items-center mb-3">
                                    <i
                                        class="ri-flag-line text-primary mr-2 w-5 h-5 flex items-center justify-center"></i>
                                    <h3 class="font-medium">United Kingdom</h3>
                                </div>
                                <div class="flex justify-between text-sm text-gray-600 mb-4">
                                    <span>Processing: 3-5 weeks</span>
                                    <span>From $140</span>
                                </div>
                                <button
                                    class="w-full bg-primary text-white font-medium py-2 !rounded-button hover:bg-primary/90 cursor-pointer">Apply
                                    Now</button>
                            </div>
                        </div>
                        <div
                            class="country-card relative bg-white rounded-lg shadow-lg overflow-hidden group min-w-[calc(100%-1.2rem)] md:min-w-[calc(25%-1.2rem)] mx-3">
                            <img src="https://public.readdy.ai/ai/img_res/ea06b1fd81a39d7277c6e245ee760542.jpg"
                                alt="Canada" class="w-full h-48 object-cover">
                            <div class="p-4">
                                <div class="flex items-center mb-3">
                                    <i
                                        class="ri-flag-line text-primary mr-2 w-5 h-5 flex items-center justify-center"></i>
                                    <h3 class="font-medium">Canada</h3>
                                </div>
                                <div class="flex justify-between text-sm text-gray-600 mb-4">
                                    <span>Processing: 4-6 weeks</span>
                                    <span>From $150</span>
                                </div>
                                <button
                                    class="w-full bg-primary text-white font-medium py-2 !rounded-button hover:bg-primary/90 cursor-pointer">Apply
                                    Now</button>
                            </div>
                        </div>
                        <div
                            class="country-card relative bg-white rounded-lg shadow-lg overflow-hidden group min-w-[calc(100%-1.2rem)] md:min-w-[calc(25%-1.2rem)] mx-3">
                            <img src="https://public.readdy.ai/ai/img_res/94b208d58fffd671872f71a9f6a3909a.jpg"
                                alt="Australia" class="w-full h-48 object-cover">
                            <div class="p-4">
                                <div class="flex items-center mb-3">
                                    <i
                                        class="ri-flag-line text-primary mr-2 w-5 h-5 flex items-center justify-center"></i>
                                    <h3 class="font-medium">Australia</h3>
                                </div>
                                <div class="flex justify-between text-sm text-gray-600 mb-4">
                                    <span>Processing: 2-4 weeks</span>
                                    <span>From $170</span>
                                </div>
                                <button
                                    class="w-full bg-primary text-white font-medium py-2 !rounded-button hover:bg-primary/90 cursor-pointer">Apply
                                    Now</button>
                            </div>
                        </div>
                        <div
                            class="country-card relative bg-white rounded-lg shadow-lg overflow-hidden group min-w-[calc(100%-1.2rem)] md:min-w-[calc(25%-1.2rem)] mx-3">
                            <img src="api/search-image?query=Dubai%20skyline%20with%20Burj%20Khalifa%2C%20modern%20architecture%2C%20desert%20city%2C%20luxury%20travel%20destination%2C%20professional%20photography&width=400&height=250&seq=6&orientation=landscape"
                                alt="Dubai" class="w-full h-48 object-cover">
                            <div class="p-4">
                                <div class="flex items-center mb-3">
                                    <i
                                        class="ri-flag-line text-primary mr-2 w-5 h-5 flex items-center justify-center"></i>
                                    <h3 class="font-medium">Dubai</h3>
                                </div>
                                <div class="flex justify-between text-sm text-gray-600 mb-4">
                                    <span>Processing: 3-5 days</span>
                                    <span>From $120</span>
                                </div>
                                <button
                                    class="w-full bg-primary text-white font-medium py-2 !rounded-button hover:bg-primary/90 cursor-pointer">Apply
                                    Now</button>
                            </div>
                        </div>
                        <div
                            class="country-card relative bg-white rounded-lg shadow-lg overflow-hidden group min-w-[calc(100%-1.2rem)] md:min-w-[calc(25%-1.2rem)] mx-3">
                            <img src="api/search-image?query=Singapore%20Marina%20Bay%20Sands%20and%20Gardens%20by%20the%20Bay%2C%20modern%20Asian%20cityscape%2C%20night%20view%2C%20professional%20travel%20photography&width=400&height=250&seq=7&orientation=landscape"
                                alt="Singapore" class="w-full h-48 object-cover">
                            <div class="p-4">
                                <div class="flex items-center mb-3">
                                    <i
                                        class="ri-flag-line text-primary mr-2 w-5 h-5 flex items-center justify-center"></i>
                                    <h3 class="font-medium">Singapore</h3>
                                </div>
                                <div class="flex justify-between text-sm text-gray-600 mb-4">
                                    <span>Processing: 4-6 days</span>
                                    <span>From $130</span>
                                </div>
                                <button
                                    class="w-full bg-primary text-white font-medium py-2 !rounded-button hover:bg-primary/90 cursor-pointer">Apply
                                    Now</button>
                            </div>
                        </div>
                        <div
                            class="country-card relative bg-white rounded-lg shadow-lg overflow-hidden group min-w-[calc(100%-1.2rem)] md:min-w-[calc(25%-1.2rem)] mx-3">
                            <img src="api/search-image?query=Tokyo%20skyline%20with%20Mount%20Fuji%2C%20modern%20Japanese%20architecture%2C%20cherry%20blossoms%2C%20spring%20season%2C%20professional%20travel%20photography&width=400&height=250&seq=8&orientation=landscape"
                                alt="Japan" class="w-full h-48 object-cover">
                            <div class="p-4">
                                <div class="flex items-center mb-3">
                                    <i
                                        class="ri-flag-line text-primary mr-2 w-5 h-5 flex items-center justify-center"></i>
                                    <h3 class="font-medium">Japan</h3>
                                </div>
                                <div class="flex justify-between text-sm text-gray-600 mb-4">
                                    <span>Processing: 5-7 days</span>
                                    <span>From $180</span>
                                </div>
                                <button
                                    class="w-full bg-primary text-white font-medium py-2 !rounded-button hover:bg-primary/90 cursor-pointer">Apply
                                    Now</button>
                            </div>
                        </div>
                        <div
                            class="country-card relative bg-white rounded-lg shadow-lg overflow-hidden group min-w-[calc(100%-1.2rem)] md:min-w-[calc(25%-1.2rem)] mx-3">
                            <img src="api/search-image?query=Paris%20Eiffel%20Tower%20and%20Seine%20River%2C%20romantic%20French%20architecture%2C%20sunset%20golden%20hour%2C%20professional%20travel%20photography&width=400&height=250&seq=9&orientation=landscape"
                                alt="France" class="w-full h-48 object-cover">
                            <div class="p-4">
                                <div class="flex items-center mb-3">
                                    <i
                                        class="ri-flag-line text-primary mr-2 w-5 h-5 flex items-center justify-center"></i>
                                    <h3 class="font-medium">France</h3>
                                </div>
                                <div class="flex justify-between text-sm text-gray-600 mb-4">
                                    <span>Processing: 2-3 weeks</span>
                                    <span>From $145</span>
                                </div>
                                <button
                                    class="w-full bg-primary text-white font-medium py-2 !rounded-button hover:bg-primary/90 cursor-pointer">Apply
                                    Now</button>
                            </div>
                        </div>
                        <div
                            class="country-card relative bg-white rounded-lg shadow-lg overflow-hidden group min-w-[calc(100%-1.2rem)] md:min-w-[calc(25%-1.2rem)] mx-3">
                            <img src="api/search-image?query=Rome%20Colosseum%20and%20Roman%20Forum%2C%20ancient%20Italian%20architecture%2C%20historic%20landmarks%2C%20professional%20travel%20photography&width=400&height=250&seq=10&orientation=landscape"
                                alt="Italy" class="w-full h-48 object-cover">
                            <div class="p-4">
                                <div class="flex items-center mb-3">
                                    <i
                                        class="ri-flag-line text-primary mr-2 w-5 h-5 flex items-center justify-center"></i>
                                    <h3 class="font-medium">Italy</h3>
                                </div>
                                <div class="flex justify-between text-sm text-gray-600 mb-4">
                                    <span>Processing: 2-3 weeks</span>
                                    <span>From $155</span>
                                </div>
                                <button
                                    class="w-full bg-primary text-white font-medium py-2 !rounded-button hover:bg-primary/90 cursor-pointer">Apply
                                    Now</button>
                            </div>
                        </div>
                        <div
                            class="country-card relative bg-white rounded-lg shadow-lg overflow-hidden group min-w-[calc(100%-1.2rem)] md:min-w-[calc(25%-1.2rem)] mx-3">
                            <img src="api/search-image?query=Swiss%20Alps%20and%20Lake%20Geneva%2C%20mountain%20landscape%2C%20luxury%20resorts%2C%20pristine%20nature%2C%20professional%20travel%20photography&width=400&height=250&seq=11&orientation=landscape"
                                alt="Switzerland" class="w-full h-48 object-cover">
                            <div class="p-4">
                                <div class="flex items-center mb-3">
                                    <i
                                        class="ri-flag-line text-primary mr-2 w-5 h-5 flex items-center justify-center"></i>
                                    <h3 class="font-medium">Switzerland</h3>
                                </div>
                                <div class="flex justify-between text-sm text-gray-600 mb-4">
                                    <span>Processing: 2-3 weeks</span>
                                    <span>From $165</span>
                                </div>
                                <button
                                    class="w-full bg-primary text-white font-medium py-2 !rounded-button hover:bg-primary/90 cursor-pointer">Apply
                                    Now</button>
                            </div>
                        </div>
                    </div>
                </div>
                <button
                    class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-2 md:-translate-x-6 w-10 h-10 md:w-12 md:h-12 bg-white rounded-full shadow-lg flex items-center justify-center text-primary hover:bg-gray-50"
                    id="prevBtn">
                    <i class="ri-arrow-left-s-line ri-xl"></i>
                </button>
                <button
                    class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-2 md:translate-x-6 w-10 h-10 md:w-12 md:h-12 bg-white rounded-full shadow-lg flex items-center justify-center text-primary hover:bg-gray-50"
                    id="nextBtn">
                    <i class="ri-arrow-right-s-line ri-xl"></i>
                </button>
            </div>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slider = document.getElementById('destinationSlider');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            let currentPosition = 0;
            const cardWidth = slider.children[0].offsetWidth + 24; // Including margin
            const totalCards = slider.children.length;
            const visibleCards = window.innerWidth < 768 ? 1 : 4;
            const maxPosition = -(totalCards - visibleCards) * cardWidth;
            // Update on window resize
            window.addEventListener('resize', () => {
                const newVisibleCards = window.innerWidth < 768 ? 1 : 4;
                if (newVisibleCards !== visibleCards) {
                    currentPosition = 0;
                    updateSliderPosition();
                }
            });

            function updateSliderPosition() {
                slider.style.transform = `translateX(${currentPosition}px)`;
            }
            prevBtn.addEventListener('click', () => {
                if (currentPosition < 0) {
                    currentPosition += cardWidth;
                    updateSliderPosition();
                }
            });
            nextBtn.addEventListener('click', () => {
                if (currentPosition > maxPosition) {
                    currentPosition -= cardWidth;
                    updateSliderPosition();
                }
            });
        });
        </script>
    </section>
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">Our Services</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-lg shadow-lg text-center">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="ri-file-list-3-line text-primary ri-2x"></i>
                    </div>
                    <h3 class="text-xl font-medium mb-4">Document Assistance</h3>
                    <p class="text-gray-600 mb-6">Expert guidance on documentation requirements and verification
                        services</p>
                    <button class="text-primary hover:text-primary/80 font-medium cursor-pointer">Learn
                        More</button>
                </div>
                <div class="bg-white p-8 rounded-lg shadow-lg text-center">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="ri-timer-flash-line text-primary ri-2x"></i>
                    </div>
                    <h3 class="text-xl font-medium mb-4">Fast Processing</h3>
                    <p class="text-gray-600 mb-6">Quick turnaround times with priority processing options available
                    </p>
                    <button class="text-primary hover:text-primary/80 font-medium cursor-pointer">Learn
                        More</button>
                </div>
                <div class="bg-white p-8 rounded-lg shadow-lg text-center">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="ri-customer-service-2-line text-primary ri-2x"></i>
                    </div>
                    <h3 class="text-xl font-medium mb-4">Expert Support</h3>
                    <p class="text-gray-600 mb-6">24/7 customer support and personalized assistance throughout the
                        process</p>
                    <button class="text-primary hover:text-primary/80 font-medium cursor-pointer">Learn
                        More</button>
                </div>
            </div>
        </div>
    </section>
    <section class="py-20">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold mb-6">Why Choose Us</h2>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="text-center bg-white p-6 rounded-lg shadow-lg">
                            <div class="text-4xl font-bold text-primary mb-2">50K+</div>
                            <div class="text-gray-600">Visas Processed</div>
                        </div>
                        <div class="text-center bg-white p-6 rounded-lg shadow-lg">
                            <div class="text-4xl font-bold text-primary mb-2">100+</div>
                            <div class="text-gray-600">Countries Served</div>
                        </div>
                        <div class="text-center bg-white p-6 rounded-lg shadow-lg">
                            <div class="text-4xl font-bold text-primary mb-2">98%</div>
                            <div class="text-gray-600">Success Rate</div>
                        </div>
                        <div class="text-center bg-white p-6 rounded-lg shadow-lg">
                            <div class="text-4xl font-bold text-primary mb-2">24/7</div>
                            <div class="text-gray-600">Support</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-lg shadow-lg">
                    <h3 class="text-2xl font-bold mb-6">What Our Customers Say</h3>
                    <div class="space-y-6">
                        <div class="border-l-4 border-primary pl-4">
                            <p class="text-gray-600 italic mb-2">"Excellent service! The process was smooth and the
                                support team was very helpful throughout."</p>
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-200 rounded-full mr-3"></div>
                                <div>
                                    <div class="font-medium">Sarah Thompson</div>
                                    <div class="text-sm text-gray-500">United States Visa</div>
                                </div>
                            </div>
                        </div>
                        <div class="border-l-4 border-primary pl-4">
                            <p class="text-gray-600 italic mb-2">"Fast and reliable service. Got my visa approved
                                without any hassle. Highly recommended!"</p>
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-200 rounded-full mr-3"></div>
                                <div>
                                    <div class="font-medium">Michael Chen</div>
                                    <div class="text-sm text-gray-500">Australia Visa</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php

    require_once 'footer.php';

?>