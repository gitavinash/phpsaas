<?php
require_once 'header.php';

require_once 'quick_filters.php';

?>
<!-- Main Content -->
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Results Content -->
        <div class="flex-1">
            <!-- Sort Controls -->
            <div
                class="bg-white rounded-lg shadow-sm p-4 mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-gray-700">
                    <span class="font-medium"><?php echo $themePage ?> theme tour packages
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

                // Filter functionality

                $filter = $_GET['filter'] ?? '';
                $value = $_GET['value'] ?? '';

                $filterQuery = ' AND ' . buildFilterQuery($filter, $value, $connection);

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
	tour_package_price_mrp_adult,
    gallery_image
FROM
    " . TABLE_PREFIX . "tour_package tp
INNER JOIN
    " . TABLE_PREFIX . "link l ON
    l.link_relation_id = tp.tour_package_id
    AND l.link_site_id = " . SITE_ID . "
    AND l.link_type = 'tour_package'
                LEFT JOIN " . TABLE_PREFIX . "gallery ON
                    gallery_category = tour_package_id
                    AND gallery_site_id = " . SITE_ID . "
                    AND gallery_type = 'tour_package'
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
    tp.tour_package_theme LIKE '%{$themePage}' {$filterQuery} 
    tp.tour_package_site_id = " . SITE_ID;

                if ($destinationPackages = mysqli_query($connection, $destinationPackagesQuery)) {
                    if (mysqli_num_rows($destinationPackages) > 0) {
                        while ($packageData = mysqli_fetch_assoc($destinationPackages)) {
                            extract($packageData);
                            echo "
                <div class='tour-card bg-white rounded-lg overflow-hidden shadow-sm transition duration-300 flex flex-col'>
                <a href='{$link_url}' target='_blank' title='{$tour_package_title}'
                        class='flex flex-col h-full'>
                    <div class='relative h-48 overflow-hidden'>
                        <img src='{$gallery_image}'
                            alt='{$tour_package_title}' class='w-full h-full object-cover object-top' />
                        <div
                            class='absolute top-4 left-4 bg-primary text-white text-xs font-medium px-2 py-1 rounded-full'>
                            " . ucwords($tour_package_theme) . "
                        </div>
                    </div>
                    <div class='p-4 flex flex-col flex-grow'>
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

                        <!-- Spacer to push price & button to bottom -->
                        <div class='flex-grow'></div>

                        <div class='mb-2'>";
                            if ($tour_package_price_mrp_adult != $tour_package_price_selling_adult && $tour_package_price_mrp_adult != 0) {
                                echo "<span class='text-gray-500 text-sm line-through'>₹ {$tour_package_price_mrp_adult} /-</span>
<span class='text-primary font-semibold ml-1'>₹ {$tour_package_price_selling_adult}</span>";
                            } else {
                                echo "<span class='text-primary font-semibold'>₹ {$tour_package_price_selling_adult} /-</span>";
                            }
                            echo "<span class='text-gray-600 text-xs'> per person</span>
                        </div>

                        <a href='{$link_url}' target='_blank'
                            class='bg-primary w-full mt-auto text-white px-4 py-2 rounded-button whitespace-nowrap text-sm font-medium hover:bg-primary/90 transition'>View
                            Details</a>
                    </div>
                    </a>
                </div>
            ";
                        }
                    } else {
                        echo '
<div class="col-span-full flex flex-col items-center justify-center text-center p-8 bg-white rounded-lg shadow-sm">
    <i class="ri-inbox-line text-5xl text-gray-400 mb-4"></i>
    <h2 class="text-xl font-semibold text-gray-700 mb-2">No Packages Found</h2>
    <p class="text-gray-500 mb-4">We couldn’t find any tour packages matching your search or filters.</p>
    <a href="/holiday/" class="px-4 py-2 bg-primary text-white rounded-button text-sm font-medium hover:bg-primary/90 transition">
        Back to All Destinations
    </a>
</div>';
                    }
                } else {
                    echo '
<div class="col-span-full flex flex-col items-center justify-center text-center p-8 bg-white rounded-lg shadow-sm">
    <i class="ri-error-warning-line text-5xl text-red-400 mb-4"></i>
    <h2 class="text-xl font-semibold text-red-600 mb-2">Something Went Wrong</h2>
    <p class="text-gray-500 mb-4">Unable to fetch tour packages. Please try again later.</p>
    <a href="/holiday/" class="px-4 py-2 bg-red-500 text-white rounded-button text-sm font-medium hover:bg-red-600 transition">
        Reload Page
    </a>
</div>';
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