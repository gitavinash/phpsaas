<!-- stay conect -->
		<section class="section-content socl-box">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="social-1"> <span>Stay Connected </span>
							<a href="//facebook.com/vedicvitals/"><i class="fab fa-facebook-square" aria-hidden="true"></i></a>
							<a href="//instagram.com/vedicvitals/"><i class="fab fa-instagram" aria-hidden="true"></i></a>
							<a href="//twitter.com/vedicvitals/"><i class="fab fa-twitter-square" aria-hidden="true"></i></a>
						</div>
					</div>
				</div>
			</div>
		</section>

		<footer>
			<div class="top-section-footer">
				<div class="container">
					<div class="row">
						<div class="col-md-3 col-6">
							<div class="fot-box">	
								<h3>Quick Links</h3>
								<ul class="list-unstyled">
									<li><a href="#">Home</a></li>
									<li><a href="#">Our Story</a></li>
									<li><a href="#">Contact Us</a></li>
					            </ul>
							</div>
						</div>

						<div class="col-md-3 col-6">
							<div class="fot-box">	
								<h3>Products</h3>
								<ul class="list-unstyled">
									<li><a href="#">Immunity Vitals</a></li>
									<li><a href="#">Tar Free</a></li>
									<li><a href="#">Gyno Vitals</a></li>
									<li><a href="#">V-Liver Vitals</a></li>
					            </ul>
							</div>
						</div>

						<div class="col-md-3 col-6">
							<div class="fot-box">	
								<h3>Information</h3>
								<ul class="list-unstyled">
									<li><a href="#">Privacy Policy</a></li>
									<li><a href="#">Disclaimer</a></li>
									<li><a href="#">Return Policy</a></li>
									<li><a href="#">Terms of Use</a></li>
					            </ul>
							</div>
						</div>

						<div class="col-md-3 col-6">
							<div class="fot-box">
								<h3>Contact Us</h3>
								<p>622-A, Ground Floor, Guru Ram Das Nagar, Laxmi Nagar, DELHI - 110051, India</p>
								<p>care@vedicvitals.com</p>
								<p>+91 9310142836</p>

								<section class="hideOnMobile" >
									<div class="container-fluid">
										<div class="row">

											<div class="col-3 text-center p-2 bot">
												<img src="<?php echo USER_IMAGES ?>secure-payment.png" width="100%">
												<p class="text-dark py-2">Safe Payments</p>
											</div>
									
											<div class="col-3 text-center p-2 bot">
												<img src="<?php echo USER_IMAGES ?>24-hours-support.png" width="100%">
												<p class="text-dark py-2">24 Hour Support</p>
											</div>
									
											<div class="col-3 text-center p-2 bot">
												<img src="<?php echo USER_IMAGES ?>free-shipping.png" width="100%">
												<p class="text-dark py-2">Free shipping</p>
											</div>
									
											<div class="col-3 text-center p-2 bot">
												<img src="<?php echo USER_IMAGES ?>made-with-love.png" width="100%">
												<p class="text-dark py-2">Made With love</p>
											</div>
									
										</div>
									</div>
								</section>
								
							</div>
						</div>
						
					</div>
					<div class="row">
						<div class="col-12">
							<section class="hideOnDesktop" >
									<div class="container-fluid">
										<div class="row">

											<div class="col-3 text-center p-2 bot">
												<img src="<?php echo USER_IMAGES ?>secure-payment.png" width="100%">
												<p class="text-dark py-2">Safe Payments</p>
											</div>
									
											<div class="col-3 text-center p-2 bot">
												<img src="<?php echo USER_IMAGES ?>24-hours-support.png" width="100%">
												<p class="text-dark py-2">24 Hour Support</p>
											</div>
									
											<div class="col-3 text-center p-2 bot">
												<img src="<?php echo USER_IMAGES ?>free-shipping.png" width="100%">
												<p class="text-dark py-2">Free shipping</p>
											</div>
									
											<div class="col-3 text-center p-2 bot">
												<img src="<?php echo USER_IMAGES ?>made-with-love.png" width="100%">
												<p class="text-dark py-2">Made With love</p>
											</div>
									
										</div>
									</div>
							</section>
						</div>
					</div>
				</div>
			</div>
			<div class="section-footer">
				<div class="container">
					<div class="row">																	
						<div class="col-md-12 col-12">
							<div class="footer-btm">
								<p>&copy; All rights reserved by Vedic Vitals Enterprise<br />Made with <i class="fa fa-heart"></i> by <a href="http://www.itssoftworld.com" title="Software development company in delhi">ITS Soft World</a> in India</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</footer>

		<!-- sections end -->

		<!-- script start -->

		<?php include_once NOTIFICATION.'index.php' ?>
	
		<script type="text/javascript" src="<?php echo USER_JS ?>jquery-3.4.1.slim.min.js"></script>
		<script type="text/javascript" src="<?php echo USER_JS ?>comaprison-slider.js"></script>
		<script type="text/javascript" src="<?php echo USER_JS ?>twentytwenty-slider.js"></script>
		<script type="text/javascript" src="<?php echo USER_JS ?>flickity.pkgd.min.js"></script>
		<script type="text/javascript" src="<?php echo JS_LIBRARY ?>bootstrap/bootstrap.min.js"></script>
		<!-- script end -->

		<!-- wish list start -->
		<script type="text/javascript">
			$('.icon-wishlist').on('click', function(){
			  $(this).toggleClass('in-wishlist');
			});
		</script>

		<!-- click js popup -->
		<script type="text/javascript">
			function show_project(theId){
				document.getElementById(theId).style.display = "inline";
			}

			function hide_project(theId){
				document.getElementById(theId).style.display = "none";
			}
		</script>

		<div class="popup" id="popup-1">
			<div class="description-pop">
		        <h4>JUST ADDED TO YOUR CART</h4>
		        <div class="links-pop">
		        	<a href="" onclick="hide_project('popup-1')"><i class="fa fa-times" aria-hidden="true"></i></a>
		     	</div>
	      	</div>
		    <div class="img-pop">
			    <img src="https://26zr682s5m7730zgrtj0stqr-wpengine.netdna-ssl.com/wp-content/uploads/2020/05/Herbocleanse-PLUS-200ml_Back-300x300.jpg">
			    <div class="prod-name">
			      	<h5>Lipoherb: Ayurvedic Medicine for Healthy Weight Management – Pack of Three</h5>
			    </div>
			    <span>
			    	Qty : 1
			    </span>
		    </div>
		    <div class="cart-duc">
		      	<a href="" class="del-b"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
		      	<a href="" class="buy-b">Proceed to Checkout</a>
		    </div>
	    </div>

	    <!-- popup end -->
	
	
	</body>
</html>