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
					            <img src="<?php echo USER_IMAGES ?>left-arow.png" class="icon-left" alt="" id="prev-img">

					            <div class="small-container">
					               	<div id="small-img-roll">
									<?php
									
										$galleryImages=json_decode($product_gallery, TRUE);
										
										if($galleryImages!='') {

											for($i=0; $i<sizeof($galleryImages); $i++) {
												
												extract($galleryImages[$i]);

												echo "<img src='{$galleryImages[$i]}' class='show-small-img' alt=''>";

											}

										} else {

											echo "<img src='{$product_image}' class='show-small-img' alt=''>";

										}

									?>
									</div>
								</div>

								<img src="<?php echo USER_IMAGES ?>right-arow.png" class="icon-right" alt="" id="next-img">
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

								<div class="quant-bx">
									Quantity : <select id="cars" name="carlist" form="carform">
									  		<option value="volvo">1</option>
									  		<option value="saab">2</option>
									  		<option value="opel">3</option>
									  		<option value="audi">4</option>
										</select> 
								</div>

								<div class="siz-bx">
									<form>
										<span>Size :</span>  
										<input type="checkbox" id="fruit1" name="fruit-1" value="Apple">
	  									<label class="size">5</label>
									</form>
								</div>

							</div>

							<div class="price-tg">
								<h2 class="ofer-price">Price : <span>₹ 193.00 - ₹ 203.00</span></h2>
								<h2 class="with-ofer-price">₹ 203.00 - ₹ 263.00</h2>
							</div>
							<div class="cart-box">
								<a href="" id="addToCart" class="cart-ad-btn">Add to Cart</a>
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
								  <li class="nav-item">
								    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">More Information</a>
								  </li>
								  <!-- <li class="nav-item">
								    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Review</a>
								  </li> -->
								</ul>
								<div class="tab-content" id="myTabContent">
								  	<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
									  	<p>
										We started the fashion revolution as being the first brand to produce jeans. Along the way we realized that our collection is timeless and keeps its value. A beautiful pair of jeans keeps its value. That’s why we have let go of the season collections.</p>
										<p>
										Find your perfect fit in New collection. Our designers work tirelessly to bring you awesome jeans for you.</p>
										<p>
										If you are in a search of fine shoes which would meet your design and functional requirements, and meanwhile you are willing to save your time, an online store is a perfect option for you. We all know how frustrating choosing a proper pair of shoes may be, because the rule “everything is great except something” works. And right now you don’t have to waste your time, you don’t have to spend your precious time even trying the shoes on - you may relax, sit back in your chair and prepare for choosing a perfect pair of shoes right away.</p>
										<p>
										First of all, you’ll save your time. Your channel your attention towards shoes only, and nothing else gets you side-tracked (like it usually happens in stores). What do you need to know to be able to choose your perfect pair of shoes? Your sizing. If you don’t know it, have a look at our sizing table, find out the parameters which would work for you and use them as your filter when searching through the website.</p>
										<p>
										Filter is your assistant number one in this online store. Use it to choose a design which you like, a color and a size. There are some extra parameters which may help you choose a pair of shoes. Don’t forget that you may choose shoes by brand, if you are a devoted fan of a specific label, you are very likely to find your perfect pair, produced by that very brand.</p>
										<p>
										Shoes are always accompanied by accessories, which is why we bet you will enjoy our category “Bags and Handbags”. As we offer items both for men and women, and, moreover, for kids - you will enjoy a huge variety of the items offered. What about shipping and return policy? We provide free shipping, and you have 365 days to return the shoes. We promise you that you will leave this store happy, having found what you have been looking for.</p>
								  </div>
								  <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
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
								  </div>
								  <!-- <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">...</div> -->
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