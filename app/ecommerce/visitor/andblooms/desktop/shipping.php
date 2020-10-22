<?php

	$totalPrice=0;

	include_once 'header.php';

?>


		<!-- section global platform start -->
		<section class="banner-section pro-galery">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="brdcrum">
							<nav aria-label="breadcrumb">
							  <ol class="breadcrumb">
							    <li class="breadcrumb-item"><a href="#">Home</a></li>
							    <li class="breadcrumb-item active" aria-current="page">Shipping</li>
							  </ol>
							</nav>
						</div>
					</div>
					
					<div class="col-md-8">

						<!-- Cart items -->

						<div id="checkOut" class="cart-area">

                            <?php

                                if($checkOrder=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."order INNER JOIN ".TABLE_PREFIX."user ON order_user_id=user_id INNER JOIN ".TABLE_PREFIX."profile ON profile_user_id=user_id INNER JOIN ".TABLE_PREFIX."address ON order_address_id=address_id INNER JOIN ".TABLE_PREFIX."city ON city_id=address_city_id INNER JOIN ".TABLE_PREFIX."state ON state_id=address_state_id WHERE order_id='{$id}' AND order_cart_session_id='".SESSION_ID."' AND order_status=0")) {

                                    if(mysqli_num_rows($checkOrder)==0) { ?>

                                        <h3 class="heading-2">Payment Method</h3>
                                        <div class="ad-card">
                                            <div class="paymnet-section">
                                                <h2 class="inter-heading">Select Payment Mode</h2>
                                                <form class="pay-form">
                                                    <div class="form-check ">
                                                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1" checked>
                                                    <label class="form-check-label" for="inlineRadio1">Prepaid Shipping</label>
                                                    </div>
                                                    <div class="form-check ">
                                                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
                                                    <label class="form-check-label" for="inlineRadio2">Cash On Delivery</label>
                                                    </div>
                                                </form>
                                                <span class="dflt-box sm-btn">
                                                    <a href="" class="btn-bloom purchase-btn">Continue Payment</a>
                                                </span>
                                            </div>
                                        </div>

                                    <?php
                                    } else {

                                        echo '<h3 class="heading-2">Somthing is wrong</h3>';

                                    }

                                }

                            ?>

                        </div>

					</div>
					<div class="col-md-4">

						<div class="cart-purchase" >
							<h2>Cart Summary</h2>
							<h4>Grand Total</h4>
							<h5 style="margin-bottom: 10px" ><img src="<?php echo USER_IMAGES ?>cart-rupees-icon.svg"><?php echo $totalPrice ?></h5>
							<!-- <p>Have a Coupon Code? You can apply the discount coupon in the Checkout Process</p> -->
							<a href="#" id="placeOrder" class="btn-bloom purchase-btn">Place Order</a>
							<a href="#" id="continueShipping" style="display: none" class="btn-bloom purchase-btn">Continue Shipping</a>
						</div>

						<div class="conti-shop">
							<a href="/" class="btn-bloom shop-btn">Continue Shoping</a>
						</div>

						<div class="site-feature">
							<ul>
								<li><span class="material-icons">money_off</span> No Hidden Charges</li>
								<li><span class="material-icons">security</span> 100% Secure payments</li>
								<li><span class="material-icons">tag_faces</span> 2 lakh Smiles Delivered</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</section>

<?php

	include_once 'footer.php';

?>