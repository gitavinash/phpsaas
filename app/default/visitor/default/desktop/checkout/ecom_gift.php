<section class="checkout-box">
    <div class="container">
        <div class="col-7 m-col">
            <div class="row">
                <div class="box white-bg">
                    <div class="col">
                        <h2>Order summary</h2>
                    </div>
                    <div class="col">
                        <?php
                        $subTotal = 0;
                        $totalQty = 0;
                        $groupedCart = [];

                        while ($cartData = mysqli_fetch_assoc($checkCart)) {
                            extract($cartData);

                            $key = $cart_relation_type . '_' . $cart_relation_id;

                            if (!isset($groupedCart[$key])) {
                                $groupedCart[$key] = [
                                    'title' => ${$cart_relation_type . '_title'},
                                    'highlight' => ${$cart_relation_type . '_highlight'},
                                    'image' => ${$cart_relation_type . '_image'},
                                    'price' => $inventory_price,
                                    'qty' => 1,
                                ];
                            } else {
                                $groupedCart[$key]['qty']++;
                            }

                            $subTotal += $inventory_price;
                            $totalQty++;
                        }

                        foreach ($groupedCart as $item) {
                            $imgSrc = $item['image'] !== ''
                                ? $item['image']
                                : IMG_LIBRARY . "profile_icon/" . strtolower(trim($item['title'][0]) ?: 'unknown') . ".png";

                            echo "
                            <div class='productBox'>
                                <div class='productImage col-2 m-col-2 no-padding'>
                                    <img src='{$imgSrc}' alt=''>
                                </div>
                                <div class='productText col-8 m-col-8'>
                                    <div class='col-7 m-col-7'>
                                        <p><strong>{$item['title']}</strong></p>
                                        " . (!isset($order) ? "<p><span>{$item['highlight']}</span></p>" : "") . "
                                        <p><span class='qty'>Quantity: {$item['qty']}</span></p>
                                    </div>
                                    <div class='col-3 m-col-3'>
                                        <div class='price float-right'>
                                            <p>{$inventory_currency_symbol} " . number_format($item['price'], 2) . "</p>
                                        </div>
                                    </div>
                                </div>
                            </div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-3 m-col float-right">
            <div class="shadow-box">
                <h2>Cart summary</h2>
                <ul>
                    <li>
                        <p><strong>Subtotal</strong>
                            <span><?php echo $inventory_currency_symbol . ' ' . number_format($subTotal, 2); ?></span>
                        </p>
                    </li>
                    <li>
                        <p><strong>Total Quantity</strong>
                            <span><?php echo $totalQty; ?></span>
                        </p>
                    </li>
                    <?php
                    $taxAmount = 0;
                    if ($inventory_tax_status == 1) {
                        $taxAmount = round(($inventory_tax_rate / 100) * $subTotal, 2);
                        echo "<li>
                            <p><strong>Tax ({$inventory_tax_rate}%)</strong>
                            <span>{$inventory_currency_symbol} {$taxAmount}</span></p>
                        </li>";
                    }

                    $totalPrice = $subTotal + $taxAmount;
                    ?>
                </ul>
                <div class="totalBox">
                    <p><strong>Total</strong>
                        <span><?php echo $inventory_currency_symbol . ' ' . number_format($totalPrice, 2); ?></span>
                    </p>
                </div>

                <!-- Hidden fields -->
                <input type="hidden" name="order" id="orderId" value="<?php echo $orderId ?>">
                <input type="hidden" name="app" id="app" value="<?php echo SITE_APP ?>">
                <input type="hidden" name="orderType" id="orderType"
                    value="<?php echo isset($order) ? $order : 'default' ?>">
                <input type="hidden" name="paymentMode" id="paymentMode" value="online">

                <a href="#" id="processToPayment" class="button">Continue</a>
            </div>
        </div>
    </div>

    <div class="creditBox">
        <div class="row">
            <div class="col">
                <p>By continuing you agree to our
                    <a href="/terms-and-conditions">Terms &amp; Conditions</a> and
                    <a href="/privacy-policy/">Privacy Policy</a>
                </p>
            </div>
        </div>
    </div>
</section>