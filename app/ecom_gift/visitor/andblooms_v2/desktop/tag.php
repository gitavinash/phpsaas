<?php

require_once 'header.php';

?>
<main>
    <section class="categories_banner">
        <div class="categories_content">
            <h1 class="main_font_family dark-brown is-font-55 d-flex">
                <?php echo ucwords($tag_title) ?>
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
                            if (!empty($tagPost)) {

                                // Extract tag_relation_ids safely
                                $tagIds = array_column($tagPost, 'tag_relation_id');

                                // Sanitize integers
                                $tagIds = array_filter($tagIds, 'is_numeric');

                                if (!empty($tagIds)) {
                                    // Sort handling
                                    $sortBy = 'l.link_published DESC';

                                    if (isset($_GET['sort'])) {
                                        switch ($_GET['sort']) {
                                            case 'price_low':
                                                $sortBy = 'i.inventory_price ASC';
                                                break;
                                            case 'price_high':
                                                $sortBy = 'i.inventory_price DESC';
                                                break;
                                        }
                                    }

                                    // Prepare query
                                    $inClause = implode(',', $tagIds);
                                    $limit = PER_PAGE_LIMIT;

                                    $query = "
            SELECT 
                l.link_url, 
                p.product_title, 
                p.product_image, 
                i.inventory_currency, 
                i.inventory_currency_symbol, 
                i.inventory_price, 
                i.inventory_mrp 
            FROM " . TABLE_PREFIX . "link l
            INNER JOIN " . TABLE_PREFIX . "product p ON l.link_relation_id = p.product_id
            INNER JOIN " . TABLE_PREFIX . "inventory i ON i.inventory_relation_id = p.product_id
            WHERE l.link_relation_id IN ($inClause)
              AND l.link_site_id = " . (int)SITE_ID . "
              AND l.link_type = 'product'
              AND l.link_status = 200
            GROUP BY l.link_url
            ORDER BY $sortBy
            LIMIT 0, $limit
        ";

                                    // Execute and render
                                    if ($tagResults = mysqli_query($connection, $query)) {
                                        if (mysqli_num_rows($tagResults) > 0) {
                                            while ($row = mysqli_fetch_assoc($tagResults)) {
                                                $price = ($row['inventory_price'] != 0) ? $row['inventory_price'] : $row['inventory_mrp'];
                                                echo "
                        <div class='col-xl-4 col-md-4 col-6'>
                            <a href='{$row['link_url']}'>
                                <div class='flower_img'>
                                    <div class='img_card'>
                                        <img src='{$row['product_image']}' class='img-fluid' alt='{$row['product_title']}'>
                                    </div>
                                    <div class='flower_content'>
                                        <h6 class='is-font-20 dark-brown'>{$row['product_title']}</h6>
                                        <p class='dark-brown main_font_family is-font-26'>{$row['inventory_currency_symbol']} {$price}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    ";
                                            }
                                        } else {
                                            echo "<div class='col-12'><h3 class='is-font-20 dark-brown main_font_family'>No products found in this category.</h3></div>";
                                        }
                                    } else {
                                        echo "<div class='col-12'><h3 class='is-font-20 dark-brown main_font_family'>Query failed: " . mysqli_error($connection) . "</h3></div>";
                                    }
                                } else {
                                    echo "<div class='col-12'><h3 class='is-font-20 dark-brown main_font_family'>Invalid tag IDs provided.</h3></div>";
                                }
                            } else {
                                echo "<div class='col-12'><h3 class='is-font-20 dark-brown main_font_family'>No tags selected.</h3></div>";
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