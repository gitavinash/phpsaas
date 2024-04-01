<?php

    require_once 'header.php';

?>
<section class="category_wrapper">
    <div class="container">
        <div class="category_title">
            <h6 class="text-capitalize">our category</h6>
        </div>
        <div class="category_container">
            <?php
            
                if($checkCategory=mysqli_query($connection, "SELECT category_title, category_image, category_thumbnail, link_url FROM ".TABLE_PREFIX."category INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=category_id WHERE link_site_id=".SITE_ID." AND link_type='category' AND link_status=200 ORDER BY link_is_featured, link_modified DESC")) {
                    if(mysqli_num_rows($checkCategory)!=0) {
                        $i=0;
                        while($categoryData=mysqli_fetch_assoc($checkCategory)) {
                            echo (($i==0) ? '<div class="category_row">' : '');
                            extract($categoryData);
                            echo "<div class='category_box'>
                                <a href='{$link_url}'><img src='{$category_image}' class='img-fluid' alt='{$category_title}'></a>
                                <p>{$category_title}</p>
                            </div>";
                            $i++;
                            if($i==4){
                                echo '</div>';
                                $i=0;
                            }
                        }
                    }
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