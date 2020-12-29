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
							<h1><?php echo ucwords($tag_title) ?></h1>
							<h3><?php echo $tag_description ?></h3>
						</div>
					</div>
					
					<?php

						for($i=0; $i<sizeof($tagPost); $i++) {

							$tagId[]=$tagPost[$i]['tag_relation_id'];

						}

						$resultTags=implode(' OR link_relation_id=', $tagId);

						?>
							
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
							    <li class="breadcrumb-item active" aria-current="page"><?php echo ucwords($tag_title) ?></li>
							  </ol>
							</nav>
						</div>
					</div>
				</div>
				<div class="row">

                    <?php

						if($tagResults=mysqli_query($connection, "SELECT link_url, product_title, product_image, inventory_currency, inventory_price, inventory_mrp FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."product ON link_relation_id=product_id INNER JOIN ".TABLE_PREFIX."inventory ON inventory_relation_id=product_id WHERE link_site_id=".SITE_ID." AND link_type='product' AND link_status=200 AND inventory_in_stock=1 AND (link_relation_id={$resultTags}) AND link_site_id=".SITE_ID." ORDER BY link_published DESC LIMIT 0, 20")) {

							if(mysqli_num_rows($tagResults)!=0) {

								while($tagResultsData=mysqli_fetch_assoc($tagResults)) {

									extract($tagResultsData);

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

						}

                    ?>

				</div>
			</div>
		</section>

<?php

    include_once 'footer.php';

?>