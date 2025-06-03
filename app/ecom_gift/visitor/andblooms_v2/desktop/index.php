<?php

require_once 'header.php';

?>
<main>
    <!-- banner section start -->
    <div class="banner_wrapper">
        <div class="container-fluid">
            <div class="banner_wrapper__heading">
                <h1 class="main_font_family is-font-55 dark-brown h1-size ">Handblooms For Your Soulmate</h1>
                <p class="is-font-18">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam vel elit
                    volutpat,
                    consequat arcu.
                </p>
                <div class="order_btn">
                    <a href="/tag/father/" class="global_btn">Order now</a>
                </div>
            </div>
        </div>
    </div>

    <!-- card gallery start -->
    <section class="gallery_wrapper">
        <div class="container">
            <div class="main--heading">
                <h1 class="main_font_family is-font-45 black-color">Featured products</h1>
                <p class="grey-color ">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            </div>

        </div>
        <div class="gallery_wrapper__content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="gallery_one">
                            <h6 class="is-font-26 dark-brown main_font_family ">Flower for lorem ipsum</h6>
                            <p class="grey-color is-font-18">Save upto 20% off</p>
                            <div class="shop_now">
                                <a href="/tag/father/" class="global_btn">Shop now</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="flower_flex">
                            <div class="gallery_two">
                                <img src="<?php echo USER_IMAGES ?>svg/img2.svg" class="img-fluid" alt="">
                            </div>
                            <div class="gallery_two">
                                <img src="<?php echo USER_IMAGES ?>svg/img2.svg" class="img-fluid" alt="">
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-3">
                        <div class="gallery_one">
                            <h6 class="is-font-26 dark-brown main_font_family ">Flower for lorem ipsum</h6>
                            <p class="grey-color is-font-18">Save upto 20% off</p>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- flower price section start -->
    <section class="price_wrapper">
        <div class="container">
            <div class="main--heading">
                <h1 class="main_font_family is-font-45 black-color">Lorem ipsum</h1>
                <p class="grey-color ">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            </div>
        </div>
        <div class="price_content">
            <div class="container">
                <div class="row">

                    <?php

                    if ($productList = mysqli_query($connection, "SELECT link_id, link_url, product_title, product_image, inventory_currency, inventory_currency_symbol, inventory_price, category_title FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "category ON category_id=product_category WHERE inventory_type='product' AND link_type='product' AND link_status=200 AND inventory_in_stock=1 AND product_category != 170 AND product_category != 195 AND product_category!=77 AND product_category!=40 AND link_site_id=" . SITE_ID . " ORDER BY link_is_featured DESC, link_published DESC LIMIT 0, 8")) {

                        if (mysqli_num_rows($productList) != 0) {

                            while ($productData = mysqli_fetch_assoc($productList)) {

                                extract($productData);

                                echo "<div class='col-xl-3 col-md-4 col-6'>
                                    <a href='{$link_url}'>
                                        <div class='flower_img'>
                                            <div class='img_card'>
                                                <img src='{$product_image}' class='img-fluid' alt='{$product_title}'>
                                            </div>
                                            <div class='flower_content'>
                                                <h6 class='is-font-20 dark-brown'>{$product_title}</h6>
                                                <span class='is-font-18 grey-color'>{$category_title}</span>
                                                <p class='dark-brown main_font_family is-font-26'>" . $inventory_currency_symbol . ' ' . (($inventory_price != 0) ? $inventory_price : $inventory_mrp) . "</p>
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
        </div>
    </section>

    <!-- offer section start -->
    <section class="offer_wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-7">
                    <div class="offer_img">
                        <img src="<?php echo USER_IMAGES ?>svg/valentine.svg" class="img-fluid" alt="">
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="offer_content">
                        <h1 class="is-font-45 dark-brown">Offer for valentines day</h1>
                        <p class="is-font-18 grey-color">Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                            Nullam
                            vel elit ,consequat arcuLorem
                            ipsum dolor sit amet, consectetur adipiscing elit. Nullam vel elit ,consequat .</p>
                        <h6 class="is-font-18 dark-brown">Save upto 20% off</h6>
                        <div class="shop_now">
                            <a href="/tag/father/" class="global_btn">Shop now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- feature section start -->
    <section class="feature_wrapper">
        <div class="container">
            <div class="main--heading">
                <h1 class="main_font_family is-font-45 black-color">Featured Categories</h1>
                <p class="grey-color ">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            </div>
        </div>
        <div class="feature_content">
            <div class="container-fluid">
                <div class="row">

                    <?php

                    if ($featureCategory = mysqli_query($connection, "SELECT category_title, category_image, link_url FROM " . TABLE_PREFIX . "category INNER JOIN " . TABLE_PREFIX . "link ON category_id=link_relation_id WHERE link_type='category' AND link_status=200 AND link_site_id=" . SITE_ID . " ORDER BY link_is_featured DESC LIMIT 4"))

                        if (mysqli_num_rows($featureCategory) != 0) {
                            while ($featureCategoryData = mysqli_fetch_assoc($featureCategory)) {
                                echo " <div class='col-md-3 col-6'>
                                    <a href='{$featureCategoryData['link_url']}'>
                                        <div class='feature_box' title='{$featureCategoryData['category_image']}'>
                                            <img src= '{$featureCategoryData['category_image']}' class='img-fluid' alt='{$featureCategoryData['category_title']}'>
                                            <div class='feature_heading'>
                                                <h1 class='is-font-26 dark-brown'>{$featureCategoryData['category_title']}</h1>
                                            </div>
                                        </div>
                                    </a>
                                </div>";
                            }
                        }

                    ?>

                </div>
            </div>
        </div>
    </section>

    <!-- flower price section start -->
    <section class="price_wrapper latest_product">
        <div class="container">
            <div class="main--heading">
                <h1 class="main_font_family is-font-45 black-color">Latest product</h1>
                <p class="grey-color ">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            </div>
        </div>
        <div class="price_content">
            <div class="container">

                <div class="row">

                    <?php

                    if ($productList = mysqli_query($connection, "SELECT link_id, link_url, product_title, product_image, inventory_currency, inventory_currency_symbol, inventory_price, inventory_mrp, category_title FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "category ON category_id=product_category WHERE inventory_type='product' AND link_type='product' AND link_status=200 AND inventory_in_stock=1 AND product_category != 170 AND product_category != 195 AND product_category!=77 AND product_category!=40 AND link_site_id=" . SITE_ID . " ORDER BY link_published DESC LIMIT 0, 8")) {

                        if (mysqli_num_rows($productList) != 0) {

                            while ($productData = mysqli_fetch_assoc($productList)) {

                                extract($productData);

                                echo "<div class='col-xl-3 col-md-4 col-6'>
                                    <a href='{$link_url}'>
                                        <div class='flower_img'>
                                            <div class='img_card'>
                                                <img src='{$product_image}' class='img-fluid' alt='{$product_title}'>
                                            </div>
                                            <div class='flower_content'>
                                                <h6 class='is-font-20 dark-brown'>{$product_title}</h6>
                                                <span class='is-font-18 grey-color'>{$category_title}</span>
                                                <p class='dark-brown main_font_family is-font-26'>" . $inventory_currency_symbol . ' ' . (($inventory_price != 0) ? $inventory_price : $inventory_mrp) . "</p>
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
        </div>
    </section>

    <!-- testimonial section start -->
    <section class="testimonial_wrapper">
        <div class="container">
            <div class="main--heading">
                <h1 class="main_font_family is-font-45 black-color">our customer review</h1>
            </div>
        </div>
        <div class="testimonial_content">
            <div class="container">
                <div class="testimonial_slider">
                    <div class="testimonial_card">
                        <div class="card_head">
                            <div class="card_head__img">
                                <img src="<?php echo USER_IMAGES ?>svg/user1.svg" class="img-fluid" alt="">
                            </div>
                            <div class="user_name">
                                <h3 class="dark-brown main_font_family is-font-20">Rahul kumar</h3>
                                <p class="is-font-18">lorem ipsum</p>
                                <div class="rating">
                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid" alt="">
                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid" alt="">
                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid" alt="">
                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid" alt="">
                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="user_parra">
                            <p class="silver-shade is-font-18">Lorem Ipsum is simply dummy text of the printing and
                                typesetting
                                industry. 1500s, Ipsum
                                is typesetting industry.</p>
                        </div>
                        <div class="read_more">
                            <a href="#" class="dark-brown is-font-18">Read more</a>
                        </div>
                    </div>
                    <div class="testimonial_card">
                        <div class="card_head">
                            <div class="card_head__img">
                                <img src="<?php echo USER_IMAGES ?>svg/userimg2.svg" class="img-fluid" alt="">
                            </div>
                            <div class="user_name">
                                <h3 class="dark-brown main_font_family is-font-20">Gourav kumar</h3>
                                <p class="is-font-18">lorem ipsum</p>
                                <div class="rating">
                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid" alt="">
                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid" alt="">
                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid" alt="">
                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid" alt="">
                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="user_parra">
                            <p class="silver-shade is-font-18">Lorem Ipsum is simply dummy text of the printing and
                                typesetting
                                industry. 1500s, Ipsum
                                is typesetting industry.</p>
                        </div>
                        <div class="read_more">
                            <a href="#" class="dark-brown is-font-18">Read more</a>
                        </div>
                    </div>
                    <div class="testimonial_card">
                        <div class="card_head">
                            <div class="card_head__img">
                                <img src="<?php echo USER_IMAGES ?>svg/userimg3.svg" class="img-fluid" alt="">
                            </div>
                            <div class="user_name">
                                <h3 class="dark-brown main_font_family is-font-20">Ravi kumar</h3>
                                <p class="is-font-18">lorem ipsum</p>
                                <div class="rating">
                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid" alt="">
                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid" alt="">
                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid" alt="">
                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid" alt="">
                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="user_parra">
                            <p class="silver-shade is-font-18">Lorem Ipsum is simply dummy text of the printing and
                                typesetting
                                industry. 1500s, Ipsum
                                is typesetting industry.</p>
                        </div>
                        <div class="read_more">
                            <a href="#" class="dark-brown is-font-18">Read more</a>
                        </div>
                    </div>
                    <div class="testimonial_card">
                        <div class="card_head">
                            <div class="card_head__img">
                                <img src="<?php echo USER_IMAGES ?>svg/user1.svg" class="img-fluid" alt="">
                            </div>
                            <div class="user_name">
                                <h3 class="dark-brown main_font_family is-font-20">Rahul kumar</h3>
                                <p class="is-font-18">lorem ipsum</p>
                                <div class="rating">
                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid" alt="">
                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid" alt="">
                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid" alt="">
                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid" alt="">
                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="user_parra">
                            <p class="silver-shade is-font-18">Lorem Ipsum is simply dummy text of the printing and
                                typesetting
                                industry. 1500s, Ipsum
                                is typesetting industry.</p>
                        </div>
                        <div class="read_more">
                            <a href="#" class="dark-brown is-font-18">Read more</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>
<?php

require_once 'footer.php';

?>