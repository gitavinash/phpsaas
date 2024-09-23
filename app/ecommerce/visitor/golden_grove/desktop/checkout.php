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
                    <div class="checkout__steps__line--progress"></div>
                </div>
                <li class="completed">
                    <span><i class='bx bx-check'></i></span>Cart
                </li>
                <li class="current">
                    <span>2</span>Checkout
                </li>
                <li>
                    <span>3</span>Payment
                </li>
            </ul>
        </div>
    </div>
</section>

<!-- information -->
<section class="contact_information">
    <div class="container">
        <form action="">
            <div class="contact_heading">
                <h6>shipping address</h6>
                <div class="contact_input">
                    <div class="input_flex">
                        <input type="text" placeholder="First Name">
                        <input type="text" placeholder="Last Name">
                    </div>
                    <div class="input_flex">
                        <input type="number" onwheel="this.blur()" placeholder="Postcode / ZIP">
                        <input type="text" placeholder="Street Address">
                    </div>
                    <div class="input_flex">
                        <input type="text" placeholder="Apartment">
                        <input type="text" placeholder="Town / City">
                    </div>
                    <div class="input_flex">
                        <select class="form-select" aria-label="Default select example">
                            <option selected>India</option>
                            <option value="1">One</option>
                            <option value="2">Two</option>
                            <option value="3">Three</option>
                        </select>
                        <select class="form-select" aria-label="Default select example">
                            <option selected>State</option>
                            <option value="1">One</option>
                            <option value="2">Two</option>
                            <option value="3">Three</option>
                        </select>
                    </div>
                    <div class="input_flex">
                        <input type="email" placeholder="Email">
                        <input type="number" onwheel="this.blur()" placeholder="Mobile Number">
                    </div>
                </div>

                <div class="diff_add">
                    <div class="diff_add_flex">
                        <input type="checkbox" data-bs-toggle="collapse" href="#collapseExample" role="button"
                            aria-expanded="false" aria-controls="collapseExample">
                        <p class="is-font-18 grey-shade">Is billing address different?</p>
                    </div>
                    <div class="collapse_input collapse" id="collapseExample">
                        <div class="contact_input">
                            <div class="input_flex">
                                <input type="text" placeholder="First Name">
                                <input type="text" placeholder="Last Name">
                            </div>
                            <div class="input_flex">
                                <input type="number" onwheel="this.blur()" placeholder="Postcode / ZIP">
                                <input type="text" placeholder="Street Address">
                            </div>
                            <div class="input_flex">
                                <input type="text" placeholder="Apartment">
                                <input type="text" placeholder="Town / City">
                            </div>
                            <div class="input_flex">
                                <select class="form-select" aria-label="Default select example">
                                    <option selected>India</option>
                                    <option value="1">One</option>
                                    <option value="2">Two</option>
                                    <option value="3">Three</option>
                                </select>
                                <select class="form-select" aria-label="Default select example">
                                    <option selected>State</option>
                                    <option value="1">One</option>
                                    <option value="2">Two</option>
                                    <option value="3">Three</option>
                                </select>
                            </div>
                            <div class="input_flex">
                                <input type="email" placeholder="Email">
                                <input type="number" onwheel="this.blur()" placeholder="Mobile Number">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="notes">
                    <label for="">Order Notes (optional)</label>
                    <span><textarea name="" id="" cols="30" rows="2"
                            placeholder="Notes about your order, e.g. special notes for delivery."></textarea></span>
                </div>



            </div>




    </div>
    </form>
    </div>
</section>
<?php

require_once 'footer.php';

?>