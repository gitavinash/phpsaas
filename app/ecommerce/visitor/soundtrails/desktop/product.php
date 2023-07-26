<?php require_once('header.php') ?>
<!-- navbar -->
<div class="container-fluid">
    <div class="row product_banner_section sidepadding hero align-items-center py-5">
        <div class="col-12 text-center my-5">
            <h5>Home / <a href="/shop/">Products</a> / <span><?php echo $product_title ?></span></h5>
        </div>
    </div>
</div>
<!-- hero -->

<!-- end -->
</section>

<main>
    <!-- single product -->
    <section class="singel_product py-5 container-fluid sidepadding">
        <div class="row gx-0">
            <div class="col-12 col-sm-12 col-md-12 col-xl-7 col-xxl-6">
                <div class="col-12">
                    <div class="row justify-content-center">
                        <div
                            class="col-12 col-sm-12 col-md-12 col-xl-4 col-xxl-2 order-2 order-xxl-1 mt-2 mt-xxl-0 d-none d-lg-block flex_div">
                            <?php

                            if ($product_gallery != '') {

                                $galleryImages = json_decode($product_gallery, TRUE);

                                if ($galleryImages != '') { ?>
                            <div class="sm-image-group mt-3 mt-xl-0">
                                <img src="<?php echo $product_image ?>" class="img-fluid"
                                    alt="<?php echo $product_title ?>" />
                            </div>
                            <?php

                                    for ($i = 0; $i < sizeof($galleryImages); $i++) {

                                        extract($galleryImages[$i]); ?>
                            <div class="sm-image-group mt-3">
                                <img src="<?php echo $path ?>" class="img-fluid" alt="<?php echo $name ?>" />
                            </div>
                            <?php
                                    }

                                    echo '</ul>';
                                }
                            }

                            ?>
                        </div>

                        <!-- mobile slider -->
                        <div
                            class="col-12 col-sm-12 col-md-12 col-xl-4 col-xxl-2 order-2 order-xxl-1 mt-2 mt-xxl-0 flex_div d-block d-xl-none">
                            <div class="slider"><?php

                                                if ($product_gallery != '') {

                                                    $galleryImages = json_decode($product_gallery, TRUE);

                                                    if ($galleryImages != '') {

                                                ?>
                                <div class="sm-image-group mt-3 mt-xl-0 mx-3">
                                    <img src="<?php echo $path ?>" class="img-fluid d-block m-auto"
                                        alt="<?php echo $name ?>" />
                                </div>
                                <?php

                                                        for ($i = 0; $i < sizeof($galleryImages); $i++) {

                                                            extract($galleryImages[$i]); ?>
                                <div class="sm-image-group mt-3 mt-xl-0 mx-3">
                                    <img src="<?php echo $path ?>" class="img-fluid d-block m-auto"
                                        alt="<?php echo $name ?>" />
                                </div>
                                <?php
                                                        }

                                                        echo '</ul>';
                                                    }
                                                }

                                ?>

                            </div>
                        </div>

                        <!-- mobile slider -->
                        <div class="col-12 col-sm-12 col-md-12 col-xl-7 col-xxl-9 order-1 order-xxl-2">
                            <div class="product_image">
                                <img src="<?php echo $product_image ?>" class="img-fluid"
                                    alt="<?php echo $product_title ?>" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-xl-5 col-xxl-6 mt-4 mt-xl-0">
                <h1><?php echo $product_title ?></h1>

                <div class="product_details">
                    <div>
                        <p><strong>Brand:</strong> <a href="<?php echo $brandURL ?>"><?php echo $brandName ?></a></p>
                    </div>
                    <div>
                        <p><strong>Product Code:</strong> <?php echo $inventory_sku ?></p>
                    </div>
                    <div>
                        <p><strong>Availability:</strong>
                            <?php echo (($inventory_in_stock == 1) ? 'In stock' : 'Out off stock') ?></p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="product_details">
                        <div class="product_hightlights">
                            <p><strong>Highlights: </strong></p>
                            <p><?php echo $product_highlight ?></p>
                        </div>
                    </div>
                </div>
                <div class="price_div py-3 mt-4 prive_top_bodder">
                    <div class="d-flex align-items-center">
                        <h4>₹
                            <?php echo preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $inventory_price); ?>/-
                        </h4>
                        <?php

                        if ($inventory_price != $inventory_mrp) { ?>
                        <p class="ms-5 line">
                            ₹
                            <?php echo preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $inventory_mrp); ?>/-
                        </p>
                        <?php
                        }

                        ?>
                    </div>
                </div>

                <div class="mt-4 prive_top_bodder py-5 filter fire">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-12 col-xl-12 col-xxl-6">
                                <h5>QUANTITY</h5>
                                <div class="quntity-div mt-3">
                                    <input type="number" placeholder="1" value="1" />
                                    <div class="controlles"></div>
                                </div>
                            </div>
                            <!-- <div
                                class="col-12 col-sm-12 col-md-12 col-xl-12 col-xxl-6 d-flex align-content-center mt-4 mt-xxl-0">
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo USER_IMAGES ?>fire.png" alt="" />
                                    <p class="mb-0 ms-2">8 sold in last 18 hours</p>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center cart_div">
                    <p style="display:none" id="productInfo"
                        value="<?php echo $product_id . '_' . (($inventory_price == 0) ? $inventory_mrp : $inventory_price) . '_1' ?>">
                    </p>
                    <button class="add_to_cart add_cart" id="addToCart">ADD TO CART</button>
                    <div class="like ms-3">
                        <img src="<?php echo USER_IMAGES ?>heart.png" alt="" />
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- single product -->

    <section class="product_discription sidepadding">
        <div class="row">
            <div class="tabs">
                <input type="radio" class="tabs__radio" name="tabs-example" id="tab1" checked>
                <label for="tab1" class=" tabs__label">
                    DESCRIPTION
                </label>
                <div class="tabs__content">
                    <?php echo $product_description ?>
                </div>
                <input type="radio" class="tabs__radio" name="tabs-example" id="tab2">
                <label for="tab2" class="tabs__label">User Reviews</label>
                <div class="tabs__content">

                </div>
            </div>
        </div>
    </section>

</main>

<?php require_once('footer.php') ?>