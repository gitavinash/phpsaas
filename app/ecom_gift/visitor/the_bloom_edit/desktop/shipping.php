<?php
$id = base64_decode($id);

include_once 'header.php';

$safeOrderId = mysqli_real_escape_string($connection, (string) $id);
$orderQuery = "
    SELECT 
        o.order_id, o.order_currency, 
        o.order_delivery_charges,
        o.order_total_price, o.order_coupon, o.order_payment_mode,
        
        c.cart_id, c.cart_relation_id,
        GROUP_CONCAT(c.cart_relation_type) AS relation_types,
        GROUP_CONCAT(GREATEST(c.cart_qty, 1)) AS cart_qty, 
        c.cart_currency,
        SUM(COALESCE(NULLIF(CAST(c.cart_price AS DECIMAL(12,2)), 0), NULLIF(i.inventory_price, 0), NULLIF(i.inventory_mrp, 0), 0) * GREATEST(c.cart_qty, 1)) AS cart_price,
        c.cart_status, 
        GROUP_CONCAT(COALESCE(NULLIF(p.product_image, ''), NULLIF(e.event_image, ''), e.event_thumbnail)) AS product_image,
        GROUP_CONCAT(COALESCE(p.product_title, e.event_title)) AS product_title,
        
        i.inventory_currency, 
        GROUP_CONCAT(COALESCE(NULLIF(CAST(c.cart_price AS DECIMAL(12,2)), 0), NULLIF(i.inventory_price, 0), NULLIF(i.inventory_mrp, 0), 0)) AS productPrices,
        GROUP_CONCAT(COALESCE(NULLIF(i.inventory_mrp, 0), NULLIF(i.inventory_price, 0), NULLIF(CAST(c.cart_price AS DECIMAL(12,2)), 0), 0)) AS productMRPs,
        
        pr.profile_first_name, pr.profile_last_name, pr.profile_display_name, 
        pr.profile_mobile, pr.profile_email, 
        
        a.address_name, a.address_house_no, a.address_landmark, a.address_area,
        ci.city_name, s.state_name, a.address_pin_code, a.address_mobile

    FROM " . TABLE_PREFIX . "order o
    INNER JOIN " . TABLE_PREFIX . "cart c 
        ON c.cart_session_id = o.order_cart_session_id AND c.cart_status=2
    LEFT JOIN " . TABLE_PREFIX . "product p 
        ON (p.product_id = c.cart_relation_id AND c.cart_relation_type = 'product')
    LEFT JOIN " . TABLE_PREFIX . "event e 
        ON (e.event_id = c.cart_relation_id AND c.cart_relation_type = 'event')
    LEFT JOIN " . TABLE_PREFIX . "inventory i 
        ON (i.inventory_relation_id = c.cart_relation_id 
            AND i.inventory_site_id = " . SITE_ID . " 
            AND ((c.cart_relation_type = 'product' AND i.inventory_type = 'product') OR (c.cart_relation_type = 'event' AND i.inventory_type = 'event')))
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

    WHERE o.order_id = '{$safeOrderId}'
      AND o.order_cart_session_id = '" . SESSION_ID . "'
      AND o.order_status = 0
      AND c.cart_site_id = " . SITE_ID . "
    
    GROUP BY o.order_id";

$checkOrder = mysqli_query($connection, $orderQuery);
$orderRowData = ($checkOrder && mysqli_num_rows($checkOrder) != 0) ? mysqli_fetch_assoc($checkOrder) : null;
$relationTypesList = !empty($orderRowData['relation_types']) ? array_map('trim', explode(',', (string) $orderRowData['relation_types'])) : [];
$hasEventInOrder = in_array('event', $relationTypesList, true);
?>

<section class="bg-[#FAF7F2] py-6 md:py-12 pb-28 lg:pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="text-xs sm:text-sm mb-5 text-[#5E5650]" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="/" class="text-[#9B5637] hover:text-[#183028] transition-colors">Home</a></li>
                <li aria-hidden="true">/</li>
                <li><a href="/checkout/" class="text-[#9B5637] hover:text-[#183028] transition-colors">Checkout</a></li>
                <li aria-hidden="true">/</li>
                <li class="text-[#1C1A18] font-medium" aria-current="page">Order Review &amp; Payment</li>
            </ol>
        </nav>

        <div class="mb-6">
            <span class="tbe-eyebrow">Final Step</span>
            <h1 class="font-serif text-3xl md:text-4xl text-[#1C1A18] mt-1">Review &amp; Confirm Order</h1>
        </div>

        <nav class="tbe-step-nav" aria-label="Checkout progress">
            <div class="tbe-step-item is-complete">
                <span class="tbe-step-num">1</span>
                <span>Floral Bag</span>
            </div>
            <span class="text-[#D4C8BA] hidden sm:inline" aria-hidden="true">/</span>
            <div class="tbe-step-item is-complete">
                <span class="tbe-step-num">2</span>
                <span>Shipping Details</span>
            </div>
            <?php if (!$hasEventInOrder): ?>
                <span class="text-[#D4C8BA] hidden sm:inline" aria-hidden="true">/</span>
                <div class="tbe-step-item is-complete">
                    <span class="tbe-step-num">3</span>
                    <span>Delivery Window &amp; Card</span>
                </div>
            <?php endif; ?>
            <span class="text-[#D4C8BA] hidden sm:inline" aria-hidden="true">/</span>
            <div class="tbe-step-item is-active" aria-current="step">
                <span class="tbe-step-num"><?php echo $hasEventInOrder ? '3' : '4'; ?></span>
                <span>Order Review &amp; Pay</span>
            </div>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            <!-- Left Panel: Shipping Info & Order Summary -->
            <div class="lg:col-span-2 space-y-6 sm:space-y-8">
                <div class="bg-white border border-[#E6DDD3] shadow-sm rounded-xl p-4 sm:p-8">
                    <?php
                    if ($orderRowData !== null) {
                        $showSiderbar = true;
                        extract($orderRowData);

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
                            <h2 class="font-serif text-2xl text-[#1C1A18] mb-5 pb-3 border-b border-[#E6DDD3]">Creations in Your Order</h2>

                            <div class="grid gap-4">
                                <?php for ($i = 0; $i < count($productNames); $i++): ?>
                                    <?php
                                    if (isset($productNames[$i])) {
                                        $price = isset($productPrices[$i]) ? (float) $productPrices[$i] : 0;
                                        $mrp = isset($productMRPs[$i]) ? (float) $productMRPs[$i] : 0;
                                        $qty = isset($productQtys[$i]) ? (int) $productQtys[$i] : 0;
                                        $discount = ($mrp > $price && $mrp > 0) ? round((($mrp - $price) / $mrp) * 100) : 0;
                                    ?>
                                        <div class="flex items-center gap-4 bg-[#FAF7F2] p-4 rounded-lg border border-[#E6DDD3]">
                                            <img src="<?php echo safe_html((string) ($productImages[$i] ?? '')); ?>"
                                                alt="<?php echo safe_html((string) $productNames[$i]); ?>"
                                                class="w-20 h-20 object-cover rounded-md border border-[#E6DDD3] flex-shrink-0">

                                            <div class="flex-1 min-w-0">
                                                <p class="font-serif text-lg text-[#1C1A18]"><?php echo safe_html((string) $productNames[$i]); ?></p>

                                                <div class="mt-1 text-sm flex flex-wrap items-baseline gap-2">
                                                    <span class="font-semibold text-[#183028]">
                                                        ₹<?php echo number_format($price, 2); ?>
                                                    </span>
                                                    <?php if ($mrp > $price): ?>
                                                        <span class="line-through text-[#5E5650]">
                                                            ₹<?php echo number_format($mrp, 2); ?>
                                                        </span>
                                                        <span class="text-[#166534] bg-[#ECFDF5] px-2 py-0.5 rounded text-xs font-semibold">
                                                            <?php echo (int) $discount; ?>% OFF
                                                        </span>
                                                    <?php endif; ?>
                                                </div>

                                                <p class="text-xs text-[#5E5650] mt-1">Quantity: <?php echo (int) $qty; ?></p>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php endfor; ?>
                            </div>

                            <!-- Contact & Delivery Info -->
                            <div class="mt-8">
                                <h2 class="font-serif text-xl text-[#1C1A18] mb-4">Contact &amp; Delivery Summary</h2>
                                <dl class="bg-[#FAF7F2] border border-[#E6DDD3] rounded-lg p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-[#1C1A18]">
                                    <div>
                                        <dt class="text-xs uppercase tracking-wider text-[#5E5650]">Sender Name</dt>
                                        <dd class="font-medium mt-0.5"><?php echo safe_html((string) $profile_display_name); ?></dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs uppercase tracking-wider text-[#5E5650]">Sender Contact</dt>
                                        <dd class="font-medium mt-0.5"><?php echo safe_html((string) $profile_mobile); ?></dd>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <dt class="text-xs uppercase tracking-wider text-[#5E5650]">Email Receipt</dt>
                                        <dd class="font-medium mt-0.5"><?php echo safe_html((string) $profile_email); ?></dd>
                                    </div>
                                    <?php if (!empty($deliveryDate)): ?>
                                        <div>
                                            <dt class="text-xs uppercase tracking-wider text-[#5E5650]">Scheduled Date</dt>
                                            <dd class="font-medium mt-0.5"><?php echo date('l, jS F Y', strtotime($deliveryDate)); ?></dd>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($deliverySlot) || !empty($deliveryTime)): ?>
                                        <div>
                                            <dt class="text-xs uppercase tracking-wider text-[#5E5650]">Delivery Window</dt>
                                            <dd class="font-medium mt-0.5"><?php echo safe_html(ucwords((string) $deliverySlot) . ' - ' . (string) $deliveryTime); ?></dd>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($occasion)): ?>
                                        <div>
                                            <dt class="text-xs uppercase tracking-wider text-[#5E5650]">Occasion</dt>
                                            <dd class="font-medium mt-0.5"><?php echo safe_html(ucwords((string) $occasion)); ?></dd>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($message)): ?>
                                        <div class="sm:col-span-2">
                                            <dt class="text-xs uppercase tracking-wider text-[#5E5650]">Enclosure Note</dt>
                                            <dd class="italic text-[#5E5650] mt-0.5">"<?php echo safe_html(ucfirst((string) $message)); ?>"</dd>
                                        </div>
                                    <?php endif; ?>
                                    <div class="sm:col-span-2 pt-3 border-t border-[#E6DDD3]">
                                        <dt class="text-xs uppercase tracking-wider text-[#5E5650]">Ship To</dt>
                                        <dd class="mt-1 leading-relaxed">
                                            <strong><?php echo safe_html((string) $address_name); ?></strong>
                                            &middot; <?php echo safe_html((string) $address_mobile); ?><br>
                                            <?php
                                            $addrParts = array_filter([
                                                $address_house_no ?? '',
                                                $address_landmark ?? '',
                                                $address_area ?? '',
                                            ], static function ($v) {
                                                return trim((string) $v) !== '';
                                            });
                                            echo safe_html(implode(', ', $addrParts));
                                            ?><br>
                                            <?php echo safe_html((string) $city_name . ', ' . (string) $state_name . ' - ' . (string) $address_pin_code); ?>
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                    <?php } else { ?>
                            <div class="text-center py-8 space-y-4">
                                <div class="w-14 h-14 mx-auto rounded-full bg-[#FEF2F2] text-[#991B1B] flex items-center justify-center">
                                    <i class="ri-error-warning-line text-2xl" aria-hidden="true"></i>
                                </div>
                                <h2 class="font-serif text-2xl text-[#1C1A18]">Order Session Expired</h2>
                                <p class="text-sm text-[#5E5650] max-w-md mx-auto">Order not found or expired. Please return to your bag to verify your selections.</p>
                                <a href="/checkout/" class="tbe-btn-forest">Return to Bag</a>
                            </div>
                    <?php } ?>
                </div>
            </div>

            <?php if (isset($showSiderbar)): ?>
                <!-- Right Panel: Cart Summary & Payment -->
                <div>
                    <div class="lg:sticky lg:top-28 bg-white border border-[#E6DDD3] shadow-sm rounded-xl p-5 sm:p-7 space-y-5">
                        <h2 class="font-serif text-xl text-[#1C1A18] pb-3 border-b border-[#E6DDD3]">Payment Breakdown</h2>

                        <?php
                        $subtotal = 0.0;
                        $totalMRP = 0.0;
                        if (isset($productPrices) && is_array($productPrices)) {
                            for ($idx = 0; $idx < count($productPrices); $idx++) {
                                $lineQty = isset($productQtys[$idx]) ? max(1, (int) $productQtys[$idx]) : 1;
                                $linePrice = isset($productPrices[$idx]) ? (float) $productPrices[$idx] : 0.0;
                                $lineMrp = isset($productMRPs[$idx]) ? (float) $productMRPs[$idx] : $linePrice;
                                if ($lineMrp <= 0) {
                                    $lineMrp = $linePrice;
                                }
                                $subtotal += $linePrice * $lineQty;
                                $totalMRP += $lineMrp * $lineQty;
                            }
                        }
                        if ($subtotal <= 0 && !empty($cart_price)) {
                            $subtotal = (float) $cart_price;
                        }
                        $deliveryCharges = (float) ($order_delivery_charges ?? 0);
                        $grandTotal = $subtotal + $deliveryCharges;
                        $discountAmount = $totalMRP - $subtotal;
                        $showDiscount = $totalMRP > $subtotal && $totalMRP > 0 && $subtotal > 0;
                        ?>

                        <div class="space-y-2.5 text-sm text-[#1C1A18]">
                            <?php if ($showDiscount): ?>
                                <div class="flex justify-between">
                                    <span class="text-[#5E5650]">MRP</span>
                                    <span class="text-[#5E5650] line-through">₹<?php echo number_format($totalMRP, 2); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-[#5E5650]">Product Savings</span>
                                    <span class="text-[#166534] font-medium">-₹<?php echo number_format($discountAmount, 2); ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="flex justify-between">
                                <span class="text-[#5E5650]">Subtotal</span>
                                <span id="cartPrice" data-amount="<?php echo $subtotal; ?>">₹<?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[#5E5650]">Delivery Charges</span>
                                <span>₹<?php echo number_format($deliveryCharges, 2); ?></span>
                            </div>
                            <div id="couponDiscountRow" class="flex justify-between hidden">
                                <span class="text-[#5E5650]">Privilege Code Discount</span>
                                <span id="couponDiscountAmount" class="text-[#166534] font-medium">-₹0.00</span>
                            </div>
                            <div class="border-t border-[#E6DDD3] pt-3 flex justify-between items-baseline font-semibold text-lg text-[#183028]">
                                <span>Grand Total</span>
                                <span id="finalPrice" data-base-total="<?php echo $grandTotal; ?>">₹<?php echo number_format($grandTotal, 2); ?></span>
                            </div>
                        </div>

                        <!-- Coupon Code -->
                        <div class="pt-2">
                            <label for="couponCode" class="block text-xs uppercase tracking-[0.14em] font-semibold text-[#5E5650] mb-2">
                                Privilege / Coupon Code
                            </label>
                            <div class="flex gap-2">
                                <input type="text" id="couponCode" autocorrect="off" autocapitalize="characters" spellcheck="false"
                                    class="tbe-input py-2.5 text-base sm:text-sm" placeholder="Enter code">
                                <button type="button" class="checkCoupon tbe-btn-outline px-4 min-h-[44px] whitespace-nowrap text-xs">Apply</button>
                            </div>
                            <span class="couponMsg text-xs text-[#991B1B] mt-1.5 block" aria-live="polite"></span>
                        </div>

                        <!-- Proceed to Payment -->
                        <form class="shipping-box mt-4" novalidate>
                            <input type="hidden" name="order" id="orderId" value="<?php echo safe_html((string) $id); ?>">
                            <input type="hidden" name="paymentMode" value="online">
                            <input type="hidden" name="recaptcha_token" class="recaptcha_token">
                            <button type="submit" id="continueToPayment" class="tbe-btn-forest w-full justify-center py-3.5 text-sm">
                                Proceed to Payment
                            </button>
                        </form>

                        <ul class="pt-4 border-t border-[#E6DDD3] space-y-3 text-xs text-[#5E5650]">
                            <li class="flex items-center gap-2.5">
                                <i class="ri-price-tag-3-line text-base text-[#9B5637]" aria-hidden="true"></i>
                                <span>No hidden charges at checkout</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <i class="ri-shield-check-line text-base text-[#183028]" aria-hidden="true"></i>
                                <span>100% secure encrypted payments</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <i class="ri-heart-3-line text-base text-[#9B5637]" aria-hidden="true"></i>
                                <span>Handcrafted &amp; hand-delivered across Delhi NCR</span>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($showSiderbar)): ?>
    <!-- Mobile Sticky Payment Summary Bar -->
    <div class="tbe-mobile-sticky-bar tbe-mobile-checkout-bar lg:hidden">
        <div class="flex items-center justify-between gap-3">
            <div class="min-w-0">
                <span class="block text-[10px] uppercase tracking-[0.14em] text-[#5E5650] font-semibold">Grand Total</span>
                <span id="mobileFinalPrice" class="font-serif text-lg font-semibold text-[#183028] whitespace-nowrap">
                    ₹<?php echo number_format($grandTotal, 2); ?>
                </span>
            </div>
            <button type="button" data-trigger-payment
                class="flex-1 tbe-btn-forest py-3 px-4 min-h-[44px] text-xs sm:text-sm justify-center whitespace-nowrap">
                <span>Proceed to Payment</span>
            </button>
        </div>
    </div>
    <?php endif; ?>
</section>

<?php include_once 'footer.php'; ?>
