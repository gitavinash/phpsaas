<aside class="lg:w-1/4">
    <div class="space-y-8 lg:ml-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <div class="relative">
                <form action="/search/" method="get">
                    <input type="text" name="q" placeholder="Search articles..."
                        class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-button focus:outline-none focus:border-primary">
                    <i class="ri-search-line absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </form>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <h3 class="text-lg font-semibold mb-4">Categories</h3>
            <div class="space-y-3">
                <?php

                if ($supplierList = mysqli_query($connection, "SELECT category_title, link_url FROM " . TABLE_PREFIX . "category INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=category_id WHERE link_status=200 AND link_type='category' AND category_type='post' AND category_site_id=" . SITE_ID . " AND link_site_id=" . SITE_ID)) {
                    echo "<tbody>";
                    if (mysqli_num_rows($supplierList) > 0) {
                        while ($supplierData = mysqli_fetch_assoc($supplierList)) {
                            extract($supplierData);
                            echo "<a href='{$link_url}' class='flex items-center gap-3'><span class='text-gray-700'>{$category_title}</span></a>";
                        }
                    }
                    echo "</tbody>";
                }

                ?>
            </div>
        </div>
    </div>
</aside>