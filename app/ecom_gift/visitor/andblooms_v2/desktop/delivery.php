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
                            <h3 class="is-font-26 dark-brown main_font_family">Delivery</h3>
                            <!-- class="was-validated" -->
                            <form action="">
                                <div class="delivery_input">
                                    <select class="form-select" required aria-label="select example">
                                        <option value="">Country/region</option>
                                        <option value="1">India</option>
                                        <option value="2">Nepal</option>
                                        <option value="3">Pakistan</option>
                                    </select>
                                    <div class="invalid-feedback">Example invalid select feedback</div>
                                </div>
                                <div class="delivery_input">
                                    <div class="delivery_input__flex">
                                        <input type="text" placeholder="First name">
                                        <input type="text" placeholder="Last name">
                                    </div>
                                </div>
                                <div class="delivery_input">
                                    <div class="delivery_input__flex">
                                        <input type="text" placeholder="Address">
                                        <input type="text" placeholder="Add apartment,suite,etc.">
                                    </div>
                                </div>
                                <div class="delivery_input">
                                    <div class="delivery_input__flex">
                                        <input type="text" placeholder="City">
                                        <select class="form-select" required aria-label="select example">
                                            <option value="">State</option>
                                            <option value="1">Delhi</option>
                                            <option value="2">Mumbai</option>
                                            <option value="3">Banglore</option>
                                        </select>
                                        <input type="text" placeholder="Zip code">
                                    </div>
                                </div>
                                <div class="diff_add">
                                    <div class="diff_add_flex">
                                        <input type="checkbox" data-bs-toggle="collapse" href="#collapseExample"
                                            role="button" aria-expanded="false" aria-controls="collapseExample">
                                        <p class="is-font-18 grey-shade">Ship to a different address?</p>
                                    </div>
                                    <div class="collapse_input collapse" id="collapseExample">

                                        <div class="delivery_input">
                                            <select class="form-select" required aria-label="select example">
                                                <option value="">Country/region</option>
                                                <option value="1">India</option>
                                                <option value="2">Nepal</option>
                                                <option value="3">Pakistan</option>
                                            </select>
                                            <div class="invalid-feedback">Example invalid select feedback</div>
                                        </div>
                                        <div class="delivery_input">
                                            <div class="delivery_input__flex">
                                                <input type="text" placeholder="First name">
                                                <input type="text" placeholder="Last name">
                                            </div>
                                        </div>
                                        <div class="delivery_input">
                                            <div class="delivery_input__flex">
                                                <input type="text" placeholder="Address">
                                                <input type="text" placeholder="Add apartment,suite,etc.">
                                            </div>
                                        </div>
                                        <div class="delivery_input">
                                            <div class="delivery_input__flex">
                                                <input type="text" placeholder="City">
                                                <select class="form-select" required aria-label="select example">
                                                    <option value="">State</option>
                                                    <option value="1">Delhi</option>
                                                    <option value="2">Mumbai</option>
                                                    <option value="3">Banglore</option>
                                                </select>
                                                <input type="text" placeholder="Zip code">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="order_notes">
                                    <label for="" class="is-font-18 grey-shade">Order notes (optional)</label>
                                    <textarea name="" id="" cols="30" rows="4"
                                        placeholder="notes about your order , eg . special notes for delievery."
                                        class="text-capitalize grey-shade"></textarea>
                                </div>
                                <div class="continue--btn">
                                    <button class="global_btn is-font-18">Continue</button>
                                </div>
                            </form>
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