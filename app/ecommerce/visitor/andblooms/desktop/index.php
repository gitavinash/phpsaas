<?php

    include_once 'header.php';

?>

<!-- section global platform start -->
<section class="banner-section">
	<div class="container-fluid">
		<div class="row">
			<div class="card-ban">
				<div class="carousel"  data-flickity='{ "autoPlay": true, "wrapAround": true }'>
					<div class="carousel-cell-2"><img src="<?php echo USER_IMAGES ?>slide-1.png"></div>
					<div class="carousel-cell-2"><img src="<?php echo USER_IMAGES ?>slide-2.png"></div>
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
				<a href="" class="card-link">
					<div class="acce-card">
						<div class="img-acc">
							<img src="https://andblooms.com/wp-content/uploads/2020/02/4993353155090-scaled.jpg">
						</div>
						<div class="acc-card-head">
							<h4>Flowers</h4>
						</div>
					</div>
				</a>
				<a href="" class="card-link">
					<div class="acce-card">
						<div class="img-acc">
							<img src="https://andblooms.com/wp-content/uploads/2020/02/4993353155090-scaled.jpg">
						</div>
						<div class="acc-card-head">
							<h4>Cakes</h4>
						</div>
					</div>
				</a>
				<a href="" class="card-link">
					<div class="acce-card">
						<div class="img-acc">
							<img src="https://andblooms.com/wp-content/uploads/2020/02/4993353155090-scaled.jpg">
						</div>
						<div class="acc-card-head">
							<h4>Combos</h4>
						</div>
					</div>
				</a>
				<a href="" class="card-link">
					<div class="acce-card">
						<div class="img-acc">
							<img src="https://andblooms.com/wp-content/uploads/2020/02/4993353155090-scaled.jpg">
						</div>
						<div class="acc-card-head">
							<h4>Combos</h4>
						</div>
					</div>
				</a>
				<a href="" class="card-link">
					<div class="acce-card">
						<div class="img-acc">
							<img src="https://andblooms.com/wp-content/uploads/2020/02/4993353155090-scaled.jpg">
						</div>
						<div class="acc-card-head">
							<h4>Combos</h4>
						</div>
					</div>
				</a>
			</div>
		</div>
	</div>
</section>

<section class="section-content bg-light">
			<div class="container">
				<!-- catagery 1 carousel -->
				<div class="row">
					<div class="col-md-12">
						<div class="head-1">
							<h2>Love Collection</h2>
							<span class="cart-bodr-h2"></span>
						</div>
					</div>

					<div class="col-md-12">
						<div class="carousel carousel-home" data-flickity='{ "groupCells": true, "freeScroll": true,"pageDots": false, "contain": true }'>
							
							<?php
							
								if($productList=mysqli_query($connection, "SELECT link_url, product_title, product_thumbnail, inventory_price, inventory_mrp FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."product ON link_relation_id=product_id INNER JOIN ".TABLE_PREFIX."inventory ON inventory_product_id=product_id WHERE link_site_id=".SITE_ID." AND link_type='product' AND inventory_in_stock=1 ORDER BY link_id DESC LIMIT 0, 10")) {

									if(mysqli_num_rows($productList)!=0) {

										while($productData=mysqli_fetch_assoc($productList)) {

											extract($productData);

											echo "<div class='carousel-cell-home'>
												<a href='{$link_url}'>
													<div class='col-card-3'>
														<div class='event-card'>
															<div class='img-cd'>
																<img src='https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg'>
															</div>
																<h4>{$product_title}</h4>
																<p class='rate'>Rs. ".(($inventory_price!=0) ? $inventory_price : $inventory_mrp)."</p>
														</div>
													</div>
												</a>
											</div>";

										}

									}

								} else {

									echo mysqli_error($connection);
									exit();

								}

							?>

						</div>
					</div>
				</div>
				<!-- catagery 2 carousel -->
				<div class="row">
					<div class="col-md-12">
						<div class="head-1">
							<h2>Luxury Collection</h2>
							<span class="cart-bodr-h2"></span>
						</div>
					</div>

					<div class="col-md-12">
						<div class="carousel carousel-home" data-flickity='{ "groupCells": true, "freeScroll": true,"pageDots": false, "contain": true }'>

							<?php
								
								if($productList=mysqli_query($connection, "SELECT link_url, product_title, product_thumbnail, inventory_price, inventory_mrp FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."product ON link_relation_id=product_id INNER JOIN ".TABLE_PREFIX."inventory ON inventory_product_id=product_id WHERE link_site_id=".SITE_ID." AND link_type='product' AND inventory_in_stock=1 ORDER BY link_id DESC LIMIT 0, 10")) {

									if(mysqli_num_rows($productList)!=0) {

										while($productData=mysqli_fetch_assoc($productList)) {

											extract($productData);

											echo "<div class='carousel-cell-home'>
												<a href='{$link_url}'>
													<div class='col-card-3'>
														<div class='event-card'>
															<div class='img-cd'>
																<img src='https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg'>
															</div>
																<h4>{$product_title}</h4>
																<p class='rate'>Rs. ".(($inventory_price!=0) ? $inventory_price : $inventory_mrp)."</p>
														</div>
													</div>
												</a>
											</div>";

										}

									}

								} else {

									echo mysqli_error($connection);
									exit();

								}

							?>
						
						</div>	
					</div>
				</div>
				<!-- catagery 3 carousel-->
				<div class="row">
					<div class="col-md-12">
						<div class="head-1">
							<h2>Birthday</h2>
							<span class="cart-bodr-h2"></span>
						</div>
					</div>

					<div class="col-md-12">
						<div class="carousel carousel-home" data-flickity='{ "groupCells": true, "freeScroll": true,"pageDots": false, "contain": true }'>
							
							<?php
								
								if($productList=mysqli_query($connection, "SELECT link_url, product_title, product_thumbnail, inventory_price, inventory_mrp FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."product ON link_relation_id=product_id INNER JOIN ".TABLE_PREFIX."inventory ON inventory_product_id=product_id WHERE link_site_id=".SITE_ID." AND link_type='product' AND inventory_in_stock=1 ORDER BY link_id DESC LIMIT 0, 10")) {

									if(mysqli_num_rows($productList)!=0) {

										while($productData=mysqli_fetch_assoc($productList)) {

											extract($productData);

											echo "<div class='carousel-cell-home'>
												<a href='{$link_url}'>
													<div class='col-card-3'>
														<div class='event-card'>
															<div class='img-cd'>
																<img src='https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg'>
															</div>
																<h4>{$product_title}</h4>
																<p class='rate'>Rs. ".(($inventory_price!=0) ? $inventory_price : $inventory_mrp)."</p>
														</div>
													</div>
												</a>
											</div>";

										}

									}

								} else {

									echo mysqli_error($connection);
									exit();

								}

							?>
							
						</div>
					</div>
				</div>
				<!-- catagery 4 carousel-->
				<div class="row">
					<div class="col-md-12">
						<div class="head-1">
							<h2>Anniversary</h2>
							<span class="cart-bodr-h2"></span>
						</div>
					</div>

					<div class="col-md-12">
						<div class="carousel carousel-home" data-flickity='{ "groupCells": true, "freeScroll": true,"pageDots": false, "contain": true }'>
								
							<?php
								
								if($productList=mysqli_query($connection, "SELECT link_url, product_title, product_thumbnail, inventory_price, inventory_mrp FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."product ON link_relation_id=product_id INNER JOIN ".TABLE_PREFIX."inventory ON inventory_product_id=product_id WHERE link_site_id=".SITE_ID." AND link_type='product' AND inventory_in_stock=1 ORDER BY link_id DESC LIMIT 0, 10")) {

									if(mysqli_num_rows($productList)!=0) {

										while($productData=mysqli_fetch_assoc($productList)) {

											extract($productData);

											echo "<div class='carousel-cell-home'>
												<a href='{$link_url}'>
													<div class='col-card-3'>
														<div class='event-card'>
															<div class='img-cd'>
																<img src='https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg'>
															</div>
																<h4>{$product_title}</h4>
																<p class='rate'>Rs. ".(($inventory_price!=0) ? $inventory_price : $inventory_mrp)."</p>
														</div>
													</div>
												</a>
											</div>";

										}

									}

								} else {

									echo mysqli_error($connection);
									exit();

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
									   <h3>super plant</h3>
									   <div class="rating ratingstar"> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup></div>
									   <div class="cont-tst">
										   <p class="rating-desc">excellent qualities</p>
										   <span class="author-name">Abhi</span>
										   <p>On October 04, 2020 from <strong>khammam</strong></p>
										   <div class="recommanded">
										      <p class="recommanded-icon">Recommends</p>
										   </div>
										</div>
									</div>
								</div>
							</div>
							<div class="carousel-cell-testimonial">
								<div class="col-card-4">
									<div class="review-list recommand-service">
									   <h3>super plant</h3>
									   <div class="rating ratingstar"> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup></div>
									   <div class="cont-tst">
										   <p class="rating-desc">excellent qualities</p>
										   <span class="author-name">Abhi</span>
										   <p>On October 04, 2020 from <strong>khammam</strong></p>
										   <div class="recommanded">
										      <p class="recommanded-icon">Recommends</p>
										   </div>
										</div>
									</div>
								</div>
							</div>
							<div class="carousel-cell-testimonial">
								<div class="col-card-4">
									<div class="review-list recommand-service">
									   <h3>super plant</h3>
									   <div class="rating ratingstar"> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup></div>
									   <div class="cont-tst">
										   <p class="rating-desc">excellent qualities</p>
										   <span class="author-name">Abhi</span>
										   <p>On October 04, 2020 from <strong>khammam</strong></p>
										   <div class="recommanded">
										      <p class="recommanded-icon">Recommends</p>
										   </div>
										</div>
									</div>
								</div>
							</div>
							<div class="carousel-cell-testimonial">
								<div class="col-card-4">
									<div class="review-list recommand-service">
									   <h3>super plant</h3>
									   <div class="rating ratingstar"> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup> <sup class="fill-star"></sup></div>
									   <div class="cont-tst">
										   <p class="rating-desc">excellent qualities</p>
										   <span class="author-name">Abhi</span>
										   <p>On October 04, 2020 from <strong>khammam</strong></p>
										   <div class="recommanded">
										      <p class="recommanded-icon">Recommends</p>
										   </div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<section class="section-content info-setion" >
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="about-info">
							<h1>#1 Online Flower Delivery Portal In Delhi & NCR</h1>
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
		</section>

<?php

    include_once 'footer.php';

?>