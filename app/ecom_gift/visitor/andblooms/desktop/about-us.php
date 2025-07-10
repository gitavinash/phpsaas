<?php

include_once 'header.php';

?>

<!-- Hero Banner -->
<div class="relative h-[380px] w-full mb-16">
    <div style="background-image: url('<?php echo USER_IMAGES . 'about-us-banner.jpg' ?>');"
        class="absolute inset-0 bg-cover bg-center"></div>
    <div class="absolute inset-0 bg-black bg-opacity-40"></div>
    <div class="container mx-auto px-4 h-full flex flex-col justify-center items-center text-center relative z-10">
        <div class="flex items-center text-sm text-white/80 mb-6">
            <a href="#" class="hover:text-white transition-colors">Home</a>
            <span class="mx-2">/</span>
            <span class="text-white">About Us</span>
        </div>
        <h1 class="font-serif text-4xl md:text-5xl text-white mb-4">Our Story</h1>
        <p class="text-white text-lg max-w-2xl">Crafting beautiful floral experiences since 2018</p>
    </div>
</div>
<!-- Company Introduction -->
<section class="container mx-auto px-4 py-16">
    <div class="flex flex-col md:flex-row items-center gap-12">
        <div class="w-full md:w-1/2 rounded-lg overflow-hidden">
            <img src="https://readdy.ai/api/search-image?query=elegant%2520florist%2520arranging%2520beautiful%2520bouquet%2520in%2520a%2520bright%2520modern%2520flower%2520shop%252C%2520soft%2520natural%2520lighting%252C%2520professional%2520photography%2520with%2520shallow%2520depth%2520of%2520field%252C%2520warm%2520color%2520palette%2520with%2520pastel%2520flowers%2520and%2520greenery%252C%2520high-end%2520floral%2520design%2520studio%2520with%2520wooden%2520elements&width=800&height=600&seq=about-intro&orientation=landscape"
                alt="Our Flower Shop" class="w-full h-auto object-cover object-top rounded-lg shadow-lg">
        </div>
        <div class="w-full md:w-1/2">
            <h2 class="font-serif text-3xl md:text-4xl mb-6">Bringing Nature’s Elegance to Life’s Cherished Moments</h2>
            <p class="text-gray-600 mb-6 leading-relaxed">Founded in 2018 in the heart of Chattarpur, New Delhi, &
                Blooms was born from a deep-rooted passion for curating meaningful floral experiences. What began as a
                boutique floral studio quickly blossomed into a sought-after destination for those looking to celebrate
                love, life, and everything in between—with the perfect bouquet.</p>
            <p class="text-gray-600 mb-8 leading-relaxed">At & Blooms, our floral artisans blend traditional Indian
                floral artistry with contemporary design aesthetics to craft arrangements that are both elegant and
                emotionally resonant. We thoughtfully source our blooms from trusted local and sustainable farms,
                ensuring every stem is fresh, vibrant, and ethically harvested.</p>
            <p class="text-gray-600 mb-8 leading-relaxed">Whether it’s an intimate celebration or a grand occasion,
                every arrangement we create is a tribute to nature’s timeless beauty and the emotions it can convey.</p>
        </div>
    </div>
</section>
<!-- Our Values -->
<section class="bg-gray-50 py-16">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="font-serif text-3xl md:text-4xl mb-4">Our Values</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">The principles that guide everything we do at Bloom</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-lg shadow-sm">
                <div class="w-16 h-16 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-6">
                    <i class="ri-leaf-line text-2xl"></i>
                </div>
                <h3 class="font-serif text-xl mb-4">Sustainability</h3>
                <p class="text-gray-600">We are committed to sustainable practices, from sourcing locally grown flowers
                    to using eco-friendly packaging and minimizing waste in our operations.</p>
            </div>
            <div class="bg-white p-8 rounded-lg shadow-sm">
                <div class="w-16 h-16 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-6">
                    <i class="ri-heart-line text-2xl"></i>
                </div>
                <h3 class="font-serif text-xl mb-4">Craftsmanship</h3>
                <p class="text-gray-600">Each arrangement is handcrafted with care and attention to detail, reflecting
                    our dedication to the art and craft of floral design.</p>
            </div>
            <div class="bg-white p-8 rounded-lg shadow-sm">
                <div class="w-16 h-16 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-6">
                    <i class="ri-customer-service-2-line text-2xl"></i>
                </div>
                <h3 class="font-serif text-xl mb-4">Customer Experience</h3>
                <p class="text-gray-600">We believe in creating meaningful connections with our customers, understanding
                    their needs, and exceeding their expectations at every touchpoint.</p>
            </div>
        </div>
    </div>
</section>
<!-- Key Statistics -->
<section class="container mx-auto px-4 py-20">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div class="stat-card bg-white p-8 rounded-lg border border-gray-100 text-center">
            <div
                class="w-16 h-16 flex items-center justify-center bg-primary/10 text-primary rounded-full mx-auto mb-6">
                <i class="ri-calendar-line text-2xl"></i>
            </div>
            <h3 class="text-4xl font-serif text-primary mb-2">6+</h3>
            <p class="text-gray-600">Years in Business</p>
        </div>
        <div class="stat-card bg-white p-8 rounded-lg border border-gray-100 text-center">
            <div
                class="w-16 h-16 flex items-center justify-center bg-primary/10 text-primary rounded-full mx-auto mb-6">
                <i class="ri-flower-line text-2xl"></i>
            </div>
            <h3 class="text-4xl font-serif text-primary mb-2">75,000+</h3>
            <p class="text-gray-600">Order Delivered</p>
        </div>
        <div class="stat-card bg-white p-8 rounded-lg border border-gray-100 text-center">
            <div
                class="w-16 h-16 flex items-center justify-center bg-primary/10 text-primary rounded-full mx-auto mb-6">
                <i class="ri-user-smile-line text-2xl"></i>
            </div>
            <h3 class="text-4xl font-serif text-primary mb-2">28,000+</h3>
            <p class="text-gray-600">Happy Customers</p>
        </div>
        <div class="stat-card bg-white p-8 rounded-lg border border-gray-100 text-center">
            <div
                class="w-16 h-16 flex items-center justify-center bg-primary/10 text-primary rounded-full mx-auto mb-6">
                <i class="ri-plant-line text-2xl"></i>
            </div>
            <h3 class="text-4xl font-serif text-primary mb-2">120+</h3>
            <p class="text-gray-600">Flower Varieties</p>
        </div>
    </div>
</section>
<!-- Meet Our Team -->
<section class="bg-gray-50 py-16">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="font-serif text-3xl md:text-4xl mb-4">Meet Our Team</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">The passionate minds behind our floral artistry</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <div class="team-card bg-white rounded-lg overflow-hidden shadow-sm">
                <img src="<?php echo USER_IMAGES ?>shalini_dugar.jpg" alt="Shalini Dugar"
                    class="w-full max-h-[400px] object-cover object-center mx-auto">
                <div class="p-6">
                    <h3 class="font-serif text-xl mb-1">Shalini Dugar</h3>
                    <p class="text-primary mb-3">Founder</p>
                    <div class="flex space-x-3">
                        <a href="#"
                            class="social-icon w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center text-gray-600">
                            <i class="ri-instagram-line"></i>
                        </a>
                        <a href="#"
                            class="social-icon w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center text-gray-600">
                            <i class="ri-linkedin-fill"></i>
                        </a>
                        <a href="#"
                            class="social-icon w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center text-gray-600">
                            <i class="ri-pinterest-line"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="team-card bg-white rounded-lg overflow-hidden shadow-sm">
                <img src="" alt="Ayush Dugar"
                    class="w-full min-h-[400px] max-h-[400px] object-cover object-center mx-auto">
                <div class="p-6">
                    <h3 class="font-serif text-xl mb-1">Ayush Dugar</h3>
                    <p class="text-primary mb-3">Director</p>
                    <div class="flex space-x-3">
                        <a href="#"
                            class="social-icon w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center text-gray-600">
                            <i class="ri-instagram-line"></i>
                        </a>
                        <a href="#"
                            class="social-icon w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center text-gray-600">
                            <i class="ri-linkedin-fill"></i>
                        </a>
                        <a href="#"
                            class="social-icon w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center text-gray-600">
                            <i class="ri-pinterest-line"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Testimonials -->
<!-- <section class="container mx-auto px-4 py-16">
    <div class="text-center mb-12">
        <h2 class="font-serif text-3xl md:text-4xl mb-4">What Our Customers Say</h2>
        <p class="text-gray-600 max-w-2xl mx-auto">Hear from those who have experienced the Bloom difference</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div class="bg-white p-8 rounded-lg border border-gray-100 shadow-sm">
            <div class="flex items-center text-primary mb-4">
                <i class="ri-star-fill"></i>
                <i class="ri-star-fill"></i>
                <i class="ri-star-fill"></i>
                <i class="ri-star-fill"></i>
                <i class="ri-star-fill"></i>
            </div>
            <p class="text-gray-600 mb-6 italic">"The arrangement I ordered for my mother's birthday was absolutely
                stunning. The flowers were fresh and beautifully arranged, and the delivery was right on time. My mother
                was thrilled!"</p>
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-full bg-gray-200 overflow-hidden mr-4">
                    <img src="https://readdy.ai/api/search-image?query=professional%2520headshot%2520of%2520a%2520woman%2520in%2520her%252030s%2520with%2520blonde%2520hair%252C%2520warm%2520smile%252C%2520neutral%2520background%252C%2520professional%2520portrait%2520photography&width=100&height=100&seq=testimonial1&orientation=squarish"
                        alt="Customer" class="w-full h-full object-cover object-top">
                </div>
                <div>
                    <h4 class="font-medium">Rebecca Thompson</h4>
                    <p class="text-gray-500 text-sm">New York, NY</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-8 rounded-lg border border-gray-100 shadow-sm">
            <div class="flex items-center text-primary mb-4">
                <i class="ri-star-fill"></i>
                <i class="ri-star-fill"></i>
                <i class="ri-star-fill"></i>
                <i class="ri-star-fill"></i>
                <i class="ri-star-fill"></i>
            </div>
            <p class="text-gray-600 mb-6 italic">"I've been ordering from Bloom for all my corporate events for the past
                3 years. Their arrangements always receive compliments, and their service is consistently reliable and
                professional."</p>
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-full bg-gray-200 overflow-hidden mr-4">
                    <img src="https://readdy.ai/api/search-image?query=professional%2520headshot%2520of%2520a%2520man%2520in%2520his%252040s%2520with%2520short%2520dark%2520hair%252C%2520business%2520attire%252C%2520confident%2520expression%252C%2520neutral%2520background%252C%2520professional%2520portrait%2520photography&width=100&height=100&seq=testimonial2&orientation=squarish"
                        alt="Customer" class="w-full h-full object-cover object-top">
                </div>
                <div>
                    <h4 class="font-medium">James Wilson</h4>
                    <p class="text-gray-500 text-sm">Chicago, IL</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-8 rounded-lg border border-gray-100 shadow-sm">
            <div class="flex items-center text-primary mb-4">
                <i class="ri-star-fill"></i>
                <i class="ri-star-fill"></i>
                <i class="ri-star-fill"></i>
                <i class="ri-star-fill"></i>
                <i class="ri-star-fill"></i>
            </div>
            <p class="text-gray-600 mb-6 italic">"The custom arrangement Bloom created for my wedding was beyond my
                expectations. Elizabeth took the time to understand my vision and brought it to life in the most
                beautiful way. I couldn't be happier!"</p>
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-full bg-gray-200 overflow-hidden mr-4">
                    <img src="https://readdy.ai/api/search-image?query=professional%2520headshot%2520of%2520a%2520woman%2520in%2520her%2520late%252020s%2520with%2520brown%2520hair%252C%2520natural%2520makeup%252C%2520warm%2520smile%252C%2520neutral%2520background%252C%2520professional%2520portrait%2520photography&width=100&height=100&seq=testimonial3&orientation=squarish"
                        alt="Customer" class="w-full h-full object-cover object-top">
                </div>
                <div>
                    <h4 class="font-medium">Olivia Martinez</h4>
                    <p class="text-gray-500 text-sm">San Francisco, CA</p>
                </div>
            </div>
        </div>
    </div>
</section> -->
<?php

include_once 'footer.php';

?>