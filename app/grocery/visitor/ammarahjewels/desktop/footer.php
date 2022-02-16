		<!-- testimonial start -->
		<section class="section-content-2">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="heading2">
							<h2>Testimonial</h2>
							<hr class="bdr-gry">
							<p>We know that your jewelry means a lot to you, and that's why we offer a 1-year warranty on it.</p>
						</div>
					</div>

					<div class="col-md-12">
						<div class="carousel carousel-home-bestlook" data-flickity='{ "groupCells": true, "freeScroll": false, "contain": true }'>
							<div class="carousel-cell-bestlook">
								<a href="">
								<div class="col-card-3">
									<div class="event-card">
										
										<div class="img-cd-look" style="background-image:url(<?php echo USER_IMAGES ?>P4_Homepage.jpg);">
											<div class="over-screen-look">
											<img class="hovr-img" src="<?php echo USER_IMAGES ?>Rachel.png">
											<h5>Cathrine</h5>
											<p>The snake earrings or serpent earrings are available in black or gold finish in a variety of stones.</p>
										</div>
										</div>
									</div>
								</div>
								</a>
							</div>
							<div class="carousel-cell-bestlook">
								<a href="">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd-look" style="background-image:url(<?php echo USER_IMAGES ?>P8_BeMaadandYou.jpg);">
											<div class="over-screen-look">
												<img class="hovr-img" src="<?php echo USER_IMAGES ?>Rachel.png">
											<h5>Cathrine</h5>
											<p>The snake earrings or serpent earrings are available in black or gold finish in a variety of stones.</p>
											</div>
										</div>
									</div>
								</div>
								</a>
							</div>
							<div class="carousel-cell-bestlook">
								<a href="">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd-look" style="background-image:url(<?php echo USER_IMAGES ?>P3_Homepage.jpg);">
											<div class="over-screen-look">
												<img class="hovr-img" src="<?php echo USER_IMAGES ?>Rachel.png">
											<h5>Cathrine</h5>
											<p>The snake earrings or serpent earrings are available in black or gold finish in a variety of stones.</p>
											</div>
										</div>
									</div>
								</div>
								</a>
							</div>
							<div class="carousel-cell-bestlook">
								<a href="">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd-look" style="background-image:url(<?php echo USER_IMAGES ?>P8_BeMaadandYou.jpg);">
											<div class="over-screen-look">
												<img class="hovr-img" src="<?php echo USER_IMAGES ?>Rachel.png">
											<h5>Cathrine</h5>
											<p>The snake earrings or serpent earrings are available in black or gold finish in a variety of stones.</p>
											</div>
										</div>
									</div>
								</div>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- testimonial end -->

		<footer>
			<div class="top-section-footer ">
				<div class="container">
					<div class="row">
						<div class="col-md-6">
							<div class="foter-left">
								<h1><a href="/"><img src="<?php echo SITE_LOGO ?>" alt=""></a></h1>
								<p><?php echo SITE_DESC ?></p>

								<div class="cont-nfo">
									<ul>
										<li><span><i class="fas fa-map-marker-alt"></i></span> Ammarah Delhi, 110057, India</li>
										<li><span><i class="fas fa-phone-alt"></i></span> 8058758008</li>
										<li><span><i class="fas fa-envelope"></i></span> contact@ammarah.com</li>
									</ul>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="fot-box">	
								<h3>Quick links</h3>
								<ul class="list-unstyled">
									<li><a href="/">Home</a></li>
									<li><a href="/about-us/" >The Brand</a></li>
									<li><a href="/faq/" >Jewery Faq</a></li>
									<li><a href="/how-to-wear/" >How to wear</a></li>
									<li><a href="/contact-us/">Contact Us</a></li>
					            </ul>
							</div>
						</div>
						<div class="col-md-3">
							<div class="fot-box">	
								<h3>Information</h3>
								<ul class="list-unstyled">
									<li><a href="/privacy-policy/">Privacy Policy</a></li>
									<li><a href="/disclaimer/">Disclaimer</a></li>
									<li><a href="/return-policy/">Return Policy</a></li>
									<li><a href="/terms-of-use/">Terms of Use</a></li>
					            </ul>
							</div>
						</div>

					</div>
				</div>
			</div>
			<div class="section-footer">
				<div class="container">
					<div class="row">
						<div class="col-md-6 col-12">
							<div class="footer-btm">
								<p>Copyright Ammarh <span>© 2020</span> All Right Reserved</a>
							</div>
						</div>
						<div class="col-md-6">
							<div class="social-1">
								<a href=""><i class="fab fa-facebook-f"></i></a>
								<a href=""><i class="fab fa-twitter" aria-hidden="true"></i></a>
								<a href=""><i class="fab fa-instagram" aria-hidden="true"></i></a>
							</div>
						</div>

					</div>
				</div>
			</div>
		</footer>

		<!-- sections end -->

		<!-- script start -->
		<!-- script start -->

		<?php include_once NOTIFICATION.'index.php' ?>
		<script type="text/javascript" src="<?php echo JS_LIBRARY ?>bootstrap/bootstrap.min.js"></script>
		<script type="text/javascript" src="<?php echo JS_LIBRARY ?>popper.min.js"></script>
		<script type="text/javascript" src="<?php echo JS_LIBRARY ?>flickity.min.js"></script>
		<script type="text/javascript" src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" ></script>
		<script type="text/javascript" src="<?php echo USER_JS ?>zoom-image.js"  ></script>
		<script type="text/javascript" src="<?php echo USER_JS ?>product-zoom-main.js"  ></script>
		<script type="text/javascript" src="<?php echo APP_JS_LIBRARY ?>app.js"  ></script>
		<!-- script end -->
		<script>
			window.onscroll = function() {myFunction()};

			var header = document.getElementById("header");
			var sticky = header.offsetTop;

			function myFunction() {
			  if (window.pageYOffset > sticky) {
			    header.classList.add("sticky");
			  } else {
			    header.classList.remove("sticky");
			  }
			}
		</script>
		
	</body>
</html>