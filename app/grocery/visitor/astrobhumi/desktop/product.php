<?php

	require_once 'header.php';

?>

		<!-- section global platform start -->
		<section class="banner-section pro-galery">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="brdcrum">
							<nav aria-label="breadcrumb">
							  <ol class="breadcrumb">
							    <li class="breadcrumb-item"><a href="#">Home</a></li>
							    <li class="breadcrumb-item"><a href="#">Product</a></li>
							    <li class="breadcrumb-item active" aria-current="page"><?php echo $product_title ?></li>
							  </ol>
							</nav>
						</div>
					</div>
					<div class="col-md-6" style="padding-bottom: 0;">
						<div class="galery-carousel">

							<?php

								if($product_gallery!='') {

									for($i=0; $i<sizeof($galleryImages); $i++) {
														
										echo "<img src='{$galleryImages[$i]}' class='show-small-img' alt=''>";

									}

								} else { ?>

									<div class="carousel carousel-main" data-flickity='{ "asNavFor": ".carousel-main", "contain": true, "imagesLoaded": true, "pageDots": false }'>
							  			<div class="carousel-cell"><img src="<?php echo $product_image ?>" alt="Tar Free The - Ultimate Herbal Lung Detox" ></div>
									</div>

								<?php
								}

							?>
						</div>

					</div>
					<div class="col-md-6" style="padding-bottom: 0;">
						<div class="prod-detail">
							<h5 class="stock">In Stock</h5>
							<!-- <h5 class="outStock">Out of Stock</h5> -->
							<h2><?php echo $product_title ?></h2>
							<p class="catag-lbel">Categories : <span> <?php echo $category_title ?></span></p>
							<p class="pro-info"><?php echo $product_description ?></p>
							
							<form action="qtyForm" >
								<!-- <div class="quantaty">
									<div class="quant-bx">
										Quantity :
										<select id="qty" >
											<option value="3_1_348" se >1</option>
											<option value="3_2_627" >2</option>
											<option value="3_3_888" >3</option>
										</select>
									</div>
								</div> -->
								<div class="cart-box">
									<a href="#" class="cart-ad-btn addToCart" >Add to Cart</a>
								</div>
							</form>

							<div class="social-2"> <span>Share </span>
								<a href="https://www.facebook.com/sharer.php?u=<?php echo ACTIVE_SITE_URL ?>"><i class="fab fa-facebook-square" aria-hidden="true"></i></a>
								<a href="https://twitter.com/intent/tweet?url=<?php echo ACTIVE_SITE_URL ?>&text=<?php echo $product_title ?>"><i class="fab fa-twitter-square" aria-hidden="true"></i></a>
								<a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo ACTIVE_SITE_URL ?>"><i class="fab fa-linkedin" aria-hidden="true"></i></a>
								<a href="https://reddit.com/submit?url=<?php echo ACTIVE_SITE_URL ?>&title=<?php echo $product_title ?>"><i class="fab fa-reddit-square" aria-hidden="true"></i></a>
							</div>
						</div>
					</div>
					
				</div>
			</div>
		</section>
		<!-- section global platform end -->

<?php

	require_once 'footer.php';

?>