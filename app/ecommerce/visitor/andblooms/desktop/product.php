<?php

    include_once 'header.php';

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
			<div class="col-md-6">
				<div class="galery-carousel">
					<div class="lg-carousel-box">
						<div class="carousel carousel-main" data-flickity>
							<?php
							
							if($product_gallery!='') {

							} else {
								echo "<div class='carousel-cell'><img src='{$product_image}'></div>";
							}

							?>
						</div>
					</div>
					<div class="vert-carousel">
						<div class="carousel carousel-nav" data-flickity='{ "asNavFor": ".carousel-main", "contain": true, "pageDots": false }'>
							<?php
							
								if($product_gallery!='') {

								} else {
									echo "<div class='carousel-cell'><img src='{$product_image}'></div>";
								}

							?>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="prod-detail">
					<h2><?php echo $product_title ?></h2>
					<?php
					
						echo (($inventory_in_stock==0) ? "<h5 class='outStock'>Out of Stock</h5>" : "<h5 class='stock'>In Stock</h5>" );

					?>
					<p class="catag-lbel">Product Contains </p>
					<p class="catg-info"><?php echo $product_highlight ?></p>
					<div class="price-tg">

						<p style="display:none" id="productInfo" ><?php echo $product_id.'_'.(($inventory_price==0) ? $inventory_mrp : $inventory_price ).'_1' ?></p>

						<?php
						
							if($inventory_price==0) {

								echo "<h2 class='ofer-price'><img src='".USER_IMAGES."rupees.svg'> <span>{$inventory_mrp}</span></h2>";

							} else { ?>

								<h2 class="ofer-price"><img src="<?php echo USER_IMAGES ?>rupees.svg"> <span><?php echo $inventory_price ?></span></h2>
								<h2 class="with-ofer-price"><span><?php echo $inventory_mrp ?></span></h2>
								<h3 class="remain-days">(<?php echo round((1 - $inventory_price / $inventory_mrp) * 100, 0); ?>% Off)</h3>

							<?php
							}

						?>

					</div>
					<div class="cart-box">
						<span><img src="<?php echo USER_IMAGES ?>van.svg">Earliest delivery date:</span>
						<!-- <a href="/checkout/" id="addToCart" class="cart-ad-btn">Buy Now</a> -->
						<a href="#" id="addToCart" class="cart-ad-btn">Buy Now</a>
						<div class="deliver">
							<img src="<?php echo USER_IMAGES ?>deliver.png" alt="deliver">
						</div>
					</div>
					<div class="similar-product">
                        <h2>Product Description</h2>
						<p><?php echo $product_description ?></p>
						<h2>Care & Tips</h2>
						<ul>
							<li>Start by removing the bouquet wrapper.</li>
							<li>Cut the stems by one or two inches from the bottom to ensure better water intake.</li>
							<li>Choose a vase and fill it with water.</li>
							<li>Remove the leaves below the water level to prevent bacterial growth.</li>
							<li>Add two tablespoon of Lemon Juice and sugar into the water.</li>
							<li>Make sure to place the vase in indirect sunlight.</li>
						</ul>
						<div class="sku">
							<h4>SKU:</h4>
							<h3><?php echo $inventory_sku ?></h3>
						</div>
                        <!-- <h2>Reviews for Enticing Red Roses</h2>
						<div class="recomnd">
							<p class="star-rate"><img src="<?php echo USER_IMAGES ?>stars.svg"> 4.6 out of 5</p>
							<p class="star-recom"><img src="<?php echo USER_IMAGES ?>recomended.svg"> 4.6 out of 5</p>
						</div> -->
					</div>
				</div>
			</div>
	    </div>
	</div>
</section>

		<!-- section global platform end -->

		<!-- similar product start -->
		<section class="section-content">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="head-1">
							<h2>Similar Products</h2>
							<span class="cart-bodr-h2"></span>
						</div>
					</div>

					<div class="col-md-12">
						<div class="carousel carousel-home" data-flickity='{ "groupCells": true, "freeScroll": true,"pageDots": false, "contain": true }'>
							<div class="carousel-cell-home">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 115</p>
									</div>
								</div>
							</div>
							<div class="carousel-cell-home">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 145</p>
									</div>
								</div>
							</div>
							<div class="carousel-cell-home">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 255</p>
									</div>
								</div>
							</div>
							<div class="carousel-cell-home">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 1255</p>
									</div>
								</div>
							</div>
							<div class="carousel-cell-home">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 1255</p>
									</div>
								</div>
							</div>
							<div class="carousel-cell-home">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 1255</p>
									</div>
								</div>
							</div>
							<div class="carousel-cell-home">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 1255</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<div class="head-1">
							<h2>Related Products</h2>
							<span class="cart-bodr-h2"></span>
						</div>
					</div>

					<div class="col-md-12">
						<div class="carousel carousel-home related-card" data-flickity='{"groupCells": true, "freeScroll": true,"pageDots": false, "contain": true, "cellAlign": "left"}'>
							<div class="carousel-cell-home">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 115</p>
									</div>
								</div>
							</div>
							<div class="carousel-cell-home">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 145</p>
									</div>
								</div>
							</div>
							<div class="carousel-cell-home">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 255</p>
									</div>
								</div>
							</div>
							<div class="carousel-cell-home">
								<div class="col-card-3">
									<div class="event-card">
										<div class="img-cd">
											<img src="https://andblooms.com/wp-content/uploads/2020/06/Untitled-design-37.jpg">
										</div>
											<h4>Alexander Mcqueen</h4>
											<p class="rate">Rs. 255</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- similar product end -->

<?php

    include_once 'footer.php';

?>