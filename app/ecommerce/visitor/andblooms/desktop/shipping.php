<?php

	$totalPrice=0;
	$id=base64_decode($id);

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
							<h2 class="heading-1">My Cart</h2>
							<h3 class="heading-2">Express Delivery Products</h3>

                            <?php

                                if($checkOrder=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."order INNER JOIN ".TABLE_PREFIX."user ON order_user_id=user_id INNER JOIN ".TABLE_PREFIX."profile ON profile_user_id=user_id INNER JOIN ".TABLE_PREFIX."address ON order_address_id=address_id INNER JOIN ".TABLE_PREFIX."city ON city_id=address_city_id INNER JOIN ".TABLE_PREFIX."state ON state_id=address_state_id WHERE order_id='{$id}' AND order_cart_session_id='".SESSION_ID."' AND order_status=0")) {

                                    if(mysqli_num_rows($checkOrder)!=0) {
										
										extract(mysqli_fetch_assoc($checkOrder));  ?>

                                        <div class="ad-card">
                                            <div class="paymnet-section">
												<h3 class="heading-2">Contact information</h3>
												<!-- Payment Gateway or Shipping Confirmation -->

											<form class="shipping-box" >

												<div class="form-row">
													<div class="form-group col-md-12">
													</div>
													<table class="table ship-table">
													
													<tbody>
														<tr>
														<th scope="row">Contact</th>
														<td id='shippingName' ><?php echo $profile_display_name ?></td>
														<!-- <td><a href="checkout-detail.html">Change</a></td> -->
														</tr>
														<tr>
														<th scope="row">Ship to</th>
														<td id='shippingAddress' ><?php echo $address_house_no.', '.$address_landmark.', '.$address_area.',<br />'.$city_name.', '.$state_name.' - '.$address_pin_code; ?></td>
														<!-- <td><a href="checkout-detail.html">Change</a></td> -->
														</tr>
													</tbody>
													</table>
												</div>

												<div class="form-row">
													<div class="form-group col-md-12">
														<h3 class="heading-2">Payment Mode</h3>
													</div>
													<table class="table ship-table ship-mth">
													
													<tbody>
														<tr>
														<td>
															<input class="form-check-input" id="online" type="radio" name="paymentMode" value="online" checked >
														</td>
														<th scope="row">Prepaid Shipping</th>
														<td><span>Free</span></td>
														</tr>
														<tr>
														<td>
															<input class="form-check-input" id="cod" type="radio" name="paymentMode" value="cash" >
														</td>
														<th scope="row">COD ( Cash handling Charges )</th>
														<td><span><i class="fas fa-rupee-sign"></i> 50/-</span></td>
														</tr>
													</tbody>
													</table>
												<!-- </div> -->

												<!-- <div class="form-row actn-box">
													<div class="form-group col-md-6">
														<a href='#' id='backToCheckout' class="retn"><i class="fa fa-angle-left" aria-hidden="true"></i> Return to Information</a>
													</div>
													<div class="form-group col-md-6">
														<input type="hidden" name="order" value="<?php echo $id ?>" >
														<a href='#' id="continueToPayment" class="btn shi-btn">Continue Payment</a>
													</div>
												</div> -->

												</form>
												</div>
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
							<form>
								<input type="hidden" name="order" value="<?php echo $id ?>" >
								<a href='#' id="continueToPayment" class="btn-bloom purchase-btn">Continue Payment</a>
							</form>
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