<div class="pagination">
    <?php
    // Set values (these should ideally come from your logic)
    $page_no = isset($_GET['pg']) ? (int)$_GET['pg'] : 1;
    $total_page = 10; // Example: total number of pages
    $previous_page = max(1, $page_no - 1);
    $next_page = min($total_page, $page_no + 1);
    $base_url = '/your-page-path.php'; // Adjust if needed

    $sort = isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : '';

    if ($page_no > 1) {
        echo "<a class='previousBtn' href='" . $base_url . "?pg=" . $previous_page . "{$sort}'>&laquo; Previous page</a>";
    }

    if ($page_no < $total_page) {
        echo "<a class='nextBtn' href='" . $base_url . "?pg=" . $next_page . "{$sort}'>Next page &raquo;</a>";
    }
    ?>
</div>