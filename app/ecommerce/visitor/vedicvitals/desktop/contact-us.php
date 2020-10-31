<?php

	require_once 'header.php';

?>

		<! section global platform start >
		<section class="bannersection">
			<div class="containerfluid">
				<div class="row">
					<div class="cardban">
						<div class="banersingle">
							<img src="<?php echo USER_IMAGES ?>slider1.jpg">
						</div>
					</div>
				</div>
			</div>
		</section>
		
		<section class="sectioncontent contform" >
			<div class="container">
				<div class="row">
					<div class="colmd12">
						<div class="head1">
							<h2>Contact Us</h2>
						</div>
					</div>

					
					<div class="colmd6">
						<div class="contctinfo">
							<ul>
								<li><i class="fa fahome" ariahidden="true"></i> 622A, Ground Floor, Guru Ram Das Nagar, Laxmi Nagar, DELHI  110051, India</li>
								<li><i class="fa faphonealt" ariahidden="true"></i> +91 9310142836</li>
								<li><i class="fa faenvelope" ariahidden="true"></i> care@vedicvitals.com</li>
							</ul>
						</div>
					</div>

					<div class="colmd6">
						<! form start >
						<form class="checkoutform">
							<div class="formrow">
								<div class="formgroup colmd12">
									<label class="lablhead">Your Query</label>
								</div>
							    <div class="formgroup colmd12">
							      <input type="text" class="formcontrol" id="" placeholder="First name">
							    </div>
							    <div class="formgroup colmd12">
							      <input type="text" class="formcontrol" id="" placeholder="Last name">
							    </div>
							    <div class="formgroup colmd12">
							      <input type="email" class="formcontrol" id="inputEmail4" placeholder="Email">
							    </div>
								    <div class="formgroup colmd12">
								    <input type="tel" class="formcontrol" id="" placeholder="Contact number">
								</div>
								<div class="formgroup colmd12">
							    	<textarea class="formcontrol txarea" id="exampleFormControlTextarea1" rows="3" placeholder="Your Query"></textarea>
								</div>
							</div>

							
							<div class="formrow actnbox">
								<div class="formgroup colmd12">
							  		<! <button type="submit" class="btn shibtn">Continue Shipping</button> >
							  		<a href="checkoutshipping.html" class="btn shibtn">Send</a>
								</div>
							</div>
						</form>
					<! form end >
					</div>


				</div>
			</div>
		</section>

<?php

	require_once 'footer.php';

?>