<?php require_once 'header.php' ?>
<div class="common_banner">
    <h1 class="banner_title">Product</h1>
</div>

<main class="container-fluid">
    <div class="row common_padding margin_top align-items-center">
        <div class="col-12 col-lg-5">
            <div class="product_box_detail py-5">
                <img src="<?php echo $product_image ?>">
            </div>
        </div>
        <div class="col-12 col-lg-7 ps-0 ps-lg-5 mt-3 mt-lg-0">
            <h1 class="product_title"><?php echo  $product_title ?></h1>
            <div class="d-flex">
                <p> <b>Category:</b> <?php echo $category_title ?></p>
            </div>
            <div class="d-flex align-items-center mt-1">
                <p class="title_semi">₹ <?php echo $inventory_price ?>
                    <?php echo (($inventory_mrp != '') ? "<del>₹ {$inventory_mrp} </del>" : "") ?></p>
                <?php
                if ($inventory_mrp != '') { ?>
                <div class="off">
                    <?php $discountPerstange = (1 - $inventory_price / $inventory_mrp) * 100;
                        echo intval($discountPerstange) ?>%
                    Off</div>
                <?php
                }
                ?>
            </div>
            <hr>
            <p><?php echo $product_highlight ?></p>
            <hr>
            <div class="mt-3 add_container">
                <div class="row">
                    <?php
                    if ($inventory_variation != '') {
                        $varition = json_decode(base64_decode($inventory_variation), TRUE);

                        for ($i = 0; $i < sizeof($varition); $i++) {

                            echo "<div class='col-md-4 col-6' >
                                <div class='row'>
                                    <a href='{$varition[$i][3]}'>
                                        <div class='col-12'>
                                            <div class='card'>
                                                <img src='{$varition[$i][2]}' class='card-img-top' />
                                                    <div class='card-body'>
                                                        <h5 class='card-title'>{$varition[$i][1]}</h5>
                                                        <p class='card-text'>
                                                        </p>
                                                    </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                        </div>";
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="mt-3 add_container">
                <p style="display:none" id="productInfo"
                    value="<?php echo $product_id . '_' . (($inventory_price == 0) ? $inventory_mrp : $inventory_price) . '_1' ?>">
                </p>
                <button class="add_cart" id="addToCart">Buy Now <span class="material-symbols-sharp">
                        shopping_bag
                    </span></button>
                <!-- <div class="share_icon"><span class="material-symbols-sharp">
                        outgoing_mail
                    </span></div> -->
            </div>
            <hr>
        </div>
    </div>
    <div class="row common_padding margin_top margin_bottom">
        <div class="col-12">
            <p><?php echo $product_description ?></p>
        </div>
    </div>
    <div class="row common_padding margin_top margin_bottom">
        <div class="col-12 text-center">
            <h1 class="title_section">Related Products</h1>
        </div>


        <div class="products_container2 mt-2 mt-md-4">

            <?php

            if ($tagListData = mysqli_query($connection, "SELECT tag_title as relatedTags FROM isw_tag WHERE tag_relation_id='{$product_id}' LIMIT 0, 1")) {

                if (mysqli_num_rows($tagListData) != 0) {

                    extract(mysqli_fetch_assoc($tagListData));
                    $relatedProductQuery = "SELECT link_url, product_title, product_image, inventory_currency, inventory_price, inventory_mrp FROM isw_tag INNER JOIN isw_link ON link_relation_id=tag_relation_id INNER JOIN isw_product ON product_id=link_relation_id INNER JOIN isw_inventory ON inventory_relation_id=product_id WHERE tag_title LIKE '%{$relatedTags}%' AND link_status=200 AND link_type='product' AND link_site_id='" . SITE_ID . "' AND inventory_in_stock=1 AND product_id!={$product_id} GROUP BY tag_relation_id ORDER BY RAND() LIMIT 0, 10";
                } else {

                    $productRelated = explode(' ', $product_title);

                    $relatedProductQuery = "SELECT link_url, product_title, product_image, inventory_currency, inventory_price, inventory_mrp FROM isw_tag INNER JOIN isw_link ON link_relation_id=tag_relation_id INNER JOIN isw_product ON product_id=link_relation_id INNER JOIN isw_inventory ON inventory_relation_id=product_id WHERE tag_title LIKE '%{$productRelated[0]}%' AND link_status=200 AND link_type='product' AND link_site_id='" . SITE_ID . "' AND inventory_in_stock=1 AND product_id!={$product_id}  GROUP BY tag_relation_id ORDER BY RAND() LIMIT 0, 10";
                }
            }

            if ($relatedProductsData = mysqli_query($connection, $relatedProductQuery)) {

                if (mysqli_num_rows($relatedProductsData) != 0) {

                    while ($relatedProducts = mysqli_fetch_assoc($relatedProductsData)) {

                        extract($relatedProducts);


                        echo "
                        <a href='{$link_url}'>
                            <div class='product_box'>
                                <div class='product_box_bg'>
                                    <img src='{$product_image}' />
        </div>
        <div class='product_contents text-center mt-3'>
            <h4 class='m-0'>{$product_title}</h4>
            <p><span class='color_red me-1'>₹{$inventory_price}</span><del>₹{$inventory_mrp}</del><br /><div class='off'>" . ((1 - $inventory_price / $inventory_mrp) * 100) . "% Off</div></p>
        </div>
    </div></a>
    ";
                    }
                } else {

                    echo "<h4 class='m-0'>No product found</h4>";
                }
            }

            ?>

        </div>
    </div>
</main>
<?php

require_once('footer.php');

?>