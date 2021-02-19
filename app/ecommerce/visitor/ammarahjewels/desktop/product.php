<?php

	include_once 'header.php';

?>
				<!-- section global platform start -->
		<section class="section-content">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="brdcrum">
							<nav aria-label="breadcrumb">
							  <ol class="breadcrumb">
							    <li class="breadcrumb-item"><a href="/">Home</a></li>
							    <!-- <li class="breadcrumb-item"><a href="#">Product</a></li> -->
							    <li class="breadcrumb-item active" aria-current="page"><?php echo $product_title ?></li>
							  </ol>
							</nav>
						</div>
					</div>
					<div class="col-md-6">
						<div class="product-gallery">

							<div class='show-pro-box' href='<?php echo $product_image ?>'>
								<img src='<?php echo $product_image ?>' id='show-img'>
							</div>
							
							<div class="small-img">
								<img src='<?php echo USER_IMAGES ?>left-arow.png' class='icon-left' alt='' id='prev-img'>
								<div class="small-container">
									<div id="small-img-roll">
										<?php
											
											$galleryImages=explode(', ', $product_gallery);

											if($galleryImages!='') {

												for($i=0; $i<sizeof($galleryImages); $i++) {
														
													echo "<img src='{$galleryImages[$i]}' class='show-small-img' alt=''>";

												}

											}

										?>
									</div>
					    		</div>
								<img src='<?php echo USER_IMAGES ?>right-arow.png' class='icon-right' alt='' id='next-img'>
							</div>

						</div>

					</div>
					<div class="col-md-6">
						<div class="prod-detail">
							
							<?php
							
								echo (($inventory_in_stock==0) ? "<h5 class='outStock'>Out of Stock</h5>" : "<h5 class='stock'>In Stock</h5>" );

							?>

							<h2><?php echo $product_title ?></h2>
							<!-- <p class="catag-lbel">Categories : <span> Women</span></p> -->
							<p class="pro-info"><?php echo $product_highlight ?></p>
							<div class="quantaty">

								<?php
								
									if($productMeta=mysqli_query($connection, "SELECT product_meta_name, product_meta_value FROM ".TABLE_PREFIX."product_meta WHERE product_meta_relation_id='{$product_id}' AND product_meta_site_id=".APP_SITE_ID)) {

										if(mysqli_num_rows($productMeta)!=0) {

											while($productMetaData=mysqli_fetch_assoc($productMeta)) {

												extract($productMetaData);
												?>

												<div class="form-group">
													<label><?php echo $product_meta_name ?></label>
													<select name="<?php echo strtolower(str_replace(' ', '_', $product_meta_name)); ?>" id="<?php echo strtolower(str_replace(' ', '_', $product_meta_name)); ?>" class="orderMeta form-control">
														<?php
														
															$productMetaValue=explode(',', $product_meta_value);

															for($i=0; $i<sizeof($productMetaValue); $i++) {

																echo "<option value='{$productMetaValue[$i]}'>{$productMetaValue[$i]}</option>";

															}

														?>
													</select>
												</div>

												<?php

											}

										}

									}

								?>

							</div>

							<div class="price-tg">
								<h2 class="ofer-price">Price : <span><?php echo $inventory_currency_symbol.' '.$inventory_price; ?></span></h2>
								<!-- <h2 class="with-ofer-price">₹ 203.00 - ₹ 263.00</h2> -->
							</div>
							<div class="cart-box">
								<a href="#" class="buyNow cart-ad-btn" id="<?php echo $product_id ?>" >Add to Cart</a>
								<div class="wish-btns">
									<div class="icon-wishlist"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="product-discription">
							<ul class="nav nav-tabs" id="myTab" role="tablist">
	  							<li class="nav-item">
								    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Details</a>
								  </li>
								  <!-- <li class="nav-item">
								    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">More Information</a>
								  </li> -->
								  <!-- <li class="nav-item">
								    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Review</a>
								  </li> -->
								</ul>
								<div class="tab-content" id="myTabContent">
								  	<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
										<?php echo $product_description ?>
								  	</div>
								  	<!-- <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
								  		<table class="table table-striped">
											<tbody>
											    <tr>
											      <th scope="row">Manufacturer</th>
											      <td>Germany</td>
											    </tr>
											    <tr>
											    	<th scope="row">Gender</th>
											    	<td>Female</td>
											    </tr>
											    <tr>
											    	<th scope="row">Care</th>
											    	<td>Protect From sun</td>
											    </tr>
											    
											  </tbody>
										</table>
								  	</div> -->
								</div>
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
						<div class="heading2 ">
							<h2>Trending Products</h2>
							<hr class="bdr-gry">
							<p>We know that your jewelry means a lot to you, and that's why we offer a 1-year warranty on it.</p>
						</div>
					</div>

					<div class="col-md-3 col-6">
						<div class="product-box light-bg">
							<a href="">
								<span class="tag-new">New</span>
								<div class="pro-img">
									<img src="<?php echo USER_IMAGES ?>home-cat-01.png">
								</div>
								<h3>Rings</h3>
								<h4>Rs. <span>900.00</span></h4>
							</a>
						</div>
					</div>
					
				</div>
			</div>
		</section>
		<!-- product end -->
<?php

	include_once 'footer.php';

?>