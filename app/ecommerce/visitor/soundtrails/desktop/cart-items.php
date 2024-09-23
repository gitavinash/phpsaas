<?php

$totalPrice = 0;

include_once 'header.php';

?>


<!-- section global platform start -->
<section class="cart-section pro-galery margin_top">
    <div class="container">
        <div class="row">

            <div class="col-md-8">

                <!-- Cart items -->

                <div id="checkOut" class="cart-area">

                    <h2 class="heading-1">My Cart</h2>
                    <h3 class="heading-2">Express Delivery Products</h3>

                    <?php

                    while ($cartData = mysqli_fetch_assoc($checkCart)) {

                        extract($cartData);

                        $totalPrice = $totalPrice + $cart_price;

                    ?>

                        <div class="ad-card">
                            <a href="#" id="<?php echo $cart_id ?>" class="removeFromCart delet product-removal"><img
                                    src="<?php echo USER_IMAGES ?>delete.png" alt=""></a>
                            <div class="cart-img">
                                <img src="<?php echo $product_thumbnail ?>">
                            </div>
                            <div class="prodct-nme">
                                <a href="<?php echo $link_url ?>">
                                    <h2><?php echo $product_title ?></h2>
                                </a>
                                <h5>₹ <?php echo $cart_price ?></h5>
                            </div>
                        </div>

                    <?php

                    }

                    ?>

                </div>

                <!-- Shipping Address -->

                <div id="shippingDetails" style="display: none" class="cart-area">
                    <h2 class="heading-1">Checkout</h2>
                    <h3 class="heading-2">Shipping Details</h3>

                    <div class="ad-card">
                        <div class="password-box">
                            <form id="shippingForm" class="checkout-form">
                                <ul class="tab-ul-password">
                                    <h2 class="heading-2" style="margin-bottom: 20px">Contact Information</h2>
                                    <li><label>First name*</label><input type="text" class="form-control"
                                            id="first_name" name="first_name" class="input" placeholder="First name"
                                            required></li>
                                    <li><label>Last name*</label><input type="text" class="form-control" id="last_name"
                                            name="last_name" class="input" placeholder="Last name" required></li>
                                    <li><label>Email*</label><input type="email" class="form-control" id="email"
                                            name="email" class="input" placeholder="Email" required></li>
                                    <li><label>Contact No.*</label><input type="number" onwheel="this.blur()" class="form-control" id="mobile"
                                            name="mobile" class="input" placeholder="Contact no." required></li>
                                    <h2 class="heading-2" style="margin-bottom: 20px">Enter Shipping Address</h2>
                                    <li>
                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <input type="text" name="address_first_name" class="form-control"
                                                    placeholder="First name*" style="margin-bottom: 5px">
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <input type="text" name="address_last_name" class="form-control"
                                                    placeholder="Last name*">
                                            </div>
                                        </div>
                                    </li>
                                    <li><input type="number" onwheel="this.blur()" id="addressMobile" name="address_mobile"
                                            class="form-control" placeholder="Contact no.*" required></li>
                                    <li><input type="text" name="house_no"
                                            placeholder="Flat, House no., Building, Company, Apartment*"
                                            class="form-control"></li>
                                    <li><input type="text" name="area" class="form-control"
                                            placeholder="Area, Colony, Street, Sector, Village*"></li>
                                    <li><input type="text" name="landmark" class="form-control" placeholder="Landmark">
                                    </li>
                                    </li>
                                    <li>
                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <input type="text" name="city" class="form-control" placeholder="City*">
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <select id="inputState" name="state" class="form-control" required>
                                                    <option selected>Choose State*</option>
                                                    <?php

                                                    if ($checkState = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "state WHERE state_country_id='101'")) {

                                                        if (mysqli_num_rows($checkState) != 0) {

                                                            while ($stateData = mysqli_fetch_assoc($checkState)) {

                                                                extract($stateData);
                                                                echo "<option value='{$state_id}'>{$state_name}</option>";
                                                            }
                                                        }
                                                    }

                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <input type="text" name="pincode" class="form-control"
                                                    placeholder="Pin code*">
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
            <div class="col-md-4">

                <div class="cart-purchase">
                    <h2>Cart Summary</h2>
                    <h4>Grand Total</h4>
                    <h5 style="margin-bottom: 10px">₹ <?php echo $totalPrice ?></h5>
                    <a href="#" id="placeOrder" class="btn-bloom purchase-btn">Place Order</a>
                    <a href="#" id="continueShipping" style="display: none" class="btn-bloom purchase-btn">Proceed to
                        payment</a>
                </div>

                <div class="site-feature">
                    <ul>
                        <li><i class="fa fa-inr" aria-hidden="true"></i> No Hidden Charges</li>
                        <li><i class="fa fa-shield" aria-hidden="true"></i> 100% Secure payments</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<?php

include_once 'footer.php';

?>