<?php

require_once 'header.php';

if (isset($id)) {

    $transaction_id = base64_decode($id);

    if ($checkOrder = mysqli_query($connection, "SELECT transaction_order_id as order_id, transaction_method, order_product_id, order_total_price, order_product_qty, profile_mobile as mobile, profile_email, profile_first_name, profile_last_name, profile_display_name FROM isw_transaction INNER JOIN isw_profile ON transaction_user_id=profile_user_id INNER JOIN isw_order ON transaction_order_id=order_id WHERE transaction_order_id='{$transaction_id}' AND transaction_site_id=" . APP_SITE_ID)) {

        if (mysqli_num_rows($checkOrder) != 0) {

            extract(mysqli_fetch_assoc($checkOrder));

            mysqli_query($connection, "START TRANSACTION");

            $updateTransaction = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "transaction SET transaction_status=1 WHERE transaction_order_id='{$order_id}'");

            if ($updateTransaction) {

                if ($transaction_method == 'online') {

                    $updateOrder = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_status=1 WHERE order_id='{$order_id}'");
                }

                $updateEnquiry = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "enquiry SET enquiry_status=1 WHERE enquiry_id='{$order_product_id}' AND enquiry_site_id=" . SITE_ID);

                if ($productDetails = mysqli_query($connection, "SELECT GROUP_CONCAT(product_title) as productName, GROUP_CONCAT(product_image) as productImage FROM isw_product WHERE product_type='product' AND {$order_product_id}")) {

                    $productName = '';

                    if (mysqli_num_rows($productDetails) != 0) {

                        extract(mysqli_fetch_assoc($productDetails));
                    }
                }

                /** Update cart table */

                if (!mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "cart SET cart_status=1 WHERE cart_session_id='" . SESSION_ID . "'")) { ?>
<div class="container">
    <div class="row">
        <div class="col">
            <section class='continue-shopping'>
                <h1>Error<span>Something is wrong while updating cart.</span></h1>
                <a href="/review-agreement/?request_id=<?php echo base64_encode($order_product_id) ?>&state=delhi"
                    class="btn btn-success">Retry payment</a>
            </section>
        </div>
    </div>
</div>

<?php

                    echo mysqli_error($connection);
                } else {

                    mysqli_query($connection, "COMMIT");

                ?>
<div class="container">
    <div class="row">
        <div class="col">
            <section class='continue-shopping'>
                <h1>Thank You<span>Your order number <strong>OD<?php echo $order_id; ?></strong> is
                        successfully
                        received. You may
                        get a confirmation call in a while.</span></h1>
                <a href="/view_agreement/?id=<?php echo $id ?>" target="_blank" class="btn btn-success">Download
                    Agreement</a>
            </section>
        </div>
    </div>
</div>
<?php

                }
            } else {

                mysqli_query($connection, "ROLLBACK");
            }
        } else { ?>
<div class="container">
    <div class="row">
        <div class="col">
            <section class='continue-shopping'>
                <h1>Error<span>Something is wrong while generate a order request.</span></h1>
                <a href="/review-agreement/?request_id=<?php echo base64_encode($order_product_id) ?>&state=delhi"
                    class="btn btn-success">Retry payment</a>
            </section>
        </div>
    </div>
</div>

<?php
        }
    } else {

        echo mysqli_error($connection);
        exit();
    }
}

if (isset($form_type)) { ?>
<div class="container">
    <div class="row">
        <div class="col">
            <section class='continue-shopping'>
                <h1>Thanks for contacting us.<span>We'll get back to you as soon as
                        possible.</span></h1>
                <a href="/" class="btn btn-success">Continue</a>
            </section>
        </div>
    </div>
</div>

<?php
}

require_once 'footer.php';

?>