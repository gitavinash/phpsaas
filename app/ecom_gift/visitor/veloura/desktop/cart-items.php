<?php

$totalPriceMRP = 0;
$totalPrice = 0;
$totalBagUnits = 0;
$hasEventInCart = false;
$cartItemsList = [];

if (isset($checkCart) && $checkCart) {
    while ($cartRow = mysqli_fetch_assoc($checkCart)) {
        $cartItemsList[] = $cartRow;
        $totalBagUnits += max(1, (int) ($cartRow['cart_qty'] ?? 1));
        if (isset($cartRow['cart_relation_type']) && strtolower($cartRow['cart_relation_type']) === 'event') {
            $hasEventInCart = true;
        }
    }
}

include_once 'header.php';

?>

<section class="bg-[#FAF7F5] py-6 md:py-12 pb-28 lg:pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="text-xs sm:text-sm mb-5 text-[#6E675F]" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="/" class="text-[#C47C68] hover:text-[#242220] transition-colors">Home</a></li>
                <li aria-hidden="true">/</li>
                <li class="text-[#242220] font-medium" aria-current="page">Bag &amp; Checkout</li>
            </ol>
        </nav>

        <div class="mb-6">
            <span class="vl-eyebrow">Atelier Checkout</span>
            <h1 class="font-serif text-3xl md:text-4xl text-[#242220] mt-1">Your Floral Bag &amp; Delivery</h1>
        </div>

        <!-- Multi-Step Progress Indicator -->
        <nav class="vl-step-nav" aria-label="Checkout progress">
            <div class="vl-step-item is-active" data-checkout-step="1" aria-current="step">
                <span class="vl-step-num">1</span>
                <span>Floral Bag</span>
            </div>
            <span class="text-[#E8DDD5] hidden sm:inline" aria-hidden="true">/</span>
            <div class="vl-step-item" data-checkout-step="2">
                <span class="vl-step-num">2</span>
                <span>Shipping Details</span>
            </div>
            <?php if (!$hasEventInCart): ?>
                <span class="text-[#E8DDD5] hidden sm:inline" aria-hidden="true">/</span>
                <div class="vl-step-item" data-checkout-step="3">
                    <span class="vl-step-num">3</span>
                    <span>Delivery Window &amp; Card</span>
                </div>
            <?php endif; ?>
            <span class="text-[#E8DDD5] hidden sm:inline" aria-hidden="true">/</span>
            <div class="vl-step-item" data-checkout-step="4">
                <span class="vl-step-num"><?php echo $hasEventInCart ? '3' : '4'; ?></span>
                <span>Payment</span>
            </div>
        </nav>

        <div class="flex flex-col lg:flex-row gap-8 items-start">
            <!-- Left Column -->
            <div class="w-full lg:w-2/3 space-y-6">
                <!-- Step 1: Cart Items -->
                <div id="checkOut" class="bg-white border border-[#E8DDD5] rounded-xl shadow-sm p-4 sm:p-8">
                    <div class="flex flex-wrap items-baseline justify-between gap-2 pb-3 sm:pb-4 mb-4 sm:mb-6 border-b border-[#E8DDD5]">
                        <div>
                            <h2 class="font-serif text-2xl text-[#242220]">Curated Selection</h2>
                            <p class="text-sm text-[#6E675F] mt-0.5">Hand-prepared fresh creations ready for dispatch</p>
                        </div>
                        <span id="checkoutItemCount" class="text-xs uppercase tracking-[0.14em] text-[#C47C68] font-semibold">
                            <?php echo $totalBagUnits; ?> Item<?php echo $totalBagUnits === 1 ? '' : 's'; ?>
                        </span>
                    </div>

                    <div class="divide-y divide-[#E8DDD5]">
                        <?php foreach ($cartItemsList as $cartData):
                            extract($cartData);
                            $itemQty = max(1, (int) ($cart_qty ?? 1));
                            $priceToShow = (float) ($inventory_price ?? 0);
                            if ($priceToShow <= 0 && !empty($cart_price)) {
                                $priceToShow = (float) $cart_price;
                            }
                            $mrpValue = (float) ($inventory_mrp ?? 0);
                            if ($mrpValue <= 0) {
                                $mrpValue = $priceToShow;
                            }
                            $totalPriceMRP += $mrpValue * $itemQty;
                            $totalPrice += $priceToShow * $itemQty;
                            $showDiscount = ($mrpValue > $priceToShow && $mrpValue > 0 && $priceToShow > 0);
                            $discount = $showDiscount ? round((($mrpValue - $priceToShow) / $mrpValue) * 100) : 0;
                            $safeTitle = htmlspecialchars(ucwords((string) ($product_title ?? 'Floral Creation')), ENT_QUOTES, 'UTF-8');
                            $safeLink = !empty($link_url) ? htmlspecialchars((string) $link_url, ENT_QUOTES, 'UTF-8') : '/shop/';
                            $safeImg = !empty($product_image) ? htmlspecialchars((string) $product_image, ENT_QUOTES, 'UTF-8') : htmlspecialchars(USER_IMAGES . 'about-us-banner.jpg', ENT_QUOTES, 'UTF-8');
                        ?>
                            <div id="product_<?php echo (int) $cart_id; ?>"
                                data-price="<?php echo $priceToShow; ?>"
                                data-mrp="<?php echo $mrpValue; ?>"
                                data-qty="<?php echo $itemQty; ?>"
                                class="cart-item flex items-start gap-3 sm:gap-5 py-4 sm:py-5 first:pt-0 last:pb-0">
                                <a href="<?php echo $safeLink; ?>" class="w-20 h-20 sm:w-28 sm:h-28 flex-shrink-0 rounded-lg overflow-hidden border border-[#E8DDD5] bg-[#FAF7F5]">
                                    <img src="<?php echo $safeImg; ?>" alt="<?php echo $safeTitle; ?>" class="w-full h-full object-cover">
                                </a>
                                <div class="flex-1 min-w-0">
                                    <a href="<?php echo $safeLink; ?>"
                                        class="font-serif text-base sm:text-xl text-[#242220] hover:text-[#C47C68] transition-colors block leading-snug">
                                        <?php echo $safeTitle; ?>
                                    </a>
                                    <?php if (!empty($cart_qty)): ?>
                                        <p class="text-xs text-[#6E675F] mt-1">Quantity: <?php echo (int) $cart_qty; ?></p>
                                    <?php endif; ?>
                                    <div class="mt-2 flex flex-wrap items-baseline gap-2.5">
                                        <span class="text-base sm:text-lg font-semibold text-[#242220]">
                                            ₹<?php echo number_format($priceToShow, 0); ?>
                                        </span>
                                        <?php if ($showDiscount): ?>
                                            <span class="text-[#6E675F] line-through text-sm">₹<?php echo number_format($mrpValue, 0); ?></span>
                                            <span class="vl-badge vl-badge-sage">
                                                <?php echo (int) $discount; ?>% OFF
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <button type="button" id="<?php echo (int) $cart_id; ?>"
                                    aria-label="Remove <?php echo $safeTitle; ?> from bag"
                                    class="removeFromCart min-w-[44px] min-h-[44px] inline-flex items-center justify-center rounded-full text-[#6E675F] hover:text-[#B91C1C] hover:bg-[#FEF2F2] transition-colors">
                                    <i class="ri-close-line text-xl" aria-hidden="true"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Step 2: Shipping Details -->
                <div id="shippingDetails" class="bg-white border border-[#E8DDD5] rounded-xl shadow-sm p-4 sm:p-8 hidden">
                    <div class="pb-4 mb-6 border-b border-[#E8DDD5]">
                        <h2 id="shippingHeading" tabindex="-1" class="font-serif text-2xl text-[#242220]">Shipping &amp; Recipient Details</h2>
                        <p class="text-sm text-[#6E675F] mt-0.5">Please enter sender contact details and recipient address across Delhi NCR.</p>
                    </div>

                    <form id="shippingForm" class="space-y-8 checkout-form" novalidate aria-labelledby="shippingHeading">
                        <fieldset class="space-y-4">
                            <legend class="font-serif text-lg text-[#242220] pb-2 border-b border-[#E8DDD5] w-full">Sender Contact Information</legend>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="vl-field">
                                    <label for="first_name" class="text-sm font-medium text-[#242220]">
                                        First Name <span class="text-[#B91C1C]" aria-hidden="true">*</span>
                                    </label>
                                    <input type="text" id="first_name" name="first_name" placeholder="e.g. Ananya"
                                        autocomplete="given-name" enterkeyhint="next" class="vl-input"
                                        data-error="first_name-error" required>
                                    <div id="first_name-error" class="vl-field-error">Error: Please enter your first name.</div>
                                </div>
                                <div class="vl-field">
                                    <label for="last_name" class="text-sm font-medium text-[#242220]">
                                        Last Name <span class="text-[#B91C1C]" aria-hidden="true">*</span>
                                    </label>
                                    <input type="text" id="last_name" name="last_name" placeholder="e.g. Kapoor"
                                        autocomplete="family-name" enterkeyhint="next" class="vl-input"
                                        data-error="last_name-error" required>
                                    <div id="last_name-error" class="vl-field-error">Error: Please enter your last name.</div>
                                </div>
                            </div>

                            <div class="vl-field">
                                <label for="email" class="text-sm font-medium text-[#242220]">
                                    Email Address <span class="text-[#B91C1C]" aria-hidden="true">*</span>
                                </label>
                                <span id="checkout-email-hint" class="vl-field-hint">Order confirmation and receipt are sent here (e.g. name@example.com).</span>
                                <input type="email" id="email" name="email" placeholder="you@example.com"
                                    autocomplete="email" autocorrect="off" autocapitalize="none" spellcheck="false"
                                    enterkeyhint="next" class="vl-input"
                                    aria-describedby="checkout-email-hint" data-error="email-error" required>
                                <div id="email-error" class="vl-field-error">Error: Enter a valid email address in the format you@example.com.</div>
                            </div>

                            <div class="vl-field">
                                <label for="mobile" class="text-sm font-medium text-[#242220]">
                                    Sender Mobile Number <span class="text-[#B91C1C]" aria-hidden="true">*</span>
                                </label>
                                <span id="checkout-mobile-hint" class="vl-field-hint">10-digit Indian mobile number for delivery coordination.</span>
                                <input type="tel" id="mobile" name="mobile" inputmode="tel" autocomplete="tel"
                                    pattern="[\d\s+\-()]{7,15}" placeholder="+91 98100 00000"
                                    enterkeyhint="next" class="vl-input"
                                    aria-describedby="checkout-mobile-hint" data-error="mobile-error" required>
                                <div id="mobile-error" class="vl-field-error">Error: Please enter a valid numeric mobile number.</div>
                            </div>
                        </fieldset>

                        <fieldset class="space-y-4">
                            <legend class="font-serif text-lg text-[#242220] pb-2 border-b border-[#E8DDD5] w-full">Recipient Delivery Address</legend>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="vl-field">
                                    <label for="address_first_name" class="text-sm font-medium text-[#242220]">
                                        Recipient First Name <span class="text-[#B91C1C]" aria-hidden="true">*</span>
                                    </label>
                                    <input type="text" id="address_first_name" name="address_first_name"
                                        placeholder="Recipient first name" autocomplete="shipping given-name"
                                        enterkeyhint="next" class="vl-input" data-error="address_first_name-error" required>
                                    <div id="address_first_name-error" class="vl-field-error">Error: Please enter recipient first name.</div>
                                </div>
                                <div class="vl-field">
                                    <label for="address_last_name" class="text-sm font-medium text-[#242220]">
                                        Recipient Last Name <span class="text-[#B91C1C]" aria-hidden="true">*</span>
                                    </label>
                                    <input type="text" id="address_last_name" name="address_last_name"
                                        placeholder="Recipient last name" autocomplete="shipping family-name"
                                        enterkeyhint="next" class="vl-input" data-error="address_last_name-error" required>
                                    <div id="address_last_name-error" class="vl-field-error">Error: Please enter recipient last name.</div>
                                </div>
                            </div>

                            <div class="vl-field">
                                <label for="address_mobile" class="text-sm font-medium text-[#242220]">
                                    Recipient Contact No. <span class="text-[#B91C1C]" aria-hidden="true">*</span>
                                </label>
                                <input type="tel" id="address_mobile" name="address_mobile" inputmode="tel"
                                    autocomplete="shipping tel" pattern="[\d\s+\-()]{7,15}" placeholder="Recipient mobile number"
                                    enterkeyhint="next" class="vl-input" data-error="address_mobile-error" required>
                                <div id="address_mobile-error" class="vl-field-error">Error: Please enter a valid recipient phone number.</div>
                            </div>

                            <div class="vl-field">
                                <label for="house_no" class="text-sm font-medium text-[#242220]">
                                    Flat, House No., Building <span class="text-[#B91C1C]" aria-hidden="true">*</span>
                                </label>
                                <input type="text" id="house_no" name="house_no"
                                    placeholder="e.g. Villa 14, DLF Phase II" autocomplete="shipping address-line1"
                                    enterkeyhint="next" class="vl-input" data-error="house_no-error" required>
                                <div id="house_no-error" class="vl-field-error">Error: Please enter flat, house, or building details.</div>
                            </div>

                            <div class="vl-field">
                                <label for="area" class="text-sm font-medium text-[#242220]">
                                    Area, Street, Locality
                                </label>
                                <input type="text" id="area" name="area" placeholder="Street, sector, or neighbourhood"
                                    autocomplete="shipping address-line2" enterkeyhint="next" class="vl-input">
                            </div>

                            <div class="vl-field">
                                <label for="landmark" class="text-sm font-medium text-[#242220]">
                                    Landmark (optional)
                                </label>
                                <input type="text" id="landmark" name="landmark" placeholder="Nearest gate, club, or metro"
                                    enterkeyhint="next" class="vl-input">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="vl-field">
                                    <label for="city" class="text-sm font-medium text-[#242220]">
                                        City <span class="text-[#B91C1C]" aria-hidden="true">*</span>
                                    </label>
                                    <input type="text" id="city" name="city" placeholder="City"
                                        autocomplete="shipping address-level2" enterkeyhint="next"
                                        class="vl-input" data-error="city-error" required>
                                    <div id="city-error" class="vl-field-error">Error: Please enter city.</div>
                                </div>

                                <div class="vl-field">
                                    <label for="state" class="text-sm font-medium text-[#242220]">
                                        State <span class="text-[#B91C1C]" aria-hidden="true">*</span>
                                    </label>
                                    <select id="state" name="state" autocomplete="shipping address-level1"
                                        class="vl-input" data-error="state-error" required>
                                        <option value="">Choose State</option>
                                        <?php
                                        $checkState = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "state WHERE state_country_id='101'");
                                        while ($stateData = mysqli_fetch_assoc($checkState)) {
                                            echo '<option value="' . (int) $stateData['state_id'] . '">' . htmlspecialchars($stateData['state_name'], ENT_QUOTES, 'UTF-8') . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <div id="state-error" class="vl-field-error">Error: Please choose a state.</div>
                                </div>

                                <div class="vl-field">
                                    <label for="pincode" class="text-sm font-medium text-[#242220]">
                                        PIN Code <span class="text-[#B91C1C]" aria-hidden="true">*</span>
                                    </label>
                                    <input type="text" id="pincode" name="pincode" inputmode="numeric" pattern="\d{6}" maxlength="6"
                                        autocomplete="shipping postal-code" enterkeyhint="done" placeholder="6-digit PIN"
                                        class="vl-input" data-error="pincode-error" required>
                                    <div id="pincode-error" class="vl-field-error">Error: Enter a valid 6-digit PIN code.</div>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>

                <!-- Step 3: Delivery Date -->
                <div id="deliveryDetails" class="bg-white border border-[#E8DDD5] rounded-xl shadow-sm p-4 sm:p-8 hidden">
                    <div class="pb-4 mb-6 border-b border-[#E8DDD5]">
                        <h2 id="deliveryHeading" tabindex="-1" class="font-serif text-2xl text-[#242220]">Select Delivery Date &amp; Time</h2>
                        <p class="text-sm text-[#6E675F] mt-0.5">Choose your preferred date for handcrafted courier delivery.</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <?php if (date('H') < 21): ?>
                            <button type="button" id="deliverByToday"
                                class="delivery-btn flex flex-col items-center justify-center gap-1 w-full min-h-[72px] border border-[#E8DDD5] rounded-lg px-4 py-3 text-sm font-medium text-[#242220] hover:border-[#C47C68] hover:bg-[#FAF7F5] transition-colors">
                                <span class="text-xs uppercase tracking-wider text-[#C47C68] font-semibold">Same Day</span>
                                <span><?php echo date('jS M'); ?>, Today</span>
                            </button>
                        <?php endif; ?>
                        <button type="button" id="deliverByTomorrow"
                            class="delivery-btn flex flex-col items-center justify-center gap-1 w-full min-h-[72px] border border-[#E8DDD5] rounded-lg px-4 py-3 text-sm font-medium text-[#242220] hover:border-[#C47C68] hover:bg-[#FAF7F5] transition-colors">
                            <span class="text-xs uppercase tracking-wider text-[#C47C68] font-semibold">Next Day</span>
                            <span><?php echo date('jS M', strtotime('+1 day')); ?>, Tomorrow</span>
                        </button>
                        <button type="button"
                            class="deliverByOther delivery-btn flex items-center justify-center gap-2 w-full min-h-[72px] border border-[#E8DDD5] rounded-lg px-4 py-3 text-sm font-medium text-[#242220] hover:border-[#C47C68] hover:bg-[#FAF7F5] transition-colors">
                            <i class="ri-calendar-event-line text-lg text-[#C47C68]" aria-hidden="true"></i>
                            <span>Choose Another Date</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column: Sticky Summary -->
            <div class="w-full lg:w-1/3">
                <div class="lg:sticky lg:top-28 bg-white border border-[#E8DDD5] rounded-xl shadow-sm p-5 sm:p-7 space-y-5">
                    <?php
                    $hasDiscount = ($totalPriceMRP > $totalPrice && $totalPriceMRP != 0);
                    $cartDiscount = $hasDiscount ? ($totalPriceMRP - $totalPrice) : 0;
                    $cartDiscountPercent = $hasDiscount ? round(($cartDiscount / $totalPriceMRP) * 100) : 0;
                    ?>
                    <h2 class="font-serif text-xl text-[#242220] pb-3 border-b border-[#E8DDD5]">Order Summary</h2>

                    <div class="space-y-2.5 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-[#6E675F]">Estimated Total</span>
                            <span id="grandTotal" class="text-xl font-semibold text-[#242220]">
                                ₹<?php echo number_format($totalPrice); ?>
                            </span>
                        </div>

                        <?php if ($hasDiscount): ?>
                            <div class="flex justify-between items-center">
                                <span class="text-[#6E675F]">Original MRP</span>
                                <span id="totalMRP" class="text-sm text-[#6E675F] line-through">
                                    ₹<?php echo number_format($totalPriceMRP); ?>
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-[#6E675F]">Your Savings</span>
                                <span id="youSave" class="text-sm text-[#2E7D32] font-semibold">
                                    ₹<?php echo number_format($cartDiscount); ?> (<?php echo (int) $cartDiscountPercent; ?>% OFF)
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <button type="button" id="placeOrder" class="vl-btn-primary w-full justify-center py-3.5 text-sm">
                        <span>Proceed to Shipping</span>
                        <i class="ri-arrow-right-line" aria-hidden="true"></i>
                    </button>
                    <button type="button" id="continueShipping" data-has-event="<?php echo $hasEventInCart ? 'true' : 'false'; ?>"
                        class="vl-btn-primary w-full justify-center py-3.5 text-sm hidden">
                        <span><?php echo $hasEventInCart ? 'Proceed to Payment' : 'Continue to Delivery Date & Time'; ?></span>
                    </button>
                    <button type="button" id="continueCheckout"
                        class="deliverByOther vl-btn-primary w-full justify-center py-3.5 text-sm hidden">
                        <span>Select Delivery Window</span>
                    </button>

                    <ul class="pt-4 border-t border-[#E8DDD5] space-y-3 text-xs text-[#6E675F]">
                        <li class="flex items-center gap-2.5">
                            <i class="ri-price-tag-3-line text-base text-[#C47C68]" aria-hidden="true"></i>
                            <span>Transparent pricing with no hidden fees</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <i class="ri-shield-check-line text-base text-[#3D5447]" aria-hidden="true"></i>
                            <span>100% encrypted Razorpay checkout</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <i class="ri-heart-3-line text-base text-[#C47C68]" aria-hidden="true"></i>
                            <span>Handcrafted boutique floral arrangements</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mobile Sticky Checkout Summary Bar -->
<div class="vl-mobile-sticky-bar lg:hidden">
    <div class="flex items-center justify-between gap-3">
        <div class="min-w-0">
            <span class="block text-[10px] uppercase tracking-[0.14em] text-[#6E675F] font-semibold">Bag Total</span>
            <span id="mobileGrandTotal" class="font-serif text-lg font-semibold text-[#242220] whitespace-nowrap">
                ₹<?php echo number_format($totalPrice); ?>
            </span>
        </div>
        <button type="button" data-checkout-trigger data-target-btn="#placeOrder"
            class="flex-1 vl-btn-primary py-3 px-4 min-h-[44px] text-xs sm:text-sm justify-center whitespace-nowrap">
            <span>Proceed to Shipping</span>
            <i class="ri-arrow-right-line" aria-hidden="true"></i>
        </button>
    </div>
</div>

<!-- Native Modal Dialog for Delivery Calendar, Time Slots & Enclosure Notes -->
<dialog id="deliveryModal" closedby="any" aria-labelledby="deliveryModalHeading" class="vl-dialog">
    <section class="deliveryDateBox bg-white border border-[#E8DDD5] w-[min(48rem,calc(100dvw-1rem))] rounded-xl shadow-2xl p-4 sm:p-8 relative overflow-hidden">
        <button type="button" aria-label="Close delivery scheduler"
            class="closeCalender absolute top-3 right-3 w-11 h-11 inline-flex items-center justify-center rounded-full text-[#6E675F] hover:text-[#242220] hover:bg-[#FAF7F5] transition-colors">
            <i class="ri-close-line text-2xl" aria-hidden="true"></i>
        </button>

        <!-- Calendar View -->
        <div class="calenderBox space-y-4">
            <h2 id="deliveryModalHeading" class="font-serif text-xl sm:text-2xl text-[#242220] pr-10">Select a Delivery Date</h2>
            <p class="text-sm text-[#6E675F]">Available up to 3 months in advance across Delhi NCR.</p>
            <div id="inlineCalendar" class="border border-[#E8DDD5] rounded-lg p-1.5 sm:p-2 w-full bg-[#FAF7F5]"></div>
        </div>

        <!-- Delivery Window Selection -->
        <div class="showTimeBox space-y-5 hidden">
            <div>
                <h2 class="font-serif text-xl sm:text-2xl text-[#242220] pr-10">Select Delivery Window</h2>
                <p class="text-sm text-[#6E675F] mt-1">
                    Delivery Date: <span id="selectedDeliveryDate" class="font-semibold text-[#242220]">Not selected</span>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <template id="deliverySlotTemplate">
                    <label class="flex items-center gap-3">
                        <input type="radio" name="deliveryType" value="" class="sr-only peer deliverySlot">
                        <div class="peer-checked:border-[#C47C68] peer-checked:bg-[#C47C68] peer-checked:text-white w-full border border-[#E8DDD5] rounded-lg p-3.5 cursor-pointer text-sm font-medium transition-colors"></div>
                    </label>
                </template>

                <label class="flex items-center gap-3">
                    <input type="radio" name="deliveryType" value="morning_0" class="sr-only peer deliverySlot">
                    <div class="peer-checked:border-[#C47C68] peer-checked:bg-[#C47C68] peer-checked:text-white w-full border border-[#E8DDD5] rounded-lg p-3.5 cursor-pointer text-sm font-medium transition-colors hover:border-[#C47C68]">
                        Morning Delivery (Complimentary)
                    </div>
                </label>

                <label class="flex items-center gap-3">
                    <input type="radio" name="deliveryType" value="afternoon_0" class="sr-only peer deliverySlot">
                    <div class="peer-checked:border-[#C47C68] peer-checked:bg-[#C47C68] peer-checked:text-white w-full border border-[#E8DDD5] rounded-lg p-3.5 cursor-pointer text-sm font-medium transition-colors hover:border-[#C47C68]">
                        Afternoon Delivery (Complimentary)
                    </div>
                </label>

                <label class="flex items-center gap-3">
                    <input type="radio" name="deliveryType" value="evening_0" class="sr-only peer deliverySlot">
                    <div class="peer-checked:border-[#C47C68] peer-checked:bg-[#C47C68] peer-checked:text-white w-full border border-[#E8DDD5] rounded-lg p-3.5 cursor-pointer text-sm font-medium transition-colors hover:border-[#C47C68]">
                        Evening Delivery (Complimentary)
                    </div>
                </label>

                <label class="flex items-center gap-3">
                    <input type="radio" name="deliveryType" value="fixed_200" class="sr-only peer deliverySlot">
                    <div class="peer-checked:border-[#C47C68] peer-checked:bg-[#C47C68] peer-checked:text-white w-full border border-[#E8DDD5] rounded-lg p-3.5 cursor-pointer text-sm font-medium transition-colors hover:border-[#C47C68]">
                        Fixed Time Delivery (₹200)
                    </div>
                </label>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                <button type="button" id="goToCalendar"
                    class="vl-btn-outline w-full justify-center py-2.5 text-sm">
                    &larr; Change Date
                </button>
                <button type="button" id="confirmDelivery"
                    class="vl-btn-primary w-full justify-center py-2.5 text-sm">
                    Confirm Delivery Window
                </button>
            </div>
        </div>

        <!-- Fixed Time Slot Grid -->
        <div class="showDeliveryTimeBox space-y-5 hidden">
            <div>
                <h2 class="font-serif text-xl sm:text-2xl text-[#242220] pr-10">Select a Time Slot</h2>
                <p class="text-sm text-[#6E675F] mt-1">
                    Delivery Date: <span id="selectedDeliveryDateConfirm" class="font-semibold text-[#242220]">Not selected</span>
                </p>
            </div>

            <div id="timeSlots" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5 max-h-60 sm:max-h-64 overflow-y-auto pr-1"></div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                <button type="button" class="visibleShowTimeBox vl-btn-outline w-full justify-center py-2.5 text-sm">
                    &larr; Change Window Type
                </button>
                <button type="button" id="confirmTimeSlot" class="vl-btn-primary w-full justify-center py-2.5 text-sm">
                    Confirm Time Slot
                </button>
            </div>
        </div>
    </section>

    <!-- Special Enclosure Message Card -->
    <div class="specialNotesBox bg-white border border-[#E8DDD5] w-[min(32rem,calc(100dvw-1rem))] rounded-xl shadow-2xl p-4 sm:p-8 relative hidden">
        <button type="button" aria-label="Return to delivery schedule"
            class="closeNotes absolute top-3 right-3 w-11 h-11 inline-flex items-center justify-center rounded-full text-[#6E675F] hover:text-[#242220] hover:bg-[#FAF7F5] transition-colors">
            <i class="ri-close-line text-2xl" aria-hidden="true"></i>
        </button>

        <h2 id="specialNotesHeading" tabindex="-1" class="font-serif text-xl sm:text-2xl text-[#242220] mb-1 pr-10">Complimentary Enclosure Card</h2>
        <p class="text-sm text-[#6E675F] mb-5">Personalize your gift with a handwritten note (optional).</p>

        <div class="space-y-4">
            <div class="vl-field">
                <label for="occasionSelect" class="text-sm font-medium text-[#242220]">Occasion</label>
                <select id="occasionSelect" data-title="Select an Occasion" class="selectJS w-full">
                    <option value="">-- Select an Occasion --</option>
                    <option value="Birthday">Birthday</option>
                    <option value="Anniversary">Anniversary</option>
                    <option value="Engagement">Engagement</option>
                    <option value="Wedding">Wedding</option>
                    <option value="Baby Shower">Baby Shower</option>
                    <option value="Housewarming">Housewarming</option>
                    <option value="Promotion">Promotion</option>
                    <option value="Retirement">Retirement</option>
                    <option value="Congratulations">Congratulations</option>
                    <option value="Get Well Soon">Get Well Soon</option>
                    <option value="Thank You">Thank You</option>
                    <option value="Best Wishes">Best Wishes</option>
                    <option value="Valentine's Day">Valentine's Day</option>
                    <option value="Mother's Day">Mother's Day</option>
                    <option value="Father's Day">Father's Day</option>
                    <option value="Friendship Day">Friendship Day</option>
                    <option value="Women's Day">Women's Day</option>
                    <option value="Diwali">Diwali</option>
                    <option value="Holi">Holi</option>
                    <option value="Raksha Bandhan">Raksha Bandhan</option>
                    <option value="Karwa Chauth">Karwa Chauth</option>
                    <option value="Eid">Eid</option>
                    <option value="Christmas">Christmas</option>
                    <option value="New Year">New Year</option>
                    <option value="Apology">Apology</option>
                    <option value="Just Because">Just Because</option>
                </select>
            </div>

            <div class="vl-field">
                <label for="specialNoteInput" class="text-sm font-medium text-[#242220]">Your Personal Message</label>
                <textarea id="specialNoteInput" rows="4" class="vl-input text-base sm:text-sm"
                    placeholder="Write a personal note to accompany your arrangement..."></textarea>
            </div>

            <div class="flex flex-col sm:flex-row justify-between gap-3 pt-2">
                <button type="button" class="backToDelivery vl-btn-outline justify-center py-2.5 px-4 text-sm">
                    &larr; Back to Delivery Window
                </button>
                <button type="button" class="reviewBtn vl-btn-primary justify-center py-2.5 px-5 text-sm">
                    Review Order &amp; Pay
                </button>
            </div>
        </div>
    </div>
</dialog>

<?php

include_once 'footer.php';

?>
