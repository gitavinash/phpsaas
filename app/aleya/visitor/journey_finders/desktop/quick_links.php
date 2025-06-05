<!-- Quick Links Section -->
<section class="mb-12">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">Explore by Travel Theme</h2>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <!-- Theme Card 1 -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden group hover:shadow-md transition">
            <div class="h-32 overflow-hidden relative">
                <a href="/theme/adventure/" title="Adventure tour packages">
                    <img src="<?php echo USER_IMAGES ?>travel_style/adventure.jpg" alt="Adventure"
                        class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-500" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <h3 class="absolute bottom-3 left-3 text-white font-semibold">
                        Adventure
                    </h3>
                </a>
            </div>
        </div>
        <!-- Theme Card 2 -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden group hover:shadow-md transition">
            <div class="h-32 overflow-hidden relative">
                <a href="/theme/beaches/" title="Beaches tour packages">
                    <img src="<?php echo USER_IMAGES ?>travel_style/beaches.jpg" alt="Beach"
                        class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-500" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <h3 class="absolute bottom-3 left-3 text-white font-semibold">
                        Beach
                    </h3>
                </a>
            </div>
        </div>
        <!-- Theme Card 3 -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden group hover:shadow-md transition">
            <div class="h-32 overflow-hidden relative">
                <a href="/theme/cultural/" title="Cultural tour packages">
                    <img src="<?php echo USER_IMAGES ?>travel_style/cultural.jpg" alt="Cultural"
                        class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-500" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <h3 class="absolute bottom-3 left-3 text-white font-semibold">
                        Cultural
                    </h3>
                </a>
            </div>
        </div>
        <!-- Theme Card 4 -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden group hover:shadow-md transition">
            <div class="h-32 overflow-hidden relative">
                <a href="/theme/wildlife/" title="Wildlife tour packages">
                    <img src="<?php echo USER_IMAGES ?>travel_style/wildlife.jpg" alt="Wildlife"
                        class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-500" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <h3 class="absolute bottom-3 left-3 text-white font-semibold">
                        Wildlife
                    </h3>
                </a>
            </div>
        </div>
        <!-- Theme Card 5 -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden group hover:shadow-md transition">
            <div class="h-32 overflow-hidden relative">
                <a href="/theme/food-and-culinary/" title="Food & Culinary tour packages">
                    <img src="<?php echo USER_IMAGES ?>travel_style/food-and-culinary.jpg" alt="Food & Culinary"
                        class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-500" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <h3 class="absolute bottom-3 left-3 text-white font-semibold">
                        Food & Culinary
                    </h3>
                </a>
            </div>
        </div>
        <!-- Theme Card 6 -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden group hover:shadow-md transition">
            <div class="h-32 overflow-hidden relative">
                <a href="/theme/wellness/" title="Wellness tour packages">
                    <img src="<?php echo USER_IMAGES ?>travel_style/wellness.jpg" alt="Wellness"
                        class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-500" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <h3 class="absolute bottom-3 left-3 text-white font-semibold">
                        Wellness
                    </h3>
                </a>
            </div>
        </div>
    </div>
</section>
<!-- Trending Destinations -->
<section class="mb-12">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">Trending Destinations</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php

        if ($destinationList = mysqli_query($connection, "
    SELECT 
    location_title,
    location_thumbnail,
    country_name,
    COUNT(tour_package_city) AS total_matching_tours,
    MIN(tour_package_price_selling_adult) AS lowest_selling_price
    FROM " . TABLE_PREFIX . "locations
    INNER JOIN 
    isw_country ON isw_locations.location_country = isw_country.country_id
    LEFT JOIN " . TABLE_PREFIX . "tour_package
    ON LOWER(CONVERT(tour_package_city USING utf8mb4)) COLLATE utf8mb4_unicode_ci
        LIKE CONCAT('%', LOWER(CONVERT(location_title USING utf8mb4)) COLLATE utf8mb4_unicode_ci, '%')
    AND tour_package_site_id = " . SITE_ID . "
    LEFT JOIN " . TABLE_PREFIX . "tour_package_price
    ON tour_package_price_parent_id = tour_package_id
    AND tour_package_price_site_id = " . SITE_ID . "
    GROUP BY location_title, location_thumbnail, country_name
    ORDER BY RAND() LIMIT 0, 3")) {
            if (mysqli_num_rows($destinationList) > 0) {
                while ($destinationData = mysqli_fetch_assoc($destinationList)) {
                    extract($destinationData);
                    echo "<div class='destination-card bg-white rounded-lg overflow-hidden shadow-sm'>
                        <a href='/holiday/" . strtolower(str_replace(' ', '_', $location_title)) . "/'>
                                <div class='relative h-64 overflow-hidden'>
                                    <img src='{$location_thumbnail}'
                                        alt='{$location_title}' class='w-full h-full object-cover object-top' />
                                    <div class='absolute inset-0 bg-gradient-to-t from-black/60 to-transparent'></div>
                                    <div class='absolute bottom-4 left-4 text-white'>
                                        <h3 class='font-bold text-xl mb-1'>{$location_title}</h3>
                                        <p class='text-white/90'>{$country_name}</p>
                                    </div>
                                    <div
                                        class='absolute top-4 right-4 bg-white/90 backdrop-blur-sm text-primary text-xs font-medium px-2 py-1 rounded-full'>
                                        Trending
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
</section>