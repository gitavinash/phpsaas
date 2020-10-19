<!-- stay conect -->
<section class="section-content bg-gr">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<h2 class="h2-socl">Spread The Love</h2>

						<div class="social-1">
							<a href=""><img src="<?php echo USER_IMAGES ?>fb.svg"></a>
							<a href=""><img src="<?php echo USER_IMAGES ?>tweet.svg"></a>
							<a href=""><img src="<?php echo USER_IMAGES ?>insta.svg"></a>
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
							<ul>
				              <li><a href="about-us/">About Us</a></li>
				              <li><a href="">Cancellation & Refund</a></li>
				              <li><a href="">Blog</a></li>
				            </ul>
						</div>
					</div>
					<div class="col-md-12">
						<div class="acept-py">
							<!-- <span class="partnr">Our Partner <a href="">Partner Name</a></span> -->
							<span class="payout">We Accept <img src="<?php echo USER_IMAGES ?>payment-option.png"></span>
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
                        <p>&copy; All rights reserved by <?php echo SITE_NAME ?><br />Made with <img src="<?php echo IMG_LIBRARY ?>heart.png" alt=""> by <a href="http://www.itssoftworld.com" title="eCommerce development company in delhi">ITS Soft World</a> in India</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		</footer>

		<!-- sections end -->

        <!-- script start -->
        <?php require_once NOTIFICATION.'index.php'; ?>
		<script src="<?php echo JS_LIBRARY ?>flickity.min.js"></script>
		<script src="<?php echo JS_LIBRARY ?>popper.min.js"></script>
		<script src="<?php echo JS_LIBRARY ?>bootstrap.min.js"></script>
		<!-- script end -->
		
		<!-- city search popup start -->
		<div id="popup1" class="overlay-pop">
			<div class="popup">
				<h2>Here i am</h2>
				<a class="close" href="#">&times;</a>
				<div class="content-pop">
					Thank to pop me out of that button, but now i'm done so you can close this window.
				</div>
			</div>
		</div>

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
	
	</body>
</html>