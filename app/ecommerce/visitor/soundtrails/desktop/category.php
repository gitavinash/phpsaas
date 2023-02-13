<?php require_once('header.php') ?>

<div class="container-fluid">
    <div class="row product_banner_section sidepadding hero align-items-center py-5">
        <div class="col-12 text-center my-5">
            <h5>Home / <span><?php echo $category_title ?></span></h5>
        </div>
    </div>
</div>
</section>



<main>
    <section class="vertical-padding sidepadding">
        <h3 class="font-size-57 text-white fontibm">
            <span class="goldan-clr"> <?php echo $category_title ?> </span> Products
        </h3>

        <div class="contianer-fluid">
            <div class="row vertical-padding">


                <?php

                if (isset($categorySinglePost)) {
                    for ($i = 0; $i < sizeof($categorySinglePost); $i++) {

                        extract($categorySinglePost[$i]);

                        echo "<div class='col-12 col-sm-6 col-md-6 col-xl-6 col-xxl-3 products-list'>
                        <a href='{$link_url}'>
                            <img src='{$product_image}' alt='{$product_title}' class='img-fluid category-product-box mx-auto d-block ' />
                            <div class='product-details'>
                                <p class='text-white font-size-20 font-family-sans text-center'>{$product_title}</p>
                            </div>
                            <div class='rating-price d-flex justify-content-around'>
                                <div class='price text-white font-size-20 font-family-sans '>" . $inventory_currency_symbol . ' ' . (($inventory_price != 0) ? preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $inventory_price) : preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $inventory_mrp)) . "/-</div>
                            </div>
                        </a>
                        </div>";
                    }
                } else {
                    echo "<h3 class='font-size-57 text-white fontibm'>No product found.</h3>";
                }

                ?>

            </div>
        </div>
    </section>

    <section class="sidepadding mx-5 details-bg">
        <div class="container-fluid">
            <div class="row py-3 d-flex align-items-center">
                <div class="row vertical-padding my-5">
                    <div
                        class="col-12 col-lg-4 col-md-4 col-sm-12 side-boder d-flex justify-content-center text-center">
                        <div>
                            <img src="<?php echo USER_IMAGES ?>d1 (3).png" alt="" style="padding: 8px 0px"
                                class=" img-fluid" />
                            <h4 class="font-size-57 fw-bold mt-3 text-white text-center my-3">
                                <span class="font-size-40">Free Shipping</span>
                            </h4>
                            <h4 class="text-white text-center">Order Over ₹ 1,000.00</h4>
                        </div>
                    </div>
                    <div
                        class="col-12 col-sm-12 col-lg-4 col-md-4 side-boder d-flex justify-content-center text-center">
                        <div>
                            <img src="<?php echo USER_IMAGES ?>d1 (2).png" alt="" style="padding: 13px 0px"
                                class="img-fluid" />
                            <h4 class="font-size-57 fw-bold mt-3 text-white text-center my-3">
                                <span class="font-size-40">Quick Payment</span>
                            </h4>
                            <h4 class="text-white text-center">100% Secure</h4>
                        </div>
                    </div>

                    <div
                        class="col-12 col-lg-4 col-md-4 col-sm-12 side-boder d-flex justify-content-center text-center">
                        <div>
                            <img src="<?php echo USER_IMAGES ?>d1 (1).png" alt="" class="img-fluid" />
                            <h4 class="font-size-57 fw-bold mt-3 text-white text-center my-3">
                                <span class="font-size-40"> 24/7 Support</span>
                            </h4>
                            <h4 class="text-white text-center">Ready For You</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<section class="vertical-padding sidepadding d-flex align-content-center">
    <div class="container-fluid">
        <div class="row d-flex justify-content-center text-center">
            <div class="col-12 col-lg-6">
                <h5 class="text-white font-size-25">
                    Stay Connected With Our Email Updates
                </h5>
            </div>
            <div class="col-12 col-lg-6">
                <div class="newsleeter d-flex m-0 justify-content-center mt-3 mt-md-0">
                    <input type="search" class="newsleeter-bar m-0" placeholder="Enter Your Email" />
                    <button class="btn1 m-0">Submit</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- treding -end -->
<?php require_once('footer.php') ?>