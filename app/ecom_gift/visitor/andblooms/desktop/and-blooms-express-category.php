<?php

include_once 'header.php';


if (is_array($categorySinglePost)) {

    // List all tag items  

?>

<!-- section global platform start -->
<section class="featured-banner">
    <img src="<?php echo USER_IMAGES ?>and-blooms-express-banner.jpg" alt="">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="landing-page-heading">
                    <h2>
                        <?php echo $category_title ?>
                    </h2>
                    <!-- <p>Send gift in 30 mins</p> -->
                </div>
            </div>
        </div>
        <div class="row">

            <?php

                for ($i = 0; $i < sizeof($categorySinglePost); $i++) {

                    extract($categorySinglePost[$i]);

                    echo "<div class='col-md-4 col-6'>
                        <a href='{$link_url}'>
                            <div class='col-card-3'>
                                <div class='event-card'>
                                    <div class='img-cd'>
                                        <img src='{$product_image}'>
                                    </div>
                                    " . (($inventory_in_stock == 0) ? "<div class='out-of-stock' ><span>Out of Stock</span></div>" : "") . "
                                    <h4>{$product_title}</h4>
                                    <p class='rate'>" . $inventory_currency_symbol . ' ' . (($inventory_price != 0) ? $inventory_price : $inventory_mrp) . "</p>
                                </div>
                            </div>
                        </a>
                    </div>";
                }

                ?>
        </div>
</section>
<!-- section global platform end -->

<!-- section global platform start -->
<section class="banner-section">
    <div class="container">
        <div class="row ">

            <?php



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