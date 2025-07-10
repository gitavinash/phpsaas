<?php

$totalPriceMRP = 0;
$totalPrice = 0;

include_once 'header.php';

?>


<!-- section global platform start -->
<section class="mt-24 bg-gray-50 py-10">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Left Column -->
            <div class="w-full lg:w-2/3">
                <!-- Cart -->
                <div id="checkOut" class="bg-white rounded shadow p-6">
                    <h2 class="text-2xl font-semibold mb-2">My Cart</h2>
                    <h3 class="text-lg text-gray-600 mb-6">Express Delivery Products</h3>

                    <?php while ($cartData = mysqli_fetch_assoc($checkCart)):
                        extract($cartData);
                        $totalPriceMRP += $inventory_mrp;
                        $totalPrice += $inventory_price;
                    ?>
                    <div id="product_<?php echo $cart_id ?>" data-price="<?php echo $inventory_price ?>"
                        data-mrp="<?php echo $inventory_mrp ?>"
                        class="cart-item flex items-start gap-4 border-b pb-4 mb-4 last:border-b-0 last:pb-0 last:mb-0">
                        <img src="<?php echo $product_thumbnail ?>" class="w-24 h-24 object-cover rounded">
                        <div class="flex-1">
                            <a href="<?php echo $link_url ?>" target="_blank">
                                <h2 class="text-lg font-medium text-gray-800"><?php echo $product_title ?></h2>
                            </a>
                            <?php
                                $priceToShow = $inventory_price;
                                $showDiscount = ($inventory_mrp > $inventory_price && $inventory_mrp != 0);
                                $discount = $showDiscount ? round((($inventory_mrp - $inventory_price) / $inventory_mrp) * 100) : 0;
                                ?>
                            <div class="mt-1 text-primary font-semibold flex items-center gap-2">
                                ₹<?php echo $priceToShow ?>
                                <?php if ($showDiscount): ?>
                                <span class="text-gray-400 line-through text-sm">₹<?php echo $inventory_mrp ?></span>
                                <span class="text-green-600 text-sm font-medium">(<?php echo $discount ?>% OFF)</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <button type="button" id="<?php echo $cart_id ?>" class="removeFromCart hover:text-red-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <?php endwhile; ?>
                </div>

                <!-- Shipping Details -->
                <div id="shippingDetails" class="bg-white rounded shadow p-6 hidden">
                    <h2 class="text-2xl font-semibold mb-2">Checkout</h2>
                    <h3 class="text-lg text-gray-600 mb-6">Shipping Details</h3>

                    <form id="shippingForm" class="space-y-6 checkout-form">
                        <h4 class="text-lg font-semibold text-gray-700">Contact Information</h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    First Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="first_name" name="first_name" placeholder="First name"
                                    class="w-full px-4 py-3 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                                    required>
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Last Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="last_name" name="last_name" placeholder="Last name"
                                    class="w-full px-4 py-3 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                                    required>
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" placeholder="Email"
                                class="w-full px-4 py-3 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                                required>
                        </div>

                        <div>
                            <label for="mobile" class="block text-sm font-medium text-gray-700 mb-1">
                                Contact No. <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="mobile" name="mobile" onwheel="this.blur()"
                                placeholder="Contact No."
                                class="w-full px-4 py-3 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                                required>
                        </div>

                        <h4 class="text-lg font-semibold text-gray-700">Shipping Address</h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="address_first_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    First Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="address_first_name" name="address_first_name"
                                    placeholder="First name"
                                    class="w-full px-4 py-3 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                                    required>
                            </div>
                            <div>
                                <label for="address_last_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Last Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="address_last_name" name="address_last_name"
                                    placeholder="Last name"
                                    class="w-full px-4 py-3 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                                    required>
                            </div>
                        </div>

                        <div>
                            <label for="address_mobile" class="block text-sm font-medium text-gray-700 mb-1">
                                Contact No. <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="address_mobile" name="address_mobile" onwheel="this.blur()"
                                placeholder="Contact No."
                                class="w-full px-4 py-3 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                                required>
                        </div>

                        <div>
                            <label for="house_no" class="block text-sm font-medium text-gray-700 mb-1">
                                Flat, House no., etc. <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="house_no" name="house_no" placeholder="Flat, House no., etc."
                                class="w-full px-4 py-3 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                                required>
                        </div>

                        <div>
                            <label for="area" class="block text-sm font-medium text-gray-700 mb-1">Area, Colony,
                                etc.</label>
                            <input type="text" id="area" name="area" placeholder="Area, Colony, etc."
                                class="w-full px-4 py-3 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>

                        <div>
                            <label for="landmark" class="block text-sm font-medium text-gray-700 mb-1">Landmark
                                (optional)</label>
                            <input type="text" id="landmark" name="landmark" placeholder="Landmark (optional)"
                                class="w-full px-4 py-3 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                                    City <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="city" name="city" placeholder="City"
                                    class="w-full px-4 py-3 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                                    required>
                            </div>

                            <div>
                                <label for="state" class="block text-sm font-medium text-gray-700 mb-1">
                                    State <span class="text-red-500">*</span>
                                </label>
                                <select id="state" name="state"
                                    class="w-full px-4 py-3 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                                    required>
                                    <option value="">Choose State*</option>
                                    <?php
                                    $checkState = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "state WHERE state_country_id='101'");
                                    while ($stateData = mysqli_fetch_assoc($checkState)) {
                                        echo "<option value='{$stateData['state_id']}'>{$stateData['state_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div>
                                <label for="pincode" class="block text-sm font-medium text-gray-700 mb-1">
                                    Pin Code <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="pincode" name="pincode" placeholder="Pin Code"
                                    class="w-full px-4 py-3 border rounded focus:outline-none focus:ring-2 focus:ring-primary"
                                    required>
                            </div>
                        </div>
                    </form>


                </div>

                <!-- Delivery Date -->
                <div id="deliveryDetails" class="bg-white rounded shadow p-6 hidden">
                    <h2 class="text-2xl font-semibold mb-2">Checkout</h2>
                    <h3 class="text-lg text-gray-600 mb-6">Delivery Date & Time</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <?php if (date('H') < 21): ?>
                        <button type="button" id="deliverByToday"
                            class="delivery-btn active block w-full text-center border rounded py-3 hover:bg-gray-100 transition">
                            <?= date('jS M,') ?> Today
                        </button>
                        <?php endif; ?>
                        <button type="button" id="deliverByTomorrow"
                            class="delivery-btn block w-full text-center border rounded py-3 hover:bg-gray-100 transition">
                            <?= date('jS M,', strtotime('+1 day')) ?> Tomorrow
                        </button>
                        <button type="button" id="deliverByOther"
                            class="delivery-btn block w-full text-center border rounded py-3 flex items-center justify-center gap-2 hover:bg-gray-100 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Other dates
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="w-full lg:w-1/3">
                <div class="lg:sticky lg:top-28 bg-white rounded shadow p-6 space-y-4">
                    <?php
                    $hasDiscount = ($totalPriceMRP > $totalPrice && $totalPriceMRP != 0);
                    if ($hasDiscount) {
                        $cartDiscount = $totalPriceMRP - $totalPrice;
                        $cartDiscountPercent = round(($cartDiscount / $totalPriceMRP) * 100);
                    }
                    ?>
                    <h2 class="text-xl font-semibold">Cart Summary</h2>

                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Grand Total</span>
                            <span id="grandTotal"
                                class="text-xl font-bold text-primary">₹<?php echo number_format($totalPrice) ?></span>
                        </div>

                        <?php if ($hasDiscount): ?>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Original MRP</span>
                            <span id="totalMRP"
                                class="text-sm text-gray-500 line-through">₹<?php echo number_format($totalPriceMRP) ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">You Save</span>
                            <span id="youSave" class="text-sm text-green-600 font-semibold">
                                ₹<?php echo number_format($cartDiscount) ?> (<?php echo $cartDiscountPercent ?>% OFF)
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <a href="#" id="placeOrder"
                        class="w-full block text-center bg-primary text-white font-semibold py-3 rounded-md hover:bg-primary/90 transition">
                        Place Order
                    </a>
                    <a href="#" id="continueShipping"
                        class="w-full block text-center bg-primary text-white font-semibold py-3 rounded-md hover:bg-primary/90 transition hidden">
                        Delivery Date & Time
                    </a>
                    <a href="#" id="specialNotes"
                        class="w-full block text-center bg-primary text-white font-semibold py-3 rounded-md hover:bg-primary/90 transition hidden">
                        Special Note
                    </a>

                    <!-- Feature List -->
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


        </div>
</section>

<section class="deliveryDateBox hidden bg-gray-50 py-10">
    <div class="container mx-auto px-4">
        <div class="bg-white shadow rounded p-6">

            <!-- Delivery Date Calendar -->
            <div class="calenderBox hidden">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold">Select delivery date</h3>
                    <button class="closeCalender text-gray-500 hover:text-red-500 text-lg">×</button>
                </div>
                <div id="calender" class="border rounded p-4"></div>
            </div>

            <!-- Delivery Type Selection -->
            <div class="showTimeBox hidden space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-semibold">Select delivery type and slot</h3>
                    <button class="closeCalender text-gray-500 hover:text-red-500 text-lg">×</button>
                </div>

                <div class="space-y-3">
                    <label class="flex items-center gap-3">
                        <input type="radio" name="deliveryType" value="morning_200" class="hidden peer deliverySlot">
                        <div
                            class="peer-checked:border-primary peer-checked:bg-primary/10 w-full border rounded p-3 cursor-pointer">
                            Morning delivery at ₹200
                        </div>
                    </label>

                    <label class="flex items-center gap-3">
                        <input type="radio" name="deliveryType" value="standard_0" class="hidden peer deliverySlot">
                        <div
                            class="peer-checked:border-primary peer-checked:bg-primary/10 w-full border rounded p-3 cursor-pointer">
                            Standard delivery for free
                        </div>
                    </label>

                    <label class="flex items-center gap-3">
                        <input type="radio" name="deliveryType" value="fixed_200" class="hidden peer deliverySlot">
                        <div
                            class="peer-checked:border-primary peer-checked:bg-primary/10 w-full border rounded p-3 cursor-pointer">
                            Fixed Time delivery at ₹200
                        </div>
                    </label>

                    <label class="flex items-center gap-3">
                        <input type="radio" name="deliveryType" value="midnight_249" class="hidden peer deliverySlot">
                        <div
                            class="peer-checked:border-primary peer-checked:bg-primary/10 w-full border rounded p-3 cursor-pointer">
                            Midnight delivery at ₹249
                        </div>
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4">
                    <a href="#"
                        class="showCalender text-center bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 rounded">← Go
                        to calendar</a>
                </div>
            </div>

            <!-- Delivery Time Slots -->
            <div class="showDeliveryTimeBox hidden">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold">Select delivery time</h3>
                    <button class="closeCalender text-gray-500 hover:text-red-500 text-lg">×</button>
                </div>

                <div class="standardDeliveryTimeSlots grid grid-cols-1 md:grid-cols-2 gap-4 hidden mb-6">
                    <?php
                    $slots = ['09:00 - 12:00', '12:00 - 15:00', '15:00 - 18:00', '18:00 - 21:00', '20:00 - 22:00'];
                    foreach ($slots as $slot) {
                        echo "
                <label class='flex items-center gap-3'>
                  <input type='radio' name='deliveryTime' value='$slot' class='hidden peer deliveryTimeSlot'>
                  <div class='w-full p-3 border rounded cursor-pointer peer-checked:border-primary peer-checked:bg-primary/10'>
                    $slot hrs
                  </div>
                </label>
              ";
                    }
                    ?>
                </div>

                <div class="midnightDeliveryTimeSlots hidden mb-6">
                    <label class="flex items-center gap-3">
                        <input type="radio" name="deliveryTime" value="23:00 - 01:00"
                            class="hidden peer deliveryTimeSlot">
                        <div class="p-3 border rounded w-full peer-checked:border-primary peer-checked:bg-primary/10">
                            23:00 - 01:00 hrs
                            <p class="text-xs text-red-500 mt-1">
                                We will deliver product on <?= date('jS F') ?> after 11:00 PM.
                            </p>
                        </div>
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <a href="#"
                        class="visibleShowTimeBox text-center bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 rounded">←
                        Delivery slots</a>
                    <a href="#"
                        class="specialNotesBox text-center bg-primary hover:bg-primary/90 text-white py-2 rounded">Special
                        Notes →</a>
                </div>
            </div>

            <!-- Special Notes -->
            <div class="specialNotesBox hidden mt-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold">Special Notes</h3>
                    <button class="closeCalender text-gray-500 hover:text-red-500 text-lg">×</button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="occasion" class="block text-sm font-medium text-gray-700 mb-1">Select
                            Occasion</label>
                        <select name="occasion" id="occasion" class="w-full border rounded px-4 py-2">
                            <option value="">Select occasion</option>
                            <?php
                            $occasions = [
                                "Anniversary",
                                "Birthday",
                                "Congratulation",
                                "Get Well Soon",
                                "I am Sorry",
                                "Thank U",
                                "Appreciation",
                                "Cheer Up",
                                "New Born",
                                "Wedding",
                                "Just Like That",
                                "Onam",
                                "Eid",
                                "Love & Romance",
                                "Christmas",
                                "New Year",
                                "Lohri",
                                "Rose Day",
                                "Propose Day",
                                "Chocolate Day",
                                "Teddy Day",
                                "Promise Day",
                                "Hug Day",
                                "Kiss Day",
                                "Valentines Day",
                                "Womens Day",
                                "Holi",
                                "April Fool",
                                "Mother's Day",
                                "Father's Day",
                                "Friendship Day",
                                "Rakhi",
                                "Independence Day",
                                "Ganesh Chaturthi",
                                "Teacher's Day",
                                "Grandparent's Day",
                                "Daughter's Day",
                                "Karwa Chauth",
                                "Other"
                            ];
                            foreach ($occasions as $o) echo "<option value='$o'>$o</option>";
                            ?>
                        </select>
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Your Message</label>
                        <textarea id="message" name="message" rows="4"
                            class="w-full border rounded px-4 py-2"></textarea>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4">
                    <a href="#"
                        class="visibleShowTimeBox text-center bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 rounded">←
                        Delivery slots</a>
                    <a href="#"
                        class="reviewBtn text-center bg-primary hover:bg-primary/90 text-white py-2 rounded">Review
                        →</a>
                </div>
            </div>

        </div>
    </div>
</section>


<?php

include_once 'footer.php';

?>