<?php

if ($checkProductMeta = mysqli_query($connection, "SELECT product_meta_key, product_meta_value FROM " . TABLE_PREFIX . "product_meta WHERE product_meta_relation_id='{$product_id}' AND product_meta_site_id=" . SITE_ID)) {

    if (mysqli_num_rows($checkProductMeta) != 0) {

        $productMeta = array();

        while ($checkProductMetaData = mysqli_fetch_assoc($checkProductMeta)) {


            // Create dynamic variable in the name of product_meta_key value

            ${$checkProductMetaData['product_meta_key']} = $checkProductMetaData['product_meta_value'];
        }
    }
}

require_once 'header.php';

?>

<main>
    <!-- single details section start -->
    <section class="single_wrapper">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="./index.html">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Product</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo $product_title ?></li>
                </ol>
            </nav>
            <div class="product_content">
                <div class="row">
                    <div class="col-xl-5 col-lg-6">
                        <div class="product_content__left">
                            <div class="small_page">

                                <?php

                                if ($product_gallery != '') {

                                    $galleryImages = json_decode($product_gallery, TRUE);

                                    if ($galleryImages != '') {
                                        for ($i = 0; $i < sizeof($galleryImages); $i++) {
                                            extract($galleryImages[$i]);
                                            echo "<div class='img_box'>
                                                    <img src='{$path}' class='img-fluid' alt='{$product_title}'
                                                        onclick='myFunction(this)'>
                                                </div>";
                                        }
                                    }
                                }

                                ?>

                            </div>
                            <div class="big_img" id="bigImage">
                                <img src="<?php echo $product_image ?>" class="img-fluid"
                                    alt="<?php echo $product_title ?>" id="imgBox" onclick="myFunction(this)">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5 col-lg-6">
                        <div class="product_content__right">
                            <h3 class="is-font-26 main_font_family dark-brown"><?php echo $product_title ?></h3>
                            <div class="product_rating d-flex">
                                <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid" alt="">
                                <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid" alt="">
                                <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid" alt="">
                                <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid" alt="">
                                <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid" alt="">
                                <p class="is-font-18 grey-color">928 Reviews</p>
                            </div>
                            <div class="flower_price">
                                <h3 class="main_font_family dark-brown is-font-26">₹6000 /-</h3>
                            </div>
                            <div class="flower_details">
                                <p class="grey-color is-font-18"><?php echo $product_highlight ?></p>
                                <p class="grey-color is-font-18 text-capitalize"><span class="dark-brown">Availability
                                        :</span> In
                                    Delhi,Gurugram,faridabad,noida only</p>
                            </div>
                            <div class="quantity">
                                <h3 class="is-font-18">Quantity :</h3>
                                <div class="quantity_no d-flex">
                                    <p id="minus-btn">-</p>
                                    <p id="count">1</p>
                                    <p id="plus-btn">+</p>
                                </div>
                            </div>
                            <div class="flower_cart">
                                <a href="#" class="global_btn is-font-18 dark-brown">Add to cart</a>
                                <?php
                                $whatsappUrl = 'https://wa.me/919910744119?text=' . ((isset($link_type)) ? (($link_type == 'product') ? urlencode('i want to know about ' . $product_title . ' ' . SITE_URL . ACTIVE_URL) : 'Hello') : 'Hello');
                                ?>
                                <a href="<?php echo $whatsappUrl ?>" target="_blank"
                                    class="global_btn is-font-18 dark-brown">Order on WhatsApp</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- single details description -->
    <section class="product_container">
        <div class="container">
            <div class="types_content">

                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home"
                            type="button" role="tab" aria-controls="home" aria-selected="true">Description</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile"
                            type="button" role="tab" aria-controls="profile" aria-selected="false">Reviews</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact"
                            type="button" role="tab" aria-controls="contact" aria-selected="false">Delivery
                            information</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div class="description_content">
                            <?php

                            echo $product_description;

                            ?>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="review_content">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-7">
                                        <div class="review_content__left">
                                            <div class="review_flex">
                                                <div class="user_img">
                                                    <img src="<?php echo USER_IMAGES ?>svg/user1.svg" class="img-fluid"
                                                        alt="">
                                                </div>
                                                <div class="user_details">
                                                    <p class="is-font-18 grey-color"><span>Ritik gupta</span> - 01
                                                        january 2024
                                                    </p>
                                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid"
                                                        alt="">
                                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid"
                                                        alt="">
                                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid"
                                                        alt="">
                                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid"
                                                        alt="">
                                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid"
                                                        alt="">
                                                    <p class="is-font-18 grey-color">Lorem ipsum dolor sit amet,
                                                        consectetur ad
                                                        elit,
                                                        sed do sedfeiusmod tempor incididunt ut labore et dolore
                                                        magna
                                                        aliqua.
                                                        Ut
                                                        enim
                                                        ad more minim veniam, quis nostrud ullamco laboris nisi ut
                                                        aliquiq
                                                        learn.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="review_flex">
                                                <div class="user_img">
                                                    <img src="<?php echo USER_IMAGES ?>svg/userimg2.svg"
                                                        class="img-fluid" alt="">
                                                </div>
                                                <div class="user_details">
                                                    <p class="is-font-18 grey-color"><span>Ritik gupta</span> - 01
                                                        january 2024
                                                    </p>
                                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid"
                                                        alt="">
                                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid"
                                                        alt="">
                                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid"
                                                        alt="">
                                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid"
                                                        alt="">
                                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid"
                                                        alt="">
                                                    <p class="is-font-18 grey-color">Lorem ipsum dolor sit amet,
                                                        consectetur ad
                                                        elit,
                                                        sed do sedfeiusmod tempor incididunt ut labore et dolore
                                                        magna
                                                        aliqua.
                                                        Ut
                                                        enim
                                                        ad more minim veniam, quis nostrud ullamco laboris nisi ut
                                                        aliquiq
                                                        learn.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="review_flex">
                                                <div class="user_img">
                                                    <img src="<?php echo USER_IMAGES ?>svg/userimg3.svg"
                                                        class="img-fluid" alt="">
                                                </div>
                                                <div class="user_details">
                                                    <p class="is-font-18 grey-color"><span>Ritik gupta</span> - 01
                                                        january 2024
                                                    </p>
                                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid"
                                                        alt="">
                                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid"
                                                        alt="">
                                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid"
                                                        alt="">
                                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid"
                                                        alt="">
                                                    <img src="<?php echo USER_IMAGES ?>svg/star.svg" class="img-fluid"
                                                        alt="">
                                                    <p class="is-font-18 grey-color">Lorem ipsum dolor sit amet,
                                                        consectetur ad
                                                        elit,
                                                        sed do sedfeiusmod tempor incididunt ut labore et dolore
                                                        magna
                                                        aliqua.
                                                        Ut
                                                        enim
                                                        ad more minim veniam, quis nostrud ullamco laboris nisi ut
                                                        aliquiq
                                                        learn.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="review_content__right">
                                            <h3 class="is-font-26 main_font_family text-capitalize">Add a review
                                            </h3>
                                            <p class="is-font-18 grey-color">Your email address will not be
                                                published.
                                            </p>
                                            <div class="myRating">
                                                <span class="is-font-18">Your rating :</span>
                                                <div class="rating_star">
                                                    <input type="radio" name="rating" id="rating-5">
                                                    <label for="rating-5"></label>
                                                    <input type="radio" name="rating" id="rating-4">
                                                    <label for="rating-4"></label>
                                                    <input type="radio" name="rating" id="rating-3">
                                                    <label for="rating-3"></label>
                                                    <input type="radio" name="rating" id="rating-2">
                                                    <label for="rating-2"></label>
                                                    <input type="radio" name="rating" id="rating-1">
                                                    <label for="rating-1"></label>
                                                </div>

                                            </div>
                                            <form action="">
                                                <div class="form_wrapper">
                                                    <div class="form_input">
                                                        <label for="">Your review :</label>
                                                        <textarea name="" id="" cols="30" rows="4" required></textarea>
                                                    </div>
                                                    <div class="form_input">
                                                        <label for="">Name :</label>
                                                        <input type="text" required>
                                                    </div>
                                                    <div class="form_input">
                                                        <label for="">Email : </label>
                                                        <input type="email" required>
                                                    </div>
                                                    <div class="submit">
                                                        <button class="global_btn">submit</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                        <div class="description_content">

                            <?php

                            echo $delivery_information

                            ?>

                        </div>
                    </div>


                </div>


            </div>
    </section>

</main>

<?php

require_once 'footer.php';

?>