<?php

if (!isset($test)) {
    header("Location: /", TRUE, 301);
    exit();
} else {
    if ($test != '') {
        $testLenght = explode('_', $test);
    } else {
        header("Location: /", TRUE, 301);
        exit();
    }
}

require_once 'header.php';

?>
<main>
    <section class="hero side-padding">
        <div class="container">
            <div class="row align-items-center " style="height: 30vh;">
                <div class="col-12 col-lg-12">
                    <h2 class="isfont-40 text-white my-3 is-font600 text-center ">
                        Selected tests : <?php $testName = ucwords(urldecode($test));
                                            echo str_replace('_', ', ', $testName);
                                            $testName = str_replace('_', "', '", $testName) ?>
                    </h2>

                </div>

            </div>
        </div>
    </section>
    <div class="opacity"></div>

    <section class="vartical-padding">
        <div class="container-fluid">
            <div class="row position-realtive">
                <div class="col-3 d-none d-lg-block sticky-top side-bar">
                    <div class="d-flex justify-content-end pe-3 pb-3">
                        <div class="filter-close  d-lg-none  ">
                            <i class='bx bxs-x-circle isfont-28 text-white'></i>
                        </div>
                    </div>
                    <div class="sticky-top">
                        <img src="<?php echo USER_IMAGES ?>side.png" alt="" class="img-fluid" />

                        <div class="my-5">
                            <h3 class="isfont-22 clrpurpl ms-5">Certification</h3>
                            <div class="m-4 py-3 filter-checbox">
                                <div class="form-check my-3">
                                    <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" />
                                    <label class="form-check-label" for="flexCheckDefault">
                                        ICMR
                                    </label>
                                </div>

                                <div class="form-check my-3">
                                    <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" />
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Cap
                                    </label>
                                </div>

                                <div class="form-check my-3">
                                    <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" />
                                    <label class="form-check-label" for="flexCheckDefault">
                                        ISO
                                    </label>
                                </div>

                                <div class="form-check my-3">
                                    <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" />
                                    <label class="form-check-label" for="flexCheckDefault">
                                        NABI
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-9">
                    <div class="px-4">
                        <div class="d-flex justify-content-end">
                            <div class="filter-icon  d-lg-none">
                                <i class='bx bx-menu isfont-28 text-white'></i>
                            </div>
                        </div>

                        <div class="detatils-list">


                            <!-- Test package query -->

                            <?php

                            if (count($testLenght) == 1) {
                                $selectTestsQuery = "SELECT category_thumbnail as lab_brand_logo, category_title as lab_brand_title, product_id, product_title, product_highlight, inventory_currency_symbol, inventory_price, inventory_mrp FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "category ON category_id = product_brand_id INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id = product_id WHERE product_title IN ('{$testName}')  AND product_type='diagnostic' AND category_type='brand' AND product_site_id=" . SITE_ID . " AND inventory_site_id = " . SITE_ID . " AND category_site_id = " . SITE_ID;
                            } else {
                                $selectTestsQuery = "SELECT category_thumbnail as lab_brand_logo, category_title as lab_brand_title, GROUP_CONCAT(product_id) as product_id, GROUP_CONCAT(product_title) as product_title, product_highlight, inventory_currency_symbol, SUM(inventory_price) as inventory_price, SUM(inventory_mrp) as inventory_mrp FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "category ON category_id = product_brand_id INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id = product_id WHERE product_title IN ('{$testName}')  AND product_type='diagnostic' AND category_type='brand' AND product_site_id=" . SITE_ID . " AND inventory_site_id = " . SITE_ID . " AND category_site_id = " . SITE_ID;
                            }

                            if ($similarTestsQuery = mysqli_query($connection, $selectTestsQuery)) {

                                if (mysqli_num_rows($similarTestsQuery) != 0) {
                                    while ($similarTests = mysqli_fetch_assoc($similarTestsQuery)) {
                                        extract($similarTests);


                            ?>

                                        <div class="row list-box align-items-center">
                                            <div class="col-12 col-md-2 my-2">
                                                <div class="">
                                                    <div>
                                                        <img src="<?php echo $lab_brand_logo ?>" alt="" class="img-fluid list-photo d-block m-auto pb-3" />
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="col-12 col-md-4 my-2">
                                                <div class="d-flex align-items-center ">
                                                    <div class="py-2">
                                                        <img src="<?php echo USER_IMAGES ?>up.png" alt="" style="height: 25px;"> :
                                                    </div>
                                                    <div class="d-flex justify-content-between">

                                                        <div class="mx-2">
                                                            <h2 class="isfont-15   is-font600 m-0">ICMR ,</h2>
                                                        </div>

                                                        <div class="mx-2">
                                                            <h2 class="isfont-15  clrpurpl is-font600 m-0">CAP ,</h2>
                                                        </div>

                                                        <div class="mx-2">
                                                            <h2 class="isfont-15  clrpurpl is-font600 m-0">ISO ,</h2>
                                                        </div>

                                                        <div class="mx-2">
                                                            <h2 class="isfont-15  clrpurpl is-font600 m-0">NABI </h2>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- <div class="d-flex align-items-center ">
                                        <div class="py-2">
                                            <img src="<?php echo USER_IMAGES ?>f-score.png" alt=""
                                                style="height: 25px;"> :
                                        </div>
                                        <div class="d-flex justify-content-between">

                                            <div class="mx-2">
                                                <h2 class="isfont-15   is-font600 m-0">90% Accuracy</h2>
                                            </div>
                                        </div>
                                    </div> -->

                                            </div>

                                            <div class="col-12 col-md-3 my-2">
                                                <div class="d-flex align-items-center ">
                                                    <!-- <div class="py-2">
                                                        <img src="<?php echo USER_IMAGES ?>delivery.png" alt="" style="height: 25px;"> :
                                                    </div> -->
                                                    <div class="d-flex justify-content-between">

                                                        <div class="mx-2">
                                                            <h2 class="isfont-15   is-font600 m-0"><?php echo '' ?></h2>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center ">
                                                    <div class="py-2">
                                                        <img src="<?php echo USER_IMAGES ?>rupee-indian.png" alt="" style="height: 25px;"> :
                                                    </div>
                                                    <div class="d-flex justify-content-between">

                                                        <div class="mx-2">
                                                            <h2 class="isfont-15   is-font600 m-0">
                                                                <?php echo $inventory_price ?>
                                                            </h2>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-3 justify-content-start justify-content-lg-end d-flex ">
                                                <a href="/checkout/?test_id=<?php echo base64_encode($product_id) . '&checkout_type=' . base64_encode('test') ?>" class="purpal-btn btn1 listbtn ">Book now</a>
                                            </div>
                                        </div>

                            <?php
                                    }
                                }
                            }

                            ?>

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