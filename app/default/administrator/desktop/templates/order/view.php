<?php

if (isset($id)) {

    $id = base64_decode($id);

    $siteId = " IN (" . APP_SITE_ID . ", " . SITE_ID . ")";
    $isApp32 = ((int)SITE_APP_ID === 32);

    $searchrProductSql = "SELECT product_id, product_title, product_image FROM isw_product WHERE product_type='product' AND product_site_id" . $siteId . " ORDER BY product_title ASC";

    $productList = mysqli_query($connection, $searchrProductSql);

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

        $checkOrderSql = "SELECT profile_first_name, profile_last_name, profile_display_name, user_mobile, user_email, order_product_id, order_assign_to, order_vendor_id, order_product_qty, order_currency, order_product_price, order_delivery_charges, order_total_price, order_payment_mode, address_name, address_house_no, address_area, address_landmark, city_name, state_id, address_pin_code, address_type, address_mobile, order_generate, order_status, product_title, product_image, inventory_currency_symbol FROM isw_order LEFT JOIN isw_address ON order_address_id=address_id LEFT JOIN isw_user ON order_user_id=user_id LEFT JOIN isw_profile ON profile_user_id=user_id LEFT JOIN isw_city ON city_id=address_city_id LEFT JOIN isw_state ON state_id=address_state_id LEFT JOIN " . TABLE_PREFIX . "transaction ON transaction_order_id=order_id";

        $checkOrderSql .= " LEFT JOIN " . TABLE_PREFIX . "product ON product_id=order_product_id AND product_type='product' AND product_site_id" . $siteId;
        $checkOrderSql .= " LEFT JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id=order_product_id AND inventory_type='product' AND inventory_site_id" . $siteId;

        $checkOrderSql .= " WHERE order_id='{$id}' AND order_site_id" . $siteId;

        if ($checkOrder = mysqli_query($connection, $checkOrderSql)) {

            if (mysqli_num_rows($checkOrder) != 0) {

                extract(mysqli_fetch_assoc($checkOrder));

                $orderStatusValue = trim((string)$order_status);

                $numericStatusMap = array(
                    '0' => 'Pending',
                    '1' => 'Order Accepted by Vendor',
                    '2' => 'Assigned to Vendor',
                    '3' => 'Processing',
                    '4' => 'Shipped',
                    '5' => 'Out for Delivery',
                    '6' => 'Delivered',
                    '7' => 'Cancelled',
                    '8' => 'Returned',
                    '9' => 'Rescheduled',
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
                } elseif (in_array($orderStatusRaw, array('waiting', 'pending', 'processing', 'on_hold', 'in_progress', 'out_for_delivery', 'shipped'))) {
                    $orderStatusChipClass = 'badge-pending';
                } elseif (in_array($orderStatusRaw, array('cancel', 'cancelled', 'return', 'returned', 'failed', 'rejected'))) {
                    $orderStatusChipClass = 'badge-cancelled';
                } elseif (in_array($orderStatusRaw, array('rescheduled', 'reschedule'))) {
                    $orderStatusChipClass = 'badge-rescheduled';
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
                                        <div id="productEditMode" style="display: <?php echo $hasProducts ? 'none' : 'block'; ?>;">
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

                                                    if ($checkProduct = mysqli_query($connection, "SELECT product_id, product_image, product_title, inventory_currency_symbol FROM isw_product INNER JOIN isw_inventory ON inventory_relation_id=product_id WHERE product_id='{$productData[$i]}' AND inventory_type='product' AND product_site_id"  . $siteId . " AND inventory_site_id" . $siteId)) {
                                                        if (mysqli_num_rows($checkProduct) != 0) {
                                                            extract(mysqli_fetch_assoc($checkProduct)); ?>

                                                            <div class="order-item-row" data-index="<?php echo $i ?>">
                                                                <!-- View mode -->
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
                                                                    <div class="order-item-edit-btn-wrapper" style="display: flex; gap: 8px;">
                                                                        <button type="button" class="btn-premium-action btn-edit edit-item-btn"><i class="fas fa-edit"></i> Edit</button>
                                                                        <button type="button" class="btn-premium-action btn-cancel remove-item-btn" style="background-color: #ef4444 !important; color: #fff !important;"><i class="fas fa-trash-alt"></i> Remove</button>
                                                                    </div>
                                                                </div>

                                                                <!-- Edit mode -->
                                                                <div class="order-item-edit-container" style="display: none;">
                                                                    <input type="hidden" name="product_id_<?php echo $i ?>" class="item-product-id-input" value="<?php echo $productData[$i] ?>">
                                                                    <div class="order-item-edit-grid">
                                                                        <div class="premium-form-group">
                                                                            <label>Select Product</label>
                                                                            <select class="premium-form-control select-product-dropdown selectJS" style="width: 100%;">
                                                                                <?php for ($p = 0; $p < sizeof($productListData); $p++) { ?>
                                                                                    <option value="<?php echo htmlspecialchars($productListData[$p]['product_id'], ENT_QUOTES); ?>"
                                                                                        data-image="<?php echo htmlspecialchars($productListData[$p]['product_image'] ?? '', ENT_QUOTES); ?>"
                                                                                        <?php echo ((string)$productListData[$p]['product_id'] == (string)$productData[$i]) ? 'selected' : ''; ?>>
                                                                                        <?php echo htmlspecialchars($productListData[$p]['product_title'], ENT_QUOTES); ?>
                                                                                    </option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                        <div class="premium-form-group">
                                                                            <label>Qty</label>
                                                                            <input type="text" name="qty_<?php echo $i ?>" class="premium-form-control edit-qty-input"
                                                                                value="<?php echo isset($productQtyData[$i]) ? htmlspecialchars(str_replace(' ', '', $productQtyData[$i]), ENT_QUOTES) : '' ?>">
                                                                        </div>
                                                                        <div style="display: flex; gap: 8px; align-items: flex-end; height: 100%;">
                                                                            <button type="button" class="btn-premium-action btn-save save-item-btn" style="flex: 1; height: 38px;"><i class="fas fa-check"></i> Assign vendor</button>
                                                                            <button type="button" class="btn-premium-action btn-cancel cancel-item-btn" style="height: 38px; background-color: var(--neutral-300) !important; color: var(--neutral-700) !important;"><i class="fas fa-times"></i> Cancel</button>
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
                                                                <!-- View mode -->
                                                                <div class="order-item-view-container" style="display: none !important;">
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
                                                                    <div class="order-item-edit-btn-wrapper" style="display: flex; gap: 8px;">
                                                                        <button type="button" class="btn-premium-action btn-edit edit-item-btn"><i class="fas fa-edit"></i> Edit</button>
                                                                        <button type="button" class="btn-premium-action btn-cancel remove-item-btn" style="background-color: #ef4444 !important; color: #fff !important;"><i class="fas fa-trash-alt"></i> Remove</button>
                                                                    </div>
                                                                </div>

                                                                <!-- Edit mode -->
                                                                <div class="order-item-edit-container" style="display: block;">
                                                                    <input type="hidden" name="product_id_<?php echo $i ?>" class="item-product-id-input" value="<?php echo $productData[$i] ?>">
                                                                    <div class="order-item-edit-grid">
                                                                        <div class="premium-form-group">
                                                                            <label>Select Product</label>
                                                                            <select class="premium-form-control select-product-dropdown selectJS" style="width: 100%;">
                                                                                <?php for ($p = 0; $p < sizeof($productListData); $p++) { ?>
                                                                                    <option value="<?php echo htmlspecialchars($productListData[$p]['product_id'], ENT_QUOTES); ?>"
                                                                                        data-image="<?php echo htmlspecialchars($productListData[$p]['product_image'] ?? '', ENT_QUOTES); ?>"
                                                                                        <?php echo ((string)$productListData[$p]['product_id'] == (string)$productData[$i]) ? 'selected' : ''; ?>>
                                                                                        <?php echo htmlspecialchars($productListData[$p]['product_title'], ENT_QUOTES); ?>
                                                                                    </option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                        <div class="premium-form-group">
                                                                            <label>Qty</label>
                                                                            <input type="text" name="qty_<?php echo $i ?>" class="premium-form-control edit-qty-input"
                                                                                value="<?php echo isset($productQtyData[$i]) ? htmlspecialchars(str_replace(' ', '', $productQtyData[$i]), ENT_QUOTES) : '' ?>">
                                                                        </div>
                                                                        <div style="display: flex; gap: 8px; align-items: flex-end; height: 100%;">
                                                                            <button type="button" class="btn-premium-action btn-save save-item-btn" style="flex: 1; height: 38px;"><i class="fas fa-check"></i> Assign Product</button>
                                                                            <button type="button" class="btn-premium-action btn-cancel cancel-item-btn" style="height: 38px; background-color: var(--neutral-300) !important; color: var(--neutral-700) !important;"><i class="fas fa-times"></i> Cancel</button>
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
                                        <div id="addProductBtnWrapper" style="margin-top: 16px; border-top: 1px dashed var(--neutral-300); padding-top: 16px; display: <?php echo $hasProducts ? 'flex' : 'none'; ?>; justify-content: flex-end;">
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
                                                <input type="text" name="first_name" class="premium-form-control" value="<?php echo $profile_first_name ?>" <?php echo ((USER_TYPE != 'administrator') ? 'readonly' : '') ?>>
                                            </div>
                                            <div class="premium-form-group">
                                                <label>Last Name</label>
                                                <input type="text" name="last_name" class="premium-form-control" value="<?php echo $profile_last_name ?>" <?php echo ((USER_TYPE != 'administrator') ? 'readonly' : '') ?>>
                                            </div>
                                            <div class="premium-form-group">
                                                <label>Email Address</label>
                                                <input type="text" name="email" class="premium-form-control" value="<?php echo $user_email ?>" <?php echo ((USER_TYPE != 'administrator') ? 'readonly' : '') ?>>
                                            </div>
                                            <div class="premium-form-group">
                                                <label>Mobile Number</label>
                                                <input type="text" name="mobile" class="premium-form-control" value="<?php echo $user_mobile ?>" readonly>
                                            </div>
                                            <div class="premium-form-group">
                                                <label>House No. / Suite</label>
                                                <input type="text" name="address_house_no" class="premium-form-control" value="<?php echo $address_house_no ?>" <?php echo ((USER_TYPE != 'administrator') ? 'readonly' : '') ?>>
                                            </div>
                                            <div class="premium-form-group">
                                                <label>Address Line</label>
                                                <input type="text" name="address_area" class="premium-form-control" value="<?php echo $address_area ?>" <?php echo ((USER_TYPE != 'administrator') ? 'readonly' : '') ?>>
                                            </div>
                                            <div class="premium-form-group">
                                                <label>Landmark</label>
                                                <input type="text" name="address_landmark" class="premium-form-control" value="<?php echo $address_landmark ?>" <?php echo ((USER_TYPE != 'administrator') ? 'readonly' : '') ?>>
                                            </div>
                                            <div class="premium-form-group">
                                                <label>City</label>
                                                <input type="text" name="city" class="premium-form-control" value="<?php echo $city_name ?>" <?php echo ((USER_TYPE != 'administrator') ? 'readonly' : '') ?>>
                                            </div>
                                            <div class="premium-form-group">
                                                <label>State</label>
                                                <select class="premium-form-control selectJS" name="state" required <?php echo ((USER_TYPE != 'administrator') ? 'disabled' : '') ?>>
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
                                                <input type="text" name="address_pin_code" class="premium-form-control" value="<?php echo $address_pin_code ?>" <?php echo ((USER_TYPE != 'administrator') ? 'readonly' : '') ?>>
                                            </div>
                                            <div class="premium-form-group full-width">
                                                <label>Address Type</label>
                                                <select name="address_type" class="premium-form-control selectJS" <?php echo ((USER_TYPE != 'administrator') ? 'disabled' : '') ?>>
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

                                    <!-- Card: Fulfillment Partner / Vendor Details -->
                                    <div class="premium-card">
                                        <h3 class="premium-card-title"><i class="fas fa-truck"></i> Fulfillment Partner</h3>
                                        <!-- No Vendor Assigned Mode -->
                                        <div id="vendorEditMode" style="display: block;">
                                            <div class="premium-form-group">
                                                <?php

                                                if ($order_vendor_id == 0) { ?>
                                                    <div style="background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 8px; padding: 12px; margin-bottom: 12px; display: flex; gap: 8px; align-items: flex-start;">
                                                        <i class="fas fa-exclamation-triangle" style="color: var(--warning); margin-top: 2px;"></i>
                                                        <div style="font-size: 12px; color: #78350f; line-height: 1.5;">
                                                            <strong>No Partner Assigned</strong><br>
                                                            Please assign a fulfillment partner to process this order.
                                                        </div>
                                                    </div>
                                                <?php
                                                }

                                                ?>
                                                <label><?php echo ($order_vendor_id == 0) ? 'Select' : 'Change'; ?> Fulfillment Partner</label>
                                                <select name="assigned_vendor" class="premium-form-control selectJS" style="width: 100%;">
                                                    <option value="">Select partner</option>
                                                    <?php

                                                    if ($checkVendor = mysqli_query($connection, "SELECT vendor_title, vendor_id FROM " . TABLE_PREFIX . "vendor WHERE vendor_site_id=" . SITE_ID)) {
                                                        if (mysqli_num_rows($checkVendor) != 0) {
                                                            while ($vendorData = mysqli_fetch_assoc($checkVendor)) {
                                                                echo "<option value='{$vendorData['vendor_id']}' " . ($order_vendor_id == $vendorData['vendor_id'] ? "selected" : "") . ">{$vendorData['vendor_title']}</option>";
                                                            }
                                                        } else {
                                                            echo "<option value=''>No partner found</option>";
                                                        }
                                                    }

                                                    ?>
                                                </select>
                                            </div>
                                            <div style="margin-top: 12px;">
                                                <button type="button" class="btn-premium-action btn-save" id="saveVendorEditBtn" style="height: 32px; font-size: 12px; padding: 0 10px;"><i class="fas fa-check"></i> <?php echo ($order_vendor_id == 0) ? 'Assign' : 'Change'; ?> Partner</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Card: Fulfillment Partner / Vendor Details -->
                                    <div class="premium-card">
                                        <h3 class="premium-card-title"><i class="fas fa-truck"></i> Delivery Partner</h3>
                                        <div id="deliveryAgentEditMode" style="display: block;">
                                            <div class="premium-form-group">
                                                <?php

                                                if ($order_assign_to == 0) { ?>
                                                    <div style="background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 8px; padding: 12px; margin-bottom: 12px; display: flex; gap: 8px; align-items: flex-start;">
                                                        <i class="fas fa-exclamation-triangle" style="color: var(--warning); margin-top: 2px;"></i>
                                                        <div style="font-size: 12px; color: #78350f; line-height: 1.5;">
                                                            <strong>No Delivery Partner Assigned</strong><br>
                                                            Please assign a delivery partner to process this order.
                                                        </div>
                                                    </div>
                                                <?php
                                                }

                                                ?>
                                                <label><?php echo ($order_assign_to == 0) ? 'Select' : 'Change'; ?> Delivery Partner</label>
                                                <select name="assigned_delivery_agent" class="premium-form-control selectJS" style="width: 100%;">
                                                    <option value="">Select delivery partner</option>
                                                    <?php

                                                    $userParentFilter = (USER_TYPE != 'administrator') ? "AND user_parent_id=" . USER_ID : "";

                                                    if ($checkDeliveryAgent = mysqli_query($connection, "SELECT user_id, profile_display_name FROM " . TABLE_PREFIX . "site_access INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=site_access_user_id INNER JOIN " . TABLE_PREFIX . "user ON user_id=site_access_user_id {$userParentFilter} WHERE site_access_type='delivery_agent' AND site_access_status=1 AND site_access_site_id=" . SITE_ID)) {
                                                        if (mysqli_num_rows($checkDeliveryAgent) != 0) {
                                                            while ($deliveryAgentData = mysqli_fetch_assoc($checkDeliveryAgent)) {
                                                                echo "<option value='{$deliveryAgentData['user_id']}' " . ($order_assign_to == $deliveryAgentData['user_id'] ? "selected" : "") . ">{$deliveryAgentData['profile_display_name']}</option>";
                                                            }
                                                        }
                                                    }

                                                    ?>
                                                </select>
                                            </div>
                                            <div style="margin-top: 12px;">
                                                <button type="button" class="btn-premium-action btn-save" id="saveDeliveryPartnerBtn" style="height: 32px; font-size: 12px; padding: 0 10px;"><i class="fas fa-check"></i> <?php echo ($order_assign_to == 0) ? 'Assign' : 'Change'; ?> Delivery Partner</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Card 4: Receiver Details & Meta Information -->
                                    <div class="premium-card">
                                        <h3 class="premium-card-title"><i class="fas fa-user-check"></i> Receiver Details & Order Meta</h3>

                                        <div class="premium-form-group" style="margin-bottom: 16px;">
                                            <label>Receiver Name</label>
                                            <input type="text" name="address_name" class="premium-form-control" value="<?php echo htmlspecialchars($address_name ?? '', ENT_QUOTES); ?>" <?php echo ((USER_TYPE != 'administrator') ? 'readonly' : '') ?>>
                                        </div>
                                        <div class="premium-form-group" style="margin-bottom: 16px;">
                                            <label>Contact Number</label>
                                            <input type="text" name="address_mobile" class="premium-form-control" value="<?php echo htmlspecialchars($address_mobile ?? '', ENT_QUOTES); ?>" <?php echo ((USER_TYPE != 'administrator') ? 'readonly' : '') ?>>
                                        </div>

                                        <?php
                                        if (!function_exists('formatDeliverySlotAMPM')) {
                                            function formatDeliverySlotAMPM($slotStr)
                                            {
                                                if (empty($slotStr)) return '';
                                                $slotStr = trim($slotStr);
                                                if (preg_match('/^(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})$/', $slotStr, $matches)) {
                                                    $startTime = strtotime($matches[1]);
                                                    $endTime = strtotime($matches[2]);
                                                    if ($startTime !== false && $endTime !== false) {
                                                        return date('h:i A', $startTime) . ' - ' . date('h:i A', $endTime);
                                                    }
                                                }
                                                return $slotStr;
                                            }
                                        }

                                        $slotAlreadyRendered = false;

                                        if ($orderMeta = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "order_meta WHERE order_relation_id='{$id}' AND order_site_id" . $siteId)) {
                                            if (mysqli_num_rows($orderMeta) != 0) {
                                                $todayDateStr = date('Y-m-d');

                                                while ($orderMetaData = mysqli_fetch_assoc($orderMeta)) {
                                                    extract($orderMetaData);
                                                    $cleanMetaKey = strtolower(trim(str_replace(['_', ' '], '', (string)$order_meta_key)));

                                                    if ($cleanMetaKey == 'deliverydate') {
                                                        $formattedDate = '';
                                                        if (!empty($order_meta_value)) {
                                                            $time = strtotime($order_meta_value);
                                                            $formattedDate = ($time !== false) ? date('Y-m-d', $time) : $order_meta_value;
                                                        }
                                        ?>
                                                        <div class="premium-form-group" style="margin-bottom: 16px;">
                                                            <label>Delivery Date</label>
                                                            <input type="date" name="order_meta[<?php echo htmlspecialchars($order_meta_key, ENT_QUOTES); ?>]" class="premium-form-control order-meta-datepicker" min="<?php echo $todayDateStr; ?>" value="<?php echo htmlspecialchars($formattedDate, ENT_QUOTES); ?>" readonly>
                                                        </div>
                                                    <?php
                                                    } else if (in_array($cleanMetaKey, ['preferredtime', 'deliveryslot', 'timeslot'])) {
                                                        if ($slotAlreadyRendered) continue;
                                                        $slotAlreadyRendered = true;
                                                        $displaySlot = formatDeliverySlotAMPM($order_meta_value ?? '');
                                                    ?>
                                                        <div class="premium-form-group" style="margin-bottom: 16px;">
                                                            <label>Delivery Slot</label>
                                                            <input type="text" name="order_meta[<?php echo htmlspecialchars($order_meta_key, ENT_QUOTES); ?>]" class="premium-form-control" value="<?php echo htmlspecialchars($displaySlot, ENT_QUOTES); ?>" readonly>
                                                        </div>
                                                    <?php
                                                    } else if (strpos($cleanMetaKey, 'photo') !== false || strpos($cleanMetaKey, 'image') !== false || preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', (string)$order_meta_value)) {
                                                    ?>
                                                        <div class="premium-form-group" style="margin-bottom: 16px;">
                                                            <label><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $order_meta_key)), ENT_QUOTES); ?></label>
                                                            <?php if (!empty($order_meta_value)) { ?>
                                                                <div class="delivery-photo-wrapper" style="margin-top: 6px;">
                                                                    <a href="<?php echo htmlspecialchars($order_meta_value, ENT_QUOTES); ?>" target="_blank" title="Click to view full image">
                                                                        <img src="<?php echo htmlspecialchars($order_meta_value, ENT_QUOTES); ?>" alt="Delivery Photo" style="max-width: 220px; max-height: 220px; border-radius: 10px; border: 1px solid #cbd5e1; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08); display: block; object-fit: cover;">
                                                                    </a>
                                                                </div>
                                                            <?php } else { ?>
                                                                <span class="text-muted" style="font-size: 13px; color: #64748b;">No photo available</span>
                                                            <?php } ?>
                                                        </div>
                                                    <?php
                                                    } else {
                                                    ?>
                                                        <div class="premium-form-group" style="margin-bottom: 16px;">
                                                            <label><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $order_meta_key)), ENT_QUOTES); ?></label>
                                                            <?php if (strlen($order_meta_value) > 60 || in_array($cleanMetaKey, ['message', 'comment', 'reason', 'note', 'description'])) { ?>
                                                                <textarea name="order_meta[<?php echo htmlspecialchars($order_meta_key, ENT_QUOTES); ?>]" class="premium-form-control" readonly><?php echo htmlspecialchars($order_meta_value ?? '', ENT_QUOTES); ?></textarea>
                                                            <?php } else { ?>
                                                                <input type="text" name="order_meta[<?php echo htmlspecialchars($order_meta_key, ENT_QUOTES); ?>]" class="premium-form-control" value="<?php echo htmlspecialchars($order_meta_value ?? '', ENT_QUOTES); ?>" readonly>
                                                            <?php } ?>
                                                        </div>
                                            <?php       }
                                                }
                                            }
                                        }

                                        if (!$slotAlreadyRendered) {
                                            ?>
                                            <div class="premium-form-group" style="margin-bottom: 16px;">
                                                <label>Delivery Slot</label>
                                                <input type="text" class="premium-form-control" value="Not selected" readonly>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Floating Actions Bar at bottom of cards -->
                            <div class="premium-action-row">
                                <input type="hidden" name="id" value="<?php echo $id ?>">

                                <!-- Backend Admin CTA -->
                                <a href="#" id="orderUpdateData" class="btn-premium-action btn-reschedule"><i class="fas fa-check"></i> Update order</a>
                                <!-- CTA for Vendors -->
                                <input type="hidden" name="type" value="order">
                                <input type="hidden" name="id" value="<?php echo $id ?>">
                                <a href="#" id="orderConfirmBtn" class="btn-premium-action btn-confirm"><i class="fas fa-check"></i> Accept</a>
                                <a href="#" id="orderCancelBtn" class="btn-premium-action btn-cancel"><i class="fas fa-times"></i> Reject</a>
                                <!-- CTA for Delivery Agents -->
                                <a href="#" id="orderRescheduleBtn" class="btn-premium-action btn-reschedule"><i class="fas fa-calendar-plus"></i> Reschedule Delivery</a>
                                <a href="#" id="orderReturnBtn" class="btn-premium-action btn-return"><i class="fas fa-undo"></i> Return</a>
                                <a href="#" id="orderDeliverBtn" class="btn-premium-action btn-delivered"><i class="fas fa-shipping-fast"></i> Delivered</a>
                                <a href="/tracking/?orderid=OD<?php echo $id ?>" class="btn-premium-action btn-track" target="_blank"><i class="fas fa-route"></i> Track order <i class="fas fa-external-link-alt" style="font-size: 11px; margin-left: 4px;"></i></a>

                            </div>
                        </form>
                    </div>
                </div>

                <!-- Order Action Modal -->
                <div id="orderActionModal" class="order-action-modal">
                    <div class="order-action-modal-backdrop"></div>
                    <div class="order-action-modal-content">
                        <div class="order-action-modal-header">
                            <h3 id="modalTitle">Process Order Action</h3>
                            <button type="button" class="close-modal-btn" aria-label="Close modal">&times;</button>
                        </div>
                        <form id="orderActionForm" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <input type="hidden" name="action_type" id="actionTypeInput" value="">

                            <div id="returnReasonGroup" class="premium-form-group" style="display: none;">
                                <label for="returnReasonInput">Return Reason <span id="returnReasonRequiredMark" class="required" style="display: none;">*</span></label>
                                <select id="returnReasonInput" name="return_reason" class="premium-form-control selectJS">
                                    <option value="">Select reason</option>
                                    <option value="Damaged Item">Damaged Item</option>
                                    <option value="Wrong Item Delivered">Wrong Item Delivered</option>
                                    <option value="Quality Issue">Quality Issue</option>
                                    <option value="Size Issue">Size Issue</option>
                                    <option value="Late Delivery">Late Delivery</option>
                                    <option value="Changed My Mind">Changed My Mind</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="premium-form-group">
                                <label for="actionComment">Comment <span id="commentRequiredMark" class="required">*</span></label>
                                <textarea id="actionComment" name="comment" class="premium-form-control" placeholder="Enter comments here..."></textarea>
                            </div>

                            <div id="rescheduleFields" style="display: none;">
                                <div class="premium-form-group">
                                    <label for="deliveryDateInput">Delivery Date <span id="deliveryDateRequiredMark" class="required" style="display: none;">*</span></label>
                                    <input type="date" id="deliveryDateInput" name="delivery_date" class="premium-form-control">
                                </div>
                                <div class="premium-form-group">
                                    <label for="deliveryPreferredTimeInput">Delivery Preferred Time <span id="deliveryTimeRequiredMark" class="required" style="display: none;">*</span></label>
                                    <select id="deliveryPreferredTimeInput" name="delivery_preferred_time" class="premium-form-control selectJS">
                                        <option value="">Select time slot</option>
                                        <option value="00:00-01:00">12:00 AM - 01:00 AM</option>
                                        <option value="01:00-02:00">01:00 AM - 02:00 AM</option>
                                        <option value="02:00-03:00">02:00 AM - 03:00 AM</option>
                                        <option value="03:00-04:00">03:00 AM - 04:00 AM</option>
                                        <option value="04:00-05:00">04:00 AM - 05:00 AM</option>
                                        <option value="05:00-06:00">05:00 AM - 06:00 AM</option>
                                        <option value="06:00-07:00">06:00 AM - 07:00 AM</option>
                                        <option value="07:00-08:00">07:00 AM - 08:00 AM</option>
                                        <option value="08:00-09:00">08:00 AM - 09:00 AM</option>
                                        <option value="09:00-10:00">09:00 AM - 10:00 AM</option>
                                        <option value="10:00-11:00">10:00 AM - 11:00 AM</option>
                                        <option value="11:00-12:00">11:00 AM - 12:00 PM</option>
                                        <option value="12:00-13:00">12:00 PM - 01:00 PM</option>
                                        <option value="13:00-14:00">01:00 PM - 02:00 PM</option>
                                        <option value="14:00-15:00">02:00 PM - 03:00 PM</option>
                                        <option value="15:00-16:00">03:00 PM - 04:00 PM</option>
                                        <option value="16:00-17:00">04:00 PM - 05:00 PM</option>
                                        <option value="17:00-18:00">05:00 PM - 06:00 PM</option>
                                        <option value="18:00-19:00">06:00 PM - 07:00 PM</option>
                                        <option value="19:00-20:00">07:00 PM - 08:00 PM</option>
                                        <option value="20:00-21:00">08:00 PM - 09:00 PM</option>
                                        <option value="21:00-22:00">09:00 PM - 10:00 PM</option>
                                        <option value="22:00-23:00">10:00 PM - 11:00 PM</option>
                                        <option value="23:00-24:00">11:00 PM - 12:00 AM</option>
                                    </select>
                                </div>
                            </div>

                            <div class="premium-form-group" id="actionPhotoGroup">
                                <label for="actionPhoto">Upload Photo <span id="photoRequiredMark" class="required" style="display: none;">*</span></label>
                                <div class="photo-upload-container">
                                    <input type="file" id="actionPhoto" name="photo" accept="image/*" class="premium-form-control-file">
                                    <div class="photo-preview-box" id="photoPreviewBox">
                                        <i class="fas fa-camera"></i>
                                        <span>Select or capture photo</span>
                                    </div>
                                </div>
                            </div>

                            <div class="order-action-modal-footer">
                                <button type="button" class="btn-premium-action btn-secondary close-modal-btn" style="background-color: #3b82f6 !important; color: #ffffff !important;">Cancel</button>
                                <button type="submit" class="btn-premium-action btn-primary" id="submitActionBtn">Confirm</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="orderActionFeedback" class="order-action-feedback" aria-live="polite" style="display: none;">
                    <div class="order-action-feedback-header">
                        <div>
                            <span class="order-action-feedback-title" id="orderActionFeedbackTitle">Order Update</span>
                            <div class="order-action-feedback-meta" id="orderActionFeedbackMeta">Status response</div>
                        </div>
                        <button type="button" id="toggleOrderActionFeedback" class="order-action-feedback-toggle" aria-expanded="false">Show</button>
                    </div>
                    <div class="order-action-feedback-body" id="orderActionFeedbackBody"></div>
                </div>

                <!-- Toast Notification Container -->
                <div id="orderToastNotification" class="order-toast-notification"></div>

                <script>
                    window.allProductsList = <?php echo json_encode($productListData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
                </script>
                <script src="<?php echo JS_LIBRARY; ?>order_view.js"></script>

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