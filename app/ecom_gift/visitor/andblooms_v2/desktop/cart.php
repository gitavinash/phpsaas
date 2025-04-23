<?php

require_once 'header.php';

?>
<main>
    <section class="cart_container">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="./index.html">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Library</a></li>
                    <li class="breadcrumb-item"><a href="./singeldetails.html">Jet set tulips</a></li>
                    <li class="breadcrumb-item active text-capitalize" aria-current="page">your cart</li>
                </ol>
            </nav>

            <div class="cart_content">
                <div class="row">
                    <div class="col-xl-8">
                        <div class="cart_content__left">
                            <div class="row">
                                <div class="col-12">
                                    <div class="row cart_head">
                                        <div class="col-3">
                                            <h3 class="main_font_family is-font-18 black-color text-capitalize">
                                                Product
                                            </h3>
                                        </div>
                                        <div class="col-3">
                                            <h3 class="main_font_family is-font-18 black-color text-capitalize">
                                                Price
                                            </h3>
                                        </div>
                                        <div class="col-3">
                                            <h3 class="main_font_family is-font-18 black-color text-capitalize">
                                                quantity
                                            </h3>
                                        </div>
                                        <div class="col-3">
                                            <h3 class="main_font_family is-font-18 black-color text-capitalize">Sub
                                                total
                                            </h3>
                                        </div>

                                    </div>
                                </div>
                                <div class="cart_wrapper">
                                    <div class="cart_types">
                                        <div class="row">
                                            <div class="col-3">
                                                <div class="product">
                                                    <img src="assets/images/svg/flowerimg.svg" alt="">
                                                    <p class="is-font-18 black-color text-truncate">Jet Set Tulips
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="product margin">
                                                    <p class="silver-shade is-font-18">Rs 3000.00</p>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="cart_quantity d-flex margin product">
                                                    <p id="minus-btn" value="-">-</p>
                                                    <p id="count" value="1">1</p>
                                                    <p id="plus-btn" value="+">+</p>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="product margin">
                                                    <p class="is-font-18 dark-brown">Rs 6000.00</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cart_types">
                                        <div class="row">
                                            <div class="col-3">
                                                <div class="product">
                                                    <img src="assets/images/svg/flowerimg.svg" alt="">
                                                    <p class="is-font-18 black-color text-truncate">Jet Set Tulips
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="product margin">
                                                    <p class="silver-shade is-font-18">Rs 3000.00</p>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="cart_quantity d-flex margin product">
                                                    <p id="minus-btn">-</p>
                                                    <p id="count">1</p>
                                                    <p id="plus-btn">+</p>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="product margin">
                                                    <p class="is-font-18 dark-brown">Rs 6000.00</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cart_types">
                                        <div class="row">
                                            <div class="col-3">
                                                <div class="product">
                                                    <img src="assets/images/svg/flowerimg.svg" alt="">
                                                    <p class="is-font-18 black-color text-truncate">Jet Set Tulips
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="product margin">
                                                    <p class="silver-shade is-font-18">Rs 3000.00</p>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="cart_quantity d-flex margin product">
                                                    <p id="minus-btn2">-</p>
                                                    <p id="count2">1</p>
                                                    <p id="plus-btn2">+</p>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="product margin">
                                                    <p class="is-font-18 dark-brown">Rs 6000.00</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="coupon_flex">
                                <div class="coupon_code">
                                    <input type="text" placeholder="Coupon code">
                                </div>
                                <div class="apply_coupon">
                                    <button>Apply coupon</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="cart_content__right">
                            <div class="cart_total">
                                <h3 class="is-font-18 black-color main_font_family text-capitalize">Cart total</h3>
                                <div class="cart_total__price">
                                    <div class="total">
                                        <h6 class="grey-shade text-capitalize is-font-18">Sub total</h6>
                                        <p class="grey-color text-capitalize is-font-18">Rs 11000.00</p>
                                    </div>
                                    <div class="total">
                                        <h6 class="grey-shade text-capitalize is-font-18">Shipping</h6>
                                        <p class="grey-color text-capitalize is-font-18">Free shipping</p>
                                    </div>
                                    <div class="total">
                                        <h6 class="dark-brown text-capitalize is-font-18"> total</h6>
                                        <p class="dark-brown text-capitalize is-font-20 main_font_family">Rs
                                            11000.00
                                        </p>
                                    </div>
                                    <div class="checkout">
                                        <a href="./delivery.html" class="is-font-18">Proced to checkout</a>
                                    </div>
                                    <div class="continue">
                                        <a href="./categoriesdetails.html"><img src="assets/images/svg/left.svg"
                                                class="img-fluid" alt="">Countinue
                                            to
                                            shopping</a>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php

require_once 'footer.php';

?>