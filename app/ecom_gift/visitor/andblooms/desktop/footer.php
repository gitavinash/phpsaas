
	
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
						
						<!-- Testimonial slider -->
						<h2 class="testimonial-slider-title">Testimonials</h2>
						<div class="testimonial-slider">
							<div class="slider">
								<p>Shalini has been a great friend in my school years and I can vouch for her amazing sense of aesthetics.She brings beauty to whatever she does. Whether it’s the way she dresses, keeps her house or runs her business.She brings value and commitment to each of these spheres. Her efforts have translated into a blooming flower business which brings joy to so many lives! ♥️</p>
								<span>Pooja Sinha</span>
							</div>
							<div class="slider">
								<p>The best ever flowers and arrangements you can buy. I have used there service many times....and every time they surprise me. Its like poetry and art with flowers. Truly the person running it loves flowers clearly evident from the selection. You cannot go wrong with them. Today I received my own Valentine day arrangement from my husband and so this review. It absolutely made my day.</p>
								<span>Nidhi Jain Seth</span>
							</div>
							<div class="slider">
								<p>They say get flowers from someone who lives flowers and that’s the case when your visit this store. It’s full of life and shall light a spark into yours the moment you visit them as well. A must visit if you love flowers. You won’t regret it.</p>
								<span>Rad Rider</span>
							</div>
							<div class="slider">
								<p>I’ve ordered from And blooms at various occasions including planning my Diwali gifts to a lot of clients! Stunning arrangements, extremely customised quick service and fresh exotic flowers are their backbone! Love the staff and the approach and the passion towards bespoke flower gifting! Very niche and very exclusive!</p>
								<span>Ankush Duggal</span>
							</div>
						</div>

					</div>
					<div class="col-md-6">
						<div class="you-tube" >
							<iframe width="100%" height="280" src="https://www.youtube.com/embed/9-gPXnnKUS8" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
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
							<p>© 2020 <?php echo SITE_NAME; ?>. All Rights Reserved</p>
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
	
		<script type="text/javascript" src="<?php echo JS_LIBRARY ?>bootstrap/bootstrap.min.js"></script>
		<script type="text/javascript" src="<?php echo JS_LIBRARY ?>popper.min.js"></script>
		<script type="text/javascript" src="<?php echo JS_LIBRARY ?>flickity.min.js"></script>
		<script type="text/javascript" src="<?php echo PLUGIN_LIBRARY ?>moment/moment.min.js"></script>
		<script type="text/javascript" src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" ></script>
		<script type="text/javascript" src="<?php echo PLUGIN_LIBRARY ?>slick/slick.min.js" ></script>
		<script type="text/javascript" src="<?php echo PLUGIN_LIBRARY ?>datepicker/datepicker.min.js" ></script>
		<script type="text/javascript" src="<?php echo USER_JS ?>app.min.js"></script>

		<!-- city search popup end -->

		<script type="text/javascript">
			$(document).ready(function() {

			  	$('.card-ban').slick({
					slidesToShow: 1,
					slidesToScroll: 1,
					autoplay: true,
					autoplaySpeed: 2000,
					dots: false,
					arrows: false,
			  	});

			  	$('.testimonial-slider').slick({
					slidesToShow: 1,
					slidesToScroll: 1,
					autoplay: true,
					autoplaySpeed: 2000,
					dots: false,
					arrows: false,
			  	});

			  	$('.product-slider').slick({
					slidesToShow: 4,
					slidesToScroll: 1,
					autoplay: false,
					autoplaySpeed: 2000,
					dots: false,
					arrows: false,
					responsive: [
						{
						breakpoint: 1024,
						settings: {
							slidesToShow: 3,
							slidesToScroll: 3,
							infinite: true,
							dots: true
						}
						},
						{
						breakpoint: 600,
						settings: {
							slidesToShow: 2,
							slidesToScroll: 1
						}
						},
						{
						breakpoint: 480,
						settings: {
							slidesToShow: 2,
							slidesToScroll: 1
						}
						}
					]
			  	});

				$('.product-main-slider').slick({
					slidesToShow: 1,
					slidesToScroll: 1,
					arrows: false,
					dots: false,
					fade: true,
					asNavFor: '.product-main-slider-nav'
				});
				$('.product-main-slider-nav').slick({
					slidesToShow: 4,
					slidesToScroll: 2,
					asNavFor: '.product-main-slider',
					dots: false,
					arrows: false,
					focusOnSelect: true
				});

			});
		</script>
		
		<!-- search in mobile div show -->

		<script type="text/javascript">
		
			function divSearchPop(e) {

				e.preventDefault();

			  	var x = document.getElementById("search-mob-div");

			  	if (x.style.display === "none") {
			    	x.style.display = "block";
			  	} else {
			    	x.style.display = "none";
			  	}

			}

			$('#searchBtn').on('click', function(e) {
				
				e.preventDefault();

			});

			$("#close").click(function(){
			  $("searchInput").hide();
			});

		</script>

		<?php
		
		if(SITE_VERSION=='desktop') { ?>

			<script>

				// Height of product details dev

				var productDetailsHeight = document.getElementById('productDetails').offsetHeight - 150;

				function sticky_relocate() {

					var window_top = $(window).scrollTop();
					
					//var div_top = $('#sticky-anchor').offset().top;
					if (window_top >= 300 ) {
						// alert('i m ready');
						$('#sticky').addClass('stick');
					} else {

						$('#sticky').removeClass('stick');

					}

				}

				$(function() {
					$(window).scroll(sticky_relocate);
					sticky_relocate();
				});

				$(function(){
					$(window).scroll(function(e) {
						if($(this).scrollTop()>productDetailsHeight){
							$('#sticky').removeClass('stick'); 
						}
					});
				});
				
			</script>

		<?php
		}

		?>

	</body>
</html>