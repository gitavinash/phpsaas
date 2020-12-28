<?php

    include_once 'header.php';

?>


		<!-- section global platform start -->
		<section class="banner-section baner-bg">
			<div class="overlay-bner"></div>
			<div class="container">
				<div class="row ">
					<div class="col-md-12">
						
						<div class="bg-content">
							<h1><?php echo $category_title ?></h1>
							<h3><?php echo $category_description ?></h3>
						</div>
					</div>
					
					<?php
								
								if($productList=mysqli_query($connection, "SELECT link_url, product_title, product_image, inventory_price, inventory_mrp FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."product ON link_relation_id=product_id INNER JOIN ".TABLE_PREFIX."inventory ON inventory_relation_id=product_id WHERE link_site_id=".SITE_ID." AND link_type='product' AND link_is_featured=1 AND inventory_in_stock=1 AND product_category='{$category_id}' ORDER BY link_id DESC LIMIT 0, 10")) {

									if(mysqli_num_rows($productList)!=0) {

										echo "<div class='col-md-12'>
										<div class='carousel carousel-baner' data-flickity='{ 'groupCells': true, 'freeScroll': true,'pageDots': false, 'contain': true }'>'";

										while($productData=mysqli_fetch_assoc($productList)) {

											extract($productData);

											echo "<div class='carousel-cell-baner'>
												<a href='{$link_url}'>
													<div class='col-card-3'>
														<div class='event-card'>
															<div class='img-cd'>
																<img src='{$product_image}'>
															</div>
																<h4>{$product_title}</h4>
														</div>
													</div>
												</a>
											</div>";

										}

									}

									echo "</div></div>";

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
		<!-- section global platform end -->

		<section class="section-content">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="brdcrum">
							<nav aria-label="breadcrumb">
							  <ol class="breadcrumb">
							    <li class="breadcrumb-item"><a href="/">Home</a></li>
							    <li class="breadcrumb-item"><a href="<?php echo ACTIVE_URL ?>">Category</a></li>
							    <li class="breadcrumb-item active" aria-current="page"><?php echo $category_title ?></li>
							  </ol>
							</nav>
						</div>
					</div>
				</div>
				<div class="row">

				<?php
							
							if($productList=mysqli_query($connection, "SELECT link_url, product_title, product_image, inventory_price, inventory_mrp FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."product ON link_relation_id=product_id INNER JOIN ".TABLE_PREFIX."inventory ON inventory_relation_id=product_id WHERE link_site_id=".SITE_ID." AND link_type='product' AND inventory_in_stock=1 ORDER BY link_id DESC LIMIT 0, 20")) {

								if(mysqli_num_rows($productList)!=0) {

									while($productData=mysqli_fetch_assoc($productList)) {

										extract($productData);

										echo "<div class='col-md-3'>
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

							} else {

								echo mysqli_error($connection);
								exit();

							}

						?>

					<!-- <div class="col-md-3">
						<div class="event-card">
							<div class="img-cd">
								<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
							</div>
							<h4>Alexander Mcqueen</h4>
							<p class="rate">Rs. 115</p>
						</div>
					</div> -->
				</div>
			</div>
		</section>

		<!-- <section class="section-content info-setion" >
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="about-info">
							<h1>#1 Online Flower Delivery Portal In India</h1>
							<p>Is there anyone in this world who does not like flowers? Anyone who does not admire the beauty of flowers? Of course not! Because flowers undoubtedly bring a smile on everyone's face, they add the warmth of sunshine to the day and make the soul feel calm and peaceful. And so to speak the language of flowers and express emotions when words are just not enough, FlowerAura has taken the responsibility to become your messenger of Emotions, Send flower bouquet online and add more smiles to your loved one's face.</p>

							<p>But finding a beautiful bunch of flower that speaks your emotions perfectly might be a tough task. That is when FlowerAura steps in with amazing flower gifts of amazing varieties so that you don't have to hop to florist shops around you and be settled with a limited choice. FlowerAura shall be your reliable online florist shop in India so that you can sit at the comfort of your home and order amazing flowers and gifts with a few taps of your finger.</p>

							<p>So, what is their favorite flower? Does your recipient love Roses, Carnations, Gerberas, Anthuriums, orchids, Lilies or? Whichever be the flower of their choice, find it at the best online florist in India- FlowerAura. We deliver freshly cut flowers in the most beautiful and classic arrangements. Wrapped beautifully in different designs and colorful paper, these flower arrangements are the best quality products which shall make your loved one's face shine even brighter.</p>

							<p>So the next time you need to send flowers online in India, you know where to head to.</p>

							<h2>Send Flowers Online from FlowerAura - India's No. 1 Online Florist</h2>
							<p>FlowerAura stands as a No. 1 online flower shop that doesn't only deliver flowers, gifts, and cakes but makes sure that each of the hampers is wrapped in love too. Along with the pocket-friendly deals come the free shipping that makes FlowerAura top the charts and establishing itself as one of the most reliable online florists in the country.</p>


						</div>
					</div>
				</div>
			</div>
		</section> -->
		
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

<?php

    include_once 'footer.php';

?>