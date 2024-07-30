<?php

if (isset($request_id)) {
    if ($request_id != '') {
        if ($checkRequest = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "enquiry WHERE enquiry_id='" . base64_decode($request_id) . "' AND enquiry_site_id=" . SITE_ID)) {
            if (mysqli_num_rows($checkRequest) != 0) {
                $validId = true;
                extract(mysqli_fetch_assoc($checkRequest));
                $subTotal = 0;
                $inventory_tax_status = 0;
                switch (strtolower($state)) {
                    case 'rajasthan':
                        $stampValue = 100;
                        $serviceCharges = 299;
                        $notaryService = 150;
                        break;
                    default:
                        $stampValue = 100;
                        $serviceCharges = 249;
                        $notaryService = 150;
                        break;
                }
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
                    <div class="row">
                        <form class="shipping-box">
                            <div class="box white-bg">
                                <div class="col">
                                    <h2>Order summary</h2>
                                </div>
                                <div class="col">
                                    <div class='productBox'>
                                        <div class='productImage col-2 m-col-2 no-padding'>
                                            <img src='<?php echo IMG_LIBRARY ?>profile_icon/r.png' alt=''>
                                        </div>
                                        <div class='productText col-8 m-col-8'>
                                            <!-- <div class='row'> -->
                                            <div class='col-8 m-col-8'>
                                                <p><strong>Rent agreement</strong></p>
                                                <p><span><?php echo ucwords($state) ?> rent agreement</span></p>
                                                <p><span><strong>Click here to download draft of rent
                                                            agreement</strong></a></span></p>
                                            </div>
                                            <div class='col-2 m-col-2'>
                                                <!-- <div class='price float-right'>
                                                <p>{$inventory_currency_symbol} {$inventory_price}</p>
                                            </div> -->
                                            </div>
                                            <!-- </div> -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="box white-bg">
                                <div class="col">
                                    <h2>Stamp Value</h2>
                                </div>
                                <div class="col">
                                    <div class='productBox'>
                                        <div class="radio-group">

                                            <input type="radio" id="notary100" name="stampValue" value="100" checked>
                                            <label for="notary100">₹ 100</label>

                                            <input type="radio" id="notary500" name="stampValue" value="500">
                                            <label for="notary500">₹ 500</label>

                                            <input type="radio" id="notaryOther" name="stampValue" value="other">
                                            <label for="notaryOther">Other</label>

                                            <!-- Input field for 'Other' value -->
                                            <div id="otherValueWrapper" style="display:none;">
                                                <input type="number" class="form-control" id="otherValue"
                                                    placeholder="Enter amount">
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="box white-bg">
                                <div class="col">
                                    <h2>Notary service</h2>
                                </div>
                                <div class="col">
                                    <div class='productBox'>
                                        <div class="radio-group">

                                            <input type="radio" id="notaryYes" name="notaryService" value="true"
                                                checked>
                                            <label for="notaryYes">Yes</label>

                                            <input type="radio" id="notaryNo" name="notaryService" value="false">
                                            <label for="notaryNo">No</label>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="box white-bg">
                                <div class="col">
                                    <h2>Next day delivery</h2>
                                </div>
                                <div class="col">
                                    <div class='productBox'>
                                        <div class="radio-group">

                                            <input type="radio" id="deliveryYes" name="deliveryServices" value="true">
                                            <label for="deliveryYes">Yes</label>

                                            <input type="radio" id="deliveryNo" name="deliveryServices" value="false"
                                                checked>
                                            <label for="deliveryNo">No</label>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class=" col-3 m-col float-right">
                    <div class="shadow-box">
                        <h2>Payment summary</h2>
                        <!-- <h2>Payment summary<span>test contents</span></h2> -->
                        <ul>
                            <li>
                                <p><strong>Stamp value</strong><span
                                        id="stampValue"><?php echo '₹ ' . $stampValue ?></span></p>
                                <p><strong>Service charges</strong><span
                                        id="serviceCharges"><?php echo '₹ ' . $serviceCharges ?></span></p>
                                <p><strong>Notary service
                                        charges</strong><span
                                        id="notaryService"><?php echo '₹ ' . $notaryService ?></span>
                                </p>
                                <p><strong>Delivery charges</strong><span id="deliveryServices">₹ 0</span>
                                </p>
                            </li>
                            <?php

                            $totalPrice = $stampValue + $serviceCharges + $notaryService

                            ?>
                        </ul>
                        <div class="totalBox">
                            <p><strong>Total</strong><span id="totalAmount">₹ <?php echo $totalPrice ?></span></p>
                        </div>
                        <input type="hidden" name="productType" value="service">
                        <input type="hidden" name="productTypeId" value="148">
                        <input type="hidden" name="order" id="orderId" value="<?php echo $request_id ?>">
                        <input type="hidden" name="paymentMode" id="paymentMode" value="online">
                        <a href="#" id="processToPaymentCustom" class="button">Proceed to payment</a>
                        <a href="/view-agreement/?id=<?php echo $request_id ?>&state=<?php echo $state ?>"
                            target="_blank" class="button blue-bg">Download draft</a>
                    </div>
                </div>
                <!-- </div> -->
            </div>
            <div class="creditBox">
                <div class="row">
                    <div class="col">
                        <p>By continuing you agree to our <a href="/terms-and-conditions">Terms
                                &amp; Conditions</a>
                            and
                            <a href="/privacy-policy/">Privacy
                                Policy</a>
                            | &nbsp; <span>Powered by <a href="https://blumox.com"
                                    title="Build online e-commerce store">Blumox Technologies</a></span>
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script src="<?php echo JS_LIBRARY ?>jquery.min.js"></script>
    <script src="<?php echo DEFAULT_USER_JS ?>app.min.js"></script>
    <script src="<?php echo USER_JS ?>app.min.js"></script>
</body>

</html>