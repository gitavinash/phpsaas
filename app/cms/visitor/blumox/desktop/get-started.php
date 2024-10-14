<?php

if (isset($id) && isset($serviceType) && isset($productType)) {

    $serviceId = base64_decode($id);

    $productInfo = mysqli_query($connection, "SELECT inventory_price, inventory_tax_status, inventory_tax_rate FROM " . TABLE_PREFIX . "inventory WHERE inventory_relation_id='{$serviceId}' AND inventory_status=1 AND inventory_type='{$productType}' AND  inventory_site_id=" . SITE_ID);

    if (mysqli_num_rows($productInfo) != 0) {

        extract(mysqli_fetch_assoc($productInfo));

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $userType = 'client';

            // Cashfree payload

            $userInfo = explode('@', $email);
            $userId = $userInfo[0];
            $profile_email = $email;
            $profile_mobile = $mobile;
            $profile_display_name = $name;
            $customOrderId = base64_encode($id . '_' . time());
            $paymentAmount = (($paymentOption != 'full') ? $inventory_price * 0.30 : $inventory_price);
            $cartProduct = ucwords(urldecode($serviceType));
            $customRedirection = SITE_URL . '/payment-status/?orderId=' . $customOrderId;

            require_once './lib/resource/payment-gateway/cashfree/generate.php';
            exit();
        } else {

            // Show enquiry form

            require_once 'header.php';

?>


            <section>
                <div class="introBox">
                    <h1><?php echo ucwords(urldecode($serviceType)) ?></h1>
                </div>
            </section>

            <section>
                <div class="col-lg-7 col-md-12 mx-auto">
                    <form method="post">
                        <div class="form-header">
                            <h2>
                                Kindly fill this form to continue.
                            </h2>
                        </div>
                        <div class="form-group">
                            <label>Full name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="emal" name="email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Contact number</label>
                            <input type="number" name="mobile" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Requirment</label>
                            <textarea name="requirment" class="form-control" id=""></textarea>
                        </div>
                        <div class="form-group">
                            <label>Payment option</label>
                        </div>
                        <div class="inline-radio form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="paymentOption" id="advancePayment" value="advance" checked>
                            <label class="form-check-label" for="advancePayment">
                                Advance Payment ( INR <?php echo $inventory_price * 0.30; ?> )
                            </label>
                        </div>
                        <div class="inline-radio form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="paymentOption" id="fullPayment" value="full">
                            <label class="form-check-label" for="fullPayment">
                                Full Payment ( INR <?php echo $inventory_price ?> )
                            </label>
                        </div>
                        <div class="form-group">
                            <input type="submit" value="Continue" class="btn btn-success">
                        </div>
                    </form>
                </div>
            </section>

<?php

            require_once 'footer.php';
        }
    } else {
        echo "Invalid request number found";
    }
} else {
    echo "Something went wrong";
}

exit();
