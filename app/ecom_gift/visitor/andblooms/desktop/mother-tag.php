<?php

include_once 'header.php';


if (is_array($tagPost)) {

    // List all tag items  

?>

<!-- section global platform start -->
<section class="featured-banner">
    <img src="<?php echo USER_IMAGES ?>mother-day-banner.jpg" alt="">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="landing-page-heading">
                    <h2>
                        Mother's Day Special
                    </h2>
                    <p>Special gift for your mother</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- section global platform end -->

<!-- section global platform start -->
<section class="banner-section">
    <div class="container">
        <div class="row ">

            <?php

                for ($i = 0; $i < sizeof($tagPost); $i++) {

                    $tagId[] = $tagPost[$i]['tag_relation_id'];
                }

                $resultTags = implode(', ', $tagId);

                ?>


            <?php

                if ($tagResults = mysqli_query($connection, "SELECT link_url, product_title, product_image, inventory_currency, inventory_currency_symbol, inventory_price, inventory_mrp FROM " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "product ON link_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id WHERE link_relation_id IN ({$resultTags}) AND link_site_id=" . SITE_ID . " AND link_type='product' AND link_status=200 ORDER BY link_is_featured, link_modified DESC")) {

                    if (mysqli_num_rows($tagResults) != 0) {

                        while ($tagResultsData = mysqli_fetch_assoc($tagResults)) {

                            extract($tagResultsData);

                            echo "<div class='col-md-4 col-6'>
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