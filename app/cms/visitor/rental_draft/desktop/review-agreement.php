<?php

if (isset($request_id)) {
    if ($request_id != '') {
        if ($checkRequest = mysqli_query($connection, "SELECT enquiry_id FROM " . TABLE_PREFIX . "enquiry WHERE enquiry_id='" . base64_decode($request_id) . "' AND enquiry_site_id=" . SITE_ID)) {
            if (mysqli_num_rows($checkRequest) != 0) {
                $validId = true;
            }
        }
    }
}

if (!isset($validId)) {
    header("Location: /", TRUE, 301);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <title><?php echo ucwords(str_replace('/', '', str_replace('-', ' ', BASE_URL))) . ' | ' . SITE_NAME ?></title>
    <link rel="stylesheet" href="/config.min.css">
    <link rel="stylesheet" href="<?php echo DEFAULT_USER_STYLESHEET ?>checkout.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">
    <?php require_once DEFAULT_META ?>
</head>

<body>

    <header class="">
        <div class="container">
            <div class="row">
                <div class="col-5 m-col">
                    <div class="logo">
                        <img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>">
                    </div>
                </div>
                <div class="col-5 m-col-3"></div>
            </div>
        </div>
    </header>
    <main>
        <section class="checkout-box">
            <div class="container">
                <!-- <div class="row"> -->
                <div class="col-7 m-col">

                </div>
                <div class="col-3 m-col float-right">
                    <div class="shadow-box">
                        <h2>Payment summary</h2>
                        <!-- <h2>Payment summary<span>test contents</span></h2> -->
                        <ul>
                            <li>
                                <p><strong>Subtotal</strong><span><?php echo $inventory_currency_symbol . ' ' . $subTotal ?></span>
                                </p>
                            </li>
                            <?php

                            $taxAmount = 0;
                            if ($inventory_tax_status == 1) {

                                $taxAmount = number_format((float)($inventory_tax_rate / 100) * $subTotal, 2, '.', '');

                                echo "<li>
                                    <p><strong>Tax ( {$inventory_tax_rate}% )</strong><span>{$inventory_currency_symbol} {$taxAmount}</span></p>
                                </li>";
                            }

                            $totalPrice = $subTotal + $taxAmount

                            ?>
                        </ul>
                        <div class="totalBox">
                            <p><strong>Total</strong>
                                <span><?php echo $inventory_currency_symbol . ' ' . $totalPrice ?></span>
                            </p>
                        </div>
                        <input type="hidden" name="order" id="orderId" value=" <?php echo $orderId ?>">
                        <input type="hidden" name="paymentMode" id="paymentMode" value="online">
                        <a href="#" id="processToPayment" class="button">Proceed to payment</a>
                    </div>
                </div>
                <!-- </div> -->
            </div>
            <div class="creditBox">
                <div class="row">
                    <div class="col">
                        <p>By continuing you agree to our <a href="/terms-and-conditions">Terms &amp; Conditions</a> and
                            <a href="/privacy-policy/">Privacy
                                Policy</a>
                            <!--  | &nbsp; <span>Powered by <a href="https://cartt.in"
                            title="Build online e-commerce store">Cartt -
                            Craft Your Online Success with
                            Cartt.</a></span> -->
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script src="<?php echo JS_LIBRARY ?>jquery.min.js"></script>
    <script src="<?php echo DEFAULT_USER_JS ?>app.min.js"></script>
</body>

</html>