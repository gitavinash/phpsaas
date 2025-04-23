<?php

require_once 'header.php';

?>
<main>
    <section class="delivery_wrapper">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="./index.html">Home</a></li>
                    <li class="breadcrumb-item"><a href="./payment.html">Library</a></li>
                    <li class="breadcrumb-item"><a href="./singeldetails.html">Jet set tulips</a></li>
                    <li class="breadcrumb-item"><a href="./cart.html">your cart</a></li>
                    <li class="breadcrumb-item active text-capitalize" aria-current="page">checkout</li>
                </ol>
            </nav>
        </div>
        <div class="delivery_wrapper__content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7">
                        <div class="delivery_form">
                            <h3 class="is-font-26 dark-brown main_font_family">Payment</h3>
                            <div class="payment_wrapper">
                                <div class="diff_add_flex payment_label">
                                    <input type="radio" class="form-check-input" data-bs-toggle="collapse"
                                        href="#collapseExample" role="button" aria-expanded="false"
                                        aria-controls="collapseExample" name="flexRadioDefault"
                                        id="flexRadioDefault1 direct_bank" checked="checked">
                                    <label class="is-font-18 grey-shade text-capitalize form-check-label"
                                        for="flexRadioDefault1 direct_bank">Direct bank transfer</label>
                                </div>
                                <div class="payment_paraa collapse show" id="collapseExample">
                                    <p class="is-font-18">Make your payment directly into our bank account. Please
                                        use
                                        your Order ID as the
                                        payment reference. Your order will not be shipped until the funds have
                                        cleared
                                        in our account.</p>
                                </div>
                                <div class="diff_add_flex payment_label">
                                    <input type="radio" class="form-check-input" data-bs-toggle="collapse"
                                        href="#collapseExample2" role="button" aria-expanded="false"
                                        aria-controls="collapseExample" name="flexRadioDefault"
                                        id="flexRadioDefault2 check_payment">
                                    <label class="is-font-18 grey-shade text-capitalize form-check-label"
                                        for="flexRadioDefault2 check_payment">Check
                                        payment</label>
                                </div>

                                <div class="diff_add_flex payment_label">
                                    <input type="radio" class="form-check-input" data-bs-toggle="collapse"
                                        href="#collapseExample3" role="button" aria-expanded="false"
                                        aria-controls="collapseExample" name="flexRadioDefault"
                                        id="flexRadioDefault3 cod">
                                    <label class="is-font-18 grey-shade text-capitalize form-check-label"
                                        for="flexRadioDefault3 cod">Cash on
                                        delivery</label>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="delivery_right">
                            <div class="delivery_right__top">
                                <div class="order_img">
                                    <div class="order--img">
                                        <img src="assets/images/svg/flowerimg.svg" class="img-fluid" alt="">
                                        <span>01</span>
                                    </div>
                                    <div class="order_title">
                                        <h6 class="is-font-18 dark-brown">Jet Set Tulips</h6>
                                        <p class="is-font-18 grey-shade">Lorem</p>
                                    </div>

                                </div>
                                <div class="order_img__price">
                                    <p class="is-font-18 black-color">₹ 3390.0</p>
                                </div>
                            </div>
                            <div class="discount">
                                <input type="text" placeholder="Discount code">
                                <div class="discount--btn">
                                    <button class="global_btn is-font-18">Apply</button>
                                </div>
                            </div>
                            <div class="discount_total">
                                <div class="total">
                                    <h6 class="grey-shade text-capitalize is-font-18">Sub total</h6>
                                    <p class="grey-color text-capitalize is-font-18">₹ 3390.0</p>
                                </div>
                                <div class="total">
                                    <h6 class="grey-shade text-capitalize is-font-18">Shipping</h6>
                                    <p class="grey-color text-capitalize is-font-18">Free shipping</p>
                                </div>
                                <div class="total">
                                    <h6 class="dark-brown text-capitalize is-font-18 main_font_family"> total</h6>
                                    <p class="dark-brown text-capitalize is-font-20 main_font_family">₹ 3390.0
                                    </p>
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