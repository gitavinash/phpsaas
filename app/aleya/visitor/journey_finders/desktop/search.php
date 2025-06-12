<?php
require_once 'header.php';
?>
<!-- Search Parameters Bar -->
<div class="bg-white border-b border-gray-200 shadow-sm">
    <div class="container mx-auto px-4 py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-6">
                <div class="flex items-center">
                    <div class="w-8 h-8 flex items-center justify-center text-primary bg-primary/10 rounded-full mr-2">
                        <i class="ri-search-line"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">
                            Search for
                        </p>
                        <p class="font-medium">
                            <?php

                            $destinationPage = ucwords(str_replace('-', ' ', ((isset($q)) ? $q : '')));

                            echo $destinationPage;

                            ?>
                        </p>
                    </div>
                </div>
                <!-- <div class="flex items-center">
                    <div class="w-8 h-8 flex items-center justify-center text-primary bg-primary/10 rounded-full mr-2">
                        <i class="ri-calendar-line"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Travel Date</p>
                        <p class="font-medium">May 15 - May 22, 2025</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="w-8 h-8 flex items-center justify-center text-primary bg-primary/10 rounded-full mr-2">
                        <i class="ri-user-line"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Travelers</p>
                        <p class="font-medium">2 Adults</p>
                    </div>
                </div> -->
            </div>
            <div class="flex gap-3">
                <a href="/holiday/" data-readdy="true"
                    class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-button whitespace-nowrap text-sm hover:bg-gray-50 transition">
                    <i class="ri-arrow-left-line mr-1"></i>
                    Back
                </a>
                <!-- <button
                    class="flex items-center justify-center px-4 py-2 bg-primary/10 text-primary rounded-button whitespace-nowrap text-sm hover:bg-primary/20 transition">
                    <i class="ri-edit-line mr-1"></i>
                    Modify Search
                </button> -->
            </div>
        </div>
    </div>
</div>
<!-- Quick Filters -->
<div class="bg-white border-b border-gray-200">
    <div class="container mx-auto px-4 py-3">
        <div class="flex items-center overflow-x-auto custom-scrollbar pb-1">
            <span class="text-gray-700 font-medium mr-3 whitespace-nowrap">Quick Filters:</span>
            <div class="flex gap-2">
                <button class="px-3 py-1.5 bg-primary text-white rounded-full text-sm whitespace-nowrap">
                    Best Sellers
                </button>
                <button
                    class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm whitespace-nowrap hover:bg-gray-200 transition">
                    5-7 Days
                </button>
                <button
                    class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm whitespace-nowrap hover:bg-gray-200 transition">
                    Adventure
                </button>
                <button
                    class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm whitespace-nowrap hover:bg-gray-200 transition">
                    Cultural
                </button>
                <button
                    class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm whitespace-nowrap hover:bg-gray-200 transition">
                    Beach
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Main Content -->
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Results Content -->
        <div class="flex-1">
            <!-- Sort Controls -->
            <div
                class="bg-white rounded-lg shadow-sm p-4 mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-gray-700">
                    <span class="font-medium">Searched</span> for <?php echo $destinationPage ?>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-600 text-sm mr-2">Sort by:</span>
                    <div class="relative">
                        <select
                            class="pl-4 pr-8 py-2 border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-primary/20 text-sm appearance-none bg-white">
                            <option value="recommended">Recommended</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                            <option value="rating">Rating</option>
                            <option value="duration">Duration</option>
                        </select>
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                            <i class="ri-arrow-down-s-line text-gray-500"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tour Results Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php

                if (isset($q)) {
                    $queryFilter = "(
                        LOWER(tp.tour_package_city) LIKE '%$q%'
                        OR LOWER(tp.tour_package_title) LIKE '%$q%'
                        OR LOWER(tp.tour_package_state) LIKE '%$q%'
                        OR LOWER(tp.tour_package_country) LIKE '%{$q}') AND ";
                }

                $destinationPackagesQuery = "SELECT
	link_url,
	tour_package_title,
	tour_package_duration,
	tour_package_theme,
	tour_package_city,
	tour_package_price,
	tour_package_country,
	tour_package_state,
	tour_package_type,
	tour_package_price_selling_adult,
	tour_package_price_mrp_adult
FROM
    " . TABLE_PREFIX . "tour_package tp
INNER JOIN
    " . TABLE_PREFIX . "link l ON
    l.link_relation_id = tp.tour_package_id
    AND l.link_site_id = " . SITE_ID . "
    AND l.link_type = 'tour_package'
LEFT JOIN (
    SELECT *
    FROM " . TABLE_PREFIX . "tour_package_price tpp1
    WHERE tpp1.tour_package_price_site_id = " . SITE_ID . "
    AND tpp1.tour_package_price_selling_adult = (
        SELECT MIN(tpp2.tour_package_price_selling_adult)
        FROM " . TABLE_PREFIX . "tour_package_price tpp2
        WHERE tpp2.tour_package_price_parent_id = tpp1.tour_package_price_parent_id
        AND tpp2.tour_package_price_site_id = " . SITE_ID . "
    )
) tpp ON tpp.tour_package_price_parent_id = tp.tour_package_id
WHERE
    {$queryFilter}
    tp.tour_package_site_id = " . SITE_ID;

                if ($destinationPackages = mysqli_query($connection, $destinationPackagesQuery)) {
                    if (mysqli_num_rows($destinationPackages) > 0) {
                        while ($packageData = mysqli_fetch_assoc($destinationPackages)) {
                            extract($packageData);
                            echo "
                                <div class='tour-card bg-white rounded-lg overflow-hidden shadow-sm transition duration-300'>
                                    <div class='relative h-48 overflow-hidden'>
                                        <img src='" . USER_IMAGES . '/city/' . strtolower(str_replace(' ', '_', $tour_package_city)) . ".jpg'
                                            alt='{$tour_package_title}' class='w-full h-full object-cover object-top' />
                                        <div
                                            class='absolute top-4 left-4 bg-primary text-white text-xs font-medium px-2 py-1 rounded-full'>
                                            " . ucwords($tour_package_theme) . "
                                        </div>
                                    </div>
                                    <div class='p-4'>
                                        <div class='flex items-center text-gray-600 text-sm mb-1'>
                                            <i class='ri-map-pin-line mr-1'></i>
                                            <span>{$tour_package_city}, {$tour_package_country}</span>
                                        </div>
                                        <h3 class='font-semibold text-lg mb-2'>
                                            {$tour_package_title}
                                        </h3>
                                        <div class='flex flex-wrap gap-2 mb-3'>
                                            <span class='bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full'>" . ($tour_package_duration + 1) . " Days</span>
                                        </div>
                                        <div class='flex justify-between items-center'>
                                        <div>
                                        <span class='text-gray-500 text-sm line-through'>₹ {$tour_package_price_mrp_adult}</span>
                                        <span class='text-primary font-semibold ml-1'>₹ {$tour_package_price_selling_adult}</span>
                                        <span class='text-gray-600 text-xs'>/ person</span>
                                        </div>
                                        </div>
                                        <div class='flex justify-between items-center'>

                                        <a href='{$link_url}' target='_blank'
                                        class='bg-primary w-full my-4 text-white px-4 py-2 rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition'>View
                                        Details</a>
                                        </div>
                                    </div>
                                </div>
                            ";
                        }
                    } else {
                        echo '<div class="col-span-3 text-center text-gray-500">No packages found.</div>';
                    }
                } else {
                    echo '<div class="col-span-3 text-center text-red-500">Error fetching packages.</div>';
                }

                ?>
            </div>
            <!-- Pagination -->
            <!-- <div class="mt-10 flex justify-between items-center">
                <div class="text-gray-600 text-sm">
                    Showing <span class="font-medium">1-6</span> of
                    <span class="font-medium">42</span> results
                </div>
                <div class="flex items-center space-x-2">
                    <button
                        class="w-10 h-10 flex items-center justify-center rounded border border-gray-200 text-gray-500 hover:border-primary hover:text-primary transition">
                        <i class="ri-arrow-left-s-line"></i>
                    </button>
                    <button class="w-10 h-10 flex items-center justify-center rounded bg-primary text-white">
                        1
                    </button>
                    <button
                        class="w-10 h-10 flex items-center justify-center rounded border border-gray-200 hover:border-primary hover:text-primary transition">
                        2
                    </button>
                    <button
                        class="w-10 h-10 flex items-center justify-center rounded border border-gray-200 hover:border-primary hover:text-primary transition">
                        3
                    </button>
                    <button
                        class="w-10 h-10 flex items-center justify-center rounded border border-gray-200 hover:border-primary hover:text-primary transition">
                        4
                    </button>
                    <button
                        class="w-10 h-10 flex items-center justify-center rounded border border-gray-200 text-gray-500 hover:border-primary hover:text-primary transition">
                        <i class="ri-arrow-right-s-line"></i>
                    </button>
                </div>
            </div> -->
        </div>
    </div>
</div>
<div class="container mx-auto px-4 py-8">
    <?php

    require_once 'quick_links.php';

    ?>
</div>
<?php

require_once 'footer.php';

?>