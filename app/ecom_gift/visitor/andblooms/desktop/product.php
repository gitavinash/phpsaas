<?php

	if($checkProductMeta=mysqli_query($connection, "SELECT product_meta_key, product_meta_value FROM ".TABLE_PREFIX."product_meta WHERE product_meta_relation_id='{$product_id}' AND product_meta_site_id=".SITE_ID)) {

		if(mysqli_num_rows($checkProductMeta)!=0) {

			$productMeta=array();

			while($checkProductMetaData=mysqli_fetch_assoc($checkProductMeta)) {


				// Create dynamic variable in the name of product_meta_key value

				${$checkProductMetaData['product_meta_key']}=$checkProductMetaData['product_meta_value'];

			}

		}

	}

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

                <div class="gallery-carousel">

                    <div class="lg-carousel-box" id="sticky">

                        <div class="product-main-slider">
                            <?php
							
							$galleryImages=json_decode($product_gallery, TRUE);
							
							if($galleryImages!='') {

								// echo "<div class='slide'><div class='img' style='background: url({$product_image}) center center no-repeat' ></div></div>";
                                echo "<div class='slide'><div class='img' ><img id='mainProductGalleryImage1' src='{$product_image}' alt='{$product_title}' /></div></div>";

								for($i=0; $i<sizeof($galleryImages); $i++) {

									extract($galleryImages[$i]);
                                    $imageNo=(($i==0) ? 2 : $i+2 );
									// echo "<div class='slide'><div class='img' style='background: url({$path}) center center no-repeat'></div></div>";
                                    echo "<div class='slide'><div class='img zoomImage'><img src='{$path}' id='mainProductGalleryImage{$imageNo}' alt='' /></div></div>";

								}


							} else {

								echo "<div class='slide'><div class='img zoomImage'><img src='{$product_image}' alt='{$product_title}' /></div></div>";

							}

							?>
                        </div>

                        <div class="product-main-slider-nav">
                            <?php
							
							$galleryImages=json_decode($product_gallery, TRUE);
							
							if($galleryImages!='') {

								echo "<div class='slide'><div class='img'><img src='{$product_image}'></div></div>";

								for($i=0; $i<sizeof($galleryImages); $i++) {

									extract($galleryImages[$i]);
									echo "<div class='slide'><div class='img'><img src='{$path}'></div></div>";

								}

							}

							?>
                        </div>

                        <?php
                        
                            if($galleryImages!='') {

								echo "<div id='mainProductGalleryImage1Preview' class='productZoomImagePreview'></div>";

								for($i=0; $i<sizeof($galleryImages); $i++) {

                                    $imageNo=(($i==0) ? 2 : $i+2 );
									echo "<div id='mainProductGalleryImage{$imageNo}Preview' class='productZoomImagePreview'></div>";

								}

							}

                        ?>

                    </div>

                </div>

            </div>
            <div class="col-md-6">

                <div class="prod-detail" id="productDetails">
                    <h2><?php echo $product_title ?></h2>
                    <?php
					
						echo (($inventory_in_stock==0) ? "<h5 class='outStock'>Out of Stock</h5>" : "<h5 class='stock'>In Stock</h5>" );

					?>
                    <p class="catag-lbel">Product Contains </p>
                    <p class="catg-info"><?php echo $product_highlight ?></p>
                    <div class="price-tg">

                        <p style="display:none" id="productInfo"
                            value="<?php echo $product_id.'_'.(($inventory_price==0) ? $inventory_mrp : $inventory_price ).'_1' ?>">
                        </p>

                        <?php
						
							if($inventory_price==0) {

								echo "<h2 class='ofer-price'><img src='".USER_IMAGES."rupees.svg'> <span>{$inventory_mrp}</span></h2>";

							} else { ?>

                        <h2 class="ofer-price"><img src="<?php echo USER_IMAGES ?>rupees.svg">
                            <span><?php echo $inventory_price ?></span>
                        </h2>
                        <h2 class="with-ofer-price"><span><?php echo $inventory_mrp ?></span></h2>

                        <?php

								if($inventory_mrp!='') {

									if($inventory_mrp!=$inventory_price) {

										$discountPersantage=round((1 - $inventory_price / $inventory_mrp) * 100, 0);

										echo "<h3 class='remain-days'>({$discountPersantage}% OFF)</h3>";

									}

								}

								?>

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
                        <input type="hidden" id="qty"
                            value="<?php echo $product_id.'_1_'.(($inventory_price!=0) ? $inventory_price : $inventory_mrp) ?>">
                        <!-- <span><img src="<?php echo USER_IMAGES ?>van.svg">Earliest delivery date: <?php echo date('d M Y', strtotime('+1 days')) ?></span> -->
                        <span><img src="<?php echo USER_IMAGES ?>van.svg">Same day delivery</span>

                        <?php
					
							echo (($inventory_in_stock==0) ? "<a class='out-of-stock'>Out of stock</a>" : "<a href='#' id='addToCart' class='cart-ad-btn'>Buy Now</a>" );

						?>

                        <p style="margin-top:5px">
                            <span>Availability in
                                <strong
                                    style="color: #2F9100"><?php echo ((isset($availability_cities)) ? $availability_cities : 'Delhi' ) ?></strong>
                                only</span>

                        </p>

                        <!-- <div class="deliver">
                            <img src="<?php echo USER_IMAGES ?>deliver.png" alt="deliver">
                        </div> -->
                    </div>
                    <div class="similar-product">
                        <h2>Product Description</h2>
                        <p><?php echo $product_description ?></p>
                        <h2>Care & Tips</h2>
                        <div class="care_n_tips">
                            <?php
							
							if(isset($care_n_tips)) {

                                if($care_n_tips!='') {

								    echo '<ul><li>'.str_replace('<br />', '</li><li>', $care_n_tips).'</li></ul>';

                                } else{

                                     ?>

                            <ul>
                                <li>Start by removing the bouquet wrapper.</li>
                                <li>Cut the stems by one or two inches from the bottom to ensure better water intake.
                                </li>
                                <li>Choose a vase and fill it with water.</li>
                                <li>Remove the leaves below the water level to prevent bacterial growth.</li>
                                <li>Add two tablespoon of Lemon Juice and sugar into the water.</li>
                                <li>Make sure to place the vase in indirect sunlight.</li>
                            </ul>

                            <?php

                                }

							} else { ?>

                            <ul>
                                <li>Start by removing the bouquet wrapper.</li>
                                <li>Cut the stems by one or two inches from the bottom to ensure better water intake.
                                </li>
                                <li>Choose a vase and fill it with water.</li>
                                <li>Remove the leaves below the water level to prevent bacterial growth.</li>
                                <li>Add two tablespoon of Lemon Juice and sugar into the water.</li>
                                <li>Make sure to place the vase in indirect sunlight.</li>
                            </ul>

                            <?php
							}

							?>
                        </div>
                        <h2>Delivery Information</h2>
                        <div class="care_n_tips">
                            <?php
							
							if(isset($delivery_information)) {

                                if($delivery_information!='') {

								    echo '<ul><li>'.str_replace('<br />', '</li><li>', $delivery_information).'</li></ul>';

                                }  else {

                                     ?>

                            <ul>
                                <li>The image displayed is indicative. Actual product may vary in design & shape</li>
                                <li>Flowers may deliver in Bud, Semi Bloomed or Fully Bloomed stage.</li>
                                <li>The chosen delivery time is an estimate.</li>
                                <li>Since flowers are perishable in nature, we will be able to attempt delivery of your
                                    order once.</li>
                                <li>Occasionally, substitution of flowers is necessary due unavailability issues.</li>
                            </ul>

                            <?php

                                }

							} else { ?>

                            <ul>
                                <li>The image displayed is indicative. Actual product may vary in design & shape</li>
                                <li>Flowers may deliver in Bud, Semi Bloomed or Fully Bloomed stage.</li>
                                <li>The chosen delivery time is an estimate.</li>
                                <li>Since flowers are perishable in nature, we will be able to attempt delivery of your
                                    order once.</li>
                                <li>Occasionally, substitution of flowers is necessary due unavailability issues.</li>
                            </ul>

                            <?php
							}

							?>
                        </div>
                        <!-- SKU  -->
                        <?php echo (($inventory_sku!='') ? "<div class='sku'><h4>SKU:</h4><h3>{$inventory_sku}</h3></div>" : "") ?>
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
                <div class="product-slider">


                    <?php
				
				if($tagListData=mysqli_query($connection, "SELECT tag_title as relatedTags FROM isw_tag WHERE tag_relation_id='{$product_id}' LIMIT 0, 1")) {

					if(mysqli_num_rows($tagListData)!=0) {
					
						extract(mysqli_fetch_assoc($tagListData));
						$relatedProductQuery= "SELECT link_url, product_title, product_image, inventory_currency, inventory_price, inventory_mrp FROM isw_tag INNER JOIN isw_link ON link_relation_id=tag_relation_id INNER JOIN isw_product ON product_id=link_relation_id INNER JOIN isw_inventory ON inventory_relation_id=product_id WHERE tag_title LIKE '%{$relatedTags}%' AND link_status=200 AND link_type='product' AND link_site_id='".SITE_ID."' AND inventory_in_stock=1 GROUP BY tag_relation_id ORDER BY RAND() LIMIT 0, 10";
					
					} else {

						$productRelated=explode(' ', $product_title);
					
						$relatedProductQuery= "SELECT link_url, product_title, product_image, inventory_currency, inventory_price, inventory_mrp FROM isw_tag INNER JOIN isw_link ON link_relation_id=tag_relation_id INNER JOIN isw_product ON product_id=link_relation_id INNER JOIN isw_inventory ON inventory_relation_id=product_id WHERE tag_title LIKE '%{$productRelated[0]}%' AND link_status=200 AND link_type='product' AND link_site_id='".SITE_ID."' AND inventory_in_stock=1 GROUP BY tag_relation_id ORDER BY RAND() LIMIT 0, 10";
					
					}
				
				}

				if($relatedProductsData=mysqli_query($connection, $relatedProductQuery)) {

					if(mysqli_num_rows($relatedProductsData)!=0) {
					
						while($relatedProducts=mysqli_fetch_assoc($relatedProductsData)) {
					
							extract($relatedProducts);

							echo "
							<div class='product-slide'>
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
                    <h2>&Blooms</h2>
                    <hr class="border-tp">
                </div>
            </div>

            <div class="col-md-12">
                <div class="why-bloom">
                    <div class="flower-box">
                        <div class="img-why box-b">
                            <img src="<?php echo USER_IMAGES; ?>artistic-flowers.jpg">
                        </div>
                        <div class="why-cont box-b">
                            <h3>Fresh Flower</h3>
                            <!-- <h2>We deliver the finest & freshest</h2> -->
                        </div>
                    </div>

                    <div class="flower-box">
                        <div class="why-cont box-b dn-s">
                            <h3>Artistic Flowers</h3>
                            <!-- <h2>Racing quick to win you love</h2> -->
                        </div>
                        <div class="img-why box-b">
                            <img src="<?php echo USER_IMAGES; ?>freshness-flower.jpg">
                        </div>
                        <div class="why-cont box-b dn-l">
                            <h3>Artistic Flowers</h3>
                            <!-- <h2>Racing quick to win you love</h2> -->
                        </div>

                    </div>

                    <div class="flower-box">
                        <div class="img-why box-b">
                            <img src="<?php echo USER_IMAGES; ?>vases-an-accessories.jpg">
                        </div>
                        <div class="why-cont box-b">
                            <h3>Vases & accessories</h3>
                            <!-- <h2>Over 1 Million Smiles delivered</h2> -->
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-content cloth-card" style="padding: 0px 0px 20px 0px">
    <div class="container">
        <div class="row">
            <div class="head-1">
                <h2>Quick Links</h2>
                <span class="cart-bodr-h2"></span>
            </div>
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

<?php

    include_once 'footer.php';

?>