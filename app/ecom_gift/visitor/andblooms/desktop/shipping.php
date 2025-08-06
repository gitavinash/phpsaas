<?php
$id = base64_decode($id);

include_once 'header.php';
?>

<section class="bg-gray-100 py-24">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Breadcrumb -->
        <nav class="text-sm mb-6 text-gray-600" aria-label="breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="/" class="text-primary hover:underline">Home</a></li>
                <li>/</li>
                <li class="text-gray-800">Shipping</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Left Panel: Shipping Info & Order Summary -->
            <div class="md:col-span-2 space-y-8">
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Shipping Details</h2>

                    <?php
                    $orderQuery = "
    SELECT 
        o.order_id, o.order_currency, 
        o.order_delivery_charges,
        o.order_total_price, o.order_coupon, o.order_payment_mode,
        
        c.cart_id, c.cart_relation_id, c.cart_relation_type,
        GROUP_CONCAT(c.cart_qty) AS cart_qty, 
        c.cart_currency, SUM(c.cart_price) AS cart_price,
        c.cart_status, 
        GROUP_CONCAT(p.product_image) AS product_image,
        GROUP_CONCAT(p.product_title) AS product_title,
        
        i.inventory_currency, 
        GROUP_CONCAT(i.inventory_price) AS productPrices,
        GROUP_CONCAT(i.inventory_mrp) AS productMRPs,
        
        pr.profile_first_name, pr.profile_last_name, pr.profile_display_name, 
        pr.profile_mobile, pr.profile_email, 
        
        a.address_name, a.address_house_no, a.address_landmark, a.address_area,
        ci.city_name, s.state_name, a.address_pin_code, a.address_mobile

    FROM " . TABLE_PREFIX . "order o
    INNER JOIN " . TABLE_PREFIX . "cart c 
        ON c.cart_session_id = o.order_cart_session_id AND c.cart_status=2
    INNER JOIN " . TABLE_PREFIX . "product p 
        ON p.product_id = c.cart_relation_id
    INNER JOIN " . TABLE_PREFIX . "inventory i 
        ON i.inventory_relation_id = p.product_id
    INNER JOIN " . TABLE_PREFIX . "user u 
        ON o.order_user_id = u.user_id
    INNER JOIN " . TABLE_PREFIX . "profile pr 
        ON pr.profile_user_id = u.user_id
    INNER JOIN " . TABLE_PREFIX . "address a 
        ON o.order_address_id = a.address_id
    INNER JOIN " . TABLE_PREFIX . "city ci 
        ON ci.city_id = a.address_city_id
    INNER JOIN " . TABLE_PREFIX . "state s 
        ON s.state_id = a.address_state_id

    WHERE i.inventory_type = 'product'
      AND o.order_id = '{$id}'
      AND o.order_cart_session_id = '" . SESSION_ID . "'
      AND o.order_status = 0
      AND c.cart_site_id = " . SITE_ID . "
    
    GROUP BY o.order_id";

                    if ($checkOrder = mysqli_query($connection, $orderQuery)) {
                        if (mysqli_num_rows($checkOrder) != 0) {
                            extract(mysqli_fetch_assoc($checkOrder));

                            $orderMetas = [];
                            $metaQuery = "SELECT order_meta_key, order_meta_value 
                                          FROM " . TABLE_PREFIX . "order_meta 
                                          WHERE order_relation_id = '{$order_id}' AND order_meta_status = 1";

                            if ($checkOrderMeta = mysqli_query($connection, $metaQuery)) {
                                while ($orderMeta = mysqli_fetch_assoc($checkOrderMeta)) {
                                    $orderMetas[$orderMeta['order_meta_key']] = $orderMeta['order_meta_value'];
                                }
                            }

                            extract($orderMetas);

                            $productNames = explode(',', $product_title);
                            $productImages = explode(',', $product_image);
                            $productQtys = explode(',', $cart_qty);
                            $productPrices = explode(',', $productPrices);
                            $productMRPs = explode(',', $productMRPs);
                    ?>

                    <!-- Order Summary -->
                    <h3 class="text-xl font-semibold text-gray-700 mb-4">Order Summary</h3>
                    <div class="grid gap-4">
                        <?php for ($i = 0; $i < count($productNames); $i++): ?>
                        <?php
                                    if (isset($productNames[$i])) {
                                        $price = isset($productPrices[$i]) ? (float)$productPrices[$i] : 0;
                                        $mrp = isset($productMRPs[$i]) ? (float)$productMRPs[$i] : 0;
                                        $qty = isset($productQtys[$i]) ? (int)$productQtys[$i] : 0;
                                        $discount = 0;
                                        if ($mrp > $price && $mrp > 0) {
                                            $discount = round((($mrp - $price) / $mrp) * 100);
                                        }
                                    ?>
                        <div class="flex items-center gap-4 bg-gray-50 p-4 rounded border">
                            <img src="<?php echo htmlspecialchars($productImages[$i]); ?>" alt="Product"
                                class="w-20 h-20 object-cover rounded">

                            <div class="flex flex-col justify-between h-full py-1">
                                <!-- Product Name -->
                                <p class="font-medium text-gray-800"><?php echo htmlspecialchars($productNames[$i]); ?>
                                </p>

                                <!-- Price, MRP, Discount -->
                                <div class="mt-1 text-sm text-gray-700">
                                    <span class="font-semibold text-green-600">
                                        ₹<?php echo number_format($price, 2); ?>
                                    </span>
                                    <?php if ($mrp > $price): ?>
                                    <span class="line-through text-gray-500 ml-2">
                                        ₹<?php echo number_format($mrp, 2); ?>
                                    </span>
                                    <span class="ml-2 text-red-500 font-medium">
                                        (<?php echo $discount; ?>% OFF)
                                    </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Quantity -->
                                <p class="text-sm text-gray-600 mt-auto">
                                    Qty: <?php echo $qty; ?>
                                </p>
                            </div>
                        </div>
                        <?php } ?>
                        <?php endfor; ?>
                    </div>



                    <!-- Contact & Delivery Info -->
                    <div class="mt-8">
                        <h3 class="text-xl font-semibold text-gray-700 mb-4">Contact & Delivery Info</h3>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 space-y-2 text-sm text-gray-700">
                            <p><strong>Name:</strong> <?php echo $profile_display_name; ?></p>
                            <p><strong>Contact:</strong> <?php echo $profile_mobile; ?></p>
                            <p><strong>Email:</strong> <?php echo $profile_email; ?></p>
                            <?php if (!empty($deliveryDate)): ?>
                            <p><strong>Delivery Date:</strong>
                                <?php echo date('l, jS F Y', strtotime($deliveryDate)); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($deliverySlot) || !empty($deliveryTime)): ?>
                            <p><strong>Time:</strong> <?php echo ucwords($deliverySlot) . ' - ' . $deliveryTime; ?></p>
                            <?php endif; ?>
                            <?php if (!empty($occasion)): ?>
                            <p><strong>Occasion:</strong> <?php echo ucwords($occasion); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($message)): ?>
                            <p><strong>Message:</strong> <?php echo ucfirst($message); ?></p>
                            <?php endif; ?>
                            <p><strong>Ship To:</strong><br>
                                <?php echo "<strong>{$address_name}</strong>,<br /> <i>{$address_mobile}</i>,<br /> {$address_house_no}, {$address_landmark}, {$address_area},<br>{$city_name}, {$state_name} - {$address_pin_code}"; ?>
                            </p>
                        </div>
                    </div>

                    <?php } else {
                            $hideSidebar = true;
                            echo '<p class="text-red-500 font-semibold">Order not found or expired. Please check your link or try again.</p>';
                        }
                    } ?>
                </div>
            </div>

            <?php

            if (isset($hideSidebar)) { ?>


            <!-- Right Panel: Cart Summary -->
            <div class="relative">
                <div class="sticky top-24 bg-white shadow-md rounded-lg p-6 space-y-6">
                    <h2 class="text-xl font-semibold text-gray-800">Cart Summary</h2>

                    <?php
                        $subtotal = (float)($cart_price ?? 0);
                        $deliveryCharges = (float)($order_delivery_charges ?? 0);
                        $grandTotal = $subtotal + $deliveryCharges;
                        $totalMRP = isset($productMRPs) ? array_sum(array_map('floatval', $productMRPs)) : 0.0;
                        $discountAmount = $totalMRP - $subtotal;
                        $showDiscount = $totalMRP > $subtotal && $totalMRP > 0;
                        ?>

                    <div class="space-y-2 text-gray-700 text-sm">
                        <?php if ($showDiscount): ?>
                        <div class="flex justify-between">
                            <span>MRP</span>
                            <span class="text-gray-500 line-through">₹<?php echo number_format($totalMRP, 2); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Discount</span>
                            <span
                                class="text-green-600 font-medium">-₹<?php echo number_format($discountAmount, 2); ?></span>
                        </div>
                        <?php endif; ?>

                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span>₹<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Delivery Charges</span>
                            <span>₹<?php echo number_format($deliveryCharges, 2); ?></span>
                        </div>
                        <div class="border-t pt-3 flex justify-between font-semibold text-xl text-gray-900">
                            <span>Grand Total</span>
                            <span>₹<?php echo number_format($grandTotal, 2); ?></span>
                        </div>
                    </div>

                    <!-- Coupon Code -->
                    <div>
                        <label for="couponCode" class="block text-sm font-medium text-gray-700 mb-1">Apply
                            Coupon</label>
                        <div class="flex gap-2">
                            <input type="text" id="couponCode"
                                class="flex-1 border rounded px-3 py-2 focus:ring-2 focus:ring-primary"
                                placeholder="Enter coupon">
                            <button type="button"
                                class="checkCoupon bg-primary text-white px-4 py-2 rounded hover:bg-primary/80 transition duration-200">Apply</button>
                        </div>
                        <span class="couponMsg text-xs text-red-500 mt-1 block"></span>
                    </div>

                    <!-- Proceed to Payment -->
                    <form class="mt-4">
                        <input type="hidden" name="order" id="orderId" value="<?php echo $id; ?>">
                        <input type="hidden" name="paymentMode" value="online">
                        <input type="hidden" name="recaptcha_token" class="recaptcha_token">
                        <a href="#" id="continueToPayment"
                            class="block w-full text-center bg-primary text-white py-3 rounded font-semibold hover:bg-primary/90">Proceed
                            to Payment</a>
                    </form>
                    <!-- Additional Benefits -->
                    <ul class="mt-6 space-y-3 text-sm text-gray-600">
                        <li class="flex items-center gap-2">
                            <!-- Money Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-primary" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.333 0-4 1-4 4s2.667 4 4 4 4-1 4-4-2.667-4-4-4zm0 0V4m0 12v4m8-4h2m-2 0a6 6 0 00-12 0h-2" />
                            </svg>
                            No Hidden Charges
                        </li>
                        <li class="flex items-center gap-2">
                            <!-- Shield Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-primary" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 2l7 4v5c0 5-3 9-7 11-4-2-7-6-7-11V6l7-4zm0 10l3 3-5-5" />
                            </svg>
                            100% Secure Payments
                        </li>
                        <li class="flex items-center gap-2">
                            <!-- Smile Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-primary" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                            </svg>
                            2 Lakh+ Smiles Delivered
                        </li>
                    </ul>
                </div>

            </div>

            <?php
            }

            ?>

        </div>
    </div>
</section>

<?php include_once 'footer.php'; ?>