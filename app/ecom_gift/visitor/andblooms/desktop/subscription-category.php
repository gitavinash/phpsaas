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
                    <h1><?php echo $category_title ?></h1>
                    <h3><?php echo $category_description ?></h3>
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
                            <li class="breadcrumb-item active" aria-current="page"><?php echo $category_title ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row">

            <?php

					if($productList=mysqli_query($connection, "SELECT link_url, product_title, product_image, inventory_price, inventory_currency_symbol, inventory_in_stock, inventory_mrp, link_published FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."product ON link_relation_id=product_id INNER JOIN ".TABLE_PREFIX."inventory ON inventory_relation_id=product_id INNER JOIN isw_category ON category_id=product_category WHERE link_site_id=".SITE_ID." AND link_type='product' AND link_status=200 AND inventory_type='product' AND product_category={$category_id} ORDER BY link_is_featured DESC LIMIT 0, 100")) {

						if(mysqli_num_rows($productList)!=0) {

							while($productData=mysqli_fetch_assoc($productList)) {

								extract($productData);

								echo "<div class='col-md-3 col-6'>
										<a href='{$link_url}'>
											<div class='col-card-3'>
												<div class='event-card'>
													<div class='img-cd'>
														<img src='{$product_image}'>
														".(($link_published>date('Y-m-d', strtotime('-7 days'))) ? "<span style='position: absolute;right: 0px;background: #D4946D;padding: 2px 15px;border-bottom-right-radius: 4px;color: #fff;font-size: 13px;margin-right: 20px;border-bottom-left-radius: 4px;' >New</span>" : '' )."
													</div>
													<h4>{$product_title}</h4>
													<p class='rate'>".$inventory_currency_symbol.' '.(($inventory_price!=0) ? $inventory_price : $inventory_mrp).(($inventory_in_stock!=1) ? '<span style="color: red; margin-left: 20px" >( Out of stock )</span>' : '' )."</p>
												</div>
											</div>
										</a>
									</div>";
                                    
							}

						} else {

							echo 'No product found';

						}

					}

				?>

        </div>
    </div>
</section>

<!-- header end -->
<section class="banner-section pro-galery">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="account-backend">
                    <h2 class="inter-heading cont-hed">Subscription</h2>
                    <h6>What's &amp; Blooms subscription?</h6>
                    <p>&amp; Blooms subscription simplifies life when you want flowers each year for your loved one as a
                        gift! You can set up flowers to arrive on a regular schedule, say birthday, anniversary, parents
                        birthday.</p>
                    <h6>What's' your happiness guarantee?</h6>
                    <p>We want to make sure you're happy with your &amp; Blooms.</p>
                    <h6>What if I want less flowers?</h6>
                    <p>No problem. You can skip upcoming flower and reschedule.</p>
                    <h6>Subscribe To Happiness</h6>
                    <p>Need a thoughtful gift or want to treat yourself? Fresh flowers delivered on your schedule. Save
                        20% + free shipping every time. The best part? The flowers keep coming.</p>
                    <p><a href="https://andblooms.com/category/subscription/"><img
                                src="/upload/2021/06/1623694292_whatsapp_image_2021_06_11_at_1.13.19_pm.jpeg"
                                width="100%"></a></p>
                </div>

            </div>
        </div>
    </div>

    </div>
</section>


<?php

    include_once 'footer.php';

?>