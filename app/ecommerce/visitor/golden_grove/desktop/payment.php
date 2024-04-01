<?php
        
    require_once 'header.php';

?>
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

<!-- checkout -->
<section class="checkout_container">
    <div class="container">
        <div class="checkout__step">
            <ul>
                <div class="checkout__steps__line">
                    <div class="checkout__steps__line--progress checkout_progress"></div>
                </div>
                <li class="completed">
                    <span><i class='bx bx-check'></i></span>Cart
                </li>
                <li class="current">
                    <span>2</span>Checkout
                </li>
                <li class="current">
                    <span>3</span>Payment
                </li>
            </ul>
        </div>
    </div>
</section>
<section class="payment_wrapper">
    <div class="container">

        <div class="payment_heading">
            <form action="">
                <!-- order -->
                <div class="order">
                    <div class="oder_heading">
                        <h6>your order</h6>
                    </div>
                    <div class="order_content">
                        <div class="order_content__left">
                            <div class="order_img">
                                <img src="assets/images/img1.jpg" class="img-fluid" alt="">
                            </div>
                            <div class="order_name">
                                <p>Handloom Pure Pashmina Tehreerkar Stole - Black, Fawn <span>qty: 1</span></p>
                            </div>
                        </div>
                        <div class="order_content__right">
                            <p>₹ 7,499</p>
                        </div>
                    </div>

                    <div class="order_price">
                        <div class="total_flex">
                            <h6>subtotal: <span>GST : ₹ 499</span></h6>
                            <p>₹ 7,998</p>
                        </div>
                        <div class="total_flex">
                            <h6>total:</h6>
                            <p><strong>₹ 7,998</strong></p>
                        </div>
                    </div>

                </div>
                <div class="payment_wrapper__content">
                    <h6>select payment</h6>
                    <div class="payment_content">
                        <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1"
                            checked>
                        <label class="form-check-label" for="flexRadioDefault1">
                            Cash on delivery
                        </label>
                    </div>
                    <div class="payment_content">
                        <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2">
                        <label class="form-check-label" for="flexRadioDefault2">Online Payment
                        </label>
                    </div>
                </div>
            </form>
        </div>


    </div>
</section>
<?php
        
    require_once 'footer.php';

?>