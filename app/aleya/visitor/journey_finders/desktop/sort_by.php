<?php
$sort_order = '';
if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'price-low':
            $sort_order = 'ORDER BY tour_package_price_selling_adult ASC';
            break;
        case 'price-high':
            $sort_order = 'ORDER BY tour_package_price_selling_adult DESC';
            break;
        case 'duration-low':
            $sort_order = 'ORDER BY tour_package_duration ASC';
            break;
        case 'duration-high':
            $sort_order = 'ORDER BY tour_package_duration DESC';
            break;
        default:
            $sort_order = 'ORDER BY link_published DESC';
    }
}

?>
<span class="text-gray-600 text-sm mr-2">Sort by:</span>
<div class="relative">
    <select name="sort"
        class="pl-4 pr-8 py-2 border border-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-primary/20 text-sm appearance-none bg-white">
        <option value="#">Select sort</option>
        <option value="price-low">Price: Low to High</option>
        <option value="price-high">Price: High to Low</option>
        <option value="duration-low">Duration: Low to High</option>
        <option value="duration-high">Duration: High to Low</option>
    </select>
    <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
        <i class="ri-arrow-down-s-line text-gray-500"></i>
    </div>
</div>