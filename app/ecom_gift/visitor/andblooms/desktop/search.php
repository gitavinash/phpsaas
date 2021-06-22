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
                    <h1><?php echo str_replace('-', ' ', ucwords($searchKeyword)); ?></h1>
                    <h3><?php echo $tag_description ?></h3>
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
                            <!-- <li class="breadcrumb-item"><a href="<?php echo ACTIVE_URL ?>">Category</a></li> -->
                            <li class="breadcrumb-item active" aria-current="page"><?php echo ucwords($searchKeyword) ?>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row">

            <?php

						if(is_array($queryPost)) {

							if(!isset($productListType)) {

								for($i=0; $i<sizeof($queryPost); $i++) {

									$tagId[]=$queryPost[$i]['tag_relation_id'];
	
								}
	
								$resultTags=implode(' OR link_relation_id=', $tagId);
	
								if($queryResults=mysqli_query($connection, "SELECT link_url, product_title, product_image, inventory_currency, inventory_in_stock, inventory_price, inventory_mrp, link_published FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."product ON link_relation_id=product_id INNER JOIN ".TABLE_PREFIX."inventory ON inventory_relation_id=product_id WHERE link_site_id=".SITE_ID." AND link_type='product' AND link_status=200 AND inventory_in_stock=1 AND (link_relation_id={$resultTags}) ORDER BY link_id DESC LIMIT 0, 10")) {
	
									if(mysqli_num_rows($queryResults)!=0) {
		
										while($queryResultsData=mysqli_fetch_assoc($queryResults)) {
		
											extract($queryResultsData);
		
											echo "<div class='col-md-3'>
													<a href='{$link_url}'>
														<div class='col-card-3'>
															<div class='event-card'>
																<div class='img-cd'>
																	<img src='{$product_image}'>
																	".(($link_published>date('Y-m-d', strtotime('-7 days'))) ? "<span style='position: absolute;right: 0px;background: #D4946D;padding: 2px 15px;border-bottom-right-radius: 4px;color: #fff;font-size: 13px;margin-right: 20px;border-bottom-left-radius: 4px;' >New</span>" : '' )."
																</div>
																<h4>{$product_title}</h4>
																<p class='rate'>Rs. ".(($inventory_price!=0) ? $inventory_price : $inventory_mrp).(($inventory_in_stock!=1) ? '<span style="color: red; margin-left: 20px" >( Out of stock )</span>' : '' )."</p>
															</div>
														</div>
													</a>
												</div>";
		
										}
		
									}
		
								}

							} else {

								for($i=0; $i<sizeof($queryPost); $i++) {

									extract($queryPost[$i]);
									
									echo "<div class='col-md-3'>
										<a href='{$link_url}'>
											<div class='col-card-3'>
												<div class='event-card'>
													<div class='img-cd'>
														<img src='{$product_image}'>
														".(($link_published>date('Y-m-d', strtotime('-7 days'))) ? "<span style='position: absolute;right: 0px;background: #D4946D;padding: 2px 15px;border-bottom-right-radius: 4px;color: #fff;font-size: 13px;margin-right: 20px;border-bottom-left-radius: 4px;' >New</span>" : '' )."
													</div>
													<h4>{$product_title}</h4>
													<p class='rate'>Rs. ".(($inventory_price!=0) ? $inventory_price : $inventory_mrp).(($inventory_in_stock!=1) ? '<span style="color: red; margin-left: 20px" >( Out of stock )</span>' : '' )."</p>
												</div>
											</div>
										</a>
									</div>";

								}

							}

						} else { ?>

            <!-- No Result found -->

            <section class='continue-shopping'>
                <h1><span>No product found</span></h1>
                <a href="/" class="btn btn-success">Go to homepage</a>
            </section>

            <?php
						}

                    ?>

        </div>
    </div>
</section>

<?php

    include_once 'footer.php';

?>