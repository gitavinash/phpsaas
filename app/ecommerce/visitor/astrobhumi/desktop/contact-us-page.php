<?php

	require_once 'header.php';

?>

		<!-- section global platform start -->
		<section class="banner-section">
			<div class="container-fluid">
				<div class="row">
					<div class="card-ban">
						<div class="baner-single">
							<img src="<?php echo USER_IMAGES ?>slider1.jpg">
						</div>
					</div>
				</div>
			</div>
		</section>
		
		<section class="section-content cont-form" >
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="head-1">
							<h2>Contact Us</h2>
						</div>
					</div>

					
					<div class="col-md-6">
						<div class="contct-info">
							<ul>
								<li><i class="fa fa-home" aria-hidden="true"></i> 543, Ward no 5,Durjanpura, Nawalgarh, Rajasthan - 333042, India</li>
								<li><i class="fa fa-phone-alt" aria-hidden="true"></i> +91-8053965181</li>
								<li><i class="fa fa-envelope" aria-hidden="true"></i> info@astrobhumi.com</li>
							</ul>
						</div>
					</div>

					<div class="col-md-6">
						<!-- form start -->
						<form class="contact-us">
							<div class="form-row">
								<div class="form-group col-md-12">
									<label class="labl-head">Your Query</label>
								</div>
							    <div class="form-group col-md-12">
							      <input type="text" class="form-control" name="name" id="name" placeholder="Full name*">
							    </div>
							    <div class="form-group col-md-12">
							      <input type="email" class="form-control" name="email" id="email" placeholder="Email*">
							    </div>
								    <div class="form-group col-md-12">
								    <input type="tel" class="form-control" name="mobile" id="mobile" placeholder="Contact number*">
								</div>
								<div class="form-group col-md-12">
							    	<textarea class="form-control textarea" name="msg" rows="3" placeholder="Your Query"></textarea>
								</div>
							</div>

							
							<div class="form-row actn-box">
								<div class="form-group col-md-12">
							  		<a href="#" id="submitContactUsForm" class="btn shi-btn">Send</a>
								</div>
							</div>
						</form>
					<!-- form end -->
					</div>


				</div>
			</div>
		</section>

<?php

	require_once 'footer.php';

?>