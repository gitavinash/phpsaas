<?php

require_once 'header.php';

?>
<div class="relative h-[500px] w-full">
    <div style="background-image: url('<?php echo $post_image ?>'); background-size: cover; background-position: center;"
        class="absolute inset-0 after:absolute after:inset-0 after:bg-black after:bg-opacity-40"></div>
    <div class="absolute inset-0 flex items-center">
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-3xl text-white">
                <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-[1.5em]"><?php echo $post_title ?></h1>
                <div class="flex items-center space-x-4 mb-6">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full overflow-hidden mr-3">
                            <img src="<?php echo (($profile_image != '') ? $profile_image : '') ?>"
                                alt="<?php echo $profile_display_name ?>" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <p class="font-medium"><?php echo ucwords($profile_display_name) ?></p>
                            <!-- <p class="text-sm text-gray-200">Hair Restoration Specialist</p> -->
                        </div>
                    </div>
                    <div class="text-sm flex items-center">
                        <span class="mr-2"><i class="ri-calendar-line"></i></span>
                        <span><?php echo date('F d, Y', strtotime($post_time)) ?></span>
                    </div>
                    <!-- <div class="text-sm flex items-center">
                        <span class="mr-2"><i class="ri-time-line"></i></span>
                        <span>12 min read</span>
                    </div> -->
                </div>
                <div class="flex space-x-3">
                    <button
                        class="w-8 h-8 rounded-full bg-white bg-opacity-20 flex items-center justify-center hover:bg-opacity-30 transition">
                        <i class="ri-facebook-fill text-white"></i>
                    </button>
                    <button
                        class="w-8 h-8 rounded-full bg-white bg-opacity-20 flex items-center justify-center hover:bg-opacity-30 transition">
                        <i class="ri-twitter-x-fill text-white"></i>
                    </button>
                    <button
                        class="w-8 h-8 rounded-full bg-white bg-opacity-20 flex items-center justify-center hover:bg-opacity-30 transition">
                        <i class="ri-linkedin-fill text-white"></i>
                    </button>
                    <button
                        class="w-8 h-8 rounded-full bg-white bg-opacity-20 flex items-center justify-center hover:bg-opacity-30 transition">
                        <i class="ri-link text-white"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Main Content -->
<div class="container mx-auto px-4 py-12">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Blog Content -->
        <div class="lg:w-2/3">
            <div class="bg-white rounded-lg shadow-sm p-8 mb-8">
                <div class="blog-content text-gray-700">
                    <?php echo $post_description ?>
                </div>
            </div>
            <!-- Gallery Modal -->
            <div id="galleryModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden">
                <div class="absolute inset-0 flex items-center justify-center p-4">
                    <div class="relative w-full max-w-4xl">
                        <button id="closeModal"
                            class="absolute -top-12 right-0 text-white hover:text-gray-300 text-3xl">
                            <i class="ri-close-line"></i>
                        </button>
                        <div class="relative">
                            <button id="prevImage"
                                class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-12 text-white hover:text-gray-300 text-4xl">
                                <i class="ri-arrow-left-s-line"></i>
                            </button>
                            <img id="modalImage" src="" alt="" class="w-full rounded-lg">
                            <button id="nextImage"
                                class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-12 text-white hover:text-gray-300 text-4xl">
                                <i class="ri-arrow-right-s-line"></i>
                            </button>
                        </div>
                        <div class="mt-4 text-white">
                            <h3 id="modalTitle" class="text-xl font-medium"></h3>
                            <p id="modalDescription" class="text-sm opacity-80 mt-1"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sidebar -->
        <div class="lg:w-1/3 space-y-8">
            <!-- Transformation Gallery -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-bold text-xl mb-4">Transformation Gallery</h3>
                <p class="text-gray-600 mb-4 text-sm">Real results from our patients who underwent hair transplant
                    procedures.</p>
                <div class="grid grid-cols-2 gap-3">
                    <div class="relative overflow-hidden rounded-lg gallery-image cursor-pointer" data-index="0">
                        <img src="https://readdy.ai/api/search-image?query=before%2520and%2520after%2520hair%2520transplant%2520male%2520patient%252C%2520side%2520view%2520of%2520balding%2520crown%2520area%2520before%2520treatment%2520and%2520full%2520hair%2520coverage%2520after%2520treatment%252C%2520same%2520lighting%2520conditions%252C%2520neutral%2520background%252C%2520clinical%2520photography%2520style&width=400&height=300&seq=3&orientation=landscape"
                            alt="Before & After - Crown Area" class="w-full h-32 object-cover object-top">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-2">
                            <p class="text-white text-xs font-medium">Crown Restoration</p>
                        </div>
                    </div>
                    <div class="relative overflow-hidden rounded-lg gallery-image cursor-pointer" data-index="1">
                        <img src="https://readdy.ai/api/search-image?query=before%2520and%2520after%2520hair%2520transplant%2520male%2520patient%252C%2520front%2520view%2520of%2520receding%2520hairline%2520before%2520treatment%2520and%2520restored%2520hairline%2520after%2520treatment%252C%2520same%2520lighting%2520conditions%252C%2520neutral%2520background%252C%2520clinical%2520photography%2520style&width=400&height=300&seq=4&orientation=landscape"
                            alt="Before & After - Hairline" class="w-full h-32 object-cover object-top">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-2">
                            <p class="text-white text-xs font-medium">Hairline Reconstruction</p>
                        </div>
                    </div>
                    <div class="relative overflow-hidden rounded-lg gallery-image cursor-pointer" data-index="2">
                        <img src="https://readdy.ai/api/search-image?query=before%2520and%2520after%2520hair%2520transplant%2520male%2520patient%252C%2520top%2520view%2520of%2520thinning%2520hair%2520before%2520treatment%2520and%2520dense%2520hair%2520coverage%2520after%2520treatment%252C%2520same%2520lighting%2520conditions%252C%2520neutral%2520background%252C%2520clinical%2520photography%2520style&width=400&height=300&seq=5&orientation=landscape"
                            alt="Before & After - Overall Density" class="w-full h-32 object-cover object-top">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-2">
                            <p class="text-white text-xs font-medium">Overall Density</p>
                        </div>
                    </div>
                    <div class="relative overflow-hidden rounded-lg gallery-image cursor-pointer" data-index="3">
                        <img src="https://readdy.ai/api/search-image?query=before%2520and%2520after%2520hair%2520transplant%2520female%2520patient%252C%2520front%2520view%2520of%2520thinning%2520hair%2520at%2520part%2520line%2520before%2520treatment%2520and%2520thicker%2520hair%2520after%2520treatment%252C%2520same%2520lighting%2520conditions%252C%2520neutral%2520background%252C%2520clinical%2520photography%2520style&width=400&height=300&seq=6&orientation=landscape"
                            alt="Before & After - Female Pattern" class="w-full h-32 object-cover object-top">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-2">
                            <p class="text-white text-xs font-medium">Female Pattern</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center mt-4">
                    <a href="#" data-readdy="true"
                        class="bg-primary text-white px-4 py-2 rounded-button text-sm font-medium hover:bg-blue-600 transition whitespace-nowrap flex items-center">
                        <i class="ri-image-line mr-2"></i> View Full Gallery
                    </a>
                </div>
            </div>
            <!-- Author Info -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center mb-4">
                    <div class="w-16 h-16 rounded-full overflow-hidden mr-4">
                        <img src="https://readdy.ai/api/search-image?query=professional%2520headshot%2520of%2520a%2520female%2520doctor%2520with%2520short%2520dark%2520hair%2520and%2520glasses%252C%2520smiling%2520confidently%252C%2520wearing%2520a%2520white%2520coat%252C%2520neutral%2520background%252C%2520warm%2520lighting%252C%2520professional%2520portrait&width=200&height=200&seq=2&orientation=squarish"
                            alt="Dr. Sarah Johnson" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800">Dr. Sarah Johnson</h3>
                        <p class="text-gray-600 text-sm">Hair Restoration Specialist</p>
                    </div>
                </div>
                <p class="text-gray-700 mb-4">Dr. Johnson is a board-certified dermatologist specializing in hair
                    restoration with over 15 years of experience. She has performed more than 2,000 successful hair
                    transplant procedures.</p>
                <button
                    class="w-full bg-white border border-primary text-primary px-4 py-2 rounded-button font-medium hover:bg-primary hover:text-white transition whitespace-nowrap">View
                    Profile</button>
            </div>
            <!-- Related Services -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-bold text-xl mb-4">Related Services</h3>
                <div class="space-y-4">
                    <a href="#"
                        class="block p-4 border border-gray-100 rounded-lg hover:border-primary hover:shadow-sm transition">
                        <div class="flex items-center">
                            <div
                                class="w-12 h-12 rounded-full bg-primary bg-opacity-10 flex items-center justify-center mr-4">
                                <i class="ri-scissors-line text-primary text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">FUE Hair Transplant</h4>
                                <p class="text-gray-600 text-sm">Minimally invasive follicular unit extraction</p>
                            </div>
                        </div>
                    </a>
                    <a href="#"
                        class="block p-4 border border-gray-100 rounded-lg hover:border-primary hover:shadow-sm transition">
                        <div class="flex items-center">
                            <div
                                class="w-12 h-12 rounded-full bg-primary bg-opacity-10 flex items-center justify-center mr-4">
                                <i class="ri-drop-line text-primary text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">PRP Therapy</h4>
                                <p class="text-gray-600 text-sm">Platelet-rich plasma for hair growth</p>
                            </div>
                        </div>
                    </a>
                    <a href="#"
                        class="block p-4 border border-gray-100 rounded-lg hover:border-primary hover:shadow-sm transition">
                        <div class="flex items-center">
                            <div
                                class="w-12 h-12 rounded-full bg-primary bg-opacity-10 flex items-center justify-center mr-4">
                                <i class="ri-medicine-bottle-line text-primary text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Medical Hair Loss Treatment</h4>
                                <p class="text-gray-600 text-sm">Prescription solutions for hair retention</p>
                            </div>
                        </div>
                    </a>
                </div>
                <button
                    class="w-full mt-4 bg-gray-100 text-gray-700 px-4 py-2 rounded-button font-medium hover:bg-gray-200 transition whitespace-nowrap">View
                    All Services</button>
            </div>
            <!-- Expert Articles -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-bold text-xl mb-4">Expert Articles</h3>
                <div class="space-y-4">
                    <a href="#" class="flex items-start space-x-3 group">
                        <div class="w-20 h-20 rounded-lg overflow-hidden flex-shrink-0">
                            <img src="https://readdy.ai/api/search-image?query=close-up%2520of%2520hair%2520follicles%2520under%2520microscope%252C%2520detailed%2520scientific%2520view%252C%2520medical%2520research%2520imagery%252C%2520high%2520resolution%252C%2520clean%2520background%252C%2520educational%2520content&width=200&height=200&seq=13&orientation=squarish"
                                alt="Hair Follicle Health" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800 group-hover:text-primary transition">Understanding
                                Hair Follicle Health</h4>
                            <p class="text-gray-600 text-sm line-clamp-2">Learn how to maintain healthy follicles
                                for stronger hair growth.</p>
                            <p class="text-gray-500 text-xs mt-1">Dr. James Chen • 5 min read</p>
                        </div>
                    </a>
                    <a href="#" class="flex items-start space-x-3 group">
                        <div class="w-20 h-20 rounded-lg overflow-hidden flex-shrink-0">
                            <img src="https://readdy.ai/api/search-image?query=comparison%2520of%2520different%2520hair%2520loss%2520treatments%252C%2520medical%2520illustration%2520showing%2520various%2520options%252C%2520educational%2520diagram%252C%2520clean%2520layout%252C%2520informative%2520visual%2520guide&width=200&height=200&seq=14&orientation=squarish"
                                alt="Hair Loss Treatments" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800 group-hover:text-primary transition">Comparing Hair
                                Loss Treatments</h4>
                            <p class="text-gray-600 text-sm line-clamp-2">A comprehensive guide to modern hair
                                restoration options.</p>
                            <p class="text-gray-500 text-xs mt-1">Dr. Sarah Johnson • 8 min read</p>
                        </div>
                    </a>
                    <a href="#" class="flex items-start space-x-3 group">
                        <div class="w-20 h-20 rounded-lg overflow-hidden flex-shrink-0">
                            <img src="https://readdy.ai/api/search-image?query=healthy%2520diet%2520for%2520hair%2520growth%252C%2520arrangement%2520of%2520nutritious%2520foods%2520that%2520promote%2520hair%2520health%252C%2520fresh%2520ingredients%252C%2520clean%2520presentation%252C%2520bright%2520lighting%252C%2520nutritional%2520concept&width=200&height=200&seq=15&orientation=squarish"
                                alt="Nutrition for Hair" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800 group-hover:text-primary transition">Nutrition and
                                Hair Health</h4>
                            <p class="text-gray-600 text-sm line-clamp-2">The essential vitamins and minerals for
                                stronger, healthier hair.</p>
                            <p class="text-gray-500 text-xs mt-1">Dr. Maria Rodriguez • 6 min read</p>
                        </div>
                    </a>
                </div>
            </div>
            <!-- Community Discussions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-bold text-xl mb-4">Community Discussions</h3>
                <div class="space-y-4">
                    <a href="#"
                        class="block p-4 border border-gray-100 rounded-lg hover:border-primary hover:shadow-sm transition">
                        <h4 class="font-medium text-gray-800">Post-Transplant Care Tips</h4>
                        <p class="text-gray-600 text-sm mb-2">Members sharing their personal care routines after
                            procedures.</p>
                        <div class="flex items-center text-xs text-gray-500">
                            <span class="flex items-center mr-4">
                                <i class="ri-user-line mr-1"></i> 42 participants
                            </span>
                            <span class="flex items-center">
                                <i class="ri-message-2-line mr-1"></i> 87 replies
                            </span>
                        </div>
                    </a>
                    <a href="#"
                        class="block p-4 border border-gray-100 rounded-lg hover:border-primary hover:shadow-sm transition">
                        <h4 class="font-medium text-gray-800">Finding the Right Surgeon</h4>
                        <p class="text-gray-600 text-sm mb-2">Discussion on credentials to look for when choosing a
                            specialist.</p>
                        <div class="flex items-center text-xs text-gray-500">
                            <span class="flex items-center mr-4">
                                <i class="ri-user-line mr-1"></i> 28 participants
                            </span>
                            <span class="flex items-center">
                                <i class="ri-message-2-line mr-1"></i> 53 replies
                            </span>
                        </div>
                    </a>
                    <a href="#"
                        class="block p-4 border border-gray-100 rounded-lg hover:border-primary hover:shadow-sm transition">
                        <h4 class="font-medium text-gray-800">Hair Transplant Cost Analysis</h4>
                        <p class="text-gray-600 text-sm mb-2">Members comparing prices and value across different
                            clinics.</p>
                        <div class="flex items-center text-xs text-gray-500">
                            <span class="flex items-center mr-4">
                                <i class="ri-user-line mr-1"></i> 64 participants
                            </span>
                            <span class="flex items-center">
                                <i class="ri-message-2-line mr-1"></i> 112 replies
                            </span>
                        </div>
                    </a>
                </div>
                <button
                    class="w-full mt-4 bg-gray-100 text-gray-700 px-4 py-2 rounded-button font-medium hover:bg-gray-200 transition whitespace-nowrap">Join
                    Community</button>
            </div>
            <!-- Newsletter Signup -->
            <div class="bg-gradient-to-r from-primary to-blue-700 rounded-lg shadow-sm p-6 text-white">
                <h3 class="font-bold text-xl mb-2">Stay Updated</h3>
                <p class="mb-4 opacity-90">Get the latest hair restoration tips, techniques, and special offers
                    delivered to your inbox.</p>
                <div class="flex flex-col space-y-3">
                    <input type="email" placeholder="Your email address"
                        class="w-full px-4 py-3 rounded-button text-gray-700 border-none focus:outline-none focus:ring-2 focus:ring-white">
                    <button
                        class="bg-white text-primary px-4 py-3 rounded-button font-medium hover:bg-opacity-90 transition whitespace-nowrap">Subscribe
                        Now</button>
                </div>
                <p class="text-xs mt-3 opacity-80">We respect your privacy. Unsubscribe at any time.</p>
            </div>
        </div>
    </div>
</div>
<!-- You May Also Like Section -->
<div class="bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl font-bold mb-8">You May Also Like</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="#" class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition group">
                <div class="h-48 overflow-hidden">
                    <img src="https://readdy.ai/api/search-image?query=person%2520looking%2520at%2520hair%2520loss%2520treatments%2520on%2520digital%2520tablet%252C%2520researching%2520options%252C%2520modern%2520setting%252C%2520clean%2520environment%252C%2520technology%2520focus%252C%2520educational%2520content&width=600&height=400&seq=16&orientation=landscape"
                        alt="Choosing the Right Treatment"
                        class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-500">
                </div>
                <div class="p-6">
                    <div class="flex items-center text-xs text-gray-500 mb-2">
                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full mr-2">Guide</span>
                        <span>June 5, 2025</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2 group-hover:text-primary transition">How to Choose the
                        Right Hair Loss Treatment for You</h3>
                    <p class="text-gray-600 text-sm mb-4">A comprehensive guide to navigating the many options
                        available for treating hair loss.</p>
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full overflow-hidden mr-3">
                            <img src="https://readdy.ai/api/search-image?query=professional%2520headshot%2520of%2520a%2520male%2520doctor%2520with%2520glasses%2520and%2520short%2520dark%2520hair%252C%2520smiling%2520confidently%252C%2520wearing%2520a%2520white%2520coat%252C%2520neutral%2520background%252C%2520warm%2520lighting&width=100&height=100&seq=17&orientation=squarish"
                                alt="Dr. James Chen" class="w-full h-full object-cover">
                        </div>
                        <span class="text-sm text-gray-700">Dr. James Chen</span>
                    </div>
                </div>
            </a>
            <a href="#" class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition group">
                <div class="h-48 overflow-hidden">
                    <img src="https://readdy.ai/api/search-image?query=close-up%2520of%2520person%2520applying%2520hair%2520growth%2520serum%2520to%2520scalp%252C%2520self-care%2520routine%252C%2520home%2520treatment%252C%2520clean%2520environment%252C%2520soft%2520lighting%252C%2520wellness%2520focus&width=600&height=400&seq=18&orientation=landscape"
                        alt="Non-Surgical Options"
                        class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-500">
                </div>
                <div class="p-6">
                    <div class="flex items-center text-xs text-gray-500 mb-2">
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full mr-2">Treatment</span>
                        <span>May 28, 2025</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2 group-hover:text-primary transition">5 Non-Surgical Hair
                        Restoration Options That Actually Work</h3>
                    <p class="text-gray-600 text-sm mb-4">Explore effective alternatives to surgery for treating
                        hair loss and improving hair density.</p>
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full overflow-hidden mr-3">
                            <img src="https://readdy.ai/api/search-image?query=professional%2520headshot%2520of%2520a%2520female%2520doctor%2520with%2520blonde%2520hair%2520in%2520a%2520ponytail%252C%2520smiling%2520warmly%252C%2520wearing%2520a%2520white%2520coat%252C%2520neutral%2520background%252C%2520warm%2520lighting&width=100&height=100&seq=19&orientation=squarish"
                                alt="Dr. Maria Rodriguez" class="w-full h-full object-cover">
                        </div>
                        <span class="text-sm text-gray-700">Dr. Maria Rodriguez</span>
                    </div>
                </div>
            </a>
            <a href="#" class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition group">
                <div class="h-48 overflow-hidden">
                    <img src="https://readdy.ai/api/search-image?query=person%2520looking%2520in%2520mirror%2520examining%2520their%2520hairline%252C%2520self-assessment%252C%2520bathroom%2520setting%252C%2520clean%2520environment%252C%2520soft%2520lighting%252C%2520personal%2520care%2520moment&width=600&height=400&seq=20&orientation=landscape"
                        alt="Hair Loss Signs"
                        class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-500">
                </div>
                <div class="p-6">
                    <div class="flex items-center text-xs text-gray-500 mb-2">
                        <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full mr-2">Education</span>
                        <span>June 2, 2025</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2 group-hover:text-primary transition">Early Signs of Hair
                        Loss and When to Seek Treatment</h3>
                    <p class="text-gray-600 text-sm mb-4">Learn to recognize the warning signs of hair loss and
                        understand when professional help is needed.</p>
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full overflow-hidden mr-3">
                            <img src="https://readdy.ai/api/search-image?query=professional%2520headshot%2520of%2520a%2520male%2520doctor%2520with%2520short%2520gray%2520hair%2520and%2520beard%252C%2520smiling%2520confidently%252C%2520wearing%2520a%2520white%2520coat%252C%2520neutral%2520background%252C%2520warm%2520lighting&width=100&height=100&seq=21&orientation=squarish"
                                alt="Dr. Robert Kim" class="w-full h-full object-cover">
                        </div>
                        <span class="text-sm text-gray-700">Dr. Robert Kim</span>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
<?php

require_once 'footer.php';

?>