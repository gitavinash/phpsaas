<?php

include_once 'header.php';


if (is_array($tagPost)) {

    // List all tag items  

?>

<!-- section global platform start -->
<section class="banner-section baner-bg">
    <div class="overlay-bner"></div>
    <div class="container">
        <div class="row ">
            <div class="col-md-12">

                <div class="bg-content">
                    <h1><?php echo ucwords($tag_title) ?></h1>
                    <h3><?php echo $tag_description ?></h3>
                </div>
            </div>

            <?php

                for ($i = 0; $i < sizeof($tagPost); $i++) {

                    $tagId[] = $tagPost[$i]['tag_relation_id'];
                }

                $resultTags = implode(' OR link_relation_id=', $tagId);

                ?>

        </div>
    </div>
    </div>
</section>
<!-- section global platform end -->

<section class="section-content">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="brdcrum">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo ACTIVE_URL ?>">Category</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo ucwords($tag_title) ?>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row">

            <?php

                if ($tagResults = mysqli_query($connection, "SELECT link_url, product_title, product_image, inventory_currency, inventory_currency_symbol, inventory_price, inventory_mrp FROM " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "product ON link_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id WHERE link_relation_id IN ({$resultTags}) AND link_site_id=" . SITE_ID . " AND link_type='product' AND link_status=200 ORDER BY link_is_featured, link_modified DESC LIMIT 0, 12")) {

                    if (mysqli_num_rows($tagResults) != 0) {

                        while ($tagResultsData = mysqli_fetch_assoc($tagResults)) {

                            extract($tagResultsData);

                            echo "<div class='col-md-3 col-6'>
											<a href='{$link_url}'>
												<div class='col-card-3'>
													<div class='event-card'>
														<div class='img-cd'>
															<img src='{$product_image}'>
														</div>
														<h4>{$product_title}</h4>
														<p class='rate'>" . $inventory_currency_symbol . ' ' . (($inventory_price != 0) ? $inventory_price : $inventory_mrp) . "</p>
													</div>
												</div>
											</a>
										</div>";
                        }
                    } else {
                    }
                }

                ?>

        </div>
        <div class="row">
            <div class="center">
                <div class="pagination">
                    <?php

                        if (PAGE_NO != 0) {
                            echo "<a href='" . BASE_URL . "?pg=" . PREVIOUS_PAGE . "'>Previous page</a>";
                        }

                        if ((PAGE_NO + 1) != TOTAL_PAGE) {
                            echo "<a href='" . BASE_URL . "?pg=" . NEXT_PAGE . "'>Next page " . TOTAL_PAGE . " - " . NEXT_PAGE . " &raquo;</a>";
                        }

                        echo 'Pagination = ' . PAGE_NO . ' = ' . TOTAL_PAGE . ' | ' . NEXT_PAGE . ' = ' . TOTAL_PAGE;


                        ?>
                </div>
            </div>
        </div>
    </div>
</section>


<?php

} else {

    // show no result found

?>

<section class='continue-shopping'>
    <h1><span>No product found</span></h1>
    <a href="/" class="btn btn-success">Go to homepage</a>
</section>

<?php

}


include_once 'footer.php';

?>