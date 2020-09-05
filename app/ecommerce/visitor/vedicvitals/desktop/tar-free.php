<?php

	require_once 'header.php';

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
							    <li class="breadcrumb-item"><a href="#">Product</a></li>
							    <li class="breadcrumb-item active" aria-current="page">Tar Free</li>
							  </ol>
							</nav>
						</div>
					</div>
					<div class="col-md-6" style="padding-bottom: 0;">
						<div class="galery-carousel">
							<div class="carousel carousel-main" data-flickity>
							  	<div class="carousel-cell"><img src="<?php echo USER_IMAGES ?>product/tar-free/1.jpg"></div>
							  	<div class="carousel-cell"><img src="<?php echo USER_IMAGES ?>product/tar-free/2.jpg"></div>
							  	<div class="carousel-cell"><img src="<?php echo USER_IMAGES ?>product/tar-free/3.jpg"></div>
							  	<div class="carousel-cell"><img src="<?php echo USER_IMAGES ?>product/tar-free/4.jpg"></div>
							</div>

							<div class="carousel carousel-nav"
							  data-flickity='{ "asNavFor": ".carousel-main", "contain": true, "pageDots": false }'>
							  	<div class="carousel-cell"><img src="<?php echo USER_IMAGES ?>product/tar-free/1.jpg"></div>
							  	<div class="carousel-cell"><img src="<?php echo USER_IMAGES ?>product/tar-free/2.jpg"></div>
							  	<div class="carousel-cell"><img src="<?php echo USER_IMAGES ?>product/tar-free/3.jpg"></div>
							  	<div class="carousel-cell"><img src="<?php echo USER_IMAGES ?>product/tar-free/4.jpg"></div>
							</div>
						</div>

					</div>
					<div class="col-md-6" style="padding-bottom: 0;">
						<div class="prod-detail">
							<h5 class="stock">In Stock</h5>
							<!-- <h5 class="outStock">Out of Stock</h5> -->
							<h2>Tar Free</h2>
							<p class="catag-lbel">Categories : <span> Health Product</span></p>
							<p class="pro-info">Let your lungs breathe again by flushing out tar accumulated in your lungs Naturally without any side effects.</p>
							<p class="pro-info">No-Tar is a scientific ayurvedic formulation backed with high quality herbs to detox your lungs from the tar accumulated by consumption of tobacco & pollution.</p>

							<ul>
								<li><i class="fas fa-circle"></i> Removes harmful toxins from lungs responsible for lung cancer.</li>
								<li><i class="fas fa-circle"></i> Removes congestion in lungs & helps to breathe freely.</li>
								<li><i class="fas fa-circle"></i> Improves stamina and allows you to perform physical work without breathlessness.</li>
							</ul>

							<p><strong>Note:- </strong>Tar Free should not be consumed during pregnancy.</p>
							
							<br>
							
							<form action="qtyForm" >
								<div class="quantaty">
									<div class="quant-bx">
										Quantity :
										<select id="qty" >
											<option value="volvo">1</option>
											<option value="saab">2</option>
											<option value="opel">3</option>
										</select> 
									</div>
								</div>
								<div class="cart-box">
									<a href="#" id="checkout" class="cart-ad-btn" >Add to Cart</a>
								</div>
							</form>

							<!-- grab deal start -->
							<div class="grab-deal">
								<div class="grab-bundl">
									<h3>More Care, More Savings</h3>
									<span>Buy 1 For <i class="fas fa-rupee-sign"></i> 345/-</span>
									<span>Buy 2 For <i class="fas fa-rupee-sign"></i> 621/- ( Flat 10% OFF )</span>
									<span>Buy 3 For <i class="fas fa-rupee-sign"></i> 880/- ( Flat 15% OFF )</span>
								</div>
							</div>
							<!-- grab deal end -->

							<div class="social-2"> <span>Share </span>
								<a href=""><i class="fab fa-facebook-square" aria-hidden="true"></i></a>
								<a href=""><i class="fab fa-twitter-square" aria-hidden="true"></i></a>
								<a href=""><i class="fab fa-instagram" aria-hidden="true"></i></a>
								<a href=""><i class="fab fa-linkedin" aria-hidden="true"></i></a>
							</div>
						</div>
					</div>
					
				</div>
			</div>
		</section>
		<!-- section global platform end -->

		<!-- comparison start
		
		<section class="section-content ">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="compare-head">
							<h2>Healthy made easy.</h2>
						</div>
					</div>

					<div class="col-md-12">
						<div id="image-slider1" class="twentytwenty-container">
			                <a href="/products/daily-greens">
			                    <picture>
			                        <source media="(max-width: 767px)" srcset="https://cdn.shopify.com/s/files/1/0240/2942/9840/files/50-50-slider-mobile-1.jpg?v=1592810396">
			                        <img src="https://cdn.shopify.com/s/files/1/0240/2942/9840/files/50-50-slider-desktop-2.jpg?v=1592810396" alt="natural antioxidants" class="img-responsive">
			                    </picture>
			                </a>
			                <a href="/products/daily-greens">
			                    <picture>
			                        <source media="(max-width: 767px)" srcset="https://cdn.shopify.com/s/files/1/0240/2942/9840/files/50-50-slider-mobile-2.jpg?v=1592810396">
			                        <img src="https://cdn.shopify.com/s/files/1/0240/2942/9840/files/50-50-slider-desktop-1.jpg?v=1592810396" alt="natural antioxidants" class="img-responsive">
			                    </picture>
			                </a>
			            </div>
					</div>

				</div>
			</div>
		</section>
		
		comparison end -->

		<section class="section-content prodct-fiz">
			<div class="container">
				<div class="row">
					<div class="col-md-6">
						<div class="fiz-box">
							<h3>From</h3>
							<h2>leaves<br>to liquid.</h2>
							<img src="<?php echo USER_IMAGES ?>product/Ingredients.png" width="100%" class="py-3">
							<h4>8 organic ingredients</h4>
						</div>
					</div>
					<div class="col-md-6">
						<div class="gradint-box">
							<div class="row">
								<div class="col-6">
									<ul class="ingredients" >
										<li>Mulethi</li>
										<li>Tulsi</li>
										<li>Trifala</li>
										<li>Haldi</li>
									</ul>
								</div>
								<div class="col-6">
									<ul class="ingredients" >
										<li>Lahsun</li>
										<li>Manjitha</li>
										<li>Shikakai</li>
										<li>Trikatu</li>
									</ul>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-12 mt-4">
						<hr color="#ccc">
						<div class="sec-1">
							<div class="grad-head">
								<p class="product-fact" ><span><i class="fa fa-quote-left"></i></span>Approximately two third of India’s rural population, who comprises 70% of the Indian population, use Ayurveda for their primary health care needs<span><i class="fa fa-quote-right"></i></span></p>
							</div>
						</div>
					</div>

					<div class="col-md-12 organic">
						<h2>VEDIC VICTALS <span style="font-weight:normal">PRODUCTS ARE</span></h2>
					</div>

					<div class="col-md-3 col-6 text-center">
						<img src="<?php echo USER_IMAGES ?>product/icon_one.png" width="150">
						<h3 class="text-dark py-2">Sugar Free</h3>
					</div>

					<div class="col-md-3 col-6 text-center">
						<img src="<?php echo USER_IMAGES ?>product/icon_two.png" width="150">
						<h3 class="text-dark py-2">100% Ayurvedic</h3>
					</div>

					<div class="col-md-3 col-6 text-center">
						<img src="<?php echo USER_IMAGES ?>product/non-gmo-icon.png" width="150">
						<h3 class="text-dark py-2">Non-GMO</h3>
					</div>

					<div class="col-md-3 col-6 text-center">
						<img src="<?php echo USER_IMAGES ?>product/icon_four.png" width="150">
						<h3 class="text-dark py-2">Balances Body</h3>
					</div>


				</div>
			</div>
		</section>


		<section class="section-content who-sectn">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="sec-2">
							<div class="who-1">
								<h2 class="pb-3 pt-0">Who Should Consume?</h2>
							</div>
							<div class="who-1">
								<p>Anyone and everyone who is exposed to pollution & consumes tobacco (smoking) should consume Tar Free on a regular basis.</p>
							</div>
						</div>
					</div>

				</div>
			</div>
		</section>

		<section>
			<div class="container-fluid" style="padding: 0;" >
				<div class="row" >
					<div class="col-md-12">
						<img src="<?php echo USER_IMAGES ?>product/Bottom-Setup.jpg" width="100%">
					</div>
				</div>
			</div>
		</section>
<?php

	require_once 'footer.php';

?>