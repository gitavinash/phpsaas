<?php

	include_once 'header.php';

?>
		<section class="banner-section mt-100">
			<div class="container-fluid">
				<div class="row">
					<img src="<?php echo USER_IMAGES ?>bg-pro.jpg" class="baner-product">
				</div>
			</div>
		</section>
		<!-- section global platform start -->
		<section class="section-content-filter">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="brdcrum">
							<nav aria-label="breadcrumb">
							  <ol class="breadcrumb">
							    <li class="breadcrumb-item"><a href="/">Home</a></li>
							    <li class="breadcrumb-item active" aria-current="page"><?php echo $category_title ?></li>
							  </ol>
							</nav>
						</div>
					</div>

					<div class="col-md-12">
						<div class="filter-section">
							<div class="filter-left dn-min-768">
								<!-- <h2>Filter By</h2>
								<div class="filter-card">
									<form>
										<div class="form-group slct-bx">
										    <select class="form-control" id="exampleFormControlSelect1">
										      <option>Ring</option>
										      <option>Neckles</option>
										      <option>Bracelets</option>
										      <option>Earing</option>
										    </select>
										</div>
										<div class="form-group slct-bx">
										    <select class="form-control" id="exampleFormControlSelect1">
										      <option>Female</option>
										      <option>Male</option>
										      <option>Kids</option>
										    </select>
										</div>
										<div class="form-group slct-bx">
										    <select class="form-control" id="exampleFormControlSelect1">
										      <option>Red</option>
										      <option>Green</option>
										      <option>Cyan</option>
										    </select>
										</div>
										<div class="form-group slct-bx">
										    <select class="form-control" id="exampleFormControlSelect1">
										      <option>Rs: 5000 - Rs: 10000</option>
										      <option>Rs: 15000 - Rs: 30000</option>
										      <option>Rs: 30000 - Rs: 50000</option>
										    </select>
										</div>
									</form>
								</div> -->
							</div>

							<div class="filter-right dn-min-768">
								<div class="recom">
									<h2>Sort By</h2>
									<div class="form-group slct-bx">
									    <select class="form-control" id="exampleFormControlSelect1">
									      <option>Recommended</option>
									      <option>Popular</option>
									      <option>Trending</option>
									      <option>Latest</option>
									    </select>
									</div>
								</div>
							</div>
							<!-- <div class="show-result">
								<h4>Showing <span>1 - 60 out of </span></h4>
							</div> -->
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- section global platform end -->


		<!-- product start -->
		<section class="section-content-2 ">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="heading2-filter ">
							<h2><?php echo $category_title ?></h2>
						</div>
					</div>

					<?php

						if($productList=mysqli_query($connection, "SELECT link_url, product_title, product_image, inventory_price, inventory_currency_symbol, inventory_in_stock, inventory_mrp, link_published FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."product ON link_relation_id=product_id INNER JOIN ".TABLE_PREFIX."inventory ON inventory_relation_id=product_id INNER JOIN isw_category ON category_id=product_category WHERE link_site_id=".SITE_ID." AND link_type='product' AND link_status=200 AND inventory_type='product' AND product_category={$category_id} ORDER BY link_is_featured DESC LIMIT 0, 20")) {

							if(mysqli_num_rows($productList)!=0) {

								while($productData=mysqli_fetch_assoc($productList)) {

									extract($productData);

									echo "<div class='col-md-3 col-6'>
										<div class='product-box'>
											<a href='{$link_url}'>
												".(($link_published>date('Y-m-d', strtotime('-7 days'))) ? "<span class='tag-new'>New</span>" : "" )."
												<div class='pro-img'>
													<img src='{$product_image}'>
												</div>
												<h3>{$product_title}</h3>
												<h4><span>{$inventory_currency_symbol} ".(($inventory_price!=0) ? $inventory_price : $inventory_mrp)."</span></h4>
											</a>
										</div>
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
		</section>

		<!-- product end -->

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

		<div class="botm-filter ds-min-768">
			<button class="btn-filr filtr1">Filter By</button>
			<button class="btn-filr filtr2">Sort By</button>
		</div>

		<div class="white-overly">
			<img src="<?php echo USER_IMAGES ?>close.png" id="close-overly">
			<div class="filter-left mob-filtr">
				<h2>Filter By</h2>
				<div class="filter-card">
					<form>
						<div class="form-group slct-bx">
						    <select class="form-control" id="exampleFormControlSelect1">
						      <option>Ring</option>
						      <option>Neckles</option>
						      <option>Bracelets</option>
						      <option>Earing</option>
						    </select>
						</div>
						<div class="form-group slct-bx">
						    <select class="form-control" id="exampleFormControlSelect1">
						      <option>Female</option>
						      <option>Male</option>
						      <option>Kids</option>
						    </select>
						</div>
						<div class="form-group slct-bx">
						    <select class="form-control" id="exampleFormControlSelect1">
						      <option>Red</option>
						      <option>Green</option>
						      <option>Cyan</option>
						    </select>
						</div>
						<div class="form-group slct-bx">
						    <select class="form-control" id="exampleFormControlSelect1">
						      <option>Rs: 5000 - Rs: 10000</option>
						      <option>Rs: 15000 - Rs: 30000</option>
						      <option>Rs: 30000 - Rs: 50000</option>
						    </select>
						</div>
					</form>
				</div>
			</div>

			<div class="filter-right mob-recom">
				<div class="recom">
					<h2>Sort By</h2>
					<div class="form-group slct-bx">
					    <select class="form-control" id="exampleFormControlSelect1">
					      <option>Recommended</option>
					      <option>Popular</option>
					      <option>Trending</option>
					      <option>Latest</option>
					    </select>
					</div>
				</div>
			</div>
		</div>

		<!-- sections end -->
<?php

	include_once 'footer.php';

?>