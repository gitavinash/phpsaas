<?php

	$totalPrice=0;

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
      	<link rel="shortcut icon" href=" ">
		<title>Checkout </title>
		<meta name="" content="">
		<meta name="viewport" content="width=device-width,height=device-height,minimum-scale=1,initial-scale=1">
		<!-- css -->
		<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;600;800&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>bootstrap.min.css">
		<link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>all.min.css">
		<link rel="stylesheet" type="text/css" href="<?php echo USER_STYLESHEET ?>style.min.css">

	</head>
	<body>

		<!-- section global platform start -->
		<!-- <section class="banner-section pro-galery"> -->
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-7">
						<div class="main-right dn-l">

							<div class="check-head mob-check">
								<div class="logo">
									<a href="/" ><img src="<?php echo SITE_LOGO ?>" class="logo-mb"></a>
								</div>
							</div>

							<?php
								
								if($checkCart=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."cart WHERE cart_session_id='".SESSION_ID."' AND cart_status=0")) {

									if(mysqli_num_rows($checkCart)!=0) {

										while($cartData=mysqli_fetch_assoc($checkCart)) {

											extract($cartData);
											
											switch($cart_relation_id) {

												case 1 :
													$productName='Immunity Vitals';
													$productImage=SITE_URL.'/app/ecommerce/visitor/vedicvitals/desktop/assets/images/product/immunity-vitals/1.jpg';
													break;

												case 2 :
													$productName='Tar Free';
													$productImage=SITE_URL.'/app/ecommerce/visitor/vedicvitals/desktop/assets/images/product/tar-free/1.jpg';
													break;

												case 3 :
													$productName='Gyno Vitals';
													$productImage=SITE_URL.'/app/ecommerce/visitor/vedicvitals/desktop/assets/images/product/gyno-vitals/1.jpg';
													break;

												case 4 :
													$productName='V Liver Vitals';
													$productImage=SITE_URL.'/app/ecommerce/visitor/vedicvitals/desktop/assets/images/product/v-liver-vitals/1.jpg';
													break;

												default :
													$productName='';
													$productImage='';
													break;

											}

											$totalPrice=$totalPrice+$cart_price;

											?>

												<div class="prodcut-list-cart">
													<img src="<?php echo $productImage ?>">
													<h3><?php echo $productName; ?> – Pack of <?php echo $cart_qty ?><a href='#' class='removeFromCart' id="<?php echo $cart_id ?>" >Remove item</a></h3>
													<div class="price-new">
														<span><i class="fas fa-rupee-sign" aria-hidden="true"></i> <?php echo $cart_price ?>.00</span>
													</div>
												</div>


											<?php

										}

									} else {

										echo "Empty Cart";

									}

								}

							?>

							<div class="copn-div">
								<form class="form-row">
								  	<div class="form-group col-md-8 cod-ap">
									    <input type="text" class="form-control couponCode" placeholder="Discount Code">
								  	</div>
								  	<div class="form-group col-md-4 ap-btn">
									  	<button type="submit" class="checkCoupon btn">Apply</button>
								  	</div>
								</form>
							</div>

							<div class="sub-totl">
								<div class="sub1">
									<span class="spn-lft">Subtotal</span>
									<span class="spn-rgt rt-t"><i class="fas fa-rupee-sign" aria-hidden="true"></i> <strong id="subTotal" ><?php echo $totalPrice ?></strong>.00</span>
								</div>
								<div class="sub1">
									<span class="spn-lft">Shipping</span>
									<span class="spn-rgt ship-rt shippingCharge" >Calculated at next step</span>
								</div>
							</div>

							<div class="sub-tot2">
								<div class="sub1">
									<span class="spn-lft-1 totl">Total<br>
										<span class="ship-ad">Including GST</span></span>
									<span class="spn-rgt-1 man-t"><i class="fas fa-rupee-sign" aria-hidden="true"></i> <strong id="finalPrice2" ><?php echo $totalPrice ?></strong>.00</span>
								</div>
							</div>

						</div>

						<div class="main-left">
							<div class="check-head dn-s">
								<div class="logo">
									<a href="/" ><img src="<?php echo SITE_LOGO ?>" class="logo-mb"></a>
								</div>
							</div>
							<div class="brdcrum check-bread dn-s">
								<nav aria-label="breadcrumb">
								  <ol class="breadcrumb">
								    <!-- <li class="breadcrumb-item"><a href="#">Home</a></li>
								    <li class="breadcrumb-item"><a href="#">Product</a></li>
								    <li class="breadcrumb-item active" aria-current="page">product name</li> -->
								  </ol>
								</nav>
							</div>

							<!-- form start -->
							<form class="checkout-form">
								<div class="form-row">
								    <div class="form-group col-md-12">
								      <label class="labl-head">Contact information</label>
								      <input type="email" name="email" class="form-control" id="email" placeholder="Email">
								    </div>
								</div>
								<div class="form-group">
								    <div class="form-check">
								      <input class="form-check-input" name="emailUpdate" type="checkbox" id="gridCheck">
								      <label class="form-check-label" for="gridCheck">
								        Keep me up to date on news and exclusive offers
								      </label>
								    </div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-12">
										<label class="labl-head">Shipping Address</label>
									</div>
								    <div class="form-group col-md-6">
								      <input type="text" name="first_name" class="form-control" id="" placeholder="First name">
								    </div>
								    <div class="form-group col-md-6">
								      <input type="text" name="last_name" class="form-control" id="" placeholder="Last name">
								    </div>
								</div>
								<div class="form-group">
								    <input type="text" name="house_no" class="form-control" id="inputAddress" placeholder="Flat, House no., Building, Company, Apartment">
								</div>
								<div class="form-group">
								    <input type="text" name="area" class="form-control" id="inputAddress2" placeholder="Area, Colony, Street, Sector, Village">
								</div>
								<div class="form-group">
								    <input type="text" name="landmark" class="form-control" id="inputAddress2" placeholder="Landmark">
								</div>
								<div class="form-row">
								    <div class="form-group col-md-6">
								      <input type="text" name="city" class="form-control" id="inputCity" placeholder="City">
								    </div>
								    <div class="form-group col-md-4">
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
								    <div class="form-group col-md-2">
								      <input type="text" class="form-control" name="pincode" id="inputZip" placeholder="Zip">
								    </div>
								</div>
								<div class="form-group">
								    <input type="tel" class="form-control" name="mobile" id="mobile" placeholder="Contact number" require >
								</div>
								<!-- <div class="form-group">
								    <div class="form-check">
								      <input class="form-check-input" name type="checkbox" id="gridCheck">
								      <label class="form-check-label" for="gridCheck">Save this information for next time</label>
								    </div>
								</div> -->
								<div class="form-row actn-box">
									<div class="form-group col-md-6">
									</div>
									<div class="form-group col-md-6">
										<input type="hidden" name="country" value='101' >
								  		<a href="#" id="continueShipping" class="btn shi-btn">Continue Shipping</a>
									</div>
								</div>
							</form>
							<!-- form end -->

							<!-- Payment Gateway or Shipping Confirmation -->

							<form class="shipping-box" style="display: none" >

								<div class="form-row">
								    <div class="form-group col-md-12">
								      <label class="labl-head">Contact information</label>
								    </div>
								    <table class="table ship-table">
									
									  <tbody>
									    <tr>
									      <th scope="row">Contact</th>
									      <td id='shippingName' ></td>
									      <!-- <td><a href="checkout-detail.html">Change</a></td> -->
									    </tr>
									    <tr>
									      <th scope="row">Ship to</th>
									      <td id='shippingAddress' ></td>
									      <!-- <td><a href="checkout-detail.html">Change</a></td> -->
									    </tr>
									  </tbody>
									</table>
								</div>
								
								<div class="form-row">
									<div class="form-group col-md-12">
										<label class="labl-head">Shipping Method</label>
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
								</div>

								<div class="form-row actn-box">
									<div class="form-group col-md-6">
										<a href='#' id='backToCheckout' class="retn"><i class="fa fa-angle-left" aria-hidden="true"></i> Return to Information</a>
									</div>
									<div class="form-group col-md-6">
								  		<a href='#' id="continueToPayment" class="btn shi-btn">Continue Payment</a>
									</div>
								</div>

							</form>

							<!-- footer-link -->

							<div class="check-footer">
								<ul>
									<li><a href="/return-policy/">Return policy</a></li>
									<li><a href="/disclaimer/">Disclaimer</a></li>
									<li><a href="/privacy-policy/">Privacy policy</a></li>
									<li><a href="/terms-of-use/">Terms of use</a></li>
								</ul>
							</div>
						</div>
					</div>
					<div class="col-md-5 bg-gry dn-s">
						<div class="main-right">

							<?php
							
								if($checkCart=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."cart WHERE cart_session_id='".SESSION_ID."' AND cart_status=0")) {

									if(mysqli_num_rows($checkCart)!=0) {

										$totalPrice=0;

										while($cartData=mysqli_fetch_assoc($checkCart)) {

											extract($cartData);
											
											switch($cart_relation_id) {

												case 1 :
													$productName='Immunity Vitals';
													$productImage=SITE_URL.'/app/ecommerce/visitor/vedicvitals/desktop/assets/images/product/immunity-vitals/1.jpg';
													break;

												case 2 :
													$productName='Tar Free';
													$productImage=SITE_URL.'/app/ecommerce/visitor/vedicvitals/desktop/assets/images/product/tar-free/1.jpg';
													break;

												case 3 :
													$productName='Gyno Vitals';
													$productImage=SITE_URL.'/app/ecommerce/visitor/vedicvitals/desktop/assets/images/product/gyno-vitals/1.jpg';
													break;

												case 4 :
													$productName='V Liver Vitals';
													$productImage=SITE_URL.'/app/ecommerce/visitor/vedicvitals/desktop/assets/images/product/v-liver-vitals/1.jpg';
													break;

												default :
													$productName='';
													$productImage='';
													break;

											}

											$totalPrice=$totalPrice+$cart_price;

											?>

												<div class="prodcut-list-cart" id="product_<?php echo $cart_id ?>" >
													<img src="<?php echo $productImage ?>">
													<h3><?php echo $productName; ?> – Pack of <?php echo $cart_qty ?><a href='#' class='removeFromCart' id="<?php echo $cart_id ?>" >Remove item</a></h3>
													<div class="price-new">
														<span ><i class="fas fa-rupee-sign" aria-hidden="true"></i> <?php echo $cart_price ?>.00</span>
													</div>
												</div>


											<?php

										}

									} else {

										echo "Empty Cart";

									}

								}

							?>

							<div class="copn-div">
								<form class="form-row">
								  	<div class="form-group col-md-8 cod-ap">
									    <input type="text" class="form-control couponCode" placeholder="Discount Code">
								  	</div>
								  	<div class="form-group col-md-4 ap-btn">
								  		<button type="submit" class="checkCoupon btn">Apply</button>
								  	</div>
								</form>
							</div>

							<div class="sub-totl">
								<div class="sub1">
									<span class="spn-lft">Subtotal</span>
									<span class="spn-rgt rt-t"><i class="fas fa-rupee-sign" aria-hidden="true"></i> <?php echo $totalPrice ?>.00</span>
								</div>
								<div class="sub1">
									<span class="spn-lft">Shipping</span>
									<span class="spn-rgt ship-rt shippingCharge" >Calculated at next step</span>
								</div>
							</div>

							<div class="sub-tot2">
								<div class="sub1">
									<span class="spn-lft-1 totl">Total<br>
									<span class="ship-ad">Including GST</span></span>
									<span class="spn-rgt-1 man-t" ><i class="fas fa-rupee-sign" aria-hidden="true"></i> <strong id="finalPrice" ><?php echo $totalPrice ?></strong>.00</span>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div> 
		<!-- </section> -->
		<!-- section global platform end -->

		<!-- script start -->
		<?php echo require_once NOTIFICATION.'index.php' ?>
		<script type="text/javascript" src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" ></script>
		<script src="<?php echo APP_JS_LIBRARY ?>app.min.js" ></script>
		<!-- script end -->

		
	
	</body>
</html>