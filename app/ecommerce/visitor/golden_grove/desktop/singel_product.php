<?php
        
    require_once 'header.php';

?>
<section class="singel_wrapper">
    <div class="container">
        <div class="singel_slider">
            <div class="product_img">
                <img src="assets/images/img1.jpg" class="img-fluid" alt="">
            </div>
            <div class="product_img">
                <img src="assets/images/img2.jpg" class="img-fluid" alt="">
            </div>
            <div class="product_img">
                <img src="assets/images/img3.jpg" class="img-fluid" alt="">
            </div>
            <div class="product_img">
                <img src="assets/images/img1.jpg" class="img-fluid" alt="">
            </div>
            <div class="product_img">
                <img src="assets/images/img2.jpg" class="img-fluid" alt="">
            </div>
            <div class="product_img">
                <img src="assets/images/img3.jpg" class="img-fluid" alt="">
            </div>
        </div>
        <div class="singel_slider__content">
            <h6>Floral Printed Pashmina Stole – Ochre Blue</h6>
            <div class="product_price">
                <p>MRP (Inc. tax) <span>₹ 7,952</span></p>
            </div>
            <div class="product_details">
                <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Sapiente reprehenderit deserunt
                    sequi qui sunt beatae molestias molestiae perferendis harum consectetur.</p>
                <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Id temporibus dolorum, ullam sequi
                    vel fugiat fugit tempora officia voluptatibus eius!</p>
            </div>


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

<!-- related product -->
<section class="related_wrapper">
    <div class="container">
        <div class="related_product">
            <div class="related_product__img">
                <img src="assets/images/rel-prod.jpg" class="img-fluid" alt="">
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
</section>

<section class="faq_wrapper">
    <div class="container">

        <div class="faq_heading">
            <h3>Frequently Asked Questions</h3>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta, aspernatur delectus est
                nesciunt quasi laudantium repellat ratione corrupti perspiciatis temporibus?</p>
        </div>
        <div class="faq_wrapper__content">
            <div class="faq">
                <div class="question footer_heading">
                    <h3>What is HTML?</h3>
                    <i class='bx bx-chevron-down'></i>
                </div>
                <div class="answer footer_content">
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Tempore architecto quia cumque
                        quos non odit, cum quasi debitis molestias, eveniet, ab minima.</p>
                </div>
            </div>
            <div class="faq">
                <div class="question footer_heading">
                    <h3>What is CSS?</h3>
                    <i class='bx bx-chevron-down'></i>
                </div>
                <div class="answer footer_content">
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Tempore architecto quia cumque
                        quos non odit, cum quasi debitis molestias, eveniet, ab minima.</p>
                </div>
            </div>
            <div class="faq">
                <div class="question footer_heading">
                    <h3>What is JavaScript?</h3>
                    <i class='bx bx-chevron-down'></i>
                </div>
                <div class="answer footer_content">
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Tempore architecto quia cumque
                        quos non odit, cum quasi debitis molestias, eveniet, ab minima.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
        
    require_once 'footer.php';

?>