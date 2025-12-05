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
                <blockquote class="border-l-4 border-primary pl-6 mb-8 italic text-gray-800 text-lg">
                    “Every journey begins with a single step - ours began with a spark of inspiration and a deep love
                    for
                    travel.”
                    <footer class="mt-4 text-sm font-semibold text-gray-600">
                        – Founder, Journey Finders
                    </footer>
                </blockquote>
                <p class="text-gray-700 mb-6">
                    Journey Finders was founded by a passionate traveler who believed that travel is
                    more than ticking destinations off a list – it's about meaningful experiences, lasting memories, and
                    personal discovery.
                </p>
                <p class="text-gray-700 mb-6">
                    The idea for Journey Finders was born during one of the founder's personal trips, where he helped a
                    group of fellow travelers navigate a foreign city and turn a stressful situation into a joyful
                    adventure. It was in that moment he realized: people want to explore the world, but they often need
                    a reliable guide – someone who truly understands travel.
                </p>
                <p class="text-gray-700 mb-6">
                    Fueled by this realization and a deep desire to bring curated, stress-free travel to others, Journey
                    Finders came to life.
                </p>
                <p class="text-gray-700">
                    Though we're a newly launched company, we've already had the joy of helping numerous travelers
                    create unforgettable journeys. With every trip planned and every smile earned, our passion grows
                    stronger.
                </p>
            </div>
            <div class="relative">
                <img src="<?php echo USER_IMAGES; ?>featured-about-us.jpg" alt="Journey Finders"
                    class="w-full h-auto rounded-xl shadow-lg" />
                <div class="absolute -bottom-6 -left-6 bg-white p-4 rounded-lg shadow-md hidden md:block">
                    <div class="flex items-center">
                        <div
                            class="w-12 h-12 flex items-center justify-center bg-primary/10 rounded-full text-primary mr-4">
                            <i class="ri-flight-takeoff-line ri-lg"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">Just the Beginning</p>
                            <p class="text-gray-600 text-sm">We can't wait to help you find your next journey</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Purpose Section -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <div class="inline-block bg-primary/10 text-primary font-medium px-4 py-2 rounded-full text-sm mb-4">
                Our Purpose
            </div>
            <h2 class="text-3xl font-bold mb-6">Why We Started</h2>
            <p class="text-gray-700">
                We started Journey Finders with a mission to provide travelers – especially families – with
                personalized, well-planned, and enriching travel experiences. From scenic escapes in Kerala to the
                rugged landscapes of Leh-Ladakh, every package we offer is designed with care and a traveler's
                perspective.
            </p>
            <p class="text-gray-700 mt-4">
                Our goal is to take the burden of planning off your shoulders and replace it with the excitement of
                discovering new places, cultures, and stories.
            </p>
        </div>
    </div>
</section>

<?php

require_once 'footer.php';

?>