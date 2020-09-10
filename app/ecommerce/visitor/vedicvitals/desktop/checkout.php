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
		<link rel="stylesheet" type="text/css" href="<?php echo USER_STYLESHEET ?>style.css">

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
							<!-- <div class="brdcrum check-bread dn-l mb-10">
								<nav aria-label="breadcrumb">
								  <ol class="breadcrumb">
								    <li class="breadcrumb-item"><a href="#">Home</a></li>
								    <li class="breadcrumb-item"><a href="#">Product</a></li>
								    <li class="breadcrumb-item active" aria-current="page">product name</li>
								  </ol>
								</nav>
							</div> -->

							<?php
							
								if($checkCart=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."cart WHERE cart_session_id='".SESSION_ID."'")) {

									if(mysqli_num_rows($checkCart)!=0) {

										$totalPrice=0;

										while($cartData=mysqli_fetch_assoc($checkCart)) {

											extract($cartData);
											
											switch($cart_product_id) {

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
													<h3><?php echo $productName; ?> – Pack of <?php echo $cart_product_qty ?></h3>
													<div class="price-new">
														<span><i class="fas fa-rupee-sign" aria-hidden="true"></i> <?php echo $cart_price ?>.00</span>
													</div>
												</div>


											<?php

										}

									} else {

										?>
										<div class="prodcut-list-cart">
											<h3>Empty Cart</h3>
										</div>
										<?php

									}

								}

							?>

<div class="copn-div">
								<form class="form-row">
								  	<div class="form-group col-md-8 cod-ap">
									    <input type="text" readonly class="form-control" id="" placeholder="Discount Code">
								  	</div>
								  	<div class="form-group col-md-4 ap-btn">
								  		<button type="submit" id="checkCoupon" class="btn">Apply</button>
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
									<span class="spn-rgt ship-rt">Calculate at next step</span>
								</div>
							</div>

							<div class="sub-tot2">
								<div class="sub1">
									<span class="spn-lft-1 totl">Total<br>
									<span class="ship-ad">Including GST</span></span>
									<span class="spn-rgt-1 man-t"><i class="fas fa-rupee-sign" aria-hidden="true"></i> <?php echo $totalPrice ?>.00</span>
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
								      <input type="email" class="form-control" id="inputEmail4" placeholder="Email">
								    </div>
								</div>
								<div class="form-group">
								    <div class="form-check">
								      <input class="form-check-input" type="checkbox" id="gridCheck">
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
								      <input type="text" class="form-control" id="" placeholder="First name">
								    </div>
								    <div class="form-group col-md-6">
								      <input type="text" class="form-control" id="" placeholder="Last name">
								    </div>
								</div>
								<div class="form-group">
								    <input type="text" class="form-control" id="inputAddress" placeholder="Address">
								</div>
								<div class="form-group">
								    <input type="text" class="form-control" id="inputAddress2" placeholder="Apartment, studio, or floor">
								</div>
								<div class="form-row">
								    <div class="form-group col-md-6">
								      <input type="text" class="form-control" id="inputCity" placeholder="City">
								    </div>
								    <div class="form-group col-md-4">
								      <select id="inputState" class="form-control">
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
								      <input type="text" class="form-control" id="inputZip" placeholder="Zip">
								    </div>
								</div>
								<div class="form-group">
								    <input type="tel" class="form-control" id="" placeholder="Contact number">
								</div>
								<div class="form-group">
								    <div class="form-check">
								      <input class="form-check-input" type="checkbox" id="gridCheck">
								      <label class="form-check-label" for="gridCheck">Save this information for next time</label>
								    </div>
								</div>
								<div class="form-row actn-box">
									<div class="form-group col-md-6">
										<a href="product.html" class="retn"><i class="fa fa-angle-left" aria-hidden="true"></i> Return to chart</a>
									</div>
									<div class="form-group col-md-6">
								  		<!-- <button type="submit" class="btn shi-btn">Continue Shipping</button> -->
								  		<a href="checkout-shipping.html" class="btn shi-btn">Continue Shipping</a>
									</div>
								</div>
							</form>
							<!-- form end -->

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
							
								if($checkCart=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."cart WHERE cart_session_id='".SESSION_ID."'")) {

									if(mysqli_num_rows($checkCart)!=0) {

										$totalPrice=0;

										while($cartData=mysqli_fetch_assoc($checkCart)) {

											extract($cartData);
											
											switch($cart_product_id) {

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
													<h3><?php echo $productName; ?> – Pack of <?php echo $cart_product_qty ?></h3>
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
									    <input type="text" readonly class="form-control" id="" placeholder="Discount Code">
								  	</div>
								  	<div class="form-group col-md-4 ap-btn">
								  		<button type="submit" id="checkCoupon" class="btn">Apply</button>
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
									<span class="spn-rgt ship-rt">Calculate at next step</span>
								</div>
							</div>

							<div class="sub-tot2">
								<div class="sub1">
									<span class="spn-lft-1 totl">Total<br>
									<span class="ship-ad">Including GST</span></span>
									<span class="spn-rgt-1 man-t"><i class="fas fa-rupee-sign" aria-hidden="true"></i> <?php echo $totalPrice ?>.00</span>
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
		<script src="<?php echo APP_JS_LIBRARY ?>app.min.js" ></script>
		<!-- script end -->

		
	
	</body>
</html>