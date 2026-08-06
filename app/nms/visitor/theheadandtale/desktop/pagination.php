<div class="pagination">
    <?php
    // ✅ Safe defaults
    if (!defined('TOTAL_PAGE')) define('TOTAL_PAGE', 10);
    if (!defined('PAGE_NO')) define('PAGE_NO', 1);
    if (!defined('NEXT_PAGE')) define('NEXT_PAGE', PAGE_NO + 1);
    if (!defined('PREVIOUS_PAGE')) define('PREVIOUS_PAGE', max(1, PAGE_NO - 1));

    // ✅ Preserve filters
    $qParam    = (isset($_GET['q']) && $_GET['q'] !== '') ? '&q=' . urlencode($_GET['q']) : '';
    $sortParam = (isset($_GET['sort']) && $_GET['sort'] !== '') ? '&sort=' . urlencode($_GET['sort']) : '';

    // ✅ Show "Previous" only if not on page 1
    if (PAGE_NO > 1) {
        echo "<a class='previousBtn' href='" . BASE_URL . "?page=" . PREVIOUS_PAGE . "{$qParam}{$sortParam}'>&laquo; Previous page</a>";
    }

    // ✅ Show "Next" only if within total pages
    if (NEXT_PAGE <= TOTAL_PAGE) {
        echo "<a class='nextBtn' href='" . BASE_URL . "?page=" . NEXT_PAGE . "{$qParam}{$sortParam}'>Next page &raquo;</a>";
    }
    ?>
</div>