<?php

    require_once 'header.php';

?>
<section class="banner_wrapper">
    <div class="banner_slider">

        <?php
       
            if($desktopSlider=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."slide WHERE slide_status=1 AND slide_site_id=".SITE_ID)) {
                $numberOfSlider=mysqli_num_rows($desktopSlider);
                if($numberOfSlider!=0) {
                    while($desktopSliderData=mysqli_fetch_assoc($desktopSlider)) {
                        extract($desktopSliderData);
                        echo "<div class='banner_img'>
                            <img src='{$slide_image}' class='img-fluid' alt='{$slide_title}'>
                        </div>";
                    }
                }
            }

        ?>
    </div>
</section>

<!-- search page -->
<section class="serach_overlay">
    <div class="search_wrapper">
        <div class="container">
            <h3>Search</h3>
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

<!-- category section -->
<section class="category_wrapper vertical_padding">
    <div class="container">
        <div class="main_heading">
            <h3>Category</h3>
        </div>
    </div>
    <div class="content">
        <div class="container">
            <div class="category_card__slider">

                <?php
            
                if($checkCategory=mysqli_query($connection, "SELECT category_title, category_image, category_thumbnail, link_url FROM ".TABLE_PREFIX."category INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=category_id WHERE link_site_id=".SITE_ID." AND link_type='category' AND link_status=200 ORDER BY link_is_featured, link_modified DESC LIMIT 0, 5")) {
                 if(mysqli_num_rows($checkCategory)!=0) {
                    while($categoryData=mysqli_fetch_assoc($checkCategory)) {
                        extract($categoryData);
                        echo "<div class='category_card'>
                            <a href='{$link_url}' title='{$category_title}' >
                                <div class='category_img'>
                                    <img src='{$category_image}' class='img-fluid' alt='{$category_title}'>
                                </div>
                            </a>
                        </div>";
                    }
                 }
                }

            ?>
            </div>
        </div>
    </div>

</section>

<!-- about us -->
<section class="about_wrapper vertical_padding">
    <div class="container">
        <div class="main_heading">
            <h3>about us</h3>
        </div>
    </div>
    <div class="content">
        <div class="container">
            <div class="about_parra">
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ratione maxime libero placeat odit
                    consequatur exercitationem quam quae iste alias, unde vero reiciendis veritatis officiis
                    animi
                    perspiciatis, corrupti, doloribus aut? Eos! Lorem ipsum dolor sit amet consectetur
                    adipisicing elit. Facere illo nisi molestias numquam architecto delectus alias unde aperiam
                    cum nemo, mollitia veritatis voluptas corrupti voluptatem modi laudantium expedita incidunt.
                    Eius architecto amet veniam quidem, vitae aliquam fugit enim debitis quisquam.</p>
            </div>
        </div>
    </div>
</section>

<!-- feature section -->
<section class="category_wrapper vertical_padding">
    <div class="container">
        <div class="main_heading">
            <h3>Feature products</h3>
        </div>
    </div>
    <div class="content">
        <div class="container">
            <div class="category_card__slider">

                <?php
            
                if($checkCategory=mysqli_query($connection, "SELECT product_title, product_image, product_thumbnail, inventory_currency_symbol, inventory_mrp, inventory_price, link_url, link_is_featured FROM ".TABLE_PREFIX."product INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=product_id INNER JOIN ".TABLE_PREFIX."inventory ON inventory_relation_id=product_id WHERE link_site_id=".SITE_ID." AND link_type='product' AND link_status=200 AND inventory_in_stock=1 ORDER BY link_relation_id, link_modified DESC LIMIT 0, 5;")) {
                 if(mysqli_num_rows($checkCategory)!=0) {
                    while($categoryData=mysqli_fetch_assoc($checkCategory)) {
                        extract($categoryData);
                        echo "<div class='category_card'>
                            <a href='{$link_url}' title='{$category_title}' >
                                <div class='category_img'>
                                    <img src='{$product_image}' class='img-fluid' alt='{$category_title}'>
                                </div>
                                <div class='category_content'>
                                    <h6>Ari Gulbahar Pure Wool Vintage Embroidery Shawl – Sukoon Black</h6>
                                    <span class='price'>{$inventory_currency_symbol} {$inventory_price}</span>
                                </div>
                            </a>
                        </div>";
                    }
                 }
                }
                
                ?>
            </div>
        </div>
    </div>

</section>

<!-- how we design us -->
<section class="design vertical_padding">
    <div class="container">
        <div class="main_heading">
            <h3>how we design</h3>
        </div>
    </div>
    <div class="content">
        <div class="container">
            <div class="about_parra">
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ratione maxime libero placeat odit
                    consequatur exercitationem quam quae iste alias, unde vero reiciendis veritatis officiis
                    animi
                    perspiciatis, corrupti, doloribus aut? Eos! Lorem ipsum dolor sit amet consectetur
                    adipisicing elit. Facere illo nisi molestias numquam architecto delectus alias unde aperiam
                    cum nemo, mollitia veritatis voluptas corrupti voluptatem modi laudantium expedita incidunt.
                    Eius architecto amet veniam quidem, vitae aliquam fugit enim debitis quisquam.</p>
            </div>
        </div>
    </div>
</section>

<!-- best seller section -->
<!-- <section class="best_seller vertical_padding">
    <div class="container">
        <div class="main_heading">
            <h3>Best seller</h3>
        </div>
    </div>
    <div class="content">
        <div class="container">
            <div class="category_card__slider">
                <div class="category_card">
                    <div class="category_img">
                        <img src="assets/images/img1.jpg" class="img-fluid" alt="">
                        <div class="add_cart">
                            <a href="#">add cart</a>
                        </div>
                    </div>
                    <div class="category_content">
                        <h6>Ari Gulbahar Pure Wool Vintage Embroidery Shawl – Sukoon Black</h6>
                        <span class="price">₹ 9,999</span>
                    </div>
                </div>
                <div class="category_card">
                    <div class="category_img">
                        <img src="assets/images/img2.jpg" class="img-fluid" alt="">
                        <div class="add_cart">
                            <a href="#">add cart</a>
                        </div>
                    </div>
                    <div class="category_content">
                        <h6>Ari Gulbahar Pure Wool Vintage Embroidery Shawl – Sukoon Black</h6>
                        <span class="price">₹ 9,999</span>
                    </div>
                </div>
                <div class="category_card">
                    <div class="category_img">
                        <img src="assets/images/img3.jpg" class="img-fluid" alt="">
                        <div class="add_cart">
                            <a href="#">add cart</a>
                        </div>
                    </div>
                    <div class="category_content">
                        <h6>Ari Gulbahar Pure Wool Vintage Embroidery Shawl – Sukoon Black</h6>
                        <span class="price">₹ 9,999</span>
                    </div>
                </div>
                <div class="category_card">
                    <div class="category_img">
                        <img src="assets/images/img1.jpg" class="img-fluid" alt="">
                        <div class="add_cart">
                            <a href="#">add cart</a>
                        </div>
                    </div>
                    <div class="category_content">
                        <h6>Ari Gulbahar Pure Wool Vintage Embroidery Shawl – Sukoon Black</h6>
                        <span class="price">₹ 9,999</span>
                    </div>
                </div>
                <div class="category_card">
                    <div class="category_img">
                        <img src="assets/images/img2.jpg" class="img-fluid" alt="">
                        <div class="add_cart">
                            <a href="#">add cart</a>
                        </div>
                    </div>
                    <div class="category_content">
                        <h6>Ari Gulbahar Pure Wool Vintage Embroidery Shawl – Sukoon Black</h6>
                        <span class="price">₹ 9,999</span>
                    </div>
                </div>
                <div class="category_card">
                    <div class="category_img">
                        <img src="assets/images/img3.jpg" class="img-fluid" alt="">
                        <div class="add_cart">
                            <a href="#">add cart</a>
                        </div>
                    </div>
                    <div class="category_content">
                        <h6>Ari Gulbahar Pure Wool Vintage Embroidery Shawl – Sukoon Black</h6>
                        <span class="price">₹ 9,999</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>

<section class="banner_wrapper">
    <div class="container">
        <div class="main_heading">
            <h3>Gifts for Your Special one</h3>
        </div>
    </div>
    <div class="content">
        <div class="container">
            <div class="special_slider">
                <div class="special_img">
                    <img src="assets/images/For-her.jpg" class="img-fluid" alt="">
                </div>
                <div class="special_img">
                    <img src="assets/images/For-Unisex.jpg" class="img-fluid" alt="">
                </div>
                <div class="special_img">
                    <img src="assets/images/For-Him.jpg" class="img-fluid" alt="">
                </div>
            </div>
        </div>
    </div>

</section> -->
<?php

    require_once 'footer.php';

?>