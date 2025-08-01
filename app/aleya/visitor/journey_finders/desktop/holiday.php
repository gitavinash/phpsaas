<?php

require_once 'header.php';

?>
<!-- Main Content -->
<div class="container mx-auto px-4 py-8">
    <!-- Featured Destinations -->
    <section class="mb-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold">Featured Destinations -
                <?php echo str_replace('-', ' ', ucwords(end($urlStructure))) ?></h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php
            if ($destinationList = mysqli_query($connection, "
            SELECT
            location_title,
            location_thumbnail,
            location_best_from,
            location_best_to,
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
            GROUP BY location_title, location_thumbnail
            ORDER BY total_matching_tours DESC")) {
                if (mysqli_num_rows($destinationList) > 0) {
                    while ($destinationData = mysqli_fetch_assoc($destinationList)) {
                        extract($destinationData);
                        echo "
                    <div class='destination-card bg-white rounded-lg overflow-hidden shadow-sm flex flex-col'>
    <a href='/packages/" . str_replace(' ', '-', strtolower($country_name . '/' . $location_title)) . "/' title='View {$location_title} Tours' class='block h-full'>
                        <div class='relative h-56 overflow-hidden'>
        <img src='{$location_thumbnail}' alt='{$location_title}' class='w-full h-full object-cover object-top' />
    </div>
    <div class='p-4 flex flex-col flex-grow'>
        <div class='flex items-center justify-between mb-2'>
            <h3 class='font-semibold text-lg'>{$location_title}</h3>
            <span class='text-sm text-gray-600'>{$country_name}</span>
        </div>";
                        if ($location_best_from != '') {
                            echo "<div class='flex items-center mb-4'>
                <div class='w-5 h-5 flex items-center justify-center text-primary mr-1'>
                    <i class='ri-calendar-line'></i>
                </div>
                <span class='text-sm text-gray-600'>Best time: " . ucwords($location_best_from) . " - " . ucwords($location_best_to) . "</span>
            </div>";
                        }
                        // Spacer to push button to bottom
                        echo "<div class='flex-grow'></div>
        <a href='/packages/" . str_replace(' ', '-', strtolower($country_name . '/' . $location_title)) . "/' data-readdy='true'
            class='block w-full bg-primary text-white text-center px-4 py-2 rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition mt-4'>
            View Tours
        </a>
    </div>
    </a>
</div>";
                    }
                } else {
                    echo "
<div class='col-span-full flex flex-col items-center justify-center text-center p-8 bg-white rounded-lg shadow-sm'>
    <i class='ri-map-pin-line text-4xl text-gray-400 mb-3'></i>
    <h2 class='text-lg font-semibold text-gray-700 mb-1'>No Destinations Found</h2>
    <p class='text-gray-500 text-sm'>Try adjusting your search or filters to explore more places.</p>
</div>";
                }
            }
            ?>
        </div>
    </section>
    <!-- Quick Links Section -->
    <?php

    require_once 'quick_links.php';

    ?>
</div>
<!-- Newsletter -->
<section class="py-12 bg-primary/5">
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