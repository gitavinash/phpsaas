
	
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
									<img src="image/fasanitization.png">
								</div>
								<div class="covid-name">
									<h4>Sanitization</h4>
									<p>Sanitize our hands & kitchen surface in every 4 hours</p>
								</div>
							</div>

							<div class="covid-box">
								<div class="covid-img">
									<img src="image/fasanitization.png">
								</div>
								<div class="covid-name">
									<h4>Contact Less Delivery</h4>
									<p>Maintaining the safe distance</p>
								</div>
							</div>

							<div class="covid-box">
								<div class="covid-img">
									<img src="image/facontactless.png">
								</div>
								<div class="covid-name">
									<h4>Temperature Checks</h4>
									<p>Real-time Temperature Tracking of kitchen & delivery staff</p>
								</div>
							</div>

							<div class="covid-box">
								<div class="covid-img">
									<img src="image/fasafety.png">
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
							<iframe width="100%" height="280" src="https://www.youtube.com/embed/WDmrzNh5Ph8" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
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
							<a href=""><img src="image/fb.svg"></a>
							<a href=""><img src="image/tweet.svg"></a>
							<a href=""><img src="image/linkedin.svg"></a>
							<a href=""><img src="image/pinterest.svg"></a>
							<!-- <a href=""><img src="image/insta.svg"></a> -->
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
							<span class="payout dn-s">We Accept <img src="image/desktop-payment-option.png"></span>
							<span class="payout dn-l">We Accept <img src="image/paymentFooter.png"></span>
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

		<!-- sections end -->

		<!-- script start -->
		<script src="js/jquery-3.4.1.slim.min.js" ></script>
		<script src="js/flickity.pkgd.min.js"></script>
		<script src="js/popper.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
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