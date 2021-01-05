<?php

	include_once 'header.php';

?>
		<!-- section global platform start -->
		<section class="banner-section">
			<div class="container-fluid">
				<div class="row">
					<div class="card-ban">
						<div class="carousel dn-s"  data-flickity='{ "autoPlay": true, "wrapAround": true }'>
							<div class="carousel-cell-2"><img src="<?php echo USER_IMAGES ?>1.png"></div>
							<div class="carousel-cell-2"><img src="<?php echo USER_IMAGES ?>2.png"></div>
							<div class="carousel-cell-2"><img src="<?php echo USER_IMAGES ?>3.png"></div>
						</div>
						<div class="carousel dn-l mob-baner"  data-flickity='{ "autoPlay": true, "wrapAround": true }'>
							<div class="carousel-cell-2 "><img src="<?php echo USER_IMAGES ?>1.png"></div>
							<div class="carousel-cell-2 "><img src="<?php echo USER_IMAGES ?>2.png"></div>
							<div class="carousel-cell-2 "><img src="<?php echo USER_IMAGES ?>3.png"></div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- section global platform end -->

		<section class="section-content cloth-card" >
			<div class="container">
				<div class="row">
					<div class="main-card">

						<a href="/tag/love/" class="card-link">
							<div class="acce-card">
								<div class="img-acc">
									<img src="<?php echo USER_IMAGES ?>love.png">
								</div>
								<div class="acc-card-head">
									<h4>Love collection</h4>
								</div>
							</div>
						</a>

						<a href="/tag/anniversary/" class="card-link">
							<div class="acce-card">
								<div class="img-acc">
									<img src="<?php echo USER_IMAGES ?>anniversary.jpg">
								</div>
								<div class="acc-card-head">
									<h4>Anniversary</h4>
								</div>
							</div>
						</a>
										
						<a href="/tag/birthday/" class="card-link">
							<div class="acce-card">
								<div class="img-acc">
									<img src="<?php echo USER_IMAGES ?>birthday.jpg">
								</div>
								<div class="acc-card-head">
									<h4>Birthday</h4>
								</div>
							</div>
						</a>
						
						<a href="/tag/luxury/" class="card-link">
							<div class="acce-card">
								<div class="img-acc">
									<img src="<?php echo USER_IMAGES ?>luxury.jpeg">
								</div>
								<div class="acc-card-head">
									<h4>Luxury</h4>
								</div>
							</div>
						</a>

						<a href="/category/cake/" class="card-link">
							<div class="acce-card">
								<div class="img-acc">
									<img src="<?php echo USER_IMAGES ?>cake.jpg">
								</div>
								<div class="acc-card-head">
									<h4>Cake</h4>
								</div>
							</div>
						</a>
					</div>
				</div>
			</div>
		</section>

		<section class="section-content bg-light">
			<div class="container">

				<!-- Love collection -->
				<div class="row">
					<div class="col-md-12">
						<div class="head-1">
							<h2>Love Collection</h2>
							<span class="cart-bodr-h2"></span>
							<div class="view-box">
								<a href="/tag/love/" class="btn-vw">View All</a>
							</div>
						</div>
					</div>

					<div class="col-md-12">
						<div class="carousel carousel-home" data-flickity='{ "groupCells": true, "freeScroll": true,"pageDots": false, "contain": true }'>
						
						<?php
							
							if($productList=mysqli_query($connection, "SELECT tag_relation_id, link_id, link_url, product_title, product_image, inventory_currency, inventory_price FROM ".TABLE_PREFIX."tag INNER JOIN ".TABLE_PREFIX."product ON product_id=tag_relation_id INNER JOIN ".TABLE_PREFIX."inventory ON inventory_relation_id=product_id INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=product_id WHERE tag_type='product' AND tag_title='green background' AND inventory_type='product' AND link_type='product' AND link_status=200 AND inventory_in_stock=1 AND tag_site_id=".SITE_ID." AND link_site_id=".SITE_ID."  ORDER BY link_published DESC LIMIT 0, 12")) {

								if(mysqli_num_rows($productList)!=0) {

									while($productData=mysqli_fetch_assoc($productList)) {

										extract($productData);

										echo "<div class='carousel-cell-home'>
											<a href='{$link_url}'>
												<div class='col-card-3'>
													<div class='event-card'>
														<div class='img-cd'>
															<img src='{$product_image}'>
														</div>
															<h4>{$product_title}</h4>
															<p class='rate'>Rs. ".(($inventory_price!=0) ? $inventory_price : $inventory_mrp)."</p>
													</div>
												</div>
											</a>
										</div>";

									}

								}

							}

						?>
						
						</div>
					</div>
				</div>
				<!-- Anniversary -->
				<div class="row">
					<div class="col-md-12">
						<div class="head-1">
							<h2>Anniversary</h2>
							<span class="cart-bodr-h2"></span>
							<div class="view-box">
								<a href="/tag/anniversary/" class="btn-vw">View All</a>
							</div>
						</div>
					</div>

					<div class="col-md-12">
						<div class="carousel carousel-home" data-flickity='{ "groupCells": true, "freeScroll": true,"pageDots": false, "contain": true }'>
						
						<?php
							
							if($productList=mysqli_query($connection, "SELECT tag_relation_id, link_id, link_url, product_title, product_image, inventory_currency, inventory_price FROM ".TABLE_PREFIX."tag INNER JOIN ".TABLE_PREFIX."product ON product_id=tag_relation_id INNER JOIN ".TABLE_PREFIX."inventory ON inventory_relation_id=product_id INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=product_id WHERE tag_type='product' AND tag_title='white background' AND inventory_type='product' AND link_type='product' AND link_status=200 AND inventory_in_stock=1 AND tag_site_id=".SITE_ID." AND link_site_id=".SITE_ID."  ORDER BY link_published DESC LIMIT 0, 12")) {

								if(mysqli_num_rows($productList)!=0) {

									while($productData=mysqli_fetch_assoc($productList)) {

										extract($productData);

										echo "<div class='carousel-cell-home'>
											<a href='{$link_url}'>
												<div class='col-card-3'>
													<div class='event-card'>
														<div class='img-cd'>
															<img src='{$product_image}'>
														</div>
															<h4>{$product_title}</h4>
															<p class='rate'>Rs. ".(($inventory_price!=0) ? $inventory_price : $inventory_mrp)."</p>
													</div>
												</div>
											</a>
										</div>";

									}

								}

							}

						?>
						
						</div>
					</div>
				</div>

				<!-- Birthday -->
				<div class="row">
					<div class="col-md-12">
						<div class="head-1">
							<h2>Birthday</h2>
							<span class="cart-bodr-h2"></span>
							<div class="view-box">
								<a href="/tag/birthday/" class="btn-vw">View All</a>
							</div>
						</div>
					</div>

					<div class="col-md-12">
						<div class="carousel carousel-home" data-flickity='{ "groupCells": true, "freeScroll": true,"pageDots": false, "contain": true }'>
						
						<?php
							
							if($productList=mysqli_query($connection, "SELECT tag_relation_id, link_id, link_url, product_title, product_image, inventory_currency, inventory_price FROM ".TABLE_PREFIX."tag INNER JOIN ".TABLE_PREFIX."product ON product_id=tag_relation_id INNER JOIN ".TABLE_PREFIX."inventory ON inventory_relation_id=product_id INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=product_id WHERE tag_type='product' AND tag_title='grey background' AND inventory_type='product' AND link_type='product' AND link_status=200 AND inventory_in_stock=1 AND tag_site_id=".SITE_ID." AND link_site_id=".SITE_ID."  ORDER BY link_published DESC LIMIT 0, 12")) {

								if(mysqli_num_rows($productList)!=0) {

									while($productData=mysqli_fetch_assoc($productList)) {

										extract($productData);

										echo "<div class='carousel-cell-home'>
											<a href='{$link_url}'>
												<div class='col-card-3'>
													<div class='event-card'>
														<div class='img-cd'>
															<img src='{$product_image}'>
														</div>
															<h4>{$product_title}</h4>
															<p class='rate'>Rs. ".(($inventory_price!=0) ? $inventory_price : $inventory_mrp)."</p>
													</div>
												</div>
											</a>
										</div>";

									}

								}

							}

						?>
						
						</div>
					</div>
				</div>

				
				<!-- Luxury Collection -->
				<div class="row">
					<div class="col-md-12">
						<div class="head-1">
							<h2>Luxury</h2>
							<span class="cart-bodr-h2"></span>
							<div class="view-box">
								<a href="/tag/luxury/" class="btn-vw">View All</a>
							</div>
						</div>
					</div>

					<div class="col-md-12">
						<div class="carousel carousel-home" data-flickity='{ "groupCells": true, "freeScroll": true,"pageDots": false, "contain": true }'>
						
						<?php
							
							if($productList=mysqli_query($connection, "SELECT tag_relation_id, link_id, link_url, product_title, product_image, inventory_currency, inventory_price FROM ".TABLE_PREFIX."tag INNER JOIN ".TABLE_PREFIX."product ON product_id=tag_relation_id INNER JOIN ".TABLE_PREFIX."inventory ON inventory_relation_id=product_id INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=product_id WHERE tag_type='product' AND tag_title='black background' AND inventory_type='product' AND link_type='product' AND link_status=200 AND inventory_in_stock=1 AND tag_site_id=".SITE_ID." AND link_site_id=".SITE_ID."  ORDER BY link_published DESC LIMIT 0, 12")) {

								if(mysqli_num_rows($productList)!=0) {

									while($productData=mysqli_fetch_assoc($productList)) {

										extract($productData);

										echo "<div class='carousel-cell-home'>
											<a href='{$link_url}'>
												<div class='col-card-3'>
													<div class='event-card'>
														<div class='img-cd'>
															<img src='{$product_image}'>
														</div>
															<h4>{$product_title}</h4>
															<p class='rate'>Rs. ".(($inventory_price!=0) ? $inventory_price : $inventory_mrp)."</p>
													</div>
												</div>
											</a>
										</div>";

									}

								}

							}

						?>
						
						</div>
					</div>
				</div>
				
			</div>
		</section>

		<section class="section-content cloth-card" >
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="head-1">
							<h2>Customer Review</h2>
							<span class="cart-bodr-h2"></span>
						</div>
					</div>

					<div class="col-md-12">
						<div class="carousel carousel-testimonial" data-flickity='{ "groupCells": true, "freeScroll": true,"pageDots": false, "contain": true }'>
							<div class="carousel-cell-testimonial">
								<div class="col-card-4">
									<div class="review-list recommand-service">
									   <h3>Tasteful beautiful</h3>
									   <div class="rating ratingstar"> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup></div>
									   <div class="cont-tst">
										   <p class="rating-desc">TASTEFULLY done up store with beautiful collection of premium quality flowers... Very creative and exquisite flower arrangements.</p>
										   <span class="author-name">Manisha Agarwal</span>
										   <!-- <p>On August 09, 2019</p> -->
										   <!-- <div class="recommanded">
										      <p class="recommanded-icon">Recommends</p>
										   </div> -->
										</div>
									</div>
								</div>
							</div>
							<div class="carousel-cell-testimonial">
								<div class="col-card-4">
									<div class="review-list recommand-service">
									   <h3>Quality & Choices</h3>
									   <div class="rating ratingstar"> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup></div>
									   <div class="cont-tst">
										   <p class="rating-desc">Very helpful staff for all your flower needs and at a reasonable price. Their product is very high quality and fresh. Lots of choices for flowers, and teddy . Many unique flowers to choose from.</p>
										   <span class="author-name">Sakshi Negi</span>
										   <!-- <p>On March 02, 2018</p> -->
										   <!-- <div class="recommanded">
										      <p class="recommanded-icon">Recommends</p>
										   </div> -->
										</div>
									</div>
								</div>
							</div>
							<div class="carousel-cell-testimonial">
								<div class="col-card-4">
									<div class="review-list recommand-service">
									   <h3>Creativity at its Best!</h3>
									   <div class="rating ratingstar"> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup></div>
									   <div class="cont-tst">
										   <p class="rating-desc">I really like the ambience of this place, beautiful flowers, creativity at it best.. anyone looking up for creative and mesmerizing bouquets for their loved ones surely go ahead and order for them. </p>
										   <span class="author-name">Jon Snow</span>
										   <!-- <p>On October 04, 2020 from <strong>khammam</strong></p>
										   <div class="recommanded">
										      <p class="recommanded-icon">Recommends</p>
										   </div> -->
										</div>
									</div>
								</div>
							</div>
							<div class="carousel-cell-testimonial">
								<div class="col-card-4">
									<div class="review-list recommand-service">
									   <h3>Flowers are Jewels!</h3>
									   <div class="rating ratingstar"> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup></div>
									   <div class="cont-tst">
										   <p class="rating-desc">Who said flowers can't replace jewels... Must visit this fabulous store. I wld rate &Blooms among the best in the business, with some great collection of fresh and exclusive flowers from across the globe.  On a lighter note, I can take the liberty of annoying my loved ones now. because I know a bunch ordered from &Blooms will serve as a saviour😀😀.</p>
										   <span class="author-name">Sunil Bhateja</span>
										   <!-- <p>On October 04, 2020 from <strong>khammam</strong></p>
										   <div class="recommanded">
										      <p class="recommanded-icon">Recommends</p>
										   </div> -->
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- <section class="section-content info-setion" >
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="about-info">
							<h1>#1 Online Flower Delivery Portal In India</h1>
							<p>Is there anyone in this world who does not like flowers? Anyone who does not admire the beauty of flowers? Of course not! Because flowers undoubtedly bring a smile on everyone's face, they add the warmth of sunshine to the day and make the soul feel calm and peaceful. And so to speak the language of flowers and express emotions when words are just not enough, & Blooms has taken the responsibility to become your messenger of Emotions, Send flower bouquet online and add more smiles to your loved one's face.</p>

							<p>But finding a beautiful bunch of flower that speaks your emotions perfectly might be a tough task. That is when & Blooms steps in with amazing flower gifts of amazing varieties so that you don't have to hop to florist shops around you and be settled with a limited choice. & Blooms shall be your reliable online florist shop in India so that you can sit at the comfort of your home and order amazing flowers and gifts with a few taps of your finger.</p>

							<p>So, what is their favorite flower? Does your recipient love Roses, Carnations, Gerberas, Anthuriums, orchids, Lilies or? Whichever be the flower of their choice, find it at the best online florist in India- & Blooms. We deliver freshly cut flowers in the most beautiful and classic arrangements. Wrapped beautifully in different designs and colorful paper, these flower arrangements are the best quality products which shall make your loved one's face shine even brighter.</p>

							<p>So the next time you need to send flowers online in India, you know where to head to.</p>

							<h2>Send Flowers Online from & Blooms - India's No. 1 Online Florist</h2>
							<p>& Blooms stands as a No. 1 online flower shop that doesn't only deliver flowers, gifts, and cakes but makes sure that each of the hampers is wrapped in love too. Along with the pocket-friendly deals come the free shipping that makes & Blooms top the charts and establishing itself as one of the most reliable online florists in the country.</p>


						</div>
					</div>
				</div>
			</div>
		</section> -->

<?php

	include_once 'footer.php';

?>