<?php

require_once 'header.php';

?>
<section class="hero side-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 col-lg-7">
                <h2 class="isfont-28 text-white my-3 is-font600">
                    Book a Lab Test from the lab of your choice
                </h2>
                <div class="hero-input d-flex align-items-center">
                    <i class="bx bx-search mx-3 isfont-24 ps-3 is-txt-color2"></i>
                    <input type="text" id="searchBar" placeholder="Type to search test" />
                    <div id="searchResults"></div>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-6 is-color-blue">
                        <div class="hero-input d-flex mt-3 px-4 prescription">
                            <i class="bx bx-cloud-upload isfont-24 is-margin-side-20"></i>
                            <button class="is-color-blue isfont-18 ">
                                Upload Prescription
                            </button>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 is-color-blue">
                        <a href="tel:+91 9212535996">
                            <div class="hero-input d-flex mt-3 px-4">
                                <i class="bx bx-phone-call isfont-24 is-margin-side-20 is-color-blue"></i>
                                <button class="is-color-blue isfont-18">
                                    Call to book
                                </button>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="selectedTests">
                    <h4>Selected tests</h4>
                    <div class="testsName"></div>
                    <a href="" class="is-color-blue isfont-18 btn1 package-btn" id="selectedTestBookNow">Book Now</a>
                </div>
            </div>
            <div class="col-12 col-lg-5 my-4 my-lg-0">
                <img src="<?php echo USER_IMAGES ?>side-image.png" alt=""
                    class="img-fluid d-none d-lg-block m-auto ms-lg-auto" />
            </div>
        </div>
    </div>

    <div class="opacity"></div>

    <div class="prescription-form">
        <div class="container">
            <div class="row justify-content-between align-items-center">
                <div class="col-8">
                    <h2 class="isfont-28 text-dark my-3 is-font600">
                        Book Your Test
                    </h2>
                </div>

                <div class="col-4">
                    <div class="close-form  d-flex justify-content-end">
                        <i class='bx bxs-x-circle isfont-28 text-dark d-flex justify-content-end'></i>
                    </div>
                </div>
                <div>

                </div>
            </div>

            <div class="py-3">
                <form method="post" action="" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-12">
                            <div class="my-3">
                                <input type="text" class="form-control" name="name" id="uploadName"
                                    placeholder="Enter Your Name" required>

                            </div>
                        </div>

                        <div class="col-12">
                            <div class="my-3">
                                <input type="number" onwheel="this.blur()" name="mobile" class="form-control"
                                    id="uploadMobile" placeholder="Enter Phone Number" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="my-3">
                                <input type="file" class="form-control" name="upload" id="uploadPrescription" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control" name="address" placeholder="Leave a comment here"
                                    id="uploadAddress" required></textarea>
                                <label for="floatingTextarea">Enter Your Address </label>
                            </div>
                        </div>

                        <div class="col-12 my-4">
                            <input type="hidden" name="type" value="enquiry">
                            <input type="hidden" name="action" value="new">
                            <input type="hidden" name="subject" value="Upload prescription form">
                            <input type="hidden" name="sender"
                                value="<?php echo base64_encode('diagnosticcare.in@gmail.com') ?>">
                            <input type="hidden" name="redirect_url"
                                value="/thank-you/?form_type=true&msg=Thank+you+for+your+query%2Ffeedback.+It+is+important+for+us.">
                            <input type="submit" class="btn w-100 btn1" value="Submit">
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</section>



<section class="parnters vartical-padding side-padding">
    <div class="container">
        <div>
            <h3 class="isfont-24 is-font600 is-color-blue">Our Partner Labs</h3>
        </div>
        <div class="row brandSlider overflow-hidden">

            <?php

            if ($brandLogo = mysqli_query($connection, "SELECT category_title, category_thumbnail FROM " . TABLE_PREFIX . "category WHERE category_site_id = " . SITE_ID . " AND category_type ='brand'")) {
                if (mysqli_num_rows($brandLogo) != 0) {
                    while ($brandLogoInfo = mysqli_fetch_assoc($brandLogo)) {
                        extract($brandLogoInfo);
                        echo '<div class="col-3">
                            <div class="brandSliderImage">
                              <img src="' . $category_thumbnail . '" alt="" />
                          </div>
                      </div>';
                    }
                }
            }

            ?>
        </div>
    </div>
</section>

<section class="vartical-padding package-parent side-padding">
    <div class="container">
        <div>
            <h3 class="isfont-24 is-font600 is-color-blue">Our Partner Packages</h3>
        </div>
        <div class="row my-4 packge-slider overflow-hidden">
            <?php

            if ($packageList = mysqli_query($connection, "SELECT category_title as productBrand, category_image as productBrandLogo, product_id, product_title, product_highlight, product_description, product_image, product_thumbnail, product_type, inventory_currency_symbol, inventory_mrp, inventory_price, link_url FROM isw_product INNER JOIN isw_inventory ON product_id=inventory_relation_id INNER JOIN " . TABLE_PREFIX . "category ON category_id = product_brand_id INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id WHERE product_site_id =  " . SITE_ID . " AND link_status = 200 AND inventory_site_id = " . SITE_ID . " AND product_type = 'packages' AND link_type='product' LIMIT 0, 20")) {

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
                        <div class="popup knowMore">know more
                            <span class="popuptext"><?php echo $product_description ?></span>
                        </div>
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
                                    <a href="/checkout/?test_id=<?php echo base64_encode($product_id) . '&checkout_type=' . base64_encode('package') ?>"
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
</section>

<section class="vartical-padding side-padding">
    <div class="container">
        <div>
            <h3 class="isfont-24 is-font600 is-color-blue">Popular Tests</h3>
        </div>
        <div class="row my-4 tests-slider overflow-hidden">
            <?php

            if ($packageList = mysqli_query($connection, "SELECT product_id, product_title, product_highlight, product_description, product_image, product_thumbnail, product_type, inventory_currency_symbol, inventory_mrp, inventory_price FROM isw_product INNER JOIN isw_product_meta ON product_id=product_meta_relation_id INNER JOIN isw_inventory ON product_id=inventory_relation_id WHERE product_site_id =  " . SITE_ID . " AND inventory_site_id = " . SITE_ID . " AND product_meta_site_id =  " . SITE_ID . "  AND link_status = 200 AND product_type = 'diagnostic' GROUP BY product_title LIMIT 0, 9")) {

                if (mysqli_num_rows($packageList) != 0) {
                    while ($packageListInfo = mysqli_fetch_assoc($packageList)) {

                        extract($packageListInfo); ?>

            <div class="col-12 col-sm-12 col-md-6 col-lg-4 ">
                <div class="card tests-cards my-3">

                    <!-- Package card -->
                    <div class="card-body smallCard">
                        <h5 class="card-title isfont-28 cardHeading is-color-blue is-font600">
                            <?php echo ucwords($product_title) ?>
                        </h5>
                        <div class="card-bottom">
                            <div class="my-4 d-flex cardHeading align-items-center justify-content-between">
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
            } else {
                echo ((DEVELOPMENT_MODE==true) ? mysqli_error($connection) : "");
            }

            ?>
        </div>
    </div>
</section>

<section class="package-parent vartical-padding side-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 col-lg-6">
                <img src="<?php echo USER_IMAGES ?>about-image.png" alt="" class="img-fluid" />
            </div>
            <div class="col-12 col-lg-6">
                <div>
                    <h3 class="isfont-24 is-font600 is-color-blue my-5">
                        Why Choose Diagnostic Services?
                    </h3>
                    <p class="text-justify">
                        Diagnostic Services is a premium medical health care facility,
                        with years of experience and 50,000+ satisfied customers within
                        the medical industry. Our lab's equipment and other primary
                        infrastructure are authenticated for smooth and proper
                        functioning at all times .
                    </p>

                    <div class="my-5">
                        <a href='./about.php' class="btn1 btn-pill">Read more</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="vartical-padding side-padding testi">
    <div class="container">
        <div>
            <h3 class="isfont-24 is-font600 is-color-blue">Testimonial</h3>
        </div>
        <div class="row my-4 packge-slider overflow-hidden">
            <div class="col-3">
                <div class="card testi-card">
                    <div>
                        <img src="<?php echo IMG_LIBRARY ?>profile_icon/r.png" class="card-img-top" />
                        <h4 class="isfont-18 is-color-blue text-center my-3 is-font600">
                            Rakesh Sharma
                        </h4>
                    </div>
                    <div class="card-body p-0">
                        <p class="card-text isfont-15 my-2">
                            Great service neat and clean best Diagnotic center in Malviya nagar.
                        </p>
                    </div>

                    <div class="rating my-2">
                        <img src="<?php echo USER_IMAGES ?>star.png" alt="" class="img-fluid mx-1" />

                        <img src="<?php echo USER_IMAGES ?>star.png" alt="" class="img-fluid mx-1" />

                        <img src="<?php echo USER_IMAGES ?>star.png" alt="" class="img-fluid mx-1" />

                        <img src="<?php echo USER_IMAGES ?>star.png" alt="" class="img-fluid mx-1" />
                        <img src="<?php echo USER_IMAGES ?>star-nulll.png" alt="" class="img-fluid mx-1" />
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card testi-card">
                    <div>
                        <img src="<?php echo IMG_LIBRARY ?>profile_icon/r.png" class="card-img-top" />
                        <h4 class="isfont-18 is-color-blue text-center my-3 is-font600">
                            Ritu Yadav
                        </h4>
                    </div>
                    <div class="card-body p-0">
                        <p class="card-text isfont-15 my-2">
                            Great service, Home sample collection is super fast .
                        </p>
                    </div>

                    <div class="rating my-2">
                        <img src="<?php echo USER_IMAGES ?>star.png" alt="" class="img-fluid mx-1" />

                        <img src="<?php echo USER_IMAGES ?>star.png" alt="" class="img-fluid mx-1" />

                        <img src="<?php echo USER_IMAGES ?>star.png" alt="" class="img-fluid mx-1" />

                        <img src="<?php echo USER_IMAGES ?>star.png" alt="" class="img-fluid mx-1" />
                        <img src="<?php echo USER_IMAGES ?>star-nulll.png" alt="" class="img-fluid mx-1" />
                    </div>
                </div>
            </div>

            <div class="col-3">
                <div class="card testi-card">
                    <div>
                        <img src="<?php echo IMG_LIBRARY ?>profile_icon/m.png" class="card-img-top" />
                        <h4 class="isfont-18 is-color-blue text-center my-3 is-font600">
                            Mohammad Zeeshan
                        </h4>
                    </div>
                    <div class="card-body p-0">
                        <p class="card-text isfont-15 my-2">
                            Very good Experience very friendly and helpful staff
                        </p>
                    </div>

                    <div class="rating my-2">
                        <img src="<?php echo USER_IMAGES ?>star.png" alt="" class="img-fluid mx-1" />

                        <img src="<?php echo USER_IMAGES ?>star.png" alt="" class="img-fluid mx-1" />

                        <img src="<?php echo USER_IMAGES ?>star.png" alt="" class="img-fluid mx-1" />

                        <img src="<?php echo USER_IMAGES ?>star.png" alt="" class="img-fluid mx-1" />
                        <img src="<?php echo USER_IMAGES ?>star-nulll.png" alt="" class="img-fluid mx-1" />
                    </div>
                </div>
            </div>

            <div class="col-3">
                <div class="card testi-card">
                    <div>
                        <img src="<?php echo IMG_LIBRARY ?>profile_icon/r.png" class="card-img-top" />
                        <h4 class="isfont-18 is-color-blue text-center my-3 is-font600">
                            Ranjana Sharma
                        </h4>
                    </div>
                    <div class="card-body p-0">
                        <p class="card-text isfont-15 my-2">
                            I booked home collection very fast service.. collection boy come to home and given report on
                            same day . Very polite behaviour of sample collection executive and owner response. </p>
                    </div>

                    <div class="rating my-2">
                        <img src="<?php echo USER_IMAGES ?>star.png" alt="" class="img-fluid mx-1" />

                        <img src="<?php echo USER_IMAGES ?>star.png" alt="" class="img-fluid mx-1" />

                        <img src="<?php echo USER_IMAGES ?>star.png" alt="" class="img-fluid mx-1" />

                        <img src="<?php echo USER_IMAGES ?>star.png" alt="" class="img-fluid mx-1" />
                        <img src="<?php echo USER_IMAGES ?>star-nulll.png" alt="" class="img-fluid mx-1" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php

require_once 'footer.php';

?>