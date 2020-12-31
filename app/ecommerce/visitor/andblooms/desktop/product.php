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

								$galleryImages=json_decode($product_gallery, TRUE);

								for($i=0; $i<sizeof($galleryImages); $i++) {

									extract($galleryImages[$i]);
									echo "<div class='carousel-cell'><img src='{$path}'></div>";

								}

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

								$galleryImages=json_decode($product_gallery, TRUE);

								for($i=0; $i<sizeof($galleryImages); $i++) {

									extract($galleryImages[$i]);
									echo "<div class='carousel-cell'><img src='{$path}'></div>";

								}

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
                    <!-- <div class="quantaty">
						<div class="siz-bx">
							<form> 
								<input type="checkbox" id="fruit1" name="fruit-1" value="Apple">
	  							<label class="size">Double the flowers @ Rs. 349</label><br>
	  							<input type="checkbox" id="fruit1" name="fruit-1" value="Apple">
	  							<label class="size">Add Vase @ Rs. 200</label>
							</form>
						</div>
					</div> -->
					<div class="cart-box">
						<input type="hidden" id="qty" value="<?php echo $product_id.'_1_'.(($inventory_price!=0) ? $inventory_price : $inventory_mrp) ?>" >
						<span><img src="<?php echo USER_IMAGES ?>van.svg">Earliest delivery date: <?php echo date('d M Y', strtotime('+3 days')) ?></span>
						
						<?php
					
							echo (($inventory_in_stock==0) ? "<a class='out-of-stock'>Out of stock</a>" : "<a href='#' id='addToCart' class='cart-ad-btn'>Buy Now</a>" );

						?>

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
							

				<?php
				
				if($tagListData=mysqli_query($connection, "SELECT tag_title as relatedTags FROM isw_tag WHERE tag_relation_id='{$product_id}' LIMIT 0, 1")) {

					if(mysqli_num_rows($tagListData)!=0) {
					
						extract(mysqli_fetch_assoc($tagListData));
						$relatedProductQuery= "SELECT link_url, product_title, product_image, inventory_currency, inventory_price, inventory_mrp FROM isw_tag INNER JOIN isw_link ON link_relation_id=tag_relation_id INNER JOIN isw_product ON product_id=link_relation_id INNER JOIN isw_inventory ON inventory_relation_id=product_id WHERE tag_title LIKE '%{$relatedTags}%' AND link_status=200 AND link_type='product' AND link_site_id='".SITE_ID."' AND inventory_in_stock=1 GROUP BY tag_relation_id ORDER BY tag_time RAND() LIMIT 10";
					
					} else {
					
						$relatedProductQuery= "SELECT link_url, product_title, product_image, inventory_currency, inventory_price, inventory_mrp FROM isw_tag INNER JOIN isw_link ON link_relation_id=tag_relation_id INNER JOIN isw_product ON product_id=link_relation_id INNER JOIN isw_inventory ON inventory_relation_id=product_id WHERE tag_title LIKE '%{$relatedTags}%' AND link_status=200 AND link_type='product' AND link_site_id='".SITE_ID."' AND inventory_in_stock=1 GROUP BY tag_relation_id ORDER BY tag_time RAND() LIMIT 10";
					
					}
				
				}

				if($relatedProductsData=mysqli_query($connection, $relatedProductQuery)) {

					if(mysqli_num_rows($relatedProductsData)!=0) {
					
						while($relatedProducts=mysqli_fetch_assoc($relatedProductsData)) {
					
							extract($relatedProducts);

							echo "
							<div class='carousel-cell-home'>
								<a href='{$link_url}'>
									<div class='col-card-3'>
										<div class='event-card'>
											<div class='img-cd'>
												<img src='{$product_image}'>
											</div>
												<h4>{$product_title}</h4>
												<p class='rate'>Rs. {$inventory_price}</p>
										</div>
									</div>
								</a>
							</div>
						";

						}

					}

				}

				?>
				
				</div>
			</div>
		</div>

	</div>
</section>

<section class="section-content">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="head-2">
					<h2>Why & Blooms?</h2>
					<hr class="border-tp">
				</div>
			</div>

			<div class="col-md-12">
				<div class="why-bloom">
					<div class="flower-box">
						<div class="img-why box-b">
							<img src="<?php echo USER_IMAGES; ?>Freshness-Guranteed.jpg">
						</div>
						<div class="why-cont box-b">
							<h3>Freshness Guaranteed</h3>
							<h2>We deliver the finest & freshest</h2>
						</div>
					</div>

					<div class="flower-box">
						<div class="why-cont box-b dn-s">
							<h3>Delivery In 3 Hours</h3>
							<h2>Racing quick to win you love</h2>
						</div>
						<div class="img-why box-b">
							<img src="<?php echo USER_IMAGES; ?>delivery_in_3hours.jpg">
						</div>
						<div class="why-cont box-b dn-l">
							<h3>Delivery In 3 Hours</h3>
							<h2>Racing quick to win you love</h2>
						</div>
							
					</div>

					<div class="flower-box">
						<div class="img-why box-b">
							<img src="<?php echo USER_IMAGES; ?>dedicated-efforts.jpg">
						</div>
						<div class="why-cont box-b">
							<h3>Dedicated Efforts</h3>
							<h2>Over 1 Million Smiles delivered</h2>
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