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
                    <h1>New Collections</h1>
                    <p>Fresh collection</p>
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
                            <li class="breadcrumb-item active" aria-current="page">Shop</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="row">
            <?php

            if($checkProducts = mysqli_query($connection, "SELECT link_url, product_image, product_title, inventory_currency_symbol, inventory_in_stock, inventory_mrp, inventory_price FROM ".TABLE_PREFIX."product INNER JOIN ".TABLE_PREFIX."inventory ON inventory_relation_id=product_id INNER JOIN ".TABLE_PREFIX."link ON link_relation_id = product_id WHERE link_site_id = 17 ORDER BY link_published DESC LIMIT 0, 20")) {

                if(mysqli_num_rows($checkProducts)==0) {

                    echo "<section class='continue-shopping'>
                        <h1><span>No product found</span></h1>
                        <a href='/' class='btn btn-success'>Go to homepage</a>
                    </section>";

                } else {

                    while($productList = mysqli_fetch_assoc($checkProducts)) {

                        extract($productList);

                        echo "<div class='col-md-3 col-6'>
                                    <a href='{$link_url}'>
                                        <div class='col-card-3'>
                                            <div class='event-card'>
                                                <div class='img-cd'>
                                                    <img src='{$product_image}'>
                                                </div>
                                                ".(($inventory_in_stock==0) ? "<div class='out-of-stock' ><span>Out of Stock</span></div>" : "" )."
                                                <h4>{$product_title}</h4>
                                                <p class='rate'>".$inventory_currency_symbol.' '.(($inventory_price!=0) ? $inventory_price : $inventory_mrp)."</p>
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