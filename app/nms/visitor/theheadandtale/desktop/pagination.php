<div class="pagination">
    <?php

    if (!defined('TOTAL_PAGE')) {
        define('TOTAL_PAGE', 10); // default value
    }

    $sort = ((isset($_GET['sort'])) ? '&sort=' . $_GET['sort'] : '');

    if (PAGE_NO != 0) {
        echo "<a class='previousBtn' href='" . BASE_URL . "?pg=" . PREVIOUS_PAGE . "{$sort}'>&laquo; Previous page</a>";
    }

    if (NEXT_PAGE <= TOTAL_PAGE) {
        echo "<a class='nextBtn' href='" . BASE_URL . "?pg=" . NEXT_PAGE . "{$sort}'>Next page &raquo;</a>";
    }

    ?>
</div>