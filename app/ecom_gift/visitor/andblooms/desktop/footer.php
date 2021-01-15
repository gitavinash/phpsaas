
	
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
						<div class="you-tube" >
							<iframe width="100%" height="280" src="https://www.youtube.com/embed/KeyGY1nuifk" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
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
							<a href="https://www.facebook.com/andblooms"><img src="<?php echo USER_IMAGES ?>facebook.png"></a>
							<a href="https://www.instagram.com/andblooms/"><img src="<?php echo USER_IMAGES ?>instagram.png"></a>
							<!-- <a href=""><img src="<?php echo USER_IMAGES ?>tweet.svg"></a>
							<a href=""><img src="<?php echo USER_IMAGES ?>linkedin.svg"></a>
							<a href=""><img src="<?php echo USER_IMAGES ?>pinterest.svg"></a> -->
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
				              <li><a href="/about-us/">About Us</a></li>
				              <!-- <li><a href="">Coupons & Deals</a></li> -->
				              <li><a href="/cancellation---refund/">Cancellation & Refund</a></li>
				              <!-- <li><a href="/career/">Career</a></li>
				              <li><a href="/media/">Media</a></li> -->
				              <!-- <li><a href="reviews/">Reviews</a></li>
				              <li><a href="/blog/">Blog</a></li> -->
				              <li><a href="/terms-and-conditions/">Terms &amp; conditions</a></li>
				              <li><a href="/cancellation/">Cancellation</a></li>
				              <li><a href="/privacy-policy/">Privacy policy</a></li>
				            </ul>
				            <ul class="dn-l">
				              <li><a href="/about-us/">About Us</a></li>
				              <!-- <li><a href="">Coupons & Deals</a></li> -->
				              <li><a href="/cancellation---refund/">Cancellation & Refund</a></li>
				              <!-- <li><a href="/career/">Career</a></li>
				              <li><a href="/media/">Media</a></li> -->
				              <!-- <li><a href="reviews/">Reviews</a></li>
				              <li><a href="/blog/">Blog</a></li> -->
				              <li><a href="/terms-and-conditions/">Terms &amp; conditions</a></li>
				              <li><a href="/cancellation/">Cancellation</a></li>
				              <li><a href="/privacy-policy/">Privacy policy</a></li>
				            </ul>
						</div>
					</div>
					<div class="col-md-12">
						<div class="acept-py">
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
							<p>© 2020 <?php echo SITE_NAME; ?> . All Rights Reserved</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="whatsapp">
			<a href="https://wa.me/919910744119?text=<?php echo ((isset($link_type)) ? (($link_type=='product') ? urlencode('i want to know about '.$product_title.' '.SITE_URL.ACTIVE_URL) : 'Hello' ) : 'Hello' ) ?>"><img src="<?php echo IMG_LIBRARY ?>whatsapp.png" alt=""></a>
		</div>
		</footer>

		<!-- sections end -->
		<?php include_once NOTIFICATION.'index.php' ?>
	
		<script type="text/javascript" src="<?php echo JS_LIBRARY ?>flickity.min.js"></script>
		<script type="text/javascript" src="<?php echo JS_LIBRARY ?>bootstrap/bootstrap.min.js"></script>
		<script type="text/javascript" src="<?php echo JS_LIBRARY ?>popper.min.js"></script>
		<script type="text/javascript" src="<?php echo PLUGIN_LIBRARY ?>moment/moment.min.js"></script>
		<script type="text/javascript" src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" ></script>
		<script type="text/javascript" src="<?php echo PLUGIN_LIBRARY ?>slick/slick.min.js" ></script>
		<script type="text/javascript" src="<?php echo PLUGIN_LIBRARY ?>datepicker/datepicker.min.js" ></script>
		<script type="text/javascript" src="<?php echo USER_JS ?>app.min.js"></script>

		<!-- city search popup end -->

		<script type="text/javascript">
			$(document).ready(function() {
			  $('.card-slider').slick({
			    dots: false,
			    arrows: true,
			    slidesToShow: 4,
			    infinite: false,
			    responsive: [
			      {
			        breakpoint: 800,
			        settings: {
			          slidesToShow: 3
			        }
			      },
			      {
			        breakpoint: 600,
			        settings: {
			          slidesToShow: 2
			        }
			      }
			    ]
			  });
			});
		</script>
		
		<!-- search in mobile div show -->

		<script type="text/javascript">
			function divSearchPop() {
			  var x = document.getElementById("search-mob-div");
			  if (x.style.display === "none") {
			    x.style.display = "block";
			  } else {
			    x.style.display = "none";
			  }
			}

			$("#close").click(function(){
			  $("searchInput").hide();
			});

		</script>
	</body>
</html>