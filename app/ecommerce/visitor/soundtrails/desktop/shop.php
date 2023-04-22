<?php require_once('header.php') ?>
<!-- navbar -->
<div class="container-fluid">
    <div class="row product_banner_section sidepadding hero align-items-center py-5">
        <div class="col-12 text-center my-5">
            <h5>Home / <span>Products</span></h5>
        </div>
    </div>
</div>
<!-- hero -->

<!-- end -->
</section>



<main>
    <section class="vertical-padding sidepadding">
        <h3 class="font-size-57 text-white fontibm">
            <span class="goldan-clr"> Product </span> Categories
        </h3>

        <div class="contianer-fluid">
            <div class="row vertical-padding">

                <?php

                if ($categoryList = mysqli_query($connection, "SELECT il.link_url, ic.category_image, ic.category_title  FROM " . TABLE_PREFIX . "link il INNER JOIN " . TABLE_PREFIX . "category ic ON ic.category_id = il.link_relation_id WHERE il.link_type = 'category' AND il.link_status = 200 AND link_site_id = " . SITE_ID . " AND ic.category_parent_id = 0 AND ic.category_type='product'")) {

                    if (mysqli_num_rows($categoryList) != 0) {

                        while ($categoryListData = mysqli_fetch_assoc($categoryList)) {

                            extract($categoryListData); ?>

                <div class="col-12 col-md-2 col-lg-3">
                    <a href="<?php echo $link_url ?>">
                        <img src="<?php echo (($category_image != '') ? $category_image : USER_IMAGES . 'image_34.png') ?>"
                            alt="" class="d-block m-auto categoryImage" />
                        <div class="cont py-3">
                            <h3 class="text-white font-size-31 px-3">
                                <?php echo $category_title ?>
                            </h3>
                        </div>
                    </a>
                </div>

                <?php
                        }
                    }
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