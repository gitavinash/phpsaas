<?php require_once('./header.php');  ?>
<div class="container-fluid">
    <div class="row product_banner_section sidepadding hero align-items-center py-5">
        <div class="col-12 text-center my-5">
            <h5>HOME / <span>Checkout</span></h5>
        </div>
        </section>

        <section class="vertical-padding sidepadding">
            <div class="contianer-fluid ">
                <div class="py-5 text-center">

                    <h2 class="text-white">Checkout form</h2>
                    <p class="lead goldan-clr mt-3 lh-base">Below is an example form built entirely with Bootstrap’s form controls. Each required form group has a
                        <br>validation state that can be triggered by attempting to submit the form without completing it.
                    </p>
                </div>

                <div class="row">
                    <div class="col-md-4 order-md-2 mb-4">
                        <h4 class="d-flex justify-content-between align-items-center mb-3">
                            <span class=" goldan-clr">Your cart</span>
                            <span class="badge badge-secondary badge-pill">3</span>
                        </h4>
                        <ul class="list-group mb-3 ">
                            <li class="list-group-item d-flex justify-content-between lh-condensed">
                                <div>
                                    <h6 class="my-0">Product name</h6>
                                    <small class="text-muted">Brief description</small>
                                </div>
                                <span class="text-muted">$12</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between lh-condensed">
                                <div>
                                    <h6 class="my-0">Second product</h6>
                                    <small class="text-muted">Brief description</small>
                                </div>
                                <span class="text-muted">$8</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between lh-condensed">
                                <div>
                                    <h6 class="my-0">Third item</h6>
                                    <small class="text-muted">Brief description</small>
                                </div>
                                <span class="text-muted">$5</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between bg-light">
                                <div class="text-success">
                                    <h6 class="my-0">Promo code</h6>
                                    <small>EXAMPLECODE</small>
                                </div>
                                <span class="text-success">-$5</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Total (USD)</span>
                                <strong>$20</strong>
                            </li>
                        </ul>
                        <form class="  p-2 form-data">
                            <div class="row align-items-center bg-white">
                                <div class="col-6 col-md-12 col-lg-6">
                                    <input type="text" class="forms" placeholder=" Promo code" />
                                </div>
                                <div class="col-6 col-md-12 col-lg-6">
                                    <button class="btn1 text-white rounded-pill">Apply</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-8">
                        <div class="form-data">
                            <form action="">
                                <div class="d-flex flex-column">
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="text" class="forms" placeholder=" First name" />
                                        </div>
                                        <div class="col-6">
                                            <input type="text" class="forms" placeholder="Last name" />
                                        </div>
                                        <div class="col-12">
                                            <input type="Email" class="forms" placeholder="Enter Your Email" />
                                        </div>

                                        <div class="col-6">
                                            <input type="Text" class="forms" placeholder="Address 1" />
                                        </div>

                                        <div class="col-6">
                                            <input type="Text" class="forms" placeholder="Address 2 " />
                                        </div>
                                    </div>

                                    <div class="col-12 mt-2">
                                        <div class="row align-items-center">
                                            <div class="col-md-12 col-sm-4">
                                                <select class="form-select" id="floatingSelect" aria-label="Floating label select example">
                                                    <option selected>Countary</option>
                                                    <option value="1">India</option>
                                                    <option value="2">Us</option>
                                                    <option value="3">newzeland</option>
                                                </select>
                                            </div>

                                            <div class="col-md-12 col-sm-4">
                                                <select class="form-select" id="floatingSelect" aria-label="Floating label select example">
                                                    <option selected>State</option>
                                                    <option value="1">Delhi</option>
                                                    <option value="2">Mumbai</option>
                                                    <option value="3">Jaiput</option>
                                                </select>
                                            </div>

                                            <div class="col-md-12 col-sm-4">
                                                <input type="number" class="forms" placeholder="Zip " />
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-12">
                                        <div class="col-12">
                                            <div class="payment-container">
                                                <h4 class="item-title text-white py-3 mt-2"> <span></span>Payment-Method</h4>
                                                <div class="payment-item py-3">

                                                    <div class="item-options">
                                                        <div class="selection">
                                                            <input id="subscription" type="radio" name="subscription" checked="checked" />
                                                            <label for="subscription">Cash<span>$20</span></label>
                                                            <div class="check"></div>
                                                        </div>
                                                        <div class="selection">
                                                            <input id="subscription-2" type="radio" name="subscription" />
                                                            <label for="subscription-2">Paypal<span>$20</span></label>
                                                            <div class="check"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <!-- <label for="cc-name">Name on card</label> -->
                                                    <input type="text" class="form-control" id="cc-name" placeholder="Name on credit card" required="">
                                                    <small class="text-muted ">Full name as displayed on card</small>
                                                    <div class="invalid-feedback "> Name on card is required </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <!-- <label for="cc-number">Credit card number</label> -->
                                                    <input type="text" class="form-control" id="cc-number" placeholder="Credit card number" required="">
                                                    <small class="text-muted ">Credit card number is required </small>
                                                    <div class="invalid-feedback goldan-clr "> Credit card number is required </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3 mb-3">
                                                    <!-- <label for="cc-expiration te">Expiration</label> -->
                                                    <input type="text" class="form-control" id="cc-expiration" placeholder="Expiration" required="">
                                                    <div class="invalid-feedback"> Expiration date required </div>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <!-- <label for="cc-cvv">CVV</label> -->
                                                    <input type="text" class="form-control" id="cc-cvv" placeholder="Cvv" required="">
                                                    <div class="invalid-feedback"> Security code required </div>
                                                </div>
                                            </div>
                                            <hr class="mb-4">
                                        </div>
                                    </div>

                                </div>

        </section>
        <!-- <footer class="my-5 pt-5 text-muted text-center text-small">
            <p class="mb-1">© 2017-2019 Company Name</p>
            <ul class="list-inline">
                <li class="list-inline-item"><a href="#">Privacy</a></li>
                <li class="list-inline-item"><a href="#">Terms</a></li>
                <li class="list-inline-item"><a href="#">Support</a></li>
            </ul>
        </footer> -->
    </div>
</div>
</section>
</div>
</div>


<?php require_once('./footer.php') ?>