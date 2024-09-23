<?php

require_once 'header.php';

?>
<section class="banner_wrapper">
    <div class="banner_slider">
        <div class="banner_img">
            <img src="assets/images/banner1.jpg" class="img-fluid" alt="">
        </div>
        <div class="banner_img">
            <img src="assets/images/KCS-Pashmina-Shawls-Banner-mobile.jpg" class="img-fluid" alt="">
        </div>
        <div class="banner_img">
            <img src="assets/images/banner.jpg" class="img-fluid" alt="">
        </div>
    </div>
</section>

<!-- search page -->
<section class="serach_overlay">
    <div class="search_wrapper">
        <div class="container">
            <h3>search</h3>
            <div class="search_input">
                <input type="text" placeholder="What are you looking for?" name="xyz" id="name" autofocus>
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
<div class="product_wrapper">
    <div class="container">
        <div class="product_head">
            <div class="left_side">
                <h6>kalamkari <span>(16)</span></h6>
            </div>
            <div class="product_right">
                <p class="myFilter"><i class='bx bx-sort-alt-2'></i>filter</p>
                <p><i class='bx bxs-filter-alt'></i>sort</p>
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
                                    <input type="range" class="range-min" min="0" max="10000" value="2500" step="100">
                                    <input type="range" class="range-max" min="0" max="10000" value="7500" step="100">
                                </div>
                                <div class="input-price">
                                    <div class="field">
                                        <span>Min</span>
                                        <input type="number" onwheel="this.blur()" class="input-min" value="2500">
                                    </div>
                                    <!-- <div class="separator">-</div> -->
                                    <div class="field">
                                        <span class="max">Max</span>
                                        <input type="number" onwheel="this.blur()" class="input-max" value="7500">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="filter_cancel">
                            <i class='bx bxs-x-circle'></i>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <div class="product_container">
            <div class="product_flex">

                <div class="category_card product_card">
                    <a href="singel_product.html">
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
                    </a>
                </div>

                <div class="category_card product_card">
                    <a href="singel_product.html">
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
                    </a>
                </div>
                <div class="category_card product_card hide_card">
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
                <div class="category_card product_card hide_card">
                    <div class="category_img">
                        <img src="assets/images/img4.jpg" class="img-fluid" alt="">
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
            <div class="product_flex">
                <div class="category_card product_card">
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
                <div class="category_card product_card">
                    <div class="category_img">
                        <img src="assets/images/img4.jpg" class="img-fluid" alt="">
                        <div class="add_cart">
                            <a href="#">add cart</a>
                        </div>
                    </div>
                    <div class="category_content">
                        <h6>Ari Gulbahar Pure Wool Vintage Embroidery Shawl – Sukoon Black</h6>
                        <span class="price">₹ 9,999</span>
                    </div>
                </div>
                <div class="category_card product_card hide_card">
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
                <div class="category_card product_card hide_card">
                    <div class="category_img">
                        <img src="assets/images/img4.jpg" class="img-fluid" alt="">
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
            <div class="product_flex">
                <div class="category_card product_card">
                    <div class="category_img">
                        <img src="assets/images/img5.jpg" class="img-fluid" alt="">
                        <div class="add_cart">
                            <a href="#">add cart</a>
                        </div>
                    </div>
                    <div class="category_content">
                        <h6>Ari Gulbahar Pure Wool Vintage Embroidery Shawl – Sukoon Black</h6>
                        <span class="price">₹ 9,999</span>
                    </div>
                </div>
                <div class="category_card product_card">
                    <div class="category_img">
                        <img src="assets/images/img6.jpg" class="img-fluid" alt="">
                        <div class="add_cart">
                            <a href="#">add cart</a>
                        </div>
                    </div>
                    <div class="category_content">
                        <h6>Ari Gulbahar Pure Wool Vintage Embroidery Shawl – Sukoon Black</h6>
                        <span class="price">₹ 9,999</span>
                    </div>
                </div>
                <div class="category_card product_card hide_card">
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
                <div class="category_card product_card hide_card">
                    <div class="category_img">
                        <img src="assets/images/img4.jpg" class="img-fluid" alt="">
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
            <div class="product_flex">
                <div class="category_card product_card">
                    <div class="category_img">
                        <img src="assets/images/img7.jpg" class="img-fluid" alt="">
                        <div class="add_cart">
                            <a href="#">add cart</a>
                        </div>
                    </div>
                    <div class="category_content">
                        <h6>Ari Gulbahar Pure Wool Vintage Embroidery Shawl – Sukoon Black</h6>
                        <span class="price">₹ 9,999</span>
                    </div>
                </div>
                <div class="category_card product_card">
                    <div class="category_img">
                        <img src="assets/images/img8.jpg" class="img-fluid" alt="">
                        <div class="add_cart">
                            <a href="#">add cart</a>
                        </div>
                    </div>
                    <div class="category_content">
                        <h6>Ari Gulbahar Pure Wool Vintage Embroidery Shawl – Sukoon Black</h6>
                        <span class="price">₹ 9,999</span>
                    </div>
                </div>
                <div class="category_card product_card hide_card">
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
                <div class="category_card product_card hide_card">
                    <div class="category_img">
                        <img src="assets/images/img4.jpg" class="img-fluid" alt="">
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

</div>
<?php

require_once 'footer.php';

?>