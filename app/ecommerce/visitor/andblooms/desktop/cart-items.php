<?php

	$totalPrice=0;

	include_once 'header.php';

?>


		<!-- section global platform start -->
		<section class="banner-section pro-galery">
			<div class="container">
				<div class="row">
					<!-- <div class="col-md-12">
						<div class="brdcrum">
							<nav aria-label="breadcrumb">
							  <ol class="breadcrumb">
							    <li class="breadcrumb-item"><a href="#">Home</a></li>
							    <li class="breadcrumb-item active" aria-current="page">Checkou</li>
							  </ol>
							</nav>
						</div>
					</div> -->
					
					<div class="col-md-8">

						<!-- Cart items -->

						<div id="checkOut" class="cart-area">

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
														<input type="text" name="first_name" class="form-control" placeholder="First name" style="margin-bottom: 15px" >
													</div>
													<div class="col-md-6">
														<input type="text" name="last_name" class="form-control" placeholder="Last name" >
													</div>
												</div>
											</li>
											<li><input type="text" name="house_no" placeholder="Flat, House no., Building, Company, Apartment" class="form-control"></li>
											<li><input type="text" name="area" class="form-control" placeholder="Area, Colony, Street, Sector, Village"></li>
											<li><input type="text" name="landmark" class="form-control" placeholder="Landmark"></li></li>
											<li>
												<div class="row">
													<div class="col-12 col-md-6">
														<input type="text" name="city" class="form-control" placeholder="City" style="margin-bottom: 15px" >
													</div>
													<div class="col-12 col-md-3">
														<select id="inputState" name="state" class="form-control" required style="margin-bottom: 15px"  >
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

						<div id="deliveryDetails" style="display: none" class="cart-area" >
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
							<!-- <a href="#" id="continueDelivery" style="display: none" class="btn-bloom purchase-btn">Pay Now</a> -->
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
								<h3 class="heading-2" >Select delivery date<span class="closeCalender" >x</span></h3>
								<div id="calender" ></div>
							</div>
							<div class="showTimeBox" style="display: none" >
								<h3 class="heading-2" >Select delivery type and slot<span class="closeCalender" >x</span></h3>
								<div class="form-group">
									<div class="custom-control custom-radio" id="morningDeliveryBox" >
										<input type="radio" class="deliverySlot custom-control-input" id="morningDelivery" name="deliveryType" value="morning_200" >
										<label class="custom-control-label" for="morningDelivery"> Morning delivery at <i class="fas fa-rupee-sign"></i> 200</label>
									</div>
								</div>
								<div class="form-group">
									<div class="custom-control custom-radio" id="standardDeliveryBox" >
										<input type="radio" class="deliverySlot custom-control-input" id="standardDelivery" name="deliveryType" value="standard_0" >
										<label class="custom-control-label" for="standardDelivery"> Standard delivery for free</label>
									</div>
								</div>
								<div class="form-group">
									<div class="custom-control custom-radio" id="fixedDeliveryBox" >
										<input type="radio" class="deliverySlot custom-control-input" id="fixedDelivery" name="deliveryType" value="fixed_200" >
										<label class="custom-control-label" for="fixedDelivery"> Fixed Time delivery at <i class="fas fa-rupee-sign"></i> 200</label>
									</div>
								</div>
								<div class="form-group">
									<div class="custom-control custom-radio" id="midnightDeliveryBox" >
										<input type="radio" class="deliverySlot custom-control-input" id="midnightDelivery" name="deliveryType" value="midnight_249" >
										<label class="custom-control-label" for="midnightDelivery"> Midnight delivery at <i class="fas fa-rupee-sign"></i> 249</label>
									</div>
								</div>
								<div class="row">
									<div class="col-6">
										<a href="#" class="showCalender btn btn-light btn-block" ><i class="fas fa-arrow-left"></i> Go to calender</a>
									</div>
									<div class="col-6">
										<!-- <a href="#" class="showTimeSlots btn btn-success btn-block" >Delivery time <i class="fas fa-arrow-right"></i></a> -->
									</div>
								</div>
							</div>

							<div class="showDeliveryTimeBox" style="display: none" >
								<h3 class="heading-2" >Select delivery time<span class="closeCalender" >x</span></h3>
								<div class="row morningDeliveryTimeSlots" style="display: none; margin-bottom: 20px" >
									<div class="col-6">
										<div class="custom-control custom-radio">
											<input type="radio" class="deliveryTimeSlot custom-control-input" id="morning07" name="deliveryTime" value="07:00 - 09:00" >
											<label class="custom-control-label" for="morning07">07:00 - 09:00 hrs</label>
										</div>
									</div>
								</div>
								<div class="row standardDeliveryTimeSlots" style="display: none; margin-bottom: 20px" >
									<div class="col-6">
										<div class="custom-control custom-radio">
											<input type="radio" class="deliveryTimeSlot custom-control-input" id="standard09" name="deliveryTime" value="09:00 - 12:00" >
											<label class="custom-control-label" for="standard09">09:00 - 12:00 hrs</label>
										</div>
										<div class="custom-control custom-radio">
											<input type="radio" class="deliveryTimeSlot custom-control-input" id="standard15" name="deliveryTime" value="15:00 - 18:00" >
											<label class="custom-control-label" for="standard15">15:00 - 18:00 hrs</label>
										</div>
										<div class="custom-control custom-radio">
											<input type="radio" class="deliveryTimeSlot custom-control-input" id="standard20" name="deliveryTime" value="20:00 - 22:00" >
											<label class="custom-control-label" for="standard20">20:00 - 22:00 hrs</label>
										</div>
									</div>
									<div class="col-6">
										<div class="custom-control custom-radio">
											<input type="radio" class="deliveryTimeSlot custom-control-input" id="standard12" name="deliveryTime" value="12:00 - 15:00" >
											<label class="custom-control-label" for="standard12">12:00 - 15:00 hrs</label>
										</div>
										<div class="custom-control custom-radio">
											<input type="radio" class="deliveryTimeSlot custom-control-input" id="standard18" name="deliveryTime" value="18:00 - 21:00" >
											<label class="custom-control-label" for="standard18">18:00 - 21:00 hrs</label>
										</div>
									</div>
								</div>
								<div class="row fixedDeliveryTimeSlots" style="display: none; margin-bottom: 20px" >
									<div class="col-6">
										<div class="custom-control custom-radio">
											<input type="radio" class="deliveryTimeSlot custom-control-input" id="fixed10" name="deliveryTime" value="10:00 - 11:00" >
											<label class="custom-control-label" for="fixed10">10:00 - 11:00 hrs</label>
										</div>
										<div class="custom-control custom-radio">
											<input type="radio" class="deliveryTimeSlot custom-control-input" id="fixed12" name="deliveryTime" value="12:00 - 13:00" >
											<label class="custom-control-label" for="fixed12">12:00 - 13:00 hrs</label>
										</div>
										<div class="custom-control custom-radio">
											<input type="radio" class="deliveryTimeSlot custom-control-input" id="fixed14" name="deliveryTime" value="14:00 - 15:00" >
											<label class="custom-control-label" for="fixed14">14:00 - 15:00 hrs</label>
										</div>
										<div class="custom-control custom-radio">
											<input type="radio" class="deliveryTimeSlot custom-control-input" id="fixed16" name="deliveryTime" value="16:00 - 17:00" >
											<label class="custom-control-label" for="fixed16">16:00 - 17:00 hrs</label>
										</div>
										<div class="custom-control custom-radio">
											<input type="radio" class="deliveryTimeSlot custom-control-input" id="fixed18" name="deliveryTime" value="18:00 - 19:00" >
											<label class="custom-control-label" for="fixed18">18:00 - 19:00 hrs</label>
										</div>
										<div class="custom-control custom-radio">
											<input type="radio" class="deliveryTimeSlot custom-control-input" id="fixed20" name="deliveryTime" value="20:00 - 21:00" >
											<label class="custom-control-label" for="fixed20">20:00 - 21:00 hrs</label>
										</div>
										<div class="custom-control custom-radio">
											<input type="radio" class="deliveryTimeSlot custom-control-input" id="fixed22" name="deliveryTime" value="22:00 - 23:00" >
											<label class="custom-control-label" for="fixed22">22:00 - 23:00 hrs</label>
										</div>
									</div>
									<div class="col-6">
										<div class="custom-control custom-radio">
											<input type="radio" class="deliveryTimeSlot custom-control-input" id="fixed11" name="deliveryTime" value="11:00 - 12:00" >
											<label class="custom-control-label" for="fixed11">11:00 - 12:00 hrs</label>
										</div>
										<div class="custom-control custom-radio">
											<input type="radio" class="deliveryTimeSlot custom-control-input" id="fixed13" name="deliveryTime" value="13:00 - 14:00" >
											<label class="custom-control-label" for="fixed13">13:00 - 14:00 hrs</label>
										</div>
										<div class="custom-control custom-radio">
											<input type="radio" class="deliveryTimeSlot custom-control-input" id="fixed15" name="deliveryTime" value="15:00 - 16:00" >
											<label class="custom-control-label" for="fixed15">15:00 - 16:00 hrs</label>
										</div>
										<div class="custom-control custom-radio">
											<input type="radio" class="deliveryTimeSlot custom-control-input" id="fixed17" name="deliveryTime" value="17:00 - 18:00" >
											<label class="custom-control-label" for="fixed17">17:00 - 18:00 hrs</label>
										</div>
										<div class="custom-control custom-radio">
											<input type="radio" class="deliveryTimeSlot custom-control-input" id="fixed19" name="deliveryTime" value="19:00 - 20:00" >
											<label class="custom-control-label" for="fixed19">19:00 - 20:00 hrs</label>
										</div>
										<div class="custom-control custom-radio">
											<input type="radio" class="deliveryTimeSlot custom-control-input" id="fixed21" name="deliveryTime" value="21:00 - 22:00" >
											<label class="custom-control-label" for="fixed21">21:00 - 22:00 hrs</label>
										</div>
									</div>
								</div>
								<div class="row midnightDeliveryTimeSlots" style="display: none; margin-bottom: 20px" >
									<div class="col-12">
										<div class="custom-control custom-radio">
											<input type="radio" class="deliveryTimeSlot custom-control-input" id="midnight23" name="deliveryTime" value="23:00 - 01:00" >
											<label class="custom-control-label" for="midnight23">23:00 - 01:00 hrs<br /><span style="color: red;" >We will deliver product on  <?php echo (((int) date('H')<20) ? date('jS F') : date('jS F', strtotime('+1 days')) ); ?> after 11:00PM.</span></label>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-6">
										<a href="#" class="visibleShowTimeBox btn btn-light btn-block" ><i class="fas fa-arrow-left"></i> Delivery slots</a>
									</div>
									<div class="col-6">
										<a href="#" class="specialNotesBox btn btn-success btn-block" >Special notes <i class="fas fa-arrow-right"></i></a>
									</div>
								</div>
							</div>

							<div class="specialNotesBox" style="display: none" >
								<h3 class="heading-2" >Special notes<span class="closeCalender" >x</span></h3>
								<div class="row">
									<div class="col-12">
										<div class="form-group">
											<label>Select occasion</label>
											<select class="form-control" name="occasion" id="occasion" >
												<option value="">Select occasion</option>
												<option value="Congratulation">Congratulation</option>
												<option value="Get Well Soon">Get Well Soon</option>
												<option value="I am Sorry">I am Sorry</option>
												<option value="Thank U">Thank U</option>
												<option value="Appreciation">Appreciation</option>
												<option value="Cheer Up">Cheer Up</option>
												<option value="New Born">New Born</option>
												<option value="Wedding">Wedding</option>
												<option value="Just Like That">Just Like That</option>
												<option value="Onam">Onam</option>
												<option value="Eid">Eid</option>
												<option value="Love &amp; Romance">Love &amp; Romance</option>
												<option value="Christmas">Christmas</option>
												<option value="New Year">New Year</option>
												<option value="Lohri">Lohri</option>
												<option value="Rose Day">Rose Day</option>
												<option value="Propose Day">Propose Day</option>
												<option value="Chocolate Day">Chocolate Day</option>
												<option value="Teddy Day">Teddy Day</option>
												<option value="Promise Day">Promise Day</option>
												<option value="Hug Day">Hug Day</option>
												<option value="Kiss Day">Kiss Day</option>
												<option value="Valentines Day">Valentines Day</option>
												<option value="Womens Day">Womens Day</option>
												<option value="Holi">Holi</option>
												<option value="April Fool">April Fool</option>
												<option value="Mother's Day">Mother's Day</option>
												<option value="Father's Day">Father's Day</option>
												<option value="Friendship Day">Friendship Day</option>
												<option value="Rakhi">Rakhi</option>
												<option value="Independence Day">Independence Day</option>
												<option value="Ganesh Chaturthi">Ganesh Chaturthi</option>
												<option value="Teacher's Day">Teacher's Day</option>
												<option value="Grandparent's Day">Grandparent's Day</option>
												<option value="Daughter's Day">Daughter's Day</option>
												<option value="Karwa Chauth">Karwa Chauth</option>
												<option value="other">Other</option>
											</select>
										</div>
										<div class="form-group">
											<label>Your message</label>
											<textarea name="message" id="message" class="form-control" style="height: 150px" ></textarea>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-6">
										<a href="#" class="visibleShowTimeBox btn btn-light btn-block" ><i class="fas fa-arrow-left"></i> Delivery slots</a>
									</div>
									<div class="col-6">
										<a href="#" class="reviewBtn btn btn-success btn-block" >Review <i class="fas fa-arrow-right"></i></a>
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