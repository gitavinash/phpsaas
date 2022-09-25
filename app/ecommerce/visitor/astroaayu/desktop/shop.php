<?php require_once 'header.php' ?>
<div class="common_banner">
    <h1 class="banner_title">Astro Shop</h1>
</div>

<main class="container-fluid">
    <div class="row common_padding margin_top">
        <div class="col-12 col-lg-4 col-xxl-3">
            <!-- <div class="shop_search">
                <input type="search" placeholder="products Search" />
                <div class="search_box">
                    <img src="<?php echo USER_IMAGES ?>search_icon2.png" class="search_icon3" />
                </div>
            </div> -->
            <div class="filter_box mt-4">
                <div class="filter_box_title">
                    <p>Top Categories</p>
                </div>
                <ul class="filter_box_ul">
                    <?php
                    
                        $categotyList = mysqli_query($connection, "SELECT category_title, link_url FROM isw_category INNER JOIN isw_link ON link_relation_id=category_id WHERE link_site_id=".SITE_ID." AND link_type='category' AND link_status=200");

                        if(mysqli_num_rows($categotyList)!=0) {

                            while($categoryData=mysqli_fetch_assoc($categotyList)) {

                                extract($categoryData);

                                echo "<li><a href='{$link_url}'>{$category_title}</a></li>";

                            }

                        }

                    ?>
                </ul>
            </div>
        </div>
        <div class="col-12 col-lg-8 col-xxl-8 ps-0 ps-lg-5 mt-3 mt-sm-4">
            <!-- <ul class="nav nav-pills mb-3 nav_tabs" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home"
                        aria-selected="true">
                        Products
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile"
                        aria-selected="false">
                        Books
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact"
                        aria-selected="false">
                        Gems
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-disabled-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-disabled" type="button" role="tab" aria-controls="pills-disabled"
                        aria-selected="false">
                        Puja Accessories
                    </button>
                </li>
            </ul> -->
            <!-- <div class="mt-4">
                <h1 class="title_semi title_semi2 relative">Products Search</h1>
                <div class="d-flex align-items-center justify-content-between width_astro_filter mt-4">
                    <p class="color_red">Sort by -</p>
                    <p><span class="color_red">Price -</span> High to Low</p>
                    <p><span class="color_red">Date by -</span> Newest First</p>
                </div>
            </div> -->
            <div class="tab-content mt-4" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab"
                    tabindex="0">
                    <div class="products_container">

                        <?php
                        
                            $productList = mysqli_query($connection, "SELECT link_url, product_thumbnail, product_title, inventory_price, inventory_mrp FROM isw_product INNER JOIN isw_link ON link_relation_id=product_id INNER JOIN isw_inventory ON inventory_relation_id=product_id WHERE product_site_id=".SITE_ID." LIMIT 30");

                            if(mysqli_num_rows($productList)!=0) {

                                while($productListData = mysqli_fetch_assoc($productList)) {

                                    extract($productListData);

                                    echo "<a class='product_box' href='{$link_url}'>
                                    <div class='product_box_bg'>
                                        <img src='{$product_thumbnail}' />
                    </div>
                    <div class='product_contents text-center mt-3'>
                        <h4 class='m-0'>{$product_title}</h4>
                        <p><span class='color_red me-1'>₹{$inventory_price}</span><del>₹{$inventory_mrp}</del><br /><div class='off'>".((1-$inventory_price/$inventory_mrp)*100)."% Off</div></p>
                    </div>
                    </a>";

                    }

                    } else {

                        echo "<h4 class='m-0'>No product found</h4>";

                    }

                    ?>

                    </div>
                </div>
                <!-- <div class="text-center mt-3 mt-sm-5">
                    <button class="see_more_btn">See More</button>
                </div> -->
            </div>
        </div>
</main>

<?php require_once 'footer.php' ?>