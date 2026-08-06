<?php

require_once 'header.php';

?>
<section class="category_wrapper">
    <div class="container">
        <div class="category_title">
            <h6 class="text-capitalize"><?php echo $category_title ?></h6>
        </div>
        <div class="category_container">

            <?php

            if (isset($categorySinglePost)) { ?>


                <div class="row">

                    <?php

                    $j = 0;
                    for ($i = 0; $i < sizeof($categorySinglePost); $i++) {

                        extract($categorySinglePost[$i]);
                        echp(($j == 0) ? "<div class='category_row'>" : '');
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

                        if ($j == 4) {
                            echo "</div>";
                            $j = 0;
                        } else {
                            $j++;
                        }
                    }

                    ?>

                </div>
                <div class="center">
                    <div class="pagination">
                        <?php

                        $sort = ((isset($_GET['sort'])) ? '&sort=' . $_GET['sort'] : '');

                        if (PAGE_NO != 0) {
                            echo "<a href='" . BASE_URL . "?page=" . PREVIOUS_PAGE . "{$sort}'>Previous page</a>";
                        }

                        if (NEXT_PAGE <= TOTAL_PAGE) {
                            echo "<a href='" . BASE_URL . "?page=" . NEXT_PAGE . "{$sort}'>Next page &raquo;</a>";
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
    </div>
</section>

<!-- search page -->
<section class="serach_overlay">
    <div class="search_wrapper">
        <div class="container">
            <h3>search</h3>
            <div class="search_input">
                <input type="text" placeholder="What are you looking for?" name="" id="" autofocus>
                <button class="search_icon">
                    <i class='bx bx-search'></i>
                </button>
            </div>
        </div>
        <div class="search_cancel">
            <i class='bx bx-x'></i>
        </div>
    </div>
</section>
<!-- cart page -->
<section class="cart_wrapper">
    <div class="cart_content">
        <div class="cart_header">
            <h6>your cart</h6>
            <div class="cart_cancel">
                <i class='bx bx-x'></i>
            </div>
        </div>
        <div class="cart_container">
            <div class="cart_flex">
                <div class="cart_container__left">
                    <div class="cart_img">
                        <img src="assets/images/img1.jpg" class="img-fluid" alt="">
                    </div>

                </div>
                <div class="cart_container__right">
                    <div class="cart_row">
                        <div class="cart_product__name">
                            <h6>Merino Wool Polka Dot Scarf - Denim Blue</h6>
                        </div>
                        <div class="remove_item">
                            <button><i class='bx bx-trash'></i></button>
                        </div>
                    </div>
                    <div class="cart_row">
                        <div class="cart_quentity">
                            <p id="minus-btn">-</p>
                            <p id="count">1</p>
                            <p id="plus-btn">+</p>
                        </div>
                        <div class="cart_price">
                            <p>₹599</p>
                        </div>
                    </div>

                </div>
            </div>
            <div class="cart_flex">
                <div class="cart_container__left">
                    <div class="cart_img">
                        <img src="assets/images/img2.jpg" class="img-fluid" alt="">
                    </div>

                </div>
                <div class="cart_container__right">
                    <div class="cart_row">
                        <div class="cart_product__name">
                            <h6>Merino Wool Polka Dot Scarf - Denim Blue</h6>
                        </div>
                        <div class="remove_item">
                            <button><i class='bx bx-trash'></i></button>
                        </div>
                    </div>
                    <div class="cart_row">
                        <div class="cart_quentity">
                            <p id="minus-btn2">-</p>
                            <p id="count2">1</p>
                            <p id="plus-btn2">+</p>
                        </div>
                        <div class="cart_price">
                            <p>₹599</p>
                        </div>
                    </div>

                </div>
            </div>

            <div class="total_container">
                <div class="total_flex">
                    <h6>subtotal:</h6>
                    <p>₹599</p>
                </div>
                <div class="total_flex">
                    <h6>total:</h6>
                    <p><strong>₹599</strong></p>
                </div>
                <div class="checkout">
                    <a href="checkout.html">
                        <button>checkout</button>
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>