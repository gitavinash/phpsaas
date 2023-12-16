<?php

require_once 'header.php';

?>

<section class="hero side-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 col-lg-7">
                <div>
                    <h2 class="isfont-40 text-white my-3 is-font600">
                        Popular Health Packages</h2>

                </div>
            </div>
            <div class="col-12 col-lg-5 my-4 my-lg-0 ">
                <img src="<?php echo USER_IMAGES ?>lite.png" alt="" class="img-fluid d-none d-lg-block   ms-lg-auto" />
            </div>
        </div>
    </div>
</section>



<section class="vartical-padding side-padding ">
    <div class="container ">

        <div class="row">
            <div class="col-12 col-lg-6 ">
                <h3 class="isfont-40 is-font600 is-color-blue">Health Packages</h3>
            </div>
            <div class="col-12 col-lg-6">
                <!-- <div class="d-flex align-items-center justify-content-center mt-5 mt-lg-0">
                    <div class="deatil-search w-100 d-flex">
                        <input type="text" class="text-manage w-100" placeholder="Search your tests">
                        <div>
                            <i class='bx bx-search icon-manage'></i>
                        </div>
                    </div>

                </div> -->
            </div>
        </div>

        <div class="row my-4 py-3 overflow-hidden ">

            <div class="row my-4 overflow-hidden">
                <?php

                if ($packageList = mysqli_query($connection, "SELECT category_title as productBrand, category_image as productBrandLogo, product_id, product_title, product_highlight, product_description, product_image, product_thumbnail, product_type, inventory_currency_symbol, inventory_mrp, inventory_price, link_url FROM isw_product INNER JOIN isw_inventory ON product_id=inventory_relation_id INNER JOIN " . TABLE_PREFIX . "category ON category_id = product_brand_id INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id WHERE product_site_id =  " . SITE_ID . " AND inventory_site_id = " . SITE_ID . " AND product_type = 'packages' AND link_type='product' LIMIT 0, 6")) {

                    if (mysqli_num_rows($packageList) != 0) {
                        $i = 1;
                        while ($packageListInfo = mysqli_fetch_assoc($packageList)) {

                            extract($packageListInfo); ?>

                <div class="col-12 col-sm-12 col-md-6 col-lg-4 ">
                    <div class="card package-cards my-3">

                        <!-- Package card -->
                        <div class="card-body">
                            <div class="row cardHeading">
                                <div class="col-5">
                                    <div class="packageBrand">
                                        <img src="<?php echo $productBrandLogo ?>" alt="">
                                    </div>
                                </div>
                                <div class="col-7">
                                    <h5 class="card-title isfont-24 is-color-blue my-3 is-font600">
                                        <?php echo ucwords($product_title) ?>
                                    </h5>
                                </div>
                            </div>
                            <h5 class="is-font600"><?php echo $product_highlight ?></h5>
                            <!-- <p class="isfont-12 knowMore" data-toggle="modal"
                                data-target="#exampleModal<?php echo $i ?>">
                                know
                                more</p> -->
                            <div class="card-bottom">
                                <div class="my-4 d-flex align-items-center cardHeading justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="packge-price me-3">
                                            <h4 class="isfont-16 is-font600 m-0">
                                                <?php echo $inventory_currency_symbol . ' ' . $inventory_price ?></h4>
                                            <strike
                                                class="isfont-12"><?php echo $inventory_currency_symbol . ' ' . $inventory_mrp ?></strike>
                                        </div>
                                        <div class="packge-price ms-3">

                                            <?php
                                                        if ($inventory_mrp != 0) {

                                                            if ($inventory_mrp != $inventory_price) {

                                                                $discountPersantage = round((1 - $inventory_price / $inventory_mrp) * 100, 0);
                                                            }
                                                        }

                                                        ?>
                                        </div>
                                    </div>

                                    <div class="ms-3">
                                        <a href="/checkout/?test_id=<?php echo base64_encode($product_id) . '&checkout_type=' . base64_encode('&type=package') ?>"
                                            class="btn1 package-btn">Book Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                            $i++;
                        }
                    }
                }

                ?>
            </div>
        </div>
    </div>
</section>



<section class=" side-padding ">
    <div class="container ">

        <div class="row">
            <div class="col-12 col-lg-6">
                <h3 class="isfont-40 is-font600 is-color-blue">Popular Tests</h3>
            </div>

        </div>

        <div class="row my-4 py-3 overflow-hidden ">
            <?php

            if ($packageList = mysqli_query($connection, "SELECT product_id, product_title, product_highlight, product_description, product_image, product_thumbnail, product_type, inventory_currency_symbol, inventory_mrp, inventory_price FROM isw_product INNER JOIN isw_product_meta ON product_id=product_meta_relation_id INNER JOIN isw_inventory ON product_id=inventory_relation_id WHERE product_site_id =  " . SITE_ID . " AND inventory_site_id = " . SITE_ID . " AND product_meta_site_id =  " . SITE_ID . " AND product_type = 'diagnostic' LIMIT 9, 18")) {

                if (mysqli_num_rows($packageList) != 0) {
                    while ($packageListInfo = mysqli_fetch_assoc($packageList)) {

                        extract($packageListInfo); ?>

            <div class="col-12 col-sm-12 col-md-6 col-lg-4 ">
                <div class="card tests-cards my-3">

                    <!-- Package card -->
                    <div class="card-body">
                        <h5 class="card-title isfont-28 is-color-blue my-3 is-font600">
                            <?php echo ucwords($product_title) ?>
                        </h5>
                        <h5 class="is-font600"><?php echo $product_highlight ?></h5>
                        <div class="card-bottom">
                            <div class="my-4 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="packge-price me-3">
                                        <h4 class="isfont-16 is-font600 m-0">
                                            <?php echo $inventory_currency_symbol . ' ' . $inventory_price ?></h4>
                                        <strike
                                            class="isfont-12"><?php echo $inventory_currency_symbol . ' ' . $inventory_mrp ?></strike>
                                    </div>
                                    <div class="packge-price ms-3">

                                        <?php
                                                    if ($inventory_mrp != 0) {

                                                        if ($inventory_mrp != $inventory_price) {

                                                            $discountPersantage = round((1 - $inventory_price / $inventory_mrp) * 100, 0);

                                                            echo "<h4 class='isfont-16 is-font600 m-0' >{$discountPersantage} Off</h4>
                                    <h6 class='isfont-12'>For a limited period</h6>";
                                                        }
                                                    }

                                                    ?>
                                    </div>
                                </div>

                                <div class="ms-3">
                                    <a href="/book-now/?test=<?php echo strtolower(urlencode($product_title)) ?>"
                                        class="btn1 tests-btn">Book Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                    }
                }
            }

            ?>

        </div>


    </div>
</section>

<?php

require_once 'footer.php';

?>