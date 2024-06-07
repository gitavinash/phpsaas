<?php

include_once 'header.php';

?>
<!-- section global platform start -->
<section class="banner-section">
    <div class="container-fluid">
        <div class="row">
            <div class="card-ban">
                <?php

                if ($desktopSlider = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "slide WHERE slide_status=1 AND slide_site_id=" . SITE_ID)) {
                    $numberOfSlider = mysqli_num_rows($desktopSlider);
                    if ($numberOfSlider != 0) {
                        while ($desktopSliderData = mysqli_fetch_assoc($desktopSlider)) {
                            extract($desktopSliderData);
                            echo "<div class='slider'><a href='{$slide_link}'><img src='{$slide_image}'></a></div>";
                        }
                    }
                }

                ?>
            </div>
        </div>
    </div>
</section>
<!-- section global platform end -->

<?php


if (sizeof($productCategoryData) != 0) {
    echo "<section class='section-content cloth-card'>
    <div class='container'>
        <div class='row'>";

    for ($i = 0; $i < sizeof($productCategoryData); $i++) {
        extract($productCategoryData[$i]);
        echo "
                    <div class='col col-2-mobile'>
                        <a href='{$categoryLink}' class='card-link'>
                    <div class='acce-card'>
                        <div class='img-acc'>
                            <img src='{$categoryImage}' title='{$categoryTitle}'>
</div>
<div class='acc-card-head'>
    <h4>{$categoryTitle}</h4>
</div>
</div>
</a>
</div>
";
    }
    echo " </div>
</div>
</section>";
}

?>

<section class="section-content bg-light">

    <div class="container">

        <!-- New collection -->
        <div class="row">

            <div class="col-md-12">
                <div class="head-1">
                    <h2>Latest Collection</h2>
                    <span class="cart-bodr-h2"></span>
                </div>
            </div>

            <?php

            if ($productList = mysqli_query($connection, "SELECT link_id, link_url, product_title, product_image, inventory_currency, inventory_currency_symbol, inventory_price FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id WHERE inventory_type='product' AND link_type='product' AND link_status=200 AND inventory_in_stock=1 AND link_site_id=" . SITE_ID . " ORDER BY link_published DESC LIMIT 0, 20")) {

                if (mysqli_num_rows($productList) != 0) {

                    while ($productData = mysqli_fetch_assoc($productList)) {

                        extract($productData);

                        echo "<div class='col-md-3 col-sm-2'>
												<a href='{$link_url}'>
													<div class='col-card-4'>
														<div class='event-card'>
															<div class='img-cd'><img src='{$product_image}' /></div>
															<h4>{$product_title}</h4>
															<p class='rate'>" . $inventory_currency_symbol . ' ' . (($inventory_price != 0) ? $inventory_price : $inventory_mrp) . "</p>
														</div>
													</div>
												</a>
											</div>";
                    }
                }
            }

            ?>

        </div>

    </div>

</section>

<?php

include_once 'footer.php';

?>