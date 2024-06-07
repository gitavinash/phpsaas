<?php

if ($checkProductMeta = mysqli_query($connection, "SELECT product_meta_key, product_meta_value FROM " . TABLE_PREFIX . "product_meta WHERE product_meta_relation_id='{$product_id}' AND product_meta_site_id=" . SITE_ID)) {

    if (mysqli_num_rows($checkProductMeta) != 0) {

        $productMeta = array();

        while ($checkProductMetaData = mysqli_fetch_assoc($checkProductMeta)) {


            // Create dynamic variable in the name of product_meta_key value

            ${$checkProductMetaData['product_meta_key']} = $checkProductMetaData['product_meta_value'];
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
            <div class="col-md-6 product-gallery">

                <!-- Product gallery -->

                <div class="picZoomer">
                    <img src="<?php echo $product_image ?>" class="touchedPic" alt="">
                </div>
                <?php

                if ($product_gallery != '') {

                    $galleryImages = json_decode($product_gallery, TRUE);

                    if ($galleryImages != '') {

                        echo "<ul class='piclist' ><li><img src='{$product_image}' /></li>";

                        for ($i = 0; $i < sizeof($galleryImages); $i++) {

                            extract($galleryImages[$i]);
                            echo "<li><img src='{$path}' /></li>";
                        }

                        echo '</ul>';
                    } else {

                        echo "<div class='slide'><div class='img'><img src='{$product_image}' alt='{$product_title}' /></div></div>";
                    }
                }

                ?>

            </div>
            <div class="col-md-6">

                <div class="prod-detail" id="productDetails">
                    <h2><?php echo $product_title ?></h2>
                    <?php

                    echo (($inventory_in_stock == 0) ? "<h5 class='outStock'>Out of Stock</h5>" : "<h5 class='stock'>In Stock</h5>");

                    ?>
                    <div class="price-tg">

                        <p style="display:none" id="productInfo"
                            value="<?php echo $product_id . '_' . (($inventory_price == 0) ? $inventory_mrp : $inventory_price) . '_1' ?>">
                        </p>

                        <?php

                        if ($inventory_price == 0) {

                            echo "<h2 class='ofer-price'><img src='" . USER_IMAGES . "rupees.svg'> <span>{$inventory_mrp}</span></h2>";
                        } else { ?>

                        <h2 class="ofer-price"><img src="<?php echo USER_IMAGES ?>rupees.svg">
                            <span><?php echo $inventory_price ?></span>
                        </h2>
                        <?php
                            if ($inventory_mrp != 0) {

                                if ($inventory_mrp != $inventory_price) {

                                    echo "<h2 class='with-ofer-price'><span>" . $inventory_mrp . "</span></h2>";

                                    $discountPersantage = round((1 - $inventory_price / $inventory_mrp) * 100, 0);

                                    echo "<h3 class='remain-days'>({$discountPersantage}% OFF)</h3>";
                                }
                            }

                            ?>

                        <?php
                        }

                        ?>

                    </div>
                    <div class="cart-box">
                        <input type="hidden" id="qty"
                            value="<?php echo $product_id . '_1_' . (($inventory_price != 0) ? $inventory_price : $inventory_mrp) ?>">
                        <!-- <span><img src="<?php echo USER_IMAGES ?>van.svg">Earliest delivery date: <?php echo date('d M Y', strtotime('+1 days')) ?></span> -->
                        <!-- <span><img src="<?php echo USER_IMAGES ?>van.svg">Same day delivery</span> -->

                        <?php

                        echo (($inventory_in_stock == 0) ? "<a class='out-of-stock'>Out of stock</a>" : "<a href='#' id='addToCart' class='cart-ad-btn'>Buy Now</a>");

                        ?>

                    </div>
                    <div class="similar-product">
                        <h2>Product Description</h2>
                        <p><?php echo $product_highlight ?></p>
                    </div>
                    <!-- SKU  -->
                    <?php echo (($inventory_sku != '') ? "<div class='sku'><h4>SKU:</h4><h3>{$inventory_sku}</h3></div>" : "") ?>
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
                    <h2>Product Description</h2>
                    <span class="cart-bodr-h2"></span>
                </div>
            </div>

            <div class="col-md-12">
                <?php echo $product_description ?>
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

                    if ($tagListData = mysqli_query($connection, "SELECT tag_title as relatedTags FROM isw_tag WHERE tag_relation_id='{$product_id}' LIMIT 0, 1")) {

                        if (mysqli_num_rows($tagListData) != 0) {

                            extract(mysqli_fetch_assoc($tagListData));
                            $relatedProductQuery = "SELECT link_url, product_title, product_image, inventory_currency, inventory_price, inventory_mrp FROM isw_tag INNER JOIN isw_link ON link_relation_id=tag_relation_id INNER JOIN isw_product ON product_id=link_relation_id INNER JOIN isw_inventory ON inventory_relation_id=product_id WHERE tag_title LIKE '%{$relatedTags}%' AND link_status=200 AND link_type='product' AND link_site_id='" . SITE_ID . "' AND inventory_in_stock=1 GROUP BY tag_relation_id ORDER BY RAND() LIMIT 0, 10";
                        } else {

                            $productRelated = explode(' ', $product_title);

                            $relatedProductQuery = "SELECT link_url, product_title, product_image, inventory_currency, inventory_price, inventory_mrp FROM isw_tag INNER JOIN isw_link ON link_relation_id=tag_relation_id INNER JOIN isw_product ON product_id=link_relation_id INNER JOIN isw_inventory ON inventory_relation_id=product_id WHERE tag_title LIKE '%{$productRelated[0]}%' AND link_status=200 AND link_type='product' AND link_site_id='" . SITE_ID . "' AND inventory_in_stock=1 GROUP BY tag_relation_id ORDER BY RAND() LIMIT 0, 10";
                        }
                    }

                    if ($relatedProductsData = mysqli_query($connection, $relatedProductQuery)) {

                        if (mysqli_num_rows($relatedProductsData) != 0) {

                            while ($relatedProducts = mysqli_fetch_assoc($relatedProductsData)) {

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

<section class="section-content cloth-card" style="padding: 0px 0px 20px 0px">
    <div class="container">
        <div class="row">
            <div class="head-1">
                <h2>Quick Links</h2>
                <span class="cart-bodr-h2"></span>
            </div>
            <div class="main-card">
                <div class='container'>
                    <div class='row'>


                        <?php

                        for ($i = 0; $i < sizeof($productCategoryData); $i++) {
                            extract($productCategoryData[$i]);
                            echo "<div class='col col-sm-2' >
                    <a href='{$categoryLink}' class='card-link'>
                    <div class='acce-card'>
                        <div class='img-acc'>
                            <img src='{$categoryImage}' title='{$categoryTitle}'>
            </div>
            <div class='acc-card-head'>
                <h4>{$categoryTitle}</h4>
            </div>
        </div>
        </a></div>";
                        }

                        ?>


                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php

include_once 'footer.php';

?>