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

        // Page Numbers (more compact: show only first, current, and last; with ellipses)
        if (TOTAL_PAGE <= 3) {
            // small number of pages -> show all
            for ($i = 1; $i <= TOTAL_PAGE; $i++) {
                $activeClass = ($i == PAGE_NO)
                    ? 'bg-primary text-white'
                    : 'border border-gray-200 hover:border-primary hover:text-primary text-gray-500';
                echo '<a href="' . BASE_URL . '?page=' . $i . '" class="w-10 h-10 flex items-center justify-center rounded ' . $activeClass . '">' . $i . '</a>';
            }
        } else {
            // first page
            $activeClass = (1 == PAGE_NO)
                ? 'bg-primary text-white'
                : 'border border-gray-200 hover:border-primary hover:text-primary text-gray-500';
            echo '<a href="' . BASE_URL . '?page=1" class="w-10 h-10 flex items-center justify-center rounded ' . $activeClass . '">1</a>';

            // gap between first and current
            if (PAGE_NO > 2) {
                if (PAGE_NO == 3) {
                    // show page 2 when current is 3 to keep compactness reasonable
                    $i = 2;
                    $activeClass = ($i == PAGE_NO)
                        ? 'bg-primary text-white'
                        : 'border border-gray-200 hover:border-primary hover:text-primary text-gray-500';
                    echo '<a href="' . BASE_URL . '?page=' . $i . '" class="w-10 h-10 flex items-center justify-center rounded ' . $activeClass . '">' . $i . '</a>';
                } else {
                    echo '<span class="px-2 text-gray-500">&hellip;</span>';
                }
            }

            // current page (if not first or last)
            if (PAGE_NO > 1 && PAGE_NO < TOTAL_PAGE) {
                $i = PAGE_NO;
                $activeClass = ($i == PAGE_NO)
                    ? 'bg-primary text-white'
                    : 'border border-gray-200 hover:border-primary hover:text-primary text-gray-500';
                echo '<a href="' . BASE_URL . '?page=' . $i . '" class="w-10 h-10 flex items-center justify-center rounded ' . $activeClass . '">' . $i . '</a>';
            }

            // gap between current and last
            if (PAGE_NO < TOTAL_PAGE - 1) {
                if (PAGE_NO == TOTAL_PAGE - 2) {
                    // show second to last when current is close to end
                    $i = TOTAL_PAGE - 1;
                    $activeClass = ($i == PAGE_NO)
                        ? 'bg-primary text-white'
                        : 'border border-gray-200 hover:border-primary hover:text-primary text-gray-500';
                    echo '<a href="' . BASE_URL . '?page=' . $i . '" class="w-10 h-10 flex items-center justify-center rounded ' . $activeClass . '">' . $i . '</a>';
                } else {
                    echo '<span class="px-2 text-gray-500">&hellip;</span>';
                }
            }

            // last page
            $i = TOTAL_PAGE;
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