		<section class="section-content bg-covid" >
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="safety-head">
							<h3>Your Safety is our Priority!</h3>
							<p>Steps we are taking to keep you safe</p>
						</div>
					</div>
					<div class="col-md-6">
						<div class="list-covid">
							<div class="covid-box">
								<div class="covid-img">
									<img src="<?php echo USER_IMAGES ?>fasanitization.png">
								</div>
								<div class="covid-name">
									<h4>Sanitization</h4>
									<p>Sanitize our hands & kitchen surface in every 4 hours</p>
								</div>
							</div>

							<div class="covid-box">
								<div class="covid-img">
									<img src="<?php echo USER_IMAGES ?>fasanitization.png">
								</div>
								<div class="covid-name">
									<h4>Contact Less Delivery</h4>
									<p>Maintaining the safe distance</p>
								</div>
							</div>

							<div class="covid-box">
								<div class="covid-img">
									<img src="<?php echo USER_IMAGES ?>facontactless.png">
								</div>
								<div class="covid-name">
									<h4>Temperature Checks</h4>
									<p>Real-time Temperature Tracking of kitchen & delivery staff</p>
								</div>
							</div>

							<div class="covid-box">
								<div class="covid-img">
									<img src="<?php echo USER_IMAGES ?>fasafety.png">
								</div>
								<div class="covid-name">
									<h4>Compulsory Mask & Gloves</h4>
									<p>Always on mask & Gloves environment</p>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="you-tube">
							<iframe width="100%" height="280" src="https://www.youtube.com/embed/bqy4X3FF1rE" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
						</div>	
					</div>
					
				</div>
			</div>
		</section>

		<!-- stay conect -->
		<section class="section-content bg-gr dn-s">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<h2 class="h2-socl">Spread The Love</h2>

						<div class="social-1">
							<a href=""><img src="<?php echo USER_IMAGES ?>fb.svg"></a>
							<a href=""><img src="<?php echo USER_IMAGES ?>tweet.svg"></a>
							<a href=""><img src="<?php echo USER_IMAGES ?>linkedin.svg"></a>
							<a href=""><img src="<?php echo USER_IMAGES ?>pinterest.svg"></a>
							<!-- <a href=""><img src="<?php echo USER_IMAGES ?>insta.svg"></a> -->
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- stay conect end -->

		<footer>
		<div class="top-section-footer bg-gr">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="fot-box">	
							<ul class="dn-s">
				              <li><a href="index">About Us</a></li>
				              <li><a href="">Coupons & Deals</a></li>
				              <li><a href="">Cancellation & Refund</a></li>
				              <li><a href="">Career</a></li>
				              <li><a href="">Media</a></li>
				              <li><a href="">Reviews</a></li>
				              <li><a href="">Blog</a></li>
				            </ul>
				            <ul class="dn-l">
				              <li><a href="">Terms</a></li>
				              <li><a href="">Cancellation</a></li>
				              <li><a href="">Privacy</a></li>
				            </ul>
						</div>
					</div>
					<div class="col-md-12">
						<div class="acept-py">
							<span class="partnr">Our Partner <a href="">Partner Name</a></span>
							<span class="payout dn-s">We Accept <img src="<?php echo USER_IMAGES ?>desktop-payment-option.png"></span>
							<span class="payout dn-l">We Accept <img src="<?php echo USER_IMAGES ?>paymentFooter.png"></span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="section-footer">
			<div class="container">
				<div class="row">
					<div class="col-md-12 col-12">
						<div class="footer-btm">
							<p>© 2020 . All Rights Reserved</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		</footer>

		<div class="popup" id="addToCardPopUp" style="display:none" >
			<div class="description-pop">
		        <h6>JUST ADDED TO YOUR CART</h6>
		        <div class="links-pop" id="closeAddToCartPopUp">
		        	<a href="#" ><i class="fa fa-times" aria-hidden="true"></i></a>
		     	</div>
	      	</div>
		    <div class="img-pop">
			    <img src="" id="selectedImg" >
			    <div class="prod-name">
			      	<h5 id="selectedName" ></h5>
			    </div>
			    <span id="selectedQty" ></span>
		    </div>
		    <div class="cart-duc">
		      	<!-- <a href="#" class="del-b remove-cart-btn removeFromCart" id="" ><i class="fas fa-trash" aria-hidden="true"></i></a> -->
		      	<a href="/checkout/" class="buy-b cart-ad-btn">Proceed to Checkout</a>
		    </div>
	    </div>

		<!-- sections end -->

		<!-- script start -->
		<?php include_once NOTIFICATION.'index.php'; ?>
		<script src="<?php echo JS_LIBRARY ?>flickity.min.js"></script>
		<script src="<?php echo JS_LIBRARY ?>popper.min.js"></script>
		<script src="<?php echo JS_LIBRARY ?>bootstrap.min.js"></script>
		<script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js"></script>
		<script src="<?php echo USER_JS ?>app.min.js"></script>
		<!-- script end -->

	</body>
</html>