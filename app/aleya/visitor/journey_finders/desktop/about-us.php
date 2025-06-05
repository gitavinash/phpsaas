<?php

require_once 'header.php';

?>
<!-- Hero Section -->
<section class="relative overflow-hidden">
    <div style="
          background-image: url('<?php echo USER_IMAGES; ?>about-us-background.jpg');
        " class="w-full h-96 bg-cover bg-center"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/50 to-transparent"></div>
    <div class="container mx-auto px-4 relative">
        <div class="absolute top-1/2 left-0 transform -translate-y-1/2 max-w-2xl px-4">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                About JourneyFinders
            </h1>
            <p class="text-xl text-white/90 mb-6">
                Creating unforgettable travel experiences since 2010. We don't just
                plan trips, we craft journeys that transform lives.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="#our-story"
                    class="bg-primary text-white px-6 py-3 !rounded-button whitespace-nowrap font-medium hover:bg-primary/90 transition">Our
                    Story</a>
                <a href="#meet-team"
                    class="bg-white text-gray-800 px-6 py-3 !rounded-button whitespace-nowrap font-medium hover:bg-gray-100 transition">Meet
                    Our Team</a>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumbs -->
<div class="bg-white border-b border-gray-100">
    <div class="container mx-auto px-4 py-3">
        <div class="flex items-center text-sm text-gray-500">
            <a href="#" class="hover:text-primary">Home</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700 font-medium">About Us</span>
        </div>
    </div>
</div>
<!-- Our Story Section -->
<section id="our-story" class="py-16">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <div class="inline-block bg-primary/10 text-primary font-medium px-4 py-2 rounded-full text-sm mb-4">
                    Our Journey
                </div>
                <h2 class="text-3xl font-bold mb-6">The JourneyFinders Story</h2>
                <p class="text-gray-700 mb-6">
                    Founded in 2010 by a group of passionate travelers with over 30
                    years of combined experience in the travel industry,
                    JourneyFinders was born out of a desire to create meaningful
                    travel experiences that go beyond the typical tourist attractions.
                </p>
                <p class="text-gray-700 mb-6">
                    What began as a small operation with just three employees in a
                    tiny office in Mumbai has grown into a respected travel company
                    with offices across India and partnerships worldwide. Throughout
                    our growth, we've remained committed to our founding principles:
                    authentic experiences, responsible tourism, and personalized
                    service.
                </p>
                <p class="text-gray-700">
                    Today, we're proud to have helped over 50,000 travelers explore
                    the world, creating memories that last a lifetime. Our team of
                    experienced travel consultants, local guides, and destination
                    experts work tirelessly to craft journeys that surprise, delight,
                    and transform.
                </p>
            </div>
            <div class="relative">
                <img src="<?php echo USER_IMAGES; ?>featured-about-us.jpg" alt="JourneyFinders Founders"
                    class="w-full h-auto rounded-xl shadow-lg" />
                <div class="absolute -bottom-6 -left-6 bg-white p-4 rounded-lg shadow-md hidden md:block">
                    <div class="flex items-center">
                        <div
                            class="w-12 h-12 flex items-center justify-center bg-primary/10 rounded-full text-primary mr-4">
                            <i class="ri-flight-takeoff-line ri-lg"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">15+ Years</p>
                            <p class="text-gray-600 text-sm">Creating Amazing Journeys</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Mission & Values Section -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <div class="inline-block bg-primary/10 text-primary font-medium px-4 py-2 rounded-full text-sm mb-4">
                Our Purpose
            </div>
            <h2 class="text-3xl font-bold mb-6">Mission & Values</h2>
            <p class="text-gray-700">
                Our mission is to create transformative travel experiences that
                foster cultural understanding, preserve natural environments, and
                support local communities while exceeding our clients' expectations.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Value 1 -->
            <div class="value-card bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition duration-300">
                <div
                    class="value-icon w-16 h-16 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-6">
                    <i class="ri-earth-line ri-2x"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Authentic Experiences</h3>
                <p class="text-gray-700">
                    We believe travel should be immersive and authentic. Our
                    itineraries take you beyond tourist hotspots to experience the
                    true essence of each destination through meaningful cultural
                    exchanges and local connections.
                </p>
            </div>
            <!-- Value 2 -->
            <div class="value-card bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition duration-300">
                <div
                    class="value-icon w-16 h-16 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-6">
                    <i class="ri-recycle-line ri-2x"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Responsible Tourism</h3>
                <p class="text-gray-700">
                    We're committed to sustainable travel practices that minimize
                    environmental impact and maximize benefits to local communities.
                    We carefully select partners who share our commitment to
                    responsible tourism.
                </p>
            </div>
            <!-- Value 3 -->
            <div class="value-card bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition duration-300">
                <div
                    class="value-icon w-16 h-16 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-6">
                    <i class="ri-heart-line ri-2x"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Personalized Service</h3>
                <p class="text-gray-700">
                    We understand that every traveler is unique. That's why we take
                    the time to understand your preferences, interests, and travel
                    style to create customized journeys that exceed your expectations.
                </p>
            </div>
            <!-- Value 4 -->
            <div class="value-card bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition duration-300">
                <div
                    class="value-icon w-16 h-16 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-6">
                    <i class="ri-shield-check-line ri-2x"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Safety & Reliability</h3>
                <p class="text-gray-700">
                    Your safety is our priority. We maintain rigorous safety
                    standards, provide 24/7 support during your journey, and work with
                    trusted partners to ensure peace of mind throughout your travels.
                </p>
            </div>
            <!-- Value 5 -->
            <div class="value-card bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition duration-300">
                <div
                    class="value-icon w-16 h-16 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-6">
                    <i class="ri-group-line ri-2x"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Community Connection</h3>
                <p class="text-gray-700">
                    We believe in creating meaningful connections between travelers
                    and local communities. Our tours include opportunities to engage
                    with local people, support local businesses, and contribute to
                    community initiatives.
                </p>
            </div>
            <!-- Value 6 -->
            <div class="value-card bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition duration-300">
                <div
                    class="value-icon w-16 h-16 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-6">
                    <i class="ri-book-open-line ri-2x"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Continuous Learning</h3>
                <p class="text-gray-700">
                    We're constantly exploring, learning, and improving. Our team
                    regularly visits destinations, undergoes training, and stays
                    updated on travel trends to provide the most current and valuable
                    guidance.
                </p>
            </div>
        </div>
    </div>
</section>
<!-- Why Choose Us Section -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <div class="inline-block bg-primary/10 text-primary font-medium px-4 py-2 rounded-full text-sm mb-4">
                Our Difference
            </div>
            <h2 class="text-3xl font-bold mb-6">Why Choose JourneyFinders</h2>
            <p class="text-gray-700">
                What sets us apart is our commitment to creating travel experiences
                that are as unique as you are. Here's why travelers choose us for
                their adventures.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                <img src="<?php echo USER_IMAGES; ?>award-winning-team.jpg" alt="Guided Tour Experience"
                    class="w-full h-auto rounded-xl shadow-lg" />
            </div>
            <div>
                <div class="space-y-6">
                    <div class="flex">
                        <div
                            class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-full mr-4 flex-shrink-0">
                            <i class="ri-team-line ri-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-2">Expert Local Guides</h3>
                            <p class="text-gray-700">
                                Our guides are passionate locals with deep knowledge of
                                their regions. They provide insider perspectives, share
                                hidden gems, and bring destinations to life through their
                                stories and expertise.
                            </p>
                        </div>
                    </div>
                    <div class="flex">
                        <div
                            class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-full mr-4 flex-shrink-0">
                            <i class="ri-medal-line ri-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-2">Award-Winning Service</h3>
                            <p class="text-gray-700">
                                Recognized by Travel + Leisure and Condé Nast Traveler for
                                excellence in specialized cultural tours, our dedication to
                                exceptional service has earned us a 97% client satisfaction
                                rate.
                            </p>
                        </div>
                    </div>
                    <div class="flex">
                        <div
                            class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-full mr-4 flex-shrink-0">
                            <i class="ri-hand-heart-line ri-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-2">Sustainable Practices</h3>
                            <p class="text-gray-700">
                                We're certified by Travelife for our commitment to
                                sustainability. We offset carbon emissions, minimize plastic
                                use, support conservation projects, and ensure tourism
                                benefits local communities.
                            </p>
                        </div>
                    </div>
                    <div class="flex">
                        <div
                            class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-full mr-4 flex-shrink-0">
                            <i class="ri-customer-service-2-line ri-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-2">24/7 Support</h3>
                            <p class="text-gray-700">
                                Travel with confidence knowing our dedicated support team is
                                available around the clock. Whether you need assistance with
                                last-minute changes or unexpected situations, we're always
                                just a call away.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Achievements Timeline -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <div class="inline-block bg-primary/10 text-primary font-medium px-4 py-2 rounded-full text-sm mb-4">
                Our Milestones
            </div>
            <h2 class="text-3xl font-bold mb-6">Our Journey & Achievements</h2>
            <p class="text-gray-700">
                From our humble beginnings to becoming a leading cultural tour
                operator, we've reached several milestones along the way. Here's a
                look at our journey.
            </p>
        </div>
        <div class="max-w-4xl mx-auto">
            <div class="space-y-8">
                <!-- Timeline Item 1 -->
                <div class="timeline-item relative pl-12">
                    <div
                        class="absolute left-0 top-0 w-8 h-8 flex items-center justify-center bg-primary rounded-full text-white">
                        <i class="ri-flag-line"></i>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <div class="flex flex-wrap justify-between items-center mb-3">
                            <h3 class="text-xl font-bold">Company Founded</h3>
                            <span class="text-primary font-medium">2010</span>
                        </div>
                        <p class="text-gray-700">
                            JourneyFinders was established in Mumbai by Priya Sharma with
                            a mission to create authentic cultural experiences. Started
                            with just 3 employees and specialized in South Asian
                            destinations.
                        </p>
                    </div>
                </div>
                <!-- Timeline Item 2 -->
                <div class="timeline-item relative pl-12">
                    <div
                        class="absolute left-0 top-0 w-8 h-8 flex items-center justify-center bg-primary rounded-full text-white">
                        <i class="ri-global-line"></i>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <div class="flex flex-wrap justify-between items-center mb-3">
                            <h3 class="text-xl font-bold">International Expansion</h3>
                            <span class="text-primary font-medium">2013</span>
                        </div>
                        <p class="text-gray-700">
                            Expanded our offerings to include Southeast Asia destinations.
                            Opened our second office in Delhi and grew to a team of 15
                            travel specialists. Served our 5,000th customer.
                        </p>
                    </div>
                </div>
                <!-- Timeline Item 3 -->
                <div class="timeline-item relative pl-12">
                    <div
                        class="absolute left-0 top-0 w-8 h-8 flex items-center justify-center bg-primary rounded-full text-white">
                        <i class="ri-award-line"></i>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <div class="flex flex-wrap justify-between items-center mb-3">
                            <h3 class="text-xl font-bold">First Industry Recognition</h3>
                            <span class="text-primary font-medium">2016</span>
                        </div>
                        <p class="text-gray-700">
                            Received "Best Cultural Tour Operator" award from Travel +
                            Leisure India. Launched our sustainable tourism initiative and
                            became Travelife certified. Expanded to 30 employees across
                            three offices.
                        </p>
                    </div>
                </div>
                <!-- Timeline Item 4 -->
                <div class="timeline-item relative pl-12">
                    <div
                        class="absolute left-0 top-0 w-8 h-8 flex items-center justify-center bg-primary rounded-full text-white">
                        <i class="ri-heart-line"></i>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <div class="flex flex-wrap justify-between items-center mb-3">
                            <h3 class="text-xl font-bold">Community Foundation</h3>
                            <span class="text-primary font-medium">2019</span>
                        </div>
                        <p class="text-gray-700">
                            Established the JourneyFinders Foundation to support education
                            and conservation projects in the communities we visit. Reached
                            the milestone of 25,000 travelers served across 20+ countries.
                        </p>
                    </div>
                </div>
                <!-- Timeline Item 5 -->
                <div class="timeline-item relative pl-12">
                    <div
                        class="absolute left-0 top-0 w-8 h-8 flex items-center justify-center bg-primary rounded-full text-white">
                        <i class="ri-medal-line"></i>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <div class="flex flex-wrap justify-between items-center mb-3">
                            <h3 class="text-xl font-bold">Global Recognition</h3>
                            <span class="text-primary font-medium">2023</span>
                        </div>
                        <p class="text-gray-700">
                            Named in Condé Nast Traveler's "Top 10 Specialist Tour
                            Operators" globally. Expanded to 50+ employees across five
                            offices. Launched our carbon-neutral travel initiative and
                            offset emissions for all tours.
                        </p>
                    </div>
                </div>
                <!-- Timeline Item 6 -->
                <div class="timeline-item relative pl-12">
                    <div
                        class="absolute left-0 top-0 w-8 h-8 flex items-center justify-center bg-primary rounded-full text-white">
                        <i class="ri-rocket-line"></i>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <div class="flex flex-wrap justify-between items-center mb-3">
                            <h3 class="text-xl font-bold">Looking Ahead</h3>
                            <span class="text-primary font-medium">2025</span>
                        </div>
                        <p class="text-gray-700">
                            Today, we continue to innovate and expand our offerings while
                            staying true to our core values. With over 50,000 satisfied
                            travelers and counting, we're excited for the journey ahead as
                            we explore new destinations and create even more meaningful
                            experiences.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Testimonials Section -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <div class="inline-block bg-primary/10 text-primary font-medium px-4 py-2 rounded-full text-sm mb-4">
                Client Stories
            </div>
            <h2 class="text-3xl font-bold mb-6">What Our Travelers Say</h2>
            <p class="text-gray-700">
                Don't just take our word for it. Here's what some of our travelers
                have to say about their experiences with JourneyFinders.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Testimonial 1 -->
            <div class="bg-white p-6 rounded-xl shadow-sm">
                <div class="flex items-center mb-4">
                    <div
                        class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center text-primary font-medium mr-4">
                        MR
                    </div>
                    <div>
                        <h4 class="font-bold">Michael Rodriguez</h4>
                        <p class="text-sm text-gray-600">Bali Cultural Explorer Tour</p>
                    </div>
                </div>
                <div class="text-yellow-400 flex mb-4">
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                </div>
                <p class="text-gray-700 mb-4">
                    "Our Bali tour exceeded all expectations. Our guide Wayan was
                    incredibly knowledgeable and took us to hidden gems we would never
                    have found on our own. The cooking class and sunrise trek were
                    highlights, but it was the connections with local people that made
                    this trip truly special."
                </p>
                <p class="text-gray-500 text-sm">Traveled April 2025</p>
            </div>
            <!-- Testimonial 2 -->
            <div class="bg-white p-6 rounded-xl shadow-sm">
                <div class="flex items-center mb-4">
                    <div
                        class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center text-primary font-medium mr-4">
                        EJ
                    </div>
                    <div>
                        <h4 class="font-bold">Emily Johnson</h4>
                        <p class="text-sm text-gray-600">Japan Heritage Journey</p>
                    </div>
                </div>
                <div class="text-yellow-400 flex mb-4">
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                </div>
                <p class="text-gray-700 mb-4">
                    "JourneyFinders created a perfect balance of cultural immersion
                    and comfort for our family trip to Japan. From a private tea
                    ceremony to staying in a traditional ryokan, every experience felt
                    authentic yet accessible for our children. The attention to detail
                    was impressive."
                </p>
                <p class="text-gray-500 text-sm">Traveled March 2025</p>
            </div>
            <!-- Testimonial 3 -->
            <div class="bg-white p-6 rounded-xl shadow-sm">
                <div class="flex items-center mb-4">
                    <div
                        class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center text-primary font-medium mr-4">
                        AP
                    </div>
                    <div>
                        <h4 class="font-bold">Ananya Patel</h4>
                        <p class="text-sm text-gray-600">
                            Peru & Machu Picchu Adventure
                        </p>
                    </div>
                </div>
                <div class="text-yellow-400 flex mb-4">
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-fill"></i>
                    <i class="ri-star-half-fill"></i>
                </div>
                <p class="text-gray-700 mb-4">
                    "From the moment we landed in Lima to our final day exploring
                    Machu Picchu, JourneyFinders ensured our trip was seamless and
                    memorable. Our guide Carlos was passionate about Peruvian history
                    and culture, and the accommodations were perfect. Highly
                    recommend!"
                </p>
                <p class="text-gray-500 text-sm">Traveled February 2025</p>
            </div>
        </div>
        <!-- <div class="mt-12 text-center">
            <a href="#"
                class="bg-white text-gray-800 px-6 py-3 !rounded-button whitespace-nowrap font-medium hover:bg-gray-100 transition inline-flex items-center">
                <span>Read More Testimonials</span>
                <i class="ri-arrow-right-line ml-2"></i>
            </a>
        </div> -->
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-primary/5">
    <div class="container mx-auto px-4">
        <div class="max-w-5xl mx-auto bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2">
                <div class="p-8 md:p-12">
                    <h2 class="text-3xl font-bold mb-4">
                        Ready to Start Your Journey?
                    </h2>
                    <p class="text-gray-700 mb-6">
                        Whether you're dreaming of a cultural immersion, adventure
                        expedition, or relaxing getaway, our travel experts are ready to
                        craft the perfect experience for you.
                    </p>
                    <div class="space-y-4">
                        <a href="/tour-packages/" data-readdy="true"
                            class="bg-primary text-white px-6 py-3 !rounded-button whitespace-nowrap font-medium hover:bg-primary/90 transition inline-block">Explore
                            Our Tours</a>
                        <a href="/contact-us/"
                            class="bg-white text-gray-800 border border-gray-200 px-6 py-3 !rounded-button whitespace-nowrap font-medium hover:bg-gray-50 transition inline-block ml-0 md:ml-4 mt-4 md:mt-0">Contact
                            Us</a>
                    </div>
                </div>
                <div class="relative h-64 md:h-auto">
                    <img src="<?php echo USER_IMAGES; ?>ready-about-us.jpg" alt="Travel Consultation"
                        class="absolute inset-0 w-full h-full object-cover object-top" />
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Newsletter -->
<section class="py-12">
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