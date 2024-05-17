<?php

include_once 'header.php';

?>


<!-- section global platform start -->
<section class="banner-section banner-bg">
    <div class="overlay-bner"></div>
    <div class="container">
        <div class="row ">
            <div class="col-md-12">
                <div class="bg-content">
                    <h1><?php echo $category_title ?></h1>
                    <h3><?php echo $category_description ?></h3>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- section global platform end -->

<section class="section-content">

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-12">
                <div class="brdcrum">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo ACTIVE_URL ?>">Category</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo $category_title ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="col-md-4 col-12">
                <div class="float-right">
                    <select name="sort" class="form-select sorting">
                        <option value="#">Sort by</option>
                        <option value="latest" <?php echo ((@$_GET['sort'] == 'latest') ? 'selected' : '') ?>>Latest
                        </option>
                        <option value="price_low" <?php echo ((@$_GET['sort'] == 'price_low') ? 'selected' : '') ?>>
                            Price
                            low to high</option>
                        <option value="price_high" <?php echo ((@$_GET['sort'] == 'price_high') ? 'selected' : '') ?>>
                            Price high to low</option>
                    </select>
                </div>
            </div>
        </div>
        <?php

        if (isset($categorySinglePost)) { ?>


            <div class="row">

                <?php

                for ($i = 0; $i < sizeof($categorySinglePost); $i++) {

                    extract($categorySinglePost[$i]);

                    echo "<div class='col-md-3 col-6'>
                                    <a href='{$link_url}'>
                                        <div class='col-card-3'>
                                            <div class='event-card'>
                                                <div class='img-cd'>
                                                    <img src='{$product_image}'>
                                                </div>
                                                " . (($inventory_in_stock == 0) ? "<div class='out-of-stock' ><span>Out of Stock</span></div>" : "") . "
                                                <h4>{$product_title}</h4>
                                                <p class='rate'>" . $inventory_currency_symbol . ' ' . (($inventory_price != 0) ? $inventory_price : $inventory_mrp) . "</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>";
                }

                ?>

            </div>
            <div class="center">
                <div class="pagination">
                    <?php

                    $sort = ((isset($_GET['sort'])) ? '&sort=' . $_GET['sort'] : '');

                    if (PAGE_NO != 0) {
                        echo "<a href='" . BASE_URL . "?pg=" . PREVIOUS_PAGE . "{$sort}'>Previous page</a>";
                    }

                    if (NEXT_PAGE <= TOTAL_PAGE) {
                        echo "<a href='" . BASE_URL . "?pg=" . NEXT_PAGE . "{$sort}'>Next page &raquo;</a>";
                    }

                    ?>
                </div>
            </div>

        <?php
        } else {

            echo "<section class='continue-shopping'>
    <h1><span>No product found</span></h1>
    <a href='/' class='btn btn-success'>Go to homepage</a>
</section>";
        }

        ?>
    </div>
</section>

<?php

include_once 'footer.php';

?>