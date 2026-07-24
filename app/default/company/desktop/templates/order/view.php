<?php

if (isset($id)) {

    $id = base64_decode($id);

    $siteId = " IN (" . APP_SITE_ID . ", " . SITE_ID . ")";
    $isApp32 = ((int)SITE_APP_ID === 32);

    $productList = mysqli_query($connection, "SELECT product_id, product_title, product_image FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id WHERE link_type='product' AND product_type='product' AND link_status=200 AND link_site_id" . $siteId);

    if ((mysqli_num_rows($productList) != 0) || $isApp32) {

        $productListData = array();

        while ($productLists = mysqli_fetch_assoc($productList)) {

            extract($productLists);

            $productListData[] = array(
                'product_id' => $product_id,
                'product_title' => $product_title,
                'product_image' => $product_image
            );
        }

        $checkOrderSql = "SELECT profile_first_name, profile_last_name, profile_display_name, user_mobile, user_email, order_product_id, order_vendor_id, order_product_qty, order_currency, order_product_price, order_delivery_charges, order_total_price, order_payment_mode, address_name, address_house_no, address_area, address_landmark, city_name, state_id, address_pin_code, address_type, address_mobile, order_generate, transaction_status, product_title, product_image, inventory_currency_symbol FROM isw_order LEFT JOIN isw_address ON order_address_id=address_id LEFT JOIN isw_user ON order_user_id=user_id LEFT JOIN isw_profile ON profile_user_id=user_id LEFT JOIN isw_city ON city_id=address_city_id LEFT JOIN isw_state ON state_id=address_state_id LEFT JOIN " . TABLE_PREFIX . "transaction ON transaction_order_id=order_id";

        $checkOrderSql .= " LEFT JOIN " . TABLE_PREFIX . "product ON product_id=order_product_id AND product_type='product' AND product_site_id" . $siteId;
        $checkOrderSql .= " LEFT JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=order_product_id AND inventory_type='product' AND inventory_site_id" . $siteId;

        $checkOrderSql .= " WHERE order_id='{$id}' AND order_site_id" . $siteId;

        if ($checkOrder = mysqli_query($connection, $checkOrderSql)) {

            if (mysqli_num_rows($checkOrder) != 0) {

                extract(mysqli_fetch_assoc($checkOrder));

                $orderStatusValue = trim((string)$transaction_status);
                $numericStatusMap = array(
                    '' => 'Pending',
                    '0' => 'Pending',
                    '1' => 'Confirmed',
                    '2' => 'Waiting',
                    '3' => 'Cancelled',
                    '4' => 'Returned',
                    '5' => 'Delivered',
                );

                if (isset($numericStatusMap[$orderStatusValue])) {
                    $orderStatusText = $numericStatusMap[$orderStatusValue];
                } elseif (is_numeric($orderStatusValue)) {
                    $orderStatusText = 'Status Updated';
                } else {
                    $orderStatusText = ucwords(str_replace(array('_', '-'), ' ', $orderStatusValue));
                }

                $orderStatusRaw = strtolower(trim((string)$orderStatusText));
                $orderStatusRaw = str_replace(array(' ', '-'), '_', $orderStatusRaw);
                $orderStatusChipClass = 'badge-default';

                if (in_array($orderStatusRaw, array('delivered', 'complete', 'completed', 'success', 'confirmed', 'confirm'))) {
                    $orderStatusChipClass = 'badge-delivered';
                } elseif (in_array($orderStatusRaw, array('waiting', 'pending', 'processing', 'on_hold', 'in_progress'))) {
                    $orderStatusChipClass = 'badge-pending';
                } elseif (in_array($orderStatusRaw, array('cancel', 'cancelled', 'return', 'returned', 'failed', 'rejected'))) {
                    $orderStatusChipClass = 'badge-cancelled';
                }

?>
                <div class="order-info-ui" data-order-id="<?php echo $id ?>">

                    <div class="premium-header">
                        <div class="premium-header-left">
                            <h2>Order #<?php echo $id ?></h2>
                            <p class="order-info-subtitle">Review, update customer profile, and process the lifecycle of this order.</p>
                        </div>
                        <div>
                            <span class="premium-badge <?php echo $orderStatusChipClass ?>">
                                <i class="fas fa-circle" style="font-size: 8px; margin-right: 5px;"></i> <?php echo $orderStatusText ?>
                            </span>
                        </div>
                    </div>

                    <div class="premium-stats-bar">
                        <div class="premium-stat-item">
                            <span class="premium-stat-label">Payment Mode</span>
                            <span class="premium-stat-value"><?php echo ucwords((($order_payment_mode != '') ? $order_payment_mode : 'N/A')) ?></span>
                        </div>
                        <div class="premium-stat-item">
                            <span class="premium-stat-label">Items Count</span>
                            <span class="premium-stat-value"><?php echo sizeof(explode(',', $order_product_id)) ?> items</span>
                        </div>
                        <div class="premium-stat-item">
                            <span class="premium-stat-label">Created Date</span>
                            <span class="premium-stat-value"><?php echo $order_generate ?></span>
                        </div>
                        <div class="premium-stat-item" style="border-right: none;">
                            <span class="premium-stat-label">Total Amount</span>
                            <span class="premium-stat-value"><?php echo (($order_total_price != '') ? $inventory_currency_symbol . ' ' . $order_total_price : '0') ?></span>
                        </div>
                    </div>

                    <div class="order-info-shell">
                        <form id="orderData">
                            <div class="premium-grid">
                                <!-- LEFT PANEL: Items and Billing info -->
                                <div class="premium-grid-left">

                                    <!-- Card 1: Order Items -->
                                    <div class="premium-card">
                                        <h3 class="premium-card-title"><i class="fas fa-shopping-basket"></i> Order Items</h3>

                                        <?php
                                        $productData = array_filter(array_map('trim', explode(',', (string)$order_product_id)), 'strlen');
                                        $productQtyData = array_map('trim', explode(',', (string)$order_product_qty));

                                        $hasProducts = !empty($productData);
                                        ?>
                                        <div id="noProductWarning" style="background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 8px; padding: 12px; margin-bottom: 12px; display: <?php echo $hasProducts ? 'none' : 'flex'; ?>; gap: 8px; align-items: flex-start;">
                                            <i class="fas fa-exclamation-triangle" style="color: var(--warning); margin-top: 2px;"></i>
                                            <div style="font-size: 12px; color: #78350f; line-height: 1.5;">
                                                <strong>No Product Assigned</strong><br>
                                                Please assign a product to this order.
                                            </div>
                                        </div>

                                        <!-- Product Edit/Re-assign Mode -->
                                        <div id="productEditMode" style="display: none !important;">
                                            <div style="display: grid; grid-template-columns: 1fr 100px; gap: 12px; align-items: flex-end; width: 100%;">
                                                <div class="premium-form-group">
                                                    <label>Select Product</label>
                                                    <select name="assigned_product_id" class="premium-form-control selectJS" style="width: 100%;">
                                                        <option value="">Select product</option>
                                                        <?php foreach ($productListData as $productInfo) {
                                                            echo "<option value='" . htmlspecialchars($productInfo['product_id'], ENT_QUOTES) . "' data-image='" . htmlspecialchars($productInfo['product_image'] ?? '', ENT_QUOTES) . "'>" . htmlspecialchars($productInfo['product_title'], ENT_QUOTES) . "</option>";
                                                        } ?>
                                                    </select>
                                                </div>
                                                <div class="premium-form-group">
                                                    <label>Qty</label>
                                                    <input type="text" name="assigned_qty" id="assignedQtyInput" class="premium-form-control" value="1">
                                                </div>
                                            </div>
                                            <div style="margin-top: 12px; display: flex; gap: 8px;">
                                                <button type="button" class="btn-premium-action btn-save" id="saveProductEditBtn" style="height: 32px; font-size: 12px; padding: 0 10px;"><i class="fas fa-check"></i> Assign product</button>
                                                <button type="button" class="btn-premium-action btn-cancel" id="cancelProductEditBtn" style="height: 32px; font-size: 12px; padding: 0 10px; background-color: var(--neutral-300) !important; color: var(--neutral-700) !important;"><i class="fas fa-times"></i> Cancel</button>
                                            </div>
                                        </div>

                                        <div id="orderItemsListContainer" style="display: <?php echo $hasProducts ? 'block' : 'none'; ?>;">
                                            <?php
                                            if ($hasProducts) {
                                                for ($i = 0; $i < sizeof($productData); $i++) {
                                                    echo "<input type='hidden' name='totalProducts' value='" . sizeof($productData) . "'>";
                                                    echo "<input type='hidden' name='product_id_{$i}' value='{$productData[$i]}'>";
                                                    echo "<input type='hidden' name='qty_{$i}' value='{$productQtyData[$i]}'>";

                                                    if ($checkProduct = mysqli_query($connection, "SELECT product_id, product_image, product_title, inventory_currency_symbol FROM isw_product INNER JOIN isw_inventory ON inventory_relation_id=product_id WHERE product_id='{$productData[$i]}' AND inventory_type='product' AND product_site_id"  . $siteId . " AND inventory_site_id" . $siteId)) {
                                                        if (mysqli_num_rows($checkProduct) != 0) {
                                                            extract(mysqli_fetch_assoc($checkProduct)); ?>

                                                            <div class="order-item-row" data-index="<?php echo $i ?>">
                                                                <div class="order-item-view-container">
                                                                    <div class="order-item-view-left">
                                                                        <div class="order-item-thumb-wrapper">
                                                                            <?php if (trim((string)$product_image) != '') { ?>
                                                                                <img class="view-item-image" src="<?php echo htmlspecialchars($product_image, ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($product_title, ENT_QUOTES); ?>">
                                                                            <?php } else { ?>
                                                                                <div class="view-item-image-placeholder select-product-option-no-img">📦</div>
                                                                            <?php } ?>
                                                                        </div>
                                                                        <div class="order-item-details">
                                                                            <div class="order-item-title view-item-title"><?php echo htmlspecialchars($product_title, ENT_QUOTES) ?></div>
                                                                            <div class="order-item-qty">Qty: <span class="view-item-qty"><?php echo isset($productQtyData[$i]) ? htmlspecialchars(str_replace(' ', '', $productQtyData[$i]), ENT_QUOTES) : '0' ?></span></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } else {
                                                            $fallbackProductTitle = 'Unknown Product';
                                                            $fallbackProductImage = '';
                                                            for ($j = 0; $j < sizeof($productListData); $j++) {
                                                                if ((string)$productListData[$j]['product_id'] == (string)$productData[$i]) {
                                                                    $fallbackProductTitle = $productListData[$j]['product_title'];
                                                                    $fallbackProductImage = isset($productListData[$j]['product_image']) ? $productListData[$j]['product_image'] : '';
                                                                    break;
                                                                }
                                                            }
                                                        ?>
                                                            <div class="order-item-row" data-index="<?php echo $i ?>">
                                                                <div class="order-item-view-container">
                                                                    <div class="order-item-view-left">
                                                                        <div class="order-item-thumb-wrapper">
                                                                            <?php if (trim((string)$fallbackProductImage) != '') { ?>
                                                                                <img class="view-item-image" src="<?php echo htmlspecialchars($fallbackProductImage, ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($fallbackProductTitle, ENT_QUOTES); ?>">
                                                                            <?php } else { ?>
                                                                                <div class="view-item-image-placeholder select-product-option-no-img">📦</div>
                                                                            <?php } ?>
                                                                        </div>
                                                                        <div class="order-item-details">
                                                                            <div class="order-item-title view-item-title"><?php echo htmlspecialchars($fallbackProductTitle, ENT_QUOTES) ?></div>
                                                                            <div class="order-item-qty">Qty: <span class="view-item-qty"><?php echo isset($productQtyData[$i]) ? htmlspecialchars(str_replace(' ', '', $productQtyData[$i]), ENT_QUOTES) : '0' ?></span></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                            <?php }
                                                    }
                                                }
                                            }
                                            ?>
                                        </div>

                                        <!-- Add Product Button Wrapper -->
                                        <div id="addProductBtnWrapper" style="display: none !important;">
                                            <button type="button" class="btn-premium-action" id="addProductBtn" style="height: 36px; font-size: 12px; padding: 0 14px; background-color: var(--primary) !important; color: #fff !important; display: flex; align-items: center; gap: 6px; border-radius: 6px; border: none; cursor: pointer; transition: all 0.2s;">
                                                <i class="fas fa-plus"></i> Add Product
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Card 2: Customer Shipping Details -->
                                    <div class="premium-card">
                                        <h3 class="premium-card-title"><i class="fas fa-user-circle"></i> Customer & Shipping Details</h3>
                                        <div class="premium-form-grid">
                                            <div class="premium-form-group">
                                                <label>First Name</label>
                                                <input type="text" name="first_name" class="premium-form-control" value="<?php echo $profile_first_name ?>">
                                            </div>
                                            <div class="premium-form-group">
                                                <label>Last Name</label>
                                                <input type="text" name="last_name" class="premium-form-control" value="<?php echo $profile_last_name ?>">
                                            </div>
                                            <div class="premium-form-group">
                                                <label>Email Address</label>
                                                <input type="text" name="email" class="premium-form-control" value="<?php echo $user_email ?>">
                                            </div>
                                            <div class="premium-form-group">
                                                <label>Mobile Number</label>
                                                <input type="text" name="mobile" class="premium-form-control" value="<?php echo $user_mobile ?>">
                                            </div>
                                            <div class="premium-form-group">
                                                <label>House No. / Suite</label>
                                                <input type="text" name="address_house_no" class="premium-form-control" value="<?php echo $address_house_no ?>">
                                            </div>
                                            <div class="premium-form-group">
                                                <label>Address Line</label>
                                                <input type="text" name="address_area" class="premium-form-control" value="<?php echo $address_area ?>">
                                            </div>
                                            <div class="premium-form-group">
                                                <label>Landmark</label>
                                                <input type="text" name="address_landmark" class="premium-form-control" value="<?php echo $address_landmark ?>">
                                            </div>
                                            <div class="premium-form-group">
                                                <label>City</label>
                                                <input type="text" name="city" class="premium-form-control" value="<?php echo $city_name ?>">
                                            </div>
                                            <div class="premium-form-group">
                                                <label>State</label>
                                                <select class="premium-form-control" name="state" required>
                                                    <?php
                                                    if ($stateDetails = mysqli_query($connection, "SELECT state_id, state_name FROM " . TABLE_PREFIX . "state WHERE state_country_id=101")) {
                                                        if (mysqli_num_rows($stateDetails) != 0) {
                                                            echo "<option value='' >Select state</option>";
                                                            while ($stateData = mysqli_fetch_assoc($stateDetails)) {
                                                                echo "<option value='{$stateData['state_id']}' " . (($stateData['state_id'] == $state_id) ? 'selected' : '') . " >{$stateData['state_name']}</option>";
                                                            }
                                                        } else {
                                                            echo "<option value=''>No state found</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="premium-form-group">
                                                <label>Pin Code</label>
                                                <input type="text" name="address_pin_code" class="premium-form-control" value="<?php echo $address_pin_code ?>">
                                            </div>
                                            <div class="premium-form-group full-width">
                                                <label>Address Type</label>
                                                <select name="address_type" class="premium-form-control">
                                                    <option value="">Select address type</option>
                                                    <option value="home" <?php echo (($address_type == 'home') ? 'selected' : '') ?>>Home</option>
                                                    <option value="work" <?php echo (($address_type == 'work') ? 'selected' : '') ?>>Work</option>
                                                    <option value="other" <?php echo (($address_type == 'other') ? 'selected' : '') ?>>Other</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- RIGHT PANEL: Billing summary and Receiver Info -->
                                <div class="premium-grid-right">

                                    <!-- Card 3: Payment Summary -->
                                    <div class="premium-card">
                                        <h3 class="premium-card-title"><i class="fas fa-credit-card"></i> Payment Summary</h3>
                                        <div class="billing-summary">
                                            <div class="billing-line">
                                                <span>Subtotal</span>
                                                <span style="font-weight: 600; color: var(--neutral-900);"><?php echo (($order_product_price != '') ? $inventory_currency_symbol . ' ' . $order_product_price : '-') ?></span>
                                            </div>
                                            <div class="billing-line">
                                                <span>Delivery Charge</span>
                                                <span style="font-weight: 600; color: var(--neutral-900);"><?php echo (($order_delivery_charges != '') ? $inventory_currency_symbol . ' ' . $order_delivery_charges : '-') ?></span>
                                            </div>
                                            <div class="billing-line total">
                                                <span>Order Total</span>
                                                <span><?php echo (($order_total_price != '') ? $inventory_currency_symbol . ' ' . $order_total_price : '-') ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Card 4: Receiver Details -->
                                    <div class="premium-card">
                                        <h3 class="premium-card-title"><i class="fas fa-map-marker-alt"></i> Receiver Details</h3>
                                        <div class="premium-form-group" style="margin-bottom: 16px;">
                                            <label>Receiver Name</label>
                                            <input type="text" class="premium-form-control" value="<?php echo $address_name ?>" readonly>
                                        </div>
                                        <div class="premium-form-group" style="margin-bottom: 16px;">
                                            <label>Contact Number</label>
                                            <input type="text" class="premium-form-control" value="<?php echo $address_mobile ?>" readonly>
                                        </div>

                                        <?php
                                        if ($orderMeta = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "order_meta WHERE order_relation_id='{$id}' AND order_site_id" . $siteId)) {
                                            if (mysqli_num_rows($orderMeta) != 0) {
                                                while ($orderMetaData = mysqli_fetch_assoc($orderMeta)) {
                                                    extract($orderMetaData);
                                                    if ($order_meta_key == 'message') { ?>
                                                        <div class="premium-form-group" style="margin-bottom: 16px;">
                                                            <label><?php echo ucwords(str_replace('_', ' ', $order_meta_key)) ?></label>
                                                            <textarea class="premium-form-control" readonly><?php echo $order_meta_value ?></textarea>
                                                        </div>
                                                    <?php } else { ?>
                                                        <div class="premium-form-group" style="margin-bottom: 16px;">
                                                            <label><?php echo ucwords(str_replace('_', ' ', $order_meta_key)) ?></label>
                                                            <input type="text" class="premium-form-control" value="<?php echo $order_meta_value ?>" readonly>
                                                        </div>
                                        <?php }
                                                }
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Floating Actions Bar at bottom of cards -->
                            <div class="premium-action-row">
                                <input type="hidden" name="type" value="order">
                                <input type="hidden" name="id" value="<?php echo $id ?>">
                                <a href="#" id="orderConfirmBtn" class="btn-premium-action btn-confirm"><i class="fas fa-check"></i> Confirm</a>
                                <a href="#" id="orderWaitingBtn" class="btn-premium-action btn-waiting"><i class="fas fa-clock"></i> Waiting</a>
                                <a href="#" id="orderCancelBtn" class="btn-premium-action btn-cancel"><i class="fas fa-times"></i> Cancel</a>
                                <a href="#" id="orderReturnBtn" class="btn-premium-action btn-return"><i class="fas fa-undo"></i> Return</a>
                                <a href="#" id="orderDeliverBtn" class="btn-premium-action btn-delivered"><i class="fas fa-shipping-fast"></i> Delivered</a>
                            </div>
                        </form>
                    </div>
                </div>

<?php
            }
        } else {

            echo 'no order found';
        }
    } else {

        echo "Invalid data received";
    }
}

?>