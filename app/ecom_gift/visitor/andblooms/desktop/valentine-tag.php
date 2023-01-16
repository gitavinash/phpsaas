<?php

include_once 'header.php';


if (is_array($tagPost)) {

    // List all tag items  

?>

<!-- section global platform start -->
<section class="featured-banner">
    <img src="<?php echo USER_IMAGES ?>valentine-day-banner.jpg" alt="">
</section>
<!-- section global platform end -->

<!-- section global platform start -->
<section class="banner-section baner-bg">
    <div class="overlay-bner"></div>
    <div class="container">
        <div class="row ">

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

<section class="section-content">
    <div class="container">
        <div class="row">

            <?php

                if ($tagResults = mysqli_query($connection, "SELECT link_url, product_title, product_image, inventory_currency, inventory_currency_symbol, inventory_price, inventory_mrp FROM " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "product ON link_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id WHERE link_site_id=" . SITE_ID . " AND link_type='product' AND link_is_featured=1 AND inventory_in_stock=1 AND link_relation_id={$resultTags} ORDER BY link_is_featured DESC LIMIT 0, 9")) {

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