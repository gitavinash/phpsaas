

		<footer>
			<div class="top-section-footer ">
				<div class="container">
					<div class="row">
						<div class="col-md-6">
							<div class="foter-left">
								<h1><?php echo SITE_NAME ?></h1>
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
								<h3>My Account</h3>
								<ul class="list-unstyled">
					              <li><a href="index">The Brand</a></li>
					              <li><a href="index">Jwelery faq</a></li>
					              <li><a href="index">REtailer</a></li>
					            </ul>
							</div>
						</div>
						<div class="col-md-3">
							<div class="fot-box">	
								<h3>Quick Links</h3>
								<ul class="list-unstyled">
					              <li><a href="index">Content</a></li>
					              <li><a href="index">Retailer</a></li>
					              <li><a href="index">Shipping</a></li>
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