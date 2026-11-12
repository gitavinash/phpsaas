<?php
$preservedQuery = $_GET;
unset($preservedQuery['page']);

$buildPageUrl = function (int $pageNum) use ($preservedQuery): string {
    $params = array_merge($preservedQuery, ['page' => $pageNum]);
    return safe_html(BASE_URL . '?' . http_build_query($params));
};
?>
<!-- Pagination -->
<nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 border-t border-[#E6DDD3] flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4"
    aria-label="Pagination">
    <p class="text-xs uppercase tracking-widest text-[#5E5650] font-medium">
        Page <strong class="text-[#1C1A18] font-semibold"><?php echo PAGE_NO; ?></strong> of <?php echo TOTAL_PAGE; ?>
    </p>

    <div class="flex flex-wrap items-center justify-center gap-1.5 sm:gap-2">
        <?php if (PAGE_NO > 1): ?>
            <a href="<?php echo $buildPageUrl(PAGE_NO - 1); ?>"
                class="w-11 h-11 inline-flex items-center justify-center rounded-md border border-[#E6DDD3] bg-white text-[#1C1A18] hover:border-[#183028] hover:bg-[#183028] hover:text-white transition-colors"
                aria-label="Previous page">
                <i class="ri-arrow-left-s-line text-lg" aria-hidden="true"></i>
            </a>
        <?php endif; ?>

        <?php
        $renderNumLink = function (int $i) use ($buildPageUrl): string {
            if ($i === (int) PAGE_NO) {
                return '<a href="' . $buildPageUrl($i) . '" aria-current="page" class="w-11 h-11 inline-flex items-center justify-center rounded-md bg-[#183028] text-white font-semibold text-sm shadow-sm">' . $i . '</a>';
            }
            return '<a href="' . $buildPageUrl($i) . '" class="w-11 h-11 inline-flex items-center justify-center rounded-md border border-[#E6DDD3] bg-white text-[#5E5650] hover:border-[#183028] hover:text-[#183028] font-medium text-sm transition-colors">' . $i . '</a>';
        };

        if (TOTAL_PAGE <= 3) {
            for ($i = 1; $i <= TOTAL_PAGE; $i++) {
                echo $renderNumLink($i);
            }
        } else {
            echo $renderNumLink(1);

            if (PAGE_NO > 2) {
                if (PAGE_NO == 3) {
                    echo $renderNumLink(2);
                } else {
                    echo '<span class="px-2 text-[#5E5650]" aria-hidden="true">&hellip;</span>';
                }
            }

            if (PAGE_NO > 1 && PAGE_NO < TOTAL_PAGE) {
                echo $renderNumLink((int) PAGE_NO);
            }

            if (PAGE_NO < TOTAL_PAGE - 1) {
                if (PAGE_NO == TOTAL_PAGE - 2) {
                    echo $renderNumLink((int) TOTAL_PAGE - 1);
                } else {
                    echo '<span class="px-2 text-[#5E5650]" aria-hidden="true">&hellip;</span>';
                }
            }

            echo $renderNumLink((int) TOTAL_PAGE);
        }
        ?>

        <?php if (PAGE_NO < TOTAL_PAGE): ?>
            <a href="<?php echo $buildPageUrl(PAGE_NO + 1); ?>"
                class="w-11 h-11 inline-flex items-center justify-center rounded-md border border-[#E6DDD3] bg-white text-[#1C1A18] hover:border-[#183028] hover:bg-[#183028] hover:text-white transition-colors"
                aria-label="Next page">
                <i class="ri-arrow-right-s-line text-lg" aria-hidden="true"></i>
            </a>
        <?php endif; ?>
    </div>
</nav>
