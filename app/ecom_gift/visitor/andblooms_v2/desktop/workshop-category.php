<?php

require_once 'header.php';

?>
<main>
    <section class="categories_banner">
        <div class="categories_content">
            <h1 class="main_font_family dark-brown is-font-55 d-flex"><img src="assets/images/svg/left-arrow.svg"
                    class="img-fluid" alt=""><?php echo ucwords($category_title) ?></h1>
        </div>
    </section>

    <!-- categories details section start -->
    <section class="categories_wrapper">
        <div class="container">
            <div class="row">
                <div class="categories_wrapper__right">

                    <div class="row">

                        <?php

                        if (isset($categorySinglePost)) {
                            for ($i = 0; $i < sizeof($categorySinglePost); $i++) {

                                extract($categorySinglePost[$i]);

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
    </section>
</main>

<?php

require_once 'footer.php';

?>