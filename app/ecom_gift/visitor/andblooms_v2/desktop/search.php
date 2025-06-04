<?php

require_once 'header.php';

?>
<main>
    <section class="categories_banner">
        <div class="categories_content">
            <h1 class="main_font_family dark-brown is-font-55 d-flex">
                <?php echo str_replace('-', ' ', ucwords($searchKeyword)); ?>
            </h1>
        </div>
    </section>

    <!-- categories details section start -->
    <section class="categories_wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="categories_wrapper__left">
                        <form action="">
                            <div class="categories_filter">
                                <h3 class="is-font-20 dark-brown main_font_family">Filter by price</h3>
                                <div class="wrapper">
                                    <div class="slider">
                                        <div class="progress"></div>
                                    </div>
                                    <div class="range-input">
                                        <input type="range" class="range-min" min="0" max="10000" value="2500"
                                            step="100">
                                        <input type="range" class="range-max" min="0" max="10000" value="7500"
                                            step="100">
                                    </div>
                                    <div class="price-input">
                                        <div class="field">
                                            <span>Min</span>
                                            <input type="number" class="input-min" value="2500">
                                        </div>
                                        <div class="separator">-</div>
                                        <div class="field">
                                            <span>Max</span>
                                            <input type="number" class="input-max" value="7500">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- filter by color -->
                            <div class="filter categories_filter">
                                <h3 class="is-font-20 dark-brown main_font_family">Filter by Colour</h3>
                                <div class="color_section">
                                    <div class="select">
                                        <label for="">
                                            <input type="checkbox" name="" id="">
                                            <div class="color_left">
                                                <span class="beige"></span>
                                                <p class="is-font-18">beige</p>
                                            </div>
                                            <div class="number">
                                                <p class="is-font-18">9</p>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="select">
                                        <label for="">
                                            <input type="checkbox" name="" id="">
                                            <div class="color_left">
                                                <span class="red"></span>
                                                <p class="is-font-18">red</p>
                                            </div>
                                            <div class="number">
                                                <p class="is-font-18">5</p>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="select">
                                        <label for="">
                                            <input type="checkbox" name="" id="">
                                            <div class="color_left">
                                                <span class="blue"></span>
                                                <p class="is-font-18">Blue</p>
                                            </div>
                                            <div class="number">
                                                <p class="is-font-18">8</p>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="select">
                                        <label for="">
                                            <input type="checkbox" name="" id="">
                                            <div class="color_left">
                                                <span class="green"></span>
                                                <p class="is-font-18">green</p>
                                            </div>
                                            <div class="number">
                                                <p class="is-font-18">4</p>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="select">
                                        <label for="">
                                            <input type="checkbox" name="" id="">
                                            <div class="color_left">
                                                <span class="silver"></span>
                                                <p class="is-font-18">grey</p>
                                            </div>
                                            <div class="number">
                                                <p class="is-font-18">2</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- filter by size -->
                            <!-- <div class="filter categories_filter">
                                <h3 class="is-font-20 dark-brown main_font_family">Filter by size</h3>
                                <div class="color_section">
                                    <div class="select">
                                        <label for="">
                                            <input type="checkbox" name="" id="">
                                            <h6 class="is-font-18 dark-brown">s</h6>
                                            <div class="number">
                                                <p class="is-font-18">9</p>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="select">
                                        <label for="">
                                            <input type="checkbox" name="" id="">
                                            <h6 class="is-font-18 dark-brown">m</h6>
                                            <div class="number">
                                                <p class="is-font-18">4</p>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="select">
                                        <label for="">
                                            <input type="checkbox" name="" id="">
                                            <h6 class="is-font-18 dark-brown">l</h6>
                                            <div class="number">
                                                <p class="is-font-18">5</p>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="select">
                                        <label for="">
                                            <input type="checkbox" name="" id="">
                                            <h6 class="is-font-18 dark-brown">xl</h6>
                                            <div class="number">
                                                <p class="is-font-18">4</p>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="select">
                                        <label for="">
                                            <input type="checkbox" name="" id="">
                                            <h6 class="is-font-18 dark-brown">xxl</h6>
                                            <div class="number">
                                                <p class="is-font-18">2</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div> -->
                        </form>
                    </div>
                    <div class="mob_filter">
                        <div class="filter_btn">
                            <h3 class="is-font-26 dark-brown main_font_family"><i class='bx bx-filter-alt'></i>
                                Filter
                            </h3>
                        </div>
                        <div class="filter_overlay">
                            <form action="">
                                <div class="filter_slider">
                                    <div class="categories_filter">
                                        <h3 class="is-font-20 dark-brown main_font_family">Filter by price</h3>
                                        <div class="wrapper">
                                            <div class="slider-range">
                                                <div class="progress-bar"></div>
                                            </div>
                                            <div class="input-range">
                                                <input type="range" class="range-min" min="0" max="10000" value="2500"
                                                    step="100">
                                                <input type="range" class="range-max" min="0" max="10000" value="7500"
                                                    step="100">
                                            </div>
                                            <div class="input-price">
                                                <div class="field">
                                                    <span>Min</span>
                                                    <input type="number" class="input-min" value="2500">
                                                </div>
                                                <div class="separator">-</div>
                                                <div class="field">
                                                    <span>Max</span>
                                                    <input type="number" class="input-max" value="7500">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="filter_cancel">
                                        <i class='bx bxs-x-circle'></i>
                                    </div>
                                    <!-- filter by color -->
                                    <div class="filter categories_filter">
                                        <h3 class="is-font-20 dark-brown main_font_family">Filter by Colour</h3>
                                        <div class="color_section">
                                            <div class="select">
                                                <label for="">
                                                    <input type="checkbox" name="" id="">
                                                    <div class="color_left">
                                                        <span class="beige"></span>
                                                        <p class="is-font-18">beige</p>
                                                    </div>
                                                    <div class="number">
                                                        <p class="is-font-18">9</p>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="select">
                                                <label for="">
                                                    <input type="checkbox" name="" id="">
                                                    <div class="color_left">
                                                        <span class="red"></span>
                                                        <p class="is-font-18">red</p>
                                                    </div>
                                                    <div class="number">
                                                        <p class="is-font-18">5</p>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="select">
                                                <label for="">
                                                    <input type="checkbox" name="" id="">
                                                    <div class="color_left">
                                                        <span class="blue"></span>
                                                        <p class="is-font-18">Blue</p>
                                                    </div>
                                                    <div class="number">
                                                        <p class="is-font-18">8</p>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="select">
                                                <label for="">
                                                    <input type="checkbox" name="" id="">
                                                    <div class="color_left">
                                                        <span class="green"></span>
                                                        <p class="is-font-18">green</p>
                                                    </div>
                                                    <div class="number">
                                                        <p class="is-font-18">4</p>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="select">
                                                <label for="">
                                                    <input type="checkbox" name="" id="">
                                                    <div class="color_left">
                                                        <span class="silver"></span>
                                                        <p class="is-font-18">grey</p>
                                                    </div>
                                                    <div class="number">
                                                        <p class="is-font-18">2</p>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- filter by size -->
                                    <div class="filter categories_filter">
                                        <h3 class="is-font-20 dark-brown main_font_family">Filter by size</h3>
                                        <div class="color_section">
                                            <div class="select">
                                                <label for="">
                                                    <input type="checkbox" name="" id="">
                                                    <h6 class="is-font-18 dark-brown">s</h6>
                                                    <div class="number">
                                                        <p class="is-font-18">9</p>
                                                    </div>
                                                </label>

                                            </div>
                                            <div class="select">
                                                <label for="">
                                                    <input type="checkbox" name="" id="">
                                                    <h6 class="is-font-18 dark-brown">m</h6>
                                                    <div class="number">
                                                        <p class="is-font-18">4</p>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="select">
                                                <label for="">
                                                    <input type="checkbox" name="" id="">
                                                    <h6 class="is-font-18 dark-brown">l</h6>
                                                    <div class="number">
                                                        <p class="is-font-18">5</p>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="select">
                                                <label for="">
                                                    <input type="checkbox" name="" id="">
                                                    <h6 class="is-font-18 dark-brown">xl</h6>
                                                    <div class="number">
                                                        <p class="is-font-18">4</p>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="select">
                                                <label for="">
                                                    <input type="checkbox" name="" id="">
                                                    <h6 class="is-font-18 dark-brown">xxl</h6>
                                                    <div class="number">
                                                        <p class="is-font-18">2</p>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="categories_wrapper__right">

                        <div class="row">

                            <?php

                            if (is_array($queryPost)) {


                                if (!isset($productListType)) {

                                    for ($i = 0; $i < sizeof($queryPost); $i++) {

                                        $tagId[] = $queryPost[$i]['tag_relation_id'];
                                    }

                                    $resultTags = implode(' OR link_relation_id=', $tagId);

                                    if ($queryResults = mysqli_query($connection, "SELECT link_url, product_title, product_image, inventory_currency_symbol, inventory_currency, inventory_in_stock, inventory_price, inventory_mrp, link_published FROM " . TABLE_PREFIX . "link INNER JOIN " . TABLE_PREFIX . "product ON link_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id WHERE link_site_id=" . SITE_ID . " AND link_type='product' AND link_status=200 AND inventory_in_stock=1 AND (link_relation_id={$resultTags}) ORDER BY RAND()")) {

                                        if (mysqli_num_rows($queryResults) != 0) {

                                            while ($queryResultsData = mysqli_fetch_assoc($queryResults)) {

                                                extract($queryResultsData);


                                                echo "
                                                                            <div class='col-xl-4 col-md-4 col-6'>
                                                                                <a href='{$link_url}'>
                                                                                    <div class='flower_img'>
                                                                                        <div class='img_card'>
                                                                                            <img src='{$product_image}' class='img-fluid' alt='{$product_title}'>
                                                                                        </div>
                                                                                        <div class='flower_content'>
                                                                                            <h6 class='is-font-20 dark-brown'>{$product_title}</h6>
                                                                                            <p class='dark-brown main_font_family is-font-26'>" . $inventory_currency_symbol . ' ' . (($inventory_price != 0) ? $inventory_price : $inventory_mrp) . "</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                        ";
                                            }
                                        }
                                    }
                                } else {

                                    for ($i = 0; $i < sizeof($queryPost); $i++) {

                                        extract($queryPost[$i]);

                                        echo "
                                                                            <div class='col-xl-4 col-md-4 col-6'>
                                                                                <a href='{$link_url}'>
                                                                                    <div class='flower_img'>
                                                                                        <div class='img_card'>
                                                                                            <img src='{$product_image}' class='img-fluid' alt='{$product_title}'>
                                                                                        </div>
                                                                                        <div class='flower_content'>
                                                                                            <h6 class='is-font-20 dark-brown'>{$product_title}</h6>
                                                                                            <p class='dark-brown main_font_family is-font-26'>" . $inventory_currency_symbol . ' ' . (($inventory_price != 0) ? $inventory_price : $inventory_mrp) . "</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                        ";
                                    }
                                }
                            } else {
                                echo '<div class="col-12"><h3 class="is-font-20 dark-brown main_font_family">No products found in this category.</h3></div>';
                            }

                            ?>

                        </div>
                        <!-- 
                        <div class="page">
                            <a href="#" class="page_active">1</a>
                            <a href="#">2</a>
                            <a href="#">3</a>
                            <a href="#">4</a>
                            <a href="#">...</a>
                            <a href="#">25</a>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php

require_once 'footer.php';

?>