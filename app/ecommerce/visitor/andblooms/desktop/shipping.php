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

                                if($checkOrder=mysqli_query($connection, "SELECT order_id, order_currency, order_delivery_charges, order_total_price, order_coupon, order_payment_mode, cart_relation_id, cart_relation_type, cart_qty, cart_currency, cart_price, cart_status, product_image, product_title, inventory_currency, inventory_price, profile_display_name, address_house_no, address_landmark, address_area, city_name, state_name, address_pin_code FROM ".TABLE_PREFIX."order INNER JOIN ".TABLE_PREFIX."cart ON cart_session_id=order_cart_session_id INNER JOIN ".TABLE_PREFIX."product ON product_id=cart_relation_id INNER JOIN ".TABLE_PREFIX."inventory ON inventory_relation_id=product_id INNER JOIN ".TABLE_PREFIX."user ON order_user_id=user_id INNER JOIN ".TABLE_PREFIX."profile ON profile_user_id=user_id INNER JOIN ".TABLE_PREFIX."address ON order_address_id=address_id INNER JOIN ".TABLE_PREFIX."city ON city_id=address_city_id INNER JOIN ".TABLE_PREFIX."state ON state_id=address_state_id WHERE inventory_type='product' AND order_id='{$id}' AND order_cart_session_id='".SESSION_ID."' AND order_status=0 AND cart_status=2 AND cart_site_id=".SITE_ID)) {

                                    if(mysqli_num_rows($checkOrder)!=0) {
										
										extract(mysqli_fetch_assoc($checkOrder));
										
										$totalPrice=$totalPrice+$cart_price;

										if($checkOrderMeta=mysqli_query($connection, "SELECT order_meta_key, order_meta_value FROM ".TABLE_PREFIX."order_meta WHERE order_relation_id='{$order_id}' AND order_meta_status=1")) {

											if(mysqli_num_rows($checkOrderMeta)!=0) {

												$orderMetas=array();

												while($orderMeta=mysqli_fetch_assoc($checkOrderMeta)) {

													extract($orderMeta);

													$orderMetas[$order_meta_key] = $order_meta_value;

												}

											}

										}

										extract($orderMetas);

										?>

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
														<th scope="row">Delivery date</th>
														<td id='shippingName' ><?php echo date('l, jS F Y', strtotime($deliveryDate)) ?></td>
														<!-- <td><a href="checkout-detail.html">Change</a></td> -->
														</tr>
														<tr>
														<th scope="row">Delivery time</th>
														<td id='shippingName' ><?php echo ucwords($deliverySlot).' - '.$deliveryTime ?></td>
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

												<!-- <h3 class="heading-2">Order summary</h3> -->

												<?php
												
												// echo implode(' OR ', $order_product_id);
												// echo 'Order product id : '.$order_product_id;

												?>

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
							<h2>Cart summary</h2>
							<div>
								<div class="row">
									<div class="col-8">Subtotal</div>
									<div class="col-4"><i class="fas fa-rupee-sign"></i> <?php echo $totalPrice ?></div>
								</div>
								<div class="row" style="margin-bottom: 10px" >
									<div class="col-8">Delivery charges</div>
									<div class="col-4"><i class="fas fa-rupee-sign"></i> <?php echo $order_delivery_charges ?></div>
								</div>
							</div>
							<h4>Grand Total</h4>
							<h5 style="margin-bottom: 10px" ><img src="<?php echo USER_IMAGES ?>cart-rupees-icon.svg"><?php echo $totalPrice+$order_delivery_charges ?></h5>
							<!-- <p>Have a Coupon Code? You can apply the discount coupon in the Checkout Process</p> -->
							<form>
								<input type="hidden" name="order" value="<?php echo $id ?>" >
								<input type="hidden" name="paymentMode" value="online" >
								<a href='#' id="continueToPayment" class="btn-bloom purchase-btn">Proceed to payment</a>
							</form>
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