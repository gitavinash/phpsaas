<?php

require_once 'header.php';

?>
<section class="thank-you">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="th-center">
                    <img src="./assest/img/thuser.png" alt="" class="d-flex m-auto">

                    <?php

                    if ($_GET['status'] == 'success') { ?>
                        <div class="text-center py">
                            <h3 class="text-center my-4  is-font600 isfont isfont-40">Thank you! you for making a payment.
                            </h3>
                            <p class="py-4"> Thank you for reaching out to DiagnosticCare. <br> Our team will be in touch
                                with you shortly to assist you further.</p>
                            <div class="w-100 d-flex justify-content-center">
                                <a href="/" class="thankyoubtn">Go Home</a>
                            </div>
                        </div>
                    <?php
                    } else { ?>
                        <div class="text-center py">
                            <h3 class="text-center my-4 is-font600 isfont isfont-40" style="color: red">Payment failed</h3>
                            <p class="py-4">We're sorry, but it seems there was an issue processing your payment. Please
                                double-check the information entered and ensure that your payment method is valid. If the
                                problem persists, consider contacting your bank or financial institution for further
                                assistance. If you have any questions or need support, feel free to reach out to our
                                customer service team. We apologize for any inconvenience this may have caused.</p>
                            <div class="w-100 d-flex justify-content-center">
                                <a href="/" class="thankyoubtn">Go Home</a>
                            </div>
                        </div>
                    <?php
                    }

                    ?>

                </div>
            </div>
        </div>
    </div>

</section>
<?php

require_once 'footer.php';

?>