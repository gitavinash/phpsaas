<?php

require_once 'header.php';

?>
<main>
    <section class="hero side-padding">
        <div class="container">
            <div class="row align-items-center" style="min-height: 200px;">
                <div class="col-12 col-lg-12 ">
                    <h2 class="isfont-40 text-white my-3 is-font600 text-center ">
                        Payment
                    </h2>
                </div>

            </div>
        </div>
    </section>

    <section>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-6 col-12">
                    <img src="<?php echo USER_IMAGES ?>upi.jpeg" alt="" class="img-fluid">
                </div>
                <div class="col-md-6 col-lg-6 col-12 paymentDone">
                    <h2 class="isfont-40 my-3 is-font600">
                        Kindly scan this QR code to make a payment of ₹ <?php echo $price ?>
                    </h2>
                    <a href="/thank-you/" class="thankyoubtn">Click here when payment is completed</a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php

require_once 'footer.php';

?>