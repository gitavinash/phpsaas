<?php

require_once 'header.php';

if (isset($id)) {

    $transaction_id = base64_decode($id);

    if ($checkOrder = mysqli_query($connection, "SELECT transaction_order_id as order_id, transaction_method, order_product_id, order_total_price, order_product_qty, profile_mobile as mobile, profile_email, profile_first_name, profile_last_name, profile_display_name, city_name, state_name, country_name, address_name, address_house_no, address_area, address_landmark, address_pin_code FROM isw_transaction INNER JOIN isw_profile ON transaction_user_id=profile_user_id INNER JOIN isw_order ON transaction_order_id=order_id INNER JOIN isw_address ON address_id=order_address_id INNER JOIN isw_city ON city_id=address_city_id INNER JOIN isw_state ON state_id=address_state_id INNER JOIN isw_country ON country_id=address_country_id WHERE transaction_order_id={$transaction_id} AND transaction_site_id=" . APP_SITE_ID)) {

        if (mysqli_num_rows($checkOrder) != 0) {

            extract(mysqli_fetch_assoc($checkOrder));

            mysqli_query($connection, "START TRANSACTION");

            $updateTransaction = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "transaction SET transaction_status=1 WHERE transaction_order_id='{$order_id}'");

            if ($updateTransaction) {

                mysqli_query($connection, "COMMIT");

                if ($transaction_method == 'online') {

                    $updateOrder = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_status=1 WHERE order_id='{$order_id}'");
                }


                if ($productDetails = mysqli_query($connection, "SELECT GROUP_CONCAT(product_title) as productName, GROUP_CONCAT(product_image) as productImage FROM isw_product WHERE product_type='product' AND product_id IN ({$order_product_id})")) {

                    $productName = '';

                    if (mysqli_num_rows($productDetails) != 0) {

                        extract(mysqli_fetch_assoc($productDetails));
                    }
                }

                /** Update cart table */

                if (!mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "cart SET cart_status=1 WHERE cart_session_id='" . SESSION_ID . "'")) { ?>

<section class="min-h-screen bg-red-100 flex flex-col justify-center items-center text-center py-12">

    <!-- Banner -->
    <div class="w-full text-white py-10">
        <h1 class="text-3xl text-red-600 font-bold">Order Error</h1>
        <p class="text-base text-gray-700 mt-2">Something went wrong with your order.</p>
    </div>

    <!-- Main Content -->
    <div class="max-w-3xl w-full bg-white shadow-lg rounded-lg p-8 mt-8 border border-gray-200">

        <!-- Error Icon -->
        <div class="mb-6">
            <i class="ri-error-warning-line w-20 h-20 text-8xl mx-auto text-red-500"></i>
        </div>

        <!-- Error Message -->
        <h2 class="text-2xl font-bold text-red-600 mb-2">There Was an Issue with Your Order</h2>
        <p class="text-gray-700 mb-6">Unfortunately, we encountered a problem while processing your order. It looks like
            there was an issue with the payment or order details. We're investigating and will update you shortly.</p>

        <!-- Order ID / Error Message -->
        <?php if (isset($order_id) && !empty($order_id)): ?>
        <div class="bg-red-100 border border-red-300 rounded p-4 text-gray-700 text-lg mb-6">
            Your Order Number: <span class="font-bold text-red-800">OD<?php echo htmlspecialchars($order_id); ?></span>
        </div>
        <?php else: ?>
        <div class="bg-red-200 border border-red-400 rounded p-4 text-gray-700 text-lg mb-6">
            <strong>Order Not Found</strong><br>
            We're unable to retrieve your order details at this moment due to a technical issue. This could be due to an
            issue with the payment gateway or a system error. Rest assured, we are working to resolve this issue and
            will notify you once we have more information.
        </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row justify-center gap-4 mb-6">
            <a href="/" class="bg-red-600 text-white px-6 py-3 rounded hover:bg-red-700 transition text-sm">
                Return to Homepage
            </a>
            <a href="mailto:support@example.com"
                class="bg-red-600 text-white px-6 py-3 rounded hover:bg-red-700 transition text-sm">
                Contact Support
            </a>
        </div>

    </div>

</section>

<!-- Support -->
<p class="text-xs text-gray-500">
    Still need help? <a href="/contact-us/" class="underline hover:text-blue-600">Contact Support</a>
</p>
<?php

                    echo mysqli_error($connection);
                } else {

                ?>

<section class="min-h-screen bg-green-100 flex flex-col justify-center items-center text-center py-12">

    <!-- Banner -->
    <div class="w-full text-white py-10">
        <h1 class="text-3xl text-green-600 font-bold">Thank You!</h1>
        <p class="text-base text-gray-700 mt-2">Your order has been placed successfully.</p>
    </div>

    <!-- Main Content -->
    <div class="max-w-3xl w-full bg-white shadow-lg rounded-lg p-8 mt-8 border border-gray-200">

        <!-- Success Icon -->
        <div class="mb-6">
            <i class="ri-check-line w-20 h-20 text-8xl mx-auto text-green-500"></i>
        </div>

        <!-- Success Message -->
        <h2 class="text-2xl font-bold text-green-600 mb-2">Thank You for Your Purchase!</h2>
        <p class="text-gray-700 mb-6">Your order has been placed successfully. You will receive a confirmation call
            shortly.</p>

        <!-- Order ID (if available) -->
        <?php if (isset($order_id)): ?>
        <div class="bg-green-100 border border-green-300 rounded p-4 text-grey-700 text-lg mb-6">
            Your Order Number: <span class="font-bold text-green-800">OD<?php echo $order_id; ?></span>
        </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row justify-center gap-4 mb-6">
            <a href="/" class="bg-green-600 text-white px-6 py-3 rounded hover:bg-green-700 transition text-sm">
                Continue Shopping
            </a>
        </div>

    </div>

</section>
<?php

                }
            } else {

                mysqli_query($connection, "ROLLBACK");
            }
        } else { ?>
<section class="min-h-screen bg-yellow-100 flex flex-col justify-center items-center text-center py-12">

    <!-- Banner -->
    <div class="w-full text-white py-10">
        <h1 class="text-3xl text-yellow-600 font-bold">Order Pending</h1>
        <p class="text-base text-gray-700 mt-2">We're reviewing your order and payment.</p>
    </div>

    <!-- Main Content -->
    <div class="max-w-3xl w-full bg-white shadow-lg rounded-lg p-8 mt-8 border border-gray-200">

        <!-- Pending Icon -->
        <div class="mb-6">
            <i class="ri-time-line w-20 h-20 text-8xl mx-auto text-yellow-500"></i>
        </div>

        <!-- Pending Message -->
        <h2 class="text-2xl font-bold text-yellow-600 mb-2">Your Order is Under Review</h2>
        <p class="text-gray-700 mb-6">We're currently processing your order. The payment status is still pending, and
            we're working hard to confirm it. Please bear with us as we ensure everything is in order.</p>

        <!-- Order ID / Error Message -->
        <?php if (isset($order_id) && !empty($order_id)): ?>
        <div class="bg-yellow-100 border border-yellow-300 rounded p-4 text-gray-700 text-lg mb-6">
            Your Order Number: <span
                class="font-bold text-yellow-800">OD<?php echo htmlspecialchars($order_id); ?></span>
        </div>
        <?php else: ?>
        <div class="bg-yellow-200 border border-yellow-400 rounded p-4 text-gray-700 text-lg mb-6">
            <strong>There seems to be an issue with your order.</strong><br>
            Unfortunately, we were unable to retrieve your order ID at the moment due to a technical issue. Your payment
            status is still under review. We are working on resolving this as quickly as possible. Please check back
            soon or contact support for more information.
        </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row justify-center gap-4 mb-6">
            <a href="/" class="bg-yellow-600 text-white px-6 py-3 rounded hover:bg-yellow-700 transition text-sm">
                Back to Shop
            </a>
        </div>
        <!-- Support -->
        <p class="text-xs text-gray-500">
            Still need help? <a href="/contact-us/" class="underline hover:text-blue-600">Contact Support</a>
        </p>

    </div>

</section>

<?php
        }
    } else {

        echo mysqli_error($connection);
        exit();
    }
}

if (isset($form_type)) { ?>

<section class="min-h-screen bg-green-100 flex flex-col justify-center items-center text-center py-12">

    <!-- Banner -->
    <div class="w-full text-white py-10">
        <h1 class="text-3xl text-green-600 font-bold">Thanks for Contacting Us!</h1>
        <p class="text-base text-gray-700 mt-2">We'll get back to you as soon as possible.</p>
    </div>

    <!-- Main Content -->
    <div class="max-w-3xl w-full bg-white shadow-lg rounded-lg p-8 mt-8 border border-gray-200">

        <!-- Success Icon -->
        <div class="mb-6">
            <i class="ri-mail-line w-20 h-20 text-8xl mx-auto text-green-500"></i>
        </div>

        <!-- Success Message -->
        <h2 class="text-2xl font-bold text-green-600 mb-2">Your Message Has Been Received!</h2>
        <p class="text-gray-700 mb-6">Thank you for reaching out. We are processing your query and will get back to you
            soon.</p>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row justify-center gap-4 mb-6">
            <a href="/" class="bg-green-600 text-white px-6 py-3 rounded hover:bg-green-700 transition text-sm">
                Continue Shopping
            </a>
        </div>

    </div>

</section>


<?php
}

require_once 'footer.php';

?>