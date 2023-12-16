<?php

require_once 'header.php';

?>
<section class="hero side-padding" style="background: #c2c3c4;padding: 100px 0px;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 col-lg-7">
                <h2 class="isfont-40 text-white my-3 is-font600 text-center text-lg-start">
                    About <?php echo $product_title ?>
                </h2>
                <p><strong>Description</strong></p>
                <p><?php echo $product_description ?></p>
            </div>
            <div class="col-12 col-lg-5 my-4 my-lg-0">
                <img src="<?php echo USER_IMAGES ?>r11.png" alt=""
                    class="img-fluid d-none d-lg-block m-auto ms-lg-auto" />
            </div>
            <div class="col-12">
                <a href="/checkout/?test_id=<?php echo base64_encode($product_id) . '&checkout_type=' . base64_encode('package') ?>"
                    class="btn1 package-btn">Book Now at
                    <?php echo $inventory_currency_symbol . ' ' . $inventory_price ?> /-</a>
            </div>
        </div>
    </div>
</section>

<?php

require_once 'footer.php';

?>