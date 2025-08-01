<?php

require_once 'header.php';

?>
<!-- Hero Section -->
<section class="relative h-[600px] overflow-hidden">
    <div style="
          background-image: url('<?php echo USER_IMAGES ?>background.jpg');
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
                <form method="get" action="/search/">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Search Input - 2/3 width -->
                        <div class="relative col-span-1 md:col-span-2">
                            <div
                                class="w-10 h-10 flex items-center justify-center text-gray-500 absolute left-2 top-1/2 transform -translate-y-1/2">
                                <i class="ri-map-pin-line ri-lg"></i>
                            </div>
                            <input type="text" name="q" id="destination" placeholder="Destination"
                                class="pl-12 pr-4 py-3 w-full border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-primary/20 text-sm" />
                        </div>

                        <!-- Search Button - 1/3 width -->
                        <div class="col-span-1">
                            <button type="submit" data-readdy="true"
                                class="bg-primary text-white px-6 py-3 rounded-button whitespace-nowrap font-medium hover:bg-primary/90 transition flex items-center justify-center w-full h-full">
                                <i class="ri-search-line mr-2"></i>
                                Search Tours
                            </button>
                        </div>
                    </div>
                </form>
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
            <?php

            if ($checkPackage = mysqli_query($connection, "
                SELECT
                    *
                FROM
                    " . TABLE_PREFIX . "tour_package
                INNER JOIN " . TABLE_PREFIX . "link ON
                    link_relation_id = tour_package_id
                    AND link_site_id = " . SITE_ID . "
                    AND link_type = 'tour_package'
                    AND link_status = 200
                LEFT JOIN " . TABLE_PREFIX . "gallery ON
                    gallery_category = tour_package_id
                    AND gallery_site_id = " . SITE_ID . "
                    AND gallery_type = 'tour_package'
                LEFT JOIN (
                    SELECT *
                    FROM " . TABLE_PREFIX . "tour_package_price
                    WHERE tour_package_price_site_id = " . SITE_ID . "
                    AND tour_package_price_net_adult = (
                        SELECT MIN(" . TABLE_PREFIX . "tour_package_price_inner.tour_package_price_net_adult)
                        FROM " . TABLE_PREFIX . "tour_package_price AS " . TABLE_PREFIX . "tour_package_price_inner
                        WHERE 
                            " . TABLE_PREFIX . "tour_package_price_inner.tour_package_price_site_id = " . TABLE_PREFIX . "tour_package_price.tour_package_price_site_id
                            AND " . TABLE_PREFIX . "tour_package_price_inner.tour_package_price_parent_id = " . TABLE_PREFIX . "tour_package_price.tour_package_price_parent_id
                    )
                ) AS " . TABLE_PREFIX . "tour_package_price_filtered
                ON " . TABLE_PREFIX . "tour_package_price_filtered.tour_package_price_parent_id = tour_package_id
                WHERE
                    tour_package_site_id = " . SITE_ID . " ORDER BY link_published DESC LIMIT 0,3")) {
                if (mysqli_num_rows($checkPackage) > 0) {
                    while ($packageData = mysqli_fetch_assoc($checkPackage)) {
                        extract($packageData);
                        echo "
                            <div class='package-card bg-white rounded-lg overflow-hidden shadow-md transition duration-300'>
                                <a href='{$link_url}' title='Explore {$tour_package_title}'>
                                <div class='relative h-56 overflow-hidden'>
                                    <img src='{$gallery_image}'
                                        alt='{$tour_package_title}' class='w-full h-full object-cover object-top' />
                                    <div class='absolute top-4 left-4 bg-primary text-white text-xs font-medium px-2 py-1 rounded-full'>
                                        Best Seller
                                    </div>
                                    <div class='absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4'>
                                        <div class='flex items-center text-white mb-1'>
                                            <i class='ri-map-pin-line mr-1'></i>
                                            <span class='text-sm'>{$tour_package_state}, {$tour_package_country}</span>
                                        </div>
                                        <h3 class='text-white font-semibold'>
                                            {$tour_package_title}
                                        </h3>
                                    </div>
                                </div>
                                <div class='p-4'>
                                    <div class='flex items-center text-gray-600 text-sm mb-3'>
                                        <div class='flex items-center mr-4'>
                                            <i class='ri-time-line mr-1'></i>
                                            <span>" . ($tour_package_duration + 1) . " Days</span>
                                        </div>
                                        <div class='flex items-center'>
                                            <i class='ri-group-line mr-1'></i>
                                            <span>Min 2 people</span>
                                        </div>
                                    </div>
                                    <p class='text-gray-600 text-sm mb-4 two-line-text'>
                                        " . htmlspecialchars(strip_tags($tour_package_overview)) . "
                                    </p>
                                    <div class='justify-between items-center'>
                                            <span class='text-gray-500 text-sm line-through'>₹ {$tour_package_price_mrp_adult}</span>
                                            <span class='text-primary font-semibold ml-1'>₹ {$tour_package_price_selling_adult}</span>
                                            <span class='text-gray-600 text-xs'>/ person</span>
                                    </div>
                                    <div class='justify-between items-center'>
                                        <a href='{$link_url}' title='Explore {$tour_package_title}'
                                        class='block w-full my-4 bg-primary text-white px-4 py-2 rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition text-center'>
                                            Explore Now
                                        </a>
                                    </div>

                                </div>
                                </a>
                            </div>
                        ";
                    }
                }
            }

            ?>
        </div>
        <div class="text-center mt-10">
            <a href="/holiday/" class="inline-flex items-center text-primary font-medium hover:underline">
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
            <?php

            $destinationPackagesListQuery = "SELECT 
    l.location_title,
    l.location_thumbnail,
    c.country_name,
    COUNT(DISTINCT tp.tour_package_id) AS total_matching_tours,
    MIN(tpp.tour_package_price_selling_adult) AS lowest_selling_price
FROM " . TABLE_PREFIX . "locations l
INNER JOIN " . TABLE_PREFIX . "country c
    ON l.location_country = c.country_id
LEFT JOIN " . TABLE_PREFIX . "tour_package tp
    ON tp.tour_package_site_id = " . SITE_ID . "
    AND (
        LOWER(CONVERT(tp.tour_package_city USING utf8mb4)) COLLATE utf8mb4_unicode_ci LIKE CONCAT('%', LOWER(CONVERT(l.location_title USING utf8mb4)) COLLATE utf8mb4_unicode_ci, '%')
        OR LOWER(CONVERT(tp.tour_package_state USING utf8mb4)) COLLATE utf8mb4_unicode_ci LIKE CONCAT('%', LOWER(CONVERT(l.location_title USING utf8mb4)) COLLATE utf8mb4_unicode_ci, '%')
        OR LOWER(CONVERT(tp.tour_package_country USING utf8mb4)) COLLATE utf8mb4_unicode_ci LIKE CONCAT('%', LOWER(CONVERT(l.location_title USING utf8mb4)) COLLATE utf8mb4_unicode_ci, '%')
    )
LEFT JOIN " . TABLE_PREFIX . "tour_package_price tpp
    ON tpp.tour_package_price_parent_id = tp.tour_package_id
    AND tpp.tour_package_price_site_id = " . SITE_ID . "
GROUP BY
    l.location_title,
    l.location_thumbnail,
    c.country_name
ORDER BY
    total_matching_tours DESC
LIMIT 0, 8
";

            if ($destinationList = mysqli_query($connection, $destinationPackagesListQuery)) {
                if (mysqli_num_rows($destinationList) > 0) {
                    while ($destinationData = mysqli_fetch_assoc($destinationList)) {
                        extract($destinationData);
                        echo "<div class='destination-card bg-white rounded-lg overflow-hidden shadow-md transition duration-300'>
                            <a href='/packages/" . strtolower(str_replace(' ', '_', $country_name . '/' . $location_title)) . "/' title='Explore {$location_title} tour packages'>
                                <div class='relative h-48 overflow-hidden'>
                                    <img src='{$location_thumbnail}' alt='{$location_title} tour packages'
                                        class='w-full h-full object-cover object-top' />
                                    <div class='absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4'>
                                        <h3 class='text-white font-semibold'>{$location_title}</h3>
                                    </div>
                                </div>
                                <div class='p-4'>
                                    <div class='flex justify-between items-center'>
                                        <span class='text-gray-600 text-sm'>{$total_matching_tours} packages</span>
                                        " . (($total_matching_tours != 0) ? "<span class='text-primary font-medium'>From ₹ {$lowest_selling_price}</span>" : "") . "
                                    </div>
                                </div>
                            </a>
                        </div>";
                    }
                } else {
                    echo "<p class='text-gray-600 text-center col-span-full'>No destinations found.</p>";
                }
            }

            ?>
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
                <h3 class="text-lg font-semibold mb-2">Personalized Service</h3>
                <p class="text-gray-600 text-sm">
                    We craft personalized travel plans that fit your family's needs, style, and budget perfectly.
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
                    <div class="text-3xl font-bold text-primary mb-2">1,000+</div>
                    <p class="text-gray-600">Happy Travelers</p>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary mb-2">200+</div>
                    <p class="text-gray-600">Tour Packages</p>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary mb-2">50+</div>
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
                <a href="/theme/luxury/" title="Luxury tour packages">
                    <div class="relative h-48">
                        <img src="<?php echo USER_IMAGES; ?>/travel_style/luxury.jpg" alt="Luxury Travel"
                            class="w-full h-full object-cover" />
                        <div class="absolute inset-0 bg-black/20"></div>
                        <div class="absolute bottom-4 left-4">
                            <h3 class="text-white text-lg font-semibold">Luxury Travel</h3>
                            <p class="text-white/90 text-sm">
                                Premium experiences & comfort
                            </p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition">
                <a href="/theme/adventure/" title="Adventure tour packages">
                    <div class="relative h-48">
                        <img src="<?php echo USER_IMAGES; ?>/travel_style/adventure.jpg" alt="Adventure Travel"
                            class="w-full h-full object-cover" />
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
                </a>
            </div>
            <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition">
                <a href="/theme/cultural/" title="Cultural tour packages">
                    <div class="relative h-48">
                        <img src="<?php echo USER_IMAGES; ?>/travel_style/cultural.jpg" alt="Cultural Travel"
                            class="w-full h-full object-cover" />
                        <div class="absolute inset-0 bg-black/20"></div>
                        <div class="absolute bottom-4 left-4">
                            <h3 class="text-white text-lg font-semibold">
                                Cultural Travel
                            </h3>
                            <p class="text-white/90 text-sm">Immersive local experiences</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition">
                <div class="relative h-48">
                    <a href="/theme/romantic/" title="Romantic tour packages">
                        <img src="<?php echo USER_IMAGES; ?>/travel_style/romantic.jpg" alt="Romantic Getaways"
                            class="w-full h-full object-cover" />
                        <div class="absolute inset-0 bg-black/20"></div>
                        <div class="absolute bottom-4 left-4">
                            <h3 class="text-white text-lg font-semibold">
                                Romantic Getaways
                            </h3>
                            <p class="text-white/90 text-sm">Perfect for couples</p>
                        </div>
                    </a>
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
                    <a href="/theme/beaches/" title="Beaches tour packages">
                        <img src="<?php echo USER_IMAGES; ?>/terrain/beaches.jpg" alt="Beaches"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-300" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <div class="absolute bottom-6 left-6">
                            <h3 class="text-white text-2xl font-semibold mb-2">Beaches</h3>
                            <!-- <div class="flex items-center text-white/90">
                            <span class="text-sm">42 destinations</span>
                            <i class="ri-arrow-right-line ml-2"></i>
                        </div> -->
                        </div>
                    </a>
                </div>
            </div>
            <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition group">
                <div class="relative h-64">
                    <a href="/theme/mountains/" title="Mountains tour packages">
                        <img src="<?php echo USER_IMAGES; ?>/terrain/mountains.jpg" alt="Mountains"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-300" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <div class="absolute bottom-6 left-6">
                            <h3 class="text-white text-2xl font-semibold mb-2">
                                Mountains
                            </h3>
                            <!-- <div class="flex items-center text-white/90">
                            <span class="text-sm">35 destinations</span>
                            <i class="ri-arrow-right-line ml-2"></i>
                        </div> -->
                        </div>
                    </a>
                </div>
            </div>
            <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition group">
                <div class="relative h-64">
                    <a href="/theme/deserts/" title="Deserts tour packages">
                        <img src="<?php echo USER_IMAGES; ?>/terrain/deserts.jpg" alt="Deserts"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-300" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <div class="absolute bottom-6 left-6">
                            <h3 class="text-white text-2xl font-semibold mb-2">Deserts</h3>
                            <!-- <div class="flex items-center text-white/90">
                            <span class="text-sm">28 destinations</span>
                            <i class="ri-arrow-right-line ml-2"></i>
                        </div> -->
                        </div>
                    </a>
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