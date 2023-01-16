<?php

include_once 'header.php';

?>
<!-- section global platform start -->
<section class="banner-section">
    <div class="container-fluid">
        <div class="row">
            <div class="card-ban">
                <div class="slider"><img src="<?php echo USER_IMAGES ?>banner/1.png"></div>
                <div class="slider"><img src="<?php echo USER_IMAGES ?>banner/2.png"></div>
                <div class="slider"><img src="<?php echo USER_IMAGES ?>banner/3.png"></div>
            </div>
        </div>
    </div>
</section>
<!-- section global platform end -->

<section class="section-content cloth-card">
    <div class="container">
        <div class="row">
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

                <a href="/tag/everlasting/" class="card-link">
                    <div class="acce-card">
                        <div class="img-acc">
                            <img src="/upload/2021/02/1612253362_img_1849.jpg" style="margin-left: -20px">
                        </div>
                        <div class="acc-card-head">
                            <h4>Everlasting</h4>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="section-content bg-light">

    <div class="container">

        <!-- New collection -->
        <div class="row">

            <div class="col-md-12">
                <div class="head-1">
                    <a href="/tag/love/">
                        <h2>New Collection</h2>
                    </a>
                    <span class="cart-bodr-h2"></span>
                    <!-- <div class="view-box">
                        <a href="/shop/" class="btn-vw">View All</a>
                    </div> -->
                </div>
            </div>

            <div class="col-md-12">

                <div class="product-slider">

                    <?php

                    if ($productList = mysqli_query($connection, "SELECT link_id, link_url, product_title, product_image, inventory_currency, inventory_currency_symbol, inventory_price FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id WHERE inventory_type='product' AND link_type='product' AND link_status=200 AND inventory_in_stock=1 AND link_site_id=" . SITE_ID . " ORDER BY link_published DESC LIMIT 0, 12")) {

                        if (mysqli_num_rows($productList) != 0) {

                            while ($productData = mysqli_fetch_assoc($productList)) {

                                extract($productData);

                                echo "<div class='product-slide'>
												<a href='{$link_url}'>
													<div class='col-card-3'>
														<div class='event-card'>
															<div class='img-cd'><img src='{$product_image}' /></div>
															<h4>{$product_title}</h4>
															<p class='rate'>" . $inventory_currency_symbol . ' ' . (($inventory_price != 0) ? $inventory_price : $inventory_mrp) . "</p>
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

        </div>

        <!-- Love collection -->
        <div class="row">

            <div class="col-md-12">
                <div class="head-1">
                    <a href="/tag/love/">
                        <h2>Love Collection</h2>
                    </a>
                    <span class="cart-bodr-h2"></span>
                    <div class="view-box">
                        <a href="/tag/love/" class="btn-vw">View All</a>
                    </div>
                </div>
            </div>

            <div class="col-md-12">

                <div class="product-slider">

                    <?php

                    if ($productList = mysqli_query($connection, "SELECT tag_relation_id, link_id, link_url, product_title, product_image, inventory_currency, inventory_currency_symbol, inventory_price FROM " . TABLE_PREFIX . "tag INNER JOIN " . TABLE_PREFIX . "product ON product_id=tag_relation_id INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id WHERE tag_type='product' AND tag_title='love' AND inventory_type='product' AND link_type='product' AND link_status=200 AND inventory_in_stock=1 AND tag_site_id=" . SITE_ID . " AND link_site_id=" . SITE_ID . "  ORDER BY link_published DESC LIMIT 0, 12")) {

                        if (mysqli_num_rows($productList) != 0) {

                            while ($productData = mysqli_fetch_assoc($productList)) {

                                extract($productData);

                                echo "<div class='product-slide'>
												<a href='{$link_url}'>
													<div class='col-card-3'>
														<div class='event-card'>
															<div class='img-cd'><img src='{$product_image}' /></div>
															<h4>{$product_title}</h4>
															<p class='rate'>" . $inventory_currency_symbol . ' ' . (($inventory_price != 0) ? $inventory_price : $inventory_mrp) . "</p>
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

        </div>

        <!-- Anniversary -->
        <div class="row">
            <div class="col-md-12">
                <div class="head-1">
                    <a href="/tag/anniversary/">
                        <h2>Anniversary</h2>
                    </a>
                    <span class="cart-bodr-h2"></span>
                    <div class="view-box">
                        <a href="/tag/anniversary/" class="btn-vw">View All</a>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="product-slider">

                    <?php

                    if ($productList = mysqli_query($connection, "SELECT tag_relation_id, link_id, link_url, product_title, product_image, inventory_currency, inventory_currency_symbol, inventory_price FROM " . TABLE_PREFIX . "tag INNER JOIN " . TABLE_PREFIX . "product ON product_id=tag_relation_id INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id WHERE tag_type='product' AND tag_title='anniversary' AND inventory_type='product' AND link_type='product' AND link_status=200 AND inventory_in_stock=1 AND tag_site_id=" . SITE_ID . " AND link_site_id=" . SITE_ID . "  ORDER BY link_published DESC LIMIT 0, 12")) {

                        if (mysqli_num_rows($productList) != 0) {

                            while ($productData = mysqli_fetch_assoc($productList)) {

                                extract($productData);

                                echo "<div class='product-slide'>
											<a href='{$link_url}'>
												<div class='col-card-3'>
													<div class='event-card'>
														<div class='img-cd'><img src='{$product_image}' /></div>
														<h4>{$product_title}</h4>
														<p class='rate'>" . $inventory_currency_symbol . ' ' . (($inventory_price != 0) ? $inventory_price : $inventory_mrp) . "</p>
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
        </div>

        <!-- Birthday -->
        <div class="row">
            <div class="col-md-12">
                <div class="head-1">
                    <a href="/tag/birthday/">
                        <h2>Birthday</h2>
                    </a>
                    <span class="cart-bodr-h2"></span>
                    <div class="view-box">
                        <a class="btn-vw">View All</a>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="product-slider">

                    <?php

                    if ($productList = mysqli_query($connection, "SELECT tag_relation_id, link_id, link_url, product_title, product_image, inventory_currency, inventory_price FROM " . TABLE_PREFIX . "tag INNER JOIN " . TABLE_PREFIX . "product ON product_id=tag_relation_id INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id WHERE tag_type='product' AND tag_title='birthday' AND inventory_type='product' AND link_type='product' AND link_status=200 AND inventory_in_stock=1 AND tag_site_id=" . SITE_ID . " AND link_site_id=" . SITE_ID . "  ORDER BY link_published DESC LIMIT 0, 12")) {

                        if (mysqli_num_rows($productList) != 0) {

                            while ($productData = mysqli_fetch_assoc($productList)) {

                                extract($productData);

                                echo "<div class='product-slide'>
											<a href='{$link_url}'>
												<div class='col-card-3'>
													<div class='event-card'>
														<div class='img-cd'><img src='{$product_image}' /></div>
														<h4>{$product_title}</h4>
														<p class='rate'>" . $inventory_currency_symbol . ' ' . (($inventory_price != 0) ? $inventory_price : $inventory_mrp) . "</p>
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
        </div>

        <!-- Luxury Collection -->
        <div class="row">
            <div class="col-md-12">
                <div class="head-1">
                    <a href="/tag/luxury/">
                        <h2>Luxury</h2>
                    </a>
                    <span class="cart-bodr-h2"></span>
                    <div class="view-box">
                        <a href="/tag/luxury/" class="btn-vw">View All</a>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="product-slider">

                    <?php

                    if ($productList = mysqli_query($connection, "SELECT tag_relation_id, link_id, link_url, product_title, product_image, inventory_currency, inventory_price FROM " . TABLE_PREFIX . "tag INNER JOIN " . TABLE_PREFIX . "product ON product_id=tag_relation_id INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=product_id INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id WHERE tag_type='product' AND tag_title='luxury' AND inventory_type='product' AND link_type='product' AND link_status=200 AND inventory_in_stock=1 AND tag_site_id=" . SITE_ID . " AND link_site_id=" . SITE_ID . "  ORDER BY link_published DESC LIMIT 0, 12")) {

                        if (mysqli_num_rows($productList) != 0) {

                            while ($productData = mysqli_fetch_assoc($productList)) {

                                extract($productData);

                                echo "<div class='product-slide'>
											<a href='{$link_url}'>
												<div class='col-card-3'>
													<div class='event-card'>
														<div class='img-cd'><img src='{$product_image}' /></div>
														<h4>{$product_title}</h4>
														<p class='rate'>" . $inventory_currency_symbol . ' ' . (($inventory_price != 0) ? $inventory_price : $inventory_mrp) . "</p>
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
        </div>

    </div>

</section>

<!-- <section class="section-content info-setion" >
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="about-info">
							<h1>#1 Online Flower Delivery Portal In India</h1>
							<p>Is there anyone in this world who does not like flowers? Anyone who does not admire the beauty of flowers? Of course not! Because flowers undoubtedly bring a smile on everyone's face, they add the warmth of sunshine to the day and make the soul feel calm and peaceful. And so to speak the language of flowers and express emotions when words are just not enough, & Blooms has taken the responsibility to become your messenger of Emotions, Send flower bouquet online and add more smiles to your loved one's face.</p>

							<p>But finding a beautiful bunch of flower that speaks your emotions perfectly might be a tough task. That is when & Blooms steps in with amazing flower gifts of amazing varieties so that you don't have to hop to florist shops around you and be settled with a limited choice. & Blooms shall be your reliable online florist shop in India so that you can sit at the comfort of your home and order amazing flowers and gifts with a few taps of your finger.</p>

							<p>So, what is their favorite flower? Does your recipient love Roses, Carnations, Gerberas, Anthuriums, orchids, Lilies or? Whichever be the flower of their choice, find it at the best online florist in India- & Blooms. We deliver freshly cut flowers in the most beautiful and classic arrangements. Wrapped beautifully in different designs and colorful paper, these flower arrangements are the best quality products which shall make your loved one's face shine even brighter.</p>

							<p>So the next time you need to send flowers online in India, you know where to head to.</p>

							<h2>Send Flowers Online from & Blooms - India's No. 1 Online Florist</h2>
							<p>& Blooms stands as a No. 1 online flower shop that doesn't only deliver flowers, gifts, and cakes but makes sure that each of the hampers is wrapped in love too. Along with the pocket-friendly deals come the free shipping that makes & Blooms top the charts and establishing itself as one of the most reliable online florists in the country.</p>


						</div>
					</div>
				</div>
			</div>
		</section> -->

<?php

include_once 'footer.php';

?>