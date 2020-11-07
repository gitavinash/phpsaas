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
							<h1><?php echo $tag_title ?></h1>
							<h3><?php echo $tag_description ?></h3>
						</div>
					</div>
					
					<?php
								
								if($productList=mysqli_query($connection, "SELECT link_url, product_title, product_image, inventory_price, inventory_mrp FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."product ON link_relation_id=product_id INNER JOIN ".TABLE_PREFIX."inventory ON inventory_relation_id=product_id WHERE link_site_id=".SITE_ID." AND link_type='product' AND link_is_featured=1 AND inventory_in_stock=1 ORDER BY link_id DESC LIMIT 0, 10")) {

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
							    <li class="breadcrumb-item active" aria-current="page"><?php echo $tag_title ?></li>
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

				</div>
			</div>
		</section>

<?php

    include_once 'footer.php';

?>