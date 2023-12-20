<?php

if ($test_id == '') {
    header("Location: /", TRUE, 301);
    exit();
}


require_once 'header.php';

?>
<section class="hero side-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 col-lg-7">
                <h2 class="isfont-40 text-white my-3 is-font600 text-center text-lg-start">
                    Appointment booking review
                </h2>
            </div>
            <div class="col-12 col-lg-5 my-4 my-lg-0">
            </div>
        </div>
    </div>
</section>

<section class="vartical-padding side-padding checkout">
    <div class="container">
        <div class="row vartical-padding align-items-top">
            <div class="col-12 col-lg-6">
                <div class="checkout-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            $productId = base64_decode($_GET['test_id']);

                            $testPackage = '';
                            $testQuery = "SELECT product_id, product_brand_id, product_title, inventory_currency_symbol, inventory_mrp, inventory_price FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id = product_id WHERE product_site_id = " . SITE_ID . " AND product_id IN ($productId) AND inventory_site_id = " . SITE_ID;

                            if ($checkTest = mysqli_query($connection, $testQuery)) {

                                $finalTestsName = '';
                                $i = 0;
                                if (mysqli_num_rows($checkTest) != 0) {
                                    $productTotalPrice = 0;
                                    while ($testList = mysqli_fetch_assoc($checkTest)) {
                                        extract($testList);
                                        $finalTestsName .= $product_title . ' | ' ?>

                                        <tr>
                                            <td class="is-color-blue is-font600 isfont-27">
                                                <?php echo ucwords($product_title) ?>
                                            </td>
                                            <td class="is-color-blue is-font600 isfont-18">
                                                <?php echo $inventory_currency_symbol . ' ' . $inventory_price;
                                                $productTotalPrice = $productTotalPrice + $inventory_price ?>
                                            </td>
                                        </tr>
                            <?php
                                        $i++;
                                    }
                                } else {
                                    echo "<tr><td colspan='3'>No test found</td></tr>";
                                }
                            }

                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php


            // Set the API endpoint URL
            $apiEndpoint = "https://secure.payu.in/_payment";

            // Set the merchant key and salt
            $merchantKey = "1rIsEcUS";
            $salt = "RDsVo3Aq";

            // Set the order details
            $fullName = $_GET['name'];
            $email = $_GET['email'];
            $phone = $_GET['mobile'];
            $txnId = "TXN" . time();
            $surl = SITE_URL . "/payment/status/";
            $furl = SITE_URL . "/payment/status/";

            $updatedProductName = str_replace('|', '_', rtrim($finalTestsName));

            // sha512(key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5||||||SALT)
            $hash = hash('sha512', $merchantKey . '|' . $txnId . '|' . $productTotalPrice * 100 . '|' . str_replace(' ', '_', $updatedProductName) . '|' . $fullName . '|' . $email . '|' . $phone . '||||||||||' . $salt);

            ?>

            <div class="col-12 col-lg-6">
                <div class="apointment-form">
                    <form action="https://secure.payu.in/_payment" method="POST">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <input type="text" name="name" class="form-control" id="fullName" placeholder="<?php echo $_GET['name'] ?>" readonly />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <input type="email" name="email" class="form-control" id="email" placeholder="<?php echo $_GET['email'] ?>" readonly />
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="mb-3">
                                    <input type="number" name="mobile" class="form-control" placeholder="<?php echo $_GET['mobile'] ?>" id="mobile" readonly />
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="mb-3">
                                    <input type="text" name="pickup_date" class="form-control" placeholder="<?php echo $_GET['pickup_date'] ?>" readonly />
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="mb-3">
                                    <input type="text" name="pickup_time" class="form-control" id="pickupTime" placeholder="<?php echo $_GET['pickup_time'] ?>" readonly />
                                </div>
                            </div>

                            <div class=" col-12">
                                <textarea class="form-control" name="address" placeholder="Test collection address *" id="pickUpAddress" readonly><?php echo urldecode($_GET['address']) ?></textarea>
                            </div>
                        </div>
                        <?php $txnId =  base64_encode(time()); ?>
                        <input type="hidden" name="key" value="<?php echo $merchantKey ?>" />
                        <input type="hidden" name="txnid" value="<?php echo $txnId ?>" />
                        <input type="hidden" name="productinfo" value="<?php echo $finalTestsName ?>" />
                        <input type="hidden" name="amount" value="<?php echo $productTotalPrice ?>" />
                        <input type="hidden" name="email" value="<?php echo $_GET['email'] ?>" />
                        <input type="hidden" name="firstname" value="<?php echo $_GET['name'] ?>" />
                        <input type="hidden" name="surl" value="/payment/status/" />
                        <input type="hidden" name="furl" value="/payment/status/" />
                        <input type="hidden" name="phone" value="<?php echo $_GET['mobile'] ?>" />
                        <?php

                        // $pgKey = 'oYsah2L8';
                        // $pgSaltKey = '4a4if493zX';

                        ?>
                        <input type="hidden" name="hash" value="<?php echo $hash ?>" />
                        <input type="submit" class="btn btn-primary w-100 my-4 py-3" value="Procees to pay ₹ <?php echo $productTotalPrice ?>">
                        <!-- <a href="<?php echo $payNowURL ?>" class="btn btn-primary w-100 my-4 py-3">Procees to pay ₹ -->
                        <!-- <?php echo $productTotalPrice ?></a> -->

                        <div>
                            <p class=" isfont-12 text-center text-">
                                <b>By clicking, you agree to our</b>
                                <a href="/privacy-policy/" target="_blank">Privacy Policy</a> and
                                <a href="/terms-and-conditions/" target="_blank">Terms of Use</a>
                            </p>
                        </div>
                    </form>

                    <body>
                </div>
            </div>
        </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>