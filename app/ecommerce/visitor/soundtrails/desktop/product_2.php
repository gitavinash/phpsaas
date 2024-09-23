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
                        <div class="col-12 col-sm-12 col-md-12 col-xl-4 col-xxl-2 order-2 order-xxl-1 mt-2 mt-xxl-0 d-none d-lg-block flex_div">
                            <?php

                            if ($product_gallery != '') {

                                $galleryImages = json_decode($product_gallery, TRUE);

                                if ($galleryImages != '') { ?>
                                    <div class="sm-image-group mt-3 mt-xl-0">
                                        <img src="<?php echo $product_image ?>" class="img-fluid" alt="<?php echo $product_title ?>" />
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
                        <div class="col-12 col-sm-12 col-md-12 col-xl-4 col-xxl-2 order-2 order-xxl-1 mt-2 mt-xxl-0 flex_div d-block d-xl-none">
                            <div class="slider"><?php

                                                if ($product_gallery != '') {

                                                    $galleryImages = json_decode($product_gallery, TRUE);

                                                    if ($galleryImages != '') {

                                                ?>
                                        <div class="sm-image-group mt-3 mt-xl-0 mx-3">
                                            <img src="<?php echo $path ?>" class="img-fluid d-block m-auto" alt="<?php echo $name ?>" />
                                        </div>
                                        <?php

                                                        for ($i = 0; $i < sizeof($galleryImages); $i++) {

                                                            extract($galleryImages[$i]); ?>
                                            <div class="sm-image-group mt-3 mt-xl-0 mx-3">
                                                <img src="<?php echo $path ?>" class="img-fluid d-block m-auto" alt="<?php echo $name ?>" />
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
                                <img src="<?php echo $product_image ?>" class="img-fluid" alt="<?php echo $product_title ?>" />
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
                        <div>
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
                                    <input type="number" onwheel="this.blur()" placeholder="1" value="1" />
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
                    <p style="display:none" id="productInfo" value="<?php echo $product_id . '_' . (($inventory_price == 0) ? $inventory_mrp : $inventory_price) . '_1' ?>">
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
        <div class="container-fluid py-5">
            <div class="row gx-0 justify-content-center justify-content-xxl-start">
                <div class="col-12 col-sm-12 col-md-10 col-xl-4 col-xxl-3">
                    <div class="tabs_div active_div">
                        <p>DESCRIPTION</p>
                    </div>
                </div>
                <!-- <div class="col-12 col-sm-12 col-md-10 col-xl-4 col-xxl-3 my-3 mt-xl-0">
                    <div class="tabs_div ">
                        <p>CUSTOMER REVIEWS</p>
                    </div>
                </div> -->
                <div class="col-12 col-sm-12 col-md-10 col-xl-4 col-xxl-3">
                    <div class="tabs_div ">
                        <p>SHIPPING & RETURNS</p>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

    <!-- <section class="products-section pb-5 mb-5 mt-4 mt-xxl-0">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-sm-12 col-md-12 col-xl-6 text-center text-xl-start">
                    <img src="https://in.jbl.com/on/demandware.static/-/Sites-masterCatalog_Harman/default/dw4117cc35/pdp/JBL_GO3_WORKOUT_LIFESTYLE01_904x560px.png"
                        class="img-fluid" alt="" />
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-xl-6 mt-4 mt-xl-0 text-center text-xl-start">
                    <h3 class="mb-4">High-fidelity Sound</h3>
                    <p>
                        This is a 2.1 ch soundbar that comes with a 16.5-cm (6.5)
                        subwoofer to deliver intense and powerful sound with deep bass.
                        This home audio system fills your room with surround sound for
                        an immersive aural experience.
                    </p>
                </div>
            </div>
            <div class="row align-items-center my-2 my-xl-0">
                <div
                    class="col-12 col-sm-12 col-md-12 col-xl-6 order-2 order-xl-1 mt-4 mt-xl-0 text-center text-xl-start">
                    <h3 class="mb-4">One Remote Control</h3>
                    <p>
                        With this One Remote Control, you do not need multiple remote
                        controls for your entertainment. Control key soundbar operations
                        like volume, power, and sound effects with your Samsung TV
                        remote directly from your TV. This remote control is compatible
                        with selected models of Samsung TVs.
                    </p>
                </div>

                <div
                    class="col-12 col-sm-12 col-md-12 col-xl-6 order-1 order-xl-2 text-center text-xl-start d-flex justify-content-end">
                    <img src="https://in.jbl.com/on/demandware.static/-/Sites-masterCatalog_Harman/default/dwde7716a2/pdp/JBL_GO3_RED_LIFESTYLE02_904x560px.png"
                        class="img-fluid" alt="" />
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-12 col-sm-12 col-md-12 col-xl-6 text-center text-xl-start">
                    <img src="https://in.jbl.com/on/demandware.static/-/Sites-masterCatalog_Harman/default/dw1a34f218/pdp/JBL_GO3_GREY_LIFESTYLE03_904x560px.png"
                        class="img-fluid" alt="" />
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-xl-6 mt-4 mt-xl-0 text-center text-xl-start">
                    <h3 class="mb-4">Optimized Sound for Different Content</h3>
                    <p>
                        This sound system analyzes the source of audio automatically,
                        then optimises the audio track according to the specific
                        content. So, you can enjoy loud cheers of sporting events as
                        well as soothing dialogues of romantic dramas on this soundbar.
                    </p>
                </div>
            </div>
            <div class="row align-items-center mt-2 mt-xl-0">
                <div
                    class="col-12 col-sm-12 col-md-12 col-xl-6 order-2 order-xl-1 mt-4 mt-xl-0 text-center text-xl-start">
                    <h3 class="mb-4">Game Mode</h3>
                    <p>
                        You can plug in your gaming console to experience impressive
                        sound effects while playing your favorite games. This soundbar
                        has auto settings to optimise the game audio for impressive
                        output. Also, it has the cross-talk cancellation technology to
                        remove any distracting sound. As a result, you can hear clearly
                        and follow the necessary instructions. This feature is available
                        on compatible TVs and consoles (over Xbox One, PS4, and Nintendo
                        Switch)
                    </p>
                </div>

                <div
                    class="col-12 col-sm-12 col-md-12 col-xl-6 order-1 order-xl-2 text-center text-xl-start d-flex justify-content-end">
                    <img src="https://in.jbl.com/on/demandware.static/-/Sites-masterCatalog_Harman/default/dw59f8399a/pdp/JBL_Go2_waterproof-f03.png"
                        class="img-fluid" alt="" />
                </div>
            </div>
        </div>
    </section> -->

    <!-- <section class="spec sidepadding py-5">
        <div class="container-fluid table">
            <div class="row">
                <div class="col-12 border_bottom py-4 px-4">
                    <h5>Specifications</h5>
                </div>
                <div class="col-12 py-3 px-4 border_bottom">
                    <h4 class="mb-0">General</h4>

                    <div class="col-12 table_content">
                        <div class="row gx-0">
                            <div class="col-12 col-sm-12 col-md-2">
                                <p>Sales Package</p>
                            </div>
                            <div class="col-12 col-sm-12 col-md-10">
                                <p>
                                    : 1 Unit Main Speaker, 1 Unit Sub Woofer, 1 Unit Remote
                                    Controller, 1 Unit Wall Mount Bracket, 2 AAA Batteries,
                                    User Manual
                                </p>
                            </div>
                        </div>
                        <div class="row gx-0">
                            <div class="col-12 col-sm-12 col-md-2">
                                <p>Model Number</p>
                            </div>
                            <div class="col-12 col-sm-12 col-md-10">
                                <p>: Model Number</p>
                            </div>
                        </div>
                        <div class="row gx-0">
                            <div class="col-12 col-sm-12 col-md-2">
                                <p>Model Name</p>
                            </div>
                            <div class="col-12 col-sm-12 col-md-10">
                                <p>: HW-T42E/XL Dolby Digital</p>
                            </div>
                        </div>
                        <div class="row gx-0">
                            <div class="col-12 col-sm-12 col-md-2">
                                <p>Power Source</p>
                            </div>
                            <div class="col-12 col-sm-12 col-md-10">
                                <p>: AC Adapter</p>
                            </div>
                        </div>
                        <div class="row gx-0">
                            <div class="col-12 col-sm-12 col-md-2">
                                <p>Color</p>
                            </div>
                            <div class="col-12 col-sm-12 col-md-10">
                                <p>: Black</p>
                            </div>
                        </div>
                        <div class="row gx-0">
                            <div class="col-12 col-sm-12 col-md-2">
                                <p>Outdoor Usage</p>
                            </div>
                            <div class="col-12 col-sm-12 col-md-10">
                                <p>: Yes</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 py-3 px-4 border_bottom">
                    <h4 class="mb-0">Product Details</h4>

                    <div class="col-12 table_content">
                        <div class="row gx-0">
                            <div class="col-12 col-sm-12 col-md-2">
                                <p>Configuration</p>
                            </div>
                            <div class="col-12 col-sm-12 col-md-10">
                                <p>: 2.1 Channel</p>
                            </div>
                        </div>
                        <div class="row gx-0">
                            <div class="col-12 col-sm-12 col-md-2">
                                <p>Driver Type</p>
                            </div>
                            <div class="col-12 col-sm-12 col-md-10">
                                <p>: 1 Sub Woofer</p>
                            </div>
                        </div>
                        <div class="row gx-0">
                            <div class="col-12 col-sm-12 col-md-2">
                                <p>Compatible Devices</p>
                            </div>
                            <div class="col-12 col-sm-12 col-md-10">
                                <p>: Mobile</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 py-3 px-4 border_bottom">
                    <h4 class="mb-0">Audio Features</h4>

                    <div class="col-12 table_content">
                        <div class="row gx-0">
                            <div class="col-12 col-sm-12 col-md-2">
                                <p>Tweeter Support</p>
                            </div>
                            <div class="col-12 col-sm-12 col-md-10">
                                <p>: Yes</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 py-3 px-4 border_bottom">
                    <h4 class="mb-0">Other Details</h4>

                    <div class="col-12 table_content">
                        <div class="row gx-0">
                            <div class="col-12 col-sm-12 col-md-2">
                                <p>Remote Control Support</p>
                            </div>
                            <div class="col-12 col-sm-12 col-md-10">
                                <p>: Wireless</p>
                            </div>
                        </div>
                        <div class="row gx-0">
                            <div class="col-12 col-sm-12 col-md-2">
                                <p>Power Input</p>
                            </div>
                            <div class="col-12 col-sm-12 col-md-10">
                                <p>: 0.5</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 py-3 px-4 border_bottom">
                    <h4 class="mb-0">Dimensions</h4>

                    <div class="col-12 table_content">
                        <div class="row gx-0">
                            <div class="col-12 col-sm-12 col-md-2">
                                <p>Width</p>
                            </div>
                            <div class="col-12 col-sm-12 col-md-10">
                                <p>: 86 cm</p>
                            </div>
                        </div>
                        <div class="row gx-0">
                            <div class="col-12 col-sm-12 col-md-2">
                                <p>Height</p>
                            </div>
                            <div class="col-12 col-sm-12 col-md-10">
                                <p>: 5.4 cm</p>
                            </div>
                        </div>
                        <div class="row gx-0">
                            <div class="col-12 col-sm-12 col-md-2">
                                <p>Depth</p>
                            </div>
                            <div class="col-12 col-sm-12 col-md-10">
                                <p>: 7.4 cm</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 py-3 px-4 border_bottom">
                    <h4 class="mb-0">Warranty</h4>

                    <div class="col-12 table_content">
                        <div class="row gx-0">
                            <div class="col-12 col-sm-12 col-md-2">
                                <p>Warranty Summary</p>
                            </div>
                            <div class="col-12 col-sm-12 col-md-10">
                                <p>
                                    : 1 Year Warranty Provided by the Manufacturer from Date
                                    of Invoice
                                </p>
                            </div>
                        </div>
                        <div class="row gx-0">
                            <div class="col-12 col-sm-12 col-md-2">
                                <p>Domestic Warranty</p>
                            </div>
                            <div class="col-12 col-sm-12 col-md-10">
                                <p>: 1 Year</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> -->

    <!-- <section class="vertical-padding sidepadding">
        <div class="container-fluid">
            <div class="row">
                <h5 class="text-center font-dancing font-size-31" style="color: var(--golden)">
                    Treding Product
                </h5>

                <h3 class="text-center font-size-57 font-work-sans text-white">
                    Recently <span style="color: var(--golden)">viewed</span>
                </h3>

                <div class="row products my-5 justify-content-center">
                    <div class="col-12 col-sm-12 col-md-8 col-xl-6 col-xxl-3">
                        <img src="./assest/product/image 27.png" alt="" class="img-fluid" />
                        <div class="product-details">
                            <p class="text-white font-size-20 font-family-sans">
                                Blaupunkt Germany's SBW200
                            </p>
                        </div>
                        <div class="rating-price d-flex justify-content-between">
                            <div class="price text-white font-size-20 font-family-sans">
                                ₹122.00
                            </div>
                            <div class="rating d-flex">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-12 col-md-8 col-xl-6 col-xxl-3">
                        <img src="./assest/product/image 28.png" alt="" class="img-fluid" />
                        <div class="product-details">
                            <p class="text-white font-size-20 font-family-sans">
                                Blaupunkt Germany's SBW200
                            </p>
                        </div>
                        <div class="rating-price d-flex justify-content-between">
                            <div class="price text-white font-size-20 font-family-sans">
                                ₹122.00
                            </div>
                            <div class="rating d-flex">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-12 col-md-8 col-xl-6 col-xxl-3">
                        <img src="./assest/product/image 29.png" alt="" class="img-fluid" />
                        <div class="product-details">
                            <p class="text-white font-size-20 font-family-sans">
                                Blaupunkt Germany's SBW200
                            </p>
                        </div>
                        <div class="rating-price d-flex justify-content-between">
                            <div class="price text-white font-size-20 font-family-sans">
                                ₹122.00
                            </div>
                            <div class="rating d-flex">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-12 col-md-8 col-xl-6 col-xxl-3">
                        <img src="./assest/product/image 30.png" alt="" class="img-fluid" />
                        <div class="product-details">
                            <p class="text-white font-size-20 font-family-sans">
                                Blaupunkt Germany's SBW200
                            </p>
                        </div>
                        <div class="rating-price d-flex justify-content-between">
                            <div class="price text-white font-size-20 font-family-sans">
                                ₹122.00
                            </div>
                            <div class="rating d-flex">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> -->
</main>

<!-- treding product -->

<!-- testimonail -->

<!-- /testimonial -->

<!-- treding -end -->

<?php require_once('footer.php') ?>