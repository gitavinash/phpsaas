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
							    <li class="breadcrumb-item active" aria-current="page">Customer Service</li>
							  </ol>
							</nav>
						</div>
					</div>
					
					<div class="col-md-8">

						<!-- Cart items -->

						<div id="checkOut" style="display: none" class="cart-area">

							<h2 class="heading-1">My Cart</h2>
							<h3 class="heading-2">Express Delivery Products</h3>

							<?php
								
								while($cartData=mysqli_fetch_assoc($checkCart)) {

									extract($cartData);

									$totalPrice=$totalPrice+$cart_price;

									?>

										<div class="ad-card">
											<a href="#" id="<?php echo $cart_id ?>" class="removeFromCart delet product-removal"><img src="<?php echo USER_IMAGES ?>delete.png" alt=""></a>
											<div class="cart-img">
												<img src="<?php echo $product_thumbnail ?>">
											</div>
											<div class="prodct-nme">
												<a href="<?php echo $link_url ?>"><h2><?php echo $product_title ?></h2></a>
												<h5><img src="<?php echo USER_IMAGES ?>pro-rupees-icon.svg"><?php echo $cart_price ?></h5>
											</div>
										</div>

									<?php

								}

							?>

						</div>

						<!-- Shipping Address -->

						<div id="shippingDetails" style="display: none" class="cart-area" >
							<h2 class="heading-1">Checkout</h2>
							<h3 class="heading-2">Shipping Details</h3>

							<div class="ad-card">
								<div class="password-box">
									<form id="shippingForm" class="checkout-form">
										<ul class="tab-ul-password">
											<h2 class="inter-heading" style="margin-bottom: 20px" >Contact Information</h2>
											<li><span>Email</span><input type="email" id="email" name="email" class="input" required ></li>
											<li><span>Contact No.</span><input type="number" id="mobile" name="mobile" class="input" required ></li>
											<h2 class="inter-heading" style="margin-bottom: 20px" >Enter Shipping Address</h2>
											<li>
												<div class="row">
													<div class="col-md-6">
														<input type="text" name="first_name" class="form-control" placeholder="Full name" >
													</div>
													<div class="col-md-6">
														<input type="text" name="last_name" class="form-control" placeholder="Full name" >
													</div>
												</div>
											</li>
											<li><input type="text" name="house_no" placeholder="Flat, House no., Building, Company, Apartment" class="form-control"></li>
											<li><input type="text" name="area" class="form-control" placeholder="Area, Colony, Street, Sector, Village"></li>
											<li><input type="text" name="landmark" class="form-control" placeholder="Landmark"></li></li>
											<li>
												<div class="row">
													<div class="col-12 col-md-6">
														<input type="text" name="city" class="form-control" placeholder="City">
													</div>
													<div class="col-12 col-md-3">
														<select id="inputState" name="state" class="form-control" required >
															<option selected>Choose State</option>
															<?php
															
															if($checkState=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."state WHERE state_country_id='101'")) {

																if(mysqli_num_rows($checkState)!=0) {

																	while($stateData=mysqli_fetch_assoc($checkState)) {

																		extract($stateData);
																		echo "<option value='{$state_id}'>{$state_name}</option>";

																	}

																}

															}

															?>
														</select>
													</div>
													<div class="col-12 col-md-3">
														<input type="text" name="pincode" class="form-control" placeholder="Pin code">
													</div>
												</div>
											</li>
										</ul>
									</form>
						  		</div>
							</div>

						</div>
						
						<!-- Delivery date -->

						<div id="deliveryDetails" class="cart-area" >
							<h2 class="heading-1">Checkout</h2>
							<h3 class="heading-2">Delivery date &amp; time</h3>

							<div class="ad-card">
								<div class="password-box">

								<form class="pay-form">
								<h4 class="heading-2">Select delivery date</h4>

									<div class="row">
										<div class="col-md-4">
											<a href="#" id="deliverByToday" class="delivery-btn active" ><?php echo date('jS M, ') ?>Today</a>
										</div>
										<div class="col-md-4">
											<a href="#" id="deliverByTomorrow" class="delivery-btn" ><?php echo date('jS M, ', strtotime('+1 days')) ?>Tomorrow</a>
										</div>
										<div class="col-md-4">
											<a href="#" id="deliverByOther" class="delivery-btn" ><img src="<?php echo IMG_LIBRARY ?>booking-calendar-sm.png" alt=""> Other dates</a>
										</div>
									</div>

									<div id="popup1" class="overlay-pop">
										<div class="popup">
											<h2>!Now Choose Delivery Date & Time</h2>
											<a class="close" href="#">&times;</a>
											<div class="content-pop">
												
												
												<div class="form-check col-md-12 radio-slct">
												<input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1" checked>
												<label class="form-check-label" for="inlineRadio1">Standard Delivery</label>
												<span class="tag-pric">Free</span>
												</div>
												<div class="form-check col-md-12 radio-slct">
												<input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
												<label class="form-check-label" for="inlineRadio2">Fixed Time Delivery</label>
												<span class="tag-pric">Rs. 200</span>
												</div>
												<div class="form-check col-md-12 radio-slct">
												<input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
												<label class="form-check-label" for="inlineRadio2">Midnight Delivery</label>
												<span class="tag-pric">Rs. 249</span>
												</div>

												<div class="form-group">
													<div class='input-group date js-datetimepicker2'>
														<input type='text' class="form-control" id="input"	placeholder="Select Date & Time" />
														<span class="input-group-addon">
														</span>
													</div>
												</div>

											</div>
										</div>
									</div>


								</form>

								</div>
							</div>

						</div>

					</div>
					<div class="col-md-4">

						<div class="cart-purchase" >
							<h2>Cart Summary</h2>
							<h4>Grand Total</h4>
							<h5 style="margin-bottom: 10px" ><img src="<?php echo USER_IMAGES ?>cart-rupees-icon.svg"><?php echo $totalPrice ?></h5>
							<!-- <p>Have a Coupon Code? You can apply the discount coupon in the Checkout Process</p> -->
							<a href="#" id="placeOrder" class="btn-bloom purchase-btn">Place Order</a>
							<a href="#" id="continueShipping" style="display: none" class="btn-bloom purchase-btn">Delivery date & time</a>
							<a href="#" id="specialNotes" style="display: none" class="btn-bloom purchase-btn">Special note</a>
							<a href="#" id="continueDelivery" style="display: none" class="btn-bloom purchase-btn">Pay Now</a>
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

		<section class="deliveryDateBox" style="display: none" >
			<div class="container">
				<div class="row">
					<div class="col-12">
						<div class="deliverySchedule" >
							<div class="calenderBox" style="display: none" >
								<h3 class="heading-2" >Select delivery date<span id="closeCalender" >x</span></h3>
								<div id="calender" ></div>
							</div>
							<div class="showTimeBox" style="display: none" >
								<h3 class="heading-2" >Select delivery type and slot</h3>
								<div class="form-group">
									<input type="radio" name="deliveryType" value="morning_200" > Morning delivery at <i class="fas fa-rupee-sign"></i> 200
								</div>
								<div class="form-group">
									<input type="radio" name="deliveryType" value="morning_200" > Standard delivery for free
								</div>
								<div class="form-group">
									<input type="radio" name="deliveryType" value="morning_200" > Fixed Time delivery at <i class="fas fa-rupee-sign"></i> 200
								</div>
								<div class="form-group">
									<input type="radio" name="deliveryType" value="morning_200" > Midnight delivery at <i class="fas fa-rupee-sign"></i> 249
								</div>
								<div class="row">
									<div class="col-6">
										<a href="#" class="showCalender btn btn-light btn-block" ><i class="fas fa-arrow-left"></i> Go to calender</a>
									</div>
									<div class="col-6">
										<a href="#" class="showTimeSlots btn btn-success btn-block" >Next <i class="fas fa-arrow-right"></i></a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

<?php

	include_once 'footer.php';

?>