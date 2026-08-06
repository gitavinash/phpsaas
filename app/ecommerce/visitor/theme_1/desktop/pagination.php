<!-- Pagination -->
<div class="container mx-auto px-4 py-8 flex justify-center">
    <div class="flex items-center space-x-2">
        <?php
        echo '<span class="text-gray-600">Page ' . PAGE_NO . ' of ' . TOTAL_PAGE . '</span>';

        // Previous Button
        if (PAGE_NO > 1) {
            $prevPage = PAGE_NO - 1;
            echo '<a href="' . BASE_URL . '?page=' . $prevPage . '" class="w-10 h-10 flex items-center justify-center rounded border border-gray-200 text-gray-500 hover:border-primary hover:text-primary transition-colors">
                    <i class="ri-arrow-left-s-line"></i>
                  </a>';
        }

        // Page Numbers
        for ($i = 1; $i <= TOTAL_PAGE; $i++) {
            $activeClass = ($i == PAGE_NO)
                ? 'bg-primary text-white'
                : 'border border-gray-200 hover:border-primary hover:text-primary text-gray-500';
            echo '<a href="' . BASE_URL . '?page=' . $i . '" class="w-10 h-10 flex items-center justify-center rounded ' . $activeClass . '">' . $i . '</a>';
        }

        // Next Button
        if (PAGE_NO < TOTAL_PAGE) {
            $nextPage = PAGE_NO + 1;
            echo '<a href="' . BASE_URL . '?page=' . $nextPage . '" class="w-10 h-10 flex items-center justify-center rounded border border-gray-200 text-gray-500 hover:border-primary hover:text-primary transition-colors">
                    <i class="ri-arrow-right-s-line"></i>
                  </a>';
        }
        ?>
    </div>
</div>