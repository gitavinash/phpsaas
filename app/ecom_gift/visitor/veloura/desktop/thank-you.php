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
                if (!mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "cart SET cart_status=1 WHERE cart_session_id='" . SESSION_ID . "' AND cart_site_id=" . SITE_ID . " AND (cart_status=0 OR cart_status=2)")) { ?>

                    <section class="bg-[#FAF7F5] py-10 sm:py-16 md:py-24 px-4">
                        <div class="max-w-2xl mx-auto bg-white border border-[#E8DDD5] shadow-sm rounded-2xl p-6 sm:p-12 text-center">
                            <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-[#FEF2F2] text-[#B91C1C] flex items-center justify-center">
                                <i class="ri-error-warning-line text-3xl" aria-hidden="true"></i>
                            </div>
                            <span class="vl-eyebrow">Order Status Notice</span>
                            <h1 class="font-serif text-2xl sm:text-3xl md:text-4xl text-[#242220] mt-1 mb-3">Order Synchronization Issue</h1>
                            <p class="text-[#6E675F] text-sm sm:text-base leading-relaxed mb-6">
                                We encountered a technical issue updating your order status. Our concierge team is investigating immediately.
                            </p>

                            <?php if (isset($order_id) && !empty($order_id)): ?>
                                <div class="bg-[#FAF7F5] border border-[#E8DDD5] rounded-lg p-4 text-[#242220] text-base mb-6">
                                    Order Reference: <span class="font-semibold text-[#C47C68]">OD<?php echo htmlspecialchars((string) $order_id, ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                            <?php else: ?>
                                <div class="bg-[#FEF2F2] border border-[#FECACA] rounded-lg p-4 text-[#B91C1C] text-sm mb-6">
                                    <strong>Order Not Found:</strong> We are unable to retrieve your reference automatically. Please reach out to concierge.
                                </div>
                            <?php endif; ?>

                            <div class="flex flex-col sm:flex-row justify-center gap-3 mb-6">
                                <a href="/" class="vl-btn-primary justify-center">Return to Homepage</a>
                                <a href="/contact-us/" class="vl-btn-outline justify-center">Concierge Support</a>
                            </div>
                        </div>
                    </section>

                <?php
                } else {
                ?>

                    <section class="bg-[#FAF7F5] py-10 sm:py-16 md:py-24 px-4">
                        <div class="max-w-2xl mx-auto bg-white border border-[#E8DDD5] shadow-sm rounded-2xl p-6 sm:p-12 text-center">
                            <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-[#EBF5EE] text-[#3D5447] flex items-center justify-center">
                                <i class="ri-check-line text-3xl" aria-hidden="true"></i>
                            </div>
                            <span class="vl-eyebrow">Commission Confirmed</span>
                            <h1 class="font-serif text-2xl sm:text-3xl md:text-4xl text-[#242220] mt-1 mb-3">Thank You for Your Order</h1>
                            <p class="text-[#6E675F] text-sm sm:text-base leading-relaxed mb-6">
                                Your floral arrangement is queued with our master florists. Our concierge team will confirm dispatch timing with you shortly.
                            </p>

                            <?php if (isset($order_id)): ?>
                                <div class="bg-[#FAF7F5] border border-[#E8DDD5] rounded-lg p-4 text-[#242220] text-base mb-8">
                                    Order Reference: <span class="font-semibold text-[#C47C68]">OD<?php echo htmlspecialchars((string) $order_id, ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="flex flex-col sm:flex-row justify-center gap-3">
                                <a href="/" class="vl-btn-primary justify-center">Continue Browsing</a>
                                <a href="/contact-us/" class="vl-btn-outline justify-center">Concierge Support</a>
                            </div>
                        </div>
                    </section>

                <?php
                }
            } else {
                mysqli_query($connection, "ROLLBACK");
            }
        } else { ?>
            <section class="bg-[#FAF7F5] py-10 sm:py-16 md:py-24 px-4">
                <div class="max-w-2xl mx-auto bg-white border border-[#E8DDD5] shadow-sm rounded-2xl p-6 sm:p-12 text-center">
                    <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-[#F4EDE7] text-[#C47C68] flex items-center justify-center">
                        <i class="ri-time-line text-3xl" aria-hidden="true"></i>
                    </div>
                    <span class="vl-eyebrow">Verification Pending</span>
                    <h1 class="font-serif text-2xl sm:text-3xl md:text-4xl text-[#242220] mt-1 mb-3">Your Order Is Under Review</h1>
                    <p class="text-[#6E675F] text-sm sm:text-base leading-relaxed mb-6">
                        We are awaiting confirmation from the payment gateway. Our team will update you once the transaction clears.
                    </p>

                    <?php if (isset($order_id) && !empty($order_id)): ?>
                        <div class="bg-[#FAF7F5] border border-[#E8DDD5] rounded-lg p-4 text-[#242220] text-base mb-6">
                            Order Reference: <span class="font-semibold text-[#C47C68]">OD<?php echo htmlspecialchars((string) $order_id, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="flex flex-col sm:flex-row justify-center gap-3 mb-6">
                        <a href="/" class="vl-btn-primary justify-center">Return to Atelier</a>
                        <a href="/contact-us/" class="vl-btn-outline justify-center">Contact Support</a>
                    </div>
                </div>
            </section>
        <?php
        }
    }
}

if (isset($form_type)) { ?>

    <section class="bg-[#FAF7F5] py-10 sm:py-16 md:py-24 px-4">
        <div class="max-w-2xl mx-auto bg-white border border-[#E8DDD5] shadow-sm rounded-2xl p-6 sm:p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-[#EBF5EE] text-[#3D5447] flex items-center justify-center">
                <i class="ri-mail-check-line text-3xl" aria-hidden="true"></i>
            </div>
            <span class="vl-eyebrow">Concierge Confirmation</span>
            <h1 class="font-serif text-2xl sm:text-3xl md:text-4xl text-[#242220] mt-1 mb-3">Thank You for Reaching Out</h1>
            <p class="text-[#6E675F] text-sm sm:text-base leading-relaxed mb-8">
                We have received your message and our floral concierge team will get back to you shortly.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-3">
                <a href="/" class="vl-btn-primary justify-center">Continue Exploring</a>
            </div>
        </div>
    </section>

<?php
}

require_once 'footer.php';
?>
