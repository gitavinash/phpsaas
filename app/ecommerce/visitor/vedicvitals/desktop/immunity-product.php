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
							    <li class="breadcrumb-item active" aria-current="page">Immunity Vitals</li>
							  </ol>
							</nav>
						</div>
					</div>
					<div class="col-md-6" style="padding-bottom: 0;">
						<div class="galery-carousel">
							<div class="carousel carousel-main" data-flickity='{ "asNavFor": ".carousel-main", "contain": true, "imagesLoaded": true, "pageDots": false }'>
							  	<div class="carousel-cell"><img src="<?php echo USER_IMAGES ?>product/immunity-vitals/1.jpg" alt="Immunity Vitals - The Ultimate Immunity Booster" ></div>
							  	<div class="carousel-cell"><img src="<?php echo USER_IMAGES ?>product/immunity-vitals/2.jpg" alt="Immunity Vitals Benefits" ></div>
							  	<div class="carousel-cell"><img src="<?php echo USER_IMAGES ?>product/immunity-vitals/3.jpg" alt="Special Things About Vedic Vitals’ Products" ></div>
							  	<div class="carousel-cell"><img src="<?php echo USER_IMAGES ?>product/immunity-vitals/4.jpg" alt="Our Promise With Every Product" ></div>
							</div>

							<div class="carousel carousel-nav" data-flickity='{ "asNavFor": ".carousel-main", "contain": true, "imagesLoaded": true, "pageDots": false }'>
							  	<div class="carousel-cell"><img src="<?php echo USER_IMAGES ?>product/immunity-vitals/1.jpg" alt="Immunity Vitals - The Ultimate Immunity Booster" ></div>
							  	<div class="carousel-cell"><img src="<?php echo USER_IMAGES ?>product/immunity-vitals/2.jpg" alt="Immunity Vitals Benefits" ></div>
							  	<div class="carousel-cell"><img src="<?php echo USER_IMAGES ?>product/immunity-vitals/3.jpg" alt="Special Things About Vedic Vitals’ Products" ></div>
							  	<div class="carousel-cell"><img src="<?php echo USER_IMAGES ?>product/immunity-vitals/4.jpg" alt="Our Promise With Every Product" ></div>
							</div>
						</div>
					</div>
					<div class="col-md-6" style="padding-bottom: 0;">
						<div class="prod-detail">
							<h5 class="stock">In Stock</h5>
							<!-- <h5 class="outStock">Out of Stock</h5> -->
							<h2 id="productName" ><?php echo $product_title ?></h2>
							<p class="catag-lbel">Categories : <span> <?php echo ucwords($category_title) ?></span></p>
							<p class="pro-info">Immunity Vitals help in developing respiratory health along with a robust immune system. Immunity Vitals is an extraordinary formulation that uses super herbs, known to Boost Immunity, Promote Respiratory health, decrease inflammation and remove the symptoms of a weak Immune System.</p>
							<ul>
							<li><i class="fas fa-circle"></i> Increase Energy Levels.</li>
							<li><i class="fas fa-circle"></i> Defends Against Virus/Bacteria.</li>
							<li><i class="fas fa-circle"></i> 15 Classic Herbs To Build Immunity.</li>
							</ul>
							<!-- <p><strong>Note:- </strong>Tar Free should not be consumed during pregnancy.</p> -->					
							<br>
							
							<form action="qtyForm" >
								<div class="quantaty">
									<div class="quant-bx">
										Quantity :
										<select id="qty" >
											<option value="1_1_369" >1</option>
											<option value="1_2_664" >2</option>
											<option value="1_3_941" >3</option>
										</select> 
									</div>
								</div>
								<div class="cart-box">
									<a href="#" class="cart-ad-btn addToCart" >Add to Cart</a>
								</div>
							</form>

							<!-- grab deal start -->
							<div class="grab-deal">
								<div class="grab-bundl">
									<h3>More Care, More Savings</h3>
									<span>Buy 1 For <i class="fas fa-rupee-sign"></i> 369/-</span>
									<span>Buy 2 For <i class="fas fa-rupee-sign"></i> 664/- ( Flat 10% OFF )</span>
									<span>Buy 3 For <i class="fas fa-rupee-sign"></i> 941/- ( Flat 15% OFF )</span>
								</div>
							</div>
							<!-- grab deal end -->

							<div class="social-2"> <span>Share </span>
								<a href=""><i class="fab fa-facebook-square" aria-hidden="true"></i></a>
								<a href=""><i class="fab fa-twitter-square" aria-hidden="true"></i></a>
								<a href=""><i class="fab fa-instagram" aria-hidden="true"></i></a>
								<a href=""><i class="fab fa-linkedin" aria-hidden="true"></i></a>
							</div>
						</>
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
							<h4>15 organic ingredients</h4>
						</div>
					</div>
					<div class="col-md-6">
						<div class="gradint-box">
							<div class="row">
								<div class="col-6">
									<ul class="ingredients" >
										<li>Mulethi</li>
										<li>Tulsi</li>
										<li>Ajwain</li>
										<li>Haldi</li>
										<li>Lahsun</li>
										<li>Giloy</li>
										<li>Dalchini</li>
										<li>Banafsha</li>
									</ul>
								</div>
								<div class="col-6">
									<ul class="ingredients" >
										<li>Draksha</li>
										<li>Long Pepper</li>
										<li>Ashwagandha</li>
										<li>Kali Mirch</li>
										<li>Adhrak</li>
										<li>Pippali</li>
										<li>Jeera</li>
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
								<p><strong>Immunity Vitals</strong> is recommended for children above 5 years and all other age groups irrespective of gender. It helps in fighting against bacteria in current conditions.</p>
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