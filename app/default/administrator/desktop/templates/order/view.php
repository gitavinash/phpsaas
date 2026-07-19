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

        $checkOrderSql = "SELECT profile_first_name, profile_last_name, profile_display_name, user_mobile, user_email, order_product_id, order_vendor_id, order_product_qty, order_currency, order_product_price, order_delivery_charges, order_total_price, order_payment_mode, address_name, address_house_no, address_area, address_landmark, city_name, state_id, address_pin_code, address_type, address_mobile, order_generate, transaction_status, product_title, product_image, inventory_currency_symbol FROM isw_order INNER JOIN isw_address ON order_address_id=address_id INNER JOIN isw_user ON order_user_id=user_id INNER JOIN isw_profile ON profile_user_id=user_id INNER JOIN isw_city ON city_id=address_city_id INNER JOIN isw_state ON state_id=address_state_id LEFT JOIN " . TABLE_PREFIX . "transaction ON transaction_order_id=order_id";

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
                                                                            <select class="premium-form-control select-product-dropdown" style="width: 100%;">
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
                                                                            <select class="premium-form-control select-product-dropdown" style="width: 100%;">
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
                                                <select name="assigned_vendor" class="premium-form-control" style="width: 100%;">
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

                <script>
                    (function() {
                        var root = document.querySelector('.order-info-ui');

                        if (!root) {
                            return;
                        }

                        var orderId = root.getAttribute('data-order-id') || 'default';
                        var storagePrefix = 'order-ui-collapse-' + orderId + '-';
                        var toggleList = root.querySelectorAll('.order-collapse-toggle');

                        function saveState(key, value) {
                            try {
                                localStorage.setItem(key, value);
                            } catch (e) {
                                // Ignore storage errors silently.
                            }
                        }

                        function readState(key) {
                            try {
                                return localStorage.getItem(key);
                            } catch (e) {
                                return null;
                            }
                        }

                        function applyState(panel, toggle, isOpen) {
                            panel.classList.toggle('is-open', isOpen);
                            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                        }

                        for (var i = 0; i < toggleList.length; i++) {
                            (function(toggle) {
                                var targetId = toggle.getAttribute('data-target');
                                var panel = toggle.parentNode;
                                var storageKey = storagePrefix + targetId;
                                var savedValue = readState(storageKey);

                                if (savedValue !== null) {
                                    applyState(panel, toggle, savedValue === 'open');
                                }

                                toggle.addEventListener('click', function() {
                                    var targetId = this.getAttribute('data-target');
                                    var body = root.querySelector('#' + targetId);

                                    if (!body) {
                                        return;
                                    }

                                    var panel = this.parentNode;
                                    var isOpen = panel.classList.contains('is-open');
                                    var nextOpen = !isOpen;

                                    applyState(panel, this, nextOpen);
                                    saveState(storagePrefix + targetId, nextOpen ? 'open' : 'closed');
                                });
                            })(toggleList[i]);
                        }
                    })();
                </script>

                <script>
                    window.allProductsList = <?php echo json_encode($productListData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
                </script>

                <script>
                    (function() {
                        var root = document.querySelector('.order-info-ui');
                        var orderId = root ? (root.getAttribute('data-order-id') || 'default') : 'default';

                        // helper function to save order data via AJAX
                        function saveOrderData(callback) {
                            if (typeof jQuery === 'undefined') {
                                if (callback) callback(false);
                                return;
                            }
                            var $ = jQuery;
                            var formData = $('#orderData').serializeArray();
                            formData.push({
                                name: 'action',
                                value: 'update'
                            });

                            $.ajax({
                                type: 'POST',
                                dataType: 'json',
                                data: formData,
                                success: function(response) {
                                    if (response.status == 200) {
                                        if (typeof Swal !== 'undefined') {
                                            Swal.fire({
                                                icon: 'success',
                                                type: 'success',
                                                title: 'Success',
                                                text: response.msg || 'Order updated successfully.',
                                                timer: 1500,
                                                showConfirmButton: false
                                            });
                                        } else {
                                            alert(response.msg || 'Order updated successfully.');
                                        }
                                        if (callback) callback(true);
                                    } else {
                                        if (typeof Swal !== 'undefined') {
                                            Swal.fire({
                                                icon: 'error',
                                                type: 'error',
                                                title: 'Error',
                                                text: response.msg || 'Something went wrong.',
                                                timer: 2000,
                                                showConfirmButton: false
                                            });
                                        } else {
                                            alert(response.msg || 'Something went wrong.');
                                        }
                                        if (callback) callback(false);
                                    }
                                },
                                error: function() {
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            icon: 'error',
                                            type: 'error',
                                            title: 'Error',
                                            text: 'Failed to update order.',
                                            timer: 2000,
                                            showConfirmButton: false
                                        });
                                    } else {
                                        alert('Failed to update order.');
                                    }
                                    if (callback) callback(false);
                                }
                            });
                        }

                        // Helper function to re-index all inputs and manage totalProducts
                        function reindexProducts() {
                            var rows = document.querySelectorAll('.order-item-row');
                            if (rows.length === 0) {
                                var totalInput = document.querySelector('input[name="totalProducts"]');
                                if (totalInput) totalInput.remove();

                                var warningBlock = document.getElementById('noProductWarning');
                                if (warningBlock) warningBlock.style.display = 'flex';
                                var productEditMode = document.getElementById('productEditMode');
                                if (productEditMode) productEditMode.style.display = 'block';

                                var container = document.getElementById('orderItemsListContainer');
                                if (container) container.style.display = 'none';
                                var addBtnWrapper = document.getElementById('addProductBtnWrapper');
                                if (addBtnWrapper) addBtnWrapper.style.display = 'none';
                                return;
                            }

                            rows.forEach(function(row, index) {
                                row.setAttribute('data-index', index);

                                var idInput = row.querySelector('.item-product-id-input');
                                if (idInput) {
                                    idInput.name = 'product_id_' + index;
                                }

                                var qtyInput = row.querySelector('.edit-qty-input');
                                if (qtyInput) {
                                    qtyInput.name = 'qty_' + index;
                                }
                            });

                            var totalInput = document.querySelector('input[name="totalProducts"]');
                            if (!totalInput) {
                                totalInput = document.createElement('input');
                                totalInput.type = 'hidden';
                                totalInput.name = 'totalProducts';
                                var form = document.getElementById('orderData');
                                if (form) {
                                    form.appendChild(totalInput);
                                }
                            }
                            totalInput.value = rows.length;

                            var allTotals = document.querySelectorAll('input[name="totalProducts"]');
                            if (allTotals.length > 1) {
                                for (var idx = 1; idx < allTotals.length; idx++) {
                                    allTotals[idx].remove();
                                }
                            }
                            updateProductDropdownOptions();
                        }

                        // Helper function to dynamically filter out already selected products from dropdowns
                        function updateProductDropdownOptions() {
                            var selectedIds = [];
                            document.querySelectorAll('.item-product-id-input').forEach(function(input) {
                                if (input.value && input.value.trim() !== '') {
                                    selectedIds.push(String(input.value));
                                }
                            });

                            var unassignedSelect = document.querySelector('#productEditMode select[name="assigned_product_id"]');

                            document.querySelectorAll('.order-item-row').forEach(function(row) {
                                var selectEl = row.querySelector('.select-product-dropdown');
                                if (selectEl) {
                                    var currentVal = String(selectEl.value || '');
                                    for (var i = 0; i < selectEl.options.length; i++) {
                                        var opt = selectEl.options[i];
                                        if (opt.value && opt.value.trim() !== '') {
                                            var optVal = String(opt.value);
                                            if (selectedIds.indexOf(optVal) !== -1 && optVal !== currentVal) {
                                                opt.disabled = true;
                                            } else {
                                                opt.disabled = false;
                                            }
                                        }
                                    }
                                    if (typeof jQuery !== 'undefined' && jQuery(selectEl).data('select2')) {
                                        initSelect2(jQuery(selectEl));
                                    }
                                }
                            });

                            if (unassignedSelect) {
                                var currentVal = String(unassignedSelect.value || '');
                                for (var i = 0; i < unassignedSelect.options.length; i++) {
                                    var opt = unassignedSelect.options[i];
                                    if (opt.value && opt.value.trim() !== '') {
                                        var optVal = String(opt.value);
                                        if (selectedIds.indexOf(optVal) !== -1 && optVal !== currentVal) {
                                            opt.disabled = true;
                                        } else {
                                            opt.disabled = false;
                                        }
                                    }
                                }
                                if (typeof jQuery !== 'undefined' && jQuery(unassignedSelect).data('select2')) {
                                    initSelect2(jQuery(unassignedSelect));
                                }
                            }
                        }

                        // Helper function to create row HTML elements dynamically
                        function createProductRowHtml(index, productId, productTitle, productImage, qty) {
                            var row = document.createElement('div');
                            row.className = 'order-item-row';
                            row.setAttribute('data-index', index);

                            var imageHtml = '';
                            if (productImage && productImage.trim() !== '') {
                                imageHtml = '<img class="view-item-image" src="' + productImage + '" alt="' + productTitle + '">';
                            } else {
                                imageHtml = '<div class="view-item-image-placeholder select-product-option-no-img">📦</div>';
                            }

                            var optionsHtml = '<option value="">Select product</option>';
                            if (window.allProductsList) {
                                window.allProductsList.forEach(function(prod) {
                                    var selected = (String(prod.product_id) === String(productId)) ? 'selected' : '';
                                    optionsHtml += '<option value="' + prod.product_id + '" data-image="' + (prod.product_image || '') + '" ' + selected + '>' + prod.product_title + '</option>';
                                });
                            }

                            var viewStyle = '';
                            var editStyle = 'style="display: none;"';
                            if (!productId) {
                                viewStyle = 'style="display: none !important;"';
                                editStyle = 'style="display: block;"';
                            }

                            row.innerHTML =
                                '<div class="order-item-view-container" ' + viewStyle + '>' +
                                '    <div class="order-item-view-left">' +
                                '        <div class="order-item-thumb-wrapper">' +
                                imageHtml +
                                '        </div>' +
                                '        <div class="order-item-details">' +
                                '            <div class="order-item-title view-item-title">' + productTitle + '</div>' +
                                '            <div class="order-item-qty">Qty: <span class="view-item-qty">' + qty + '</span></div>' +
                                '        </div>' +
                                '    </div>' +
                                '    <div class="order-item-edit-btn-wrapper" style="display: flex; gap: 8px;">' +
                                '        <button type="button" class="btn-premium-action btn-edit edit-item-btn"><i class="fas fa-edit"></i> Edit</button>' +
                                '        <button type="button" class="btn-premium-action btn-cancel remove-item-btn" style="background-color: #ef4444 !important; color: #fff !important;"><i class="fas fa-trash-alt"></i> Remove</button>' +
                                '    </div>' +
                                '</div>' +
                                '<div class="order-item-edit-container" ' + editStyle + '>' +
                                '    <input type="hidden" name="product_id_' + index + '" class="item-product-id-input" value="' + productId + '">' +
                                '    <div class="order-item-edit-grid">' +
                                '        <div class="premium-form-group">' +
                                '            <label>Select Product</label>' +
                                '            <select class="premium-form-control select-product-dropdown" style="width: 100%;">' +
                                optionsHtml +
                                '            </select>' +
                                '        </div>' +
                                '        <div class="premium-form-group">' +
                                '            <label>Qty</label>' +
                                '            <input type="text" name="qty_' + index + '" class="premium-form-control edit-qty-input" value="' + qty + '">' +
                                '        </div>' +
                                '        <div style="display: flex; gap: 8px; align-items: flex-end; height: 100%;">' +
                                '            <button type="button" class="btn-premium-action btn-save save-item-btn" style="flex: 1; height: 38px;"><i class="fas fa-check"></i> Assign Product</button>' +
                                '            <button type="button" class="btn-premium-action btn-cancel cancel-item-btn" style="height: 38px; background-color: var(--neutral-300) !important; color: var(--neutral-700) !important;"><i class="fas fa-times"></i> Cancel</button>' +
                                '        </div>' +
                                '    </div>' +
                                '</div>';

                            return row;
                        }

                        // event delegation using vanilla JS
                        document.addEventListener('click', function(e) {
                            // 1. Edit Button Click
                            var editBtn = e.target.closest('.edit-item-btn');
                            if (editBtn) {
                                e.preventDefault();
                                var row = editBtn.closest('.order-item-row');
                                if (row) {
                                    var viewContainer = row.querySelector('.order-item-view-container');
                                    var editContainer = row.querySelector('.order-item-edit-container');
                                    if (viewContainer && editContainer) {
                                        viewContainer.style.setProperty('display', 'none', 'important');
                                        editContainer.style.display = 'block';

                                        // Initialize Select2 using jQuery (if loaded)
                                        if (typeof jQuery !== 'undefined') {
                                            var $select = jQuery(row).find('.select-product-dropdown');
                                            initSelect2($select);
                                        } else {
                                            var checkInterval = setInterval(function() {
                                                if (typeof jQuery !== 'undefined') {
                                                    clearInterval(checkInterval);
                                                    var $select = jQuery(row).find('.select-product-dropdown');
                                                    initSelect2($select);
                                                }
                                            }, 50);
                                        }
                                    }
                                }
                                return;
                            }

                            // 2. Save Button Click
                            var saveBtn = e.target.closest('.save-item-btn');
                            if (saveBtn) {
                                e.preventDefault();
                                var row = saveBtn.closest('.order-item-row');
                                if (row) {
                                    var viewContainer = row.querySelector('.order-item-view-container');
                                    var editContainer = row.querySelector('.order-item-edit-container');
                                    if (viewContainer && editContainer) {
                                        var selectEl = editContainer.querySelector('.select-product-dropdown');
                                        var qtyInput = editContainer.querySelector('.edit-qty-input');
                                        var productIdInput = editContainer.querySelector('.item-product-id-input');

                                        if (selectEl && qtyInput && productIdInput) {
                                            var selectedOption = selectEl.options[selectEl.selectedIndex];
                                            var newProductId = selectEl.value;

                                            if (!newProductId || newProductId.trim() === '') {
                                                if (typeof Swal !== 'undefined') {
                                                    Swal.fire({
                                                        icon: 'error',
                                                        type: 'error',
                                                        title: 'Product Required',
                                                        text: 'Please select a product before saving.',
                                                        timer: 2000,
                                                        showConfirmButton: false
                                                    });
                                                } else {
                                                    alert('Please select a product before saving.');
                                                }
                                                return;
                                            }

                                            var newProductTitle = selectedOption ? selectedOption.text.trim() : '';
                                            var newProductImage = selectedOption ? selectedOption.getAttribute('data-image') : '';
                                            var newQty = qtyInput.value;
                                            var oldProductId = productIdInput.value;

                                            // Call API endpoint to save / assign product
                                            $.ajax({
                                                type: 'POST',
                                                url: '/api/order/assign_product/',
                                                data: {
                                                    id: orderId,
                                                    product_id: newProductId,
                                                    qty: newQty,
                                                    old_product_id: oldProductId
                                                },
                                                dataType: 'json',
                                                success: function(response) {
                                                    if (response.status == 200) {
                                                        // Sync backend inputs
                                                        productIdInput.value = newProductId;

                                                        // Update view title and qty
                                                        var viewTitle = viewContainer.querySelector('.view-item-title');
                                                        var viewQty = viewContainer.querySelector('.view-item-qty');
                                                        if (viewTitle) viewTitle.textContent = newProductTitle;
                                                        if (viewQty) viewQty.textContent = newQty;

                                                        // Update view image
                                                        var thumbWrapper = viewContainer.querySelector('.order-item-thumb-wrapper');
                                                        if (thumbWrapper) {
                                                            thumbWrapper.innerHTML = '';
                                                            if (newProductImage && newProductImage.trim() !== '') {
                                                                var img = document.createElement('img');
                                                                img.className = 'view-item-image';
                                                                img.src = newProductImage;
                                                                img.alt = newProductTitle;
                                                                thumbWrapper.appendChild(img);
                                                            } else {
                                                                var placeholder = document.createElement('div');
                                                                placeholder.className = 'view-item-image-placeholder select-product-option-no-img';
                                                                placeholder.textContent = '📦';
                                                                thumbWrapper.appendChild(placeholder);
                                                            }
                                                        }

                                                        editContainer.style.display = 'none';
                                                        viewContainer.removeAttribute('style');

                                                        // Re-index all inputs
                                                        reindexProducts();

                                                        if (typeof Swal !== 'undefined') {
                                                            Swal.fire({
                                                                icon: 'success',
                                                                type: 'success',
                                                                title: 'Success',
                                                                text: response.msg || 'Product assigned successfully.',
                                                                timer: 1500,
                                                                showConfirmButton: false
                                                            });
                                                        }
                                                    } else {
                                                        if (typeof Swal !== 'undefined') {
                                                            Swal.fire({
                                                                icon: 'error',
                                                                type: 'error',
                                                                title: 'Error',
                                                                text: response.msg || 'Something went wrong.',
                                                                timer: 2000,
                                                                showConfirmButton: false
                                                            });
                                                        } else {
                                                            alert(response.msg || 'Something went wrong.');
                                                        }
                                                    }
                                                },
                                                error: function() {
                                                    if (typeof Swal !== 'undefined') {
                                                        Swal.fire({
                                                            icon: 'error',
                                                            type: 'error',
                                                            title: 'Error',
                                                            text: 'Failed to assign product.',
                                                            timer: 2000,
                                                            showConfirmButton: false
                                                        });
                                                    } else {
                                                        alert('Failed to assign product.');
                                                    }
                                                }
                                            });
                                        }
                                    }
                                }
                                return;
                            }

                            // 2.5 Cancel Item Edit Button Click
                            var cancelBtn = e.target.closest('.cancel-item-btn');
                            if (cancelBtn) {
                                e.preventDefault();
                                var row = cancelBtn.closest('.order-item-row');
                                if (row) {
                                    var productIdInput = row.querySelector('.item-product-id-input');
                                    if (productIdInput && !productIdInput.value) {
                                        // It's a new unsaved row - remove it completely
                                        row.remove();
                                        reindexProducts();
                                        return;
                                    }

                                    var viewContainer = row.querySelector('.order-item-view-container');
                                    var editContainer = row.querySelector('.order-item-edit-container');
                                    if (viewContainer && editContainer) {
                                        var selectEl = editContainer.querySelector('.select-product-dropdown');
                                        var qtyInput = editContainer.querySelector('.edit-qty-input');

                                        // Reset select and quantity to current values
                                        if (selectEl && qtyInput && productIdInput) {
                                            selectEl.value = productIdInput.value;
                                            if (typeof jQuery !== 'undefined' && jQuery(selectEl).data('select2')) {
                                                jQuery(selectEl).trigger('change');
                                            }
                                            var viewQty = viewContainer.querySelector('.view-item-qty');
                                            if (viewQty) {
                                                qtyInput.value = viewQty.textContent.trim();
                                            }
                                        }

                                        editContainer.style.display = 'none';
                                        viewContainer.removeAttribute('style');
                                        updateProductDropdownOptions();
                                    }
                                }
                                return;
                            }

                            // 2.6 Remove Product Button Click
                            var removeBtn = e.target.closest('.remove-item-btn');
                            if (removeBtn) {
                                e.preventDefault();
                                var row = removeBtn.closest('.order-item-row');
                                if (row) {
                                    var performRemoval = function() {
                                        var productIdInput = row.querySelector('.item-product-id-input');
                                        var productId = productIdInput ? productIdInput.value : '';

                                        if (productId && productId.trim() !== '') {
                                            $.ajax({
                                                type: 'GET',
                                                url: '/api/order/remove_product/',
                                                data: {
                                                    id: orderId,
                                                    product_id: productId
                                                },
                                                dataType: 'json',
                                                success: function(response) {
                                                    if (response.status == 200) {
                                                        row.remove();
                                                        reindexProducts();

                                                        if (typeof Swal !== 'undefined') {
                                                            Swal.fire({
                                                                icon: 'success',
                                                                type: 'success',
                                                                title: 'Success',
                                                                text: response.msg || 'Product removed successfully.',
                                                                timer: 1500,
                                                                showConfirmButton: false
                                                            });
                                                        }
                                                    } else {
                                                        if (typeof Swal !== 'undefined') {
                                                            Swal.fire({
                                                                icon: 'error',
                                                                type: 'error',
                                                                title: 'Error',
                                                                text: response.msg || 'Something went wrong.',
                                                                timer: 2000,
                                                                showConfirmButton: false
                                                            });
                                                        } else {
                                                            alert(response.msg || 'Something went wrong.');
                                                        }
                                                    }
                                                },
                                                error: function() {
                                                    if (typeof Swal !== 'undefined') {
                                                        Swal.fire({
                                                            icon: 'error',
                                                            type: 'error',
                                                            title: 'Error',
                                                            text: 'Failed to remove product.',
                                                            timer: 2000,
                                                            showConfirmButton: false
                                                        });
                                                    } else {
                                                        alert('Failed to remove product.');
                                                    }
                                                }
                                            });
                                        } else {
                                            row.remove();
                                            reindexProducts();
                                        }
                                    };

                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            title: 'Are you sure?',
                                            text: 'Do you want to remove this product from the order?',
                                            icon: 'warning',
                                            type: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#ef4444',
                                            cancelButtonColor: '#6b7280',
                                            confirmButtonText: 'Yes, remove it!'
                                        }).then(function(result) {
                                            if (result.value) {
                                                performRemoval();
                                            }
                                        });
                                    } else {
                                        if (confirm('Are you sure you want to remove this product from the order?')) {
                                            performRemoval();
                                        }
                                    }
                                }
                                return;
                            }

                            // 3. Change Vendor Button Click
                            var changeVendorBtn = e.target.closest('#changeVendorBtn');
                            if (changeVendorBtn) {
                                e.preventDefault();
                                var viewMode = document.getElementById('vendorViewMode');
                                var editMode = document.getElementById('vendorEditMode');
                                if (viewMode && editMode) {
                                    viewMode.style.display = 'none';
                                    editMode.style.display = 'block';
                                }
                                return;
                            }

                            // 4. Cancel Vendor Edit Button Click
                            var cancelVendorEditBtn = e.target.closest('#cancelVendorEditBtn');
                            if (cancelVendorEditBtn) {
                                e.preventDefault();
                                var viewMode = document.getElementById('vendorViewMode');
                                var editMode = document.getElementById('vendorEditMode');
                                if (viewMode && editMode) {
                                    editMode.style.display = 'none';
                                    viewMode.style.display = 'block';
                                }
                                return;
                            }

                            // 5. Save Vendor Edit Button Click
                            var saveVendorEditBtn = e.target.closest('#saveVendorEditBtn');
                            if (saveVendorEditBtn) {
                                e.preventDefault();
                                var selectEl = document.querySelector('#vendorEditMode select[name="assigned_vendor"]');
                                if (selectEl) {
                                    var vendorCode = selectEl.value;
                                    if (!vendorCode || vendorCode.trim() === '') {
                                        if (typeof Swal !== 'undefined') {
                                            Swal.fire({
                                                icon: 'error',
                                                type: 'error',
                                                title: 'Vendor Required',
                                                text: 'Please select a partner.',
                                                timer: 2000,
                                                showConfirmButton: false
                                            });
                                        } else {
                                            alert('Please select a partner.');
                                        }
                                        return;
                                    }

                                    $.ajax({
                                        type: 'POST',
                                        url: '/api/order/assign_vendor/',
                                        data: {
                                            id: orderId,
                                            assigned_vendor: vendorCode
                                        },
                                        dataType: 'json',
                                        success: function(response) {
                                            if (response.status == 200) {
                                                if (typeof Swal !== 'undefined') {
                                                    Swal.fire({
                                                        icon: 'success',
                                                        type: 'success',
                                                        title: 'Success',
                                                        text: response.msg || 'Vendor assigned successfully.',
                                                        timer: 1500,
                                                        showConfirmButton: false
                                                    });
                                                } else {
                                                    alert(response.msg || 'Vendor assigned successfully.');
                                                }
                                                setTimeout(function() {
                                                    window.location.reload();
                                                }, 1500);
                                            } else {
                                                if (typeof Swal !== 'undefined') {
                                                    Swal.fire({
                                                        icon: 'error',
                                                        type: 'error',
                                                        title: 'Error',
                                                        text: response.msg || 'Something went wrong.',
                                                        timer: 2000,
                                                        showConfirmButton: false
                                                    });
                                                } else {
                                                    alert(response.msg || 'Something went wrong.');
                                                }
                                            }
                                        },
                                        error: function() {
                                            if (typeof Swal !== 'undefined') {
                                                Swal.fire({
                                                    icon: 'error',
                                                    type: 'error',
                                                    title: 'Error',
                                                    text: 'Failed to assign vendor.',
                                                    timer: 2000,
                                                    showConfirmButton: false
                                                });
                                            } else {
                                                alert('Failed to assign vendor.');
                                            }
                                        }
                                    });
                                }
                                return;
                            }

                            // 5.5 Remove Vendor Button Click
                            var removeVendorBtn = e.target.closest('#removeVendorBtn');
                            if (removeVendorBtn) {
                                e.preventDefault();
                                var performVendorRemoval = function() {
                                    $.ajax({
                                        type: 'GET',
                                        url: '/api/order/remove_vendor/',
                                        data: {
                                            id: orderId
                                        },
                                        dataType: 'json',
                                        success: function(response) {
                                            if (response.status == 200) {
                                                if (typeof Swal !== 'undefined') {
                                                    Swal.fire({
                                                        icon: 'success',
                                                        type: 'success',
                                                        title: 'Success',
                                                        text: response.msg || 'Vendor removed successfully.',
                                                        timer: 1500,
                                                        showConfirmButton: false
                                                    });
                                                } else {
                                                    alert(response.msg || 'Vendor removed successfully.');
                                                }
                                                setTimeout(function() {
                                                    window.location.reload();
                                                }, 1500);
                                            } else {
                                                if (typeof Swal !== 'undefined') {
                                                    Swal.fire({
                                                        icon: 'error',
                                                        type: 'error',
                                                        title: 'Error',
                                                        text: response.msg || 'Something went wrong.',
                                                        timer: 2000,
                                                        showConfirmButton: false
                                                    });
                                                } else {
                                                    alert(response.msg || 'Something went wrong.');
                                                }
                                            }
                                        },
                                        error: function() {
                                            if (typeof Swal !== 'undefined') {
                                                Swal.fire({
                                                    icon: 'error',
                                                    type: 'error',
                                                    title: 'Error',
                                                    text: 'Failed to remove vendor.',
                                                    timer: 2000,
                                                    showConfirmButton: false
                                                });
                                            } else {
                                                alert('Failed to remove vendor.');
                                            }
                                        }
                                    });
                                };

                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        title: 'Are you sure?',
                                        text: 'Do you want to remove the fulfillment partner from this order?',
                                        icon: 'warning',
                                        type: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#ef4444',
                                        cancelButtonColor: '#6b7280',
                                        confirmButtonText: 'Yes, remove it!'
                                    }).then(function(result) {
                                        if (result.value) {
                                            performVendorRemoval();
                                        }
                                    });
                                } else {
                                    if (confirm('Are you sure you want to remove the fulfillment partner from this order?')) {
                                        performVendorRemoval();
                                    }
                                }
                                return;
                            }

                            // 6. Save Product Edit Button Click
                            var saveProductEditBtn = e.target.closest('#saveProductEditBtn');
                            if (saveProductEditBtn) {
                                e.preventDefault();
                                var selectEl = document.querySelector('#productEditMode select[name="assigned_product_id"]');
                                var qtyInput = document.getElementById('assignedQtyInput');
                                if (selectEl) {
                                    var selectedOption = selectEl.options[selectEl.selectedIndex];
                                    var selectedId = selectEl.value;

                                    if (!selectedId || selectedId.trim() === '') {
                                        if (typeof Swal !== 'undefined') {
                                            Swal.fire({
                                                icon: 'error',
                                                type: 'error',
                                                title: 'Product Required',
                                                text: 'Please select a product to assign.',
                                                timer: 2000,
                                                showConfirmButton: false
                                            });
                                        } else {
                                            alert('Please select a product to assign.');
                                        }
                                        return;
                                    }

                                    var title = selectedOption ? selectedOption.text.trim() : '';
                                    var image = selectedOption ? selectedOption.getAttribute('data-image') : '';
                                    var qty = qtyInput ? qtyInput.value : '1';

                                    $.ajax({
                                        type: 'POST',
                                        url: '/api/order/assign_product/',
                                        data: {
                                            id: orderId,
                                            product_id: selectedId,
                                            qty: qty
                                        },
                                        dataType: 'json',
                                        success: function(response) {
                                            if (response.status == 200) {
                                                var container = document.getElementById('orderItemsListContainer');
                                                if (container) {
                                                    var newRow = createProductRowHtml(0, selectedId, title, image, qty);
                                                    container.appendChild(newRow);

                                                    reindexProducts();

                                                    if (typeof Swal !== 'undefined') {
                                                        Swal.fire({
                                                            icon: 'success',
                                                            type: 'success',
                                                            title: 'Success',
                                                            text: response.msg || 'Product assigned successfully.',
                                                            timer: 1500,
                                                            showConfirmButton: false
                                                        });
                                                    }

                                                    var warningBlock = document.getElementById('noProductWarning');
                                                    if (warningBlock) warningBlock.style.display = 'none';
                                                    var editMode = document.getElementById('productEditMode');
                                                    if (editMode) editMode.style.display = 'none';

                                                    container.style.display = 'block';
                                                    var addBtnWrapper = document.getElementById('addProductBtnWrapper');
                                                    if (addBtnWrapper) addBtnWrapper.style.display = 'flex';

                                                    selectEl.value = '';
                                                    if (qtyInput) qtyInput.value = '1';
                                                    if (typeof jQuery !== 'undefined' && jQuery(selectEl).data('select2')) {
                                                        jQuery(selectEl).trigger('change');
                                                    }
                                                }
                                            } else {
                                                if (typeof Swal !== 'undefined') {
                                                    Swal.fire({
                                                        icon: 'error',
                                                        type: 'error',
                                                        title: 'Error',
                                                        text: response.msg || 'Something went wrong.',
                                                        timer: 2000,
                                                        showConfirmButton: false
                                                    });
                                                } else {
                                                    alert(response.msg || 'Something went wrong.');
                                                }
                                            }
                                        },
                                        error: function() {
                                            if (typeof Swal !== 'undefined') {
                                                Swal.fire({
                                                    icon: 'error',
                                                    type: 'error',
                                                    title: 'Error',
                                                    text: 'Failed to assign product.',
                                                    timer: 2000,
                                                    showConfirmButton: false
                                                });
                                            } else {
                                                alert('Failed to assign product.');
                                            }
                                        }
                                    });
                                }
                                return;
                            }

                            // 7. Cancel Product Edit Button Click
                            var cancelProductEditBtn = e.target.closest('#cancelProductEditBtn');
                            if (cancelProductEditBtn) {
                                e.preventDefault();
                                if (typeof jQuery !== 'undefined') {
                                    var $select = jQuery('#productEditMode .selectJS');
                                    if ($select.length && $select.val() !== '') {
                                        $select.val('').trigger('change');
                                    }
                                }
                                var qtyInput = document.getElementById('assignedQtyInput');
                                if (qtyInput) {
                                    qtyInput.value = '1';
                                }
                                return;
                            }
                        });

                        // Add Product Button Click
                        var addProductBtn = document.getElementById('addProductBtn');
                        if (addProductBtn) {
                            addProductBtn.addEventListener('click', function(e) {
                                e.preventDefault();
                                var container = document.getElementById('orderItemsListContainer');
                                if (container) {
                                    var nextIndex = document.querySelectorAll('.order-item-row').length;
                                    var newRow = createProductRowHtml(nextIndex, '', '', '', '1');

                                    container.appendChild(newRow);

                                    var $newSelect = jQuery(newRow).find('.select-product-dropdown');
                                    if (typeof initSelect2 === 'function') {
                                        initSelect2($newSelect);
                                    }

                                    reindexProducts();
                                }
                            });
                        }

                        // Helper functions for Select2
                        function ensureSelect2(callback) {
                            if (typeof jQuery === 'undefined') {
                                return; // cannot load without jQuery
                            }
                            var $ = jQuery;
                            if (!$.fn.select2) {
                                var cssUrl = "<?php echo SITE_URL . '/' . PLUGIN_LIBRARY; ?>select2/css/select2.min.css";
                                var jsUrl = "<?php echo SITE_URL . '/' . PLUGIN_LIBRARY; ?>select2/js/select2.min.js";

                                if ($('link[href="' + cssUrl + '"]').length === 0) {
                                    $("head").append('<link rel="stylesheet" href="' + cssUrl + '">');
                                }

                                $.getScript(jsUrl, function() {
                                    callback();
                                });
                            } else {
                                callback();
                            }
                        }

                        function formatProductOption(state) {
                            if (!state.id || !state.element) {
                                return state.text;
                            }
                            if (state.element.disabled) {
                                return null;
                            }
                            var imgUrl = state.element.getAttribute('data-image');
                            var imgHtml = '';
                            if (imgUrl && imgUrl.trim() !== '') {
                                imgHtml = '<img class="select-product-option-img" src="' + imgUrl + '" />';
                            } else {
                                imgHtml = '<div class="select-product-option-no-img">📦</div>';
                            }
                            return '<div class="select-product-option-wrapper">' + imgHtml + '<span class="select-product-option-text">' + state.text + '</span></div>';
                        }

                        function initSelect2($selects) {
                            ensureSelect2(function() {
                                if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
                                    $selects.each(function() {
                                        var $el = jQuery(this);
                                        if ($el.data('select2')) {
                                            $el.select2('destroy');
                                        }
                                    });
                                    $selects.select2({
                                        templateResult: formatProductOption,
                                        templateSelection: formatProductOption,
                                        escapeMarkup: function(m) {
                                            return m;
                                        },
                                        width: '100%'
                                    });
                                }
                            });
                        }


                        // Product selection validation for main action buttons
                        function validateProductSelection() {
                            var productInputs = document.querySelectorAll('.item-product-id-input');
                            for (var i = 0; i < productInputs.length; i++) {
                                if (!productInputs[i].value || productInputs[i].value.trim() === '') {
                                    return false;
                                }
                            }
                            var assignedProductSelect = document.querySelector('select[name="assigned_product_id"]');
                            if (assignedProductSelect && (!assignedProductSelect.value || assignedProductSelect.value.trim() === '')) {
                                return false;
                            }
                            return true;
                        }

                        var actionButtons = ['#orderUpdateData', '#orderConfirmBtn', '#orderWaitingBtn', '#orderDeliverBtn'];
                        actionButtons.forEach(function(selector) {
                            var btn = document.querySelector(selector);
                            if (btn) {
                                btn.addEventListener('click', function(e) {
                                    if (!validateProductSelection()) {
                                        e.preventDefault();
                                        e.stopImmediatePropagation();
                                        if (typeof Swal !== 'undefined') {
                                            Swal.fire({
                                                icon: 'error',
                                                type: 'error',
                                                title: 'Product Required',
                                                text: 'Please select a product before updating the order.',
                                                timer: 2500,
                                                showConfirmButton: false
                                            });
                                        } else {
                                            alert('Please select a product before updating the order.');
                                        }
                                    }
                                }, true); // capture phase
                            }
                        });

                        // Listen to changes on product dropdowns
                        document.addEventListener('change', function(e) {
                            if (e.target.classList.contains('select-product-dropdown') || e.target.classList.contains('selectJS')) {
                                updateProductDropdownOptions();
                            }
                        });

                        // Initialize Select2 on load for any pre-opened edit container dropdowns or product edit mode select
                        var visibleSelects = document.querySelectorAll('.order-item-edit-container[style*="display: block"] .select-product-dropdown, #productEditMode .selectJS');
                        if (visibleSelects.length > 0) {
                            if (typeof jQuery !== 'undefined') {
                                updateProductDropdownOptions();
                                initSelect2(jQuery(visibleSelects));
                            } else {
                                var initInterval = setInterval(function() {
                                    if (typeof jQuery !== 'undefined') {
                                        clearInterval(initInterval);
                                        updateProductDropdownOptions();
                                        initSelect2(jQuery(visibleSelects));
                                    }
                                }, 50);
                            }
                        }
                    })();
                </script>

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